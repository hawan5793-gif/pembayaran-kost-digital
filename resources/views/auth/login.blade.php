<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#273876">
    <title>Masuk · Yani Kost</title>
    <style>
        :root {
            color-scheme: light;
            --navy: #273876;
            --navy-light: #344b8c;
            --canvas: #f3f4f6;
            --muted: #667085;
        }
        * { box-sizing: border-box; }
        body {
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            margin: 0;
            padding: 0;
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            overflow-x: hidden;
            background: var(--canvas);
            color: #171b27;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .login-panel {
            position: relative;
            isolation: isolate;
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            padding: max(36px, env(safe-area-inset-top)) max(26px, env(safe-area-inset-right)) max(30px, env(safe-area-inset-bottom)) max(26px, env(safe-area-inset-left));
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
            border-radius: 0;
            background: linear-gradient(150deg, #344b8c 0%, var(--navy) 52%, #1f2e63 100%);
            box-shadow: none;
            color: #fff;
        }
        .login-panel::before,
        .login-panel::after {
            position: absolute;
            z-index: -1;
            content: '';
            pointer-events: none;
        }
        .login-panel::before {
            width: 145%;
            height: 58%;
            left: -24%;
            top: 27%;
            border-radius: 46% 54% 55% 45% / 44% 43% 57% 56%;
            transform: rotate(-13deg);
            background: linear-gradient(135deg, rgba(255,255,255,.13), rgba(255,255,255,.025) 68%);
        }
        .login-panel::after {
            width: 135%;
            height: 37%;
            right: -57%;
            bottom: -8%;
            border-radius: 58% 42% 0 0 / 48% 55% 0 0;
            transform: rotate(-19deg);
            background: rgba(14, 28, 70, .18);
        }
        .brand, .login-panel section { width: min(100%, 440px); margin-right: auto; margin-left: auto; }
        .brand { text-align: center; margin-bottom: 42px; }
        .brand-mark {
            width: 88px;
            height: 88px;
            margin: 0 auto 17px;
            display: grid;
            place-items: center;
            border-radius: 25px;
            background: #fff;
            color: var(--navy);
            box-shadow: 0 10px 28px rgba(15, 23, 42, .16);
        }
        .brand-mark svg { width: 48px; height: 48px; }
        .brand h1 { margin: 0; font-size: 1.55rem; font-weight: 800; letter-spacing: -.04em; }
        .brand p { margin: 8px 0 0; color: rgba(255,255,255,.76); font-size: .94rem; }
        .welcome { margin: 0 0 22px; font-size: clamp(1.7rem, 7vw, 2rem); font-weight: 800; letter-spacing: -.04em; }
        .error {
            margin: 0 0 14px;
            padding: 11px 13px;
            border: 1px solid rgba(255,255,255,.24);
            border-radius: 12px;
            background: rgba(127, 29, 29, .28);
            color: #fff;
            font-size: .86rem;
        }
        label { display: block; margin: 16px 0 7px; font-size: .9rem; font-weight: 700; }
        input[type="email"], input[type="password"] {
            width: 100%;
            min-height: 50px;
            padding: 12px 14px;
            border: 1px solid rgba(255,255,255,.38);
            border-radius: 13px;
            outline: none;
            background: transparent;
            color: #fff;
            font: inherit;
            transition: border-color .18s, background .18s, box-shadow .18s;
        }
        input::placeholder { color: rgba(255,255,255,.58); }
        input[type="email"]:focus, input[type="password"]:focus {
            border-color: #fff;
            box-shadow: 0 0 0 3px rgba(255,255,255,.14);
        }
        input[type="email"]:-webkit-autofill,
        input[type="password"]:-webkit-autofill {
            -webkit-text-fill-color: #fff;
            -webkit-box-shadow: 0 0 0 1000px transparent inset;
            transition: background-color 9999s ease-out;
        }
        .remember { display: flex; align-items: center; gap: 9px; margin-top: 17px; color: rgba(255,255,255,.9); font-size: .86rem; }
        .remember input { width: 17px; height: 17px; margin: 0; accent-color: #fff; }
        button {
            width: 100%;
            min-height: 54px;
            margin-top: 26px;
            border: 0;
            border-radius: 999px;
            background: #fff;
            color: var(--navy);
            font: inherit;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .16);
            transition: transform .15s, box-shadow .15s;
        }
        button:hover { transform: translateY(-1px); box-shadow: 0 13px 28px rgba(15, 23, 42, .2); }
        button:focus-visible { outline: 3px solid rgba(255,255,255,.65); outline-offset: 3px; }
        @media (max-height: 760px) {
            .login-panel { min-height: 100vh; min-height: 100dvh; padding-top: max(24px, env(safe-area-inset-top)); padding-bottom: max(24px, env(safe-area-inset-bottom)); }
            .brand { margin-bottom: 28px; }
            .brand-mark { width: 72px; height: 72px; border-radius: 21px; margin-bottom: 12px; }
            .brand-mark svg { width: 40px; height: 40px; }
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <main class="login-panel">
        <header class="brand">
            <div class="brand-mark" aria-hidden="true">
                <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 39V13.5C7 12.12 8.12 11 9.5 11H28.5C29.88 11 31 12.12 31 13.5V39H7Z" fill="currentColor"/>
                    <path d="M13 17H17V21H13V17ZM21 17H25V21H21V17ZM13 24H17V28H13V24ZM21 24H25V28H21V24ZM16 32H22V39H16V32Z" fill="white"/>
                    <path d="M31 25.5C34.04 25.5 36.5 27.96 36.5 31C36.5 34.04 34.04 36.5 31 36.5C27.96 36.5 25.5 34.04 25.5 31C25.5 27.96 27.96 25.5 31 25.5Z" fill="white"/>
                    <path d="M22 40C22.45 36.39 26.27 34 31 34C35.73 34 39.55 36.39 40 40H22Z" fill="white"/>
                    <path d="M5 39H42" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </div>
            <h1>Yani Kost</h1>
            <p>Kelola penghuni dan pembayaran</p>
        </header>

        <section aria-labelledby="welcome-title">
            <h2 class="welcome" id="welcome-title">Selamat datang</h2>

            @if ($errors->any())
                <div class="error" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Masukkan email" autocomplete="username" required autofocus>

                <label for="password">Kata sandi</label>
                <input id="password" name="password" type="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>

                <label class="remember" for="remember">
                    <input id="remember" type="checkbox" name="remember" value="1">
                    <span>Ingat saya</span>
                </label>

                <button type="submit">Masuk</button>
            </form>
        </section>
    </main>
</body>
</html>
