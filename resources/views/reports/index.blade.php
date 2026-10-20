@extends('layouts.mobile')

@section('content')
<div class="px-5 pt-8 pb-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="text-2xl font-black text-neutral-900 tracking-tight">Laporan</h1>
    </div>

    <!-- Performa Bulan Ini Card -->
    <div class="bg-gradient-to-br from-neutral-900 to-neutral-800 text-white rounded-3xl p-6 shadow-xl mb-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs text-neutral-300 font-medium">Performa bulan ini</span>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                {{ $currentMonthPeriod }}
            </span>
        </div>
        <div class="text-4xl font-extrabold tracking-tight mb-3 text-emerald-400">
            {{ $performanceRate }}%
        </div>
        <p class="text-xs text-neutral-300">
            {{ $paidRooms }} dari {{ $totalRooms }} kamar sudah membayar {{ $currentMonthName }}
        </p>
    </div>

    <!-- Diterima vs Belum Diterima -->
    <div class="grid grid-cols-2 gap-3 mb-6">
        <div class="bg-emerald-50/70 border border-emerald-200/80 rounded-2xl p-4">
            <span class="text-[11px] text-emerald-700 font-semibold block mb-1">Diterima</span>
            <div class="text-lg font-black text-emerald-700">
                Rp{{ number_format($collectedLastFiveMonths / 1000000, 3, '.', '.') }}
            </div>
            <span class="text-[10px] text-emerald-600 font-medium mt-1 block">Penerimaan 5 bulan terakhir</span>
        </div>
        <div class="bg-rose-50/70 border border-rose-200/80 rounded-2xl p-4">
            <span class="text-[11px] text-rose-700 font-semibold block mb-1">Belum diterima</span>
            <div class="text-lg font-black text-rose-700">
                Rp{{ number_format($uncollectedAmount / 1000000, 3, '.', '.') }}
            </div>
            <span class="text-[10px] text-rose-600 font-medium mt-1 block">{{ $unpaidCount }} penghuni</span>
        </div>
    </div>

    <!-- Grafik Bar 5 Bulan (Jun - Okt) -->
    <div class="bg-white border border-neutral-200/80 rounded-3xl p-5 shadow-sm mb-6">
        <div class="flex items-center justify-between mb-1">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block">RINGKASAN</span>
                <h2 class="text-sm font-bold text-neutral-900">Pembayaran per bulan</h2>
            </div>
            <span class="text-xs text-neutral-400 font-medium">5 bulan ⌄</span>
        </div>
        <p class="text-[11px] text-neutral-400 mb-6">Nominal aktual, bukan jumlah tagihan</p>

        <!-- Bar chart manual dengan HTML/CSS -->
        <div class="flex items-end justify-between gap-3 h-40 pt-4 px-2">
            @php
                $maxAmount = max(array_column($monthlyChart, 'amount')) ?: 1;
            @endphp
            @foreach($monthlyChart as $bar)
                @php
                    $heightPercent = ($bar['amount'] / $maxAmount) * 100;
                    $isCurrentMonth = $bar['current'];
                @endphp
                <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                    <span class="text-[10px] font-bold {{ $isCurrentMonth ? 'text-emerald-600' : 'text-neutral-500' }}">
                        {{ $bar['label'] }}
                    </span>
                    <div class="w-full max-w-[32px] rounded-t-xl transition-all duration-300 {{ $isCurrentMonth ? 'bg-emerald-600 shadow-md' : 'bg-neutral-200' }}" style="height: {{ $heightPercent }}%;"></div>
                    <span class="text-[11px] font-bold {{ $isCurrentMonth ? 'text-neutral-900' : 'text-neutral-400' }}">
                        {{ $bar['month'] }}
                    </span>
                </div>
            @endforeach
        </div>

        <div class="mt-4 pt-3 border-t border-neutral-100 flex items-center justify-between text-xs text-neutral-500">
            <span>Uang diterima</span>
            <span class="font-semibold">{{ $currentMonthName }} {{ $currentYear }}</span>
        </div>
    </div>
</div>
@endsection
