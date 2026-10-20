@extends('layouts.mobile')

@section('content')
<div class="px-5 pt-8 pb-4">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('dashboard') }}" class="w-9 h-9 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-600 hover:bg-neutral-200">
            ←
        </a>
        <h1 class="text-xl font-black text-neutral-900 tracking-tight">Catat Pembayaran</h1>
    </div>

    @if ($errors->any())
    <div class="bg-rose-50 text-rose-700 p-3 rounded-2xl text-xs mb-4">
        @foreach ($errors->all() as $error)
            <div>• {{ $error }}</div>
        @endforeach
    </div>
    @endif

    <form action="{{ route('payments.store') }}" method="POST" class="space-y-4">
        @csrf

        <!-- Pilih Penghuni -->
        <div>
            <label class="text-xs font-bold text-neutral-600 block mb-1.5">Penghuni / Kamar</label>
            <select name="tenant_id" required class="w-full bg-neutral-50 border border-neutral-200 rounded-2xl p-3 text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Pilih penghuni</option>
                @foreach($tenants as $t)
                    <option value="{{ $t->id }}">
                        {{ $t->name }} ({{ $t->room->number ?? 'Kamar' }}) — {{ $t->status === 'lunas' ? 'Lunas' : 'Belum bayar ' . $t->unpaid_months . ' bln' }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Periode Bulan -->
        <div>
            <label class="text-xs font-bold text-neutral-600 block mb-1.5">Periode Bulan</label>
            <select name="month_period" required class="w-full bg-neutral-50 border border-neutral-200 rounded-2xl p-3 text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                @foreach($monthOptions as $monthOption)
                    <option value="{{ $monthOption }}" {{ $monthOption === $currentMonthPeriod ? 'selected' : '' }}>{{ $monthOption }}</option>
                @endforeach
            </select>
        </div>

        <!-- Nominal -->
        <div>
            <label class="text-xs font-bold text-neutral-600 block mb-1.5">Nominal (Rp)</label>
            <input type="number" name="amount" value="650000" required class="w-full bg-neutral-50 border border-neutral-200 rounded-2xl p-3 text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <!-- Tanggal Bayar -->
        <div>
            <label class="text-xs font-bold text-neutral-600 block mb-1.5">Tanggal Pembayaran</label>
            <input type="date" name="payment_date" value="{{ $todayDate }}" required class="w-full bg-neutral-50 border border-neutral-200 rounded-2xl p-3 text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <!-- Metode Pembayaran -->
        <div>
            <label class="text-xs font-bold text-neutral-600 block mb-1.5">Metode</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center gap-2 p-3 border border-neutral-200 rounded-2xl cursor-pointer hover:bg-neutral-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/40">
                    <input type="radio" name="method" value="Tunai" checked class="text-emerald-600 focus:ring-emerald-500">
                    <span class="text-xs font-bold text-neutral-800">Tunai</span>
                </label>
                <label class="flex items-center gap-2 p-3 border border-neutral-200 rounded-2xl cursor-pointer hover:bg-neutral-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/40">
                    <input type="radio" name="method" value="Transfer" class="text-emerald-600 focus:ring-emerald-500">
                    <span class="text-xs font-bold text-neutral-800">Transfer</span>
                </label>
            </div>
        </div>

        <!-- Catatan -->
        <div>
            <label class="text-xs font-bold text-neutral-600 block mb-1.5">Catatan (opsional)</label>
            <input type="text" name="note" placeholder="Misal: Titip tetangga, transfer BCA" class="w-full bg-neutral-50 border border-neutral-200 rounded-2xl p-3 text-sm font-medium focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <button type="submit" class="w-full bg-neutral-900 hover:bg-neutral-800 text-white font-bold py-3.5 rounded-2xl shadow-lg mt-6 transition active:scale-[0.98]">
            Simpan Pembayaran
        </button>
    </form>
</div>
@endsection
