@extends('layouts.dashboard')

@section('title', 'Sales Performance')

@push('styles')
<style>
    /* ------------------------------------------------------------------
       Containment encodes importance. The earnings hero sits on a washed
       surface with no border; supporting sections are ordinary panels;
       company context is plain text. Chrome increases as importance falls.
       ------------------------------------------------------------------ */

    .s-panel {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
    }

    .dark .s-panel {
        background-color: #111827;
        border-color: #1f2937;
        box-shadow: none;
    }

    .s-hero {
        border-radius: 1.25rem;
        padding: 1.5rem;
        background-image: linear-gradient(180deg, #ffffff 0%, #ecfdf5 100%);
    }

    .dark .s-hero {
        background-image: linear-gradient(180deg, #111827 0%, rgba(6, 78, 59, 0.55) 100%);
    }

    @media (min-width: 640px) {
        .s-hero { padding: 2rem; }
    }

    .s-eyebrow {
        font-size: 0.6875rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #6b7280;
    }

    .dark .s-eyebrow {
        color: #9ca3af;
    }

    .s-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: 'tnum';
    }

    .s-stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #6b7280;
    }

    .dark .s-stat-label {
        color: #9ca3af;
    }

    /* The one figure the page exists to deliver. */
    .s-bonus-num {
        font-size: clamp(2.75rem, 6vw, 4rem);
        line-height: 1;
        font-weight: 800;
        letter-spacing: -0.03em;
        color: #059669;
        font-variant-numeric: tabular-nums;
    }

    .dark .s-bonus-num { color: #34d399; }

    .s-split {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 1.25rem;
        align-items: start;
    }

    @media (min-width: 1280px) {
        .s-split { grid-template-columns: minmax(0, 2fr) minmax(20rem, 1fr); }
    }

    .s-share {
        margin-top: 0.375rem;
        height: 0.3125rem;
        border-radius: 9999px;
        background-color: #eef2f7;
        overflow: hidden;
    }

    .dark .s-share { background-color: #1f2937; }

    .s-share span {
        display: block;
        height: 100%;
        border-radius: 9999px;
        background-image: linear-gradient(90deg, #F0430A 0%, #F59A0F 100%);
    }

    .s-share span.is-emerald { background-image: linear-gradient(90deg, #059669 0%, #10b981 100%); }
    .s-share span.is-amber { background-image: linear-gradient(90deg, #d97706 0%, #f59e0b 100%); }

    .s-primary-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: #ffffff;
        background-image: linear-gradient(90deg, #F0430A 0%, #D67D06 100%);
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.1);
        transition: filter 0.15s ease;
    }

    .s-primary-btn:hover {
        filter: brightness(1.08);
    }

    .s-secondary-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        transition: background-color 0.15s ease;
    }

    .s-secondary-btn:hover {
        background-color: #f9fafb;
    }

    .dark .s-secondary-btn {
        border-color: #1f2937;
        color: #d1d5db;
    }

    .dark .s-secondary-btn:hover {
        background-color: #1f2937;
    }

    /* ------------------------------------------------------------------
       Status. Never colour alone — every badge carries an icon and text.
       Three accents only: emerald = money earned, indigo = the goal,
       amber = accruing / in progress. Gray carries context.
       ------------------------------------------------------------------ */

    .s-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .s-badge.is-emerald { background-color: #d1fae5; color: #047857; }
    .s-badge.is-amber { background-color: #fef3c7; color: #b45309; }
    .s-badge.is-indigo { background-color: #FFF3EC; color: #C63508; }
    .s-badge.is-gray { background-color: #f3f4f6; color: #6b7280; }

    .dark .s-badge.is-emerald { background-color: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
    .dark .s-badge.is-amber { background-color: rgba(245, 158, 11, 0.15); color: #fcd34d; }
    .dark .s-badge.is-indigo { background-color: rgba(255, 90, 31, 0.15); color: #FFA26E; }
    .dark .s-badge.is-gray { background-color: #1f2937; color: #9ca3af; }

    /* Target / bonus zone track */
    .s-zone {
        position: relative;
        height: 1.125rem;
        border-radius: 9999px;
        background-color: #eef2f7;
        overflow: hidden;
    }

    .dark .s-zone { background-color: #1f2937; }

    .s-zone-seg {
        position: absolute;
        top: 0;
        bottom: 0;
    }

    .s-zone-seg.is-target { background-image: linear-gradient(90deg, #F0430A 0%, #F59A0F 100%); }
    .s-zone-seg.is-bonus { background-image: linear-gradient(90deg, #059669 0%, #10b981 100%); }

    .s-zone-mark {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #ffffff;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.06);
    }

    .s-zone-labels {
        display: flex;
        justify-content: space-between;
        gap: 0.5rem;
        margin-top: 0.5rem;
        font-size: 0.6875rem;
        font-weight: 700;
        color: #9ca3af;
    }

    .dark .s-zone-labels { color: #6b7280; }

    .s-zone-labels strong { color: #111827; }
    .dark .s-zone-labels strong { color: #e5e7eb; }

    .s-zone-key {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.6875rem;
        font-weight: 700;
        color: #6b7280;
    }

    .dark .s-zone-key { color: #9ca3af; }

    .s-zone-key i {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 9999px;
        display: inline-block;
    }

    .s-zone-key i.is-target { background-color: #FF5A1F; }
    .s-zone-key i.is-bonus { background-color: #10b981; }

    /* Bonus derivation, shown as reading material rather than debug output. */
    .s-formula {
        border-radius: 0.875rem;
        background-color: rgba(255, 255, 255, 0.72);
        border: 1px solid #d1fae5;
        padding: 0.875rem 1rem;
    }

    .dark .s-formula {
        background-color: rgba(17, 24, 39, 0.55);
        border-color: rgba(16, 185, 129, 0.2);
    }

    .s-formula-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 1rem;
        font-size: 0.875rem;
    }

    .s-formula-row + .s-formula-row {
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px dashed #d1fae5;
    }

    .dark .s-formula-row + .s-formula-row { border-top-color: rgba(16, 185, 129, 0.18); }

    /* Callout */
    .s-callout {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        border-radius: 0.875rem;
        padding: 0.875rem 1rem;
    }

    .s-callout.is-emerald { background-color: #ecfdf5; border: 1px solid #d1fae5; }
    .s-callout.is-amber { background-color: #fffbeb; border: 1px solid #fef3c7; }

    .dark .s-callout.is-emerald { background-color: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.2); }
    .dark .s-callout.is-amber { background-color: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2); }

    /* ------------------------------------------------------------------
       Earnings history. Monthly bars carry trajectory; the quarter each
       group sits under carries what was actually paid. The grouping is
       the point: qualification is monthly, payment is quarterly.
       ------------------------------------------------------------------ */

    .s-history {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    @media (min-width: 768px) {
        .s-history { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }

    .s-quarter {
        border-radius: 0.875rem;
        padding: 0.875rem;
        background-color: #f9fafb;
        border: 1px solid #f3f4f6;
    }

    .dark .s-quarter {
        background-color: rgba(31, 41, 55, 0.5);
        border-color: #1f2937;
    }

    .s-quarter.is-current {
        background-color: #ecfdf5;
        border-color: #a7f3d0;
    }

    .dark .s-quarter.is-current {
        background-color: rgba(16, 185, 129, 0.1);
        border-color: rgba(16, 185, 129, 0.25);
    }

    .s-spark {
        display: flex;
        align-items: flex-end;
        gap: 0.25rem;
        height: 2.75rem;
    }

    .s-spark-bar {
        flex: 1 1 0%;
        min-height: 0.25rem;
        border-radius: 0.1875rem 0.1875rem 0 0;
        background-color: #FFC7A6;
    }

    .dark .s-spark-bar { background-color: #9C2B0B; }

    /* A month that cleared its target is money, so it takes the money colour. */
    .s-spark-bar.is-qualified { background-color: #10b981; }
    .dark .s-spark-bar.is-qualified { background-color: #34d399; }

    .s-spark-months {
        display: flex;
        gap: 0.25rem;
        margin-top: 0.25rem;
    }

    .s-spark-months span {
        flex: 1 1 0%;
        text-align: center;
        font-size: 0.5625rem;
        font-weight: 700;
        color: #9ca3af;
    }

    .dark .s-spark-months span { color: #6b7280; }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $isPreview = $isPreview ?? false;
    $firstName = $isPreview ? 'Sales Member' : explode(' ', $user->name)[0];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-7xl mx-auto space-y-5">

    @if($isPreview)
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white text-sm font-semibold">
            <span><i class="fas fa-eye mr-2"></i>Preview — sample data. This is what a sales member's own dashboard looks like; no real account is shown.</span>
            <a href="{{ route('sales.leadership') }}" class="underline hover:no-underline">Back to Leadership Overview</a>
        </div>
    @endif

    @include('sales.partials.header', compact('firstName', 'user', 'isPreview', 'dashboard'))

    @include('sales.partials.bonus-hero', ['dashboard' => $dashboard])

    @include('sales.partials.month-progress', ['dashboard' => $dashboard])

    @include('sales.partials.quarter-performance', ['dashboard' => $dashboard])

    @include('sales.partials.earnings-history', ['dashboard' => $dashboard])

    @include('sales.partials.company-summary', ['dashboard' => $dashboard])

</div>
@endsection
