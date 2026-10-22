<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TenantBillingService
{
    /**
     * Accrue one rent month for every elapsed calendar month without a payment.
     * Existing tenants with no billing cursor are reconciled from their entry
     * month and paid periods, preserving the arrears already on the record.
     */
    public function accrueThroughCurrentMonth(): void
    {
        $currentMonth = now()->startOfMonth();

        Tenant::query()->select('id')->chunkById(100, function ($tenants) use ($currentMonth) {
            foreach ($tenants as $tenantRow) {
                DB::transaction(function () use ($tenantRow, $currentMonth) {
                    $tenant = Tenant::query()->lockForUpdate()->find($tenantRow->id);
                    if (! $tenant) {
                        return;
                    }

                    $cursor = $tenant->last_billed_at
                        ? Carbon::parse($tenant->last_billed_at)->startOfMonth()
                        : ($tenant->entry_date
                            ? Carbon::parse($tenant->entry_date)->startOfMonth()
                            : $currentMonth->copy());
                    if ($cursor->greaterThanOrEqualTo($currentMonth)) {
                        if (! $tenant->last_billed_at) {
                            $tenant->forceFill(['last_billed_at' => $currentMonth->toDateString()])->save();
                        }

                        return;
                    }

                    $paidPeriods = Payment::query()
                        ->where('tenant_id', $tenant->id)
                        ->where('status', 'Lunas')
                        ->pluck('month_period')
                        ->flip();
                    $unpaidMonths = (int) $tenant->unpaid_months;

                    while ($cursor->lessThan($currentMonth)) {
                        $cursor->addMonthNoOverflow();
                        $period = $cursor->copy()->locale('id')->isoFormat('MMM YYYY');

                        if (! $paidPeriods->has($period)) {
                            $unpaidMonths++;
                        }
                    }

                    $tenant->forceFill([
                        'last_billed_at' => $currentMonth->toDateString(),
                        'unpaid_months' => $unpaidMonths,
                        'status' => $unpaidMonths > 0 ? 'belum_bayar' : 'lunas',
                    ])->save();
                });
            }
        });
    }
}
