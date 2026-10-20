<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $currentMonthPeriod = now()->locale('id')->isoFormat('MMM YYYY');
        $filter = $request->query('status', 'semua');
        
        $query = Tenant::with(['room', 'payments' => fn($q) => $q->where('status', 'Lunas')->orderByDesc('payment_date')]);
        
        if ($filter === 'lunas') {
            $query->where('status', 'lunas');
        } elseif ($filter === 'belum_bayar') {
            $query->where('status', 'belum_bayar');
        }
        
        $tenants = $query->get();
        $totalCount = Tenant::count();
        $lunasCount = Tenant::where('status', 'lunas')->count();
        $belumBayarCount = Tenant::where('status', 'belum_bayar')->count();
        
        return view('tenants.index', compact(
            'tenants',
            'filter',
            'totalCount',
            'lunasCount',
            'belumBayarCount',
            'currentMonthPeriod'
        ));
    }
}
