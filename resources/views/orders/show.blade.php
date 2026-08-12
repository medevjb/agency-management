@extends('layouts.dashboard')

@section('title', 'Order Details')

@push('styles')
<style>
    .panel {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
    }

    .dark .panel {
        background-color: #111827;
        border-color: #1f2937;
        box-shadow: none;
    }

    .eyebrow {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #6b7280;
    }

    .dark .eyebrow {
        color: #9ca3af;
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.3125rem 0.6875rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        line-height: 1;
    }

    /* ---- Delivery track ---- */
    .track {
        position: relative;
        height: 2.75rem;
        border-radius: 9999px;
        background-color: #eef2f7;
        overflow: hidden;
    }

    .dark .track {
        background-color: #1f2937;
    }

    .track-fill {
        position: absolute;
        inset: 0 auto 0 0;
        border-radius: 9999px;
        transition: width 900ms cubic-bezier(0.22, 1, 0.36, 1);
    }

    .track-label {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.01em;
        color: #374151;
    }

    .dark .track-label {
        color: #d1d5db;
    }

    /* Same label clipped to the filled portion, so it reads on both sides. */
    .track-label-clip {
        position: absolute;
        inset: 0 auto 0 0;
        overflow: hidden;
        transition: width 900ms cubic-bezier(0.22, 1, 0.36, 1);
    }

    .track-label-clip .track-label {
        color: #ffffff;
    }

    .track-today {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: rgba(17, 24, 39, 0.55);
        transform: translateX(-1px);
    }

    .dark .track-today {
        background-color: rgba(255, 255, 255, 0.65);
    }

    @media (prefers-reduced-motion: reduce) {
        .track-fill,
        .track-label-clip {
            transition: none;
        }
    }

    /* ---- Tabs ---- */
    .tabbar {
        position: sticky;
        top: 0;
        z-index: 20;
        background-color: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid #f1f5f9;
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
    }

    .dark .tabbar {
        background-color: rgba(17, 24, 39, 0.92);
        border-bottom-color: #1f2937;
    }

    .tab-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.9375rem 0.125rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
        border-bottom: 2px solid transparent;
        white-space: nowrap;
        transition: color 0.15s ease, border-color 0.15s ease;
    }

    .tab-button:hover {
        color: #4f46e5;
    }

    .tab-button.is-active {
        color: #4f46e5;
        border-bottom-color: #4f46e5;
    }

    .dark .tab-button {
        color: #9ca3af;
    }

    .dark .tab-button:hover,
    .dark .tab-button.is-active {
        color: #a5b4fc;
    }

    .dark .tab-button.is-active {
        border-bottom-color: #a5b4fc;
    }

    .tab-count {
        padding: 0.0625rem 0.375rem;
        border-radius: 9999px;
        font-size: 0.625rem;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        background-color: #f3f4f6;
        color: #6b7280;
    }

    .dark .tab-count {
        background-color: #1f2937;
        color: #9ca3af;
    }

    /* ---- Data rows ---- */
    .meta-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.625rem 0;
        border-bottom: 1px dashed #eef2f7;
    }

    .meta-row:last-child {
        border-bottom: 0;
    }

    .dark .meta-row {
        border-bottom-color: #1f2937;
    }

    .meta-key {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        white-space: nowrap;
    }

    .dark .meta-key {
        color: #9ca3af;
    }

    .meta-value {
        font-size: 0.875rem;
        font-weight: 600;
        color: #111827;
        text-align: right;
        word-break: break-word;
    }

    .dark .meta-value {
        color: #f3f4f6;
    }

    .empty {
        border: 1px dashed #e5e7eb;
        border-radius: 0.875rem;
        padding: 2rem 1.5rem;
        text-align: center;
    }

    .dark .empty {
        border-color: #1f2937;
    }

    /* ---- Rich text (credentials) ---- */
    .rich-text {
        font-size: 0.9375rem;
        line-height: 1.65;
        color: #1f2937;
        word-break: break-word;
    }

    .dark .rich-text {
        color: #e5e7eb;
    }

    .rich-text h2 { font-size: 1.0625rem; font-weight: 700; margin: 0.75rem 0 0.25rem; }
    .rich-text h3 { font-size: 0.9375rem; font-weight: 700; margin: 0.75rem 0 0.25rem; }
    .rich-text p { margin: 0.25rem 0; }
    .rich-text ul, .rich-text ol { margin: 0.375rem 0 0.375rem 1.25rem; }
    .rich-text ul { list-style: disc; }
    .rich-text ol { list-style: decimal; }
    .rich-text a { color: #4f46e5; text-decoration: underline; }

    .rich-text pre,
    .rich-text code {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 0.8125rem;
        background-color: #f3f4f6;
        border-radius: 0.375rem;
        padding: 0.125rem 0.375rem;
    }

    .dark .rich-text pre,
    .dark .rich-text code {
        background-color: #1f2937;
    }

    .rich-text pre { padding: 0.75rem; overflow-x: auto; }

    .rich-text blockquote {
        border-left: 3px solid #c7d2fe;
        padding-left: 0.75rem;
        color: #4b5563;
    }

    .dark .rich-text blockquote {
        border-left-color: #4338ca;
        color: #9ca3af;
    }

    .is-masked {
        filter: blur(6px);
        user-select: none;
        pointer-events: none;
    }

    /* ---- Change log ----
       A time axis: the moment sits in a fixed gutter on the left, the entry
       hangs off a hairline rail to its right. */
    .log-filters {
        display: inline-flex;
        padding: 0.1875rem;
        gap: 0.125rem;
        border-radius: 9999px;
        background-color: #f3f4f6;
    }

    .dark .log-filters {
        background-color: #1f2937;
    }

    .filter-chip {
        padding: 0.3125rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    .filter-chip:hover {
        color: #4f46e5;
    }

    .filter-chip.is-active {
        background-color: #ffffff;
        color: #4f46e5;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.08);
    }

    .dark .filter-chip {
        color: #9ca3af;
    }

    .dark .filter-chip:hover {
        color: #c7d2fe;
    }

    .dark .filter-chip.is-active {
        background-color: #374151;
        color: #c7d2fe;
        box-shadow: none;
    }

    .log-count {
        margin-top: 0.5rem;
        margin-bottom: 1.25rem;
        font-size: 0.75rem;
        color: #9ca3af;
        font-variant-numeric: tabular-nums;
    }

    .log-list {
        transition: opacity 0.15s ease;
    }

    .log-list[aria-busy='true'] {
        opacity: 0.45;
        pointer-events: none;
    }

    .log-day + .log-day {
        margin-top: 1.5rem;
    }

    .log-day-label {
        position: sticky;
        top: 3.5rem;
        z-index: 5;
        display: inline-block;
        margin-bottom: 0.75rem;
        padding: 0.1875rem 0.625rem;
        border-radius: 9999px;
        background-color: #f3f4f6;
        font-size: 0.6875rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #6b7280;
    }

    .dark .log-day-label {
        background-color: #1f2937;
        color: #9ca3af;
    }

    .log-entry {
        display: grid;
        grid-template-columns: 3.25rem minmax(0, 1fr);
        gap: 0.75rem;
    }

    .log-time {
        padding-top: 0.1875rem;
        text-align: right;
        font-size: 0.75rem;
        font-weight: 700;
        color: #9ca3af;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .log-meridiem {
        margin-left: 0.125rem;
        font-size: 0.5625rem;
        font-weight: 800;
        letter-spacing: 0.04em;
    }

    .log-body {
        position: relative;
        padding-left: 1.75rem;
        padding-bottom: 1.25rem;
        border-left: 1px solid #e5e7eb;
    }

    .dark .log-body {
        border-left-color: #1f2937;
    }

    .log-entry:last-child .log-body {
        border-left-color: transparent;
    }

    .log-dot {
        position: absolute;
        left: -0.8125rem;
        top: 0;
        width: 1.625rem;
        height: 1.625rem;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.625rem;
        border: 2px solid #ffffff;
    }

    .dark .log-dot {
        border-color: #111827;
    }

    .tone-create { background-color: #d1fae5; color: #047857; }
    .tone-edit { background-color: #e0e7ff; color: #4338ca; }
    .tone-delete { background-color: #fee2e2; color: #b91c1c; }
    .tone-restore { background-color: #fef3c7; color: #b45309; }
    .tone-people { background-color: #f3e8ff; color: #7e22ce; }
    .tone-files { background-color: #dbeafe; color: #1d4ed8; }

    .dark .tone-create { background-color: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
    .dark .tone-edit { background-color: rgba(99, 102, 241, 0.15); color: #a5b4fc; }
    .dark .tone-delete { background-color: rgba(239, 68, 68, 0.15); color: #fca5a5; }
    .dark .tone-restore { background-color: rgba(245, 158, 11, 0.15); color: #fcd34d; }
    .dark .tone-people { background-color: rgba(168, 85, 247, 0.15); color: #d8b4fe; }
    .dark .tone-files { background-color: rgba(59, 130, 246, 0.15); color: #93c5fd; }

    .log-headline {
        font-size: 0.875rem;
        line-height: 1.45;
        color: #4b5563;
    }

    .dark .log-headline {
        color: #9ca3af;
    }

    .log-actor {
        font-weight: 700;
        color: #111827;
    }

    .dark .log-actor {
        color: #f3f4f6;
    }

    .log-changes {
        margin-top: 0.625rem;
        border: 1px solid #f1f5f9;
        border-radius: 0.75rem;
        overflow: hidden;
    }

    .dark .log-changes {
        border-color: #1f2937;
    }

    .log-change {
        display: grid;
        grid-template-columns: 8.5rem minmax(0, 1fr) 0.75rem minmax(0, 1fr);
        align-items: baseline;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.8125rem;
    }

    .log-change + .log-change,
    .log-change-rest .log-change:first-child {
        border-top: 1px solid #f1f5f9;
    }

    .dark .log-change + .log-change,
    .dark .log-change-rest .log-change:first-child {
        border-top-color: #1f2937;
    }

    .log-change-field {
        font-size: 0.6875rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #9ca3af;
    }

    .log-change-old {
        text-decoration: line-through;
        color: #9ca3af;
        word-break: break-word;
    }

    .log-change-arrow {
        font-size: 0.5625rem;
        color: #d1d5db;
    }

    .dark .log-change-arrow {
        color: #4b5563;
    }

    .log-change-new {
        font-weight: 700;
        color: #047857;
        word-break: break-word;
    }

    .dark .log-change-new {
        color: #34d399;
    }

    @media (max-width: 640px) {
        .log-change {
            grid-template-columns: minmax(0, 1fr);
            gap: 0.125rem;
        }

        .log-change-arrow {
            display: none;
        }
    }

    .log-expand {
        display: block;
        width: 100%;
        padding: 0.4375rem 0.75rem;
        border-top: 1px solid #f1f5f9;
        background-color: #fafafa;
        font-size: 0.75rem;
        font-weight: 700;
        color: #4f46e5;
        text-align: left;
    }

    .log-expand:hover {
        background-color: #eef2ff;
    }

    .dark .log-expand {
        border-top-color: #1f2937;
        background-color: rgba(31, 41, 55, 0.5);
        color: #a5b4fc;
    }

    .dark .log-expand:hover {
        background-color: rgba(99, 102, 241, 0.12);
    }

    .log-ip {
        margin-top: 0.375rem;
        font-size: 0.6875rem;
        color: #cbd5e1;
        font-variant-numeric: tabular-nums;
    }

    .dark .log-ip {
        color: #4b5563;
    }

    .log-foot {
        margin-top: 0.5rem;
        text-align: center;
    }

    .log-load-more {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1.125rem;
        border-radius: 9999px;
        border: 1px solid #e5e7eb;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #4b5563;
        transition: border-color 0.15s ease, color 0.15s ease, background-color 0.15s ease;
    }

    .log-load-more:hover {
        border-color: #a5b4fc;
        background-color: #eef2ff;
        color: #4f46e5;
    }

    .log-load-more[disabled] {
        opacity: 0.6;
        cursor: progress;
    }

    .dark .log-load-more {
        border-color: #1f2937;
        color: #9ca3af;
    }

    .dark .log-load-more:hover {
        border-color: rgba(99, 102, 241, 0.4);
        background-color: rgba(99, 102, 241, 0.1);
        color: #c7d2fe;
    }

    .log-end {
        font-size: 0.75rem;
        color: #cbd5e1;
    }

    .dark .log-end {
        color: #4b5563;
    }

    .is-hidden {
        display: none;
    }

    /* ---- Page layout ----
       Written as page CSS rather than utilities so the layout does not depend
       on a Tailwind rebuild to be correct. */
    .detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 1.25rem;
        align-items: start;
    }

    @media (min-width: 1280px) {
        .detail-grid {
            grid-template-columns: minmax(0, 2fr) minmax(19rem, 1fr);
        }

        .detail-side {
            position: sticky;
            top: 1.25rem;
        }
    }

    .detail-main {
        min-width: 0;
    }

    .detail-side {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: 'tnum';
    }

    /* Money ledger: labels left, figures right, digits in one column. */
    .money-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: baseline;
        gap: 1rem;
        padding: 0.5rem 0;
        border-bottom: 1px dashed #eef2f7;
    }

    .dark .money-row {
        border-bottom-color: #1f2937;
    }

    .money-row dt {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
    }

    .dark .money-row dt {
        color: #9ca3af;
    }

    .money-row dd {
        font-size: 0.9375rem;
        font-weight: 700;
        text-align: right;
        color: #111827;
        font-variant-numeric: tabular-nums;
        font-feature-settings: 'tnum';
    }

    .dark .money-row dd {
        color: #f3f4f6;
    }

    .money-row dd.is-negative {
        color: #9ca3af;
        font-weight: 600;
    }

    .money-row.is-total {
        border-bottom: 0;
        padding-top: 0.75rem;
        margin-top: 0.25rem;
        border-top: 1px solid #e5e7eb;
    }

    .dark .money-row.is-total {
        border-top-color: #1f2937;
    }

    .money-row.is-total dt {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #111827;
    }

    .dark .money-row.is-total dt {
        color: #f3f4f6;
    }

    .money-row.is-total dd {
        font-size: 1.5rem;
        line-height: 1.2;
        letter-spacing: -0.02em;
        color: #059669;
    }

    .dark .money-row.is-total dd {
        color: #34d399;
    }

    .body-text {
        font-size: 0.9375rem;
        line-height: 1.65;
        white-space: pre-line;
    }

    .note-card {
        border: 1px solid #fde68a;
        border-left: 4px solid #f59e0b;
        border-radius: 0.75rem;
        background-color: #fffbeb;
        padding: 1rem;
    }

    .dark .note-card {
        background-color: rgba(245, 158, 11, 0.06);
        border-color: rgba(245, 158, 11, 0.2);
        border-left-color: #d97706;
    }

    .cred-card {
        border: 1px solid #d1fae5;
        border-radius: 0.75rem;
        background-color: #f0fdf9;
        padding: 1rem;
    }

    .dark .cred-card {
        background-color: rgba(16, 185, 129, 0.05);
        border-color: rgba(16, 185, 129, 0.2);
    }

    .lock-tag {
        text-transform: none;
        letter-spacing: normal;
    }

    .row-actions {
        opacity: 0.55;
        transition: opacity 0.15s ease;
    }

    .doc-row:hover .row-actions,
    .row-actions:focus-within {
        opacity: 1;
    }

    .status-row {
        background-color: #f9fafb;
    }

    .dark .status-row {
        background-color: rgba(31, 41, 55, 0.5);
    }

    .range {
        flex: 1;
        min-width: 12rem;
        height: 0.5rem;
        border-radius: 9999px;
        appearance: none;
        cursor: pointer;
        background-color: #e5e7eb;
        accent-color: #4f46e5;
    }

    .dark .range {
        background-color: #374151;
    }

    .btn-ghost {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid #c7d2fe;
        font-size: 0.75rem;
        font-weight: 600;
        color: #4f46e5;
        transition: background-color 0.15s ease;
    }

    .btn-ghost:hover {
        background-color: #eef2ff;
    }

    .dark .btn-ghost {
        border-color: rgba(99, 102, 241, 0.3);
        color: #a5b4fc;
    }

    .dark .btn-ghost:hover {
        background-color: rgba(99, 102, 241, 0.1);
    }

    .icon-amber { color: #f59e0b; }
    .icon-emerald { color: #10b981; }
    .icon-indigo { color: #6366f1; }
    .icon-purple { color: #a855f7; }
    .icon-blue { color: #3b82f6; }
    .icon-orange { color: #f97316; }

    /* Visible keyboard focus everywhere on this page. */
    .panel a:focus-visible,
    .panel button:focus-visible,
    .tab-button:focus-visible {
        outline: 2px solid #6366f1;
        outline-offset: 2px;
        border-radius: 0.5rem;
    }
</style>
@endpush

@section('content')
@php
    $viewer = auth()->user();
    $isSalesUser = $viewer->isSales();
    $showsSalesOwner = $isSalesUser || $viewer->hasRole('admin');
    $statusConfig = [
        'nra' => ['label' => 'NRA', 'chip' => 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300', 'rail' => '#9ca3af', 'dot' => 'bg-gray-400'],
        'pending' => ['label' => 'Pending', 'chip' => 'bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400', 'rail' => '#f59e0b', 'dot' => 'bg-amber-500'],
        'in-progress' => ['label' => 'In progress', 'chip' => 'bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400', 'rail' => '#3b82f6', 'dot' => 'bg-blue-500'],
        'delivered' => ['label' => 'Delivered', 'chip' => 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400', 'rail' => '#10b981', 'dot' => 'bg-emerald-500'],
        'completed' => ['label' => 'Completed', 'chip' => 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400', 'rail' => '#10b981', 'dot' => 'bg-emerald-500'],
        'on-hold' => ['label' => 'On hold', 'chip' => 'bg-orange-100 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400', 'rail' => '#f97316', 'dot' => 'bg-orange-500'],
        'cancelled' => ['label' => 'Cancelled', 'chip' => 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400', 'rail' => '#ef4444', 'dot' => 'bg-red-500'],
    ];
    $status = $statusConfig[$order->status] ?? $statusConfig['nra'];

    $priorityConfig = [
        'low' => ['label' => 'Low priority', 'chip' => 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'],
        'medium' => ['label' => 'Medium priority', 'chip' => 'bg-yellow-100 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400'],
        'high' => ['label' => 'High priority', 'chip' => 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400'],
    ];
    $priority = $priorityConfig[$order->priority] ?? $priorityConfig['medium'];

    $progress = max(0, min(100, (int) ($order->progress ?? 0)));
    $documents = $order->documents();
    $isFinished = in_array($order->status, ['delivered', 'completed', 'cancelled'], true);

    // Share of the scheduled window that has already gone by.
    $elapsed = null;
    if ($order->start_date && $order->end_date && $order->end_date->gt($order->start_date)) {
        $window = $order->start_date->diffInSeconds($order->end_date);
        $spent = $order->start_date->diffInSeconds(now(), false);
        $elapsed = max(0, min(100, (int) round($spent / $window * 100)));
    }

    $pace = ($elapsed !== null && ! $isFinished) ? $progress - $elapsed : null;
    $daysLeft = $order->end_date ? (int) round(now()->startOfDay()->diffInDays($order->end_date->copy()->startOfDay(), false)) : null;

    $fillGradient = $progress >= 100
        ? 'linear-gradient(90deg, #059669 0%, #10b981 100%)'
        : ($pace !== null && $pace <= -15
            ? 'linear-gradient(90deg, #ea580c 0%, #f59e0b 100%)'
            : 'linear-gradient(90deg, #4f46e5 0%, #8b5cf6 100%)');
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-7xl mx-auto space-y-5">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
        <a href="{{ route('orders.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Orders</a>
        <i class="fas fa-chevron-right text-[10px] opacity-50"></i>
        <span class="text-gray-700 dark:text-gray-300 font-medium truncate">{{ $order->name }}</span>
    </nav>

    <!-- Header -->
    <div class="panel overflow-hidden">
        <div style="height: 4px; background-color: {{ $status['rail'] }}"></div>
        <div class="p-5 sm:p-6">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="chip {{ $status['chip'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $status['dot'] }}"></span>
                            {{ $status['label'] }}
                        </span>
                        <span class="chip {{ $priority['chip'] }}">{{ $priority['label'] }}</span>
                        @if($order->special_status)
                            <span class="chip bg-purple-100 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400">
                                <i class="fas fa-bookmark text-[10px]"></i>{{ $order->special_status->getLabel() }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100 truncate">{{ $order->name }}</h1>

                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-mono text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                            {{ $order->order_id ?: 'no order ID' }}
                        </span>
                        @if($order->freelanceProfile)
                            <span><i class="fas fa-user-tie mr-1.5 opacity-60"></i>{{ $order->freelanceProfile->username }}</span>
                        @endif
                        {{-- The creator of a sales-opened order is the sales person, who stays invisible to operations. --}}
                        <span><i class="fas fa-calendar-plus mr-1.5 opacity-60"></i>Opened {{ $order->created_at->format('M d, Y') }}@if($order->creator && ($showsSalesOwner || ! $order->creator->isSales())) by {{ $order->creator->name }}@endif</span>
                        <span><i class="fas fa-rotate mr-1.5 opacity-60"></i>Updated {{ $order->updated_at->diffForHumans() }}</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                    @can('update', $order)
                        <a href="{{ route('orders.edit', $order) }}"
                           class="inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-sm transition-all">
                            <i class="fas fa-pen mr-2"></i>Edit order
                        </a>
                    @endcan
                    @can('delete', $order)
                        <button onclick="deleteOrder({{ $order->id }})"
                                class="inline-flex items-center px-3 py-2.5 rounded-lg text-sm font-semibold text-red-600 dark:text-red-400 border border-gray-200 dark:border-gray-800 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                            <i class="fas fa-trash"></i>
                            <span class="sr-only">Delete order</span>
                        </button>
                    @endcan
                    <a href="{{ route('orders.index') }}"
                       class="inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                        Back to orders
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Delivery track -->
    <div class="panel p-5 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <p class="eyebrow"><i class="fas fa-route icon-indigo"></i>Delivery</p>

            <div class="flex items-center gap-3">
                @if($order->delivered_at)
                    <span class="chip bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">
                        <i class="fas fa-check text-[10px]"></i>Delivered {{ $order->delivered_at->format('M d, Y') }}
                    </span>
                @elseif($daysLeft === null)
                    <span class="chip bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400">No deadline set</span>
                @elseif($daysLeft < 0)
                    <span class="chip bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400">
                        <i class="fas fa-triangle-exclamation text-[10px]"></i>{{ abs($daysLeft) }} {{ \Illuminate\Support\Str::plural('day', abs($daysLeft)) }} overdue
                    </span>
                @elseif($daysLeft === 0)
                    <span class="chip bg-orange-100 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400">Due today</span>
                @else
                    <span class="chip {{ $daysLeft <= 4 ? 'bg-orange-100 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400' }}">
                        {{ $daysLeft }} {{ \Illuminate\Support\Str::plural('day', $daysLeft) }} left
                    </span>
                @endif

                @can('manageOperations', $order)
                    <button type="button" id="progressEditToggle" onclick="toggleProgressEditor()"
                            class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                        <i class="fas fa-sliders mr-1"></i>Update progress
                    </button>
                @endcan
            </div>
        </div>

        <!-- Dates rail -->
        <div class="flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">
            <span>{{ $order->start_date ? $order->start_date->format('M d, Y') : 'No start date' }}</span>
            <span>{{ $order->end_date ? $order->end_date->format('M d, Y') : 'No deadline' }}</span>
        </div>

        <div class="track" id="progressTrack" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100" aria-label="Order progress">
            <div class="track-fill" id="progressFill" style="width: {{ $progress }}%; background-image: {{ $fillGradient }}"></div>
            <span class="track-label" id="progressLabelBase">{{ $progress }}%</span>
            <span class="track-label-clip" id="progressLabelClip" style="width: {{ $progress }}%">
                <span class="track-label" style="width: {{ $progress > 0 ? 10000 / $progress : 100 }}%">{{ $progress }}%</span>
            </span>
            @if($elapsed !== null && ! $isFinished)
                <span class="track-today" style="left: {{ $elapsed }}%" title="Today — {{ $elapsed }}% of the schedule has passed"></span>
            @endif
        </div>

        <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm font-medium" id="paceLine">
                @if($progress >= 100)
                    <span class="text-emerald-600 dark:text-emerald-400"><i class="fas fa-circle-check mr-1.5"></i>Work complete</span>
                @elseif($pace === null)
                    <span class="text-gray-500 dark:text-gray-400">{{ 100 - $progress }}% of the work still to go</span>
                @elseif($pace <= -15)
                    <span class="text-orange-600 dark:text-orange-400">
                        <i class="fas fa-gauge-high mr-1.5"></i>Behind the clock — {{ abs($pace) }} points less done than time spent
                    </span>
                @elseif($pace >= 15)
                    <span class="text-emerald-600 dark:text-emerald-400">
                        <i class="fas fa-gauge-high mr-1.5"></i>Ahead of the clock by {{ $pace }} points
                    </span>
                @else
                    <span class="text-gray-600 dark:text-gray-300"><i class="fas fa-gauge-high mr-1.5"></i>Tracking with the schedule</span>
                @endif
            </p>
            @if($elapsed !== null && ! $isFinished)
                <p class="text-xs text-gray-400 dark:text-gray-500">Marker shows today — {{ $elapsed }}% of the schedule has passed</p>
            @endif
        </div>

        @can('manageOperations', $order)
            <div id="progressEditor" class="hidden mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                <div class="flex flex-wrap items-center gap-4">
                    <input type="range" id="progressInput" min="0" max="100" step="5" value="{{ $progress }}"
                           class="range">
                    <span id="progressInputValue" class="w-14 text-center text-sm font-bold num text-gray-900 dark:text-gray-100">{{ $progress }}%</span>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="saveProgress({{ $order->id }})"
                                class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                            Save progress
                        </button>
                        <button type="button" onclick="toggleProgressEditor()"
                                class="px-3 py-2 rounded-lg text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    <div class="detail-grid">

        <!-- Tabbed content -->
        {{-- No overflow clipping here: it would break the sticky tab bar. --}}
        <div class="panel detail-main">
            <div class="tabbar px-5">
                <nav class="flex items-center gap-6 overflow-x-auto" id="orderTabs" role="tablist">
                    <button type="button" class="tab-button is-active" data-tab-target="overview" role="tab">
                        <i class="fas fa-circle-info"></i>Overview
                    </button>
                    <button type="button" class="tab-button" data-tab-target="notes" role="tab">
                        <i class="fas fa-note-sticky"></i>Notes &amp; credentials
                    </button>
                    <button type="button" class="tab-button" data-tab-target="documents" role="tab">
                        <i class="fas fa-paperclip"></i>Documents
                        <span class="tab-count">{{ count($documents) }}</span>
                    </button>
                    <button type="button" class="tab-button" data-tab-target="activity" role="tab">
                        <i class="fas fa-clock-rotate-left"></i>Change log
                        <span class="tab-count">{{ $order->audits->count() }}</span>
                    </button>
                </nav>
            </div>

            <!-- Overview -->
            <div class="p-5 sm:p-6" data-tab-panel="overview">
                @if($order->description)
                    <p class="eyebrow mb-2"><i class="fas fa-align-left icon-indigo"></i>What the client asked for</p>
                    <p class="body-text text-gray-700 dark:text-gray-300">{{ $order->description }}</p>
                @else
                    <div class="empty">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No description on this order.</p>
                        @can('update', $order)
                            <a href="{{ route('orders.edit', $order) }}" class="mt-1 inline-block text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">Add one</a>
                        @endcan
                    </div>
                @endif

                @unless($isSalesUser)
                <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex items-center justify-between mb-3">
                        <p class="eyebrow"><i class="fas fa-users icon-purple"></i>Working on it</p>
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $order->users->count() }} assigned</span>
                    </div>

                    @if($order->users->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @foreach($order->users as $member)
                                <div class="flex items-center gap-3 p-2.5 rounded-xl border border-gray-100 dark:border-gray-800 transition-colors">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $member->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $member->email }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Nobody is assigned yet</p>
                            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Assign the people delivering this order so it shows up on their board.</p>
                            @can('update', $order)
                                <a href="{{ route('orders.edit', $order) }}"
                                   class="mt-3 inline-flex items-center px-3.5 py-2 rounded-lg text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                    <i class="fas fa-user-plus mr-1.5"></i>Assign people
                                </a>
                            @endcan
                        </div>
                    @endif
                @endunless
            </div>

            <!-- Notes & credentials -->
            <div class="p-5 sm:p-6 hidden" data-tab-panel="notes">
                <div class="flex items-center justify-between mb-2">
                    <p class="eyebrow"><i class="fas fa-note-sticky icon-amber"></i>Notes</p>
                    @if($order->notes)
                        <button type="button" onclick="copyToClipboard(document.getElementById('orderNotes').innerText, this)"
                                class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                            <i class="fas fa-copy mr-1"></i>Copy
                        </button>
                    @endif
                </div>

                @if($order->notes)
                    <div id="orderNotes"
                         class="note-card body-text text-gray-800 dark:text-gray-200">{{ $order->notes }}</div>
                @else
                    <div class="empty">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nothing noted yet.</p>
                        @can('update', $order)
                            <a href="{{ route('orders.edit', $order) }}" class="mt-1 inline-block text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">Write a note</a>
                        @endcan
                    </div>
                @endif

                <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <p class="eyebrow">
                            <i class="fas fa-key icon-emerald"></i>Credentials
                            <span class="ml-1 inline-flex items-center gap-1 lock-tag text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                                <i class="fas fa-lock"></i>encrypted
                            </span>
                        </p>
                        @if($order->credentials)
                            <div class="flex items-center gap-3">
                                <button type="button" id="credentialsToggle" onclick="toggleCredentials()"
                                        class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    <i class="fas fa-eye mr-1"></i>Reveal
                                </button>
                                <button type="button" onclick="copyToClipboard(document.getElementById('credentialsBody').innerText, this)"
                                        class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    <i class="fas fa-copy mr-1"></i>Copy
                                </button>
                            </div>
                        @endif
                    </div>

                    @if($order->credentials)
                        <div class="cred-card">
                            <div id="credentialsBody" class="rich-text is-masked">{!! $order->credentials !!}</div>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-shield-halved mr-1"></i>Encrypted in the database, and never written to the change log.
                        </p>
                    @else
                        <div class="empty">
                            <p class="text-sm text-gray-500 dark:text-gray-400">No credentials stored for this order.</p>
                            @can('update', $order)
                                <a href="{{ route('orders.edit', $order) }}" class="mt-1 inline-block text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">Add credentials</a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            <div class="p-5 sm:p-6 hidden" data-tab-panel="documents">
                <div class="flex items-center justify-between mb-3">
                    <p class="eyebrow"><i class="fas fa-paperclip icon-blue"></i>Client documents</p>
                    @can('update', $order)
                        <a href="{{ route('orders.edit', $order) }}"
                           class="btn-ghost">
                            <i class="fas fa-cloud-arrow-up mr-1.5"></i>Upload files
                        </a>
                    @endcan
                </div>

                @if(count($documents) > 0)
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($documents as $document)
                            @php
                                $extension = strtolower(pathinfo($document['name'], PATHINFO_EXTENSION));
                                $documentIcon = match (true) {
                                    in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'webp']) => ['fa-file-image', 'icon-purple'],
                                    $extension === 'pdf' => ['fa-file-pdf', 'text-red-500'],
                                    in_array($extension, ['xls', 'xlsx', 'csv']) => ['fa-file-excel', 'icon-emerald'],
                                    in_array($extension, ['doc', 'docx']) => ['fa-file-word', 'icon-blue'],
                                    $extension === 'zip' => ['fa-file-zipper', 'icon-amber'],
                                    default => ['fa-file-lines', 'text-gray-400'],
                                };
                            @endphp
                            <div class="doc-row flex items-center gap-3 py-3" data-document-row="{{ $document['id'] }}">
                                <span class="w-9 h-9 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center flex-shrink-0">
                                    <i class="fas {{ $documentIcon[0] }} {{ $documentIcon[1] }}"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $document['name'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 num">
                                        {{ number_format(($document['size'] ?? 0) / 1024, 1) }} KB
                                        @if(! empty($document['uploaded_by_name']))
                                            · {{ $document['uploaded_by_name'] }}
                                        @endif
                                        · {{ \Illuminate\Support\Carbon::parse($document['uploaded_at'])->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="row-actions flex items-center gap-1">
                                    <a href="{{ route('orders.documents.download', [$order, $document['id']]) }}"
                                       class="px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-colors">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                    @can('update', $order)
                                        <button type="button"
                                                onclick="deleteDocument('{{ route('orders.documents.destroy', [$order, $document['id']]) }}')"
                                                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                            <i class="fas fa-trash"></i>
                                            <span class="sr-only">Remove {{ $document['name'] }}</span>
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty">
                        <i class="fas fa-folder-open text-2xl text-gray-300 dark:text-gray-700"></i>
                        <p class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">No files yet</p>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Add whatever the client sent when the order started — briefs, assets, access details.</p>
                        @can('update', $order)
                            <a href="{{ route('orders.edit', $order) }}"
                               class="mt-3 inline-flex items-center px-3.5 py-2 rounded-lg text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-cloud-arrow-up mr-1.5"></i>Upload files
                            </a>
                        @endcan
                    </div>
                @endif
            </div>

            <!-- Change log -->
            <div class="p-5 sm:p-6 hidden" data-tab-panel="activity">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-1">
                    <p class="eyebrow"><i class="fas fa-clock-rotate-left icon-indigo"></i>Change log</p>
                    @if($auditsTotal > 0)
                        <div class="log-filters" id="logFilters" role="group" aria-label="Filter the change log">
                            <button type="button" class="filter-chip is-active" data-log-filter="all">Everything</button>
                            <button type="button" class="filter-chip" data-log-filter="fields">Field edits</button>
                            <button type="button" class="filter-chip" data-log-filter="people">People</button>
                            <button type="button" class="filter-chip" data-log-filter="files">Files</button>
                        </div>
                    @endif
                </div>

                @if($auditsTotal > 0)
                    <p class="log-count" id="logCount">
                        Showing <span data-log-showing>{{ $audits->count() }}</span> of <span data-log-total>{{ $auditsTotal }}</span> {{ \Illuminate\Support\Str::plural('change', $auditsTotal) }}
                    </p>
                @endif

                <div class="log-list" id="auditList"
                     data-audits-url="{{ route('orders.audits', $order) }}"
                     aria-live="polite" aria-busy="false">
                    @if($auditsTotal > 0)
                        @include('orders.partials.audit-entries', ['audits' => $audits, 'order' => $order])
                    @else
                        <div class="empty">
                            <i class="fas fa-clock-rotate-left text-2xl text-gray-300 dark:text-gray-700"></i>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nothing has changed since this order was created.</p>
                        </div>
                    @endif
                </div>

                <div class="log-foot">
                    <button type="button" id="auditLoadMore" class="log-load-more {{ $audits->hasMorePages() ? '' : 'is-hidden' }}"
                            data-next-page="2" onclick="loadMoreAudits()">
                        <i class="fas fa-arrow-down mr-1.5"></i>
                        <span data-load-label>Load older changes</span>
                    </button>
                    <p id="auditEnd" class="log-end {{ $audits->hasMorePages() || $auditsTotal === 0 ? 'is-hidden' : '' }}">
                        That is the whole history of this order.
                    </p>
                </div>

                @if($order->statusHistory->count() > 0)
                    <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-800">
                        <p class="eyebrow mb-3"><i class="fas fa-timeline icon-blue"></i>Status history</p>
                        <div class="space-y-2">
                            @foreach($order->statusHistory as $history)
                                @php $historyStatus = $statusConfig[$history->status] ?? $statusConfig['nra']; @endphp
                                <div class="status-row flex flex-wrap items-center gap-3 px-3 py-2 rounded-lg">
                                    <span class="chip {{ $historyStatus['chip'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $historyStatus['dot'] }}"></span>
                                        {{ $historyStatus['label'] }}
                                    </span>
                                    @if($history->actual_revenue)
                                        <span class="text-xs font-bold num text-emerald-600 dark:text-emerald-400">${{ number_format($history->actual_revenue, 2) }}</span>
                                    @endif
                                    @if($history->changedByUser)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $history->changedByUser->name }}</span>
                                    @endif
                                    <span class="ml-auto text-xs text-gray-400 dark:text-gray-500 num">{{ $history->created_at->format('M d, Y g:i A') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="detail-side">
            @php
                $grossValue = (float) ($order->value ?: ($order->actual_revenue ? $order->actual_revenue / 0.80 : 0));
                $netValue = (float) ($order->actual_revenue ?? 0);
                $platformFee = max(0, $grossValue - $netValue);
            @endphp
            <div class="panel p-5">
                <p class="eyebrow mb-3"><i class="fas fa-sack-dollar icon-emerald"></i>Money</p>

                <dl class="money">
                    <div class="money-row">
                        <dt>Order amount</dt>
                        <dd>${{ number_format($grossValue, 2) }}</dd>
                    </div>
                    <div class="money-row">
                        <dt>Platform fee</dt>
                        <dd class="is-negative">−${{ number_format($platformFee, 2) }}</dd>
                    </div>
                    <div class="money-row is-total">
                        <dt>Net value</dt>
                        <dd>${{ number_format($netValue, 2) }}</dd>
                    </div>
                </dl>
            </div>

            <div class="panel p-5">
                <p class="eyebrow mb-1"><i class="fas fa-circle-nodes icon-indigo"></i>{{ $isSalesUser ? 'Order' : 'Assignment' }}</p>
                @unless($isSalesUser)
                    <div class="meta-row">
                        <span class="meta-key">Team</span>
                        <span class="meta-value">{{ $order->team?->name ?: 'Unassigned' }}</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Tech stack</span>
                        <span class="meta-value">{{ $order->techStack?->name ?: 'Not set' }}</span>
                    </div>
                @endunless
                @if($showsSalesOwner && $order->salesUser)
                    <div class="meta-row">
                        <span class="meta-key">Sold by</span>
                        <span class="meta-value">{{ $order->salesUser->name }}</span>
                    </div>
                @endif
                <div class="meta-row">
                    <span class="meta-key">Freelance profile</span>
                    <span class="meta-value">{{ $order->freelanceProfile?->username ?: 'Not set' }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-key">Order ID</span>
                    <span class="meta-value font-mono text-xs">{{ $order->order_id ?: '—' }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-key">Record</span>
                    <span class="meta-value num">#{{ $order->id }}</span>
                </div>
            </div>

            <div class="panel p-5">
                <p class="eyebrow mb-1"><i class="fas fa-calendar-days icon-orange"></i>Dates</p>
                <div class="meta-row">
                    <span class="meta-key">Started</span>
                    <span class="meta-value">{{ $order->start_date ? $order->start_date->format('M d, Y') : 'Not set' }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-key">Deadline</span>
                    <span class="meta-value">{{ $order->end_date ? $order->end_date->format('M d, Y') : 'Not set' }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-key">Delivered</span>
                    <span class="meta-value">{{ $order->delivered_at ? $order->delivered_at->format('M d, Y g:i A') : 'Not yet' }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-key">Created</span>
                    <span class="meta-value">{{ $order->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Tabs ---
        const tabButtons = document.querySelectorAll('[data-tab-target]');
        const tabPanels = document.querySelectorAll('[data-tab-panel]');

        function activateTab(target, updateHash = true) {
            tabButtons.forEach(button => button.classList.toggle('is-active', button.dataset.tabTarget === target));
            tabPanels.forEach(panel => panel.classList.toggle('hidden', panel.dataset.tabPanel !== target));

            if (updateHash && history.replaceState) {
                history.replaceState(null, '', '#' + target);
            }
        }

        tabButtons.forEach(button => {
            button.addEventListener('click', () => activateTab(button.dataset.tabTarget));
        });

        const initialTab = window.location.hash.replace('#', '');
        if (initialTab && document.querySelector(`[data-tab-panel="${initialTab}"]`)) {
            activateTab(initialTab, false);
        }

        // --- Progress fill animates in on load ---
        const fill = document.getElementById('progressFill');
        const clip = document.getElementById('progressLabelClip');
        if (fill && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            const target = fill.style.width;
            fill.style.width = '0%';
            clip.style.width = '0%';
            requestAnimationFrame(() => {
                fill.style.width = target;
                clip.style.width = target;
            });
        }

        // --- Progress editor ---
        const progressInput = document.getElementById('progressInput');
        if (progressInput) {
            progressInput.addEventListener('input', function() {
                document.getElementById('progressInputValue').textContent = this.value + '%';
            });
        }

        // --- Change log: filtering, paging and per-entry expansion ---
        const auditList = document.getElementById('auditList');

        document.querySelectorAll('[data-log-filter]').forEach(filter => {
            filter.addEventListener('click', function() {
                if (this.classList.contains('is-active')) {
                    return;
                }

                document.querySelectorAll('[data-log-filter]')
                    .forEach(item => item.classList.toggle('is-active', item === this));

                auditLog.filter = this.dataset.logFilter;
                fetchAudits(1, true);
            });
        });

        // Delegated so entries added by "load more" work without rebinding.
        if (auditList) {
            auditList.addEventListener('click', function(event) {
                const button = event.target.closest('[data-log-expand]');
                if (!button) {
                    return;
                }

                const rest = button.parentElement.querySelector('.log-change-rest');
                const isHidden = rest.hasAttribute('hidden');

                rest.toggleAttribute('hidden', !isHidden);
                button.textContent = isHidden ? button.dataset.lessLabel : button.dataset.moreLabel;
            });
        }
    });

    const auditLog = { filter: 'all', loading: false };

    function fetchAudits(page, replace) {
        const list = document.getElementById('auditList');
        const loadMore = document.getElementById('auditLoadMore');
        const end = document.getElementById('auditEnd');

        if (auditLog.loading) {
            return;
        }

        auditLog.loading = true;
        list.setAttribute('aria-busy', 'true');
        loadMore.disabled = true;
        loadMore.querySelector('[data-load-label]').textContent = 'Loading…';

        axios.get(list.dataset.auditsUrl, { params: { page: page, filter: auditLog.filter } })
            .then(({ data }) => {
                if (replace) {
                    list.innerHTML = data.html || emptyLogMarkup();
                } else {
                    appendAudits(list, data.html);
                }

                loadMore.dataset.nextPage = data.next_page;
                loadMore.classList.toggle('is-hidden', !data.has_more);
                end.classList.toggle('is-hidden', data.has_more || data.total === 0);

                const showing = list.querySelectorAll('[data-log-entry]').length;
                document.querySelector('[data-log-showing]').textContent = showing;
                document.querySelector('[data-log-total]').textContent = data.total;
            })
            .catch(() => {
                Swal.fire({
                    title: 'Could not load the change log',
                    text: 'Check your connection and try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc2626'
                });
            })
            .finally(() => {
                auditLog.loading = false;
                list.setAttribute('aria-busy', 'false');
                loadMore.disabled = false;
                loadMore.querySelector('[data-load-label]').textContent = 'Load older changes';
            });
    }

    function loadMoreAudits() {
        const loadMore = document.getElementById('auditLoadMore');
        fetchAudits(parseInt(loadMore.dataset.nextPage) || 2, false);
    }

    /**
     * Append a page of entries, folding the first day into the day already on
     * screen when the page break falls inside it.
     */
    function appendAudits(list, html) {
        const incoming = document.createElement('div');
        incoming.innerHTML = html;

        incoming.querySelectorAll('[data-log-day]').forEach(day => {
            const existing = list.querySelector(`[data-log-day="${day.dataset.logDay}"]`);

            if (existing) {
                existing.querySelector('.log-day-entries').append(...day.querySelector('.log-day-entries').children);
            } else {
                list.appendChild(day);
            }
        });
    }

    function emptyLogMarkup() {
        return `<div class="empty">
                    <i class="fas fa-clock-rotate-left text-2xl text-gray-300 dark:text-gray-700"></i>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No changes of this kind yet.</p>
                </div>`;
    }

    function toggleProgressEditor() {
        const editor = document.getElementById('progressEditor');
        editor.classList.toggle('hidden');

        if (!editor.classList.contains('hidden')) {
            document.getElementById('progressInput').focus();
        }
    }

    function saveProgress(orderId) {
        const value = document.getElementById('progressInput').value;

        axios.post(`/orders/${orderId}/quick-update`, { progress: value })
            .then(() => {
                Swal.fire({
                    title: 'Progress saved',
                    text: `This order is now at ${value}%.`,
                    icon: 'success',
                    timer: 1200,
                    showConfirmButton: false
                }).then(() => window.location.reload());
            })
            .catch(error => {
                Swal.fire({
                    title: 'Could not save',
                    text: error.response?.data?.message || 'Try again in a moment.',
                    icon: 'error',
                    confirmButtonColor: '#dc2626'
                });
            });
    }

    function toggleCredentials() {
        const body = document.getElementById('credentialsBody');
        const toggle = document.getElementById('credentialsToggle');
        const masked = body.classList.toggle('is-masked');

        toggle.innerHTML = masked
            ? '<i class="fas fa-eye mr-1"></i>Reveal'
            : '<i class="fas fa-eye-slash mr-1"></i>Hide';
    }

    function copyToClipboard(text, button) {
        navigator.clipboard.writeText(text).then(() => {
            const original = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-1"></i>Copied';
            setTimeout(() => {
                button.innerHTML = original;
            }, 1600);
        });
    }

    function deleteDocument(url) {
        Swal.fire({
            title: 'Remove this file?',
            text: 'It will be deleted from storage and logged in the change log.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Remove file',
            cancelButtonText: 'Keep it'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            axios.delete(url)
                .then(() => window.location.reload())
                .catch(error => {
                    Swal.fire({
                        title: 'Could not remove it',
                        text: error.response?.data?.message || 'Try again in a moment.',
                        icon: 'error',
                        confirmButtonColor: '#dc2626'
                    });
                });
        });
    }

    function deleteOrder(id) {
        Swal.fire({
            title: 'Delete this order?',
            text: 'This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Delete order',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();

                axios.delete('/orders/' + id)
                    .then(response => {
                        Swal.fire({
                            title: 'Deleted',
                            text: response.data.message || 'The order has been deleted.',
                            icon: 'success',
                            confirmButtonColor: '#9333ea'
                        }).then(() => {
                            window.location.href = '{{ route('orders.index') }}';
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Could not delete it',
                            text: error.response?.data?.message || 'Try again in a moment.',
                            icon: 'error',
                            confirmButtonColor: '#dc2626'
                        });
                    });
            }
        });
    }
</script>
@endpush
@endsection
