<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private function getCommonData()
    {
        $today = now()->locale('id');
        $currentMonthPeriod = $today->isoFormat('MMM YYYY');
        $currentMonthName = $today->isoFormat('MMMM');
        $currentMonthShort = $today->isoFormat('MMM');
        $currentYear = $today->year;
        $todayDate = $today->toDateString();
        $todayDisplay = $today->isoFormat('D MMM YYYY');
        $monthOptions = collect(range(-11, 2))
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

        $collectedThisMonth = Payment::where('month_period', $currentMonthPeriod)
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
            $message = "{$tenant->name} sudah membayar periode {$validated['month_period']}.";
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->withErrors(['month_period' => $message])->withInput();
        }

        $validated['room_id'] = $tenant->room_id;
        $validated['status'] = 'Lunas';

        $payment = Payment::create($validated);

        if ($validated['month_period'] === now()->locale('id')->isoFormat('MMM YYYY') && $tenant->unpaid_months > 0) {
            $tenant->decrement('unpaid_months');
            if ($tenant->unpaid_months === 0) {
                $tenant->update(['status' => 'lunas']);
            }
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil disimpan',
                'payment' => $payment
            ]);
        }

        return redirect()->route('payments.index')->with('success', 'Pembayaran berhasil disimpan!');
    }

    public function storeTenant(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:25',
            'room_number' => 'required|string|max:20',
            'price' => 'required|numeric|min:0',
            'entry_date' => 'required|date',
        ]);

        $room = Room::firstOrCreate(
            ['number' => $validated['room_number']],
            ['price' => $validated['price'], 'status' => 'terisi']
        );
        if ($room->status !== 'terisi') {
            $room->update(['status' => 'terisi']);
        }

        $tenant = Tenant::create([
            'room_id' => $room->id,
            'price' => $validated['price'],
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'entry_date' => $validated['entry_date'],
            'due_date' => date('Y-m-d', strtotime('+1 month', strtotime($validated['entry_date']))),
            'unpaid_months' => 1,
            'status' => 'belum_bayar',
        ]);

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
