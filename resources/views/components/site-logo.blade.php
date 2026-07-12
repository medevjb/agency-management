@props([
    'imgClass' => 'h-9 w-auto max-w-[150px] object-contain',
    'badgeClass' => 'h-9 w-9 text-base',
    'showName' => false,
    'nameClass' => 'font-bold text-lg tracking-tight text-gray-900',
])

@php
    $logo = \App\Models\Setting::get('site_logo');
    $name = \App\Models\Setting::get('site_name', config('app.name', 'PMT'));
    $initial = strtoupper(mb_substr(trim((string) $name), 0, 1)) ?: 'P';
@endphp

@if($logo)
    <img src="{{ asset('storage/'.$logo) }}" alt="{{ $name }}" class="{{ $imgClass }}">
@else
    <span class="inline-flex items-center justify-center rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 text-white font-bold shadow-lg shadow-indigo-500/30 {{ $badgeClass }}">
        {{ $initial }}
    </span>
    @if($showName)
        <span class="{{ $nameClass }}">{{ $name }}</span>
    @endif
@endif
