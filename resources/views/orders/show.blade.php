@extends('layouts.dashboard')

@section('title', 'Order Details')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
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

    .log-pager {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        margin-top: 0.5rem;
        flex-wrap: wrap;
    }

    .log-pager.is-hidden {
        display: none;
    }

    .log-page-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.4rem 0.9rem;
        border-radius: 9999px;
        border: 1px solid #e5e7eb;
        font-size: 0.75rem;
        font-weight: 700;
        color: #4b5563;
        transition: border-color 0.15s ease, color 0.15s ease, background-color 0.15s ease;
    }

    .log-page-btn:hover:not([disabled]) {
        border-color: #a5b4fc;
        background-color: #eef2ff;
        color: #4f46e5;
    }

    .log-page-btn[disabled] {
        opacity: 0.45;
        cursor: not-allowed;
    }

    .dark .log-page-btn {
        border-color: #1f2937;
        color: #9ca3af;
    }

    .dark .log-page-btn:hover:not([disabled]) {
        border-color: rgba(99, 102, 241, 0.4);
        background-color: rgba(99, 102, 241, 0.1);
        color: #c7d2fe;
    }

    .log-page-numbers {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .log-page-num {
        min-width: 2rem;
        height: 2rem;
        padding: 0 0.375rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #4b5563;
        border: 1px solid transparent;
        transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    }

    .log-page-num:hover:not(.is-active) {
        background-color: #f3f4f6;
        border-color: #e5e7eb;
    }

    .log-page-num.is-active {
        background-color: #4f46e5;
        color: #ffffff;
    }

    .dark .log-page-num {
        color: #9ca3af;
    }

    .dark .log-page-num:hover:not(.is-active) {
        background-color: #1f2937;
        border-color: #374151;
    }

    .dark .log-page-num.is-active {
        background-color: #6366f1;
        color: #ffffff;
    }

    .log-page-ellipsis {
        color: #9ca3af;
        padding: 0 0.25rem;
        font-size: 0.75rem;
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

    .detail-main {
        display: flex;
        flex-direction: column;
        overflow: visible;
    }

    .detail-main .tabbar {
        flex-shrink: 0;
    }

    .detail-main [data-tab-panel] {
        flex: 1;
        min-height: 0;
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

    .inline-dropzone {
        border: 2px dashed #d1d5db;
        background-color: #f9fafb;
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }

    .inline-dropzone:hover,
    .inline-dropzone.is-dragging {
        border-color: #818cf8;
        background-color: #eef2ff;
    }

    .dark .inline-dropzone {
        border-color: #374151;
        background-color: #1f2937;
    }

    .dark .inline-dropzone:hover,
    .dark .inline-dropzone.is-dragging {
        border-color: #6366f1;
        background-color: rgba(99, 102, 241, 0.08);
    }

    /* Inline editors: click a value to edit it in place, it saves when you leave. */
    .inline-editor {
        position: relative;
    }

    .inline-editor-display {
        cursor: pointer;
        transition: box-shadow 0.15s ease, outline-color 0.15s ease;
        border-radius: 0.5rem;
        outline: 2px solid transparent;
        outline-offset: 1px;
    }

    .inline-editor-display:hover,
    .inline-editor.is-editing .inline-editor-display {
        outline-color: #6366f1;
    }

    .inline-editor-display:hover {
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }

    .inline-editor-status {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.6875rem;
        font-weight: 600;
        color: #6b7280;
        min-height: 1em;
    }

    .inline-editor-status.is-error {
        color: #dc2626;
    }

    .inline-editor-status.is-saved {
        color: #059669;
    }

    .inline-editor .inline-editor-input {
        width: 100%;
    }

    .icon-amber { color: #f59e0b; }
    .icon-emerald { color: #10b981; }
    .icon-indigo { color: #6366f1; }
    .icon-purple { color: #a855f7; }
    .icon-blue { color: #3b82f6; }
    .icon-orange { color: #f97316; }

    .credit-share-pct {
        margin-left: 0.375rem;
        padding: 0.0625rem 0.375rem;
        border-radius: 9999px;
        font-size: 0.625rem;
        font-weight: 800;
        background-color: #eef2ff;
        color: #4338ca;
    }

    .dark .credit-share-pct {
        background-color: rgba(99, 102, 241, 0.15);
        color: #a5b4fc;
    }

    .credit-unattributed {
        margin-top: 0.625rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #b45309;
    }

    .dark .credit-unattributed {
        color: #fcd34d;
    }

    /* Visible keyboard focus everywhere on this page. */
    .panel a:focus-visible,
    .panel button:focus-visible,
    .tab-button:focus-visible {
        outline: 2px solid #6366f1;
        outline-offset: 2px;
        border-radius: 0.5rem;
    }

    #credentialsQuillEditor {
        min-height: 160px;
        font-size: 0.875rem;
    }

    .credentials-editor-shell .ql-toolbar.ql-snow,
    .credentials-editor-shell .ql-container.ql-snow {
        border: 0;
    }

    .credentials-editor-shell .ql-toolbar.ql-snow {
        border-bottom: 1px solid #e5e7eb;
    }

    .dark .credentials-editor-shell .ql-toolbar.ql-snow {
        border-bottom-color: rgba(245, 158, 11, 0.3) !important;
        background-color: rgba(245, 158, 11, 0.18) !important;
    }

    .dark .credentials-editor-shell .ql-editor {
        color: #f3f4f6;
    }

    .dark .credentials-editor-shell .ql-editor.ql-blank::before {
        color: #6b7280;
    }

    .dark .credentials-editor-shell .ql-snow .ql-stroke {
        stroke: #d1d5db;
    }

    .dark .credentials-editor-shell .ql-snow .ql-fill {
        fill: #d1d5db;
    }

    .dark .credentials-editor-shell .ql-snow .ql-picker {
        color: #d1d5db;
    }

    .dark .credentials-editor-shell .ql-snow .ql-picker-options {
        background-color: #1f2937;
        border-color: #374151;
    }

    .credentials-editor-shell {
        background-color: #fefcf0 !important;
        border-color: #fcd34d !important;
    }

    .credentials-editor-shell .ql-container.ql-snow,
    .credentials-editor-shell .ql-editor {
        background-color: #fefcf0 !important;
    }

    .dark .credentials-editor-shell {
        background-color: rgba(245, 158, 11, 0.12) !important;
        border-color: rgba(245, 158, 11, 0.35) !important;
    }

    .dark .credentials-editor-shell .ql-container.ql-snow,
    .dark .credentials-editor-shell .ql-editor {
        background-color: rgba(245, 158, 11, 0.12) !important;
        color: #f9fafb;
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

    $statusOptions = collect($statusConfig)->map(fn ($item, $value) => ['value' => $value, 'label' => $item['label']])->values()->all();
    $priorityOptions = collect($priorityConfig)->map(fn ($item, $value) => ['value' => $value, 'label' => $item['label']])->values()->all();
    $specialStatusOptions = collect(\App\SpecialStatus::cases())->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])->all();

    $allTeams = \App\Models\Team::orderBy('name')->get(['id', 'name']);
    $allTechStacks = \App\Models\TechStack::orderBy('name')->get(['id', 'name']);
    $allProfiles = \App\Models\Profile::orderBy('username')->get(['id', 'username']);

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
        <div style="height: 4px; background-color: {{ $status['rail'] }}" data-status-rail></div>
        <div class="p-5 sm:p-6">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        @can('manageOperations', $order)
                            <div class="inline-editor" data-field="status" data-type="select" data-url="{{ route('orders.quick-update', $order) }}">
                                <div class="inline-editor-display chip {{ $status['chip'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $status['dot'] }}"></span>
                                    {{ $status['label'] }}
                                </div>
                                <select class="inline-editor-input hidden rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-semibold px-2 py-1 text-gray-900 dark:text-gray-100">
                                    @foreach($statusOptions as $option)
                                        <option value="{{ $option['value'] }}" {{ $order->status === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                                <span class="inline-editor-status"></span>
                            </div>
                        @else
                            <span class="chip {{ $status['chip'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $status['dot'] }}"></span>
                                {{ $status['label'] }}
                            </span>
                        @endcan
                        <div class="flex items-center gap-2">
                            @can('manageOperations', $order)
                                <div class="inline-editor" data-field="priority" data-type="select" data-url="{{ route('orders.quick-update', $order) }}">
                                    <div class="inline-editor-display chip {{ $priority['chip'] }}">{{ $priority['label'] }}</div>
                                    <select class="inline-editor-input hidden rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-semibold px-2 py-1 text-gray-900 dark:text-gray-100">
                                        @foreach($priorityOptions as $option)
                                            <option value="{{ $option['value'] }}" {{ $order->priority === $option['value'] ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                        @endforeach
                                    </select>
                                    <span class="inline-editor-status"></span>
                                </div>
                            @else
                                <span class="chip {{ $priority['chip'] }}">{{ $priority['label'] }}</span>
                            @endcan
                        </div>
                        @can('manageOperations', $order)
                            <div class="inline-editor" data-field="special_status" data-type="select" data-url="{{ route('orders.quick-update', $order) }}">
                                <div class="inline-editor-display chip {{ $order->special_status ? 'bg-purple-100 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400' }}">
                                    @if($order->special_status)
                                        <i class="fas fa-bookmark text-[10px]"></i>{{ $order->special_status->getLabel() }}
                                    @else
                                        <i class="fas fa-tag text-[10px]"></i>Special status
                                    @endif
                                </div>
                                <select class="inline-editor-input hidden rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm font-semibold px-2 py-1 text-gray-900 dark:text-gray-100">
                                    <option value="">None</option>
                                    @foreach($specialStatusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ $order->special_status?->value === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <span class="inline-editor-status"></span>
                            </div>
                        @else
                            @if($order->special_status)
                                <span class="chip bg-purple-100 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400">
                                    <i class="fas fa-bookmark text-[10px]"></i>{{ $order->special_status->getLabel() }}
                                </span>
                            @endif
                        @endcan
                        @if($order->is_repeat)
                            <span class="chip bg-amber-100 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>Repeat
                            </span>
                        @else
                            <span class="chip bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Fresh
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100 truncate">{{ $order->name }}</h1>

                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                        <span class="font-mono text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300" data-mirror-field="order_id" data-empty-label="no order ID">
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
                    <div class="relative inline-block">
                        <button onclick="toggleOrderActionMenu('order-action-menu')"
                                class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-all"
                                title="Actions">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div id="order-action-menu"
                             class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                            <div class="py-1">
                                @can('update', $order)
                                    <a href="{{ route('orders.edit', $order) }}"
                                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-pen text-indigo-500"></i>
                                        Edit Order
                                    </a>
                                @endcan
                                @can('delete', $order)
                                    <button onclick="deleteOrder({{ $order->id }})"
                                            class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                        <i class="fas fa-trash"></i>
                                        Delete Order
                                    </button>
                                @endcan
                                <a href="{{ route('orders.index') }}"
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-arrow-left text-gray-500"></i>
                                    Back to List
                                </a>
                            </div>
                        </div>
                    </div>
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
                    <span class="chip bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400" id="deliveryDaysChip">
                        <i class="fas fa-triangle-exclamation text-[10px]"></i>{{ abs($daysLeft) }} {{ \Illuminate\Support\Str::plural('day', abs($daysLeft)) }} overdue
                    </span>
                @elseif($daysLeft === 0)
                    <span class="chip bg-orange-100 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400" id="deliveryDaysChip">Due today</span>
                @else
                    <span class="chip {{ $daysLeft <= 4 ? 'bg-orange-100 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400' }}" id="deliveryDaysChip">
                        {{ $daysLeft }} {{ \Illuminate\Support\Str::plural('day', $daysLeft) }} left
                    </span>
                @endif

                @can('updateProgress', $order)
                    <button type="button" id="progressEditToggle" onclick="toggleProgressEditor()"
                            class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                        <i class="fas fa-sliders mr-1"></i>Update progress
                    </button>
                @endif
            </div>
        </div>

        <!-- Dates rail -->
        <div class="flex items-center justify-between text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">
            @can('manageOperations', $order)
                <span class="inline-editor" data-field="start_date" data-type="date" data-url="{{ route('orders.quick-update', $order) }}">
                    <span class="inline-editor-display hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline underline-offset-2" data-empty-label="No start date" data-mirror-field="start_date">{{ $order->start_date ? $order->start_date->format('M d, Y') : 'No start date' }}</span>
                    <input type="date" class="inline-editor-input hidden rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs px-2 py-1" value="{{ $order->start_date ? $order->start_date->format('Y-m-d') : '' }}">
                    <span class="inline-editor-status"></span>
                </span>
            @else
                <span>{{ $order->start_date ? $order->start_date->format('M d, Y') : 'No start date' }}</span>
            @endcan
            @can('manageOperations', $order)
                <span class="inline-editor" data-field="end_date" data-type="date" data-url="{{ route('orders.quick-update', $order) }}">
                    <span class="inline-editor-display hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline underline-offset-2" data-empty-label="No deadline" data-mirror-field="end_date">{{ $order->end_date ? $order->end_date->format('M d, Y') : 'No deadline' }}</span>
                    <input type="date" class="inline-editor-input hidden rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs px-2 py-1" value="{{ $order->end_date ? $order->end_date->format('Y-m-d') : '' }}">
                    <span class="inline-editor-status"></span>
                </span>
            @else
                <span>{{ $order->end_date ? $order->end_date->format('M d, Y') : 'No deadline' }}</span>
            @endcan
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

        @can('updateProgress', $order)
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
        @endif
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
            <div class="p-5 sm:p-6 hidden" data-tab-panel="overview">
                <div class="flex items-center justify-between mb-2">
                    <p class="eyebrow"><i class="fas fa-align-left icon-indigo"></i>What the client asked for</p>
                </div>
                @can('editContent', $order)
                    <div class="inline-editor" data-field="description" data-type="textarea" data-url="{{ route('orders.quick-update', $order) }}">
                        <div class="inline-editor-display body-text {{ $order->description ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500 italic border border-dashed border-gray-200 dark:border-gray-700 rounded-lg px-3 py-4 text-sm' }}"
                             data-empty-label="No description on this order — click to add one">
                            {{ $order->description ?: 'No description on this order — click to add one' }}
                        </div>
                        <textarea class="inline-editor-input hidden w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm" rows="4">{{ $order->description }}</textarea>
                        <span class="inline-editor-status"></span>
                    </div>
                @else
                    <p class="body-text text-gray-700 dark:text-gray-300">{{ $order->description ?: 'No description on this order.' }}</p>
                @endcan

                @unless($isSalesUser)
                <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex items-center justify-between mb-3">
                        <p class="eyebrow"><i class="fas fa-users icon-purple"></i>Working on it</p>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400 dark:text-gray-500" id="assignedCount">{{ $order->users->count() }} assigned</span>
                            @can('manageAssignment', $order)
                                <button id="assignmentToggle" onclick="toggleAssignmentPicker()" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                    <i class="fas fa-pen mr-1"></i>Edit
                                </button>
                            @endcan
                        </div>
                    </div>

                    @if($order->users->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5" id="assignedMembersGrid">
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
                        <div class="empty" id="assignedMembersGrid">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Nobody is assigned yet</p>
                            <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Assign the people delivering this order so it shows up on their board.</p>
                        </div>
                    @endif

@can('manageAssignment', $order)
                    <div id="assignmentPicker" class="hidden mt-3 border border-gray-200 dark:border-gray-800 rounded-xl p-3 bg-gray-50 dark:bg-gray-800">
                        <div class="relative">
                            <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 focus-within:border-indigo-400 transition-colors">
                                <i class="fas fa-search text-xs text-gray-400"></i>
                                <input type="text" id="assignmentSearchInput" autocomplete="off"
                                       placeholder="Search people to assign…"
                                       class="w-full bg-transparent text-sm text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none">
                            </div>
                            <div id="assignmentSearchDropdown"
                                 class="hidden absolute left-0 right-0 mt-1 rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-lg z-20 max-h-64 overflow-y-auto"></div>
                        </div>
                        <div class="flex flex-wrap gap-1.5 mt-3" id="assignmentChips"></div>
                        <span id="assignmentStatus" class="inline-editor-status"></span>
                    </div>
                @endcan
                </div>
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

                @can('editContent', $order)
                    <div class="inline-editor" data-field="notes" data-type="textarea" data-url="{{ route('orders.quick-update', $order) }}">
                        <div id="orderNotes"
                             class="inline-editor-display note-card body-text {{ $order->notes ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400 dark:text-gray-500 italic border border-dashed border-gray-200 dark:border-gray-700 rounded-lg px-3 py-4 text-sm' }}"
                             data-empty-label="Nothing noted yet — click to write a note">
                            {{ $order->notes ?: 'Nothing noted yet — click to write a note' }}
                        </div>
                        <textarea class="inline-editor-input hidden w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm" rows="5">{{ $order->notes }}</textarea>
                        <span class="inline-editor-status"></span>
                    </div>
                @else
                    @if($order->notes)
                        <div id="orderNotes" class="note-card body-text text-gray-800 dark:text-gray-200">{{ $order->notes }}</div>
                    @else
                        <div class="empty">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nothing noted yet.</p>
                        </div>
                    @endif
                @endcan

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

                    @can('editContent', $order)
                        <div class="inline-editor" data-field="credentials" data-type="quill" data-url="{{ route('orders.quick-update', $order) }}">
                            <div id="credentialsBody" class="inline-editor-display cred-card rich-text is-masked {{ $order->credentials ? '' : 'text-gray-400 dark:text-gray-500 italic text-sm' }}"
                                 data-empty-label="No credentials stored — click to add">
                                {!! $order->credentials ?: 'No credentials stored — click to add' !!}
                            </div>
                            <div class="inline-editor-input hidden w-full credentials-editor-shell rounded-lg border overflow-hidden">
                                <div id="credentialsQuillEditor"></div>
                                <input type="hidden" name="credentials" id="credentialsInput" value="{{ $order->credentials }}">
                                <div class="p-2 border-t border-amber-300 dark:border-amber-500/20 flex items-center justify-end gap-2 bg-amber-50/50 dark:bg-amber-950/10 rounded-b-lg">
                                    <button type="button" class="quill-save-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">Save</button>
                                    <button type="button" class="quill-cancel-btn px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">Cancel</button>
                                </div>
                            </div>
                            <span class="inline-editor-status"></span>
                        </div>
                    @else
                        @if($order->credentials)
                            <div class="cred-card">
                                <div id="credentialsBody" class="rich-text is-masked">{!! $order->credentials !!}</div>
                            </div>
                        @else
                            <div class="empty">
                                <p class="text-sm text-gray-500 dark:text-gray-400">No credentials stored for this order.</p>
                            </div>
                        @endif
                    @endcan

                    @if($order->credentials)
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-shield-halved mr-1"></i>Encrypted in the database, and never written to the change log.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Documents -->
            <div class="p-5 sm:p-6 hidden" data-tab-panel="documents">
                <div class="flex items-center justify-between mb-3">
                    <p class="eyebrow"><i class="fas fa-paperclip icon-blue"></i>Client documents</p>
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ count($documents) }} uploaded</span>
                </div>

                @can('editContent', $order)
                    <form action="{{ route('orders.documents.store', $order) }}" method="POST"
                          enctype="multipart/form-data" class="mb-5" id="inlineDocForm">
                        @csrf
                        <label for="inlineDocInput"
                               id="inlineDocDropzone"
                               class="inline-dropzone flex flex-col items-center justify-center gap-1.5 w-full px-4 py-5 rounded-xl cursor-pointer">
                            <span class="w-9 h-9 rounded-lg bg-indigo-100 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                                <i class="fas fa-cloud-arrow-up"></i>
                            </span>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Click to upload or drop files here</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">PDF, Office, images, archives — up to 20 MB each</span>
                            <input type="file" name="client_documents[]" id="inlineDocInput" multiple class="hidden"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.md,.json,.zip,.png,.jpg,.jpeg,.gif,.webp">
                        </label>
                        <div id="inlineDocPreview" class="mt-3 space-y-2 hidden"></div>
                        <div class="mt-3 flex items-center justify-end gap-2">
                            <button type="button" onclick="clearInlineDocs()"
                                    class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                Clear
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-cloud-arrow-up mr-1.5"></i>Upload files
                            </button>
                        </div>
                        @error('client_documents')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @foreach($errors->get('client_documents.*') as $documentErrors)
                            @foreach($documentErrors as $documentError)
                                <p class="mt-1 text-xs text-red-600">{{ $documentError }}</p>
                            @endforeach
                        @endforeach
                    </form>
                @endcan

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
                                    @can('deleteDocuments', $order)
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
                        Showing <span data-log-showing>{{ $audits->firstItem() }}–{{ $audits->lastItem() }}</span> of <span data-log-total>{{ $auditsTotal }}</span> {{ \Illuminate\Support\Str::plural('change', $auditsTotal) }}
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

                <div class="log-pager {{ $audits->lastPage() <= 1 ? 'is-hidden' : '' }}" id="auditPager"
                     data-last-page="{{ $audits->lastPage() }}"
                     data-from="{{ $audits->firstItem() ?? 0 }}"
                     data-to="{{ $audits->lastItem() ?? 0 }}"
                     data-total="{{ $auditsTotal }}">
                    <button type="button" class="log-page-btn" id="auditPrevPage" disabled>
                        <i class="fas fa-chevron-left mr-1"></i>Newer
                    </button>
                    <div class="log-page-numbers" id="auditPageNumbers" role="navigation" aria-label="Change log pages"></div>
                    <button type="button" class="log-page-btn" id="auditNextPage">
                        Older<i class="fas fa-chevron-right ml-1"></i>
                    </button>
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
                                    @if($history->changedByUser && ($showsSalesOwner || ! $history->changedByUser->isSales()))
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
                <div class="flex items-center justify-between mb-1">
                    <p class="eyebrow"><i class="fas fa-circle-nodes icon-indigo"></i>{{ $isSalesUser ? 'Order' : 'Assignment' }}</p>
                </div>
                @unless($isSalesUser)
                    <div class="meta-row">
                        <span class="meta-key">Team</span>
                        @can('manageOperations', $order)
                            <span class="inline-editor" data-field="team_id" data-type="select" data-url="{{ route('orders.quick-update', $order) }}">
                                <span class="inline-editor-display meta-value" data-empty-label="Unassigned">{{ $order->team?->name ?: 'Unassigned' }}</span>
                                <select class="inline-editor-input hidden rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs px-2 py-1 text-gray-900 dark:text-gray-100">
                                    <option value="">None</option>
                                    @foreach($allTeams as $team)
                                        <option value="{{ $team->id }}" {{ $order->team_id === $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                    @endforeach
                                </select>
                                <span class="inline-editor-status"></span>
                            </span>
                        @else
                            <span class="meta-value">{{ $order->team?->name ?: 'Unassigned' }}</span>
                        @endcan
                    </div>
                    <div class="meta-row">
                        <span class="meta-key">Tech stack</span>
                        @can('manageOperations', $order)
                            <span class="inline-editor" data-field="tech_stack_id" data-type="select" data-url="{{ route('orders.quick-update', $order) }}">
                                <span class="inline-editor-display meta-value" data-empty-label="Not set">{{ $order->techStack?->name ?: 'Not set' }}</span>
                                <select class="inline-editor-input hidden rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs px-2 py-1 text-gray-900 dark:text-gray-100">
                                    <option value="">None</option>
                                    @foreach($allTechStacks as $stack)
                                        <option value="{{ $stack->id }}" {{ $order->tech_stack_id === $stack->id ? 'selected' : '' }}>{{ $stack->name }}</option>
                                    @endforeach
                                </select>
                                <span class="inline-editor-status"></span>
                            </span>
                        @else
                            <span class="meta-value">{{ $order->techStack?->name ?: 'Not set' }}</span>
                        @endcan
                    </div>
                @endunless
                @if($showsSalesOwner && $order->salesUser)
                    <div class="meta-row">
                        <span class="meta-key">Opened by</span>
                        <span class="meta-value">{{ $order->salesUser->name }}</span>
                    </div>
                @endif
                <div class="meta-row">
                    <span class="meta-key">Freelance profile</span>
                    @can('manageOperations', $order)
                        <span class="inline-editor" data-field="freelance_profile_id" data-type="select" data-url="{{ route('orders.quick-update', $order) }}">
                            <span class="inline-editor-display meta-value" data-empty-label="Not set">{{ $order->freelanceProfile?->username ?: 'Not set' }}</span>
                            <select class="inline-editor-input hidden rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs px-2 py-1 text-gray-900 dark:text-gray-100">
                                <option value="">None</option>
                                @foreach($allProfiles as $profile)
                                    <option value="{{ $profile->id }}" {{ $order->freelance_profile_id === $profile->id ? 'selected' : '' }}>{{ $profile->username }}</option>
                                @endforeach
                            </select>
                            <span class="inline-editor-status"></span>
                        </span>
                    @else
                        <span class="meta-value">{{ $order->freelanceProfile?->username ?: 'Not set' }}</span>
                    @endcan
                </div>
                <div class="meta-row">
                    <span class="meta-key">Order ID</span>
                    @can('manageOperations', $order)
                        <span class="inline-editor" data-field="order_id" data-type="text" data-url="{{ route('orders.quick-update', $order) }}">
                            <span class="inline-editor-display meta-value font-mono text-xs" data-empty-label="—" data-mirror-field="order_id">{{ $order->order_id ?: '—' }}</span>
                            <input type="text" class="inline-editor-input hidden rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs px-2 py-1 text-gray-900 dark:text-gray-100 font-mono" value="{{ $order->order_id }}" maxlength="255" placeholder="Set an order ID">
                            <span class="inline-editor-status"></span>
                        </span>
                    @else
                        <span class="meta-value font-mono text-xs">{{ $order->order_id ?: '—' }}</span>
                    @endcan
                </div>
                <div class="meta-row">
                    <span class="meta-key">Record</span>
                    <span class="meta-value num">#{{ $order->id }}</span>
                </div>
            </div>

            @if($showsSalesOwner && $order->salesShares->isNotEmpty())
                @php $creditTotal = (float) $order->salesShares->sum('amount'); @endphp
                <div class="panel p-5">
                    <p class="eyebrow mb-1"><i class="fas fa-handshake icon-emerald"></i>Sales credit</p>
                    @foreach($order->salesShares->sortByDesc('amount') as $share)
                        <div class="meta-row">
                            <span class="meta-key">
                                {{ $share->user?->name ?? 'Removed user' }}
                                @if($creditTotal > 0)
                                    <span class="credit-share-pct">{{ round((float) $share->amount / $creditTotal * 100) }}%</span>
                                @endif
                            </span>
                            <span class="meta-value num">${{ number_format((float) $share->amount, 2) }}</span>
                        </div>
                    @endforeach

                    @if($order->unattributedAmount() > 0.009)
                        <p class="credit-unattributed num">
                            <i class="fas fa-triangle-exclamation mr-1"></i>
                            ${{ number_format($order->unattributedAmount(), 2) }} of this order is not credited to anyone.
                        </p>
                    @endif
                </div>
            @endif

            <div class="panel p-5">
                <div class="flex items-center justify-between mb-1">
                    <p class="eyebrow"><i class="fas fa-calendar-days icon-orange"></i>Dates</p>
                </div>
                <div class="meta-row">
                    <span class="meta-key">Started</span>
                    @can('manageOperations', $order)
                        <span class="inline-editor" data-field="start_date" data-type="date" data-url="{{ route('orders.quick-update', $order) }}">
                            <span class="inline-editor-display meta-value" data-empty-label="Not set" data-mirror-field="start_date">{{ $order->start_date ? $order->start_date->format('M d, Y') : 'Not set' }}</span>
                            <input type="date" class="inline-editor-input hidden rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs px-2 py-1" value="{{ $order->start_date ? $order->start_date->format('Y-m-d') : '' }}">
                            <span class="inline-editor-status"></span>
                        </span>
                    @else
                        <span class="meta-value">{{ $order->start_date ? $order->start_date->format('M d, Y') : 'Not set' }}</span>
                    @endcan
                </div>
                <div class="meta-row">
                    <span class="meta-key">Deadline</span>
                    @can('manageOperations', $order)
                        <span class="inline-editor" data-field="end_date" data-type="date" data-url="{{ route('orders.quick-update', $order) }}">
                            <span class="inline-editor-display meta-value" data-empty-label="Not set" data-mirror-field="end_date">{{ $order->end_date ? $order->end_date->format('M d, Y') : 'Not set' }}</span>
                            <input type="date" class="inline-editor-input hidden rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-xs px-2 py-1" value="{{ $order->end_date ? $order->end_date->format('Y-m-d') : '' }}">
                            <span class="inline-editor-status"></span>
                        </span>
                    @else
                        <span class="meta-value">{{ $order->end_date ? $order->end_date->format('M d, Y') : 'Not set' }}</span>
                    @endcan
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
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Tabs ---
        const tabButtons = document.querySelectorAll('[data-tab-target]');
        const tabPanels = document.querySelectorAll('[data-tab-panel]');

        if (tabButtons.length === 0 || tabPanels.length === 0) {
            console.error('Tabs not found:', { tabButtons: tabButtons.length, tabPanels: tabPanels.length });
            return;
        }

        function activateTab(target, updateHash = true) {
            // Update button states
            tabButtons.forEach(button => {
                if (button.dataset.tabTarget === target) {
                    button.classList.add('is-active');
                } else {
                    button.classList.remove('is-active');
                }
            });

            // Update panel visibility
            tabPanels.forEach(panel => {
                if (panel.dataset.tabPanel === target) {
                    panel.classList.remove('hidden');
                } else {
                    panel.classList.add('hidden');
                }
            });

            if (updateHash && history.replaceState) {
                history.replaceState(null, '', '#' + target);
            }
        }

        tabButtons.forEach(button => {
            button.addEventListener('click', () => activateTab(button.dataset.tabTarget));
        });

        // Initialize tabs
        (function initTabs() {
            try {
                // Ensure DOM elements exist
                if (!tabButtons || tabButtons.length === 0) {
                    console.warn('No tab buttons found');
                    return;
                }
                if (!tabPanels || tabPanels.length === 0) {
                    console.warn('No tab panels found');
                    return;
                }

                const initialTab = window.location.hash.replace('#', '') || 'overview';
                // Activate the initial tab
                activateTab(initialTab, false);

            } catch (error) {
                console.error('Error initializing tabs:', error);
            }
        })();

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
                fetchAudits(1);
            });
        });

        // Delegated so entries re-rendered by paging still work without rebinding.
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

        // --- Change log pagination ---
        const auditPager = document.getElementById('auditPager');
        if (auditPager) {
            auditLog.page = 1;
            auditLog.lastPage = parseInt(auditPager.dataset.lastPage, 10) || 1;
            auditLog.from = parseInt(auditPager.dataset.from, 10) || 0;
            auditLog.to = parseInt(auditPager.dataset.to, 10) || 0;
            auditLog.total = parseInt(auditPager.dataset.total, 10) || 0;

            document.getElementById('auditPrevPage').addEventListener('click', () => {
                if (!auditLog.loading && auditLog.page > 1) {
                    fetchAudits(auditLog.page - 1);
                }
            });

            document.getElementById('auditNextPage').addEventListener('click', () => {
                if (!auditLog.loading && auditLog.page < auditLog.lastPage) {
                    fetchAudits(auditLog.page + 1);
                }
            });

            document.getElementById('auditPageNumbers').addEventListener('click', (event) => {
                const button = event.target.closest('[data-page]');
                if (button && !auditLog.loading) {
                    fetchAudits(parseInt(button.dataset.page, 10));
                }
            });

            renderAuditPager();
        }
    });

    const auditLog = { filter: 'all', loading: false, page: 1, lastPage: 1, from: 0, to: 0, total: 0 };

    function fetchAudits(page, filter = auditLog.filter) {
        const list = document.getElementById('auditList');

        if (!list || auditLog.loading) {
            return;
        }

        auditLog.loading = true;
        list.setAttribute('aria-busy', 'true');

        axios.get(list.dataset.auditsUrl, { params: { page, filter } })
            .then(({ data }) => {
                list.innerHTML = data.html || emptyLogMarkup();

                auditLog.page = data.current_page;
                auditLog.lastPage = data.last_page;
                auditLog.from = data.from ?? 0;
                auditLog.to = data.to ?? 0;
                auditLog.total = data.total;

                const showing = document.querySelector('[data-log-showing]');
                const total = document.querySelector('[data-log-total]');
                if (showing) {
                    showing.textContent = auditLog.from ? `${auditLog.from}–${auditLog.to}` : '0';
                }
                if (total) {
                    total.textContent = auditLog.total;
                }
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
                renderAuditPager();
            });
    }

    function renderAuditPager() {
        const pager = document.getElementById('auditPager');
        if (!pager) {
            return;
        }

        pager.classList.toggle('is-hidden', auditLog.lastPage <= 1);

        const prev = document.getElementById('auditPrevPage');
        const next = document.getElementById('auditNextPage');
        prev.disabled = auditLog.loading || auditLog.page <= 1;
        next.disabled = auditLog.loading || auditLog.page >= auditLog.lastPage;

        const numbers = document.getElementById('auditPageNumbers');
        numbers.innerHTML = pageButtons(auditLog.page, auditLog.lastPage)
            .map(item => item === '…'
                ? '<span class="log-page-ellipsis">…</span>'
                : `<button type="button" class="log-page-num ${item === auditLog.page ? 'is-active' : ''}" data-page="${item}">${item}</button>`)
            .join('');
    }

    function pageButtons(current, last) {
        const visible = new Set();
        [1, 2, last, last - 1].forEach(page => {
            if (page >= 1 && page <= last) {
                visible.add(page);
            }
        });
        for (let i = current - 1; i <= current + 1; i++) {
            if (i >= 1 && i <= last) {
                visible.add(i);
            }
        }

        const pages = Array.from(visible).sort((a, b) => a - b);
        const buttons = [];
        let previous = 0;

        for (const page of pages) {
            if (page - previous > 1) {
                buttons.push('…');
            }
            buttons.push(page);
            previous = page;
        }

        return buttons;
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

    // --- Inline document upload (details page) ---
    function formatInlineSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
    }

    function renderInlineDocPreview(input) {
        const preview = document.getElementById('inlineDocPreview');
        const files = Array.from(input.files || []);

        if (!preview || files.length === 0) {
            return;
        }

        if (files.length === 0) {
            preview.classList.add('hidden');
            preview.innerHTML = '';
            return;
        }

        preview.classList.remove('hidden');
        preview.innerHTML = files.map(file => `
            <li class="flex items-center gap-3 px-3 py-2 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100">
                <i class="fas fa-file-arrow-up text-indigo-500 dark:text-indigo-400"></i>
                <span class="text-sm text-gray-800 dark:text-gray-200 truncate flex-1">${file.name}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">${formatInlineSize(file.size)}</span>
            </li>
        `).join('');
    }

    function clearInlineDocs() {
        const input = document.getElementById('inlineDocInput');
        const preview = document.getElementById('inlineDocPreview');
        if (input) input.value = '';
        if (preview) {
            preview.classList.add('hidden');
            preview.innerHTML = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('inlineDocInput');
        const dropzone = document.getElementById('inlineDocDropzone');

        if (!input || !dropzone) {
            return;
        }

        input.addEventListener('change', () => renderInlineDocPreview(input));

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(event) {
                event.preventDefault();
                dropzone.classList.add('is-dragging');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(event) {
                event.preventDefault();
                dropzone.classList.remove('is-dragging');
            });
        });

        dropzone.addEventListener('drop', function(event) {
            input.files = event.dataTransfer.files;
            renderInlineDocPreview(input);
        });
    });

    // Toggle order action menu
    function toggleOrderActionMenu(menuId) {
        // Close all other menus first
        const allMenus = document.querySelectorAll('[id^="order-action-menu"], [id^="action-menu-"], [id^="overdue-menu-"]');
        allMenus.forEach(menu => {
            if (menu.id !== menuId) {
                menu.classList.add('hidden');
            }
        });

        // Toggle the current menu
        const menu = document.getElementById(menuId);
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    // Close menus when clicking outside
    document.addEventListener('click', function(event) {
        const isMenuButton = event.target.closest('[onclick^="toggleOrderActionMenu"], [onclick^="toggleActionMenu"], [onclick^="toggleOverdueActionMenu"]');
        const isMenuContent = event.target.closest('[id^="order-action-menu"], [id^="action-menu-"], [id^="overdue-menu-"]');

        if (!isMenuButton && !isMenuContent) {
            const allMenus = document.querySelectorAll('[id^="order-action-menu"], [id^="action-menu-"], [id^="overdue-menu-"]');
            allMenus.forEach(menu => menu.classList.add('hidden'));
        }
    });

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

    // --- Inline editors: click a value, edit it in place, it saves on blur/change ---
    const ORDER_STATUS_CONFIG = @js($statusConfig);
    const ORDER_PRIORITY_CONFIG = @js($priorityConfig);
    const SPECIAL_STATUS_LABELS = @js($specialStatusOptions);
    const QUICK_UPDATE_URL = @js(route('orders.quick-update', $order));

    let credentialsQuill = null;
    document.addEventListener('DOMContentLoaded', function() {
        const editorElement = document.getElementById('credentialsQuillEditor');
        const hiddenInput = document.getElementById('credentialsInput');
        if (editorElement && hiddenInput && typeof Quill !== 'undefined') {
            credentialsQuill = new Quill(editorElement, {
                theme: 'snow',
                placeholder: 'Hosting panel, client portal, API keys…',
                modules: {
                    toolbar: [
                        [{ header: [2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['link', 'code-block', 'blockquote'],
                        ['clean'],
                    ],
                },
            });
            if (hiddenInput.value) {
                credentialsQuill.root.innerHTML = hiddenInput.value;
            }
        }
    });

    const currentDates = {
        start_date: @js($order->start_date ? $order->start_date->format('Y-m-d') : ''),
        end_date: @js($order->end_date ? $order->end_date->format('Y-m-d') : ''),
    };

    function formatShortDate(value) {
        if (!value) return '';
        const [year, month, day] = value.split('-');
        return new Date(year, month - 1, day).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    function dayCountFrom(dateStr) {
        if (!dateStr) return null;
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const end = new Date(dateStr + 'T00:00:00');
        return Math.round((end - today) / 86400000);
    }

    function renderDeliveryDaysChip() {
        const chip = document.getElementById('deliveryDaysChip');
        if (!chip) return;

        const daysLeft = dayCountFrom(currentDates.end_date);
        let html;
        if (currentDates.end_date === '') {
            html = 'No deadline set';
        } else if (daysLeft < 0) {
            const days = Math.abs(daysLeft);
            html = `<i class="fas fa-triangle-exclamation text-[10px]"></i>${days} ${days === 1 ? 'day' : 'days'} overdue`;
        } else if (daysLeft === 0) {
            html = 'Due today';
        } else {
            html = `${daysLeft} ${daysLeft === 1 ? 'day' : 'days'} left`;
        }

        chip.className = 'chip ' + (currentDates.end_date === ''
            ? 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'
            : (daysLeft < 0
                ? 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400'
                : (daysLeft <= 4
                    ? 'bg-orange-100 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400'
                    : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400')));
        chip.innerHTML = html;
    }

    function updateInlineDisplay(editor, value) {
        const display = editor.display;

        switch (editor.field) {
            case 'status': {
                const cfg = ORDER_STATUS_CONFIG[value] || ORDER_STATUS_CONFIG.nra;
                display.className = 'inline-editor-display chip ' + cfg.chip;
                display.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${cfg.dot}"></span> ${cfg.label}`;
                const rail = document.querySelector('[data-status-rail]');
                if (rail) rail.style.backgroundColor = cfg.rail;
                break;
            }
            case 'priority': {
                const cfg = ORDER_PRIORITY_CONFIG[value] || ORDER_PRIORITY_CONFIG.medium;
                display.className = 'inline-editor-display chip ' + cfg.chip;
                display.textContent = cfg.label;
                break;
            }
            case 'special_status': {
                if (value) {
                    display.className = 'inline-editor-display chip bg-purple-100 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400';
                    display.innerHTML = `<i class="fas fa-bookmark text-[10px]"></i>${SPECIAL_STATUS_LABELS[value] || value}`;
                } else {
                    display.className = 'inline-editor-display chip bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400';
                    display.innerHTML = '<i class="fas fa-tag text-[10px]"></i>Special status';
                }
                break;
            }
            case 'start_date':
            case 'end_date': {
                currentDates[editor.field] = value || '';
                document.querySelectorAll(`[data-mirror-field="${editor.field}"]`).forEach(el => {
                    el.textContent = value ? formatShortDate(value) : (el.dataset.emptyLabel || 'Not set');
                });
                renderDeliveryDaysChip();
                break;
            }
            case 'team_id':
            case 'tech_stack_id':
            case 'freelance_profile_id': {
                const selected = editor.input.selectedOptions[0];
                display.textContent = selected && selected.value !== '' ? selected.textContent : (display.dataset.emptyLabel || 'Not set');
                break;
            }
            case 'order_id': {
                document.querySelectorAll('[data-mirror-field="order_id"]').forEach(el => {
                    el.textContent = value || (el.dataset.emptyLabel || '—');
                });
                break;
            }
            case 'credentials': {
                if (value) {
                    display.classList.remove('text-gray-400', 'dark:text-gray-500', 'italic', 'text-sm');
                    display.classList.add('is-masked');
                    display.innerHTML = value;
                    const toggle = document.getElementById('credentialsToggle');
                    if (toggle) {
                        toggle.innerHTML = '<i class="fas fa-eye mr-1"></i>Reveal';
                    }
                } else {
                    display.textContent = display.dataset.emptyLabel || 'No credentials stored';
                }
                break;
            }
            default: {
                display.textContent = value || (display.dataset.emptyLabel || '—');
            }
        }
    }

    function handleInlineSave(editor, value) {
        const data = {};
        data[editor.field] = value;
        return axios.post(QUICK_UPDATE_URL, data);
    }

    document.querySelectorAll('.inline-editor').forEach(root => {
        const isQuill = root.dataset.type === 'quill';
        const editor = {
            root,
            field: root.dataset.field,
            display: root.querySelector('.inline-editor-display'),
            input: isQuill ? root.querySelector('#credentialsInput') : root.querySelector('.inline-editor-input'),
            status: root.querySelector('.inline-editor-status'),
            value: isQuill ? root.querySelector('#credentialsInput').value : root.querySelector('.inline-editor-input').value,
            saving: false,
        };

        const restore = () => {
            editor.root.classList.remove('is-editing');
            if (isQuill) {
                root.querySelector('.credentials-editor-shell').classList.add('hidden');
            } else {
                editor.input.classList.add('hidden');
            }
            editor.display.classList.remove('hidden');
        };

        const save = (value) => {
            if (editor.saving) {
                return;
            }

            if (value === editor.value) {
                restore();
                return;
            }

            editor.saving = true;
            editor.status.textContent = 'Saving…';
            editor.status.className = 'inline-editor-status';

            handleInlineSave(editor, value)
                .then(() => {
                    editor.value = value;
                    updateInlineDisplay(editor, value);
                    editor.status.textContent = 'Saved';
                    editor.status.classList.add('is-saved');
                    setTimeout(() => {
                        if (editor.status.textContent === 'Saved') {
                            editor.status.textContent = '';
                            editor.status.className = 'inline-editor-status';
                        }
                    }, 2000);
                })
                .catch(() => {
                    editor.status.textContent = 'Could not save';
                    editor.status.classList.add('is-error');
                })
                .finally(() => {
                    editor.saving = false;
                    restore();
                });
        };

        editor.display.addEventListener('click', () => {
            if (editor.root.classList.contains('is-editing')) {
                return;
            }
            editor.root.classList.add('is-editing');
            editor.display.classList.add('hidden');
            if (isQuill) {
                root.querySelector('.credentials-editor-shell').classList.remove('hidden');
                if (credentialsQuill) {
                    credentialsQuill.focus();
                }
            } else {
                editor.input.classList.remove('hidden');
                editor.input.focus();
                if (editor.input.tagName === 'TEXTAREA') {
                    editor.input.select();
                }
            }
        });

        if (isQuill) {
            root.querySelector('.quill-save-btn').addEventListener('click', () => {
                const htmlValue = credentialsQuill.getText().trim() === '' ? '' : credentialsQuill.root.innerHTML;
                save(htmlValue);
            });
            root.querySelector('.quill-cancel-btn').addEventListener('click', () => {
                credentialsQuill.root.innerHTML = editor.value;
                restore();
            });
            if (credentialsQuill) {
                credentialsQuill.keyboard.addBinding({
                    key: 'Escape'
                }, function() {
                    credentialsQuill.root.innerHTML = editor.value;
                    restore();
                });
                credentialsQuill.keyboard.addBinding({
                    key: 'Enter',
                    shortKey: true
                }, function() {
                    const htmlValue = credentialsQuill.getText().trim() === '' ? '' : credentialsQuill.root.innerHTML;
                    save(htmlValue);
                });
            }
        } else {
            editor.input.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    event.preventDefault();
                    editor.input.value = editor.value;
                    restore();
                } else if (event.key === 'Enter' && editor.input.tagName === 'TEXTAREA' && (event.ctrlKey || event.metaKey)) {
                    event.preventDefault();
                    save(editor.input.value);
                }
            });

            if (editor.input.tagName === 'TEXTAREA') {
                editor.input.addEventListener('blur', () => save(editor.input.value));
            } else {
                editor.input.addEventListener('change', () => save(editor.input.value));
            }
        }
    });

    // --- Assignment picker: searchable, saves on every add/remove ---
    const ASSIGNMENT_SEARCH_URL = @js(route('orders.search-users'));

    const assignment = {
        users: @js($order->users->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email])->values()->all()),
        searchTimeout: null,
        saving: false,
    };

    function toggleAssignmentPicker() {
        const picker = document.getElementById('assignmentPicker');
        if (picker) {
            picker.classList.toggle('hidden');
            if (!picker.classList.contains('hidden')) {
                renderAssignmentChips();
                document.getElementById('assignmentSearchInput').focus();
                fetchAssignmentUsers('').then(renderAssignmentDropdown);
            }
        }
    }

    function assignmentStatus(message, kind) {
        const status = document.getElementById('assignmentStatus');
        if (!status) {
            return;
        }
        status.textContent = message;
        status.className = 'inline-editor-status' + (kind ? ` is-${kind}` : '');
    }

    function renderAssignedMembers() {
        const grid = document.getElementById('assignedMembersGrid');
        const count = document.getElementById('assignedCount');

        if (count) {
            count.textContent = assignment.users.length + ' assigned';
        }

        if (!grid) {
            return;
        }

        if (assignment.users.length === 0) {
            grid.className = 'empty';
            grid.innerHTML = '<p class="text-sm font-medium text-gray-600 dark:text-gray-300">Nobody is assigned yet</p>' +
                '<p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Assign the people delivering this order so it shows up on their board.</p>';
            return;
        }

        grid.className = 'grid grid-cols-1 sm:grid-cols-2 gap-2.5';
        grid.innerHTML = assignment.users.map(user => {
            const initial = user.name.charAt(0).toUpperCase();

            return `<div class="flex items-center gap-3 p-2.5 rounded-xl border border-gray-100 dark:border-gray-800 transition-colors">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">${initial}</div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">${user.name}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">${user.email || ''}</p>
                </div>
            </div>`;
        }).join('');
    }

    function renderAssignmentChips() {
        const chips = document.getElementById('assignmentChips');
        if (!chips) {
            return;
        }

        if (assignment.users.length === 0) {
            chips.innerHTML = '<span class="text-xs text-gray-400 dark:text-gray-500">No one assigned yet — search above to add people.</span>';
            return;
        }

        chips.innerHTML = assignment.users.map(user => `
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-100 dark:bg-indigo-500/10 text-indigo-800 dark:text-indigo-300 rounded-lg text-sm">
                <span class="truncate max-w-40">${user.name}</span>
                <button type="button" class="assignment-chip-remove hover:text-red-600 dark:hover:text-red-400 transition-colors" data-user-id="${user.id}" title="Remove ${user.name}">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </span>
        `).join('');

        chips.querySelectorAll('.assignment-chip-remove').forEach(button => {
            button.addEventListener('click', function() {
                removeAssignmentUser(parseInt(this.dataset.userId, 10));
            });
        });
    }

    async function fetchAssignmentUsers(search = '') {
        const excludeIds = assignment.users.map(user => user.id);

        try {
            const params = new URLSearchParams();
            params.append('exclude_roles', 'sales-leader,sales-member');
            if (search) {
                params.append('search', search);
            }
            if (excludeIds.length > 0) {
                params.append('exclude', excludeIds.join(','));
            }

            const response = await axios.get(`${ASSIGNMENT_SEARCH_URL}?${params}`);
            return response.data.users || [];
        } catch (error) {
            return [];
        }
    }

    function renderAssignmentDropdown(users) {
        const dropdown = document.getElementById('assignmentSearchDropdown');
        if (!dropdown) {
            return;
        }

        if (users.length === 0) {
            dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">No one found</div>';
            return;
        }

        dropdown.innerHTML = users.map(user => `
            <div class="assignment-option flex items-center gap-3 p-3 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors"
                 data-user-id="${user.id}" data-user-name="${user.name}" data-user-email="${user.email || ''}">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                    ${user.name.charAt(0).toUpperCase()}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-semibold text-sm text-gray-900 dark:text-gray-100 truncate">${user.name}</span>
                        ${user.employee_id ? `<span class="text-xs text-white bg-indigo-500 px-2 py-0.5 rounded-full flex-shrink-0">${user.employee_id}</span>` : ''}
                        ${user.team ? `<span class="text-xs text-white bg-purple-500 px-2 py-0.5 rounded-full flex-shrink-0"><i class="fas fa-users mr-1"></i>${user.team.name}</span>` : ''}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        ${user.position ? `<span class="font-medium text-gray-600 dark:text-gray-300">${user.position}</span> • ` : ''}${user.email || ''}
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-500/10 hover:bg-indigo-200 dark:hover:bg-indigo-500/20 flex items-center justify-center transition-colors">
                        <i class="fas fa-plus text-indigo-600 dark:text-indigo-400 text-xs"></i>
                    </div>
                </div>
            </div>
        `).join('');

        dropdown.querySelectorAll('.assignment-option').forEach(option => {
            option.addEventListener('click', function() {
                addAssignmentUser({
                    id: parseInt(this.dataset.userId, 10),
                    name: this.dataset.userName,
                    email: this.dataset.userEmail,
                });
            });
        });
    }

    function addAssignmentUser(user) {
        if (assignment.users.some(existing => existing.id === user.id)) {
            return;
        }

        assignment.users.push(user);
        saveAssignment();
    }

    function removeAssignmentUser(userId) {
        assignment.users = assignment.users.filter(user => user.id !== userId);
        saveAssignment();
    }

    function saveAssignment() {
        if (assignment.saving) {
            return;
        }

        assignment.saving = true;
        assignmentStatus('Saving…');

        axios.post(QUICK_UPDATE_URL, { users: assignment.users.map(user => user.id) })
            .then(() => {
                renderAssignmentChips();
                renderAssignedMembers();
                assignmentStatus('Saved', 'saved');
                fetchAssignmentUsers(document.getElementById('assignmentSearchInput').value.trim()).then(renderAssignmentDropdown);
            })
            .catch(() => {
                assignmentStatus('Could not save', 'error');
            })
            .finally(() => {
                assignment.saving = false;
                setTimeout(() => {
                    const status = document.getElementById('assignmentStatus');
                    if (status && status.textContent === 'Saved') {
                        assignmentStatus('');
                    }
                }, 2000);
            });
    }

    // Search input events
    const assignmentSearchInput = document.getElementById('assignmentSearchInput');
    if (assignmentSearchInput) {
        assignmentSearchInput.addEventListener('input', function() {
            const query = this.value.trim();
            const dropdown = document.getElementById('assignmentSearchDropdown');
            dropdown.classList.remove('hidden');

            if (assignment.searchTimeout) {
                clearTimeout(assignment.searchTimeout);
            }

            assignment.searchTimeout = setTimeout(async () => {
                const users = await fetchAssignmentUsers(query);
                renderAssignmentDropdown(users);
            }, 250);
        });

        assignmentSearchInput.addEventListener('focus', async function() {
            document.getElementById('assignmentSearchDropdown').classList.remove('hidden');
            const users = await fetchAssignmentUsers(this.value.trim());
            renderAssignmentDropdown(users);
        });

        assignmentSearchInput.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.getElementById('assignmentSearchDropdown').classList.add('hidden');
            }
        });
    }

    // Close the dropdown and the picker when clicking outside.
    document.addEventListener('click', (event) => {
        const picker = document.getElementById('assignmentPicker');
        const toggle = document.getElementById('assignmentToggle');
        if (!picker || picker.classList.contains('hidden')) {
            return;
        }

        if (!picker.contains(event.target) && !(toggle && toggle.contains(event.target))) {
            picker.classList.add('hidden');
        }
    });

    document.addEventListener('click', (event) => {
        const picker = document.getElementById('assignmentPicker');
        if (!picker || picker.classList.contains('hidden')) {
            return;
        }

        const searchBox = picker.querySelector('.relative');
        const dropdown = document.getElementById('assignmentSearchDropdown');
        if (searchBox && dropdown && !dropdown.classList.contains('hidden') && !searchBox.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection
