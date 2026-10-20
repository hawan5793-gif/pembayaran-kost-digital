<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Yani Kost · Kelola Sewa & Pembayaran Kost</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom Modern Mobile CSS -->
    <link rel="stylesheet" href="{{ asset('mobile-app.css') }}?v=11">
</head>
<body>

<div class="app-shell">
    <!-- Toast Notification Popup -->
    <div id="toastBar" class="toast-bar" style="display: none;">
        <i class="fa-solid fa-circle-check"></i>
        <span id="toastBarMsg">Pembayaran berhasil disimpan</span>
    </div>

    <!-- Main Scrollable View Area -->
    <main class="view-viewport" id="mainViewport">

        <!-- ============================================== -->
        <!-- VIEW: BERANDA (HOME)                           -->
        <!-- ============================================== -->
        <section id="view-home" class="page-view {{ $initialView === 'home' ? 'active' : '' }}">
            <header class="navy-header">
                <div class="header-top">
                    <div class="user-profile-header">
                        <div class="user-avatar">
                            <i class="fa-solid fa-building-user"></i>
                        </div>
                        <div class="user-meta">
                            <span>{{ $currentMonthName }} {{ $currentYear }}</span>
                            <h2>Yani Kost <i class="fa-solid fa-circle-check"></i></h2>
                        </div>
                    </div>
                    <div class="header-actions">
                        <button type="button" class="header-btn" onclick="navigateTo('payment')" aria-label="Catat Pembayaran" style="width: auto; padding: 0 12px; gap: 6px; font-size: 0.8rem; font-weight: 700;">
                            <i class="fa-solid fa-plus"></i> <span>Catat</span>
                        </button>
                        <button type="button" class="header-btn" onclick="navigateTo('notifications')" aria-label="Notifikasi">
                            <i class="fa-solid fa-bell bell-animated"></i>
                            @if($unpaidTenants->count() > 0)<span class="badge-dot" id="headerNotifDot"></span>@endif
                        </button>
                    </div>
                </div>

                <div style="font-size: 0.8rem; opacity: 0.85; margin-bottom: 4px;">
                    <i class="fa-solid fa-location-dot" style="margin-right: 4px;"></i> Cekok, Babadan, Ponorogo
                </div>
            </header>

            <!-- Hero Balance Card -->
            <div class="hero-card">
                <div class="hero-card-header">
                    <span class="hero-card-label">Terkumpul Bulan Ini</span>
                    <span class="trend-badge">
                        <i class="fa-solid fa-arrow-trend-up"></i> +8%
                    </span>
                </div>
                <div class="hero-balance">
                    Rp {{ number_format($collectedThisMonth, 0, ',', '.') }}
                </div>
                <div class="progress-track">
                    <div class="progress-fill" style="width: {{ $performanceRate }}%;"></div>
                </div>
                <div class="hero-card-footer">
                    <span>{{ $paidRoomCount }} dari {{ $totalRooms }} kamar lunas</span>
                    <strong>{{ $performanceRate }}%</strong>
                </div>
            </div>

            <!-- Quick Metrics Grid -->
            <div class="metrics-row">
                <div class="metric-pill">
                    <div class="metric-icon-box navy">
                        <i class="fa-solid fa-door-closed"></i>
                    </div>
                    <small>Total Kamar</small>
                    <strong>{{ $totalRooms }}</strong>
                </div>
                <div class="metric-pill">
                    <div class="metric-icon-box green">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <small>Kamar Terisi</small>
                    <strong>{{ $occupiedRooms }}</strong>
                </div>
                <div class="metric-pill" onclick="navigateTo('arrears')">
                    <div class="metric-icon-box red">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <small>Tunggakan</small>
                    <strong>{{ round($uncollectedAmount / 1000000, 1) }} jt</strong>
                </div>
            </div>

            <!-- Section: Belum Bayar (Arrears Strip) -->
            <div class="section-header">
                <h3>
                    <i class="fa-solid fa-hourglass-half" style="color: var(--amber-warning); font-size: 0.85rem;"></i>
                    Belum Bayar
                </h3>
                <button type="button" onclick="navigateTo('arrears')">
                    Lihat Semua <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
                </button>
            </div>

            <div class="horizontal-scroll-list">
                @forelse($unpaidTenants as $t)
                    <div class="arrears-card-mini" onclick="quickSelectPay('{{ $t->id }}', '{{ $t->name }}', '{{ $t->room->number ?? 'Kamar' }}', '{{ $t->unpaid_months * ($t->price ?? $t->room?->price ?? 650000) }}', '{{ $t->price ?? $t->room?->price ?? 650000 }}')">
                        <div class="mini-card-top">
                            <div class="mini-avatar">
                                {{ substr($t->name, 0, 1) }}
                            </div>
                            <span class="badge-months">{{ $t->unpaid_months }} Bulan</span>
                        </div>
                        <div class="mini-card-name">{{ $t->name }}</div>
                        <div class="mini-card-room">{{ $t->room->number ?? 'Kamar' }}</div>
                        <div class="mini-card-amount">Rp {{ number_format($t->unpaid_months * ($t->price ?? $t->room?->price ?? 650000), 0, ',', '.') }}</div>
                        <button type="button" class="mini-card-btn">
                            <i class="fa-solid fa-receipt"></i> Catat Bayar
                        </button>
                    </div>
                @empty
                    <div style="padding: 16px; color: var(--text-secondary); font-size: 0.85rem; width: 100%; text-align: center;">
                        <i class="fa-solid fa-circle-check" style="color: var(--green-success);"></i> Semua kamar telah melunasi sewa!
                    </div>
                @endforelse
            </div>

            <!-- Section: Pembayaran Terbaru -->
            <div class="section-header" style="margin-top: 10px;">
                <h3>
                    <i class="fa-solid fa-clock-rotate-left" style="color: var(--blue-accent); font-size: 0.85rem;"></i>
                    Pembayaran Terbaru
                </h3>
                <button type="button" onclick="navigateTo('history')">
                    Riwayat <i class="fa-solid fa-chevron-right" style="font-size: 0.7rem;"></i>
                </button>
            </div>

            <div class="activity-card-list">
                @forelse($payments->take(3) as $p)
                    <div class="activity-card" onclick="showReceipt('{{ $p->tenant->name ?? 'Penghuni' }}', '{{ $p->room->number ?? '-' }}', '{{ \Illuminate\Support\Carbon::parse($p->payment_date)->locale('id')->isoFormat('D MMM YYYY') }}', '{{ $p->method }}', '{{ $p->amount }}', '{{ $p->month_period }}')">
                        <div class="act-avatar success">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <div class="act-info">
                            <div class="act-title">{{ $p->tenant->name ?? 'Penghuni' }}</div>
                            <div class="act-sub">
                                <span><i class="fa-solid fa-door-open"></i> {{ $p->room->number ?? 'Kamar' }}</span>
                                <span>•</span>
                                <span>{{ \Illuminate\Support\Carbon::parse($p->payment_date)->locale('id')->isoFormat('D MMM') }}</span>
                            </div>
                        </div>
                        <div class="act-amount-box">
                            <div class="act-amount">Rp {{ number_format($p->amount, 0, ',', '.') }}</div>
                            <span class="act-badge {{ strtolower($p->method) }}">
                                <i class="fa-solid {{ $p->method === 'Tunai' ? 'fa-money-bill' : 'fa-building-columns' }}"></i> {{ $p->method }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="padding: 18px; text-align: center; color: var(--text-secondary); font-size: 0.85rem;">
                        Belum ada riwayat transaksi pembayaran bulan ini.
                    </div>
                @endforelse
            </div>

            <!-- Primary Action Button (+ Catat Pembayaran) -->
            <div style="padding: 12px 16px 20px;">
                <button type="button" class="btn-primary-action" onclick="navigateTo('payment')">
                    <i class="fa-solid fa-plus"></i>
                    <span>Catat Pembayaran Baru</span>
                </button>
            </div>
        </section>


        <!-- ============================================== -->
        <!-- VIEW: TUNGGAKAN (ARREARS)                      -->
        <!-- ============================================== -->
        <section id="view-arrears" class="page-view {{ $initialView === 'arrears' ? 'active' : '' }}">
            <header class="sub-header">
                <button type="button" class="back-btn" onclick="navigateTo('home')">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <div class="sub-header-title">Tunggakan Sewa</div>
                <div style="width: 36px;"></div>
            </header>

            <div class="hero-card" style="margin-top: 14px;">
                <div class="hero-card-header">
                    <span class="hero-card-label">Total Tagihan Tertunggak</span>
                    <span class="badge-months" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Perhatian
                    </span>
                </div>
                <div class="hero-balance" style="color: var(--red-danger);">
                    Rp {{ number_format($uncollectedAmount, 0, ',', '.') }}
                </div>
                <div class="hero-card-footer">
                    <span>{{ $unpaidCount }} Penghuni belum membayar sewa</span>
                    <strong>{{ $currentMonthName }} {{ $currentYear }}</strong>
                </div>
            </div>

            <div class="segmented-pills" style="--segment-count: 2">
                <span class="segmented-indicator" aria-hidden="true"></span>
                <button type="button" id="btn-seg-all" class="seg-btn active" onclick="filterArrears('all')">
                    Semua · {{ $unpaidCount }}
                </button>
                <button type="button" id="btn-seg-long" class="seg-btn" onclick="filterArrears('long')">
                    ≥ 3 Bulan · {{ $unpaidTenants->where('unpaid_months', '>=', 3)->count() }}
                </button>
            </div>

            <div class="activity-card-list" id="arrearsListWrap">
                @forelse($unpaidTenants as $t)
                    <div class="arrears-card-item" data-months="{{ $t->unpaid_months }}" style="background: #ffffff; border: 1px solid var(--border-color); border-radius: 18px; padding: 14px; box-shadow: var(--shadow-sm); margin-bottom: 10px; border-left: 5px solid var(--red-danger);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 38px; height: 38px; border-radius: 10px; background: var(--red-bg); color: var(--red-danger); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.95rem;">
                                    {{ substr($t->name, 0, 1) }}
                                </div>
                                <div>
                                    <div style="font-size: 0.92rem; font-weight: 700; color: var(--text-primary);">{{ $t->name }}</div>
                                    <div style="font-size: 0.72rem; color: var(--text-secondary);">
                                        {{ $t->room->number ?? 'Kamar' }} · Jatuh tempo tgl {{ date('d', strtotime($t->due_date ?? now())) }}
                                    </div>
                                </div>
                            </div>
                            <span class="badge-months">{{ $t->unpaid_months }} Bulan</span>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 10px; background: #f8fafc; border-radius: 10px; margin-bottom: 12px; font-size: 0.78rem;">
                            <span style="color: var(--text-secondary);">
                                @php
                                    $periodCount = min($t->unpaid_months, 3);
                                    $periodMonths = [];
                                    for ($monthsAgo = $periodCount - 1; $monthsAgo >= 0; $monthsAgo--) {
                                        $periodMonths[] = now()->locale('id')->subMonths($monthsAgo)->isoFormat('MMM');
                                    }
                                @endphp
                                {{ implode(', ', $periodMonths) }} {{ $currentYear }}
                            </span>
                            <strong style="color: var(--primary-navy); font-size: 0.95rem;">
                                Rp {{ number_format($t->unpaid_months * ($t->price ?? $t->room?->price ?? 650000), 0, ',', '.') }}
                            </strong>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <button type="button" class="mini-card-btn" style="background: var(--primary-navy); color: #ffffff; border-color: var(--primary-navy);" onclick="openWhatsAppModal('{{ $t->name }}', '{{ $t->phone }}', '{{ $t->room->number ?? 'Kamar' }}', '{{ $t->unpaid_months }}', '{{ $t->unpaid_months * ($t->price ?? $t->room?->price ?? 650000) }}')">
                                <i class="fa-brands fa-whatsapp" style="font-size: 0.9rem;"></i> Ingatkan WA
                            </button>
                            <button type="button" class="mini-card-btn" style="background: var(--primary-navy); color: #ffffff; border-color: var(--primary-navy);" onclick="quickSelectPay('{{ $t->id }}', '{{ $t->name }}', '{{ $t->room->number ?? 'Kamar' }}', '{{ $t->unpaid_months * ($t->price ?? $t->room?->price ?? 650000) }}', '{{ $t->price ?? $t->room?->price ?? 650000 }}')">
                                <i class="fa-solid fa-receipt"></i> Catat Bayar
                            </button>
                        </div>
                    </div>
                @empty
                    <div style="padding: 24px; text-align: center; color: var(--text-secondary);">
                        <i class="fa-solid fa-circle-check" style="font-size: 2rem; color: var(--green-success); margin-bottom: 8px;"></i>
                        <p>Tidak ada penghuni yang menunggak sewa.</p>
                    </div>
                @endforelse
            </div>
        </section>


        <!-- ============================================== -->
        <!-- VIEW: PENGHUNI (TENANTS)                       -->
        <!-- ============================================== -->
        <section id="view-tenants" class="page-view {{ $initialView === 'tenants' ? 'active' : '' }}">
            <header class="sub-header">
                <div class="sub-header-title">Daftar Penghuni</div>
                <button type="button" class="mini-card-btn" style="width: auto; background: rgba(255, 255, 255, 0.2); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 99px; padding: 6px 14px;" onclick="openAddTenantModal()">
                    <i class="fa-solid fa-user-plus"></i> Tambah
                </button>
            </header>

            <div class="hero-card" style="margin-top: 14px;">
                <div class="hero-card-header">
                    <span class="hero-card-label">Status Okupansi Kamar</span>
                    <span class="trend-badge">
                        <i class="fa-solid fa-house-chimney"></i> {{ $occupiedRooms }} / {{ $totalRooms }} Terisi
                    </span>
                </div>
                <div class="hero-balance" style="font-size: 1.4rem; margin-bottom: 6px;">
                    {{ $tenants->count() }} Orang Penyewa
                </div>
                <div class="hero-card-footer">
                    <span><i class="fa-solid fa-circle-check" style="color: var(--green-success);"></i> {{ $paidCount }} Lunas</span>
                    <span><i class="fa-solid fa-circle-exclamation" style="color: var(--red-danger);"></i> {{ $unpaidCount }} Belum Bayar</span>
                </div>
            </div>

            <!-- Search Field -->
            <div class="search-container">
                <div class="search-field-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="tenantSearchInput" placeholder="Cari nama penghuni atau kamar..." oninput="filterTenantCards(this.value)">
                </div>
            </div>

            <!-- Filter Status Pills -->
            <div class="segmented-pills" style="--segment-count: 3">
                <span class="segmented-indicator" aria-hidden="true"></span>
                <button type="button" id="tpill-all" class="seg-btn active" onclick="filterTenantStatus('all')">Semua</button>
                <button type="button" id="tpill-lunas" class="seg-btn" onclick="filterTenantStatus('lunas')">Lunas</button>
                <button type="button" id="tpill-overdue" class="seg-btn" onclick="filterTenantStatus('belum_bayar')">Belum Bayar</button>
            </div>

            <!-- Tenant List -->
            <div class="activity-card-list" id="tenantListContainer">
                @forelse($tenants as $t)
                    <div class="activity-card tenant-row-item" data-name="{{ strtolower($t->name) }}" data-room="{{ strtolower($t->room->number ?? '') }}" data-status="{{ $t->status }}" onclick="showReceipt('{{ $t->name }}', '{{ $t->room->number ?? 'Kamar' }}', '{{ $t->status === 'lunas' ? $todayDisplay : \Illuminate\Support\Carbon::parse($t->due_date ?? now())->locale('id')->isoFormat('D MMM YYYY') }}', '{{ $t->status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}', '{{ $t->price ?? $t->room?->price ?? 650000 }}', '{{ $currentMonthPeriod }}')">
                        <div class="act-avatar {{ $t->status === 'lunas' ? 'success' : '' }}" style="{{ $t->status !== 'lunas' ? 'background: var(--red-bg); color: var(--red-danger);' : '' }}">
                            <i class="fa-solid {{ $t->status === 'lunas' ? 'fa-user' : 'fa-user-clock' }}"></i>
                        </div>
                        <div class="act-info">
                            <div class="act-title">{{ $t->name }}</div>
                            @if($t->payments->isNotEmpty())
                                <div class="tenant-paid-periods">Sudah bayar: {{ $t->payments->pluck('month_period')->unique()->implode(', ') }}</div>
                            @endif
                            <div class="act-sub">
                                <span><i class="fa-solid fa-door-open"></i> {{ $t->room->number ?? 'Kamar' }}</span>
                                <span>&middot;</span>
                                <span>Jatuh tempo tgl {{ date('d', strtotime($t->due_date ?? now())) }}</span>
                            </div>
                        </div>
                        <div class="act-amount-box">
                            <span class="act-badge {{ $t->status === 'lunas' ? 'tunai' : 'transfer' }}" style="{{ $t->status !== 'lunas' ? 'background: var(--red-bg); color: var(--red-danger);' : '' }}">
                                {{ $t->status === 'lunas' ? 'Lunas' : 'Belum Bayar (' . $t->unpaid_months . ' bln)' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="padding: 28px 16px; text-align: center; color: var(--text-secondary); width: 100%;">
                        <i class="fa-solid fa-user-slash" style="font-size: 2.2rem; opacity: 0.35; margin-bottom: 10px;"></i>
                        <p style="font-size: 0.9rem; margin-bottom: 12px;">Belum ada data penghuni terdaftar.</p>
                        <button type="button" class="mini-card-btn" style="display: inline-flex; width: auto; padding: 8px 18px; margin: 0 auto;" onclick="openAddTenantModal()">
                            <i class="fa-solid fa-user-plus"></i> Tambah Penghuni Pertama
                        </button>
                    </div>
                @endforelse
            </div>
        </section>


        <!-- ============================================== -->
        <!-- VIEW: RIWAYAT (HISTORY)                        -->
        <!-- ============================================== -->
        <section id="view-history" class="page-view {{ $initialView === 'history' ? 'active' : '' }}">
            <header class="sub-header">
                <div class="sub-header-title">Riwayat Pembayaran</div>
                <div style="font-size: 0.8rem; color: #94a3b8;"><i class="fa-solid fa-calendar-days"></i> {{ $currentMonthPeriod }}</div>
            </header>

            <div class="hero-card" style="margin-top: 14px;">
                <div class="hero-card-header">
                    <span class="hero-card-label">Total Pembayaran Diterima</span>
                    <span class="trend-badge">
                        <i class="fa-solid fa-receipt"></i> {{ $payments->count() }} Transaksi
                    </span>
                </div>
                <div class="hero-balance">
                    Rp {{ number_format($collectedThisMonth, 0, ',', '.') }}
                </div>
                <div class="hero-card-footer">
                    <span>Periode {{ $currentMonthName }} {{ $currentYear }}</span>
                    <strong>Yani Kost</strong>
                </div>
            </div>

            <!-- Method Filter Pills -->
            <div class="segmented-pills" style="--segment-count: 3">
                <span class="segmented-indicator" aria-hidden="true"></span>
                <button type="button" id="hpill-all" class="seg-btn active" onclick="filterHistoryMethod('all')">Semua</button>
                <button type="button" id="hpill-tunai" class="seg-btn" onclick="filterHistoryMethod('Tunai')">
                    <i class="fa-solid fa-money-bill-wave"></i> Tunai
                </button>
                <button type="button" id="hpill-transfer" class="seg-btn" onclick="filterHistoryMethod('Transfer')">
                    <i class="fa-solid fa-building-columns"></i> Transfer
                </button>
            </div>

            <div class="activity-card-list" id="historyListContainer">
                @forelse($payments as $p)
                    <div class="activity-card history-row-item" data-method="{{ $p->method }}" onclick="showReceipt('{{ $p->tenant->name ?? 'Penghuni' }}', '{{ $p->room->number ?? '-' }}', '{{ \Illuminate\Support\Carbon::parse($p->payment_date)->locale('id')->isoFormat('D MMM YYYY') }}', '{{ $p->method }}', '{{ $p->amount }}', '{{ $p->month_period }}')">
                        <div class="act-avatar success">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div class="act-info">
                            <div class="act-title">{{ $p->tenant->name ?? 'Penghuni' }}</div>
                            <div class="act-sub">
                                <span><i class="fa-solid fa-door-open"></i> {{ $p->room->number ?? 'Kamar' }}</span>
                                <span>•</span>
                                <span>{{ \Illuminate\Support\Carbon::parse($p->payment_date)->locale('id')->isoFormat('D MMM YYYY') }}</span>
                            </div>
                        </div>
                        <div class="act-amount-box">
                            <div class="act-amount">Rp {{ number_format($p->amount, 0, ',', '.') }}</div>
                            <span class="act-badge {{ strtolower($p->method) }}">
                                {{ $p->method }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div style="padding: 28px 16px; text-align: center; color: var(--text-secondary); width: 100%;">
                        <i class="fa-solid fa-receipt" style="font-size: 2.2rem; opacity: 0.35; margin-bottom: 10px;"></i>
                        <p style="font-size: 0.9rem; margin-bottom: 12px;">Belum ada riwayat transaksi pembayaran.</p>
                        <button type="button" class="mini-card-btn" style="display: inline-flex; width: auto; padding: 8px 18px; margin: 0 auto;" onclick="navigateTo('payment')">
                            <i class="fa-solid fa-plus"></i> Catat Pembayaran Baru
                        </button>
                    </div>
                @endforelse
            </div>
        </section>


        <!-- ============================================== -->
        <!-- VIEW: LAPORAN (REPORT)                         -->
        <!-- ============================================== -->
        <section id="view-report" class="page-view {{ $initialView === 'report' ? 'active' : '' }}">
            <header class="sub-header">
                <div class="sub-header-title">Laporan Keuangan</div>
                <div style="font-size: 0.8rem; color: #94a3b8;"><i class="fa-solid fa-chart-line"></i> Statistik</div>
            </header>

            <div class="hero-card" style="margin-top: 14px;">
                <div class="hero-card-header">
                    <span class="hero-card-label">Performa Penagihan Sewa</span>
                    <span class="trend-badge">
                        <i class="fa-solid fa-bullseye"></i> Target 100%
                    </span>
                </div>
                <div class="hero-balance">
                    {{ $performanceRate }}% Lunas
                </div>
                <div class="progress-track">
                    <div class="progress-fill" style="width: {{ $performanceRate }}%;"></div>
                </div>
                <div class="hero-card-footer">
                    <span>{{ $paidRoomCount }} dari {{ $totalRooms }} kamar terbayar</span>
                    <strong>Sisa: {{ $unpaidCount }}</strong>
                </div>
            </div>

            <div class="metrics-row">
                <div class="metric-pill" style="grid-column: span 1.5;">
                    <div class="metric-icon-box green">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <small>Diterima</small>
                    <strong style="font-size: 0.85rem;">Rp {{ number_format($collectedLastFiveMonths, 0, ',', '.') }}</strong>
                </div>
                <div class="metric-pill" style="grid-column: span 1.5;">
                    <div class="metric-icon-box red">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <small>Belum Diterima</small>
                    <strong style="font-size: 0.85rem; color: var(--red-danger);">Rp {{ number_format($uncollectedAmount, 0, ',', '.') }}</strong>
                </div>
            </div>

            <!-- Bar Chart Card -->
            <div class="chart-box-card">
                <div class="chart-box-header">
                    <div>
                        <h4>Penerimaan Kas (5 Bulan)</h4>
                        <span style="font-size: 0.72rem; color: var(--text-secondary);">Nominal aktual pembayaran yang diterima</span>
                    </div>
                    <span class="trend-badge" style="background: #f1f5f9; color: var(--primary-navy);">
                        <i class="fa-solid fa-calendar"></i> {{ $monthlyChart[0]['year'] === $monthlyChart[count($monthlyChart) - 1]['year'] ? $monthlyChart[0]['year'] : $monthlyChart[0]['year'] . '-' . $monthlyChart[count($monthlyChart) - 1]['year'] }}
                    </span>
                </div>

                <div class="chart-bars-wrap">
                    @foreach($monthlyChart as $bar)
                        <div class="bar-col">
                            <div class="bar-track">
                                <div class="bar-stick" style="height: {{ $bar['h'] }}%;">
                                    <div class="bar-val">{{ $bar['val'] }}</div>
                                </div>
                            </div>
                            <span class="bar-lbl">{{ $bar['month'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--text-secondary);">
                    <span><i class="fa-solid fa-square" style="color: var(--blue-accent);"></i> Uang sewa masuk</span>
                    <span>{{ $currentMonthShort }}: <strong>Rp {{ number_format($collectedThisMonth, 0, ',', '.') }}</strong></span>
                </div>
            </div>
        </section>


        <!-- ============================================== -->
        <!-- VIEW: CATAT PEMBAYARAN (PAYMENT)               -->
        <!-- ============================================== -->
        <section id="view-payment" class="page-view {{ $initialView === 'payment' ? 'active' : '' }}">
            <header class="sub-header">
                <button type="button" class="back-btn" onclick="navigateTo('home')">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <div class="sub-header-title">Catat Pembayaran</div>
                <div style="width: 36px;"></div>
            </header>

            <div class="search-container" style="margin-top: 14px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 6px;">
                    <i class="fa-solid fa-bolt" style="color: var(--amber-warning);"></i> Ketik Cepat Nama & Nominal
                </label>
                <div class="search-field-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="quickInput" placeholder="Misal: Juna 650 atau Putri 1950" oninput="parseQuickInput(this.value)">
                </div>
            </div>

            <!-- Quick Tenant Match Pill List -->
            <div style="padding: 0 16px 8px;">
                <div class="pill-grid" id="quickResultList">
                    @forelse($tenants as $idx => $t)
                        <div class="pill-option tenant-match-btn {{ $idx === 0 ? 'active' : '' }}" data-name="{{ strtolower($t->name) }}" data-room="{{ strtolower($t->room->number ?? '') }}" onclick="quickSelectPay('{{ $t->id }}', '{{ $t->name }}', '{{ $t->room->number ?? 'Kamar' }}', '{{ ($t->unpaid_months > 0 ? $t->unpaid_months : 1) * ($t->price ?? $t->room?->price ?? 650000) }}', '{{ $t->price ?? $t->room?->price ?? 650000 }}')">
                            <i class="fa-solid fa-user"></i> {{ $t->name }} ({{ $t->room->number ?? 'Kamar' }})
                        </div>
                    @empty
                        <div style="font-size: 0.8rem; color: var(--text-secondary); padding: 8px;">
                            Belum ada penghuni terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Form Card -->
            <form id="paymentForm" onsubmit="submitPayment(event)">
                <input type="hidden" id="formTenantId" value="{{ $tenants->first()?->id ?? '' }}">

                <div class="form-card">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #eef0f5; color: var(--primary-navy); display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                            <i class="fa-solid fa-bed"></i>
                        </div>
                        <div>
                            <h3 id="displayTenantName" style="font-size: 1.05rem; font-weight: 800; color: var(--primary-navy);">
                                {{ $tenants->first()?->name ?? 'Pilih / Tambah Penghuni' }} {{ $tenants->first()?->room ? '· ' . $tenants->first()->room->number : '' }}
                            </h3>
                            <span id="displayTenantPrice" style="font-size: 0.78rem; color: var(--text-secondary);">
                                Tarif Rp {{ number_format($tenants->first()?->price ?? $tenants->first()?->room?->price ?? 0, 0, ',', '.') }}/bulan
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fa-solid fa-calendar-check"></i> Untuk Periode Bulan</label>
                        <div class="pill-grid">
                            @foreach($monthOptions as $monthOption)
                                <button type="button" class="pill-option month-chip {{ $monthOption === $currentMonthPeriod ? 'active' : '' }}" onclick="selectMonth(this, '{{ $monthOption }}')">{{ $monthOption }}</button>
                            @endforeach
                        </div>
                        <input type="hidden" id="formMonthPeriod" value="{{ $currentMonthPeriod }}">
                    </div>

                    <div class="form-group">
                        <label><i class="fa-solid fa-money-bill"></i> Nominal Bayar (Rp)</label>
                        <input type="text" id="formAmount" value="{{ $tenants->first() ? number_format($tenants->first()->price ?? $tenants->first()->room?->price ?? 0, 0, ',', '.') : '0' }}" style="font-size: 1.25rem; font-weight: 800; color: var(--primary-navy);" oninput="formatRupiahInput(this)">
                    </div>

                    <div class="form-group">
                        <label><i class="fa-solid fa-credit-card"></i> Metode Pembayaran</label>
                        <div class="pill-grid">
                            <button type="button" class="pill-option method-chip active" onclick="selectMethod(this, 'Tunai')">
                                <i class="fa-solid fa-money-bill-wave"></i> Tunai
                            </button>
                            <button type="button" class="pill-option method-chip" onclick="selectMethod(this, 'Transfer')">
                                <i class="fa-solid fa-building-columns"></i> Transfer Bank
                            </button>
                        </div>
                        <input type="hidden" id="formMethod" value="Tunai">
                        <input type="hidden" id="formPaymentDate" value="{{ $todayDate }}">
                    </div>

                    <div style="background: var(--green-bg); border-radius: 10px; padding: 10px 12px; display: flex; align-items: center; gap: 8px; margin-top: 10px;">
                        <i class="fa-solid fa-shield-halved" style="color: var(--green-success); font-size: 1rem;"></i>
                        <span id="dynamicSuccessNote" style="font-size: 0.75rem; color: var(--success-text); font-weight: 600;">
                            Data tersimpan otomatis dan status kamar diupdate.
                        </span>
                    </div>
                </div>

                <div style="padding: 0 16px 20px;">
                    <button type="submit" class="btn-primary-action" id="btnSubmitPayment">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Pembayaran</span>
                    </button>
                </div>
            </form>
        </section>


        <!-- ============================================== -->
        <!-- VIEW: NOTIFIKASI (NOTIFICATIONS)               -->
        <!-- ============================================== -->
        <section id="view-notifications" class="page-view {{ $initialView === 'notifications' ? 'active' : '' }}">
            <header class="sub-header">
                <button type="button" class="back-btn" onclick="navigateTo('home')">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <div class="sub-header-title">Notifikasi</div>
                <button type="button" class="mini-card-btn" style="width: auto; background: none; border: none; color: #ffffff;" onclick="markAllNotificationsRead()">
                    <i class="fa-solid fa-check-double"></i> Dibaca
                </button>
            </header>

            <div class="segmented-pills" style="--segment-count: 3">
                <span class="segmented-indicator" aria-hidden="true"></span>
                <button type="button" id="notif-pill-all" class="seg-btn active" onclick="filterNotifs('all')">Semua</button>
                <button type="button" id="notif-pill-unread" class="seg-btn" onclick="filterNotifs('unread')">Belum Dibaca</button>
                <button type="button" id="notif-pill-arrears" class="seg-btn" onclick="filterNotifs('arrears')">Tunggakan</button>
            </div>

            <div class="activity-card-list" style="margin-top: 10px;">
                @forelse($unpaidTenants as $ut)
                    <div class="activity-card notif-item notif-type-arrears" onclick="navigateTo('arrears')">
                        <div class="act-avatar" style="background: var(--red-bg); color: var(--red-danger);">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div class="act-info">
                            <div class="act-title">{{ $ut->name }} menunggak sewa</div>
                            <div class="act-sub">{{ $ut->room->number ?? 'Kamar' }} · {{ $ut->unpaid_months }} bulan tertunggak</div>
                        </div>
                        <span class="badge-months" style="font-size: 0.65rem;">Tagihan</span>
                    </div>
                @empty
                    @if($payments->isEmpty())
                        <div style="padding: 32px 16px; text-align: center; color: var(--text-secondary); width: 100%;">
                            <i class="fa-solid fa-bell-slash" style="font-size: 2.2rem; opacity: 0.35; margin-bottom: 10px;"></i>
                            <p style="font-size: 0.9rem; margin: 0;">Belum ada notifikasi.</p>
                        </div>
                    @endif
                @endforelse

                @foreach($payments as $np)
                    <div class="activity-card notif-item notif-type-payment">
                        <div class="act-avatar success">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div class="act-info">
                            <div class="act-title">{{ $np->tenant->name ?? 'Penghuni' }} lunas sewa</div>
                            <div class="act-sub">{{ $np->room->number ?? 'Kamar' }} · Rp {{ number_format($np->amount, 0, ',', '.') }} ({{ $np->method }})</div>
                        </div>
                        <span style="font-size: 0.7rem; color: var(--text-muted);">{{ \Illuminate\Support\Carbon::parse($np->payment_date)->locale('id')->isoFormat('D MMM') }}</span>
                    </div>
                @endforeach
            </div>
        </section>

    </main>

    <!-- ============================================== -->
    <!-- FLOATING DOCKED BOTTOM NAVIGATION BAR          -->
    <!-- ============================================== -->
    @php
        $dockIndexes = ['home' => 0, 'tenants' => 1, 'history' => 2, 'report' => 3];
        $activeDockIndex = $dockIndexes[$initialView] ?? 0;
    @endphp
    <nav class="bottom-dock" id="bottomDock" aria-label="Navigasi Utama">
        <span id="dockIndicator" class="dock-indicator" aria-hidden="true" style="transform: translateX({{ $activeDockIndex * 100 }}%)"></span>
        <button type="button" id="dock-btn-home" class="dock-item {{ $initialView === 'home' ? 'active' : '' }}" onclick="navigateTo('home')">
            <i class="fa-solid fa-house"></i>
            <span>Beranda</span>
        </button>

        <button type="button" id="dock-btn-tenants" class="dock-item {{ $initialView === 'tenants' ? 'active' : '' }}" onclick="navigateTo('tenants')">
            <i class="fa-solid fa-users"></i>
            <span>Penghuni</span>
        </button>

        <button type="button" id="dock-btn-history" class="dock-item {{ $initialView === 'history' ? 'active' : '' }}" onclick="navigateTo('history')">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>Riwayat</span>
        </button>

        <button type="button" id="dock-btn-report" class="dock-item {{ $initialView === 'report' ? 'active' : '' }}" onclick="navigateTo('report')">
            <i class="fa-solid fa-chart-simple"></i>
            <span>Laporan</span>
        </button>
    </nav>
</div>


<!-- ============================================== -->
<!-- MODAL: KUITANSI DIGITAL (BOARDING PASS STYLE)   -->
<!-- ============================================== -->
<div id="modalReceipt" class="modal-backdrop" onclick="handleBackdropClick(event, 'modalReceipt')">
    <div class="modal-bottom-sheet">
        <div class="sheet-drag-bar"></div>
        <button type="button" class="close-sheet-btn" onclick="closeModal('modalReceipt')" aria-label="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div class="ticket-boarding-pass">
            <div class="ticket-header">
                <div>
                    <div class="ticket-header-title"><i class="fa-solid fa-building-user"></i> YANI KOST</div>
                    <div style="font-size: 0.7rem; opacity: 0.85;">Kuitansi Resmi Pembayaran Sewa</div>
                </div>
                <div class="ticket-code" id="rcptCode">#KS-{{ rand(1000, 9999) }}</div>
            </div>

            <div class="ticket-body">
                <div class="ticket-row">
                    <span class="t-label">Nama Penyewa</span>
                    <span class="t-value" id="rcptName">Juna</span>
                </div>
                <div class="ticket-row">
                    <span class="t-label">Nomor Kamar</span>
                    <span class="t-value" id="rcptRoom">Kamar 3</span>
                </div>
                <div class="ticket-row">
                    <span class="t-label">Periode Sewa</span>
                    <span class="t-value" id="rcptPeriod">{{ $currentMonthPeriod }}</span>
                </div>
                <div class="ticket-row">
                    <span class="t-label" id="rcptDateLabel">Tanggal Bayar</span>
                    <span class="t-value" id="rcptDate">{{ $todayDisplay }}</span>
                </div>
                <div class="ticket-row">
                    <span class="t-label">Metode Pembayaran</span>
                    <span class="t-value" id="rcptMethod">Tunai</span>
                </div>
                <div class="ticket-row">
                    <span class="t-label" id="rcptStatusLabel">Status Verifikasi</span>
                    <span class="t-value receipt-status" id="rcptStatus"><i class="fa-solid fa-circle-check"></i> LUNAS</span>
                </div>

                <!-- Ticket Notch Divider -->
                <div class="ticket-divider-wrap">
                    <div class="cutout-left"></div>
                    <div class="ticket-divider-line"></div>
                    <div class="cutout-right"></div>
                </div>

                <div class="ticket-row" style="padding-top: 4px;">
                    <span class="t-label" id="rcptAmountLabel" style="font-size: 0.95rem; font-weight: 700;">Total Dibayar</span>
                    <span class="t-value" id="rcptAmount" style="font-size: 1.35rem; font-weight: 800; color: var(--primary-navy);">Rp 650.000</span>
                </div>
            </div>

            <div class="ticket-footer">
                <div class="barcode-mockup">
                    <div class="bar-line" style="width: 3px; height: 100%;"></div>
                    <div class="bar-line" style="width: 1px; height: 100%;"></div>
                    <div class="bar-line" style="width: 4px; height: 100%;"></div>
                    <div class="bar-line" style="width: 2px; height: 100%;"></div>
                    <div class="bar-line" style="width: 5px; height: 100%;"></div>
                    <div class="bar-line" style="width: 2px; height: 100%;"></div>
                    <div class="bar-line" style="width: 1px; height: 100%;"></div>
                    <div class="bar-line" style="width: 4px; height: 100%;"></div>
                    <div class="bar-line" style="width: 3px; height: 100%;"></div>
                    <div class="bar-line" style="width: 1px; height: 100%;"></div>
                    <div class="bar-line" style="width: 5px; height: 100%;"></div>
                    <div class="bar-line" style="width: 2px; height: 100%;"></div>
                    <div class="bar-line" style="width: 4px; height: 100%;"></div>
                    <div class="bar-line" style="width: 2px; height: 100%;"></div>
                </div>
                <div class="barcode-num" id="rcptFooterNote">ELECTRONIC RECEIPT VERIFIED</div>
            </div>
        </div>

        <button type="button" class="btn-primary-action" onclick="printReceipt()">
            <i class="fa-solid fa-print"></i> Cetak / Bagikan Kuitansi
        </button>
    </div>
</div>


<!-- ============================================== -->
<!-- MODAL: WHATSAPP PENGINGAT (REMINDER)           -->
<!-- ============================================== -->
<div id="modalWhatsApp" class="modal-backdrop" onclick="handleBackdropClick(event, 'modalWhatsApp')">
    <div class="modal-bottom-sheet">
        <div class="sheet-drag-bar"></div>
        <button type="button" class="close-sheet-btn" onclick="closeModal('modalWhatsApp')" aria-label="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 14px;">
            <div style="width: 42px; height: 42px; border-radius: 12px; background: var(--navy-light); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.4rem;">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin: 0;">Pengingat WhatsApp</h3>
                <span id="waTargetTenant" style="font-size: 0.78rem; color: var(--text-secondary);">Putri (Kamar 8)</span>
            </div>
        </div>

        <div class="form-group">
            <label><i class="fa-solid fa-message"></i> Pratinjau Pesan</label>
            <textarea id="waMessageText" rows="4" style="resize: none; font-size: 0.85rem; line-height: 1.45;"></textarea>
        </div>

        <button type="button" class="btn-primary-action" style="background: var(--primary-navy); color: #ffffff;" onclick="sendWhatsAppNow()">
            <i class="fa-brands fa-whatsapp"></i> Kirim ke WhatsApp (wa.me)
        </button>
    </div>
</div>


<!-- ============================================== -->
<!-- MODAL: TAMBAH PENGHUNI (ADD TENANT)            -->
<!-- ============================================== -->
<div id="modalAddTenant" class="modal-backdrop" onclick="handleBackdropClick(event, 'modalAddTenant')">
    <div class="modal-bottom-sheet">
        <div class="sheet-drag-bar"></div>
        <button type="button" class="close-sheet-btn" onclick="closeModal('modalAddTenant')" aria-label="Tutup">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
            <div style="width: 38px; height: 38px; border-radius: 10px; background: #eef0f5; color: var(--primary-navy); display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 800; color: var(--text-primary); margin: 0;">Tambah Penghuni Baru</h3>
                <span style="font-size: 0.75rem; color: var(--text-secondary);">Terintegrasi langsung ke database sistem</span>
            </div>
        </div>

        <form id="addTenantForm" onsubmit="submitNewTenant(event)">
            <div class="form-group">
                <label><i class="fa-solid fa-id-card"></i> Nama Lengkap</label>
                <input type="text" id="newTenantName" placeholder="Contoh: Farhan Pratama" required>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label><i class="fa-solid fa-door-open"></i> Nomor Kamar</label>
                    <input type="text" id="newTenantRoom" placeholder="Contoh: Kamar 11" required>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-money-bill-wave"></i> Tarif Sewa (Rp)</label>
                    <input type="text" id="newTenantPrice" placeholder="Contoh: 650.000" inputmode="numeric" autocomplete="off" oninput="formatRupiahInput(this)" required>
                </div>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label><i class="fa-solid fa-phone"></i> WhatsApp / HP</label>
                    <input type="tel" id="newTenantPhone" placeholder="081234567890" required>
                </div>

                <div class="form-group">
                    <label><i class="fa-solid fa-calendar-day"></i> Masuk</label>
                    <input type="date" id="newTenantEntryDate" value="{{ $todayDate }}" required>
                </div>
            </div>

            <button type="submit" class="btn-primary-action" id="btnSubmitNewTenant" style="margin-top: 6px;">
                <i class="fa-solid fa-user-check"></i> Simpan Penghuni
            </button>
        </form>
    </div>
</div>


<!-- ============================================== -->
<!-- JAVASCRIPT LOGIC & INTERACTIONS               -->
<!-- ============================================== -->
<script>
    let currentView = '{{ $initialView }}';
    let currentWaPhone = '';

    const routeMap = {
        'home': '/',
        'tenants': '/penghuni',
        'history': '/riwayat',
        'report': '/laporan',
        'arrears': '/tunggakan',
        'payment': '/riwayat/catat',
        'notifications': '/notifikasi'
    };

    function navigateTo(viewName, pushHistory = true) {
        const targetView = document.getElementById('view-' + viewName);
        if (!targetView) return;

        document.querySelectorAll('.page-view').forEach(el => el.classList.remove('active'));
        targetView.classList.add('active');

        // Scroll to top
        document.getElementById('mainViewport').scrollTo({ top: 0, behavior: 'instant' });

        // Update bottom dock
        const bottomDock = document.getElementById('bottomDock');
        const mainNavViews = ['home', 'tenants', 'history', 'report'];

        if (mainNavViews.includes(viewName)) {
            bottomDock.style.display = 'grid';
            document.querySelectorAll('.dock-item').forEach(btn => btn.classList.remove('active'));
            const activeBtn = document.getElementById('dock-btn-' + viewName);
            if (activeBtn) {
                activeBtn.classList.add('active');
                const activeIndex = mainNavViews.indexOf(viewName);
                document.getElementById('dockIndicator').style.transform = `translateX(${activeIndex * 100}%)`;
            }
        } else {
            bottomDock.style.display = 'none';
        }

        currentView = viewName;

        if (pushHistory && routeMap[viewName]) {
            history.pushState({ view: viewName }, '', routeMap[viewName]);
        }
    }

    window.addEventListener('popstate', (e) => {
        if (e.state && e.state.view) {
            navigateTo(e.state.view, false);
        } else {
            const path = window.location.pathname;
            for (const [v, r] of Object.entries(routeMap)) {
                if (r === path) {
                    navigateTo(v, false);
                    return;
                }
            }
            navigateTo('home', false);
        }
    });

    function showToast(message) {
        const bar = document.getElementById('toastBar');
        const msg = document.getElementById('toastBarMsg');
        msg.innerText = message;
        bar.style.display = 'flex';
        setTimeout(() => {
            bar.style.display = 'none';
        }, 3200);
    }

    function openModal(id) {
        const m = document.getElementById(id);
        if (m) m.classList.add('open');
    }
    function closeModal(id) {
        const m = document.getElementById(id);
        if (m) m.classList.remove('open');
    }
    function handleBackdropClick(e, id) {
        if (e.target.id === id) closeModal(id);
    }

    function openAddTenantModal() {
        openModal('modalAddTenant');
    }

    async function submitNewTenant(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitNewTenant');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

        const payload = {
            name: document.getElementById('newTenantName').value,
            room_number: document.getElementById('newTenantRoom').value,
            phone: document.getElementById('newTenantPhone').value,
            price: document.getElementById('newTenantPrice').value.replace(/[^0-9]/g, ''),
            entry_date: document.getElementById('newTenantEntryDate').value,
        };

        try {
            const resp = await fetch('{{ route('api.tenants.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });
            const data = await resp.json();
            if (data.success) {
                closeModal('modalAddTenant');
                showToast('Penghuni ' + payload.name + ' berhasil ditambahkan!');
                setTimeout(() => window.location.reload(), 800);
            } else {
                alert(data.message || 'Gagal menyimpan data.');
            }
        } catch (err) {
            alert('Terjadi kesalahan koneksi.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-user-check"></i> Simpan Penghuni';
        }
    }

    function openWhatsAppModal(name, phone, room, months, totalAmount) {
        currentWaPhone = phone ? phone.replace(/[^0-9]/g, '') : '';
        if (currentWaPhone.startsWith('0')) {
            currentWaPhone = '62' + currentWaPhone.substring(1);
        }

        document.getElementById('waTargetTenant').innerText = `${name} (${room}) · ${months} Bulan`;
        const msg = `Halo Kak ${name},\n\nSalam hangat dari pengelola Yani Kost. Mengingatkan pembayaran sewa untuk kamar ${room} telah jatuh tempo dengan tunggakan ${months} bulan sebesar Rp ${Number(totalAmount).toLocaleString('id-ID')}.\n\nMohon konfirmasi jika sudah melakukan pembayaran via transfer atau tunai ya kak. Terima kasih!`;

        document.getElementById('waMessageText').value = msg;
        openModal('modalWhatsApp');
    }

    function sendWhatsAppNow() {
        const text = encodeURIComponent(document.getElementById('waMessageText').value);
        const url = `https://wa.me/${currentWaPhone}?text=${text}`;
        window.open(url, '_blank');
        closeModal('modalWhatsApp');
        showToast('Membuka aplikasi WhatsApp...');
    }

    function showReceipt(name, room, date, method, amount, period) {
        const isUnpaid = method === 'Belum Bayar';
        document.getElementById('rcptName').innerText = name;
        document.getElementById('rcptRoom').innerText = room;
        document.getElementById('rcptDate').innerText = date;
        document.getElementById('rcptDateLabel').innerText = isUnpaid ? 'Jatuh Tempo' : 'Tanggal Bayar';
        document.getElementById('rcptMethod').innerText = method;
        document.getElementById('rcptPeriod').innerText = period || @js($currentMonthPeriod);
        document.getElementById('rcptAmount').innerText = 'Rp ' + Number(amount).toLocaleString('id-ID');
        document.getElementById('rcptAmountLabel').innerText = isUnpaid ? 'Tagihan' : 'Total Dibayar';

        const receiptStatus = document.getElementById('rcptStatus');
        receiptStatus.classList.toggle('unpaid', isUnpaid);
        receiptStatus.innerHTML = isUnpaid
            ? '<i class="fa-solid fa-circle-exclamation"></i> BELUM BAYAR'
            : '<i class="fa-solid fa-circle-check"></i> LUNAS';
        document.getElementById('rcptStatusLabel').innerText = isUnpaid ? 'Status Pembayaran' : 'Status Verifikasi';
        document.getElementById('rcptFooterNote').innerText = isUnpaid
            ? 'TAGIHAN BELUM DIBAYAR'
            : 'ELECTRONIC RECEIPT VERIFIED';
        document.getElementById('rcptCode').innerText = '#KS-' + Math.floor(1000 + Math.random() * 9000);
        openModal('modalReceipt');
    }

    function printReceipt() {
        window.print();
    }

    function filterArrears(type) {
        const btnAll = document.getElementById('btn-seg-all');
        const btnLong = document.getElementById('btn-seg-long');
        const cards = document.querySelectorAll('.arrears-card-item');
        setSegmentedActive(type === 'all' ? btnAll : btnLong);

        if (type === 'all') {
            cards.forEach(c => c.style.display = 'block');
        } else {
            cards.forEach(c => {
                const m = parseInt(c.getAttribute('data-months') || '0');
                c.style.display = m >= 3 ? 'block' : 'none';
            });
        }
    }

    function setSegmentedActive(button) {
        const group = button.closest('.segmented-pills');
        if (!group) return;

        const buttons = Array.from(group.querySelectorAll('.seg-btn'));
        buttons.forEach(item => item.classList.toggle('active', item === button));

        const indicator = group.querySelector('.segmented-indicator');
        const index = buttons.indexOf(button);
        if (indicator && index >= 0) {
            indicator.style.transform = `translateX(${index * 100}%)`;
        }
    }

    function quickSelectPay(tenantId, name, room, amount, price) {
        document.getElementById('formTenantId').value = tenantId;
        document.getElementById('displayTenantName').innerText = `${name} · ${room}`;
        document.getElementById('displayTenantPrice').innerText = `Tarif Rp ${Number(price).toLocaleString('id-ID')}/bulan`;
        document.getElementById('formAmount').value = Number(amount || price).toLocaleString('id-ID');
        document.getElementById('dynamicSuccessNote').innerText = `${name} (${room}) akan ditandai Lunas untuk periode ini.`;

        document.querySelectorAll('.tenant-match-btn').forEach(btn => btn.classList.remove('active'));
        const matched = Array.from(document.querySelectorAll('.tenant-match-btn')).find(b => b.getAttribute('data-name') === name.toLowerCase());
        if (matched) {
            matched.classList.add('active');
            matched.scrollIntoView({ block: 'nearest', inline: 'center' });
        }

        navigateTo('payment');
    }

    function parseQuickInput(query) {
        query = query.toLowerCase().trim();
        const items = document.querySelectorAll('.tenant-match-btn');

        items.forEach(el => {
            const name = el.getAttribute('data-name');
            const room = el.getAttribute('data-room');
            if (!query || name.includes(query) || room.includes(query)) {
                el.style.display = 'inline-flex';
            } else {
                el.style.display = 'none';
            }
        });

        const parts = query.split(/\s+/);
        if (parts.length >= 2) {
            const possibleNum = parts[1].replace(/[^0-9]/g, '');
            if (possibleNum) {
                let parsedAmt = parseInt(possibleNum);
                if (parsedAmt < 10000) parsedAmt *= 1000;
                document.getElementById('formAmount').value = parsedAmt.toLocaleString('id-ID');
            }
        }
    }

    function selectMonth(btn, month) {
        document.querySelectorAll('.month-chip').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('formMonthPeriod').value = month;
    }

    function selectMethod(btn, method) {
        document.querySelectorAll('.method-chip').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('formMethod').value = method;
    }

    function formatRupiahInput(input) {
        let val = input.value.replace(/[^0-9]/g, '');
        if (!val) {
            input.value = '';
            return;
        }
        input.value = Number(val).toLocaleString('id-ID');
    }

    async function submitPayment(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitPayment');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

        const rawAmount = document.getElementById('formAmount').value.replace(/[^0-9]/g, '');

        const payload = {
            tenant_id: document.getElementById('formTenantId').value,
            month_period: document.getElementById('formMonthPeriod').value,
            amount: rawAmount,
            method: document.getElementById('formMethod').value,
            payment_date: document.getElementById('formPaymentDate').value,
            note: 'Dicatat via mobile web',
        };

        try {
            const resp = await fetch('{{ route('api.payments.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });
            const data = await resp.json();
            if (data.success) {
                showToast('Pembayaran berhasil disimpan');
                setTimeout(() => {
                    window.location.href = '{{ route('dashboard') }}';
                }, 900);
            } else {
                alert(data.message || 'Gagal menyimpan pembayaran.');
            }
        } catch (err) {
            alert('Terjadi kesalahan koneksi.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Simpan Pembayaran';
        }
    }

    function filterTenantCards(q) {
        q = q.toLowerCase().trim();
        document.querySelectorAll('.tenant-row-item').forEach(el => {
            const name = el.getAttribute('data-name');
            const room = el.getAttribute('data-room');
            el.style.display = (!q || name.includes(q) || room.includes(q)) ? 'flex' : 'none';
        });
    }

    function filterTenantStatus(status) {
        const activeButton = status === 'all'
            ? document.getElementById('tpill-all')
            : (status === 'lunas' ? document.getElementById('tpill-lunas') : document.getElementById('tpill-overdue'));
        setSegmentedActive(activeButton);

        document.querySelectorAll('.tenant-row-item').forEach(el => {
            const s = el.getAttribute('data-status');
            el.style.display = (status === 'all' || s === status) ? 'flex' : 'none';
        });
    }

    function filterHistoryMethod(method) {
        const activeButton = method === 'all'
            ? document.getElementById('hpill-all')
            : (method === 'Tunai' ? document.getElementById('hpill-tunai') : document.getElementById('hpill-transfer'));
        setSegmentedActive(activeButton);

        document.querySelectorAll('.history-row-item').forEach(el => {
            const m = el.getAttribute('data-method');
            el.style.display = (method === 'all' || m === method) ? 'flex' : 'none';
        });
    }

    function filterNotifs(type) {
        const activeButton = type === 'all'
            ? document.getElementById('notif-pill-all')
            : (type === 'unread' ? document.getElementById('notif-pill-unread') : document.getElementById('notif-pill-arrears'));
        setSegmentedActive(activeButton);

        document.querySelectorAll('.notif-item').forEach(el => {
            if (type === 'all') {
                el.style.display = 'flex';
            } else if (type === 'unread') {
                el.style.display = el.classList.contains('notif-type-arrears') ? 'flex' : 'none';
            } else if (type === 'arrears') {
                el.style.display = el.classList.contains('notif-type-arrears') ? 'flex' : 'none';
            }
        });
    }

    function markAllNotificationsRead() {
        const dot = document.getElementById('headerNotifDot');
        if (dot) dot.style.display = 'none';
        document.querySelectorAll('.notif-item .badge-months').forEach(el => el.style.display = 'none');
        showToast('Semua notifikasi ditandai sudah dibaca');
    }
</script>

</body>
</html>