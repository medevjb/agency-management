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
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

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
