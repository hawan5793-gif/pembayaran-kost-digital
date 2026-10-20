<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $today = now()->locale('id');
        $currentMonthPeriod = $today->isoFormat('MMM YYYY');
        $currentMonthName = $today->isoFormat('MMMM');
        $currentYear = $today->year;
        $totalRooms = Room::count();
        $paidRooms = Room::with('tenants')->get()->filter(fn ($room) => $room->tenants->isNotEmpty() && $room->tenants->every(fn ($tenant) => $tenant->status === 'lunas'))->count();
        $performanceRate = $totalRooms > 0 ? round(($paidRooms / $totalRooms) * 100) : 0;
        
        $collectedThisMonth = Payment::where('month_period', $currentMonthPeriod)
            ->where('status', 'Lunas')
            ->sum('amount');

        $collectedLastFiveMonths = Payment::whereBetween('payment_date', [
                $today->copy()->subMonths(4)->startOfMonth()->toDateString(),
                $today->copy()->endOfMonth()->toDateString(),
            ])
            ->where('status', 'Lunas')
            ->sum('amount');
            
        $unpaidTenants = Tenant::with('room')->where('status', 'belum_bayar')->get();
        $uncollectedAmount = $unpaidTenants->sum(fn($t) => $t->unpaid_months * ($t->price ?? $t->room?->price ?? 650000));
        $unpaidCount = $unpaidTenants->count();
        
        $monthlyChart = [];
        for ($monthsAgo = 4; $monthsAgo >= 0; $monthsAgo--) {
            $monthDate = $today->copy()->subMonths($monthsAgo);
            $amount = Payment::whereBetween('payment_date', [
                $monthDate->copy()->startOfMonth()->toDateString(),
                $monthDate->copy()->endOfMonth()->toDateString(),
            ])->where('status', 'Lunas')->sum('amount');

            $monthlyChart[] = [
                'month' => $monthDate->isoFormat('MMM'),
                'amount' => $amount,
                'label' => $amount > 0 ? number_format($amount / 1000000, 1, ',', '.') . ' jt' : '0',
                'current' => $monthsAgo === 0,
            ];
        }
        
        return view('reports.index', compact(
            'performanceRate',
            'paidRooms',
            'totalRooms',
            'collectedThisMonth',
            'collectedLastFiveMonths',
            'uncollectedAmount',
            'unpaidCount',
            'monthlyChart',
            'currentMonthPeriod',
            'currentMonthName',
            'currentYear'
        ));
    }
}
