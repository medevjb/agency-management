@props([
    'imgClass' => 'h-9 w-auto max-w-[150px] object-contain',
    'badgeClass' => 'h-9 w-9 text-base',
    'showName' => false,
    'nameClass' => 'font-bold text-lg tracking-tight text-gray-900',
])

@php
    $logo = \App\Models\Setting::get('site_logo');
    $name = \App\Models\Setting::get('site_name', config('app.name', 'Orbit'));
@endphp

@if($logo)
    <img src="{{ asset('storage/'.$logo) }}" alt="{{ $name }}" class="{{ $imgClass }}">
@else
    <span class="inline-flex items-center justify-center rounded-[28%] overflow-hidden shadow-lg shadow-ember-500/30 {{ $badgeClass }}">
        <svg viewBox="0 0 40 40" class="h-full w-full" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="{{ $name }}">
            <defs>
                <linearGradient id="siteLogoGrad" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
                    <stop offset="0" stop-color="#FFC94D"/>
                    <stop offset="1" stop-color="#FF5A1F"/>
                </linearGradient>
            </defs>
            <rect width="40" height="40" fill="url(#siteLogoGrad)"/>
            <rect y="17" width="40" height="6" fill="#0B0E14"/>
            <path d="M15 20 L22 15 L22 18.5 L27 18.5 L27 21.5 L22 21.5 L22 25 Z" fill="#0B0E14"/>
        </svg>
    </span>
    @if($showName)
        <span class="{{ $nameClass }}">{{ $name }}</span>
    @endif
@endif
