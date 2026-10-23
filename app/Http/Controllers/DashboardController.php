<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Services\TenantBillingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    private function getCommonData()
    {
        app(TenantBillingService::class)->accrueThroughCurrentMonth();

        $today = now()->locale('id');
        $currentMonthPeriod = $today->isoFormat('MMM YYYY');
        $currentMonthName = $today->isoFormat('MMMM');
        $currentMonthShort = $today->isoFormat('MMM');
        $currentYear = $today->year;
        $todayDate = $today->toDateString();
        $todayDisplay = $today->isoFormat('D MMM YYYY');
        // Include the previous October while keeping the picker focused on
        // recent periods, rather than starting two years back.
        $monthOptions = collect(range(-13, 2))
            ->map(fn ($monthsAhead) => $today->copy()->addMonths($monthsAhead)->isoFormat('MMM YYYY'))
            ->all();

        $rooms = Room::with('tenants')->orderBy('id')->get();
        $totalRooms = $rooms->count();
        $occupiedRooms = $rooms->filter(fn ($room) => $room->tenants->isNotEmpty())->count();

        $tenants = Tenant::with(['room', 'payments' => fn ($query) => $query->where('status', 'Lunas')->orderByDesc('payment_date')])->orderBy('id')->get();

        $paidTenants = $tenants->where('status', 'lunas');
        $paidCount = $paidTenants->count();
        $paidRoomCount = $rooms->filter(fn ($room) => $room->tenants->isNotEmpty() && $room->tenants->every(fn ($tenant) => $tenant->status === 'lunas'))->count();

        $unpaidTenants = $tenants->where('status', 'belum_bayar')->sortByDesc('unpaid_months')->values();
        $unpaidCount = $unpaidTenants->count();

        $collectedThisMonth = Payment::whereBetween('payment_date', [
                $today->copy()->startOfMonth()->toDateString(),
                $today->copy()->endOfMonth()->toDateString(),
            ])
            ->where('status', 'Lunas')
            ->sum('amount');

        $collectedLastFiveMonths = Payment::whereBetween('payment_date', [
                $today->copy()->subMonths(4)->startOfMonth()->toDateString(),
                $today->copy()->endOfMonth()->toDateString(),
            ])
            ->where('status', 'Lunas')
            ->sum('amount');

        $uncollectedAmount = $unpaidTenants->sum(function ($t) {
            $price = $t->price ?? $t->room?->price ?? 650000;
            return $t->unpaid_months * $price;
        });

        $performanceRate = $totalRooms > 0 ? round(($paidRoomCount / $totalRooms) * 100) : 0;

        $payments = Payment::with(['tenant', 'room'])
            ->where('month_period', $currentMonthPeriod)
            ->orderByDesc('payment_date')
            ->get();

        $chartRaw = [];
        $maxAmt = 0;
        for ($monthsAgo = 4; $monthsAgo >= 0; $monthsAgo--) {
            $monthDate = $today->copy()->subMonths($monthsAgo);
            $amt = Payment::whereBetween('payment_date', [
                    $monthDate->copy()->startOfMonth()->toDateString(),
                    $monthDate->copy()->endOfMonth()->toDateString(),
                ])
                ->where('status', 'Lunas')
                ->sum('amount');
            if ($amt > $maxAmt) {
                $maxAmt = $amt;
            }
            $chartRaw[] = [
                'month' => $monthDate->isoFormat('MMM'),
                'year' => $monthDate->year,
                'current' => $monthsAgo === 0,
                'amount' => $amt,
                'val' => $amt > 0 ? round($amt / 1000000, 1) . 'jt' : '0',
            ];
        }

        $monthlyChart = [];
        foreach ($chartRaw as $cr) {
            $h = ($maxAmt > 0 && $cr['amount'] > 0) ? max(10, round(($cr['amount'] / $maxAmt) * 100)) : 0;
            $monthlyChart[] = [
                'month' => $cr['month'],
                'year' => $cr['year'],
                'current' => $cr['current'],
                'amount' => $cr['amount'],
                'val' => $cr['val'],
                'h' => $h,
            ];
        }

        return compact(
            'rooms',
            'totalRooms',
            'occupiedRooms',
            'tenants',
            'paidTenants',
            'paidCount',
            'paidRoomCount',
            'unpaidTenants',
            'unpaidCount',
            'collectedThisMonth',
            'collectedLastFiveMonths',
            'uncollectedAmount',
            'performanceRate',
            'payments',
            'monthlyChart',
            'currentMonthPeriod',
            'currentMonthName',
            'currentMonthShort',
            'currentYear',
            'todayDate',
            'todayDisplay',
            'monthOptions'
        );
    }

    public function index()
    {
        $data = $this->getCommonData();
        $data['initialView'] = 'home';
        return view('app', $data);
    }

    public function tenants()
    {
        $data = $this->getCommonData();
        $data['initialView'] = 'tenants';
        return view('app', $data);
    }

    public function history()
    {
        $data = $this->getCommonData();
        $data['initialView'] = 'history';
        return view('app', $data);
    }

    public function report()
    {
        $data = $this->getCommonData();
        $data['initialView'] = 'report';
        return view('app', $data);
    }

    public function arrears()
    {
        $data = $this->getCommonData();
        $data['initialView'] = 'arrears';
        return view('app', $data);
    }

    public function payment()
    {
        $data = $this->getCommonData();
        $data['initialView'] = 'payment';
        return view('app', $data);
    }

    public function notifications()
    {
        $data = $this->getCommonData();
        $data['initialView'] = 'notifications';
        return view('app', $data);
    }

    public function storePayment(Request $request)
    {
        app(TenantBillingService::class)->accrueThroughCurrentMonth();

        $periodOptions = collect(range(-13, 2))
            ->map(fn ($offset) => now()->locale('id')->addMonths($offset)->isoFormat('MMM YYYY'))
            ->values();

        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'amount' => 'required|numeric|decimal:0,2|min:0.01|max:9999999999.99',
            'month_period' => ['required', 'string', Rule::in($periodOptions)],
            'payment_date' => 'required|date|before_or_equal:today',
            'method' => 'required|in:Tunai,Transfer',
            'note' => 'nullable|string|max:255',
        ]);

        $currentPeriodIndex = 13;
        $selectedPeriodIndex = $periodOptions->search($validated['month_period']);
        $isCurrentOrPastPeriod = $selectedPeriodIndex <= $currentPeriodIndex;

        $result = DB::transaction(function () use ($request, $validated, $periodOptions, $selectedPeriodIndex, $currentPeriodIndex, $isCurrentOrPastPeriod) {
            $tenant = Tenant::with('room')->lockForUpdate()->findOrFail($validated['tenant_id']);
            $monthlyPriceCents = (int) round(((float) ($tenant->price ?? $tenant->room?->price ?? 0)) * 100);
            $amountCents = (int) round(((float) $validated['amount']) * 100);

            if ($monthlyPriceCents < 1 || $amountCents < $monthlyPriceCents || $amountCents % $monthlyPriceCents !== 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Nominal harus sama dengan satu atau beberapa bulan sewa penuh.',
                ]);
            }

            $monthsCovered = intdiv($amountCents, $monthlyPriceCents);
            $maximumMonths = $isCurrentOrPastPeriod ? max(1, (int) $tenant->unpaid_months) : 1;

            if ($monthsCovered > $maximumMonths) {
                throw ValidationException::withMessages([
                    'amount' => 'Nominal melebihi jumlah bulan yang dapat dibayar untuk periode ini.',
                ]);
            }

            $paidPeriods = Payment::query()
                ->where('tenant_id', $tenant->id)
                ->where('status', 'Lunas')
                ->pluck('month_period')
                ->flip();
            if ($paidPeriods->has($validated['month_period'])) {
                throw ValidationException::withMessages([
                    'month_period' => "{$tenant->name} sudah membayar periode {$validated['month_period']}.",
                ]);
            }

            $firstPeriod = now()->startOfMonth()->addMonths($selectedPeriodIndex - $currentPeriodIndex);
            $periodsToRecord = collect();

            if ($isCurrentOrPastPeriod) {
                $entryMonth = $tenant->entry_date
                    ? Carbon::parse($tenant->entry_date)->startOfMonth()
                    : $firstPeriod->copy()->subMonths(max(0, (int) $tenant->unpaid_months - 1));
                $candidateMonth = $entryMonth->copy();
                $availablePeriods = collect();

                while ($candidateMonth->lessThanOrEqualTo($firstPeriod)) {
                    $period = $candidateMonth->copy()->locale('id')->isoFormat('MMM YYYY');
                    if (! $paidPeriods->has($period)) {
                        $availablePeriods->push($period);
                    }
                    $candidateMonth->addMonthNoOverflow();
                }
                $periodsToRecord = $availablePeriods->take(-$monthsCovered)->values();
            } else {
                $periodsToRecord->push($validated['month_period']);
            }

            if ($periodsToRecord->count() !== $monthsCovered) {
                throw ValidationException::withMessages([
                    'month_period' => 'Periode yang dipilih tidak memiliki cukup bulan sewa yang belum dibayar.',
                ]);
            }

            $payments = $periodsToRecord->map(fn (string $period) => Payment::create([
                ...$validated,
                'amount' => $monthlyPriceCents / 100,
                'month_period' => $period,
                'room_id' => $tenant->room_id,
                'status' => 'Lunas',
            ]));

            if ($isCurrentOrPastPeriod && $tenant->unpaid_months > 0) {
                $remainingMonths = max(0, (int) $tenant->unpaid_months - $monthsCovered);
                $tenant->update([
                    'unpaid_months' => $remainingMonths,
                    'status' => $remainingMonths > 0 ? 'belum_bayar' : 'lunas',
                ]);
            }

            return $request->wantsJson()
                ? response()->json([
                    'success' => true,
                    'message' => 'Pembayaran berhasil disimpan',
                    'payments' => $payments,
                ])
                : redirect()->route('payments.index')->with('success', 'Pembayaran berhasil disimpan!');
        });

        return $result;
    }

    public function storeTenant(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:25',
            'room_number' => 'required|string|max:20',
            'price' => 'required|numeric|decimal:0,2|min:0.01|max:9999999999.99',
            'entry_date' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $tenant = DB::transaction(function () use ($validated) {
            $room = Room::firstOrCreate(
                ['number' => $validated['room_number']],
                ['price' => $validated['price'], 'status' => 'terisi']
            );
            if ($room->status !== 'terisi') {
                $room->update(['status' => 'terisi']);
            }

            return Tenant::create([
                'room_id' => $room->id,
                'price' => $validated['price'],
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'entry_date' => $validated['entry_date'],
                'due_date' => Carbon::parse($validated['entry_date'])->addMonthNoOverflow()->toDateString(),
                'last_billed_at' => Carbon::parse($validated['entry_date'])->startOfMonth()->toDateString(),
                'unpaid_months' => 1,
                'status' => 'belum_bayar',
            ]);
        });

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Penghuni baru berhasil ditambahkan',
                'tenant' => $tenant
            ]);
        }

        return redirect()->route('tenants.index')->with('success', 'Penghuni baru berhasil ditambahkan!');
    }
}
