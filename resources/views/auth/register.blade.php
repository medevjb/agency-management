<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteLogo = \App\Models\Setting::get('site_logo');
        $siteName = \App\Models\Setting::get('site_name', config('app.name', 'Orbit'));
    @endphp

    <title>{{ $siteName }} — Create account</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Keep this CDN Tailwind instance in sync with the app's brand palette
        // (tailwind.config.js) since it doesn't read that file.
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['"Bricolage Grotesque"', 'sans-serif'],
                        sans: ['"Instrument Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        indigo: { 50: '#FFF3EC', 100: '#FFE4D3', 200: '#FFC7A6', 300: '#FFA26E', 400: '#FF7A3D', 500: '#FF5A1F', 600: '#F0430A', 700: '#C63508', 800: '#9C2B0B', 900: '#7A250D', 950: '#431004' },
                        purple: { 50: '#FFF8EB', 100: '#FFEFC7', 200: '#FFDD8A', 300: '#FFC94D', 400: '#FFB238', 500: '#F59A0F', 600: '#D67D06', 700: '#AD5F08', 800: '#8A4A0E', 900: '#723D10', 950: '#5C3208' },
                        violet: { 50: '#FFF8EB', 100: '#FFEFC7', 200: '#FFDD8A', 300: '#FFC94D', 400: '#FFB238', 500: '#F59A0F', 600: '#D67D06', 700: '#AD5F08', 800: '#8A4A0E', 900: '#723D10', 950: '#5C3208' },
                        ink: { DEFAULT: '#0B0E14', soft: '#151A24' },
                        paper: { DEFAULT: '#FBF8F2' },
                        ember: { 50: '#FFF3EC', 100: '#FFE4D3', 200: '#FFC7A6', 300: '#FFA26E', 400: '#FF7A3D', 500: '#FF5A1F', 600: '#F0430A', 700: '#C63508' },
                        gold: { 300: '#FFC94D', 400: '#FFB238', 500: '#F59A0F' },
                        aqua: { 400: '#2FCBB6', 500: '#16B8A0' },
                    },
                },
            },
        };
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|bricolage-grotesque:600,700,800|jetbrains-mono:500,600" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0' y1='0' x2='40' y2='40' gradientUnits='userSpaceOnUse'%3E%3Cstop offset='0' stop-color='%23FFC94D'/%3E%3Cstop offset='1' stop-color='%23FF5A1F'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='40' height='40' rx='9' fill='url(%23g)'/%3E%3Crect y='17' width='40' height='6' fill='%230B0E14'/%3E%3Cpath d='M15 20 L22 15 L22 18.5 L27 18.5 L27 21.5 L22 21.5 L22 25 Z' fill='%230B0E14'/%3E%3C/svg%3E">

    <style>
        * { font-family: 'Instrument Sans', sans-serif; }

        body { background: #FBF8F2; }

        .eyebrow {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            font-size: 0.6875rem;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #FFC94D;
        }

        .gradient-mesh {
            background:
                radial-gradient(at 20% 20%, rgba(255, 90, 31, 0.30) 0px, transparent 45%),
                radial-gradient(at 80% 0%, rgba(255, 178, 56, 0.22) 0px, transparent 45%),
                radial-gradient(at 50% 100%, rgba(22, 184, 160, 0.16) 0px, transparent 45%);
        }

        .glow-blob {
            position: absolute;
            border-radius: 9999px;
            filter: blur(70px);
            opacity: 0.5;
            pointer-events: none;
        }

        .board-row {
            display: grid;
            grid-template-columns: auto 1fr auto auto;
            align-items: center;
            gap: 10px;
            padding: 11px 4px;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.07);
        }
        .board-row:last-child { border-bottom: none; }
        .row-code { font-family: 'JetBrains Mono', ui-monospace, monospace; font-size: 0.6875rem; color: rgba(255, 255, 255, 0.35); white-space: nowrap; }
        .row-title { font-size: 0.8125rem; font-weight: 600; color: #F5F1E8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .row-platform { font-family: 'JetBrains Mono', ui-monospace, monospace; font-size: 0.625rem; font-weight: 700; letter-spacing: 0.06em; padding: 2px 7px; border-radius: 5px; }
        .row-platform.fvr { background: rgba(22, 184, 160, 0.16); color: #5FE2CE; }
        .row-platform.upw { background: rgba(255, 178, 56, 0.16); color: #FFC94D; }
        .flap-face { display: inline-flex; align-items: center; justify-content: center; padding: 2px 9px; border-radius: 5px; font-family: 'JetBrains Mono', ui-monospace, monospace; font-size: 0.625rem; font-weight: 700; letter-spacing: 0.06em; }
        .flap-face.pend { background: rgba(255, 178, 56, 0.16); color: #FFC94D; }
        .flap-face.tran { background: rgba(255, 90, 31, 0.18); color: #FFA26E; }
        .flap-face.dlvr { background: rgba(22, 184, 160, 0.18); color: #5FE2CE; }

        .board-live-dot {
            display: inline-block; width: 7px; height: 7px; border-radius: 9999px; background: #FF5A1F;
            animation: livePulse 1.8s ease-in-out infinite;
        }
        @keyframes livePulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 90, 31, 0.55); }
            70% { box-shadow: 0 0 0 8px rgba(255, 90, 31, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 90, 31, 0); }
        }

        .input-field { transition: border-color .2s ease, box-shadow .2s ease; border: 1.5px solid #e5e7eb; }
        .input-field:focus { border-color: #FF5A1F; box-shadow: 0 0 0 4px rgba(255, 90, 31, 0.12); }

        .reveal { opacity: 0; transform: translateY(14px); animation: revealIn .6s ease forwards; }
        .reveal-delay-1 { animation-delay: .08s; }
        .reveal-delay-2 { animation-delay: .16s; }
        @keyframes revealIn { to { opacity: 1; transform: none; } }

        @media (prefers-reduced-motion: reduce) {
            .board-live-dot { animation: none; }
            .reveal { animation: none; opacity: 1; transform: none; }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 lg:p-8">

    <div class="w-full max-w-5xl grid lg:grid-cols-2 rounded-3xl overflow-hidden shadow-2xl shadow-ember-900/10">

        <!-- Left: brand panel (hidden on small screens) -->
        <div class="hidden lg:flex relative flex-col justify-between bg-ink p-10 overflow-hidden">
            <div class="absolute inset-0 gradient-mesh"></div>
            <div class="glow-blob bg-ember-500 w-72 h-72 -top-16 -left-16"></div>
            <div class="glow-blob bg-gold-400 w-64 h-64 bottom-0 -right-10"></div>

            <div class="relative reveal">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                    <x-site-logo img-class="h-9 w-auto max-w-[150px] object-contain" badge-class="h-9 w-9 text-base" />
                    <span class="font-display font-semibold text-lg text-white">{{ $siteName }}</span>
                </a>

                <h1 class="font-display mt-10 text-3xl font-bold tracking-tight text-white leading-[1.15]">
                    Run your agency like<br>a dispatch tower
                </h1>
                <p class="mt-4 text-sm text-white/55 leading-relaxed max-w-sm">
                    Track every Fiverr &amp; Upwork order, assign the right team, and see real delivered revenue — all in one command center.
                </p>
            </div>

            <div class="relative reveal reveal-delay-1 rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                <div class="flex items-center gap-2 mb-1 eyebrow">
                    <span class="board-live-dot"></span> Live dispatch
                </div>
                <div class="mt-2">
                    <div class="board-row">
                        <span class="row-code">ORD-2041</span>
                        <span class="row-title">Logo animation</span>
                        <span class="row-platform fvr">FVR</span>
                        <span class="flap-face pend">PEND</span>
                    </div>
                    <div class="board-row">
                        <span class="row-code">ORD-2039</span>
                        <span class="row-title">Shopify theme</span>
                        <span class="row-platform upw">UPW</span>
                        <span class="flap-face tran">TRAN</span>
                    </div>
                    <div class="board-row">
                        <span class="row-code">ORD-2034</span>
                        <span class="row-title">Brand kit</span>
                        <span class="row-platform fvr">FVR</span>
                        <span class="flap-face dlvr">DLVR</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: create account form -->
        <div class="bg-white p-8 sm:p-10 lg:p-12 flex flex-col justify-center">

            <!-- Mobile-only logo -->
            <a href="{{ route('home') }}" class="lg:hidden inline-flex items-center gap-2.5 mb-8 self-center">
                <x-site-logo img-class="h-9 w-auto max-w-[150px] object-contain" badge-class="h-9 w-9 text-base" />
                <span class="font-display font-semibold text-lg text-gray-900">{{ $siteName }}</span>
            </a>

            <div class="mb-8 text-center lg:text-left reveal">
                <h2 class="font-display text-2xl font-bold text-gray-900">Create your account</h2>
                <p class="mt-1 text-gray-500 text-sm">Get started with {{ $siteName }}</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5 reveal reveal-delay-1">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="name">Name</label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Your full name"
                        class="input-field w-full px-4 py-2.5 rounded-lg outline-none text-gray-900 placeholder-gray-400"
                    >
                    @error('name')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="email">Email address</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="username"
                        placeholder="you@example.com"
                        class="input-field w-full px-4 py-2.5 rounded-lg outline-none text-gray-900 placeholder-gray-400"
                    >
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="password">Password</label>
                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                            class="input-field w-full px-4 py-2.5 rounded-lg outline-none text-gray-900 placeholder-gray-400 pr-11"
                        >
                        <button
                            type="button"
                            onclick="togglePassword('password', 'password-toggle-icon')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                            aria-label="Show password"
                        >
                            <i id="password-toggle-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5" for="password_confirmation">Confirm password</label>
                    <div class="relative">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                            class="input-field w-full px-4 py-2.5 rounded-lg outline-none text-gray-900 placeholder-gray-400 pr-11"
                        >
                        <button
                            type="button"
                            onclick="togglePassword('password_confirmation', 'password-confirmation-toggle-icon')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
                            aria-label="Show password"
                        >
                            <i id="password-confirmation-toggle-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full py-3 rounded-lg text-white font-semibold bg-gradient-to-r from-ember-600 to-gold-500 shadow-lg shadow-ember-500/25 hover:shadow-ember-500/40 transition-shadow"
                >
                    Create account <i class="fas fa-arrow-right text-xs ml-1"></i>
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500 reveal reveal-delay-2">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-ember-600 hover:text-ember-700 transition-colors">Sign in</a>
            </p>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
