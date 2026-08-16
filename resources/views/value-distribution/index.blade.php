@extends('layouts.dashboard')

@section('title', 'Value Distribution')

@push('styles')
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=jetbrains-mono:500,600,700,800&display=swap" rel="stylesheet">
<style>
    /* A monospace face reserved for money and counts only — everything else
       on the page stays in the app's normal Figtree. Ledgers align digits
       in columns for a reason; this is that reason, not a theme choice. */
    .vd-mono {
        font-family: 'JetBrains Mono', ui-monospace, 'SFMono-Regular', Menlo, monospace;
        font-variant-numeric: tabular-nums;
        font-feature-settings: 'tnum';
    }

    .vd-panel {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
    }

    .dark .vd-panel { background-color: #111827; border-color: #1f2937; box-shadow: none; }

    .vd-eyebrow {
        font-size: 0.6875rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #6b7280;
    }

    .dark .vd-eyebrow { color: #9ca3af; }

    .vd-empty { border: 1px dashed #e5e7eb; border-radius: 0.875rem; padding: 2.5rem 1.5rem; text-align: center; }
    .dark .vd-empty { border-color: #1f2937; }

    /* ==================================================================
       Balance strip — the page's one signature moment. Everything this
       page exists to do collapses into one number: how much is still
       unpaid. Drive it to zero and the strip changes state to say so.
       ================================================================== */
    .vd-hero {
        position: relative;
        overflow: hidden;
        border-radius: 1.25rem;
        padding: 2rem clamp(1.25rem, 4vw, 2.25rem) 1.875rem;
        background: linear-gradient(160deg, #0b1120 0%, #172033 55%, #0b1120 100%);
        box-shadow: 0 24px 48px -24px rgba(2, 6, 23, 0.55);
        color: #f8fafc;
    }

    /* Faint horizontal rules, like ruled ledger paper — quiet, not a texture stunt. */
    .vd-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: repeating-linear-gradient(
            0deg, rgba(255, 255, 255, 0.035) 0px, rgba(255, 255, 255, 0.035) 1px,
            transparent 1px, transparent 2.75rem
        );
        pointer-events: none;
    }

    .vd-hero-body { position: relative; display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 1.5rem; }
    .vd-hero-eyebrow { font-size: 0.6875rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: #93a3ba; }

    .vd-hero-figure {
        margin-top: 0.375rem;
        font-weight: 700;
        font-size: clamp(2.25rem, 5.5vw, 3.5rem);
        line-height: 1;
        letter-spacing: -0.02em;
    }

    .vd-hero-figure.is-open { color: #fbbf24; }
    .vd-hero-figure.is-settled { color: #34d399; }
    .vd-hero-figure.is-neutral { color: #f1f5f9; }

    .vd-hero-sub { margin-top: 0.5rem; font-size: 0.9375rem; color: #cbd5e1; max-width: 34rem; }
    .vd-hero-sub strong { color: #f8fafc; font-weight: 700; }

    .vd-hero-facts { position: relative; z-index: 1; display: flex; flex-direction: column; align-items: flex-end; gap: 0.375rem; text-align: right; flex-shrink: 0; }
    .vd-hero-fact-value { font-size: 1.375rem; font-weight: 700; color: #f8fafc; }
    .vd-hero-fact-label { font-size: 0.75rem; color: #93a3ba; }

    .vd-hero-meter { position: relative; margin-top: 1.625rem; height: 0.625rem; border-radius: 9999px; background-color: rgba(255, 255, 255, 0.08); overflow: hidden; display: flex; }
    .vd-hero-meter-settled { display: block; height: 100%; background-image: linear-gradient(90deg, #059669, #34d399); transition: width 0.5s ease; }
    .vd-hero-meter-open { display: block; height: 100%; background-image: linear-gradient(90deg, #b45309, #fbbf24); transition: width 0.5s ease; }

    .vd-hero-legend { position: relative; margin-top: 0.75rem; display: flex; flex-wrap: wrap; gap: 1.5rem; font-size: 0.75rem; color: #93a3ba; }
    .vd-hero-legend .vd-dot { display: inline-block; width: 0.5rem; height: 0.5rem; border-radius: 9999px; margin-right: 0.4375rem; }
    .vd-hero-legend .vd-dot.is-settled { background-color: #34d399; }
    .vd-hero-legend .vd-dot.is-open { background-color: #fbbf24; }

    .vd-hero-done { position: relative; display: flex; align-items: center; gap: 1.125rem; }
    .vd-hero-done-badge {
        flex-shrink: 0;
        width: 3.5rem; height: 3.5rem;
        border-radius: 9999px;
        background-color: rgba(52, 211, 153, 0.14);
        border: 2px solid #34d399;
        display: flex; align-items: center; justify-content: center;
        color: #34d399; font-size: 1.375rem;
    }

    /* ---- Leaderboard rail ---- */
    .vd-board-row {
        display: grid;
        grid-template-columns: 1.75rem minmax(0, 1fr) auto;
        align-items: center;
        gap: 0.875rem;
        padding: 0.625rem 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .vd-board-row:last-child { border-bottom: 0; }
    .dark .vd-board-row { border-bottom-color: #1f2937; }

    .vd-board-rank {
        display: flex; align-items: center; justify-content: center;
        width: 1.75rem; height: 1.75rem;
        border-radius: 9999px;
        font-size: 0.75rem; font-weight: 800;
        background-color: #f3f4f6; color: #6b7280;
    }

    .dark .vd-board-rank { background-color: #1f2937; color: #9ca3af; }
    .vd-board-rank.rank-1 { background-image: linear-gradient(135deg, #f59e0b, #fbbf24); color: #fff; }
    .vd-board-rank.rank-2 { background-image: linear-gradient(135deg, #94a3b8, #cbd5e1); color: #fff; }
    .vd-board-rank.rank-3 { background-image: linear-gradient(135deg, #b45309, #d97706); color: #fff; }

    .vd-board-name { font-size: 0.875rem; font-weight: 700; color: #111827; }
    .dark .vd-board-name { color: #f3f4f6; }

    .vd-progress { height: 0.3125rem; margin-top: 0.375rem; border-radius: 9999px; background-color: #eef2f7; overflow: hidden; }
    .dark .vd-progress { background-color: #1f2937; }
    .vd-progress span { display: block; height: 100%; border-radius: 9999px; }
    .vd-progress span.is-met { background-image: linear-gradient(90deg, #059669, #10b981); }
    .vd-progress span.is-close { background-image: linear-gradient(90deg, #d97706, #f59e0b); }
    .vd-progress span.is-behind { background-image: linear-gradient(90deg, #dc2626, #f87171); }
    .vd-progress span.is-none { background-color: #cbd5e1; }

    .vd-board-meta { margin-top: 0.25rem; font-size: 0.6875rem; color: #9ca3af; }
    .vd-board-amount { font-size: 0.9375rem; font-weight: 800; color: #111827; }
    .dark .vd-board-amount { color: #f3f4f6; }

    .vd-workspace {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        grid-template-areas: "pending" "board";
        gap: 1.5rem;
        align-items: start;
    }

    @media (min-width: 1024px) {
        .vd-workspace {
            grid-template-columns: minmax(0, 1fr) 21rem;
            grid-template-areas: "pending board";
        }
        .vd-workspace-board { position: sticky; top: 1.25rem; }
    }

    .vd-workspace-pending { grid-area: pending; min-width: 0; }
    .vd-workspace-board { grid-area: board; }

    /* ---- Search ---- */
    .vd-search-form { position: relative; }
    .vd-search-input {
        width: 100%;
        padding: 0.625rem 0.875rem 0.625rem 2.25rem;
        border: 1px solid #d1d5db;
        border-radius: 0.625rem;
        font-size: 0.8125rem;
        background-color: #ffffff;
        color: #111827;
    }

    .dark .vd-search-input { background-color: #1f2937; border-color: #374151; color: #f3f4f6; }
    .vd-search-icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem; }
    .vd-search-clear { position: absolute; right: 0.625rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 0.75rem; }
    .vd-search-clear:hover { color: #6b7280; }

    /* ---- Settled disclosure ---- */
    .vd-settled-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 0.875rem 1.125rem;
        text-align: left;
    }

    .vd-settled-toggle-label { font-size: 0.6875rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: #6b7280; }
    .dark .vd-settled-toggle-label { color: #9ca3af; }
    .vd-settled-toggle-action { display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 700; color: #4f46e5; }
    .dark .vd-settled-toggle-action { color: #a5b4fc; }
    .vd-settled-toggle-action i { transition: transform 0.2s ease; font-size: 0.75rem; }
    .vd-settled-toggle[aria-expanded='true'] .vd-settled-toggle-action i { transform: rotate(180deg); }
    .vd-settled-body[hidden] { display: none; }
    .vd-settled-body { padding: 0 1.125rem 1.125rem; }

    /* ---- Pager ---- */
    .vd-pager { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; margin-top: 1rem; }
    .vd-pager-range { font-size: 0.75rem; color: #6b7280; }
    .dark .vd-pager-range { color: #9ca3af; }
    .vd-pager-of { font-family: inherit; font-variant-numeric: normal; }
    .vd-pager-nav { display: flex; align-items: center; gap: 0.5rem; }
    .vd-pager-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 2rem; height: 2rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        color: #374151;
        font-size: 0.75rem;
    }

    .vd-pager-btn:hover { background-color: #f3f4f6; }
    .vd-pager-btn.is-disabled { opacity: 0.35; pointer-events: none; }
    .dark .vd-pager-btn { border-color: #374151; color: #d1d5db; }
    .dark .vd-pager-btn:hover { background-color: #1f2937; }
    .vd-pager-page { font-size: 0.75rem; font-weight: 700; color: #6b7280; }
    .dark .vd-pager-page { color: #9ca3af; }

    /* ==================================================================
       Order cards — same interaction contract as before (accordion, live
       remaining total, add-person search, balance button, AJAX save);
       only the surface treatment changes.
       ================================================================== */
    .dist-card { border: 1px solid #e5e7eb; border-radius: 0.875rem; overflow: hidden; background-color: #ffffff; }
    .dark .dist-card { border-color: #1f2937; background-color: #111827; }
    .dist-card.is-pending { border-left: 3px solid #f59e0b; }
    .dist-card.is-complete { border-left: 3px solid #10b981; }

    .dist-summary { display: grid; grid-template-columns: 0.75rem minmax(0, 1fr) auto 1rem; align-items: center; gap: 0.875rem; width: 100%; padding: 0.875rem 1rem; text-align: left; }
    .dist-summary:hover { background-color: #f9fafb; }
    .dark .dist-summary:hover { background-color: rgba(31, 41, 55, 0.5); }

    .dist-status-dot { width: 0.5rem; height: 0.5rem; border-radius: 9999px; }
    .dist-status-dot.is-pending { background-color: #f59e0b; }
    .dist-status-dot.is-complete { background-color: #10b981; }

    .dist-summary-main { min-width: 0; }
    .dist-order-name { display: block; font-size: 0.875rem; font-weight: 700; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .dark .dist-order-name { color: #f3f4f6; }
    .dist-order-meta { display: block; margin-top: 0.125rem; font-size: 0.75rem; color: #9ca3af; }

    .dist-summary-figures { display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem; }
    .dist-net-amount { font-size: 0.9375rem; font-weight: 800; color: #111827; }
    .dark .dist-net-amount { color: #f3f4f6; }

    .dist-badge { padding: 0.0625rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; white-space: nowrap; }
    .dist-badge.is-pending { background-color: #fef3c7; color: #b45309; }
    .dist-badge.is-complete { background-color: #d1fae5; color: #047857; }
    .dark .dist-badge.is-pending { background-color: rgba(245, 158, 11, 0.15); color: #fcd34d; }
    .dark .dist-badge.is-complete { background-color: rgba(16, 185, 129, 0.15); color: #6ee7b7; }

    .dist-chevron { color: #9ca3af; transition: transform 0.2s ease; }
    .dist-summary[aria-expanded='true'] .dist-chevron { transform: rotate(180deg); }

    .dist-body { padding: 0 1rem 1.125rem; border-top: 1px solid #f3f4f6; }
    .dark .dist-body { border-top-color: #1f2937; }

    .dist-fee-line { margin: 0.875rem 0; font-size: 0.75rem; color: #6b7280; }
    .dark .dist-fee-line { color: #9ca3af; }

    .dist-rows { display: flex; flex-direction: column; gap: 0.5rem; }
    .dist-row { display: grid; grid-template-columns: minmax(0, 1fr) 9rem 2.25rem; align-items: center; gap: 0.5rem; }

    .dist-person { font-size: 0.875rem; font-weight: 600; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .dark .dist-person { color: #f3f4f6; }

    .dist-tag { margin-left: 0.375rem; padding: 0.0625rem 0.375rem; border-radius: 9999px; font-size: 0.5625rem; font-weight: 800; text-transform: uppercase; background-color: #eef2ff; color: #4338ca; }
    .dark .dist-tag { background-color: rgba(99, 102, 241, 0.15); color: #a5b4fc; }

    .dist-amount-wrap { position: relative; }
    .dist-amount { width: 100%; padding: 0.5rem 0.75rem 0.5rem 1.5rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.8125rem; background-color: #ffffff; color: #111827; }
    .dark .dist-amount { background-color: #1f2937; border-color: #374151; color: #f3f4f6; }
    /* Same font as the amount input next to it — a proportional "$" beside
       monospace digits reads as misaligned even when the boxes are exact. */
    .dist-currency { position: absolute; left: 0.625rem; top: 50%; transform: translateY(-50%); font-size: 0.8125rem; color: #9ca3af; font-family: 'JetBrains Mono', ui-monospace, 'SFMono-Regular', Menlo, monospace; }

    .dist-remove { display: flex; align-items: center; justify-content: center; height: 2.125rem; border-radius: 0.5rem; color: #9ca3af; }
    .dist-remove:hover { background-color: #fee2e2; color: #b91c1c; }
    .dark .dist-remove:hover { background-color: rgba(239, 68, 68, 0.15); color: #fca5a5; }

    .dist-empty-hint { font-size: 0.8125rem; color: #9ca3af; }

    .dist-controls { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; margin-top: 0.75rem; }
    .dist-search-wrap { position: relative; flex: 1; min-width: 12rem; }
    .dist-search { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.8125rem; background-color: #ffffff; color: #111827; }
    .dark .dist-search { background-color: #1f2937; border-color: #374151; color: #f3f4f6; }

    .dist-search-results { position: absolute; z-index: 20; top: calc(100% + 0.25rem); left: 0; right: 0; max-height: 12rem; overflow-y: auto; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.5rem; box-shadow: 0 8px 20px rgba(16, 24, 40, 0.12); }
    .dark .dist-search-results { background-color: #1f2937; border-color: #374151; }
    .dist-search-result { display: block; width: 100%; padding: 0.5rem 0.75rem; text-align: left; font-size: 0.8125rem; color: #111827; }
    .dark .dist-search-result { color: #f3f4f6; }
    .dist-search-result:hover { background-color: #f3f4f6; }
    .dark .dist-search-result:hover { background-color: #111827; }

    .dist-balance-btn { font-size: 0.75rem; font-weight: 700; color: #4f46e5; white-space: nowrap; }
    .dist-balance-btn:hover { text-decoration: underline; }
    .dark .dist-balance-btn { color: #a5b4fc; }

    .dist-footer { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; margin-top: 1rem; padding-top: 0.875rem; border-top: 1px dashed #e5e7eb; }
    .dark .dist-footer { border-top-color: #1f2937; }

    .dist-remaining { font-family: 'JetBrains Mono', ui-monospace, 'SFMono-Regular', Menlo, monospace; font-size: 0.75rem; font-weight: 700; padding: 0.1875rem 0.625rem; border-radius: 9999px; }
    .dist-remaining.is-balanced { background-color: #d1fae5; color: #047857; }
    .dist-remaining.is-left { background-color: #fef3c7; color: #b45309; }
    .dist-remaining.is-over { background-color: #fee2e2; color: #b91c1c; }
    .dark .dist-remaining.is-balanced { background-color: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
    .dark .dist-remaining.is-left { background-color: rgba(245, 158, 11, 0.15); color: #fcd34d; }
    .dark .dist-remaining.is-over { background-color: rgba(239, 68, 68, 0.15); color: #fca5a5; }

    .dist-footer-actions { display: flex; align-items: center; gap: 0.75rem; }
    .dist-view-link { font-size: 0.75rem; font-weight: 600; color: #6b7280; }
    .dist-view-link:hover { text-decoration: underline; }
    .dark .dist-view-link { color: #9ca3af; }

    .dist-save-btn { padding: 0.4375rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; font-weight: 700; color: #ffffff; background-image: linear-gradient(90deg, #4f46e5, #7c3aed); }
    .dist-save-btn:hover { filter: brightness(1.08); }
    .dist-save-btn[disabled] { opacity: 0.6; cursor: progress; }
</style>
@endpush

@section('content')
@php
    $totalLedger = $distributedTotal + $openTotal;
    $reconciledPercent = $totalLedger > 0 ? round(($distributedTotal / $totalLedger) * 100) : null;
    $hasAnyOrders = ($pendingCount + $distributedCount) > 0;
    $isFullyReconciled = $hasAnyOrders && $openTotal < 0.01;
@endphp
<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-6xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="vd-eyebrow">Operations · Closing the books</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">Value Distribution</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Completed orders in {{ $monthDate->format('F Y') }} — credit the net value to whoever delivered them.
            </p>
        </div>

        {{-- Set once from the header picker, shared by every dashboard. --}}
        <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 text-sm font-semibold">
            <i class="fas fa-calendar-alt text-xs"></i>
            {{ $availableMonths[$selectedMonth] ?? $selectedMonth }}
        </span>
    </div>

    <!-- Balance strip -->
    <div class="vd-hero">
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

    <div class="vd-workspace">
        <!-- Pending + settled -->
        <div class="vd-workspace-pending space-y-6">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                    <p class="vd-eyebrow">Pending distribution ({{ $pendingCount }})</p>
                    <form method="GET" class="vd-search-form sm:w-64">
                        <i class="fas fa-search vd-search-icon"></i>
                        <input type="text" name="search" value="{{ request()->get('search') }}"
                               placeholder="Search by order name…" class="vd-search-input">
                        @if(request()->filled('search'))
                            <a href="{{ route('value-distribution.index') }}" class="vd-search-clear" aria-label="Clear search">
                                <i class="fas fa-xmark"></i>
                            </a>
                        @endif
                    </form>
                </div>

                <div class="space-y-3">
                    @forelse($pendingOrders as $order)
                        @include('value-distribution.partials.order-card', ['order' => $order, 'defaultOpen' => $pendingOrders->total() <= 3])
                    @empty
                        @if(request()->filled('search'))
                            <div class="vd-empty">
                                <i class="fas fa-magnifying-glass text-2xl text-gray-300 dark:text-gray-700"></i>
                                <p class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">No pending orders match "{{ request()->get('search') }}"</p>
                                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"><a href="{{ route('value-distribution.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Clear the search</a> to see everything pending.</p>
                            </div>
                        @elseif($hasAnyOrders)
                            <div class="vd-empty">
                                <i class="fas fa-circle-check text-2xl text-emerald-300 dark:text-emerald-700"></i>
                                <p class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">Nothing waiting on you.</p>
                                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Every order completed in {{ $monthDate->format('F') }} has been fully distributed.</p>
                            </div>
                        @else
                            <div class="vd-empty">
                                <i class="fas fa-inbox text-2xl text-gray-300 dark:text-gray-700"></i>
                                <p class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">No completed orders yet.</p>
                                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Nothing's been marked complete in {{ $monthDate->format('F') }} so far.</p>
                            </div>
                        @endif
                    @endforelse
                </div>

                @include('value-distribution.partials.pager', ['paginator' => $pendingOrders, 'itemLabel' => 'pending order'])
            </div>

            <!-- Settled — collapsed and paginated, so a month with hundreds of
                 closed-out orders never has to render them all at once. -->
            @if($distributedCount > 0)
                <div class="vd-panel">
                    <button type="button" class="vd-settled-toggle" id="vdSettledToggle"
                            aria-expanded="{{ request()->filled('distributed_page') ? 'true' : 'false' }}"
                            aria-controls="vdSettledBody">
                        <span class="vd-settled-toggle-label">Already distributed ({{ $distributedCount }})</span>
                        <span class="vd-settled-toggle-action">
                            <span data-label>{{ request()->filled('distributed_page') ? 'Hide' : 'Show' }}</span>
                            <i class="fas fa-chevron-{{ request()->filled('distributed_page') ? 'up' : 'down' }}"></i>
                        </span>
                    </button>
                    <div id="vdSettledBody" class="vd-settled-body" {{ request()->filled('distributed_page') ? '' : 'hidden' }}>
                        <div class="space-y-3">
                            @foreach($distributedOrders as $order)
                                @include('value-distribution.partials.order-card', ['order' => $order, 'defaultOpen' => false])
                            @endforeach
                        </div>
                        @include('value-distribution.partials.pager', ['paginator' => $distributedOrders, 'itemLabel' => 'settled order'])
                    </div>
                </div>
            @endif
        </div>

        <!-- Achievement board -->
        <div class="vd-workspace-board">
            @if($leaderboard->isNotEmpty())
                <div class="vd-panel p-5">
                    <p class="vd-eyebrow mb-3"><i class="fas fa-trophy text-amber-500 mr-1"></i> Distribution achievement · {{ $monthDate->format('F') }}</p>
                    @foreach($leaderboard as $index => $person)
                        @php
                            $tone = $person['achievement'] === null ? 'is-none' : ($person['achievement'] >= 100 ? 'is-met' : ($person['achievement'] >= 60 ? 'is-close' : 'is-behind'));
                        @endphp
                        <div class="vd-board-row">
                            <span class="vd-board-rank {{ $index < 3 ? 'rank-'.($index + 1) : '' }}">{{ $index + 1 }}</span>
                            <div class="min-w-0">
                                <p class="vd-board-name">{{ $person['name'] }}</p>
                                <div class="vd-progress">
                                    <span class="{{ $tone }}" style="width: {{ $person['achievement'] === null ? 0 : min(100, $person['achievement']) }}%"></span>
                                </div>
                                <p class="vd-board-meta vd-mono">
                                    {{ $person['orders'] }} {{ \Illuminate\Support\Str::plural('order', $person['orders']) }}
                                    @if($person['achievement'] !== null)
                                        · {{ $person['achievement'] }}% of ${{ number_format($person['target'], 2) }} target
                                    @else
                                        · no target set
                                    @endif
                                </p>
                            </div>
                            <p class="vd-board-amount vd-mono">${{ number_format($person['amount'], 2) }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="vd-panel p-5 vd-empty">
                    <i class="fas fa-trophy text-2xl text-gray-300 dark:text-gray-700"></i>
                    <p class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">No one credited yet</p>
                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Distribute an order and this board fills in.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ---- Settled section disclosure ----
        const settledToggle = document.getElementById('vdSettledToggle');
        const settledBody = document.getElementById('vdSettledBody');

        if (settledToggle && settledBody) {
            settledToggle.addEventListener('click', function() {
                const willOpen = settledBody.hidden;
                settledBody.hidden = !willOpen;
                this.setAttribute('aria-expanded', String(willOpen));
                this.querySelector('[data-label]').textContent = willOpen ? 'Hide' : 'Show';
                this.querySelector('i').className = willOpen ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
            });
        }

        // ---- Accordion toggles (per order card) ----
        document.querySelectorAll('[data-dist-toggle]').forEach(button => {
            button.addEventListener('click', function() {
                const card = this.closest('[data-distribution-card]');
                const body = card.querySelector('[data-dist-body]');
                const willOpen = body.hidden;

                body.hidden = !willOpen;
                this.setAttribute('aria-expanded', String(willOpen));
            });
        });

        // ---- Per-card distribution logic, scoped so many cards coexist ----
        document.querySelectorAll('[data-distribution-card]').forEach(initCard);

        function initCard(card) {
            const netValue = parseFloat(card.dataset.netValue) || 0;
            const rows = card.querySelector('[data-dist-rows]');
            const remaining = card.querySelector('[data-dist-remaining]');
            const search = card.querySelector('[data-dist-search]');
            const results = card.querySelector('[data-dist-results]');
            const saveBtn = card.querySelector('[data-dist-save]');
            let searchTimeout = null;

            function creditedTotal() {
                return Array.from(rows.querySelectorAll('[data-dist-amount]'))
                    .reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
            }

            function money(value) {
                return '$' + value.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function refresh() {
                const left = Math.round((netValue - creditedTotal()) * 100) / 100;

                remaining.classList.remove('is-balanced', 'is-left', 'is-over');

                if (rows.children.length === 0) {
                    remaining.textContent = '';
                } else if (left === 0) {
                    remaining.classList.add('is-balanced');
                    remaining.textContent = 'Fully allocated';
                } else if (left > 0) {
                    remaining.classList.add('is-left');
                    remaining.textContent = money(left) + ' left to credit';
                } else {
                    remaining.classList.add('is-over');
                    remaining.textContent = money(Math.abs(left)) + ' over the net value';
                }
            }

            function addRow(userId, name, tag) {
                if (rows.querySelector(`[data-dist-row][data-user-id="${userId}"]`)) {
                    return;
                }

                const row = document.createElement('div');
                row.className = 'dist-row';
                row.dataset.distRow = '';
                row.dataset.userId = userId;
                row.innerHTML = `
                    <span class="dist-person">${name}${tag ? ` <span class="dist-tag">${tag}</span>` : ''}</span>
                    <div class="dist-amount-wrap">
                        <span class="dist-currency">$</span>
                        <input type="number" step="0.01" min="0" class="dist-amount vd-mono" data-dist-amount value="" placeholder="0.00">
                    </div>
                    <button type="button" class="dist-remove" data-dist-remove aria-label="Remove"><i class="fas fa-xmark"></i></button>
                `;
                rows.appendChild(row);
                refresh();
            }

            rows.addEventListener('input', refresh);

            rows.addEventListener('click', function(event) {
                const removeBtn = event.target.closest('[data-dist-remove]');
                if (removeBtn) {
                    removeBtn.closest('[data-dist-row]').remove();
                    refresh();
                }
            });

            card.querySelector('[data-dist-balance]')?.addEventListener('click', function() {
                const first = rows.querySelector('[data-dist-amount]');
                if (!first) {
                    return;
                }

                const others = Array.from(rows.querySelectorAll('[data-dist-amount]')).slice(1)
                    .reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);

                first.value = Math.max(0, Math.round((netValue - others) * 100) / 100).toFixed(2);
                refresh();
            });

            if (search) {
                search.addEventListener('input', function() {
                    const query = this.value.trim();
                    clearTimeout(searchTimeout);

                    if (query.length < 2) {
                        results.hidden = true;
                        return;
                    }

                    searchTimeout = setTimeout(async () => {
                        try {
                            const response = await axios.get(card.dataset.searchUrl, { params: { search: query } });
                            const users = response.data.users || [];

                            results.innerHTML = users.length
                                ? users.map(user => `
                                    <button type="button" class="dist-search-result" data-user-id="${user.id}" data-user-name="${user.name}">
                                        ${user.name} <span class="text-gray-400">— ${user.email}</span>
                                    </button>
                                `).join('')
                                : '<div class="dist-search-result text-gray-400">No one found</div>';

                            results.hidden = false;
                        } catch (error) {
                            results.hidden = true;
                        }
                    }, 300);
                });

                results.addEventListener('click', function(event) {
                    const button = event.target.closest('[data-user-id]');
                    if (!button) {
                        return;
                    }

                    addRow(button.dataset.userId, button.dataset.userName, null);
                    search.value = '';
                    results.hidden = true;
                });

                document.addEventListener('click', function(event) {
                    if (!card.contains(event.target)) {
                        results.hidden = true;
                    }
                });
            }

            saveBtn?.addEventListener('click', function() {
                const shares = Array.from(rows.querySelectorAll('[data-dist-row]'))
                    .map(row => ({
                        user_id: row.dataset.userId,
                        amount: parseFloat(row.querySelector('[data-dist-amount]').value) || 0,
                    }))
                    .filter(share => share.amount > 0);

                if (shares.length === 0) {
                    Swal.fire({ title: 'Nothing to save', text: 'Enter at least one amount first.', icon: 'info' });
                    return;
                }

                saveBtn.disabled = true;
                saveBtn.textContent = 'Saving…';

                axios.post(card.dataset.storeUrl, { shares })
                    .then(({ data }) => {
                        Swal.fire({
                            title: data.is_complete ? 'Fully distributed' : 'Saved',
                            text: data.message,
                            icon: data.is_complete ? 'success' : 'info',
                            timer: 1400,
                            showConfirmButton: false,
                        }).then(() => window.location.reload());
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Could not save',
                            text: error.response?.data?.message || 'Try again in a moment.',
                            icon: 'error',
                            confirmButtonColor: '#dc2626',
                        });
                    })
                    .finally(() => {
                        saveBtn.disabled = false;
                        saveBtn.textContent = 'Save distribution';
                    });
            });

            refresh();
        }
    });
</script>
@endpush
@endsection
