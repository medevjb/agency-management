@extends('layouts.dashboard')

@section('title', 'Orders')

@push('styles')
    <style>
        /* Column Width Control */
        /* Status Column - compact width for dropdown */
        #ordersTable colgroup col[data-dt-column="3"],
        #ordersTable tbody td[data-dt-column="3"] {
            min-width: 115px !important;
            max-width: 130px !important;
            width: 120px !important;
        }

        /* Priority Column - compact width for dropdown */
        #ordersTable colgroup col[data-dt-column="4"],
        #ordersTable tbody td[data-dt-column="4"] {
            min-width: 90px !important;
            max-width: 100px !important;
            width: 95px !important;
        }

        /* Team Column - compact width */
        #ordersTable colgroup col[data-dt-column="7"],
        #ordersTable tbody td[data-dt-column="7"] {
            min-width: 80px !important;
            max-width: 120px !important;
            width: 100px !important;
        }

        /* Users Column - compact width */
        #ordersTable colgroup col[data-dt-column="8"],
        #ordersTable tbody td[data-dt-column="8"] {
            min-width: 85px !important;
            max-width: 110px !important;
            width: 95px !important;
        }

        /* Profile Column - compact width */
        #ordersTable colgroup col[data-dt-column="3"],
        #ordersTable tbody td[data-dt-column="3"] {
            min-width: 90px !important;
            max-width: 120px !important;
            width: 100px !important;
        }

        /* Custom scrollbar for horizontal scrolling */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* Responsive DataTable container */
        .datatable-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .datatable-container table {
            min-width: 100%;
        }

        /* DataTables Pagination Styling matching users module */
        #ordersTable_wrapper .dataTables_length select {
            border-radius: 10px;
            padding: 5px 25px;
            border: 2px solid #e5e7eb;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            background-color: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        #ordersTable_wrapper .dataTables_length select:hover {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        #ordersTable_wrapper .dataTables_length select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        #ordersTable_wrapper .dataTables_info {
            padding: 15px 20px;
            margin: 0;
            background: #ffffff;
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
        }

        #ordersTable_wrapper .dataTables_paginate {
            padding: 15px 20px;
            margin: 0;
            background: #ffffff;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 5px;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button {
            border: 1px solid #d1d5db !important;
            border-radius: 8px !important;
            padding: 0.5rem 1rem !important;
            margin: 0 2px !important;
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%) !important;
            color: #374151 !important;
            font-weight: 500 !important;
            font-size: 14px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button:hover {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%) !important;
            border-color: #d1d5db !important;
            color: #374151 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #6366f1 0%, #5558dd 100%) !important;
            border-color: #6366f1 !important;
            color: white !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3) !important;
            transform: translateY(-1px);
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: linear-gradient(135deg, #5558dd 0%, #4c51bf 100%) !important;
            border-color: #4c51bf !important;
            box-shadow: 0 6px 16px rgba(85, 83, 221, 0.4) !important;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.first,
        #ordersTable_wrapper .dataTables_paginate .paginate_button.last {
            font-weight: 600;
            padding: 0.75rem 1.5rem !important;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.previous,
        #ordersTable_wrapper .dataTables_paginate .paginate_button.next {
            padding: 0.75rem 1.25rem !important;
            font-weight: 600;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.ellipsis {
            padding: 0.75rem 1rem !important;
            background: transparent !important;
            border: none !important;
            color: #6b7280 !important;
            cursor: default !important;
            box-shadow: none !important;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.ellipsis:hover {
            background: transparent !important;
            border: none !important;
            color: #6b7280 !important;
            cursor: default !important;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed !important;
        }

        #ordersTable_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%) !important;
            border-color: #d1d5db !important;
            transform: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        }

        /* Countdown Timer Styling */
        .countdown-timer {
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .countdown-text {
            white-space: nowrap;
        }

        .countdown-unit {
            display: inline-flex;
            align-items: baseline;
            gap: 1px;
            font-family: 'Courier New', monospace;
            font-weight: 700;
            letter-spacing: 0.3px;
            white-space: nowrap;
            font-size: 0.85em;
        }

        .countdown-unit small {
            font-size: 0.7em !important;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: 1px;
        }

        .animate-pulse {
            animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        /* Tooltip styling */
        .cursor-help {
            cursor: help;
            position: relative;
        }

        .cursor-help[title]:hover::before {
            content: attr(title);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 8px 12px;
            background-color: #1f2937;
            color: #fff;
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            margin-bottom: 8px;
            pointer-events: none;
        }

        .cursor-help[title]:hover::after {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 6px solid transparent;
            border-top-color: #1f2937;
            margin-bottom: -4px;
            z-index: 1000;
        }

        /* Inline select styling */
        .inline-select {
            display: inline-block;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 4px center;
            background-size: 12px;
            padding-right: 20px !important;
            line-height: 1.2;
            height: auto;
        }

        .inline-select:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(139, 92, 246, 0.2);
        }

        .inline-select option {
            padding: 4px 8px;
            font-size: 12px;
        }

        .statusDropdown {
            font-size: 14px;
            font-weight: bold;
        }

        .priorityDropdown {
            font-size: 14px;
            font-weight: bold;
        }

        /* ===== Dark Mode: DataTables chrome ===== */
        /* DataTables renders its own controls outside Tailwind's reach, so
               these need plain CSS overrides keyed off the `dark` class on <html>.
               This build is DataTables 2 + the Bootstrap 5 integration (dt-length,
               dt-search, dt-info, dt-paging / .page-link), not the classic 1.x
               dataTables_* class names. */
        .dark #ordersTable_wrapper {
            color: #e5e7eb;
        }

        .dark #ordersTable_wrapper .dt-length select,
        .dark #ordersTable_wrapper .dt-search input {
            border-color: #374151;
            color: #e5e7eb;
            background-color: #1f2937;
        }

        .dark #ordersTable_wrapper .dt-length select:hover,
        .dark #ordersTable_wrapper .dt-length select:focus,
        .dark #ordersTable_wrapper .dt-search input:hover,
        .dark #ordersTable_wrapper .dt-search input:focus {
            border-color: #818cf8;
        }

        .dark #ordersTable_wrapper .dt-length label,
        .dark #ordersTable_wrapper .dt-search label,
        .dark #ordersTable_wrapper .dt-info {
            color: #9ca3af;
        }

        .dark #ordersTable_wrapper .dt-paging {
            background: transparent !important;
        }

        .dark #ordersTable_wrapper .dt-paging .page-link {
            background-color: #1f2937;
            border-color: #374151;
            color: #e5e7eb;
        }

        .dark #ordersTable_wrapper .dt-paging .page-link:hover {
            background-color: #374151;
            border-color: #4b5563;
            color: #f3f4f6;
        }

        .dark #ordersTable_wrapper .dt-paging .page-item.active .page-link {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: #ffffff;
        }

        .dark #ordersTable_wrapper .dt-paging .page-item.disabled .page-link {
            background-color: #111827;
            border-color: #374151;
            color: #4b5563;
        }

        .dark #ordersTable.dataTable {
            color: #e5e7eb;
            border-color: #374151;
        }

        .dark #ordersTable.dataTable tbody tr {
            background-color: #111827;
        }

        .dark #ordersTable.dataTable tbody tr:hover {
            background-color: #1f2937;
        }

        .dark #ordersTable.dataTable thead th {
            color: #9ca3af;
            border-bottom-color: #374151;
        }

        .dark #ordersTable.dataTable tbody td {
            border-top-color: #1f2937;
        }

        /* DataTables' Bootstrap integration right-aligns numeric cells via
           td.dt-type-numeric, which beats Tailwind's .text-left utility.
           Keep the Value column left-aligned with an explicit override. */
        #ordersTable td.dt-type-numeric.text-left {
            text-align: left;
        }
        .dark .inline-select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        }

        /* Progress ring: circular gauge with the percentage in the middle */
        .progress-ring {
            position: relative;
            width: 2.375rem;
            height: 2.375rem;
            flex-shrink: 0;
        }

        .progress-ring svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .progress-ring-track {
            fill: none;
            stroke: #e8edf5;
            stroke-width: 3.5;
        }

        .dark .progress-ring-track {
            stroke: #374151;
        }

        .progress-ring-value {
            fill: none;
            stroke-width: 3.5;
            stroke-linecap: round;
            transition: stroke-dashoffset 300ms ease;
        }

        .progress-ring-label {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.625rem;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.02em;
            color: #4b5563;
        }

        .dark .progress-ring-label {
            color: #d1d5db;
        }

        .progress-ring-label.is-empty {
            color: #9ca3af;
        }

        /* Progress update modal: the slider is the star, so it gets a track
           and thumb styled for both themes. */
        .progress-modal-range {
            height: 0.5rem;
            border-radius: 9999px;
            appearance: none;
            cursor: pointer;
            background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 100%);
            outline: none;
        }

        .progress-modal-range::-webkit-slider-thumb {
            appearance: none;
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 9999px;
            background: #ffffff;
            border: 3px solid #6366f1;
            box-shadow: 0 2px 6px rgba(16, 24, 40, 0.25);
            cursor: grab;
        }

        .progress-modal-range::-moz-range-thumb {
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 9999px;
            background: #ffffff;
            border: 3px solid #6366f1;
            box-shadow: 0 2px 6px rgba(16, 24, 40, 0.25);
            cursor: grab;
        }

        .progress-modal-range::-webkit-slider-runnable-track {
            border-radius: 9999px;
        }

        .progress-modal-value {
            font-variant-numeric: tabular-nums;
        }

        /* ---- Overdue accordion ----
               Severity is carried by the number and the left rail, so the block can
               be urgent without shouting in every cell. */
        .overdue {
            margin-bottom: 1.5rem;
            border: 1px solid #fecaca;
            border-radius: 1rem;
            background-color: #ffffff;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
        }

        .dark .overdue {
            background-color: #111827;
            border-color: rgba(220, 38, 38, 0.3);
            box-shadow: none;
        }

        .overdue-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.25rem;
            background-color: #fef2f2;
            border-bottom: 1px solid #fecaca;
            border-left: 4px solid #dc2626;
        }

        .dark .overdue-head {
            background-color: rgba(220, 38, 38, 0.08);
            border-bottom-color: rgba(220, 38, 38, 0.25);
        }

        .overdue-head-main {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            min-width: 0;
        }

        .overdue-count {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 2.75rem;
            height: 2.75rem;
            padding: 0 0.5rem;
            border-radius: 0.875rem;
            background-color: #dc2626;
            color: #ffffff;
            font-size: 1.375rem;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.03em;
        }

        .overdue-title {
            font-size: 1.0625rem;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.01em;
        }

        .dark .overdue-title {
            color: #f3f4f6;
        }

        .overdue-sub {
            margin-top: 0.125rem;
            font-size: 0.8125rem;
            color: #b91c1c;
        }

        .dark .overdue-sub {
            color: #fca5a5;
        }

        .overdue-collapse {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #b91c1c;
            flex-shrink: 0;
            transition: background-color 0.15s ease;
        }

        .overdue-collapse:hover {
            background-color: rgba(220, 38, 38, 0.08);
        }

        .dark .overdue-collapse {
            color: #fca5a5;
        }

        .overdue-body[hidden] {
            display: none;
        }

        .overdue-item {
            border-bottom: 1px solid #f3f4f6;
            border-left: 4px solid transparent;
        }

        .dark .overdue-item {
            border-bottom-color: #1f2937;
        }

        .overdue-item:last-child {
            border-bottom: 0;
        }

        .overdue-item.sev-1 {
            border-left-color: #fb923c;
        }

        .overdue-item.sev-2 {
            border-left-color: #ef4444;
        }

        .overdue-item.sev-3 {
            border-left-color: #991b1b;
        }

        .overdue-row {
            display: grid;
            grid-template-columns: 4rem minmax(0, 1fr) auto 7rem 1rem;
            align-items: center;
            gap: 1rem;
            width: 100%;
            padding: 0.75rem 1.25rem 0.75rem 1rem;
            text-align: left;
            transition: background-color 0.15s ease;
        }

        .overdue-row:hover {
            background-color: #fef2f2;
        }

        .dark .overdue-row:hover {
            background-color: rgba(220, 38, 38, 0.06);
        }

        .overdue-late {
            display: flex;
            flex-direction: column;
            line-height: 1.05;
        }

        .overdue-late-num {
            font-size: 1.5rem;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
            letter-spacing: -0.04em;
        }

        .sev-1 .overdue-late-num {
            color: #ea580c;
        }

        .sev-2 .overdue-late-num {
            color: #dc2626;
        }

        .sev-3 .overdue-late-num {
            color: #991b1b;
        }

        .dark .sev-1 .overdue-late-num {
            color: #fb923c;
        }

        .dark .sev-2 .overdue-late-num {
            color: #f87171;
        }

        .dark .sev-3 .overdue-late-num {
            color: #ef4444;
        }

        .overdue-late-unit {
            margin-top: 0.125rem;
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9ca3af;
        }

        .overdue-name {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .overdue-client {
            font-size: 0.875rem;
            font-weight: 700;
            color: #111827;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dark .overdue-client {
            color: #f3f4f6;
        }

        .overdue-meta {
            margin-top: 0.125rem;
            font-size: 0.75rem;
            color: #6b7280;
        }

        .dark .overdue-meta {
            color: #9ca3af;
        }

        .overdue-people {
            display: flex;
            align-items: center;
        }

        .overdue-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 1.5rem;
            height: 1.5rem;
            margin-left: -0.375rem;
            border-radius: 9999px;
            border: 2px solid #ffffff;
            background-color: #4f46e5;
            color: #ffffff;
            font-size: 0.625rem;
            font-weight: 700;
        }

        .overdue-avatar:first-child {
            margin-left: 0;
        }

        .dark .overdue-avatar {
            border-color: #111827;
        }

        .overdue-avatar.is-rest {
            background-color: #9ca3af;
        }

        .overdue-unassigned {
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            background-color: #fef3c7;
            color: #92400e;
            font-size: 0.6875rem;
            font-weight: 700;
        }

        .dark .overdue-unassigned {
            background-color: rgba(245, 158, 11, 0.15);
            color: #fcd34d;
        }

        .overdue-progress {
            position: relative;
            height: 0.375rem;
            border-radius: 9999px;
            background-color: #e5e7eb;
            overflow: hidden;
        }

        .dark .overdue-progress {
            background-color: #374151;
        }

        .overdue-progress-fill {
            position: absolute;
            inset: 0 auto 0 0;
            border-radius: 9999px;
            background-color: #6366f1;
        }

        .overdue-chevron {
            font-size: 0.75rem;
            color: #9ca3af;
            transition: transform 0.2s ease;
        }

        .overdue-row[aria-expanded='true'] .overdue-chevron {
            transform: rotate(180deg);
        }

        .overdue-detail {
            padding: 0 1.25rem 1rem 5rem;
            background-color: #fafafa;
        }

        .dark .overdue-detail {
            background-color: rgba(31, 41, 55, 0.4);
        }

        .overdue-detail[hidden] {
            display: none;
        }

        .overdue-facts {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.75rem 1.5rem;
            padding-top: 0.75rem;
        }

        .overdue-facts .is-wide {
            grid-column: 1 / -1;
        }

        .overdue-facts dt {
            font-size: 0.625rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #9ca3af;
        }

        .overdue-facts dd {
            margin-top: 0.125rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: #111827;
        }

        .dark .overdue-facts dd {
            color: #e5e7eb;
        }

        .overdue-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .overdue-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.4375rem 0.875rem;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            font-size: 0.75rem;
            font-weight: 700;
            color: #374151;
            transition: background-color 0.15s ease, border-color 0.15s ease;
        }

        .overdue-btn:hover {
            background-color: #f3f4f6;
        }

        .dark .overdue-btn {
            border-color: #374151;
            color: #d1d5db;
        }

        .dark .overdue-btn:hover {
            background-color: #1f2937;
        }

        .overdue-btn.is-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-color: #4f46e5;
            color: #ffffff;
        }

        .overdue-btn.is-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
        }

        /* Action menu styles */
        [id^="action-menu-"],
        [id^="overdue-menu-"] {
            min-width: 160px;
        }

        [id^="action-menu-"] button,
        [id^="overdue-menu-"] button {
            width: 100%;
            text-align: left;
        }
        }

        .dark .overdue-btn:hover {
            background-color: #1f2937;
        }

        .overdue-btn.is-primary {
            background-color: #dc2626;
            border-color: #dc2626;
            color: #ffffff;
        }

        .overdue-btn.is-primary:hover {
            background-color: #b91c1c;
        }

        .overdue-more {
            padding: 0.75rem 1.25rem;
            border-top: 1px solid #f3f4f6;
            font-size: 0.75rem;
            color: #6b7280;
        }

        .dark .overdue-more {
            border-top-color: #1f2937;
            color: #9ca3af;
        }

        .overdue-row:focus-visible,
        .overdue-collapse:focus-visible,
        .overdue-btn:focus-visible {
            outline: 2px solid #dc2626;
            outline-offset: -2px;
        }

        @media (max-width: 900px) {
            .overdue-row {
                grid-template-columns: 3.5rem minmax(0, 1fr) 1rem;
                gap: 0.75rem;
            }

            .overdue-people,
            .overdue-progress {
                display: none;
            }

            .overdue-detail {
                padding-left: 1.25rem;
            }

            .overdue-facts {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
@endpush

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        @php
            $currentUser = auth()->user();
            // Overdue / due-soon alerts are team-scoped: team leaders, team
            // members and stack leaders only see their own team's orders.
            // Admin, OM, PM and sales users see every team's alerts.
            $teamScoped = ! $currentUser->isSales()
                && $currentUser->hasRole(['team-leader', 'team-member', 'stack-leader'])
                && $currentUser->team_id;

            $dueSoonOrders = \App\Models\Order::with(['team', 'users'])
                ->when($teamScoped, fn ($q) => $q->where('team_id', $currentUser->team_id))
                ->whereNotIn('status', ['delivered', 'completed', 'cancelled'])
                ->whereNotNull('end_date')
                ->where('end_date', '>=', now())
                ->where('end_date', '<=', now()->addDays(4))
                ->orderBy('end_date')
                ->take(6)
                ->get();

            $urgentCount = \App\Models\Order::when($teamScoped, fn ($q) => $q->where('team_id', $currentUser->team_id))
                ->whereNotIn('status', ['delivered', 'completed', 'cancelled'])
                ->whereNotNull('end_date')
                ->where('end_date', '<=', now()->addDays(4))
                ->where('end_date', '>=', now())
                ->count();

            $overdueOrders = \App\Models\Order::with(['team', 'users'])
                ->when($teamScoped, fn ($q) => $q->where('team_id', $currentUser->team_id))
                ->whereNotIn('status', ['delivered', 'completed', 'cancelled'])
                ->whereNotNull('end_date')
                ->where('end_date', '<', now())
                ->orderBy('end_date')
                ->take(8)
                ->get();

            $overdueCount = \App\Models\Order::when($teamScoped, fn ($q) => $q->where('team_id', $currentUser->team_id))
                ->whereNotIn('status', ['delivered', 'completed', 'cancelled'])
                ->whereNotNull('end_date')
                ->where('end_date', '<', now())
                ->count();
        @endphp

        <!-- Overdue: ranked accordion, worst first -->
        @if ($overdueOrders->count() > 0)
            @php
                $worstOverdue = (int) round($overdueOrders->first()->end_date->diffInDays(now()));
                $unassignedOverdue = $overdueOrders->filter(fn($order) => $order->users->isEmpty())->count();
            @endphp

            <section class="overdue" id="overduePanel" aria-labelledby="overdueHeading">
                <header class="overdue-head">
                    <div class="overdue-head-main">
                        <span class="overdue-count" aria-hidden="true">{{ $overdueCount }}</span>
                        <div>
                            <h2 class="overdue-title" id="overdueHeading">
                                {{ $overdueCount === 1 ? 'Order is overdue' : 'Orders are overdue' }}
                            </h2>
                            <p class="overdue-sub">
                                Worst is
                                @if ($worstOverdue >= 1)
                                    {{ $worstOverdue }} {{ \Illuminate\Support\Str::plural('day', $worstOverdue) }}
                                @else
                                    less than a day
                                @endif
                                past deadline
                                @if ($unassignedOverdue > 0)
                                    , and {{ $unassignedOverdue }} {{ $unassignedOverdue === 1 ? 'has' : 'have' }} nobody
                                    assigned.
                                @endif
                            </p>
                        </div>
                    </div>

                    <button type="button" class="overdue-collapse" id="overdueCollapse" aria-expanded="false"
                        aria-controls="overdueBody">
                        <span data-collapse-label>Show</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </header>

                <div class="overdue-body" id="overdueBody" hidden>
                    <ul class="overdue-list">
                        @foreach ($overdueOrders as $overdue)
                            @php
                                $lateDays = (int) round($overdue->end_date->diffInDays(now()));
                                $lateHours = (int) round($overdue->end_date->diffInHours(now()));
                                $severity = $lateDays >= 7 ? 3 : ($lateDays >= 3 ? 2 : 1);
                                $lateLabel = $lateDays >= 1 ? $lateDays : $lateHours;
                                $lateUnit = $lateDays >= 1 ? \Illuminate\Support\Str::plural('day', $lateDays) : 'hrs';
                                $overdueProgress = max(0, min(100, (int) ($overdue->progress ?? 0)));
                            @endphp

                            <li class="overdue-item sev-{{ $severity }}">
                                <button type="button" class="overdue-row" aria-expanded="false"
                                    aria-controls="overdue-detail-{{ $overdue->id }}">
                                    <span class="overdue-late">
                                        <span class="overdue-late-num">{{ $lateLabel }}</span>
                                        <span class="overdue-late-unit">{{ $lateUnit }} late</span>
                                    </span>

                                    <span class="overdue-name">
                                        <span class="overdue-client">{{ $overdue->name }}</span>
                                        <span class="overdue-meta">
                                            {{ $overdue->team?->name ?? 'No team' }} · due
                                            {{ $overdue->end_date->format('M d') }}
                                        </span>
                                    </span>

                                    <span class="overdue-people">
                                        @if ($overdue->users->count() > 0)
                                            @foreach ($overdue->users->take(3) as $assignee)
                                                <span class="overdue-avatar"
                                                    title="{{ $assignee->name }}">{{ strtoupper(substr($assignee->name, 0, 1)) }}</span>
                                            @endforeach
                                            @if ($overdue->users->count() > 3)
                                                <span
                                                    class="overdue-avatar is-rest">+{{ $overdue->users->count() - 3 }}</span>
                                            @endif
                                        @else
                                            <span class="overdue-unassigned">Unassigned</span>
                                        @endif
                                    </span>

                                    <span class="overdue-progress" title="{{ $overdueProgress }}% complete">
                                        <span class="overdue-progress-fill" style="width: {{ $overdueProgress }}%"></span>
                                    </span>

                                    <i class="fas fa-chevron-down overdue-chevron"></i>
                                </button>

                                <div class="overdue-detail" id="overdue-detail-{{ $overdue->id }}" hidden>
                                    <dl class="overdue-facts">
                                        <div>
                                            <dt>Progress</dt>
                                            <dd>{{ $overdueProgress }}% done</dd>
                                        </div>
                                        <div>
                                            <dt>Status</dt>
                                            <dd>{{ ucfirst(str_replace('-', ' ', $overdue->status)) }}</dd>
                                        </div>
                                        <div>
                                            <dt>Deadline</dt>
                                            <dd>{{ $overdue->end_date->format('M d, Y') }}</dd>
                                        </div>
                                        <div>
                                            <dt>Value</dt>
                                            <dd>${{ number_format($overdue->actual_revenue ?? 0, 2) }}</dd>
                                        </div>
                                        <div class="is-wide">
                                            <dt>Assigned to</dt>
                                            <dd>{{ $overdue->users->pluck('name')->join(', ') ?: 'Nobody yet — assign someone to unblock this.' }}
                                            </dd>
                                        </div>
                                    </dl>

                                    <div class="overdue-actions">
                                        <a href="{{ route('orders.show', $overdue) }}" class="overdue-btn is-primary">
                                            Open order
                                        </a>
                                        <div class="relative inline-block">
                                            <button onclick="toggleOverdueActionMenu('overdue-menu-{{ $overdue->id }}')"
                                                    class="overdue-btn">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div id="overdue-menu-{{ $overdue->id }}"
                                                 class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                                <div class="py-1">
                                                    <a href="{{ route('orders.show', $overdue) }}"
                                                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                        <i class="fas fa-eye text-purple-500"></i>
                                                        View Details
                                                    </a>
                                                    @can('update', $overdue)
                                                        <a href="{{ route('orders.edit', $overdue) }}"
                                                           class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                                            <i class="fas fa-edit text-indigo-500"></i>
                                                            Edit Order
                                                        </a>
                                                    @endcan
            </div>
        </div>
    </div>

                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    @if ($overdueCount > $overdueOrders->count())
                        <p class="overdue-more">
                            {{ $overdueCount - $overdueOrders->count() }} more overdue
                            {{ \Illuminate\Support\Str::plural('order', $overdueCount - $overdueOrders->count()) }} — the
                            table below is sorted worst first.
                        </p>
                    @endif
                </div>
            </section>
        @endif

        <!-- Due Soon Alert Section -->
        @if ($dueSoonOrders->count() > 0)
            <div
                class="mb-6 bg-gradient-to-r from-red-50 via-orange-50 to-amber-50 dark:from-red-950/30 dark:via-orange-950/20 dark:to-amber-950/10 rounded-2xl shadow-lg border-2 border-red-200 dark:border-orange-900 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <div
                                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center shadow-lg animate-pulse">
                                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                                </div>
                                <span
                                    class="absolute -top-1 -right-1 w-6 h-6 bg-red-600 rounded-full flex items-center justify-center text-white text-xs font-bold shadow-md">
                                    {{ $urgentCount }}
                                </span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                    Due Soon Alert
                                    <span class="px-3 py-1 bg-red-600 text-white text-sm font-semibold rounded-full">
                                        {{ $dueSoonOrders->count() }} Order{{ $dueSoonOrders->count() > 1 ? 's' : '' }}
                                    </span>
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    <i class="fas fa-clock text-red-500 dark:text-red-400 mr-1"></i>
                                    Orders ending within 4 days - {{ $urgentCount }} urgent (within 4 days)
                                </p>
                            </div>
                        </div>
                        <button onclick="this.parentElement.parentElement.nextElementSibling.classList.toggle('hidden')"
                            class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-2">
                            <i class="fas fa-chevron-down text-lg"></i>
                        </button>
                    </div>

                    <!-- Collapsible Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($dueSoonOrders as $order)
                            @php
                                $diff = $order->end_date->diff(now());
                                $daysUntilDue = $diff->d;
                                $hoursUntilDue = $diff->h;
                                $isUrgent = $daysUntilDue <= 3;

                                // Format time remaining in human-readable format
                                if ($daysUntilDue == 0 && $order->end_date->isToday()) {
                                    $timeRemaining = 'TODAY';
                                } elseif ($daysUntilDue == 0 && $order->end_date->isTomorrow()) {
                                    $timeRemaining = 'TOMORROW';
                                } elseif ($daysUntilDue == 1) {
                                    $timeRemaining = 'TOMORROW';
                                } elseif ($daysUntilDue == 0) {
                                    // Less than a day but not today/tomorrow (edge case)
                                    $timeRemaining = $hoursUntilDue . 'h';
                                } elseif ($daysUntilDue < 1) {
                                    // Less than 24 hours
                                    $totalHours = $order->end_date->diffInHours(now());
                                    $timeRemaining = $totalHours . 'h';
                                } else {
                                    // More than a day
                                    $timeRemaining = $daysUntilDue . 'd';
                                }
                            @endphp

                            <a href="{{ route('orders.show', $order) }}"
                                class="group relative overflow-hidden rounded-xl bg-white dark:bg-gray-900 shadow-md hover:shadow-xl transition-all duration-300 border-2 {{ $isUrgent ? 'border-red-300 dark:border-red-800 hover:border-red-500 dark:hover:border-red-600' : 'border-orange-200 dark:border-orange-900 hover:border-orange-400 dark:hover:border-orange-700' }}">
                                <!-- Progress Bar Background -->
                                <div
                                    class="absolute top-0 left-0 w-full h-1 {{ $isUrgent ? 'bg-red-500' : 'bg-orange-400' }}">
                                </div>

                                <div class="p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1 min-w-0">
                                            <h4
                                                class="text-sm font-bold text-gray-900 dark:text-gray-100 truncate group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                                                {{ $order->name }}
                                            </h4>
                                            @if ($order->team)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    <i class="fas fa-users mr-1"></i>{{ $order->team->name }}
                                                </p>
                                            @endif
                                        </div>
                                        <div class="ml-3 flex-shrink-0">
                                            @if ($isUrgent)
                                                <div
                                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-md animate-pulse">
                                                    <i class="fas fa-fire text-white"></i>
                                                </div>
                                            @else
                                                <div
                                                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-400 to-amber-500 flex items-center justify-center shadow-md">
                                                    <i class="fas fa-hourglass-half text-white"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            @if ($timeRemaining == 'TODAY')
                                                <span class="px-2 py-1 bg-red-600 text-white text-xs font-bold rounded-lg">
                                                    TODAY
                                                </span>
                                            @elseif($timeRemaining == 'TOMORROW')
                                                <span class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-lg">
                                                    TOMORROW
                                                </span>
                                            @elseif($isUrgent)
                                                <span
                                                    class="px-2 py-1 bg-orange-500 text-white text-xs font-bold rounded-lg">
                                                    {{ $timeRemaining }} left
                                                </span>
                                            @else
                                                <span
                                                    class="px-2 py-1 bg-amber-500 text-white text-xs font-bold rounded-lg">
                                                    {{ $timeRemaining }} left
                                                </span>
                                            @endif
                                            <span
                                                class="text-xs text-gray-500 dark:text-gray-400">{{ $order->end_date->format('M d') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            @if ($order->users->count() > 0)
                                                <div class="flex -space-x-2">
                                                    @foreach ($order->users->take(3) as $user)
                                                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 border-2 border-white dark:border-gray-900 flex items-center justify-center text-white text-xs font-bold"
                                                            title="{{ $user->name }}">
                                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                                        </div>
                                                    @endforeach
                                                    @if ($order->users->count() > 3)
                                                        <div
                                                            class="w-6 h-6 rounded-full bg-gray-300 dark:bg-gray-600 border-2 border-white dark:border-gray-900 flex items-center justify-center text-gray-600 dark:text-gray-200 text-xs font-bold">
                                                            +{{ $order->users->count() - 3 }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400 dark:text-gray-500">Myself</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Page Header -->
        <div class="mb-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Orders</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Manage orders and track their status</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="exportOrders()"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl font-semibold transition-all shadow-lg">
                        <i class="fas fa-file-export mr-2"></i>
                        Export CSV
                    </button>
                    <a href="{{ route('orders.dashboard') }}"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all shadow-lg">
                        <i class="fas fa-chart-line mr-2"></i>
                        View Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters (One Line) -->
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-100 dark:border-gray-800 p-3 mb-4">
            <div class="flex flex-wrap items-center gap-3">
                <!-- Search Field -->
                <div class="relative flex-shrink-0 flex-grow max-w-xs">
                    <i
                        class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-sm"></i>
                    <input type="text" id="filterSearch" placeholder="Search orders..."
                        class="w-full pl-9 pr-4 py-1.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500 transition-all duration-200 text-sm">
                </div>

                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center">
                    <i class="fas fa-filter text-indigo-500 dark:text-indigo-400 mr-2"></i>
                    Filters:
                </span>

                <!-- Month Filter -->
                <div class="relative flex-shrink-0 inline-block">
                    <select id="filterMonth" name="filterMonth"
                        class="px-3 py-1.5 pr-8 border border-indigo-300 dark:border-indigo-800 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 text-gray-900 dark:text-gray-100 appearance-none transition-all duration-200 text-sm font-medium">
                        <option value="">All Time</option>
                        @foreach ($availableMonths as $value => $label)
                            <option value="{{ $value }}" {{ $selectedMonth === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-[21px] flex items-center pr-2 pointer-events-none">
                        <i class="fas fa-calendar-alt text-indigo-400 text-xs"></i>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="relative flex-shrink-0">
                    <select id="filterStatus"
                        class="px-3 py-1.5 pr-8 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 appearance-none transition-all duration-200 text-sm">
                        <option value="">All Statuses</option>
                        <option value="nra">NRA</option>
                        <option value="pending">Pending</option>
                        <option value="in-progress">In Progress</option>
                        <option value="delivered">Delivered</option>
                        <option value="completed">Completed</option>
                        <option value="on-hold">On Hold</option>
                        <option value="cancelled">Cancelled</option>
                    </select>

                </div>

                <!-- Team Filter -->
                <div class="relative flex-shrink-0">
                    <select id="filterTeam"
                        class="px-3 py-1.5 pr-8 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 appearance-none transition-all duration-200 text-sm">
                        <option value="" {{ $selectedTeamId ? '' : 'selected' }}>All Teams</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" {{ $selectedTeamId == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tech Stack Filter -->
                <div class="relative flex-shrink-0">
                    <select id="filterTechStack"
                        class="px-3 py-1.5 pr-8 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 appearance-none transition-all duration-200 text-sm">
                        <option value="">All Tech Stacks</option>
                        @foreach ($techStacks as $stack)
                            <option value="{{ $stack->id }}">{{ $stack->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Freelance Profile Filter -->
                <div class="relative flex-shrink-0">
                    <select id="filterProfile"
                        class="px-3 py-1.5 pr-8 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 appearance-none transition-all duration-200 text-sm">
                        <option value="">All Profiles</option>
                        @foreach ($profiles as $profile)
                            <option value="{{ $profile->id }}">{{ $profile->profile_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Priority Filter -->
                <div class="relative flex-shrink-0">
                    <select id="filterPriority"
                        class="px-3 py-1.5 pr-8 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 appearance-none transition-all duration-200 text-sm">
                        <option value="">All Priorities</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                @if (auth()->user()->isSales())
                    <!-- My Sales Only Filter -->
                    <label for="filterMySales"
                        class="relative flex items-center gap-2 px-3 py-1.5 border border-indigo-300 dark:border-indigo-800 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 cursor-pointer select-none text-sm font-medium text-gray-900 dark:text-gray-100 transition-all duration-200">
                        <input type="checkbox" id="filterMySales" name="filterMySales"
                            class="w-4 h-4 rounded border-indigo-400 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        <i class="fas fa-user-tie text-indigo-500 dark:text-indigo-400"></i>
                        <span>My sales only</span>
                    </label>
                @endif

                <!-- Spacer -->
                <div class="flex-1"></div>

                <!-- Action Buttons -->
                <button id="clearFilters"
                    class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-all duration-200">
                    <i class="fas fa-times mr-1"></i>
                    Clear
                </button>
                <button id="applyFilters"
                    class="px-3 py-1.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-all shadow-md">
                    <i class="fas fa-filter mr-1"></i>
                    Apply
                </button>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        <div id="bulkActions"
            class="hidden bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-500/10 dark:to-purple-500/10 rounded-xl shadow-md border-2 border-indigo-200 dark:border-indigo-900 p-4 mb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="selectAll"
                        class="w-5 h-5 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        <span id="selectedCount">0</span> orders selected
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <select id="bulkAction"
                        class="px-3 py-1.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
                        <option value="">Bulk Actions</option>
                        <option value="mark-completed">Mark as Completed</option>
                        <option value="mark-in-progress">Mark as In Progress</option>
                        <option value="mark-pending">Mark as Pending</option>
                        <option value="mark-on-hold">Mark as On Hold</option>
                        <option value="mark-cancelled">Mark as Cancelled</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button id="applyBulkAction"
                        class="px-4 py-1.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-all shadow-md">
                        <i class="fas fa-check mr-1"></i>
                        Apply
                    </button>
                    <button id="cancelSelection"
                        class="px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-all">
                        <i class="fas fa-times mr-1"></i>
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <div
            class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 datatableprimary mx-0">
            <div class="datatable-container">
                <table id="ordersTable" class="display w-full" style="width:100%;">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 40px;">
                                <input type="checkbox" id="checkAll"
                                    class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </th>
                            <th>Client</th>
                            <th>Profile</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Value</th>
                            <th>Team</th>
                            <th>Users</th>
                            {{-- <th>Started</th> --}}
                            <th>Completed</th>
                            <th>Deadline</th>
                            <th class="text-center">Progress</th>
                            {{-- <th>Special</th> --}}
                            {{-- <th>Last Update</th> --}}
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- User Assignment Modal -->
    <div id="userAssignmentModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeUserAssignmentModal()">
        </div>

        <!-- Modal Panel -->
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform  rounded-xl bg-white dark:bg-gray-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3 sm:px-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-semibold leading-6 text-white" id="modal-title">
                                <i class="fas fa-users mr-2"></i>
                                Assign Users to Order
                            </h3>
                            <button type="button" onclick="closeUserAssignmentModal()"
                                class="text-white hover:text-gray-200 transition-colors">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                        <p class="mt-1 text-sm text-indigo-100" id="modal-order-name"></p>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-4 py-4 sm:px-6">
                        <!-- Selected Users Display -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Assigned Users
                            </label>
                            <div id="modalSelectedUsersDisplay"
                                class="border border-gray-300 dark:border-gray-700 rounded-lg p-3 min-h-[60px] bg-gray-50 dark:bg-gray-800 mb-3">
                                <span class="text-gray-400 dark:text-gray-500 text-sm">No users assigned</span>
                            </div>
                        </div>

                        <!-- Search and Add Users -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Search & Add Users
                            </label>
                            <div class="relative">
                                <input type="text" id="modalUserSearchInput"
                                    placeholder="Search users by name or email..."
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500 text-sm">

                                <!-- Dropdown List -->
                                <div id="modalUserDropdown"
                                    class="hidden absolute z-50 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <!-- Users will be populated here by JavaScript -->
                                </div>
                            </div>

                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-info-circle mr-1"></i>
                                Start typing to search and add users
                            </p>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" onclick="saveUserAssignments()"
                            class="inline-flex w-full justify-center rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:from-indigo-700 hover:to-purple-700 sm:ml-3 sm:w-auto transition-all">
                            <i class="fas fa-save mr-2"></i>
                            Save Assignments
                        </button>
                        <button type="button" onclick="closeUserAssignmentModal()"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:mt-0 sm:w-auto transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Update Modal -->
    <div id="progressModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="progressModalTitle">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeProgressModal()"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end sm:items-center justify-center p-4 sm:p-0">
                <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all border border-gray-100 dark:border-gray-700">
                    <!-- Modal Header -->
                    <div class="flex items-start justify-between px-6 pt-5 pb-4 border-b border-gray-100 dark:border-gray-800">
                        <div>
                            <h3 id="progressModalTitle" class="text-lg font-bold text-gray-900 dark:text-gray-100">Update progress</h3>
                            <p id="progressModalOrderName" class="mt-0.5 text-sm text-gray-500 dark:text-gray-400"></p>
                        </div>
                        <button type="button" onclick="closeProgressModal()"
                                class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-6">
                        <div class="text-center">
                            <span id="progressModalValue" class="text-5xl font-extrabold tracking-tight text-gray-900 dark:text-white progress-modal-value">0%</span>
                            <p id="progressModalHint" class="mt-1 text-xs text-gray-500 dark:text-gray-400"></p>
                        </div>

                        <input type="range" id="progressModalRange" min="0" max="100" step="5" value="0"
                               class="progress-modal-range w-full mt-5" aria-label="Completion progress">
                        <div class="mt-1 flex justify-between text-[10px] font-bold text-gray-400 dark:text-gray-500">
                            <span>0%</span>
                            <span>25%</span>
                            <span>50%</span>
                            <span>75%</span>
                            <span>100%</span>
                        </div>

                        <div class="mt-5 flex flex-wrap items-center justify-center gap-2">
                            @foreach([0, 25, 50, 75, 100] as $preset)
                                <button type="button" data-progress-preset="{{ $preset }}"
                                        class="progress-preset-btn px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    {{ $preset }}%
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end gap-2 px-6 py-4 bg-gray-50 dark:bg-gray-800/60 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" onclick="closeProgressModal()"
                                class="inline-flex items-center rounded-lg bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-700 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-all">
                            Cancel
                        </button>
                        <button type="button" id="progressModalSaveBtn" onclick="saveProgressModal()"
                                class="inline-flex items-center rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:from-indigo-700 hover:to-purple-700 transition-all">
                            <i class="fas fa-check mr-2"></i>Save progress
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            @php($canManageAssignment = ! auth()->user()->isSales())
            @php($canUpdateProgress = ! auth()->user()->isSales())
            const canManageAssignment = @json($canManageAssignment);
            const canUpdateProgress = @json($canUpdateProgress);

            // --- Overdue accordion ---
            document.addEventListener('DOMContentLoaded', function() {
                const panel = document.getElementById('overduePanel');

                if (!panel) {
                    return;
                }

                const body = document.getElementById('overdueBody');
                const collapse = document.getElementById('overdueCollapse');
                const STORAGE_KEY = 'pmt.orders.overdueCollapsed';

                function setCollapsed(collapsed) {
                    body.hidden = collapsed;
                    collapse.setAttribute('aria-expanded', String(!collapsed));
                    collapse.querySelector('[data-collapse-label]').textContent = collapsed ? 'Show' : 'Hide';
                    collapse.querySelector('i').className = collapsed ? 'fas fa-chevron-down' : 'fas fa-chevron-up';
                }

                // Default to collapsed on first visit, then keep it the way it was left.
                const storedState = localStorage.getItem(STORAGE_KEY);
                const initialState = storedState === null ? true : storedState === '1';
                setCollapsed(initialState);

                collapse.addEventListener('click', function() {
                    const collapsed = !body.hidden;
                    setCollapsed(collapsed);
                    localStorage.setItem(STORAGE_KEY, collapsed ? '1' : '0');
                });

                // One row open at a time, so the block stays scannable.
                panel.querySelectorAll('.overdue-row').forEach(row => {
                    row.addEventListener('click', function() {
                        const detail = document.getElementById(this.getAttribute('aria-controls'));
                        const willOpen = detail.hidden;

                        panel.querySelectorAll('.overdue-row').forEach(other => {
                            other.setAttribute('aria-expanded', 'false');
                            document.getElementById(other.getAttribute('aria-controls'))
                                .hidden = true;
                        });

                        if (willOpen) {
                            this.setAttribute('aria-expanded', 'true');
                            detail.hidden = false;
                        }
                    });
                });
            });

            // DataTables initialization matching users module
            const ROUTES = {
                destroy: @js(route('orders.destroy', ['order' => '__ID__'])),
                quickUpdate: @js(route('orders.quick-update', ['order' => '__ID__'])),
                users: @js(route('orders.users', ['order' => '__ID__'])),
                searchUsers: @js(route('orders.search-users')),
                assignUsers: @js(route('orders.assign-users', ['order' => '__ID__'])),
            };

            document.addEventListener('DOMContentLoaded', function() {
                console.log('Initializing orders DataTable...');
                console.log('Total users loaded for modal:', {{ $users->count() }});

                initializeDataTable();
            });

            function initializeDataTable() {
                createDataTable();
            }

            function createDataTable() {
                console.log('Creating DataTable...');

                // Define URL templates
                const urlTemplates = {
                    show: @js(route('orders.show', ['order' => '__ID__'])).replace('__ID__', ''),
                    edit: @js(route('orders.edit', ['order' => '__ID__'])).replace('/__ID__/edit', ''),
                };
                @php($canDistributeValue = ! auth()->user()->isSales() && auth()->user()->hasRole(['admin', 'operation-manager', 'project-manager', 'team-leader']))
                const valueDistributionUrl = @json($canDistributeValue ? route('value-distribution.index') : null);

                // Initialize DataTable
                const table = $('#ordersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('orders.data') }}',
                        type: 'GET',
                        data: function(d) {
                            // Add filter parameters
                            d.search = $('#filterSearch').val();
                            d.status = $('#filterStatus').val();
                            d.team_id = $('#filterTeam').val();
                            d.tech_stack_id = $('#filterTechStack').val();
                            d.freelance_profile_id = $('#filterProfile').val();
                            d.priority = $('#filterPriority').val();
                            d.month = $('#filterMonth').val();
                            d.my_sales = $('#filterMySales').is(':checked') ? '1' : '';
                        }
                    },
                    columns: [{
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data) {
                                return `<input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 cursor-pointer" data-id="${data.id}">`;
                            }
                        },
                        {
                            data: 'name',
                            name: 'name',
                            render: function(data, type, row) {
                                const mineBadge = row.is_my_sale
                                    ? '<span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-indigo-100 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400">Your sale</span>'
                                    : '';
                                return `<a href="${urlTemplates.show}${row.key}" class="font-semibold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">${data}</a>${mineBadge}`;
                            }
                        },
                        {
                            data: 'freelance_profile',
                            name: 'freelance_profile_id',
                            render: function(data) {
                                if (!data || data === 'N/A')
                                    return '<span class="text-gray-400 dark:text-gray-500 text-xs">No profile</span>';
                                return `<span class="px-2 py-1 rounded-lg text-xs font-semibold bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400">@${data}</span>`;
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            render: function(data, type, row) {
                                const statusConfig = {
                                    'nra': {
                                        color: 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300',
                                        label: 'NRA'
                                    },
                                    'pending': {
                                        color: 'bg-yellow-100 dark:bg-yellow-500/10 text-yellow-800 dark:text-yellow-400',
                                        label: 'Pending'
                                    },
                                    'in-progress': {
                                        color: 'bg-blue-100 dark:bg-blue-500/10 text-blue-800 dark:text-blue-400',
                                        label: 'Progress'
                                    },
                                    'delivered': {
                                        color: 'bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400',
                                        label: 'Delivered'
                                    },
                                    'completed': {
                                        color: 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-400',
                                        label: 'Completed'
                                    },
                                    'on-hold': {
                                        color: 'bg-orange-100 dark:bg-orange-500/10 text-orange-800 dark:text-orange-400',
                                        label: 'Hold'
                                    },
                                    'cancelled': {
                                        color: 'bg-red-100 dark:bg-red-500/10 text-red-800 dark:text-red-400',
                                        label: 'Cancelled'
                                    }
                                };
                                const config = statusConfig[data] || statusConfig['nra'];

                                return `
                                    <select class="inline-select px-2 py-1 rounded text-xs font-semibold ${config.color} border-0 cursor-pointer focus:ring-1 focus:ring-offset-1 focus:ring-purple-500 min-w-[100px] max-w-[115px] statusDropdown"
                                            data-order-id="${row.id}" data-order-key="${row.key}"
                                            data-field="status"
                                            data-original-value="${data}"
                                            onchange="handleQuickUpdate(this)">
                                        <option value="nra" ${data === 'nra' ? 'selected' : ''}> NRA</option>
                                        <option value="pending" ${data === 'pending' ? 'selected' : ''}> Pending</option>
                                        <option value="in-progress" ${data === 'in-progress' ? 'selected' : ''}> Progress</option>
                                        <option value="delivered" ${data === 'delivered' ? 'selected' : ''}> Delivered</option>
                                        <option value="completed" ${data === 'completed' ? 'selected' : ''}> Completed</option>
                                        <option value="on-hold" ${data === 'on-hold' ? 'selected' : ''}>Hold</option>
                                        <option value="cancelled" ${data === 'cancelled' ? 'selected' : ''}> Cancelled</option>
                                    </select>
                                `;
                            }
                        },
                        {
                            data: 'priority',
                            name: 'priority',
                            render: function(data, type, row) {
                                const config = {
                                    'low': {
                                        color: 'bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400',
                                        label: 'Low'
                                    },
                                    'medium': {
                                        color: 'bg-yellow-100 dark:bg-yellow-500/10 text-yellow-800 dark:text-yellow-400',
                                        label: 'Medium'
                                    },
                                    'high': {
                                        color: 'bg-red-100 dark:bg-red-500/10 text-red-800 dark:text-red-400',
                                        label: 'High'
                                    }
                                };
                                const p = config[data] || config['medium'];

                                return `
                                    <select class="inline-select px-2 py-1 rounded text-xs font-semibold ${p.color} border-0 cursor-pointer focus:ring-1 focus:ring-offset-1 focus:ring-purple-500 min-w-[90px] max-w-[105px] priorityDropdown"
                                            data-order-id="${row.id}" data-order-key="${row.key}"
                                            data-field="priority"
                                            data-original-value="${data}"
                                            onchange="handleQuickUpdate(this)">
                                        <option value="low" ${data === 'low' ? 'selected' : ''}> Low</option>
                                        <option value="medium" ${data === 'medium' ? 'selected' : ''}> Medium</option>
                                        <option value="high" ${data === 'high' ? 'selected' : ''}> High</option>
                                    </select>
                                `;
                            }
                        },
                        {
                            data: 'actual_revenue',
                            name: 'actual_revenue',
                            className: 'text-left',
                            render: function(data) {
                                return data ?
                                    '<span class="font-semibold text-green-600 dark:text-green-400 text-start">$' +
                                    parseFloat(data)
                                    .toFixed(2) + '</span>' :
                                    '<span class="text-gray-400 dark:text-gray-500">N/A</span>';
                            }
                        },
                        {
                            data: 'team',
                            name: 'team_id',
                            render: function(data) {
                                if (!data || data === 'N/A')
                                    return '<span class="text-gray-400 dark:text-gray-500 text-xs">No team</span>';
                                return `<span class="px-2 py-1 rounded-lg text-xs font-semibold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 inline-block whitespace-nowrap">${data}</span>`;
                            }
                        },

                        {
                            data: 'users',
                            name: 'users',
                            render: function(data, type, row) {
                                if (!canManageAssignment) {
                                    if (!data || data.length === 0) {
                                        return '<span class="text-gray-400 dark:text-gray-500 text-xs">No users</span>';
                                    }
                                    const assignedNames = data.map(u => u.name).join(', ');
                                    return `<span class="px-2 py-1 rounded-lg text-xs font-semibold bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 truncate max-w-[140px] inline-block" title="${assignedNames}">${assignedNames}</span>`;
                                }
                                if (!data || data.length === 0) {
                                    return `<button onclick="openUserAssignmentModal('${row.key}', '${row.name.replace(/'/g, "\\'")}')" class="w-9 h-8 flex items-center justify-center rounded-lg text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-all cursor-pointer" title="Assign Users">
                                    <i class="fas fa-user-plus "></i>
                                </button>`;
                                }
                                if (data.length === 1) {
                                    return `<button onclick="openUserAssignmentModal('${row.key}', '${row.name.replace(/'/g, "\\'")}')" class="px-2 py-1 rounded-lg text-xs font-semibold bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 truncate max-w-[100px] inline-block hover:bg-purple-100 dark:hover:bg-purple-500/20 cursor-pointer transition-all" title="${data[0].name}">${data[0].name}</button>`;
                                }
                                // Multiple users - show icon with count
                                return `<button onclick="openUserAssignmentModal('${row.key}', '${row.name.replace(/'/g, "\\'")}')" class="w-12 h-8 px-2 flex items-center justify-center rounded-lg text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-500/10 hover:bg-purple-100 dark:hover:bg-purple-500/20 transition-all cursor-pointer" title="${data.length} users: ${row.users_names}">
                                <i class="fas fa-users "></i>
                                <span class="ml-0.5  font-bold">${data.length}</span>
                            </button>`;
                            }
                        },

                        /* {
                             data: 'start_date',
                             name: 'start_date',
                             render: function(data, type, row) {
                                 // For orders without start date, show clickable placeholder
                                 if (!data) {
                                     return `<input type="text"
                                         class="start-date-input px-2 py-1 rounded-lg text-xs font-medium border border-gray-300 bg-gray-50 text-gray-400 whitespace-nowrap w-28 cursor-pointer"
                                         data-order-id="${row.id}" data-order-key="${row.key}"
                                         data-original-value=""
                                         placeholder="Set date"
                                         readonly onclick="handleStartDateClick(this)">`;
                                 }

                                 const date = new Date(data);
                                 const formattedDate = date.toLocaleDateString('en-US', {
                                     year: 'numeric',
                                     month: 'short',
                                     day: 'numeric'
                                 });
                                 const dbDate = data; // Keep original format for database

                                 return `<input type="text"
                                     class="start-date-input px-2 py-1 rounded-lg text-xs font-medium border border-indigo-300 bg-indigo-50 text-indigo-700 whitespace-nowrap w-28 cursor-pointer"
                                     value="${formattedDate}"
                                     data-order-id="${row.id}" data-order-key="${row.key}"
                                     data-original-value="${dbDate}"
                                     data-current-value="${dbDate}"
                                     title="${dbDate}"
                                     readonly onclick="handleStartDateClick(this)">`;
                             }
                         },*/
                        {
                            data: 'completed_at',
                            name: 'completed_at',
                            render: function(data, type, row) {
                                if (data) {
                                    const formattedDate = new Date(data).toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric'
                                    });

                                    return `<input type="text"
                                                class="completed-date-input px-2 py-1 rounded-lg text-xs font-medium border border-green-300 dark:border-green-800 bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 whitespace-nowrap w-28 cursor-pointer"
                                                value="${formattedDate}"
                                                data-order-id="${row.id}" data-order-key="${row.key}"
                                                data-original-value="${data}"
                                                title="${data}"
                                                readonly onclick="handleCompletedDateClick(this)">`;
                                }

                                return `<input type="text"
                                            class="completed-date-input px-2 py-1 rounded-lg text-xs font-medium border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-400 dark:text-gray-500 whitespace-nowrap w-28 cursor-pointer"
                                            data-order-id="${row.id}" data-order-key="${row.key}"
                                            data-original-value=""
                                            placeholder="Set date"
                                            readonly onclick="handleCompletedDateClick(this)">`;
                            }
                        },
                        {
                            data: 'end_date',
                            name: 'end_date',
                            render: function(data, type, row) {
                                // Check if order is completed
                                if (row.status === 'delivered') {
                                    return '<span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 dark:bg-green-500/10 text-green-600 dark:text-green-400"><i class="fas fa-check"></i></span>';
                                }
                                if (row.status === 'completed') {
                                    if (row.needs_value_distribution) {
                                        const badge = `<span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400" title="Completed — value not distributed yet">
                                                <i class="fas fa-triangle-exclamation"></i>
                                            </span>`;
                                        return valueDistributionUrl
                                            ? `<a href="${valueDistributionUrl}" class="hover:opacity-80 transition-opacity">${badge}</a>`
                                            : badge;
                                    }
                                    return '<span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-green-100 dark:bg-green-500/10 text-green-600 dark:text-green-400" title="Completed — value distributed"><i class="fas fa-check"></i></span>';
                                }
                                if (row.status === 'cancelled') {
                                    return '<span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400"><i class="fas fa-times"></i></span>';
                                }

                                if (!data) {
                                    return `<span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium text-gray-400 dark:text-gray-500 whitespace-nowrap cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                                                data-order-id="${row.id}" data-order-key="${row.key}"
                                                data-deadline=""
                                                onclick="handleEndDateClick(this)">
                                                <i class="fas fa-calendar-plus text-xs"></i>
                                                <span>Set deadline</span>
                                            </span>`;
                                }

                                return `<span class="countdown-timer inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-bold whitespace-nowrap cursor-pointer hover:ring-2 hover:ring-orange-300 transition-all"
                                            data-deadline="${data}"
                                            data-order-id="${row.id}" data-order-key="${row.key}"
                                            data-original-value="${data}"
                                            onclick="handleEndDateClick(this)"
                                            title="Click to edit deadline">
                                            <i class="fas fa-clock text-xs"></i>
                                            <span class="countdown-text">Loading...</span>
                                        </span>`;
                            }
                        },
                        {
                            data: 'progress',
                            name: 'progress',
                            orderable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                const progress = Math.max(0, Math.min(100, parseInt(data) || 0));
                                const circumference = 2 * Math.PI * 15.5;
                                const offset = circumference * (1 - progress / 100);
                                const stroke = progress >= 100 ? '#10b981' : '#6366f1';
                                const documents = parseInt(row.documents_count) || 0;
                                const documentsBadge = documents > 0 ?
                                    `<a href="${urlTemplates.show}${row.key}" class="inline-flex items-center gap-0.5 text-[10px] font-semibold text-blue-500 dark:text-blue-400 hover:underline" title="${documents} document(s)">
                                        <i class="fas fa-paperclip text-[9px]"></i>${documents}
                                     </a>` : '';
                                const ringClass = canUpdateProgress ? 'progress-ring cursor-pointer' : 'progress-ring';
                                const ringTitle = canUpdateProgress ? `${progress}% complete — click to update` : `${progress}% complete`;
                                const ringClick = canUpdateProgress ? ` data-order-id="${row.id}" data-order-key="${row.key}" data-progress="${progress}" data-order-name="${row.name}" onclick="handleProgressClick(this)"` : '';

                                return `
                                    <div class="flex items-center justify-center gap-1.5">
                                        <div class="${ringClass}" title="${ringTitle}"${ringClick}>
                                            <svg viewBox="0 0 36 36">
                                                <circle class="progress-ring-track" cx="18" cy="18" r="15.5"></circle>
                                                <circle class="progress-ring-value" cx="18" cy="18" r="15.5"
                                                        stroke="${stroke}"
                                                        stroke-dasharray="${circumference.toFixed(2)}"
                                                        stroke-dashoffset="${offset.toFixed(2)}"></circle>
                                            </svg>
                                            <span class="progress-ring-label ${progress === 0 ? 'is-empty' : ''}">${progress}%</span>
                                        </div>
                                        ${documentsBadge}
                                    </div>
                                `;
                            }
                        },
                        // Special Status Column - Temporarily disabled
                        // {
                        //     data: 'special_status',
                        //     name: 'special_status',
                        //     render: function(data, type, row) {
                        //         const statusConfig = {
                        //             'selected_for_delivery': {
                        //                 color: 'bg-green-100 text-green-800',
                        //                 label: 'Sel'
                        //             },
                        //             'continue_for_next': {
                        //                 color: 'bg-blue-100 text-blue-800',
                        //                 label: 'Cont'
                        //             },
                        //             'stuck_order': {
                        //                 color: 'bg-red-100 text-red-800',
                        //                 label: 'Stuck'
                        //             }
                        //         };

                        //         const currentStatus = data || '';
                        //         const config = statusConfig[currentStatus];
                        //         const bgClass = config ? config.color : 'bg-gray-100 text-gray-800';

                        //         return `
                //             <select class="inline-select px-2 py-1 rounded text-xs font-semibold border-0 cursor-pointer focus:ring-1 focus:ring-offset-1 focus:ring-purple-500 min-w-[70px] max-w-[80px] specialStatusDropdown ${bgClass}"
                //                     data-order-id="${row.id}" data-order-key="${row.key}"
                //                     data-field="special_status"
                //                     data-original-value="${currentStatus}"
                //                     onchange="handleQuickUpdate(this)">
                //                 <option value="" ${!data ? 'selected' : ''}> None</option>
                //                 <option value="selected_for_delivery" ${data === 'selected_for_delivery' ? 'selected' : ''}> Sel</option>
                //                 <option value="continue_for_next" ${data === 'continue_for_next' ? 'selected' : ''}> Cont</option>
                //                 <option value="stuck_order" ${data === 'stuck_order' ? 'selected' : ''}> Stuck</option>
                //             </select>
                //         `;
                        //     }
                        // },
                        // Last Update Date Column - Temporarily disabled
                        // {
                        //     data: 'updated_at',
                        //     name: 'updated_at',
                        //     render: function(data, type, row) {
                        //         // Has a saved date — show it regardless
                        //         if (row.last_update_date) {
                        //             const date = new Date(row.last_update_date);
                        //             const formattedDate = date.toLocaleDateString('en-US', {
                        //                 month: 'short',
                        //                 day: 'numeric'
                        //             });
                        //             const dbDate = row.last_update_date;

                        //             return `<input type="text"
                //                         class="last-update-date-input px-2 py-1 rounded-lg text-xs font-medium border border-purple-300 bg-purple-50 text-purple-700 whitespace-nowrap w-28 cursor-pointer"
                //                         value="${formattedDate}"
                //                         data-order-id="${row.id}" data-order-key="${row.key}"
                //                         data-original-value="${dbDate}"
                //                         data-current-value="${dbDate}"
                //                         title="${dbDate}"
                //                         readonly onclick="handleLastUpdateClick(this)">`;
                        //         }

                        //         // No date set yet
                        //         return `<input type="text"
                //                     class="last-update-date-input px-2 py-1 rounded-lg text-xs font-medium border border-gray-300 bg-gray-50 text-gray-400 whitespace-nowrap w-28 cursor-pointer"
                //                     data-order-id="${row.id}" data-order-key="${row.key}"
                //                     data-original-value=""
                //                     placeholder="Set date"
                //                     readonly onclick="handleLastUpdateClick(this)">`;
                        //     }
                        // },
                        {
                            data: null,
                            orderable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                return `
                        <div class="relative inline-block">
                            <button onclick="toggleActionMenu('action-menu-${row.id}')"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition-all"
                                    title="Actions">
                                <i class="fas fa-ellipsis-v text-xs"></i>
                            </button>
                            <div id="action-menu-${row.id}"
                                 class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                <div class="py-1">
                                    <a href="${urlTemplates.show}${row.key}"
                                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-eye text-purple-500"></i>
                                        View
                                    </a>
                                    <a href="${urlTemplates.edit}/${row.key}/edit"
                                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-edit text-indigo-500"></i>
                                        Edit
                                    </a>
                                    <button onclick="deleteOrder('${row.key}')"
                                            class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                            }
                        }
                    ],
                    order: [],
                    createdRow: function(row, data) {
                        if (data.is_my_sale) {
                            $(row).addClass('bg-indigo-50/60 dark:bg-indigo-500/5');
                        }
                    },
                    pageLength: 20,
                    lengthMenu: [
                        [20, 25, 50, -1],
                        [20, 25, 50, "All"]
                    ],
                    responsive: true,
                    language: {
                        processing: '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...',
                        emptyTable: 'No orders found',
                        zeroRecords: 'No matching orders found',
                        lengthMenu: 'Show _MENU_ entries',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                        infoEmpty: 'Showing 0 to 0 of 0 entries',
                        infoFiltered: '(filtered from _MAX_ total entries)',
                        search: 'Search:',
                        paginate: {
                            first: 'First',
                            last: 'Last',
                            next: 'Next',
                            previous: 'Previous'
                        }
                    },
                    dom: 'lfrtip',
                    initComplete: function() {
                        console.log('✅ DataTable initialized successfully');

                        // Add action buttons above the table
                        const actionButtons = `
                                                <div class="flex flex-wrap items-center justify-end gap-2 mb-4 createbutton">
                                                    <a href="{{ route('orders.create') }}"
                                                        class="inline-flex items-center px-3 py-1.5 h-8 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105 whitespace-nowrap">
                                                        <i class="fas fa-plus mr-1.5 text-xs"></i>
                                                        Add New Order
                                                    </a>
                                                </div>
                                            `;

                        // Insert buttons before the table container
                        $('#ordersTable_wrapper').prepend(actionButtons);

                        // Filter form handlers - real-time filter changes
                        $('#filterSearch').on('keyup', function() {
                            table.ajax.reload();
                        });

                        $('#filterStatus, #filterTeam, #filterTechStack, #filterPriority, #filterMonth, #filterMySales').on(
                            'change',
                            function() {
                                console.log($('#filterMonth').val());
                                table.ajax.reload();
                            });

                        // Initialize countdown timers
                        updateAllCountdowns();

                        // Update countdowns every second for real-time display
                        setInterval(updateAllCountdowns, 1000);
                    },
                    drawCallback: function(settings) {
                        // Update countdowns after table redraw
                        updateAllCountdowns();
                    }
                });
            }

            // Update all countdown timers
            function updateAllCountdowns() {
                $('.countdown-timer').each(function() {
                    const $timer = $(this);
                    const deadline = new Date($timer.data('deadline'));
                    const now = new Date();
                    const diff = deadline - now;

                    if (diff <= 0) {
                        $timer.removeClass(
                                'bg-green-100 text-green-700 bg-yellow-100 text-yellow-700 bg-blue-100 text-blue-700')
                            .addClass('bg-red-100 text-red-700');
                        $timer.find('.countdown-text').text('⚠️ Overdue');
                        return;
                    }

                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    let countdown = '';
                    if (days > 0) {
                        countdown +=
                            `<span class="countdown-unit">${days}<small class="text-xs opacity-75">d</small></span> `;
                    }
                    countdown +=
                        `<span class="countdown-unit">${hours}<small class="text-xs opacity-75">h</small></span> `;
                    countdown +=
                        `<span class="countdown-unit">${minutes}<small class="text-xs opacity-75">m</small></span> `;
                    countdown +=
                        `<span class="countdown-unit">${seconds}<small class="text-xs opacity-75">s</small></span>`;

                    let colorClass = 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400';
                    if (days <= 1) {
                        colorClass = 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 animate-pulse';
                    } else if (days <= 4) {
                        colorClass = 'bg-yellow-100 dark:bg-yellow-500/10 text-yellow-800 dark:text-yellow-400';
                    } else if (days <= 30) {
                        colorClass = 'bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400';
                    }

                    $timer.removeClass(
                            'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400 bg-yellow-100 dark:bg-yellow-500/10 text-yellow-800 dark:text-yellow-400 bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 animate-pulse'
                        )
                        .addClass(colorClass);
                    $timer.find('.countdown-text').html(countdown);
                });
            }

            // Apply filters button - wait for document ready
            document.addEventListener('DOMContentLoaded', function() {
                // Check if jQuery is loaded
                if (typeof $ !== 'undefined') {
                    $('#applyFilters').on('click', function() {
                        $('#ordersTable').DataTable().ajax.reload();
                    });

                    $('#clearFilters').on('click', function() {
                        $('#filterStatus').val('');
                        $('#filterTeam').val('');
                        $('#filterTechStack').val('');
                        $('#filterPriority').val('');
                        $('#filterMonth').val('');
                        $('#filterMySales').prop('checked', false);
                        $('#ordersTable').DataTable().ajax.reload();
                    });

                    // Bulk Actions
                    let selectedIds = [];

                    // Check all functionality
                    $('#checkAll').on('change', function() {
                        const isChecked = $(this).prop('checked');
                        $('.row-checkbox').prop('checked', isChecked).trigger('change');
                    });

                    $('#selectAll').on('change', function() {
                        const isChecked = $(this).prop('checked');
                        $('#checkAll').prop('checked', isChecked);
                        $('.row-checkbox').prop('checked', isChecked).trigger('change');
                    });

                    // Individual checkbox change
                    $(document).on('change', '.row-checkbox', function() {
                        const id = $(this).data('id');

                        if ($(this).prop('checked')) {
                            if (!selectedIds.includes(id)) {
                                selectedIds.push(id);
                            }
                        } else {
                            selectedIds = selectedIds.filter(item => item !== id);
                        }

                        updateBulkActionsUI();
                    });

                    // Update bulk actions UI
                    function updateBulkActionsUI() {
                        const count = selectedIds.length;
                        $('#selectedCount').text(count);

                        if (count > 0) {
                            $('#bulkActions').removeClass('hidden');
                        } else {
                            $('#bulkActions').addClass('hidden');
                        }

                        // Update check all checkbox state
                        const totalCheckboxes = $('.row-checkbox').length;
                        const checkedCheckboxes = $('.row-checkbox:checked').length;
                        $('#checkAll, #selectAll').prop('checked', totalCheckboxes > 0 && totalCheckboxes ===
                            checkedCheckboxes);
                    }

                    // Cancel selection
                    $('#cancelSelection').on('click', function() {
                        selectedIds = [];
                        $('.row-checkbox').prop('checked', false);
                        $('#checkAll, #selectAll').prop('checked', false);
                        $('#bulkActions').addClass('hidden');
                    });

                    // Apply bulk action
                    $('#applyBulkAction').on('click', function() {
                        const action = $('#bulkAction').val();

                        if (!action) {
                            Swal.fire({
                                title: 'Select Action',
                                text: 'Please select a bulk action to perform.',
                                icon: 'warning',
                                confirmButtonColor: '#9333ea'
                            });
                            return;
                        }

                        if (selectedIds.length === 0) {
                            Swal.fire({
                                title: 'No Selection',
                                text: 'Please select at least one order.',
                                icon: 'warning',
                                confirmButtonColor: '#9333ea'
                            });
                            return;
                        }

                        if (action === 'delete') {
                            Swal.fire({
                                title: 'Delete Selected Orders?',
                                text: `Are you sure you want to delete ${selectedIds.length} order(s)? This action cannot be undone.`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#dc2626',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'Yes, delete them!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    performBulkAction(action);
                                }
                            });
                        } else {
                            const actionNames = {
                                'mark-completed': 'Completed',
                                'mark-in-progress': 'In Progress',
                                'mark-pending': 'Pending',
                                'mark-on-hold': 'On Hold',
                                'mark-cancelled': 'Cancelled'
                            };

                            Swal.fire({
                                title: 'Update Status?',
                                text: `Mark ${selectedIds.length} order(s) as ${actionNames[action]}?`,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#9333ea',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'Yes, update!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    performBulkAction(action);
                                }
                            });
                        }
                    });

                    // Perform bulk action
                    function performBulkAction(action) {
                        Swal.showLoading();

                        const data = {
                            ids: selectedIds,
                            action: action
                        };

                        axios.post('{{ route('orders.bulk-action') }}', data)
                            .then(response => {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.data.message || 'Bulk action completed successfully.',
                                    icon: 'success',
                                    confirmButtonColor: '#9333ea'
                                }).then(() => {
                                    selectedIds = [];
                                    $('#checkAll, #selectAll').prop('checked', false);
                                    $('#bulkActions').addClass('hidden');
                                    $('#ordersTable').DataTable().ajax.reload();
                                });
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error!',
                                    text: error.response?.data?.message || 'Something went wrong.',
                                    icon: 'error',
                                    confirmButtonColor: '#9333ea'
                                });
                            });
                    }

                    // Re-bind checkboxes after DataTable reload
                    $('#ordersTable').on('draw.dt', function() {
                        selectedIds = [];
                        $('#checkAll, #selectAll').prop('checked', false);
                        $('#bulkActions').addClass('hidden');
                    });
                }
            });

            // Delete order function
            // Toggle action menu
            function toggleActionMenu(menuId) {
                toggleMenu(menuId);
            }

            // Toggle overdue action menu
            function toggleOverdueActionMenu(menuId) {
                toggleMenu(menuId);
            }

            // Generic menu toggle function
            function toggleMenu(menuId) {
                // Close all other menus first
                const allMenus = document.querySelectorAll('[id^="action-menu-"], [id^="overdue-menu-"]');
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
                const isMenuButton = event.target.closest('[onclick^="toggleActionMenu"]');
                const isMenuContent = event.target.closest('[id^="action-menu-"]');

                if (!isMenuButton && !isMenuContent) {
                    const allMenus = document.querySelectorAll('[id^="action-menu-"]');
                    allMenus.forEach(menu => menu.classList.add('hidden'));
                }
            });

            function deleteOrder(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.showLoading();
                        axios.delete(ROUTES.destroy.replace('__ID__', id))
                            .then(response => {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: response.data.message || 'Order has been deleted.',
                                    icon: 'success',
                                    confirmButtonColor: '#9333ea'
                                }).then(() => {
                                    $('#ordersTable').DataTable().ajax.reload();
                                });
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error!',
                                    text: error.response?.data?.message || 'Something went wrong.',
                                    icon: 'error',
                                    confirmButtonColor: '#dc2626'
                                });
                            });
                    }
                });
            }

            // Quick update function for status and priority
            function handleQuickUpdate(selectElement) {
                const orderId = selectElement.dataset.orderKey;
                const field = selectElement.dataset.field;
                const newValue = selectElement.value;
                const originalValue = selectElement.dataset.originalValue;

                const fieldLabels = {
                    'status': 'Status',
                    'priority': 'Priority',
                    'special_status': 'Special Status',
                    'last_update_date': 'Last Update Date'
                };

                const valueLabels = {
                    'nra': 'NRA',
                    'pending': 'Pending',
                    'in-progress': 'In Progress',
                    'delivered': 'Delivered',
                    'completed': 'Completed',
                    'on-hold': 'On Hold',
                    'cancelled': 'Cancelled',
                    'low': 'Low',
                    'medium': 'Medium',
                    'high': 'High',
                    '': 'None',
                    'selected_for_delivery': 'Selected for delivery',
                    'continue_for_next': 'Continue for next',
                    'stuck_order': 'Stuck order'
                };

                Swal.fire({
                    title: 'Update Order?',
                    text: `Change ${fieldLabels[field]} to "${valueLabels[newValue]}"?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#9333ea',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, update!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.showLoading();

                        const formData = new FormData();
                        formData.append(field, newValue);

                        axios.post(ROUTES.quickUpdate.replace('__ID__', orderId), formData)
                            .then(response => {
                                Swal.fire({
                                    title: 'Updated!',
                                    text: response.data.message || 'Order updated successfully.',
                                    icon: 'success',
                                    confirmButtonColor: '#9333ea',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    $('#ordersTable').DataTable().ajax.reload();
                                });
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error!',
                                    text: error.response?.data?.message || 'Something went wrong.',
                                    icon: 'error',
                                    confirmButtonColor: '#dc2626'
                                }).then(() => {
                                    // Revert the select to its original value on error
                                    selectElement.value = originalValue;
                                });
                            });
                    } else {
                        // User cancelled, revert the select to original value
                        selectElement.value = originalValue;
                    }
                });
            }

            function handleProgressClick(element) {
                const modal = document.getElementById('progressModal');
                const range = document.getElementById('progressModalRange');
                const valueLabel = document.getElementById('progressModalValue');
                const orderName = document.getElementById('progressModalOrderName');
                const hint = document.getElementById('progressModalHint');

                progressModalState = {
                    orderId: element.dataset.orderId,
                    key: element.dataset.orderKey,
                    original: parseInt(element.dataset.progress) || 0,
                };

                orderName.textContent = (element.dataset.orderName || 'Order') + ' · ' + progressModalState.orderId;
                range.value = progressModalState.original;
                syncProgressModal();
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                setTimeout(() => range.focus(), 50);
            }

            let progressModalState = { orderId: null, original: 0 };

            function closeProgressModal() {
                const modal = document.getElementById('progressModal');
                if (modal) {
                    modal.classList.add('hidden');
                }
                document.body.style.overflow = '';
            }

            function syncProgressModal() {
                const range = document.getElementById('progressModalRange');
                const valueLabel = document.getElementById('progressModalValue');
                const hint = document.getElementById('progressModalHint');
                if (!range || !valueLabel) {
                    return;
                }

                const value = parseInt(range.value) || 0;
                const dark = document.documentElement.classList.contains('dark');
                const trackColor = dark ? '#374151' : '#e5e7eb';
                range.style.background = `linear-gradient(90deg, #6366f1 0%, #8b5cf6 ${value}%, ${trackColor} ${value}%, ${trackColor} 100%)`;
                valueLabel.textContent = value + '%';

                if (value === progressModalState.original) {
                    hint.textContent = 'Keep the current progress';
                } else if (value > progressModalState.original) {
                    hint.textContent = value - progressModalState.original + '% further along than before';
                } else {
                    hint.textContent = 'Lowered by ' + (progressModalState.original - value) + '%';
                }
            }

            function saveProgressModal() {
                const saveBtn = document.getElementById('progressModalSaveBtn');
                const value = document.getElementById('progressModalRange').value;

                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving…';

                axios.post(ROUTES.quickUpdate.replace('__ID__', progressModalState.key), { progress: value })
                    .then(() => {
                        closeProgressModal();
                        $('#ordersTable').DataTable().ajax.reload();
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Error!',
                            text: error.response?.data?.message || 'Something went wrong.',
                            icon: 'error',
                            confirmButtonColor: '#dc2626'
                        });
                    })
                    .finally(() => {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Save progress';
                    });
            }

            (function initProgressModal() {
                const range = document.getElementById('progressModalRange');
                const modal = document.getElementById('progressModal');
                if (!range || !modal) {
                    return;
                }

                range.addEventListener('input', syncProgressModal);

                modal.querySelectorAll('[data-progress-preset]').forEach(button => {
                    button.addEventListener('click', () => {
                        range.value = button.dataset.progressPreset;
                        syncProgressModal();
                    });
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                        closeProgressModal();
                    }
                });
            })();

            function handleCompletedDateClick(inputElement) {
                const orderId = inputElement.dataset.orderId;
                const originalValue = inputElement.dataset.originalValue || '';

                if (typeof flatpickr === 'undefined') {
                    console.error('Flatpickr is not loaded');
                    return;
                }

                if (inputElement._flatpickr) {
                    inputElement._flatpickr.destroy();
                }

                const picker = flatpickr(inputElement, {
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i:S',
                    time_24hr: true,
                    defaultDate: originalValue ? new Date(originalValue) : new Date(),
                    static: false,
                    appendTo: document.body,
                    onClose: function(selectedDates, dateStr, instance) {
                        instance.destroy();

                        if (selectedDates.length > 0) {
                            Swal.fire({
                                title: 'Update Completed Date?',
                                text: `Change completed date to "${dateStr}"?`,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#9333ea',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'Yes, update!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    Swal.showLoading();

                                    const formData = new FormData();
                                    formData.append('completed_at', dateStr);

                                    axios.post(ROUTES.quickUpdate.replace('__ID__', orderId), formData)
                                        .then(response => {
                                            Swal.fire({
                                                title: 'Updated!',
                                                text: response.data.message ||
                                                    'Completed date updated successfully.',
                                                icon: 'success',
                                                confirmButtonColor: '#9333ea',
                                                timer: 1500,
                                                showConfirmButton: false
                                            }).then(() => {
                                                $('#ordersTable').DataTable().ajax.reload();
                                            });
                                        })
                                        .catch(error => {
                                            Swal.fire({
                                                title: 'Error!',
                                                text: error.response?.data?.message ||
                                                    'Something went wrong.',
                                                icon: 'error',
                                                confirmButtonColor: '#dc2626'
                                            });
                                        });
                                }
                            });
                        }
                    }
                });

                picker.open();
            }

            function handleStartDateClick(inputElement) {
                const orderId = inputElement.dataset.orderId;
                const originalValue = inputElement.dataset.originalValue || '';

                if (typeof flatpickr === 'undefined') {
                    console.error('Flatpickr is not loaded');
                    return;
                }

                if (inputElement._flatpickr) {
                    inputElement._flatpickr.destroy();
                }

                const picker = flatpickr(inputElement, {
                    enableTime: false,
                    dateFormat: 'Y-m-d',
                    defaultDate: originalValue ? new Date(originalValue) : new Date(),
                    static: false,
                    appendTo: document.body,
                    onClose: function(selectedDates, dateStr, instance) {
                        instance.destroy();

                        if (selectedDates.length > 0) {
                            Swal.fire({
                                title: 'Update Start Date?',
                                text: `Change start date to "${dateStr}"?`,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#9333ea',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'Yes, update!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    Swal.showLoading();

                                    const formData = new FormData();
                                    formData.append('start_date', dateStr);

                                    axios.post(ROUTES.quickUpdate.replace('__ID__', orderId), formData)
                                        .then(response => {
                                            Swal.fire({
                                                title: 'Updated!',
                                                text: response.data.message ||
                                                    'Start date updated successfully.',
                                                icon: 'success',
                                                confirmButtonColor: '#9333ea',
                                                timer: 1500,
                                                showConfirmButton: false
                                            }).then(() => {
                                                $('#ordersTable').DataTable().ajax.reload();
                                            });
                                        })
                                        .catch(error => {
                                            Swal.fire({
                                                title: 'Error!',
                                                text: error.response?.data?.message ||
                                                    'Something went wrong.',
                                                icon: 'error',
                                                confirmButtonColor: '#dc2626'
                                            });
                                        });
                                }
                            });
                        }
                    }
                });

                picker.open();
            }

            function handleEndDateClick(element) {
                const orderId = element.dataset.orderId;
                const originalValue = element.dataset.originalValue || element.dataset.deadline || '';

                if (typeof flatpickr === 'undefined') {
                    console.error('Flatpickr is not loaded');
                    return;
                }

                // Create a temporary input for flatpickr
                const tempInput = document.createElement('input');
                tempInput.type = 'text';
                element.appendChild(tempInput);

                const picker = flatpickr(tempInput, {
                    enableTime: false,
                    dateFormat: 'Y-m-d',
                    defaultDate: originalValue ? new Date(originalValue) : new Date(),
                    static: true,
                    appendTo: element,
                    onClose: function(selectedDates, dateStr, instance) {
                        instance.destroy();
                        element.removeChild(tempInput);

                        if (selectedDates.length > 0) {
                            // Only show confirmation if date actually changed
                            if (dateStr !== originalValue) {
                                Swal.fire({
                                    title: 'Update Deadline Date?',
                                    text: `Change deadline date to "${dateStr}"?`,
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#f97316',
                                    cancelButtonColor: '#6b7280',
                                    confirmButtonText: 'Yes, update!',
                                    cancelButtonText: 'Cancel'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        Swal.showLoading();

                                        const formData = new FormData();
                                        formData.append('end_date', dateStr);

                                        axios.post(ROUTES.quickUpdate.replace('__ID__', orderId), formData)
                                            .then(response => {
                                                Swal.fire({
                                                    title: 'Updated!',
                                                    text: response.data.message ||
                                                        'Deadline date updated successfully.',
                                                    icon: 'success',
                                                    confirmButtonColor: '#f97316',
                                                    timer: 1500,
                                                    showConfirmButton: false
                                                }).then(() => {
                                                    $('#ordersTable').DataTable().ajax.reload();
                                                });
                                            })
                                            .catch(error => {
                                                Swal.fire({
                                                    title: 'Error!',
                                                    text: error.response?.data?.message ||
                                                        'Something went wrong.',
                                                    icon: 'error',
                                                    confirmButtonColor: '#dc2626'
                                                });
                                            });
                                    }
                                });
                            }
                        }
                    }
                });

                picker.open();
            }

            function handleLastUpdateClick(inputElement) {
                const orderId = inputElement.dataset.orderId;
                const originalValue = inputElement.dataset.originalValue || '';

                if (typeof flatpickr === 'undefined') {
                    console.error('Flatpickr is not loaded');
                    return;
                }

                if (inputElement._flatpickr) {
                    inputElement._flatpickr.destroy();
                }

                const picker = flatpickr(inputElement, {
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i:S',
                    time_24hr: true,
                    defaultDate: originalValue ? new Date(originalValue) : new Date(),
                    static: false,
                    appendTo: document.body,
                    onClose: function(selectedDates, dateStr, instance) {
                        instance.destroy();

                        if (selectedDates.length > 0) {
                            Swal.fire({
                                title: 'Update Last Update Date?',
                                text: `Change last update date to "${dateStr}"?`,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#9333ea',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'Yes, update!',
                                cancelButtonText: 'Cancel'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    Swal.showLoading();

                                    const formData = new FormData();
                                    formData.append('last_update_date', dateStr);

                                    axios.post(ROUTES.quickUpdate.replace('__ID__', orderId), formData)
                                        .then(response => {
                                            Swal.fire({
                                                title: 'Updated!',
                                                text: response.data.message ||
                                                    'Last update date updated successfully.',
                                                icon: 'success',
                                                confirmButtonColor: '#9333ea',
                                                timer: 1500,
                                                showConfirmButton: false
                                            }).then(() => {
                                                $('#ordersTable').DataTable().ajax.reload();
                                            });
                                        })
                                        .catch(error => {
                                            Swal.fire({
                                                title: 'Error!',
                                                text: error.response?.data?.message ||
                                                    'Something went wrong.',
                                                icon: 'error',
                                                confirmButtonColor: '#dc2626'
                                            });
                                        });
                                }
                            });
                        }
                    }
                });

                picker.open();
            }

            // User Assignment Modal - Vanilla JS Implementation
            let currentOrderId = null;
            let modalSelectedUsers = [];
            let modalSearchTimeout = null;
            let modalCurrentUsers = [];

            function openUserAssignmentModal(orderId, orderName) {
                if (!canManageAssignment) {
                    Swal.fire({
                        title: 'Access denied',
                        text: 'You do not have permission to manage order assignments.',
                        icon: 'error',
                        confirmButtonColor: '#dc2626'
                    });

                    return;
                }

                currentOrderId = orderId;
                const modal = document.getElementById('userAssignmentModal');
                const orderNameEl = document.getElementById('modal-order-name');

                // Set order name in modal header
                orderNameEl.textContent = `Order: ${orderName}`;

                // Show modal
                modal.classList.remove('hidden');

                // Fetch current assigned users for this order
                axios.get(ROUTES.users.replace('__ID__', orderId))
                    .then(response => {
                        modalSelectedUsers = response.data.users || [];
                        updateModalSelectedDisplay();

                        // Load initial available users
                        fetchModalUsers('').then(users => renderModalDropdown(users));
                    })
                    .catch(error => {
                        console.error('Error fetching users:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to load assigned users.',
                            icon: 'error',
                            confirmButtonColor: '#dc2626'
                        });
                    });
            }

            function closeUserAssignmentModal() {
                const modal = document.getElementById('userAssignmentModal');
                modal.classList.add('hidden');
                currentOrderId = null;
                modalSelectedUsers = [];
                modalCurrentUsers = [];
            }

            async function fetchModalUsers(search = '') {
                try {
                    const excludeIds = modalSelectedUsers.map(u => u.id);
                    const params = new URLSearchParams();
                    if (search) {
                        params.append('search', search);
                    }
                    if (excludeIds.length > 0) {
                        params.append('exclude', excludeIds.join(','));
                    }

                    const response = await axios.get(`${ROUTES.searchUsers}?${params}`);
                    modalCurrentUsers = response.data.users;
                    return modalCurrentUsers;
                } catch (error) {
                    console.error('Error fetching users:', error);
                    return [];
                }
            }

            function renderModalDropdown(users) {
                const dropdown = document.getElementById('modalUserDropdown');
                if (users.length === 0) {
                    dropdown.innerHTML =
                        '<div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">No users found</div>';
                } else {
                    dropdown.innerHTML = users.map(user => `
                        <div class="user-option flex items-center gap-3 p-3 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors"
                             data-user-id="${user.id}"
                             data-user-name="${user.name}"
                             data-user-email="${user.email}">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                ${user.name.charAt(0).toUpperCase()}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                                    <div class="font-semibold text-sm text-gray-900 dark:text-gray-100 truncate">${user.name}</div>
                                    <span class="text-xs text-white bg-indigo-500 px-2 py-0.5 rounded-full flex-shrink-0">${user.employee_id || 'N/A'}</span>
                                    ${user.team ? `<span class="text-xs text-white bg-purple-500 px-2 py-0.5 rounded-full flex-shrink-0"><i class="fas fa-users mr-1"></i>${user.team.name}</span>` : ''}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    ${user.position ? `<span class="font-medium text-gray-600 dark:text-gray-300">${user.position}</span> &bull; ` : ''}${user.email}
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-500/10 hover:bg-indigo-200 dark:hover:bg-indigo-500/20 flex items-center justify-center transition-colors">
                                    <i class="fas fa-plus text-indigo-600 dark:text-indigo-400 text-xs"></i>
                                </div>
                            </div>
                        </div>
                    `).join('');

                    // Add click handlers
                    dropdown.querySelectorAll('.user-option').forEach(option => {
                        option.addEventListener('click', function(e) {
                            e.stopPropagation();
                            const user = {
                                id: parseInt(this.dataset.userId),
                                name: this.dataset.userName,
                                email: this.dataset.userEmail
                            };
                            addModalUser(user);
                        });
                    });
                }
            }

            function addModalUser(user) {
                if (!modalSelectedUsers.find(s => s.id === user.id)) {
                    modalSelectedUsers.push(user);
                    updateModalSelectedDisplay();

                    const searchInput = document.getElementById('modalUserSearchInput');
                    if (searchInput.value.trim()) {
                        if (modalSearchTimeout) {
                            clearTimeout(modalSearchTimeout);
                        }
                        modalSearchTimeout = setTimeout(async () => {
                            const users = await fetchModalUsers(searchInput.value.trim());
                            renderModalDropdown(users);
                        }, 300);
                    } else {
                        fetchModalUsers('').then(users => renderModalDropdown(users));
                    }
                }
            }

            function removeModalUser(userId) {
                modalSelectedUsers = modalSelectedUsers.filter(u => u.id !== userId);
                updateModalSelectedDisplay();

                const searchInput = document.getElementById('modalUserSearchInput');
                if (searchInput.value.trim()) {
                    if (modalSearchTimeout) {
                        clearTimeout(modalSearchTimeout);
                    }
                    modalSearchTimeout = setTimeout(async () => {
                        const users = await fetchModalUsers(searchInput.value.trim());
                        renderModalDropdown(users);
                    }, 300);
                } else {
                    fetchModalUsers('').then(users => renderModalDropdown(users));
                }
            }

            function updateModalSelectedDisplay() {
                const display = document.getElementById('modalSelectedUsersDisplay');
                if (modalSelectedUsers.length === 0) {
                    display.innerHTML = '<span class="text-gray-400 dark:text-gray-500 text-sm">No users assigned</span>';
                } else {
                    display.innerHTML = modalSelectedUsers.map(user => `
                        <span class="inline-flex items-center gap-1 px-3 py-1 m-1 bg-indigo-100 dark:bg-indigo-500/10 text-indigo-800 dark:text-indigo-400 rounded-lg text-sm">
                            <span>${user.name}</span>
                            <button type="button" class="remove-modal-user-btn hover:text-red-600 dark:hover:text-red-400 transition-colors" data-user-id="${user.id}">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </span>
                    `).join('');

                    // Add click handlers for remove buttons
                    display.querySelectorAll('.remove-modal-user-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const userId = parseInt(this.dataset.userId);
                            removeModalUser(userId);
                        });
                    });
                }
            }

            function saveUserAssignments() {
                if (!currentOrderId) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'No order selected.',
                        icon: 'error',
                        confirmButtonColor: '#dc2626'
                    });
                    return;
                }

                const userIds = modalSelectedUsers.map(u => u.id);

                Swal.fire({
                    title: 'Update User Assignments?',
                    text: `Assign ${userIds.length} user(s) to this order?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#9333ea',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, update!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.showLoading();

                        const formData = new FormData();
                        userIds.forEach(id => {
                            formData.append('users[]', id);
                        });

                        axios.post(ROUTES.assignUsers.replace('__ID__', currentOrderId), formData)
                            .then(response => {
                                Swal.fire({
                                    title: 'Updated!',
                                    text: response.data.message || 'User assignments updated successfully.',
                                    icon: 'success',
                                    confirmButtonColor: '#9333ea',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    closeUserAssignmentModal();
                                    $('#ordersTable').DataTable().ajax.reload();
                                });
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Error!',
                                    text: error.response?.data?.message ||
                                        'Failed to update user assignments.',
                                    icon: 'error',
                                    confirmButtonColor: '#dc2626'
                                });
                            });
                    }
                });
            }

            // Modal search input event listener
            document.addEventListener('DOMContentLoaded', function() {
                const modalSearchInput = document.getElementById('modalUserSearchInput');
                const modalDropdown = document.getElementById('modalUserDropdown');

                if (modalSearchInput) {
                    modalSearchInput.addEventListener('input', function() {
                        const query = this.value.trim();
                        modalDropdown.classList.remove('hidden');

                        if (modalSearchTimeout) {
                            clearTimeout(modalSearchTimeout);
                        }

                        modalSearchTimeout = setTimeout(async () => {
                            const users = await fetchModalUsers(query);
                            renderModalDropdown(users);
                        }, 300);
                    });

                    modalSearchInput.addEventListener('focus', async function() {
                        modalDropdown.classList.remove('hidden');
                        const users = await fetchModalUsers('');
                        renderModalDropdown(users);
                    });
                }

                // Close modal dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (modalDropdown && !modalDropdown.classList.contains('hidden') &&
                        !modalDropdown.contains(e.target) &&
                        e.target !== modalSearchInput) {
                        modalDropdown.classList.add('hidden');
                    }
                });

                // Close modal on Escape key
                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape') {
                        const modal = document.getElementById('userAssignmentModal');
                        if (!modal.classList.contains('hidden')) {
                            closeUserAssignmentModal();
                        }
                    }
                });
            });

            // Export orders to CSV
            function exportOrders() {
                Swal.fire({
                    title: 'Export Orders',
                    text: 'Do you want to export all orders or just the filtered results?',
                    icon: 'question',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'Export All',
                    denyButtonText: 'Export Filtered',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#9333ea',
                    denyButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280'
                }).then((result) => {
                    if (result.isConfirmed || result.isDenied) {
                        const exportFiltered = result.isDenied;
                        exportOrdersToCSV(exportFiltered);
                    }
                });
            }

            function exportOrdersToCSV(filteredOnly = false) {
                Swal.showLoading();

                // Build query parameters
                const params = new URLSearchParams();

                // Only apply filters if exporting filtered results
                if (filteredOnly) {
                    const filters = {
                        search: $('#filterSearch').val(),
                        status: $('#filterStatus').val(),
                        team_id: $('#filterTeam').val(),
                        tech_stack_id: $('#filterTechStack').val(),
                        freelance_profile_id: $('#filterProfile').val(),
                        month: $('#filterMonth').val(),
                        my_sales: $('#filterMySales').is(':checked') ? '1' : '',
                    };

                    Object.keys(filters).forEach(key => {
                        if (filters[key]) {
                            params.append(key, filters[key]);
                        }
                    });
                }

                // For export all, we need to fetch without pagination limits
                // Set length to -1 to get all records
                params.append('start', '0');
                params.append('length', '-1');

                // Fetch orders data
                axios.get('{{ route('orders.data') }}?' + params.toString())
                    .then(response => {
                        const orders = response.data.data;

                        if (orders.length === 0) {
                            Swal.fire({
                                title: 'No Data',
                                text: 'No orders found to export.',
                                icon: 'info',
                                confirmButtonColor: '#9333ea'
                            });
                            return;
                        }

                        // Define CSV headers
                        const headers = [
                            'Order Name',
                            'Profile',
                            'Status',
                            'Special Status',
                            'Value',
                            'Actual Revenue',
                            'Team',
                            'Users',
                            'Start Date',
                            'End Date',
                            'Completed At',
                            'Progress (%)',
                            'Documents'
                        ];

                        // Convert orders to CSV format
                        const csvRows = [headers.join(',')];

                        orders.forEach(order => {
                            const row = [
                                `"${(order.name || '').replace(/"/g, '""')}"`,
                                `"${(order.freelance_profile || '').replace(/"/g, '""')}"`,
                                `"${order.status || ''}"`,
                                `"${getSpecialStatusLabel(order.special_status) || ''}"`,
                                `"${order.value || ''}"`,
                                `"${order.actual_revenue || ''}"`,
                                `"${(order.team || '').replace(/"/g, '""')}"`,
                                `"${(order.users_names || '').replace(/"/g, '""')}"`,
                                `"${order.start_date || ''}"`,
                                `"${order.end_date || ''}"`,
                                `"${order.completed_at || ''}"`,
                                `"${order.progress ?? 0}"`,
                                `"${order.documents_count ?? 0}"`
                            ];
                            csvRows.push(row.join(','));
                        });

                        // Create CSV blob and download
                        const csvContent = csvRows.join('\n');
                        const blob = new Blob([csvContent], {
                            type: 'text/csv;charset=utf-8;'
                        });
                        const link = document.createElement('a');
                        const url = URL.createObjectURL(blob);

                        const timestamp = new Date().toISOString().replace(/[:.]/g, '-').slice(0, -5);
                        link.setAttribute('href', url);
                        link.setAttribute('download', `orders-export-${timestamp}.csv`);
                        link.style.visibility = 'hidden';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        Swal.fire({
                            title: 'Export Complete!',
                            text: `Successfully exported ${orders.length} order(s).`,
                            icon: 'success',
                            confirmButtonColor: '#9333ea'
                        });
                    })
                    .catch(error => {
                        console.error('Export error:', error);
                        Swal.fire({
                            title: 'Export Failed',
                            text: error.response?.data?.message || 'Failed to export orders.',
                            icon: 'error',
                            confirmButtonColor: '#dc2626'
                        });
                    });
            }

            function getSpecialStatusLabel(status) {
                const labels = {
                    'selected_for_delivery': 'Selected for delivery',
                    'continue_for_next': 'Continue for next',
                    'stuck_order': 'Stuck order'
                };
                return labels[status] || '';
            }
        </script>
    @endpush
@endsection
