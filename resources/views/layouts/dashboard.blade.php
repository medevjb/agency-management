<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PMT') }} - Dashboard</title>
    <link rel="icon" type="image/png/jpg" href="{{ asset('image/stv.jpg') }}">

    <!-- Apply saved/system theme before first paint to avoid a light/dark flash -->
    <script>
        (function () {
            const stored = localStorage.getItem('theme');
            const isDark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', isDark);
        })();
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ Vite::asset('resources/css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Axios CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.8/dist/axios.min.js"></script>
    <script>
        // Configure axios defaults for CSRF token
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
        axios.defaults.headers.common['Accept'] = 'application/json';
    </script>
    <style>
        /* Custom Flatpickr Styling */
        .flatpickr-calendar {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            font-family: 'Figtree', sans-serif;
            padding: 12px;
            width: 280px !important;
            max-width: 95vw !important;
            max-height: 90vh !important;
            overflow: auto;
            z-index: 99999 !important;
        }

        .flatpickr-calendar.inline {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .flatpickr-calendar.hasTime.noCalendar {
            width: 280px !important;
        }

        .flatpickr-calendar:before,
        .flatpickr-calendar:after {
            display: none;
        }

        /* Month navigation */
        .flatpickr-months {
            margin: 0 0 12px 0;
            padding: 0;
            height: 32px;
        }

        .flatpickr-month {
            height: 32px;
            display: flex;
            align-items: center;
        }

        .flatpickr-current-month {
            padding: 0;
            height: 32px;
            line-height: 32px;
            color: #1f2937;
            font-weight: 600;
            font-size: 14px;
            position: relative;
            left: 0;
        }

        .flatpickr-current-month span.cur-month {
            font-weight: 700;
            color: #6366f1;
        }

        .flatpickr-current-month span.cur-year {
            color: #6b7280;
            font-weight: 500;
        }

        .flatpickr-months .flatpickr-prev-month,
        .flatpickr-months .flatpickr-next-month {
            height: 32px;
            width: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border: none;
            border-radius: 8px;
            color: #4b5563;
            fill: #4b5563;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .flatpickr-months .flatpickr-prev-month:hover,
        .flatpickr-months .flatpickr-next-month:hover {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: #ffffff;
            fill: #ffffff;
            transform: scale(1.05);
        }

        .flatpickr-months .flatpickr-prev-month:active,
        .flatpickr-months .flatpickr-next-month:active {
            transform: scale(0.95);
        }

        .flatpickr-months .flatpickr-prev-month svg,
        .flatpickr-months .flatpickr-next-month svg {
            width: 16px;
            height: 16px;
        }

        /* Days of week */
        .flatpickr-weekdays {
            margin-bottom: 6px;
        }

        .flatpickr-weekday {
            color: #6b7280;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Calendar days */
        .flatpickr-days {
            width: 100%;
        }

        .dayContainer {
            width: 100%;
            min-width: 100%;
            max-width: 100%;
        }

        .flatpickr-day {
            height: 32px;
            line-height: 32px;
            border-radius: 8px;
            color: #374151;
            font-weight: 500;
            font-size: 13px;
            margin: 1px 0;
            transition: all 0.2s ease;
        }

        .flatpickr-day:hover {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #4f46e5;
            transform: scale(1.05);
            border-color: transparent;
        }

        .flatpickr-day.prevMonthDay,
        .flatpickr-day.nextMonthDay {
            color: #d1d5db;
        }

        .flatpickr-day.today {
            border: 2px solid #6366f1;
            background: #ffffff;
            color: #6366f1;
            font-weight: 600;
        }

        .flatpickr-day.today:hover {
            border: 2px solid #6366f1;
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #4f46e5;
        }

        .flatpickr-day.selected,
        .flatpickr-day.startRange,
        .flatpickr-day.endRange,
        .flatpickr-day.selected.inRange,
        .flatpickr-day.startRange.inRange,
        .flatpickr-day.endRange.inRange,
        .flatpickr-day.selected:focus,
        .flatpickr-day.startRange:focus,
        .flatpickr-day.endRange:focus,
        .flatpickr-day.selected:hover,
        .flatpickr-day.startRange:hover,
        .flatpickr-day.endRange:hover,
        .flatpickr-day.selected.prevMonthDay,
        .flatpickr-day.startRange.prevMonthDay,
        .flatpickr-day.endRange.prevMonthDay,
        .flatpickr-day.selected.nextMonthDay,
        .flatpickr-day.startRange.nextMonthDay,
        .flatpickr-day.endRange.nextMonthDay {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
            border-color: transparent !important;
            color: #ffffff !important;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }

        .flatpickr-day.inRange {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%) !important;
            border-color: transparent !important;
            color: #4f46e5 !important;
            box-shadow: none;
        }

        .flatpickr-day.disabled {
            color: #d1d5db;
            background: #f9fafb;
            cursor: not-allowed;
        }

        .flatpickr-day.disabled:hover {
            background: #f9fafb;
            color: #d1d5db;
            transform: none;
        }

        .flatpickr-day.week.selected {
            box-shadow: -5px 0 0 #6366f1, 5px 0 0 #6366f1;
        }

        /* Week numbers */
        .flatpickr-weekwrapper {
            display: none;
        }

        /* Time picker */
        .flatpickr-time {
            border-top: 1px solid #e5e7eb;
            height: 40px;
        }

        .flatpickr-time .numInputWrapper {
            height: 32px;
        }

        .flatpickr-time .flatpickr-am-pm {
            height: 32px;
            line-height: 32px;
        }

        /* Calendar container position */
        .flatpickr-calendar.open {
            margin-top: 4px;
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        /* ===== Custom SweetAlert2 Styling ===== */
        .swal2-popup {
            font-family: 'Figtree', system-ui, -apple-system, sans-serif !important;
            border-radius: 20px !important;
            padding: 2rem 1.75rem 1.5rem !important;
            border: 1px solid #e5e7eb !important;
            box-shadow:
                0 25px 50px -12px rgba(0, 0, 0, 0.15),
                0 0 0 1px rgba(0, 0, 0, 0.03) !important;
            max-width: 420px !important;
        }

        .swal2-popup.swal2-toast {
            border-radius: 14px !important;
            padding: 0.75rem 1rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        }

        /* Backdrop */
        .swal2-container {
            backdrop-filter: blur(4px) !important;
            -webkit-backdrop-filter: blur(4px) !important;
        }

        .swal2-container.swal2-backdrop-show {
            background: rgba(17, 24, 39, 0.4) !important;
        }

        /* Icon ring overrides */
        .swal2-icon {
            border-width: 3px !important;
            width: 64px !important;
            height: 64px !important;
            margin: 0 auto 1rem !important;
        }

        .swal2-icon .swal2-icon-content {
            font-size: 2rem !important;
        }

        .swal2-icon.swal2-success {
            border-color: #22c55e !important;
            color: #22c55e !important;
        }

        .swal2-icon.swal2-success [class^='swal2-success-line'] {
            background-color: #22c55e !important;
        }

        .swal2-icon.swal2-success .swal2-success-ring {
            border-color: rgba(34, 197, 94, 0.25) !important;
        }

        .swal2-icon.swal2-warning {
            border-color: #f59e0b !important;
            color: #f59e0b !important;
        }

        .swal2-icon.swal2-error {
            border-color: #ef4444 !important;
            color: #ef4444 !important;
        }

        .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
            background-color: #ef4444 !important;
        }

        .swal2-icon.swal2-info {
            border-color: #6366f1 !important;
            color: #6366f1 !important;
        }

        .swal2-icon.swal2-question {
            border-color: #6366f1 !important;
            color: #6366f1 !important;
        }

        /* Title */
        .swal2-title {
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            color: #111827 !important;
            padding: 0 !important;
            margin-bottom: 0.35rem !important;
            line-height: 1.4 !important;
        }

        /* Text / HTML content */
        .swal2-html-container {
            font-size: 0.875rem !important;
            color: #6b7280 !important;
            line-height: 1.6 !important;
            margin: 0 0 0.25rem !important;
            padding: 0 !important;
        }

        /* Actions / Buttons container */
        .swal2-actions {
            margin-top: 1.25rem !important;
            gap: 0.625rem !important;
        }

        /* Shared button base */
        .swal2-popup .swal2-styled {
            font-family: 'Figtree', system-ui, -apple-system, sans-serif !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            padding: 0.625rem 1.5rem !important;
            border-radius: 10px !important;
            letter-spacing: 0.01em !important;
            transition: all 0.15s ease !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
            border: none !important;
            min-width: 100px !important;
        }

        .swal2-popup .swal2-styled:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3) !important;
        }

        /* Confirm button */
        .swal2-popup .swal2-styled.swal2-confirm {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
            color: #ffffff !important;
        }

        .swal2-popup .swal2-styled.swal2-confirm:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35) !important;
        }

        .swal2-popup .swal2-styled.swal2-confirm:active {
            transform: translateY(0) !important;
        }

        /* Deny / Destructive button */
        .swal2-popup .swal2-styled.swal2-deny {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            color: #ffffff !important;
        }

        .swal2-popup .swal2-styled.swal2-deny:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.35) !important;
        }

        /* Cancel button */
        .swal2-popup .swal2-styled.swal2-cancel {
            background: #ffffff !important;
            color: #374151 !important;
            border: 1px solid #d1d5db !important;
        }

        .swal2-popup .swal2-styled.swal2-cancel:hover {
            background: #f9fafb !important;
            border-color: #9ca3af !important;
            transform: translateY(-1px) !important;
        }

        .swal2-popup .swal2-styled.swal2-cancel:focus {
            box-shadow: 0 0 0 3px rgba(107, 114, 128, 0.2) !important;
        }

        /* Input fields */
        .swal2-input,
        .swal2-textarea,
        .swal2-select {
            border: 1px solid #d1d5db !important;
            border-radius: 10px !important;
            font-size: 0.875rem !important;
            padding: 0.625rem 0.875rem !important;
            color: #111827 !important;
            font-family: 'Figtree', system-ui, -apple-system, sans-serif !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
        }

        .swal2-input:focus,
        .swal2-textarea:focus,
        .swal2-select:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
            outline: none !important;
        }

        .swal2-input::placeholder,
        .swal2-textarea::placeholder {
            color: #9ca3af !important;
        }

        /* Validation message */
        .swal2-validation-message {
            background: #fef2f2 !important;
            color: #dc2626 !important;
            border-radius: 10px !important;
            font-size: 0.8125rem !important;
            font-weight: 500 !important;
            padding: 0.625rem 1rem !important;
            margin-top: 0.5rem !important;
            border: 1px solid #fecaca !important;
        }

        .swal2-validation-message::before {
            background: #ef4444 !important;
        }

        /* Close button */
        .swal2-close {
            font-size: 1.5rem !important;
            color: #9ca3af !important;
            width: 32px !important;
            height: 32px !important;
            border-radius: 8px !important;
            transition: all 0.15s ease !important;
            top: 0.875rem !important;
            right: 0.875rem !important;
        }

        .swal2-close:hover {
            color: #374151 !important;
            background: #f3f4f6 !important;
        }

        /* Loading spinner */
        .swal2-loading .swal2-styled.swal2-confirm {
            border-color: transparent !important;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
            opacity: 0.8 !important;
        }

        /* Timer progress bar */
        .swal2-timer-progress-bar {
            background: linear-gradient(90deg, #6366f1, #8b5cf6) !important;
            height: 4px !important;
            border-radius: 0 0 20px 20px !important;
        }

        /* Footer */
        .swal2-footer {
            border-top: 1px solid #f3f4f6 !important;
            color: #9ca3af !important;
            font-size: 0.8125rem !important;
            padding-top: 0.75rem !important;
            margin-top: 0.75rem !important;
        }

        .swal2-footer a {
            color: #6366f1 !important;
            font-weight: 500 !important;
        }

        .swal2-footer a:hover {
            color: #4f46e5 !important;
        }

        /* Popup animation overrides */
        .swal2-popup.swal2-show {
            animation: swalPopIn 0.25s cubic-bezier(0.21, 1.02, 0.73, 1) !important;
        }

        .swal2-popup.swal2-hide {
            animation: swalPopOut 0.15s ease-in forwards !important;
        }

        @keyframes swalPopIn {
            0% { opacity: 0; transform: scale(0.92) translateY(8px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        @keyframes swalPopOut {
            0% { opacity: 1; transform: scale(1) translateY(0); }
            100% { opacity: 0; transform: scale(0.92) translateY(8px); }
        }

        /* ===== Dark Mode Overrides ===== */
        /* Flatpickr and SweetAlert2 render their own markup outside Tailwind's
           reach, so they need plain CSS overrides keyed off the `dark` class
           on <html> rather than Tailwind's `dark:` utility variant. */
        .dark .flatpickr-calendar {
            background: #1f2937;
            border-color: #374151;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        .dark .flatpickr-current-month {
            color: #f3f4f6;
        }

        .dark .flatpickr-current-month span.cur-year {
            color: #9ca3af;
        }

        .dark .flatpickr-weekday {
            color: #9ca3af;
        }

        .dark .flatpickr-months .flatpickr-prev-month,
        .dark .flatpickr-months .flatpickr-next-month {
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            color: #e5e7eb;
            fill: #e5e7eb;
        }

        .dark .flatpickr-day {
            color: #e5e7eb;
        }

        .dark .flatpickr-day.prevMonthDay,
        .dark .flatpickr-day.nextMonthDay {
            color: #4b5563;
        }

        .dark .flatpickr-day.today {
            background: #1f2937;
        }

        .dark .flatpickr-day.disabled {
            background: #111827;
            color: #4b5563;
        }

        .dark .flatpickr-day.disabled:hover {
            background: #111827;
            color: #4b5563;
        }

        .dark .flatpickr-time {
            border-top-color: #374151;
        }

        .dark .swal2-popup {
            background: #1f2937 !important;
            border-color: #374151 !important;
        }

        .dark .swal2-title {
            color: #f3f4f6 !important;
        }

        .dark .swal2-html-container {
            color: #9ca3af !important;
        }

        .dark .swal2-popup .swal2-styled.swal2-cancel {
            background: #374151 !important;
            color: #e5e7eb !important;
            border-color: #4b5563 !important;
        }

        .dark .swal2-popup .swal2-styled.swal2-cancel:hover {
            background: #4b5563 !important;
        }

        .dark .swal2-input,
        .dark .swal2-textarea,
        .dark .swal2-select {
            background: #111827 !important;
            border-color: #4b5563 !important;
            color: #f3f4f6 !important;
        }

        .dark .swal2-close {
            color: #9ca3af !important;
        }

        .dark .swal2-close:hover {
            color: #e5e7eb !important;
            background: #374151 !important;
        }

        .dark .swal2-footer {
            border-top-color: #374151 !important;
            color: #6b7280 !important;
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-950">
    <!-- Sidebar Component -->
    @include('components.sidebar')

    <!-- Mobile Sidebar Overlay -->
    <div id="mobileSidebarOverlay" class="hidden fixed inset-0 z-40 lg:hidden bg-gray-600 bg-opacity-75 transition-opacity duration-300"></div>

    <!-- Mobile Sidebar -->
    <aside id="mobileSidebar" class="hidden fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-indigo-600 to-indigo-800 text-white lg:hidden">
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between h-16 px-4 border-b border-indigo-700">
                <div class="flex items-center gap-2">
                    @php $mobileSiteName = \App\Models\Setting::get('site_name', config('app.name', 'PMT')); @endphp
                    <x-site-logo
                        img-class="h-8 w-auto max-w-[120px] object-contain"
                        badge-class="h-8 w-8 text-sm" />
                    <h1 class="text-xl font-bold">{{ $mobileSiteName }}</h1>
                </div>
                <button id="closeMobileSidebar" class="text-white hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <!-- Dashboard Section - Only for management -->
                @role(['admin', 'operation-manager', 'project-manager'])
                <div class="mb-2 sidebar-section" data-section="dashboard">
                    <button class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-indigo-700/50 transition-all duration-200 group">
                        <div class="flex items-center">
                            <i class="fas fa-th-large w-5 text-indigo-300 group-hover:text-white transition-colors"></i>
                            <span class="ml-3">Dashboard</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron"></i>
                    </button>
                    <div class="sidebar-content mt-1 space-y-1 ml-4 hidden">
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-chart-line w-5 mr-2"></i>
                            Overview
                        </a>
                    </div>
                </div>
                @endrole

                <!-- Orders Section -->
                <div class="mb-2 sidebar-section" data-section="orders">
                    <button class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-indigo-700/50 transition-all duration-200 group">
                        <div class="flex items-center">
                            <i class="fas fa-shopping-cart w-5 text-indigo-300 group-hover:text-white transition-colors"></i>
                            <span class="ml-3">Orders</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron"></i>
                    </button>
                    <div class="sidebar-content mt-1 space-y-1 ml-4 hidden">
                        <a href="{{ route('orders.dashboard') }}"
                           class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('orders.dashboard') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-chart-line w-5 mr-2"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('orders.index') }}"
                           class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('orders.index') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-list w-5 mr-2"></i>
                            All Orders
                        </a>
                        <a href="{{ route('orders.create') }}"
                           class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('orders.create') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-plus w-5 mr-2"></i>
                            Add Order
                        </a>
                    </div>
                </div>

                <!-- Tasks Section -->
                <div class="mb-2 sidebar-section" data-section="tasks">
                    <button class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-indigo-700/50 transition-all duration-200 group">
                        <div class="flex items-center">
                            <i class="fas fa-tasks w-5 text-cyan-400 group-hover:text-cyan-300 transition-colors"></i>
                            <span class="ml-3">Tasks</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron"></i>
                    </button>
                    <div class="sidebar-content mt-1 space-y-1 ml-4 hidden">
                        <a href="{{ route('tasks.index') }}"
                           class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('tasks.index') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-list w-5 mr-2"></i>
                            All Tasks
                        </a>
                        <a href="{{ route('tasks.archived') }}"
                           class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('tasks.archived') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-archive w-5 mr-2"></i>
                            Archived Tasks
                        </a>
                        <a href="{{ route('tasks.create') }}"
                           class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('tasks.create') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-plus w-5 mr-2"></i>
                            Add Task
                        </a>
                    </div>
                </div>

                <!-- Admin Section -->
                @role('admin')
                <div class="mb-2 sidebar-section" data-section="admin">
                    <button class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-red-600/30 transition-all duration-200 group">
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt w-5 text-red-400 group-hover:text-red-300 transition-colors"></i>
                            <span class="ml-3">Admin</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron"></i>
                    </button>
                    <div class="sidebar-content mt-1 space-y-1 ml-4 hidden">
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-red-600/20 transition-colors {{ request()->routeIs('admin.*') ? 'bg-red-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-tachometer-alt w-5 mr-2"></i>
                            Admin Dashboard
                        </a>
                        <a href="{{ route('users.index') }}"
                           class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-red-600/20 transition-colors {{ request()->routeIs('users.*') ? 'bg-red-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-users-cog w-5 mr-2"></i>
                            Manage Users
                        </a>
                        <a href="{{ route('admin.roles') }}"
                           class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-red-600/20 transition-colors {{ request()->routeIs('admin.roles*') ? 'bg-red-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-key w-5 mr-2"></i>
                            Roles & Permissions
                        </a>
                        <a href="{{ route('admin.github.index') }}"
                           class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-red-600/20 transition-colors {{ request()->routeIs('admin.github.*') ? 'bg-red-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-code-branch w-5 mr-2"></i>
                            GitHub Commits
                        </a>
                        <a href="{{ route('settings.index') }}"
                           class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-red-600/20 transition-colors {{ request()->routeIs('settings.*') ? 'bg-red-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-cog w-5 mr-2"></i>
                            System Settings
                        </a>
                    </div>
                </div>
                @endrole

                @role(['operation-manager', 'project-manager'])
                <div class="mb-2 sidebar-section" data-section="admin">
                    <a href="{{ route('admin.github.index') }}"
                       class="flex items-center px-4 py-3 text-sm font-semibold rounded-lg hover:bg-indigo-700/50 transition-all duration-200 {{ request()->routeIs('admin.github.*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                        <i class="fas fa-code-branch w-5 mr-2 text-indigo-300"></i>
                        GitHub Commits
                    </a>
                </div>
                @endrole

                <!-- Team Section -->
                @if(auth()->user()->can('viewAny', \App\Models\Team::class) || auth()->user()->can('viewAny', \App\Models\TechStack::class))
                <div class="mb-2 sidebar-section" data-section="team">
                    <button class="sidebar-toggle w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-lg hover:bg-indigo-700/50 transition-all duration-200 group">
                        <div class="flex items-center">
                            <i class="fas fa-users w-5 text-green-400 group-hover:text-green-300 transition-colors"></i>
                            <span class="ml-3">Team</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200 sidebar-chevron"></i>
                    </button>
                    <div class="sidebar-content mt-1 space-y-1 ml-4 hidden">
                        @can('viewAny', \App\Models\Team::class)
                        <a href="{{ route('teams.index') }}"
                           class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('teams.*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-users w-5 mr-2"></i>
                            Teams
                        </a>
                        @endcan
                        @can('viewAny', \App\Models\TechStack::class)
                        <a href="{{ route('tech-stacks.index') }}"
                           class="flex items-center px-4 py-2 text-sm rounded-lg hover:bg-indigo-700/30 transition-colors {{ request()->routeIs('tech-stacks.*') ? 'bg-indigo-600/50 text-white' : 'text-indigo-200' }}">
                            <i class="fas fa-layer-group w-5 mr-2"></i>
                            Tech Stacks
                        </a>
                        @endcan
                    </div>
                </div>
                @endif
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden lg:ml-64">
        <!-- Header Component -->
        @include('components.header')

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-950">
            @yield('content')
        </main>
    </div>

    <script src="{{ Vite::asset('resources/js/app.js') }}" type="module" defer></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const SwalDefault = Swal.mixin({
            customClass: {
                popup: 'swal2-popup',
                confirmButton: 'swal2-styled swal2-confirm',
                cancelButton: 'swal2-styled swal2-cancel',
                denyButton: 'swal2-styled swal2-deny',
            },
            buttonsStyling: false,
            reverseButtons: true,
            showClass: { popup: 'swal2-show' },
            hideClass: { popup: 'swal2-hide' },
        });
    </script>

    <!-- Flash Message Handler -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: @json(session('success')),
                timer: 2500,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        @endif
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: @json(session('error')),
            });
        @endif
    });
    </script>

    <!-- Sidebar Accordion Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all sidebar sections
        const sidebarSections = document.querySelectorAll('.sidebar-section');

        // Function to toggle a section (independent - no auto-close of others)
        function toggleSection(section) {
            const content = section.querySelector('.sidebar-content');
            const chevron = section.querySelector('.sidebar-chevron');

            // Toggle visibility
            content.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }

        // Add click listeners to all sidebar toggles
        sidebarSections.forEach(function(section) {
            const toggle = section.querySelector('.sidebar-toggle');
            if (toggle) {
                toggle.addEventListener('click', function() {
                    toggleSection(section);
                });
            }
        });

        // Mobile sidebar functionality
        const mobileSidebarBtn = document.getElementById('mobileSidebarBtn');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const mobileSidebarOverlay = document.getElementById('mobileSidebarOverlay');
        const closeMobileSidebarBtn = document.getElementById('closeMobileSidebar');

        if (mobileSidebarBtn && mobileSidebar && mobileSidebarOverlay) {
            // Open mobile sidebar
            mobileSidebarBtn.addEventListener('click', function(e) {
                e.preventDefault();
                mobileSidebar.classList.remove('hidden');
                mobileSidebarOverlay.classList.remove('hidden');
            });

            // Close mobile sidebar when clicking overlay
            mobileSidebarOverlay.addEventListener('click', function() {
                mobileSidebar.classList.add('hidden');
                mobileSidebarOverlay.classList.add('hidden');
            });

            // Close mobile sidebar when clicking close button
            if (closeMobileSidebarBtn) {
                closeMobileSidebarBtn.addEventListener('click', function() {
                    mobileSidebar.classList.add('hidden');
                    mobileSidebarOverlay.classList.add('hidden');
                });
            }
        }
    });
    </script>

    <!-- User Dropdown Script -->
    <script>
    // User dropdown functionality - load when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        const userDropdownBtn = document.getElementById('user-dropdown-btn');
        const userDropdown = document.getElementById('user-dropdown');

        if (userDropdownBtn && userDropdown) {
            // Toggle dropdown when button is clicked
            userDropdownBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!userDropdown.contains(e.target) && !userDropdownBtn.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });

            // Close dropdown when pressing Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    userDropdown.classList.add('hidden');
                }
            });
        }
    });
    </script>

    <!-- Theme Toggle Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggleBtn = document.getElementById('themeToggle');
        if (!themeToggleBtn) {
            return;
        }

        themeToggleBtn.addEventListener('click', function() {
            const isDark = !document.documentElement.classList.contains('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            // Reload so canvas-based charts (Chart.js) and any other JS that
            // bakes colors in at render time pick up the new theme correctly.
            window.location.reload();
        });
    });
    </script>

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize Flatpickr for date fields
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all datepicker inputs
            const dateInputs = document.querySelectorAll('.datepicker');
            dateInputs.forEach(function(input) {
                const picker = flatpickr(input, {
                    dateFormat: 'Y-m-d',
                    allowInput: false,
                    disableMobile: true,
                    clickOpens: true,
                    inline: false,
                    static: true,
                    animate: true,
                    monthSelectorType: 'static',
                    appendTo: document.body,
                    position: 'below',
                    prevArrow:
                        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>',
                    nextArrow:
                        '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>',
                    onChange: function(selectedDates, dateStr, instance) {
                        // If end date exists and is before start date, update it
                        if (instance.element.id === 'start_date' && selectedDates.length > 0) {
                            const endDateInput = document.getElementById('end_date');
                            if (endDateInput) {
                                const endDate = flatpickr(endDateInput);
                                endDate.set('minDate', dateStr);
                                if (endDate.selectedDates.length > 0 && endDate.selectedDates[0] < selectedDates[0]) {
                                    endDate.setDate(selectedDates[0]);
                                }
                            }
                        }

                        // Calculate duration if function exists
                        if (typeof calculateDuration === 'function') {
                            calculateDuration();
                        }
                    }
                });
            });
        });
    </script>

    @stack('scripts')

    {{-- Global AJAX Select Component --}}
    <script>
    (function () {
        const statusColors = {
            'nra':         'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
            'pending':     'bg-yellow-100 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400',
            'in-progress': 'bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400',
            'delivered':   'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400',
            'on-hold':     'bg-orange-100 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400',
            'cancelled':   'bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400',
        };

        function badgeClass(val) {
            return statusColors[val] || 'bg-purple-100 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400';
        }

        function initAjaxSelect(wrapper) {
            if (wrapper._asi) return;
            wrapper._asi = true;

            const url      = wrapper.dataset.url;
            const badgeKey = wrapper.dataset.badge || '';
            const uid      = wrapper.dataset.uid;
            const search   = document.getElementById(uid + '_s');
            const hidden   = document.getElementById(uid + '_h');
            const dropdown = document.getElementById(uid + '_d');
            const clear    = document.getElementById(uid + '_c');

            let cached = null;
            let timer  = null;

            function renderOptions(items) {
                if (!items.length) {
                    dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-400 dark:text-gray-500">No results found</div>';
                } else {
                    dropdown.innerHTML = items.map(item => {
                        const label    = String(item.name || item.label || '');
                        const badgeVal = badgeKey ? (item[badgeKey] || '') : '';
                        return `<button type="button"
                                        data-id="${item.id}"
                                        data-label="${label.replace(/"/g, '&quot;')}"
                                        class="asi-opt w-full text-left px-4 py-2.5 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 text-gray-900 dark:text-gray-100 text-sm border-b border-gray-100 dark:border-gray-700 last:border-0 flex items-center justify-between gap-2 transition-colors">
                                    <span class="truncate">${label}</span>
                                    ${badgeVal ? `<span class="flex-shrink-0 text-xs font-medium px-1.5 py-0.5 rounded ${badgeClass(badgeVal)}">${badgeVal}</span>` : ''}
                                </button>`;
                    }).join('');

                    dropdown.querySelectorAll('.asi-opt').forEach(btn => {
                        btn.addEventListener('click', () => pick(btn.dataset.id, btn.dataset.label));
                    });
                }
                dropdown.classList.remove('hidden');
            }

            function pick(id, label) {
                hidden.value = id;
                search.value = label;
                search.classList.add('border-indigo-400', 'bg-indigo-50');
                clear.classList.remove('hidden');
                dropdown.classList.add('hidden');
                hidden.dispatchEvent(new Event('change', {bubbles: true}));
            }

            function reset() {
                hidden.value = '';
                search.value = '';
                search.classList.remove('border-indigo-400', 'bg-indigo-50');
                clear.classList.add('hidden');
                dropdown.classList.add('hidden');
                hidden.dispatchEvent(new Event('change', {bubbles: true}));
            }

            function fetchItems(q) {
                const endpoint = q ? `${url}?q=${encodeURIComponent(q)}` : url;
                fetch(endpoint)
                    .then(r => r.json())
                    .then(data => {
                        if (!q) cached = data;
                        renderOptions(data);
                    });
            }

            search.addEventListener('focus', () => {
                if (search.value) return;
                if (cached) renderOptions(cached);
                else fetchItems('');
            });

            search.addEventListener('input', () => {
                const q = search.value.trim();
                clearTimeout(timer);
                if (!q) {
                    if (cached) renderOptions(cached);
                    else fetchItems('');
                    return;
                }
                timer = setTimeout(() => fetchItems(q), 300);
            });

            clear.addEventListener('click', reset);

            document.addEventListener('click', e => {
                if (!wrapper.contains(e.target)) dropdown.classList.add('hidden');
            });
        }

        function initAll() {
            document.querySelectorAll('[data-ajax-select]').forEach(initAjaxSelect);
        }

        document.addEventListener('DOMContentLoaded', initAll);

        // Support dynamic modals — re-init whenever new nodes appear
        const observer = new MutationObserver(() => initAll());
        observer.observe(document.body, { childList: true, subtree: true });
    })();
    </script>
</body>
</html>
