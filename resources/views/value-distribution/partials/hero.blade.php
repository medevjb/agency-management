{{--
    The month's balance strip. Shared by the server-rendered page and the
    AJAX lists endpoint so a save updates it without a reload.

    Expects: $openTotal, $distributedTotal, $pendingCount, $distributedCount,
             $monthDate (Carbon).
--}}
@php
    $totalLedger = $distributedTotal + $openTotal;
    $reconciledPercent = $totalLedger > 0 ? round(($distributedTotal / $totalLedger) * 100) : null;
    $hasAnyOrders = ($pendingCount + $distributedCount) > 0;
    $isFullyReconciled = $hasAnyOrders && $openTotal < 0.01;
@endphp
<div class="vd-hero" id="vdHero">
    @if(! $hasAnyOrders)
        <div class="vd-hero-body">
            <div>
                <p class="vd-hero-eyebrow">{{ $monthDate->format('F') }}</p>
                <p class="vd-hero-figure is-neutral vd-mono">—</p>
                <p class="vd-hero-sub">No completed orders yet this month. Once operations marks one complete, it'll show up here waiting to be credited.</p>
            </div>
        </div>
    @elseif($isFullyReconciled)
        <div class="vd-hero-done">
            <span class="vd-hero-done-badge"><i class="fas fa-check"></i></span>
            <div>
                <p class="vd-hero-eyebrow">{{ $monthDate->format('F') }} — fully reconciled</p>
                <p class="mt-1 text-xl font-bold text-white">You're all caught up</p>
                <p class="vd-hero-sub mt-1">
                    <strong class="vd-mono">${{ number_format($distributedTotal, 2) }}</strong> distributed across
                    {{ $distributedCount }} {{ \Illuminate\Support\Str::plural('order', $distributedCount) }}. Nothing owed.
                </p>
            </div>
        </div>
    @else
        <div class="vd-hero-body">
            <div>
                <p class="vd-hero-eyebrow">{{ $monthDate->format('F') }} balance</p>
                <p class="vd-hero-figure is-open vd-mono">${{ number_format($openTotal, 2) }}</p>
                <p class="vd-hero-sub">
                    still open across <strong>{{ $pendingCount }} {{ \Illuminate\Support\Str::plural('order', $pendingCount) }}</strong>
                    — credit them below to close out the month.
                </p>
            </div>
            <div class="vd-hero-facts">
                <p class="vd-hero-fact-value vd-mono">{{ $distributedCount }} / {{ $pendingCount + $distributedCount }}</p>
                <p class="vd-hero-fact-label">orders settled</p>
            </div>
        </div>

        <div class="vd-hero-meter" role="img" aria-label="{{ $reconciledPercent }}% of this month's value has been distributed">
            <span class="vd-hero-meter-settled" style="width: {{ $totalLedger > 0 ? ($distributedTotal / $totalLedger * 100) : 0 }}%"></span>
            <span class="vd-hero-meter-open" style="width: {{ $totalLedger > 0 ? ($openTotal / $totalLedger * 100) : 0 }}%"></span>
        </div>
        <div class="vd-hero-legend">
            <span><span class="vd-dot is-settled"></span>Settled <span class="vd-mono">${{ number_format($distributedTotal, 2) }}</span></span>
            <span><span class="vd-dot is-open"></span>Open <span class="vd-mono">${{ number_format($openTotal, 2) }}</span></span>
            @if($reconciledPercent !== null)
                <span>{{ $reconciledPercent }}% reconciled</span>
            @endif
        </div>
    @endif
</div>