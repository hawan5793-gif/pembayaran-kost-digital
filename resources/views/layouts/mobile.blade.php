<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Kost Saya' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        @view-transition {
            navigation: auto;
        }

        ::view-transition-group(nav-active-pill) {
            animation-duration: 320ms;
            animation-timing-function: cubic-bezier(.22, 1, .36, 1);
        }

        @media (prefers-reduced-motion: reduce) {
            ::view-transition-group(nav-active-pill) { animation-duration: 1ms; }
        }
    </style>
</head>
<body class="bg-neutral-100 flex justify-center min-h-screen text-neutral-900 antialiased selection:bg-emerald-500 selection:text-white">
    <!-- Mobile Frame Wrapper (max-w-md center) -->
    <div class="w-full max-w-md bg-white min-h-screen flex flex-col shadow-2xl relative pb-32">
        
        <!-- Main Content Area -->
        <main class="flex-1">
            @yield('content')
        </main>

        <!-- Bottom Navigation Bar (4 Menus: Beranda, Penghuni, Riwayat, Laporan) -->
        @php
            $activeNavIndex = request()->routeIs('dashboard') ? 0 : (request()->routeIs('tenants.*') ? 1 : (request()->routeIs('payments.*') ? 2 : 3));
        @endphp
        <nav aria-label="Navigasi utama" class="fixed bottom-3 left-1/2 -translate-x-1/2 w-[calc(100%-24px)] max-w-md bg-white/95 backdrop-blur-md border border-neutral-200/80 p-1.5 z-50 grid grid-cols-4 items-center rounded-full shadow-[0_8px_28px_rgba(15,23,42,0.12)] isolate">
            <span aria-hidden="true" style="--indicator-offset: {{ $activeNavIndex * 100 }}%" class="absolute z-0 left-1.5 top-1.5 bottom-1.5 w-[calc((100%-12px)/4)] rounded-full bg-slate-900 shadow-[0_4px_12px_rgba(15,23,42,0.22)] [transform:translateX(var(--indicator-offset))] [view-transition-name:nav-active-pill]"></span>
            <!-- Beranda -->
            <a href="{{ route('dashboard') }}" @class(['relative z-10 flex flex-col items-center gap-1 py-1.5 px-1 rounded-full transition-colors duration-300', 'text-white font-bold' => request()->routeIs('dashboard'), 'text-slate-500 hover:text-slate-800 font-medium' => !request()->routeIs('dashboard')])>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                </svg>
                <span class="text-[11px]">Beranda</span>
            </a>

            <!-- Penghuni -->
            <a href="{{ route('tenants.index') }}" @class(['relative z-10 flex flex-col items-center gap-1 py-1.5 px-1 rounded-full transition-colors duration-300', 'text-white font-bold' => request()->routeIs('tenants.*'), 'text-slate-500 hover:text-slate-800 font-medium' => !request()->routeIs('tenants.*')])>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                </svg>
                <span class="text-[11px]">Penghuni</span>
            </a>

            <!-- Riwayat -->
            <a href="{{ route('payments.index') }}" @class(['relative z-10 flex flex-col items-center gap-1 py-1.5 px-1 rounded-full transition-colors duration-300', 'text-white font-bold' => request()->routeIs('payments.*'), 'text-slate-500 hover:text-slate-800 font-medium' => !request()->routeIs('payments.*')])>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <span class="text-[11px]">Riwayat</span>
            </a>

            <!-- Laporan -->
            <a href="{{ route('reports.index') }}" @class(['relative z-10 flex flex-col items-center gap-1 py-1.5 px-1 rounded-full transition-colors duration-300', 'text-white font-bold' => request()->routeIs('reports.*'), 'text-slate-500 hover:text-slate-800 font-medium' => !request()->routeIs('reports.*')])>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>
                </svg>
                <span class="text-[11px]">Laporan</span>
            </a>
        </nav>

    </div>
</body>
</html>
