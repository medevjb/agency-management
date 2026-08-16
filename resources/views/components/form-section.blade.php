@props([
    'icon' => null,
    'title' => '',
    'description' => null,
])

<section class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-800">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
        @if($icon)
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white flex-shrink-0">
                <i class="fas {{ $icon }}"></i>
            </div>
        @endif
        <div class="min-w-0">
            <h2 class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $title }}</h2>
            @if($description)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $description }}</p>
            @endif
        </div>
        @isset($header)
            <div class="ml-auto flex-shrink-0">
                {{ $header }}
            </div>
        @endisset
    </div>
    <div class="p-6 space-y-6">
        {{ $slot }}
    </div>
</section>