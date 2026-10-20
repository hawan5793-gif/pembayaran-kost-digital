<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->locale('id');
        $currentMonthPeriod = $today->isoFormat('MMM YYYY');
        $currentMonthName = $today->isoFormat('MMMM YYYY');
        $filter = $request->query('metode', 'semua');
        
        $query = Payment::with(['tenant', 'room'])->where('month_period', $currentMonthPeriod);
        
        if ($filter === 'Tunai' || $filter === 'Transfer') {
            $query->where('method', $filter);
        }
        
        $payments = $query->orderByDesc('payment_date')->get();
        $totalAmount = $payments->sum('amount');
        $totalCount = $payments->count();
        
        return view('payments.index', compact('payments', 'filter', 'totalAmount', 'totalCount', 'currentMonthName', 'currentMonthPeriod'));
    }

    public function create()
    {
        $tenants = Tenant::with('room')->get();
        $today = now()->locale('id');
        $currentMonthPeriod = $today->isoFormat('MMM YYYY');
        $todayDate = $today->toDateString();
        $monthOptions = collect(range(-11, 2))
            ->map(fn ($monthsAhead) => $today->copy()->addMonths($monthsAhead)->isoFormat('MMM YYYY'));
        return view('payments.create', compact('tenants', 'currentMonthPeriod', 'todayDate', 'monthOptions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'amount' => 'required|numeric|min:0',
            'month_period' => 'required|string',
            'payment_date' => 'required|date',
            'method' => 'required|in:Tunai,Transfer',
            'note' => 'nullable|string',
        ]);

        $tenant = Tenant::findOrFail($validated['tenant_id']);
        $alreadyPaid = Payment::where('tenant_id', $tenant->id)
            ->where('month_period', $validated['month_period'])
            ->where('status', 'Lunas')
            ->exists();

        if ($alreadyPaid) {
            return back()->withErrors([
                'month_period' => "{$tenant->name} sudah membayar periode {$validated['month_period']}.",
            ])->withInput();
        }

        $validated['room_id'] = $tenant->room_id;
        $validated['status'] = 'Lunas';

        $payment = Payment::create($validated);

        // Update tenant status jika bayar
        if ($validated['month_period'] === now()->locale('id')->isoFormat('MMM YYYY') && $tenant->unpaid_months > 0) {
            $tenant->decrement('unpaid_months');
            if ($tenant->unpaid_months === 0) {
                $tenant->update(['status' => 'lunas']);
            }
        }

        return redirect()->route('payments.index')->with('success', 'Pembayaran berhasil dicatat!');
    }
}
