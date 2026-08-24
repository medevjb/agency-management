<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $siteLogo = \App\Models\Setting::get('site_logo');
        $siteName = \App\Models\Setting::get('site_name', config('app.name', 'Orbit'));
        $siteDescription = \App\Models\Setting::get('site_description', 'Run your Fiverr & Upwork agency from one command center.');
        $companyName = \App\Models\Setting::get('company_name', $siteName);
        $companyEmail = \App\Models\Setting::get('company_email');
        $companyPhone = \App\Models\Setting::get('company_phone');
    @endphp

    <title>{{ $siteName }} — Agency order management for Fiverr & Upwork</title>
    <meta name="description" content="{{ $siteDescription }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|bricolage-grotesque:500,600,700,800|jetbrains-mono:500,600,700" rel="stylesheet" />

    <!-- Favicon (dispatch mark) -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 40 40'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0' y1='0' x2='40' y2='40' gradientUnits='userSpaceOnUse'%3E%3Cstop offset='0' stop-color='%23FFC94D'/%3E%3Cstop offset='1' stop-color='%23FF5A1F'/%3E%3C/linearGradient%3E%3C/defs%3E%3Crect width='40' height='40' rx='9' fill='url(%23g)'/%3E%3Crect y='17' width='40' height='6' fill='%230B0E14'/%3E%3Cpath d='M15 20 L22 15 L22 18.5 L27 18.5 L27 21.5 L22 21.5 L22 25 Z' fill='%230B0E14'/%3E%3C/svg%3E">

    <!-- Styles -->
    @vite('resources/css/landing.css')

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" referrerpolicy="no-referrer" />

    <!-- Alpine (CDN) for nav + FAQ interactions (collapse plugin must load before core) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="landing antialiased">

    @include('landing.partials.nav')

    <main>
        @include('landing.partials.hero')
        @include('landing.partials.trust-bar')
        @include('landing.partials.features')
        @include('landing.partials.how-it-works')
        @include('landing.partials.stats')
        @include('landing.partials.testimonials')
        @include('landing.partials.pricing')
        @include('landing.partials.faq')
        @include('landing.partials.cta')
    </main>

    @include('landing.partials.footer')

    <!-- GSAP + ScrollTrigger (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>

    @include('landing.partials.scripts')
</body>
</html>
