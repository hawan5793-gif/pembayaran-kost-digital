@extends('layouts.mobile')

@section('content')
<div class="px-5 pt-8 pb-4">
    <!-- Header: Bulan & Judul Kost -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <span class="text-[11px] font-bold tracking-wider text-neutral-400 uppercase">OKTOBER 2026</span>
            <h1 class="text-2xl font-black text-neutral-900 tracking-tight">Kost Saya</h1>
        </div>
        <div class="w-9 h-9 rounded-full bg-neutral-900 text-white flex items-center justify-center font-bold text-sm shadow">
            3
        </div>
    </div>

    <!-- Hero Card: Terkumpul bulan ini -->
    <div class="bg-gradient-to-br from-neutral-900 to-neutral-800 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden mb-5">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-neutral-300 font-medium">Terkumpul bulan ini</span>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                +8%
            </span>
        </div>
        <div class="text-3xl font-extrabold tracking-tight mb-4">
            Rp {{ number_format($collectedThisMonth, 0, ',', '.') }}
        </div>
        <div class="flex items-center gap-2 text-xs text-neutral-300 pt-3 border-t border-neutral-700/60">
            <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
            <span><strong class="text-white">{{ $paidRoomCount }} dari {{ $totalRooms }} kamar</strong> sudah lunas</span>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="bg-neutral-50 border border-neutral-200/70 rounded-2xl p-3 text-center">
            <span class="text-[11px] text-neutral-500 block mb-1">Total kamar</span>
            <span class="text-lg font-bold text-neutral-900">{{ $totalRooms }}</span>
        </div>
        <div class="bg-neutral-50 border border-neutral-200/70 rounded-2xl p-3 text-center">
            <span class="text-[11px] text-neutral-500 block mb-1">Kamar terisi</span>
            <span class="text-lg font-bold text-neutral-900">{{ $occupiedRooms }}</span>
        </div>
        <div class="bg-rose-50 border border-rose-100 rounded-2xl p-3 text-center">
            <span class="text-[11px] text-rose-600 block mb-1">Total tunggakan</span>
            <span class="text-lg font-bold text-rose-600">Rp{{ number_format($totalDebt / 1000000, 1, ',', '.') }} jt</span>
        </div>
    </div>

    <!-- Section: PERLU PERHATIAN (Belum bayar) -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block">PERLU PERHATIAN</span>
                <h2 class="text-base font-bold text-neutral-900">Belum bayar</h2>
            </div>
            <a href="{{ route('tenants.index', ['status' => 'belum_bayar']) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-0.5">
                Lihat semua
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 17 17 7M7 7h10v10"/>
                </svg>
            </a>
        </div>

        <div class="space-y-2.5">
            @foreach($unpaidTenants as $t)
            <div class="bg-white border border-neutral-200/80 rounded-2xl p-3.5 flex items-center justify-between shadow-sm hover:border-neutral-300 transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 font-bold flex items-center justify-center text-sm">
                        {{ substr($t->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-sm text-neutral-900">{{ $t->name }}</div>
                        <div class="text-xs text-neutral-400">{{ $t->room->number ?? 'Kamar' }} · {{ $t->unpaid_months }} bulan</div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-sm font-extrabold text-rose-600">
                        Rp {{ number_format($t->unpaid_months * ($t->price ?? $t->room?->price ?? 650000), 0, ',', '.') }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Section: AKTIVITAS (Pembayaran terbaru) -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block">AKTIVITAS</span>
                <h2 class="text-base font-bold text-neutral-900">Pembayaran terbaru</h2>
            </div>
            <a href="{{ route('payments.index') }}" class="text-xs font-semibold text-neutral-400 hover:text-neutral-600">
                →
            </a>
        </div>

        <div class="space-y-2.5">
            @foreach($recentPayments as $p)
            <div class="bg-white border border-neutral-200/80 rounded-2xl p-3.5 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 font-bold flex items-center justify-center text-sm">
                        {{ substr($p->tenant->name ?? 'P', 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-sm text-neutral-900">{{ $p->tenant->name ?? 'Penghuni' }}</div>
                        <div class="text-xs text-neutral-400">{{ $p->room->number ?? 'Kamar' }} · {{ $p->month_period }} · {{ $p->note ?? 'Hari ini' }}</div>
                    </div>
                </div>
                <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-100">
                    Lunas
                </span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Floating Action Button: Catat pembayaran -->
    <div class="mt-4">
        <a href="{{ route('payments.create') }}" class="w-full bg-neutral-900 hover:bg-neutral-800 text-white font-bold py-3.5 rounded-2xl flex items-center justify-center gap-2 shadow-lg transition active:scale-[0.98]">
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Catat pembayaran
        </a>
    </div>
</div>
@endsection
