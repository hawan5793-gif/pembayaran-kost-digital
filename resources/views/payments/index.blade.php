@extends('layouts.mobile')

@section('content')
<div class="px-5 pt-8 pb-4">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-black text-neutral-900 tracking-tight">Riwayat</h1>
            <span class="text-xs text-neutral-400">{{ $currentMonthName }}</span>
        </div>
        <a href="{{ route('payments.create') }}" class="bg-neutral-900 hover:bg-neutral-800 text-white font-semibold text-xs px-3.5 py-2 rounded-xl flex items-center gap-1.5 shadow">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Catat
        </a>
    </div>

    <!-- Mini Summary Card -->
    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-3xl p-5 mb-4 shadow-lg">
        <span class="text-xs text-emerald-100 font-medium block mb-1">Total Penerimaan ({{ $currentMonthPeriod }})</span>
        <div class="text-2xl font-black mb-2">
            Rp {{ number_format($totalAmount, 0, ',', '.') }}
        </div>
        <div class="text-xs text-emerald-100">
            {{ $totalCount }} transaksi terverifikasi · {{ $currentMonthName }}
        </div>
    </div>

    <!-- Segmented Filter Buttons (Semua, Tunai, Transfer) -->
    <div class="flex gap-2 p-1 bg-neutral-100 rounded-2xl mb-5">
        <a href="{{ route('payments.index', ['metode' => 'semua']) }}" class="flex-1 py-2 text-center text-xs font-bold rounded-xl transition {{ $filter === 'semua' ? 'bg-white text-neutral-900 shadow-sm' : 'text-neutral-500 hover:text-neutral-900' }}">
            Semua
        </a>
        <a href="{{ route('payments.index', ['metode' => 'Tunai']) }}" class="flex-1 py-2 text-center text-xs font-bold rounded-xl transition {{ $filter === 'Tunai' ? 'bg-white text-emerald-600 shadow-sm' : 'text-neutral-500 hover:text-neutral-900' }}">
            Tunai
        </a>
        <a href="{{ route('payments.index', ['metode' => 'Transfer']) }}" class="flex-1 py-2 text-center text-xs font-bold rounded-xl transition {{ $filter === 'Transfer' ? 'bg-white text-emerald-600 shadow-sm' : 'text-neutral-500 hover:text-neutral-900' }}">
            Transfer
        </a>
    </div>

    <!-- Daftar Transaksi -->
    <div>
        <div class="mb-3">
            <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block">RINGKASAN</span>
            <h2 class="text-base font-bold text-neutral-900">Semua transaksi</h2>
        </div>

        <div class="space-y-2.5">
            @forelse($payments as $p)
            <div class="bg-white border border-neutral-200/80 rounded-2xl p-3.5 flex items-center justify-between shadow-sm hover:border-neutral-300 transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-sm">
                        {{ substr($p->tenant->name ?? 'P', 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-sm text-neutral-900">{{ $p->tenant->name ?? 'Penghuni' }}</div>
                        <div class="text-xs text-neutral-400">
                            {{ $p->room->number ?? 'Kamar' }} · {{ $p->month_period }} · {{ $p->note ?? \Carbon\Carbon::parse($p->payment_date)->isoFormat('D MMM') }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $p->method === 'Tunai' ? 'bg-amber-50 text-amber-700 border border-amber-100' : 'bg-blue-50 text-blue-700 border border-blue-100' }}">
                        {{ $p->method }}
                    </span>
                    <span class="text-neutral-300 text-sm">›</span>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-neutral-400 text-sm">
                Belum ada transaksi pada filter ini.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
