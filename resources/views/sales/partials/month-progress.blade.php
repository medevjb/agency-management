@php
    $month = $dashboard['currentMonth'];
    $period = $dashboard['period'];

    // The track spans $0 to whichever is larger, sales or target. The filled
    // part is what has been sold — up to the target it reads as target zone,
    // anything past it as bonus zone. The mark sits where the target falls,
    // so a short month shows an unfilled run before it.
    $trackMax = max($month['sales'], $month['target']);
    $filledPct = $trackMax > 0 ? round(min($month['sales'], $month['target']) / $trackMax * 100, 2) : 0;
    $bonusZonePct = $trackMax > 0 ? round(max(0, $month['sales'] - $month['target']) / $trackMax * 100, 2) : 0;
    $markPct = $trackMax > 0 ? round($month['target'] / $trackMax * 100, 2) : 0;
@endphp

<div class="s-panel p-5 sm:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <p class="s-eyebrow">{{ $period['month'] }} target progress</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Where this month's sales sit against your target.
            </p>
        </div>
        @include('sales.partials.status-badge', ['status' => $month['achieved'] ? 'achieved' : 'in-progress'])
    </div>

    <div class="s-zone">
        <div class="s-zone-seg is-target" style="width: {{ $filledPct }}%"></div>
        @if($bonusZonePct > 0)
            <div class="s-zone-seg is-bonus" style="left: {{ $markPct }}%; width: {{ $bonusZonePct }}%"></div>
        @endif
        <div class="s-zone-mark" style="left: {{ $markPct }}%"></div>
    </div>

    <div class="s-zone-labels">
        <span>$0</span>
        <span><strong>Target</strong> ${{ number_format($month['target'], 0) }}</span>
        <span><strong>You</strong> ${{ number_format($month['sales'], 0) }}</span>
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-4">
        <span class="s-zone-key"><i class="is-target"></i>Target zone</span>
        <span class="s-zone-key"><i class="is-bonus"></i>Bonus zone</span>
        <span class="s-stat-label s-num ml-auto">{{ number_format($month['achievement'], 1) }}% of target</span>
    </div>

    @if($month['achieved'])
        <div class="s-callout is-emerald mt-4">
            <i class="fas fa-check-circle text-emerald-500 mt-0.5"></i>
            <p class="text-sm text-emerald-800 dark:text-emerald-300">
                You are <span class="s-num font-bold">${{ number_format($month['extraSales'], 2) }}</span> above your
                {{ $period['month'] }} target. That extra is what earns your bonus.
            </p>
        </div>
    @else
        <div class="s-callout is-amber mt-4">
            <i class="fas fa-lock text-amber-500 mt-0.5"></i>
            <p class="text-sm text-amber-800 dark:text-amber-300">
                <span class="s-num font-bold">${{ number_format($month['unlockNeeded'], 2) }}</span> more sales needed to unlock your monthly bonus.
            </p>
        </div>
    @endif
</div>
