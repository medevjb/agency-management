@php
    // Fixed hex pairs rather than dynamic Tailwind color classes, so this
    // never depends on the JIT purge having seen a particular `bg-{color}-100`
    // combination somewhere else in the codebase.
    $iconGradient = match ($color) {
        'green' => ['#10b981', '#34d399'],
        'yellow' => ['#f59e0b', '#fbbf24'],
        'purple' => ['#8b5cf6', '#a78bfa'],
        'orange' => ['#f97316', '#fb923c'],
        'red' => ['#ef4444', '#f87171'],
        default => ['#6366f1', '#8b5cf6'],
    };
@endphp

<div class="dash-panel p-6">
    <div class="flex items-center justify-between">
        <div class="min-w-0">
            <p class="dash-stat-label mb-1">{{ $title }}</p>
            <p class="dash-hero dash-num" style="font-size: 1.75rem;">{{ $value }}</p>
            @if($description)
                <p class="dash-stat-foot">{{ $description }}</p>
            @endif
            @if($trend && $trendValue)
                <p class="dash-stat-foot {{ $trend === 'up' ? 'text-green-600 dark:text-green-400' : ($trend === 'down' ? 'text-red-600 dark:text-red-400' : '') }}">
                    @if($trend === 'up')
                        <i class="fas fa-arrow-up mr-1"></i>
                    @elseif($trend === 'down')
                        <i class="fas fa-arrow-down mr-1"></i>
                    @else
                        <i class="fas fa-minus mr-1"></i>
                    @endif
                    {{ $trendValue }}
                </p>
            @endif
        </div>
        @if($icon)
            <div class="dash-icon" style="--icon-from: {{ $iconGradient[0] }}; --icon-to: {{ $iconGradient[1] }};">
                <i class="{{ $icon }}"></i>
            </div>
        @endif
    </div>
</div>
