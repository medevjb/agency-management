@extends('layouts.dashboard')

@section('title', 'Value Distribution')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
<style>
    /* Premium Value Distribution Layout */
    .vd-container {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .vd-mono {
        font-family: 'JetBrains Mono', ui-monospace, 'SFMono-Regular', Menlo, monospace;
        font-variant-numeric: tabular-nums;
        font-feature-settings: 'tnum';
    }

    .vd-panel {
        background-color: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 1.25rem;
        box-shadow: 0 4px 20px -2px rgba(16, 24, 40, 0.02), 0 2px 8px -1px rgba(16, 24, 40, 0.02);
        transition: all 0.2s ease-in-out;
    }

    .dark .vd-panel {
        background-color: #1e293b;
        border-color: #334155;
        box-shadow: none;
    }

    .vd-eyebrow {
        font-size: 0.6875rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
    }

    .dark .vd-eyebrow { color: #94a3b8; }

    .vd-empty {
        border: 1px dashed #cbd5e1;
        border-radius: 1rem;
        padding: 3rem 1.5rem;
        text-align: center;
        background-color: #f8fafc;
    }
    .dark .vd-empty { border-color: #475569; background-color: #0f172a; }

    /* ==================================================================
       Balance strip — Reconciled visual hero
       ================================================================== */
    .vd-hero {
        position: relative;
        overflow: hidden;
        border-radius: 1.25rem;
        padding: 2.25rem clamp(1.25rem, 5vw, 2.5rem);
        background: linear-gradient(135deg, #431004 0%, #7A250D 60%, #431004 100%);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        color: #f8fafc;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .vd-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: repeating-linear-gradient(
            0deg, rgba(255, 255, 255, 0.02) 0px, rgba(255, 255, 255, 0.02) 1px,
            transparent 1px, transparent 2.5rem
        );
        pointer-events: none;
    }

    .vd-hero-body { position: relative; display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 1.5rem; }
    .vd-hero-eyebrow { font-size: 0.6875rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: #FFA26E; }

    .vd-hero-figure {
        margin-top: 0.5rem;
        font-weight: 800;
        font-size: clamp(2.5rem, 6vw, 3.75rem);
        line-height: 1;
        letter-spacing: -0.03em;
    }

    .vd-hero-figure.is-open { color: #fbbf24; text-shadow: 0 0 20px rgba(251,191,36,0.25); }
    .vd-hero-figure.is-settled { color: #10b981; text-shadow: 0 0 20px rgba(16,185,129,0.25); }
    .vd-hero-figure.is-neutral { color: #f1f5f9; }

    .vd-hero-sub { margin-top: 0.625rem; font-size: 0.9375rem; color: #cbd5e1; max-width: 34rem; line-height: 1.5; }
    .vd-hero-sub strong { color: #ffffff; font-weight: 700; }

    .vd-hero-facts { position: relative; z-index: 1; display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem; text-align: right; flex-shrink: 0; }
    .vd-hero-fact-value { font-size: 1.5rem; font-weight: 800; color: #ffffff; }
    .vd-hero-fact-label { font-size: 0.75rem; color: #FFA26E; font-weight: 500; }

    .vd-hero-meter { position: relative; margin-top: 1.75rem; height: 0.625rem; border-radius: 9999px; background-color: rgba(255, 255, 255, 0.08); overflow: hidden; display: flex; }
    .vd-hero-meter-settled { display: block; height: 100%; background-image: linear-gradient(90deg, #059669, #10b981); transition: width 0.5s ease; }
    .vd-hero-meter-open { display: block; height: 100%; background-image: linear-gradient(90deg, #b45309, #fbbf24); transition: width 0.5s ease; }

    .vd-hero-legend { position: relative; margin-top: 1rem; display: flex; flex-wrap: wrap; gap: 1.5rem; font-size: 0.75rem; color: #FFA26E; }
    .vd-hero-legend .vd-dot { display: inline-block; width: 0.5rem; height: 0.5rem; border-radius: 9999px; margin-right: 0.4375rem; }
    .vd-hero-legend .vd-dot.is-settled { background-color: #10b981; }
    .vd-hero-legend .vd-dot.is-open { background-color: #fbbf24; }

    .vd-hero-done { position: relative; display: flex; align-items: center; gap: 1.25rem; }
    .vd-hero-done-badge {
        flex-shrink: 0;
        width: 3.75rem; height: 3.75rem;
        border-radius: 9999px;
        background-color: rgba(16, 185, 129, 0.15);
        border: 2px solid #10b981;
        display: flex; align-items: center; justify-content: center;
        color: #10b981; font-size: 1.5rem;
        box-shadow: 0 0 20px rgba(16,185,129,0.2);
    }

    /* ---- Leaderboard rail ---- */
    .vd-board-row {
        display: grid;
        grid-template-columns: 1.75rem minmax(0, 1fr) auto;
        align-items: center;
        gap: 0.875rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .vd-board-row:last-child { border-bottom: 0; }
    .dark .vd-board-row { border-bottom-color: #334155; }

    .vd-board-rank {
        display: flex; align-items: center; justify-content: center;
        width: 1.75rem; height: 1.75rem;
        border-radius: 9999px;
        font-size: 0.75rem; font-weight: 800;
        background-color: #f1f5f9; color: #64748b;
    }

    .dark .vd-board-rank { background-color: #334155; color: #cbd5e1; }
    .vd-board-rank.rank-1 { background-image: linear-gradient(135deg, #f59e0b, #fbbf24); color: #fff; }
    .vd-board-rank.rank-2 { background-image: linear-gradient(135deg, #94a3b8, #cbd5e1); color: #fff; }
    .vd-board-rank.rank-3 { background-image: linear-gradient(135deg, #b45309, #d97706); color: #fff; }

    .vd-board-name { font-size: 0.875rem; font-weight: 700; color: #0f172a; }
    .dark .vd-board-name { color: #f8fafc; }

    .vd-progress { height: 0.375rem; margin-top: 0.375rem; border-radius: 9999px; background-color: #f1f5f9; overflow: hidden; }
    .dark .vd-progress { background-color: #334155; }
    .vd-progress span { display: block; height: 100%; border-radius: 9999px; }
    .vd-progress span.is-met { background-image: linear-gradient(90deg, #059669, #10b981); }
    .vd-progress span.is-close { background-image: linear-gradient(90deg, #d97706, #f59e0b); }
    .vd-progress span.is-behind { background-image: linear-gradient(90deg, #dc2626, #f87171); }
    .vd-progress span.is-none { background-color: #cbd5e1; }

    .vd-board-meta { margin-top: 0.375rem; font-size: 0.6875rem; color: #64748b; }
    .dark .vd-board-meta { color: #94a3b8; }
    .vd-board-amount { font-size: 0.9375rem; font-weight: 800; color: #0f172a; }
    .dark .vd-board-amount { color: #f8fafc; }

    .vd-workspace {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        grid-template-areas: "pending" "board";
        gap: 1.5rem;
        align-items: start;
    }

    @media (min-width: 1024px) {
        .vd-workspace {
            grid-template-columns: minmax(0, 1fr) 22rem;
            grid-template-areas: "pending board";
        }
        .vd-workspace-board { position: sticky; top: 1.5rem; }
    }

    .vd-workspace-pending { grid-area: pending; min-width: 0; }
    .vd-workspace-board { grid-area: board; }

    /* ---- Search ---- */
    .vd-search-form { position: relative; }
    .vd-search-input {
        width: 100%;
        padding: 0.625rem 0.875rem 0.625rem 2.25rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.75rem;
        font-size: 0.8125rem;
        background-color: #ffffff;
        color: #0f172a;
        transition: all 0.15s ease;
    }

    .dark .vd-search-input { background-color: #1e293b; border-color: #475569; color: #f8fafc; }
    .vd-search-input:focus { outline: none; border-color: #FF5A1F; box-shadow: 0 0 0 3px rgba(255, 90, 31, 0.1); }
    
    .vd-filter-select {
        width: 100%;
        padding: 0.625rem 2rem 0.625rem 0.875rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.75rem;
        font-size: 0.8125rem;
        background-color: #ffffff;
        color: #0f172a;
        transition: all 0.15s ease;
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.625rem center;
        background-repeat: no-repeat;
        background-size: 1.25em 1.25em;
    }
    .dark .vd-filter-select {
        background-color: #1e293b;
        border-color: #475569;
        color: #f8fafc;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    }
    .vd-filter-select:focus {
        outline: none;
        border-color: #FF5A1F;
        box-shadow: 0 0 0 3px rgba(255, 90, 31, 0.1);
    }
    
    .vd-search-icon { position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 0.75rem; }
    .vd-search-clear { position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); color: #64748b; font-size: 0.75rem; }
    .vd-search-clear:hover { color: #334155; }

    /* ---- Settled disclosure ---- */
    .vd-settled-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 1rem 1.25rem;
        text-align: left;
    }

    .vd-settled-toggle-label { font-size: 0.6875rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: #64748b; }
    .dark .vd-settled-toggle-label { color: #94a3b8; }
    .vd-settled-toggle-action { display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 700; color: #F0430A; }
    .dark .vd-settled-toggle-action { color: #FF7A3D; }
    .vd-settled-toggle-action i { transition: transform 0.2s ease; font-size: 0.75rem; }
    .vd-settled-toggle[aria-expanded='true'] .vd-settled-toggle-action i { transform: rotate(180deg); }
    .vd-settled-body[hidden] { display: none; }
    .vd-settled-body { padding: 0 1.25rem 1.25rem; }

    /* ---- Pager ---- */
    .vd-pager { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; margin-top: 1rem; }
    .vd-pager-range { font-size: 0.75rem; color: #64748b; font-weight: 500; }
    .dark .vd-pager-range { color: #94a3b8; }
    .vd-pager-of { font-family: inherit; font-variant-numeric: normal; }
    .vd-pager-nav { display: flex; align-items: center; gap: 0.5rem; }
    .vd-pager-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 2rem; height: 2rem;
        border-radius: 0.5rem;
        border: 1px solid #cbd5e1;
        color: #334155;
        font-size: 0.75rem;
        background-color: #ffffff;
        transition: all 0.15s ease;
    }

    .vd-pager-btn:hover { background-color: #f1f5f9; border-color: #94a3b8; }
    .vd-pager-btn.is-disabled { opacity: 0.35; pointer-events: none; }
    .dark .vd-pager-btn { border-color: #475569; color: #cbd5e1; background-color: #1e293b; }
    .dark .vd-pager-btn:hover { background-color: #334155; border-color: #64748b; }
    .vd-pager-page { font-size: 0.75rem; font-weight: 700; color: #64748b; }
    .dark .vd-pager-page { color: #94a3b8; }

    /* ==================================================================
       Order cards — accordion split design
       ================================================================== */
    .dist-card {
        border: 1px solid #f1f5f9;
        border-radius: 1.25rem;
        overflow: hidden;
        background-color: #ffffff;
        box-shadow: 0 4px 20px -2px rgba(16, 24, 40, 0.01), 0 2px 8px -1px rgba(16, 24, 40, 0.01);
        transition: all 0.2s ease;
    }
    .dark .dist-card { border-color: #334155; background-color: #1e293b; box-shadow: none; }
    .dist-card.is-pending { border-left: 4px solid #f59e0b; }
    .dist-card.is-complete { border-left: 4px solid #10b981; }
    
    .dist-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 25px -5px rgba(16, 24, 40, 0.03);
    }

    .dist-summary { display: grid; grid-template-columns: 0.75rem minmax(0, 1fr) auto 1rem; align-items: center; gap: 1rem; width: 100%; padding: 1.125rem 1.25rem; text-align: left; transition: background 0.15s ease; }
    .dist-summary:hover { background-color: #f8fafc; }
    .dark .dist-summary:hover { background-color: rgba(51, 65, 85, 0.3); }

    .dist-status-dot { width: 0.5rem; height: 0.5rem; border-radius: 9999px; }
    .dist-status-dot.is-pending { background-color: #f59e0b; box-shadow: 0 0 8px #f59e0b; }
    .dist-status-dot.is-complete { background-color: #10b981; box-shadow: 0 0 8px #10b981; }

    .dist-summary-main { min-width: 0; }
    .dist-order-name { display: block; font-size: 0.9375rem; font-weight: 700; color: #0f172a; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .dark .dist-order-name { color: #f8fafc; }
    .dist-order-meta { display: block; margin-top: 0.25rem; font-size: 0.75rem; color: #64748b; }
    .dark .dist-order-meta { color: #94a3b8; }

    .dist-summary-figures { display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem; }
    .dist-net-amount { font-size: 0.9375rem; font-weight: 800; color: #0f172a; }
    .dark .dist-net-amount { color: #f8fafc; }

    .dist-badge { padding: 0.125rem 0.625rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; white-space: nowrap; }
    .dist-badge.is-pending { background-color: #fef3c7; color: #b45309; }
    .dist-badge.is-complete { background-color: #d1fae5; color: #047857; }
    .dark .dist-badge.is-pending { background-color: rgba(245, 158, 11, 0.15); color: #fcd34d; }
    .dark .dist-badge.is-complete { background-color: rgba(16, 185, 129, 0.15); color: #6ee7b7; }

    .dist-chevron { color: #94a3b8; transition: transform 0.2s ease; }
    .dist-summary[aria-expanded='true'] .dist-chevron { transform: rotate(180deg); }

    .dist-body { padding: 0 1.25rem 1.25rem; border-top: 1px solid #f1f5f9; }
    .dark .dist-body { border-top-color: #334155; }

    .dist-fee-line {
        margin: 1rem 0;
        font-size: 0.75rem;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background-color: #f8fafc;
        padding: 0.625rem 0.875rem;
        border-radius: 0.5rem;
        border: 1px solid #f1f5f9;
    }
    .dark .dist-fee-line {
        background-color: rgba(51, 65, 85, 0.2);
        border-color: rgba(255, 255, 255, 0.05);
        color: #cbd5e1;
    }

    .dist-rows { display: flex; flex-direction: column; gap: 0.625rem; }
    .dist-row { display: grid; grid-template-columns: minmax(0, 1fr) 9rem 2.25rem; align-items: center; gap: 0.75rem; }

    .dist-person { font-size: 0.875rem; font-weight: 600; color: #0f172a; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .dark .dist-person { color: #f8fafc; }

    .dist-tag { margin-left: 0.375rem; padding: 0.0625rem 0.5rem; border-radius: 9999px; font-size: 0.5625rem; font-weight: 800; text-transform: uppercase; background-color: #FFF3EC; color: #C63508; }
    .dark .dist-tag { background-color: rgba(255, 90, 31, 0.15); color: #FFA26E; }

    .dist-amount-wrap { position: relative; }
    .dist-amount {
        width: 100%;
        padding: 0.5rem 0.75rem 0.5rem 1.5rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        font-size: 0.8125rem;
        background-color: #ffffff;
        color: #0f172a;
        transition: border-color 0.15s ease;
    }
    .dark .dist-amount { background-color: #1e293b; border-color: #475569; color: #f8fafc; }
    .dist-amount:focus { outline: none; border-color: #FF5A1F; }
    .dist-currency { position: absolute; left: 0.625rem; top: 50%; transform: translateY(-50%); font-size: 0.8125rem; color: #94a3b8; font-family: 'JetBrains Mono', ui-monospace, 'SFMono-Regular', Menlo, monospace; }

    .dist-remove { display: flex; align-items: center; justify-content: center; height: 2.125rem; border-radius: 0.5rem; color: #94a3b8; transition: all 0.15s ease; }
    .dist-remove:hover { background-color: #fee2e2; color: #b91c1c; }
    .dark .dist-remove:hover { background-color: rgba(239, 68, 68, 0.15); color: #fca5a5; }

    .dist-empty-hint { font-size: 0.8125rem; color: #94a3b8; padding: 0.5rem 0; }

    .dist-controls { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; margin-top: 1rem; }
    .dist-search-wrap { position: relative; flex: 1; min-width: 12rem; }
    .dist-search {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #cbd5e1;
        border-radius: 0.5rem;
        font-size: 0.8125rem;
        background-color: #ffffff;
        color: #0f172a;
        transition: border-color 0.15s ease;
    }
    .dark .dist-search { background-color: #1e293b; border-color: #475569; color: #f8fafc; }
    .dist-search:focus { outline: none; border-color: #FF5A1F; }

    .dist-search-results { position: absolute; z-index: 20; top: calc(100% + 0.25rem); left: 0; right: 0; max-height: 12rem; overflow-y: auto; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 0.5rem; box-shadow: 0 10px 25px -5px rgba(16, 24, 40, 0.08); }
    .dark .dist-search-results { background-color: #1e293b; border-color: #475569; }
    .dist-search-result { display: block; width: 100%; padding: 0.5rem 0.75rem; text-align: left; font-size: 0.8125rem; color: #0f172a; transition: background 0.15s ease; }
    .dark .dist-search-result { color: #f8fafc; }
    .dist-search-result:hover { background-color: #f1f5f9; }
    .dark .dist-search-result:hover { background-color: #0f172a; }

    .dist-balance-btn { font-size: 0.75rem; font-weight: 700; color: #F0430A; white-space: nowrap; transition: color 0.15s ease; }
    .dist-balance-btn:hover { text-decoration: underline; color: #C63508; }
    .dark .dist-balance-btn { color: #FF7A3D; }
    .dark .dist-balance-btn:hover { color: #FFA26E; }

    .dist-footer { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px dashed #e2e8f0; }
    .dark .dist-footer { border-top-color: #334155; }

    .dist-remaining { font-family: 'JetBrains Mono', ui-monospace, 'SFMono-Regular', Menlo, monospace; font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.75rem; border-radius: 9999px; }
    .dist-remaining.is-balanced { background-color: #d1fae5; color: #047857; }
    .dist-remaining.is-left { background-color: #fef3c7; color: #b45309; }
    .dist-remaining.is-over { background-color: #fee2e2; color: #b91c1c; }
    .dark .dist-remaining.is-balanced { background-color: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
    .dark .dist-remaining.is-left { background-color: rgba(245, 158, 11, 0.15); color: #fcd34d; }
    .dark .dist-remaining.is-over { background-color: rgba(239, 68, 68, 0.15); color: #fca5a5; }

    .dist-footer-actions { display: flex; align-items: center; gap: 1rem; }
    .dist-view-link { font-size: 0.75rem; font-weight: 600; color: #64748b; transition: color 0.15s ease; }
    .dist-view-link:hover { text-decoration: underline; color: #334155; }
    .dark .dist-view-link { color: #94a3b8; }
    .dark .dist-view-link:hover { color: #cbd5e1; }

    .dist-save-btn {
        padding: 0.5rem 1.125rem;
        border-radius: 0.5rem;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #ffffff;
        background-image: linear-gradient(135deg, #F0430A, #D67D06);
        box-shadow: 0 2px 4px rgba(255, 90, 31, 0.1);
        transition: all 0.15s ease;
    }
    .dist-save-btn:hover { filter: brightness(1.08); box-shadow: 0 4px 12px rgba(255, 90, 31, 0.2); }
    .dist-save-btn[disabled] { opacity: 0.6; cursor: progress; }
</style>
@endpush

@section('content')
@php
    $isGlobalOps = auth()->user()->hasRole(['admin', 'operation-manager', 'project-manager']);
@endphp
<div class="vd-container px-4 sm:px-6 lg:px-8 py-6 max-w-6xl mx-auto space-y-6">

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
    @include('value-distribution.partials.hero', [
        'openTotal' => $snapshot['open_total'],
        'distributedTotal' => $snapshot['distributed_total'],
        'pendingCount' => $snapshot['pending_count'],
        'distributedCount' => $snapshot['distributed_count'],
        'monthDate' => $monthDate,
    ])

    <div class="vd-workspace">
        <!-- Pending + settled -->
        <div class="vd-workspace-pending space-y-6">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                    <p class="vd-eyebrow">Pending distribution (<span id="vdPendingCount">{{ $pendingOrders->total() }}</span>)</p>
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <div class="vd-search-form flex-1 sm:w-56">
                            <i class="fas fa-search vd-search-icon"></i>
                            <input type="text" id="vdSearch" value="{{ $filters['search'] }}"
                                   placeholder="Search by order name…" class="vd-search-input" autocomplete="off">
                            <button type="button" id="vdSearchClear" class="vd-search-clear {{ $filters['search'] !== '' ? '' : 'hidden' }}" aria-label="Clear search">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>
                        @if($isGlobalOps)
                            <select id="vdTeamFilter" class="vd-filter-select flex-1 sm:w-48" aria-label="Filter by team">
                                <option value="">All teams</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ $filters['team_id'] == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>

                <div id="vdPendingList">
                    @include('value-distribution.partials.pending-list', [
                        'orders' => $pendingOrders,
                        'filters' => $filters,
                        'hasAnyOrders' => $snapshot['has_any_orders'],
                        'monthDate' => $monthDate,
                    ])
                </div>

                <div id="vdPendingPager">
                    @include('value-distribution.partials.pager', ['paginator' => $pendingOrders, 'itemLabel' => 'pending order', 'section' => 'pending'])
                </div>
            </div>

            <!-- Settled — collapsed and paginated, so a month with hundreds of
                 closed-out orders never has to render them all at once. -->
            <div id="vdSettledPanelWrap">
                @include('value-distribution.partials.settled-panel', [
                    'distributedOrders' => $distributedOrders,
                    'distributedCount' => $distributedOrders->total(),
                    'openOnLoad' => request()->filled('distributed_page'),
                ])
            </div>
        </div>

        <!-- Achievement board -->
        <div class="vd-workspace-board">
            @include('value-distribution.partials.leaderboard', ['leaderboard' => $snapshot['leaderboard'], 'monthDate' => $monthDate])
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const LISTS_URL = @json(route('value-distribution.lists'));

        const state = {
            search: @json($filters['search']),
            team: @json($filters['team_id']),
            pendingPage: 1,
            distributedPage: 1,
            settledOpen: document.getElementById('vdSettledBody')
                ? ! document.getElementById('vdSettledBody').hidden
                : false,
        };

        let searchTimeout = null;

        // ---- Fragment refresh: search, team filter, pagination and saves all
        //      round-trip through here and swap the lists in place. ----
        async function refreshLists() {
            const params = {
                search: state.search,
                team: state.team || '',
                pending_page: state.pendingPage,
                distributed_page: state.distributedPage,
                settled_open: state.settledOpen ? 1 : 0,
            };

            try {
                const { data } = await axios.get(LISTS_URL, { params });

                document.getElementById('vdPendingList').innerHTML = data.pending_html;
                document.getElementById('vdPendingPager').innerHTML = data.pending_pager_html;
                document.getElementById('vdPendingCount').textContent = data.pending_count;

                const settledPanel = document.getElementById('vdSettledPanel');
                const newlyAppeared = ! settledPanel && data.distributed_count > 0;
                document.getElementById('vdSettledPanelWrap').outerHTML = data.settled_panel_html;
                if (newlyAppeared) {
                    state.settledOpen = true;
                }

                const hero = document.getElementById('vdHero');
                if (hero) {
                    hero.outerHTML = data.hero_html;
                }

                const board = document.getElementById('vdLeaderboard');
                if (board) {
                    board.outerHTML = data.leaderboard_html;
                }

                bindAll();
            } catch (error) {
                console.error('Could not refresh distribution lists.', error);
            }
        }

        function resetPages() {
            state.pendingPage = 1;
            state.distributedPage = 1;
        }

        function applyFilters() {
            resetPages();
            refreshLists();
        }

        // ---- Settled disclosure ----
        function bindSettledToggle() {
            const toggle = document.querySelector('[data-settled-toggle]');
            const body = document.getElementById('vdSettledBody');
            if (! toggle || ! body) {
                return;
            }

            toggle.addEventListener('click', function() {
                const willOpen = body.hidden;
                body.hidden = ! willOpen;
                state.settledOpen = willOpen;
                this.setAttribute('aria-expanded', String(willOpen));
                this.querySelector('[data-label]').textContent = willOpen ? 'Hide' : 'Show';
                this.querySelector('i').className = willOpen ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
            });
        }

        // ---- Per-card distribution logic, scoped so many cards coexist ----
        function initCard(card) {
            const netValue = parseFloat(card.dataset.netValue) || 0;
            const rows = card.querySelector('[data-dist-rows]');
            const remaining = card.querySelector('[data-dist-remaining]');
            const search = card.querySelector('[data-dist-search]');
            const results = card.querySelector('[data-dist-results]');
            const saveBtn = card.querySelector('[data-dist-save]');
            let searchTimeout = null;

            card.querySelector('[data-dist-toggle]')?.addEventListener('click', function() {
                const body = card.querySelector('[data-dist-body]');
                const willOpen = body.hidden;
                body.hidden = ! willOpen;
                this.setAttribute('aria-expanded', String(willOpen));
            });

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
                if (! first) {
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
                    if (! button) {
                        return;
                    }

                    addRow(button.dataset.userId, button.dataset.userName, null);
                    search.value = '';
                    results.hidden = true;
                });

                document.addEventListener('click', function(event) {
                    if (! card.contains(event.target)) {
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
                        });
                        resetPages();
                        refreshLists();
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

        function bindAll() {
            bindSettledToggle();
            document.querySelectorAll('[data-distribution-card]').forEach(initCard);
        }

        // ---- Filter controls (live, debounced) ----
        const searchInput = document.getElementById('vdSearch');
        const searchClear = document.getElementById('vdSearchClear');
        const teamFilter = document.getElementById('vdTeamFilter');

        searchInput?.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                state.search = searchInput.value.trim();
                searchClear.classList.toggle('hidden', state.search === '');
                applyFilters();
            }, 300);
        });

        searchClear?.addEventListener('click', function() {
            searchInput.value = '';
            state.search = '';
            this.classList.add('hidden');
            applyFilters();
        });

        teamFilter?.addEventListener('change', function() {
            state.team = this.value ? parseInt(this.value, 10) : null;
            applyFilters();
        });

        // ---- Pager clicks + clear-filters buttons (delegated, since the
        //      lists swap in fresh markup) ----
        document.addEventListener('click', function(event) {
            const pagerLink = event.target.closest('[data-pager-page]');
            if (pagerLink) {
                event.preventDefault();
                const page = parseInt(pagerLink.dataset.pagerPage, 10);
                if (pagerLink.dataset.pagerSection === 'distributed') {
                    state.distributedPage = page;
                } else {
                    state.pendingPage = page;
                }
                refreshLists();
                return;
            }

            const clearBtn = event.target.closest('[data-clear-filters]');
            if (clearBtn) {
                searchInput.value = '';
                state.search = '';
                searchClear.classList.add('hidden');
                if (teamFilter) {
                    teamFilter.value = '';
                    state.team = null;
                }
                applyFilters();
            }
        });

        bindAll();
    });
</script>
@endpush
@endsection
