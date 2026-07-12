@props([
    'name',
    'url',
    'value'       => null,
    'label'       => null,
    'placeholder' => 'Search...',
    'badge'       => null,
])

@php
    $uid     = 'as_' . preg_replace('/[^a-z0-9]/i', '_', $name) . '_' . substr(md5(uniqid()), 0, 6);
    $current = old($name, $value);
@endphp

<div class="relative"
     data-ajax-select
     data-url="{{ $url }}"
     data-badge="{{ $badge ?? '' }}"
     data-uid="{{ $uid }}">

    <input type="hidden"
           id="{{ $uid }}_h"
           name="{{ $name }}"
           value="{{ $current }}">

    <input type="text"
           id="{{ $uid }}_s"
           placeholder="{{ $placeholder }}"
           autocomplete="off"
           value="{{ $current ? $label : '' }}"
           class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white text-sm {{ $current ? 'border-indigo-400 bg-indigo-50' : '' }}">

    <button type="button"
            id="{{ $uid }}_c"
            class="{{ $current ? '' : 'hidden' }} absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
        <i class="fas fa-times text-xs"></i>
    </button>

    <div id="{{ $uid }}_d"
         class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
    </div>
</div>
