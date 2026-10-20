@extends('layouts.mobile')

@section('content')
<div class="px-5 pt-8 pb-4">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-black text-neutral-900 tracking-tight">Penghuni</h1>
        <button class="bg-neutral-900 hover:bg-neutral-800 text-white font-semibold text-xs px-3.5 py-2 rounded-xl flex items-center gap-1.5 shadow">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Tambah
        </button>
    </div>

    <!-- Mini Summary Card -->
    <div class="bg-neutral-50 border border-neutral-200/80 rounded-2xl p-4 mb-4">
        <div class="flex items-center justify-between mb-1">
            <span class="text-xs text-neutral-500 font-medium">10 kamar terisi</span>
            <span class="text-xs font-bold text-neutral-900">{{ $totalCount }} penghuni</span>
        </div>
        <div class="text-xs text-neutral-400">
            <strong class="text-emerald-600">{{ $lunasCount }} lunas</strong> · <strong class="text-rose-600">{{ $belumBayarCount }} belum bayar</strong>
        </div>
    </div>

    <!-- Segmented Filter Buttons -->
    <div class="flex gap-2 p-1 bg-neutral-100 rounded-2xl mb-5">
        <a href="{{ route('tenants.index', ['status' => 'semua']) }}" class="flex-1 py-2 text-center text-xs font-bold rounded-xl transition {{ $filter === 'semua' ? 'bg-white text-neutral-900 shadow-sm' : 'text-neutral-500 hover:text-neutral-900' }}">
            Semua
        </a>
        <a href="{{ route('tenants.index', ['status' => 'lunas']) }}" class="flex-1 py-2 text-center text-xs font-bold rounded-xl transition {{ $filter === 'lunas' ? 'bg-white text-emerald-600 shadow-sm' : 'text-neutral-500 hover:text-neutral-900' }}">
            Lunas
        </a>
        <a href="{{ route('tenants.index', ['status' => 'belum_bayar']) }}" class="flex-1 py-2 text-center text-xs font-bold rounded-xl transition {{ $filter === 'belum_bayar' ? 'bg-white text-rose-600 shadow-sm' : 'text-neutral-500 hover:text-neutral-900' }}">
            Belum bayar
        </a>
    </div>

    <!-- List Penghuni -->
    <div>
        <div class="mb-3">
            <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400 block">RINGKASAN</span>
            <h2 class="text-base font-bold text-neutral-900">Daftar penghuni</h2>
        </div>

        <div class="space-y-2.5">
            @foreach($tenants as $t)
            <div class="bg-white border border-neutral-200/80 rounded-2xl p-3.5 flex items-center justify-between shadow-sm hover:border-neutral-300 transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full {{ $t->status === 'lunas' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} font-bold flex items-center justify-center text-sm">
                        {{ substr($t->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-sm text-neutral-900">{{ $t->name }}</div>
                        <div class="text-xs text-neutral-400">{{ $t->room->number ?? 'Kamar' }}</div>
                        @if($t->payments->isNotEmpty())
                            <div class="text-[10px] leading-snug text-neutral-500 mt-1">Sudah bayar: {{ $t->payments->pluck('month_period')->unique()->implode(', ') }}</div>
                        @else
                            <div class="text-[10px] text-neutral-400 mt-1">Belum ada pembayaran tercatat</div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $t->status === 'lunas' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                        {{ $t->status === 'lunas' ? 'Lunas' : 'Belum bayar' }}
                    </span>
                    <span class="text-neutral-300 text-sm">›</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
