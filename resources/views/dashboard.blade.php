@extends('layouts.dashboard')

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }
    .gradient-icon {
        background: linear-gradient(135deg, var(--icon-from) 0%, var(--icon-to) 100%);
    }
    .order-card {
        transition: all 0.2s ease;
    }
    .order-card:hover {
        border-color: #FF5A1F;
        box-shadow: 0 4px 12px rgba(255, 90, 31, 0.15);
    }
    .progress-bar {
        transition: width 0.6s ease;
    }

    /* This page's stat row wants a fifth column at xl; the shared .dash-grid-4
       caps at four, so extend it here rather than depend on an arbitrary
       Tailwind utility that may not be in the compiled bundle. */
    @media (min-width: 1280px) {
        .dash-grid-5-at-xl {
            grid-template-columns: repeat(5, minmax(0, 1fr));
        }
    }

    /* ---- Sales band ---- */
    .sales-band {
        padding: 1.5rem;
        border-radius: 1.25rem;
        background-color: #fffbeb;
        border: 1px solid #fde68a;
    }

    .dark .sales-band {
        background-color: rgba(245, 158, 11, 0.05);
        border-color: rgba(245, 158, 11, 0.2);
    }

    .sales-band-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.125rem;
        font-weight: 800;
        letter-spacing: -0.01em;
        color: #111827;
    }

    .dark .sales-band-title {
        color: #f3f4f6;
    }

    .sales-band-dot {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 9999px;
        background-image: linear-gradient(135deg, #f59e0b, #f97316);
    }

    .sales-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: 'tnum';
    }

    .sales-hero {
        font-size: 2.25rem;
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -0.035em;
        color: #111827;
        font-variant-numeric: tabular-nums;
    }

    .dark .sales-hero {
        color: #ffffff;
    }

    .sales-delta {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        font-weight: 800;
    }

    .sales-delta.is-up { background-color: #d1fae5; color: #047857; }
    .sales-delta.is-down { background-color: #fee2e2; color: #b91c1c; }
    .dark .sales-delta.is-up { background-color: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
    .dark .sales-delta.is-down { background-color: rgba(239, 68, 68, 0.15); color: #fca5a5; }

    .sales-pct {
        font-size: 1.125rem;
        font-weight: 800;
    }

    .sales-pct.is-met { color: #059669; }
    .sales-pct.is-close { color: #d97706; }
    .sales-pct.is-behind { color: #dc2626; }
    .sales-pct.is-none { color: #9ca3af; }

    .dark .sales-pct.is-met { color: #34d399; }
    .dark .sales-pct.is-close { color: #fbbf24; }
    .dark .sales-pct.is-behind { color: #f87171; }

    .sales-bar {
        height: 0.5rem;
        border-radius: 9999px;
        background-color: #f1f5f9;
        overflow: hidden;
    }

    .sales-bar.is-slim {
        height: 0.25rem;
        margin-top: 0.3125rem;
    }

    .dark .sales-bar {
        background-color: #1f2937;
    }

    .sales-bar span {
        display: block;
        height: 100%;
        border-radius: 9999px;
    }

    .sales-bar span.is-met { background-image: linear-gradient(90deg, #059669, #10b981); }
    .sales-bar span.is-close { background-image: linear-gradient(90deg, #d97706, #f59e0b); }
    .sales-bar span.is-behind { background-image: linear-gradient(90deg, #dc2626, #f87171); }
    .sales-bar span.is-none { background-color: #cbd5e1; }

    .sales-mini {
        padding: 0.75rem;
        border-radius: 0.75rem;
        background-color: #f9fafb;
        border: 1px solid #f1f5f9;
    }

    .dark .sales-mini {
        background-color: rgba(31, 41, 55, 0.5);
        border-color: #1f2937;
    }

    .sales-mini-label {
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #9ca3af;
    }

    .sales-mini-value {
        margin-top: 0.125rem;
        font-size: 1.125rem;
        font-weight: 800;
        color: #111827;
    }

    .dark .sales-mini-value {
        color: #f3f4f6;
    }

    /* ---- Leaderboard ---- */
    .board-row {
        display: grid;
        grid-template-columns: 1.75rem minmax(0, 1fr) auto;
        align-items: center;
        gap: 0.875rem;
        padding: 0.625rem 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .board-row:last-child { border-bottom: 0; }
    .dark .board-row { border-bottom-color: #1f2937; }

    .board-rank {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        background-color: #f3f4f6;
        color: #6b7280;
    }

    .dark .board-rank { background-color: #1f2937; color: #9ca3af; }

    .board-rank.rank-1 { background-image: linear-gradient(135deg, #f59e0b, #fbbf24); color: #ffffff; }
    .board-rank.rank-2 { background-image: linear-gradient(135deg, #94a3b8, #cbd5e1); color: #ffffff; }
    .board-rank.rank-3 { background-image: linear-gradient(135deg, #b45309, #d97706); color: #ffffff; }

    .board-name {
        font-size: 0.875rem;
        font-weight: 700;
        color: #111827;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dark .board-name { color: #f3f4f6; }

    .board-tag {
        margin-left: 0.375rem;
        padding: 0.0625rem 0.375rem;
        border-radius: 9999px;
        font-size: 0.5625rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        background-color: #FFF3EC;
        color: #C63508;
    }

    .dark .board-tag { background-color: rgba(255, 90, 31, 0.15); color: #FFA26E; }

    .board-meta {
        margin-top: 0.25rem;
        font-size: 0.6875rem;
        color: #9ca3af;
    }

    .board-amount {
        font-size: 0.9375rem;
        font-weight: 800;
        color: #111827;
    }

    .dark .board-amount { color: #f3f4f6; }

    .board-row.is-first .board-amount { color: #b45309; }
    .dark .board-row.is-first .board-amount { color: #fbbf24; }
</style>
@endpush

@section('content')
<div class="p-6 space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Welcome back, {{ Auth::user()->name }}! Here's your order overview.</p>
        </div>
        <div class="flex flex-wrap gap-3 items-center">
            {{-- The month is now chosen once from the header picker and applies
                 to every dashboard; this page just reports which one is active. --}}
            <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 text-sm font-semibold">
                <i class="fas fa-calendar-alt text-xs"></i>
                {{ $availableMonths[$selectedMonth] ?? $selectedMonth }}
            </span>
            <a href="{{ route('orders.create') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-all shadow-lg shadow-indigo-200 dark:shadow-none hover:shadow-xl">
                <i class="fas fa-plus mr-2"></i>New Order
            </a>
        </div>
    </div>

    <!-- Analytics Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Revenue Change -->
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-md p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <span class="text-green-100 text-sm font-medium">Revenue vs Last Month</span>
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-chart-line text-white"></i>
                </div>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-bold">{{ number_format(abs($analytics['revenueChange']), 1) }}%</span>
                <span class="text-lg mb-1">
                    @if($analytics['revenueChange'] > 0)
                        <i class="fas fa-arrow-up"></i>
                    @elseif($analytics['revenueChange'] < 0)
                        <i class="fas fa-arrow-down"></i>
                    @else
                        <i class="fas fa-minus"></i>
                    @endif
                </span>
            </div>
            <p class="text-green-100 text-xs mt-2">{{ $analytics['actualRevenue'] > 0 ? '$' . number_format($analytics['actualRevenue']) : '$0' }} this month</p>
        </div>

        <!-- Orders Change -->
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-md p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <span class="text-blue-100 text-sm font-medium">Orders vs Last Month</span>
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-white"></i>
                </div>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-bold">{{ number_format(abs($analytics['ordersChange']), 1) }}%</span>
                <span class="text-lg mb-1">
                    @if($analytics['ordersChange'] > 0)
                        <i class="fas fa-arrow-up"></i>
                    @elseif($analytics['ordersChange'] < 0)
                        <i class="fas fa-arrow-down"></i>
                    @else
                        <i class="fas fa-minus"></i>
                    @endif
                </span>
            </div>
            <p class="text-blue-100 text-xs mt-2">{{ $stats['total'] }} orders this month</p>
        </div>

        <!-- Completion Rate -->
        <div class="bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl shadow-md p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <span class="text-purple-100 text-sm font-medium">Completion Rate</span>
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-check-double text-white"></i>
                </div>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-bold">{{ $stats['total'] > 0 ? number_format(($stats['delivered'] / $stats['total']) * 100, 1) : 0 }}%</span>
            </div>
            <p class="text-purple-100 text-xs mt-2">{{ $stats['delivered'] }} of {{ $stats['total'] }} delivered</p>
        </div>

        <!-- High Priority Alert -->
        <div class="bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl shadow-md p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <span class="text-red-100 text-sm font-medium">High Priority</span>
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
            </div>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-bold">{{ $analytics['highPriorityCount'] }}</span>
            </div>
            <p class="text-red-100 text-xs mt-2">{{ $analytics['overdueCount'] }} overdue</p>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="dash-grid-4 dash-grid-5-at-xl">
        @php
            $myTasks = \App\Models\Task::where('assigned_to', Auth::id())->where('status', '!=', 'completed')->count();
        @endphp

        <!-- Total Orders -->
        <div class="dash-panel stat-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="dash-stat-label mb-1">Total Orders</p>
                    <p class="dash-hero dash-num">{{ number_format($stats['total']) }}</p>
                    <p class="dash-stat-foot"><i class="fas fa-folder mr-1"></i>All orders</p>
                </div>
                <div class="dash-icon" style="--icon-from: #FF5A1F; --icon-to: #F59A0F;">
                    <i class="fas fa-folder"></i>
                </div>
            </div>
        </div>

        <!-- Delivered Orders (Revenue Generating) -->
        <div class="dash-panel stat-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="dash-stat-label mb-1">Delivered</p>
                    <p class="dash-hero dash-num">{{ number_format($stats['delivered']) }}</p>
                    <p class="dash-stat-foot text-green-600 dark:text-green-400"><i class="fas fa-check-circle mr-1"></i>Revenue earned</p>
                </div>
                <div class="dash-icon" style="--icon-from: #10b981; --icon-to: #34d399;">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <!-- Workload (Active Orders) -->
        <div class="dash-panel stat-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="dash-stat-label mb-1">Workload</p>
                    <p class="dash-hero dash-num">{{ number_format($stats['workload']) }}</p>
                    <p class="dash-stat-foot text-blue-600 dark:text-blue-400 dash-num"><i class="fas fa-dollar-sign mr-1"></i>${{ number_format($stats['workloadValue'], 0) }} running value</p>
                </div>
                <div class="dash-icon" style="--icon-from: #3b82f6; --icon-to: #60a5fa;">
                    <i class="fas fa-spinner"></i>
                </div>
            </div>
        </div>

        <!-- Delivered Revenue -->
        <div class="dash-panel stat-card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="dash-stat-label mb-1">Delivered Revenue</p>
                    <p class="dash-hero dash-num">${{ number_format($stats['revenue'], 0) }}</p>
                    <p class="dash-stat-foot text-purple-600 dark:text-purple-400 dash-num"><i class="fas fa-check-circle mr-1"></i>{{ $stats['delivered'] }} orders this month</p>
                </div>
                <div class="dash-icon" style="--icon-from: #F59A0F; --icon-to: #FFB238;">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>

        <!-- Sales This Month (gross sold, before delivery) -->
        <div class="dash-panel stat-card p-6">
            <div class="flex items-center justify-between">
                <div class="min-w-0">
                    <p class="dash-stat-label mb-1">Sales This Month</p>
                    <p class="dash-hero dash-num">${{ number_format($salesStats['sold'], 0) }}</p>
                    <p class="dash-stat-foot dash-num">
                        @if($salesStats['achievement'] !== null)
                            <span class="{{ $salesStats['achievement'] >= 100 ? 'text-green-600 dark:text-green-400' : ($salesStats['achievement'] >= 60 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
                                <i class="fas fa-bullseye mr-1"></i>{{ $salesStats['achievement'] }}% of target
                            </span>
                        @else
                            <span class="text-amber-600 dark:text-amber-400">
                                <i class="fas fa-sack-dollar mr-1"></i>{{ $salesStats['count'] }} {{ \Illuminate\Support\Str::plural('order', $salesStats['count']) }} sold
                            </span>
                        @endif
                    </p>
                </div>
                <div class="dash-icon" style="--icon-from: #f59e0b; --icon-to: #f97316;">
                    <i class="fas fa-sack-dollar"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= SALES ================= -->
    @php
        $salesAchievement = $salesStats['achievement'];
        $salesTone = $salesAchievement === null
            ? 'is-none'
            : ($salesAchievement >= 100 ? 'is-met' : ($salesAchievement >= 60 ? 'is-close' : 'is-behind'));
        $topAmount = max(1, (float) collect($leaderboard)->max('amount'));
    @endphp

    <div class="sales-band">
        <div class="flex flex-wrap items-baseline justify-between gap-3 mb-4">
            <h2 class="sales-band-title">
                <span class="sales-band-dot"></span>
                Sales
            </h2>
            <a href="{{ route('sales.dashboard') }}" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                Full sales dashboard <i class="fas fa-arrow-right text-xs ml-1"></i>
            </a>
        </div>

        <!-- Quarterly Sales Progress Chart (Full Width) -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md p-6 border border-gray-100 dark:border-gray-800 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">
                    <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>Quarterly Sales Progress
                </h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">Current Quarter</span>
            </div>
            <div class="h-64 relative">
                <canvas id="quarterTrendChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Sold this month, against target -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md p-6 border border-gray-100 dark:border-gray-800">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Company Sold in {{ $availableMonths[$selectedMonth] ?? '' }}</p>
                        <p class="sales-hero">${{ number_format($salesStats['sold'], 0) }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                            @if($salesStats['change'] !== null)
                                <span class="sales-delta {{ $salesStats['change'] >= 0 ? 'is-up' : 'is-down' }}">
                                    <i class="fas fa-arrow-{{ $salesStats['change'] >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($salesStats['change']) }}%
                                </span>
                            @endif
                            <span class="text-gray-500 dark:text-gray-400 sales-num">
                                {{ $salesStats['count'] }} {{ \Illuminate\Support\Str::plural('order', $salesStats['count']) }} ·
                                ${{ number_format($salesStats['averageDeal'], 0) }} average deal
                            </span>
                        </div>
                    </div>
                    <div class="gradient-icon w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0" style="--icon-from: #f59e0b; --icon-to: #f97316;">
                        <i class="fas fa-sack-dollar text-white text-xl"></i>
                    </div>
                </div>

                @if($salesStats['target'] > 0)
                    <div class="mt-5 pt-4 border-t border-dashed border-gray-200 dark:border-gray-800">
                        <div class="flex items-baseline justify-between gap-3 mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Company target</span>
                            <span class="sales-pct {{ $salesTone }} sales-num">{{ $salesAchievement }}%</span>
                        </div>
                        <div class="sales-bar">
                            <span class="{{ $salesTone }}" style="width: {{ min(100, $salesAchievement) }}%"></span>
                        </div>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 sales-num">
                            ${{ number_format($salesStats['sold'], 0) }} of ${{ number_format($salesStats['target'], 0) }}
                            @if($salesStats['remaining'] > 0)
                                · <strong>${{ number_format($salesStats['remaining'], 0) }} to go</strong>
                            @else
                                · target met
                            @endif
                        </p>
                    </div>
                @else
                    <div class="mt-5 pt-4 border-t border-dashed border-gray-200 dark:border-gray-800">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            No sales targets set yet — add a monthly target on a sales person's profile to track achievement here.
                        </p>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-3 mt-5">
                    <div class="sales-mini">
                        <p class="sales-mini-label">Sold, still in delivery</p>
                        <p class="sales-mini-value sales-num">${{ number_format($salesStats['inDelivery'], 0) }}</p>
                    </div>
                    <div class="sales-mini">
                        <p class="sales-mini-label">Sales people</p>
                        <p class="sales-mini-value sales-num">{{ $salesStats['sellers'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Top performers -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md p-6 border border-gray-100 dark:border-gray-800">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-trophy text-amber-500 mr-2"></i>Company Top performers
                    </h3>
                    <span class="text-xs text-gray-400 dark:text-gray-500">this month</span>
                </div>

                @forelse($leaderboard as $index => $seller)
                    @php
                        $sellerTone = $seller['achievement'] === null
                            ? 'is-none'
                            : ($seller['achievement'] >= 100 ? 'is-met' : ($seller['achievement'] >= 60 ? 'is-close' : 'is-behind'));
                    @endphp
                    <div class="board-row {{ $index === 0 && $seller['amount'] > 0 ? 'is-first' : '' }}">
                        <span class="board-rank rank-{{ $index + 1 }}">{{ $index + 1 }}</span>
                        <div class="min-w-0">
                            <p class="board-name">
                                {{ $seller['name'] }}
                                @if($seller['is_leader'])
                                    <span class="board-tag">Leader</span>
                                @endif
                            </p>
                            <div class="sales-bar is-slim">
                                <span class="{{ $sellerTone }}"
                                      style="width: {{ $seller['achievement'] === null ? round($seller['amount'] / $topAmount * 100) : min(100, $seller['achievement']) }}%"></span>
                            </div>
                            <p class="board-meta sales-num">
                                {{ $seller['count'] }} {{ \Illuminate\Support\Str::plural('order', $seller['count']) }}
                                @if($seller['achievement'] !== null)
                                    · {{ $seller['achievement'] }}% of ${{ number_format($seller['target'], 0) }}
                                @else
                                    · no target set
                                @endif
                            </p>
                            @php
                                $qSeller = collect($quarterBreakdown)->firstWhere('id', $seller['id']);
                            @endphp
                            @if($qSeller)
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1 sales-num">
                                    Quarter: <span class="font-bold text-indigo-600 dark:text-indigo-400">${{ number_format($qSeller['quarter_amount']) }}</span> 
                                    ({{ $quarterNames['q1'] }}: ${{ number_format($qSeller['q1_amount']) }} · 
                                    {{ $quarterNames['q2'] }}: ${{ number_format($qSeller['q2_amount']) }} · 
                                    {{ $quarterNames['q3'] }}: ${{ number_format($qSeller['q3_amount']) }})
                                </p>
                            @endif
                        </div>
                        <p class="board-amount sales-num">${{ number_format($seller['amount'], 0) }}</p>
                    </div>
                @empty
                    <div class="py-8 text-center">
                        <i class="fas fa-trophy text-3xl text-gray-200 dark:text-gray-700"></i>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nobody has sold anything this month yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - 2/3 width -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Daily Revenue Chart - Full Width -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md p-6 border border-gray-100 dark:border-gray-800">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Delivered Revenue</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $availableMonths[$selectedMonth] ?? 'Selected Month' }} - Daily (Days 1-{{ $daysInMonth }})</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-green-100 dark:bg-green-500/10 flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
                <div class="chart-container" style="position: relative; height: 250px; width: 100%;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Daily Orders & Delivered vs Workload - Side by Side -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Daily Orders Chart -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md p-6 border border-gray-100 dark:border-gray-800">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Daily Orders</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $availableMonths[$selectedMonth] ?? 'Selected Month' }} - All Orders</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-blue-600 dark:text-blue-400"></i>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 200px; width: 100%;">
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>

                <!-- Delivered vs Workload -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md p-6 border border-gray-100 dark:border-gray-800">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Delivered vs Workload</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">Revenue-generating vs Active</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/10 flex items-center justify-center">
                            <i class="fas fa-chart-pie text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 200px; width: 100%;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Revenue by Team Chart -->
            @if($revenueByTeam->isNotEmpty())
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md p-6 border border-gray-100 dark:border-gray-800">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Revenue by Team</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $availableMonths[$selectedMonth] ?? 'Selected Month' }} - delivered revenue</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-500/10 flex items-center justify-center">
                        <i class="fas fa-users text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                </div>
                <div class="chart-container" style="position: relative; height: 250px; width: 100%;">
                    <canvas id="teamRevenueChart"></canvas>
                </div>
            </div>
            @endif

            <!-- Recent Orders Table - Delivered & Workload -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-100 dark:border-gray-800 ">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Recent Delivered</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">Revenue-generating orders</p>
                    </div>
                    <a href="{{ route('orders.index') }}?status=completed" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($recentDelivered as $order)
                        <a href="{{ route('orders.show', $order) }}" class="order-card block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-green-100 dark:bg-green-500/10">
                                        <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $order->name }}</p>
                                            @if($order->priority === 'high')
                                                <span class="flex-shrink-0 px-2 py-0.5 bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 text-xs font-medium rounded-full">High</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">
                                            @if($order->team)
                                                <span><i class="fas fa-users mr-1"></i>{{ $order->team->name }}</span>
                                            @endif
                                            @if($order->actual_revenue && $order->actual_revenue > 0)
                                                <span><i class="fas fa-dollar-sign mr-1"></i>{{ number_format($order->actual_revenue) }}</span>
                                            @endif
                                            @if($order->delivered_at)
                                                <span><i class="fas fa-check-circle mr-1 text-green-500"></i>{{ $order->delivered_at->format('M d') }}</span>
                                            @elseif($order->end_date)
                                                <span><i class="far fa-calendar mr-1"></i>Due {{ $order->end_date->format('M d') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 ml-4">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400">
                                        Delivered
                                    </span>
                                    <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-8 text-center">
                            <i class="fas fa-check-circle text-4xl text-gray-200 dark:text-gray-700 mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400 dark:text-gray-500">No delivered orders yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Workload Orders -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-100 dark:border-gray-800">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Current Workload</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">Active orders in progress</p>
                    </div>
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($recentWorkload as $order)
                        <a href="{{ route('orders.show', $order) }}" class="order-card block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center
                                        @if($order->status === 'in-progress') bg-blue-100 dark:bg-blue-500/10
                                        @elseif($order->status === 'on-hold') bg-yellow-100 dark:bg-yellow-500/10
                                        @else bg-gray-100 dark:bg-gray-700 @endif">
                                        <i class="fas fa-spinner
                                            @if($order->status === 'in-progress') text-blue-600 dark:text-blue-400
                                            @elseif($order->status === 'on-hold') text-yellow-600 dark:text-yellow-400
                                            @else text-gray-600 dark:text-gray-400 dark:text-gray-500 @endif"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $order->name }}</p>
                                            @if($order->priority === 'high')
                                                <span class="flex-shrink-0 px-2 py-0.5 bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 text-xs font-medium rounded-full">High</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">
                                            @if($order->team)
                                                <span><i class="fas fa-users mr-1"></i>{{ $order->team->name }}</span>
                                            @endif
                                            @if($order->end_date)
                                                <span><i class="far fa-calendar mr-1"></i>{{ $order->end_date->format('M d') }}</span>
                                                @if($order->end_date->isPast())
                                                    <span class="text-red-600 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-1"></i>Overdue</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 ml-4">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        @if($order->status === 'in-progress') bg-blue-100 dark:bg-blue-500/10 text-blue-800 dark:text-blue-400
                                        @elseif($order->status === 'on-hold') bg-yellow-100 dark:bg-yellow-500/10 text-yellow-800 dark:text-yellow-400
                                        @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @endif">
                                        {{ ucfirst(str_replace('-', ' ', $order->status)) }}
                                    </span>
                                    <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-8 text-center">
                            <i class="fas fa-spinner text-4xl text-gray-200 dark:text-gray-700 mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400 dark:text-gray-500">No active orders</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- My Tasks Section -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-100 dark:border-gray-800 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">My Tasks</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $myTasks }} tasks assigned to you</p>
                    </div>
                    <a href="{{ route('tasks.index') }}" class="inline-flex items-center text-indigo-600 dark:text-indigo-400 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @php
                        $myTasks = \App\Models\Task::with(['order', 'assignedTo'])
                            ->where('assigned_to', Auth::id())
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    @forelse($myTasks as $task)
                        <a href="{{ route('tasks.show', $task) }}" class="block px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                        @if($task->status === 'completed') bg-green-100 dark:bg-green-500/10
                                        @elseif($task->priority === 'urgent') bg-red-100 dark:bg-red-500/10
                                        @elseif($task->priority === 'high') bg-orange-100 dark:bg-orange-500/10
                                        @elseif($task->status === 'in-progress') bg-blue-100 dark:bg-blue-500/10
                                        @else bg-gray-100 dark:bg-gray-700 @endif">
                                        @if($task->status === 'completed')
                                            <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                                        @elseif($task->priority === 'urgent')
                                            <i class="fas fa-exclamation text-red-600 dark:text-red-400"></i>
                                        @elseif($task->priority === 'high')
                                            <i class="fas fa-arrow-up text-orange-600 dark:text-orange-400"></i>
                                        @elseif($task->status === 'in-progress')
                                            <i class="fas fa-spinner text-blue-600 dark:text-blue-400"></i>
                                        @else
                                            <i class="fas fa-circle text-gray-400 dark:text-gray-500"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $task->title }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $task->order?->name ?? 'No order' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($task->due_date)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500 mb-1">
                                            <i class="far fa-calendar mr-1"></i>{{ $task->due_date->format('M d') }}
                                        </p>
                                    @endif
                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full
                                        @if($task->status === 'completed') bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400
                                        @elseif($task->status === 'in-progress') bg-blue-100 dark:bg-blue-500/10 text-blue-800 dark:text-blue-400
                                        @elseif($task->status === 'pending') bg-yellow-100 dark:bg-yellow-500/10 text-yellow-800 dark:text-yellow-400
                                        @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @endif">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-8 text-center">
                            <i class="fas fa-tasks text-4xl text-gray-200 dark:text-gray-700 mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400 dark:text-gray-500">No tasks assigned to you</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column - 1/3 width -->
        <div class="space-y-6">
            <!-- Order Priority Breakdown -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md p-6 border border-gray-100 dark:border-gray-800">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Priority</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">By urgency</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-500/10 flex items-center justify-center">
                        <i class="fas fa-flag text-orange-600 dark:text-orange-400"></i>
                    </div>
                </div>
                @php
                    $totalOrders = max(array_sum($priorityStats), 1);
                @endphp
                <div class="space-y-4">
                    @foreach(['urgent' => ['color' => 'red', 'icon' => 'fas fa-fire'], 'high' => ['color' => 'orange', 'icon' => 'fas fa-arrow-up'], 'medium' => ['color' => 'yellow', 'icon' => 'fas fa-minus'], 'low' => ['color' => 'green', 'icon' => 'fas fa-arrow-down']] as $priority => $config)
                        @if($priorityStats[$priority] > 0)
                            <div>
                                <div class="flex items-center justify-between text-sm mb-2">
                                    <span class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                        <i class="{{ $config['icon'] }} text-{{ $config['color'] }}-600"></i>
                                        <span class="font-medium capitalize">{{ $priority }}</span>
                                    </span>
                                    <span class="font-bold text-gray-900 dark:text-gray-100">{{ $priorityStats[$priority] }}</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                    <div class="progress-bar h-2 rounded-full bg-{{ $config['color'] }}-500"
                                         style="width: {{ ($priorityStats[$priority] / $totalOrders) * 100 }}%"></div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Tasks Overview -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md p-6 border border-gray-100 dark:border-gray-800">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tasks</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $taskStats['completed'] }} of {{ $taskStats['total'] }} completed</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-500/10 flex items-center justify-center">
                        <i class="fas fa-tasks text-purple-600 dark:text-purple-400"></i>
                    </div>
                </div>
                <div class="chart-container" style="position: relative; height: 190px; width: 100%;">
                    <canvas id="taskStatusChart"></canvas>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-5">
                    @foreach(['urgent' => 'red', 'high' => 'orange', 'medium' => 'yellow', 'low' => 'green'] as $priority => $color)
                        @if(($taskStats['priority'][$priority] ?? 0) > 0)
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-1.5 text-gray-600 dark:text-gray-400 capitalize">
                                    <span class="w-2 h-2 rounded-full bg-{{ $color }}-500"></span>{{ $priority }}
                                </span>
                                <span class="font-bold text-gray-900 dark:text-gray-100">{{ $taskStats['priority'][$priority] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-between text-xs">
                    @if($taskStats['overdue'] > 0)
                        <span class="text-red-600 dark:text-red-400 font-semibold"><i class="fas fa-exclamation-triangle mr-1"></i>{{ $taskStats['overdue'] }} overdue</span>
                    @else
                        <span class="text-green-600 dark:text-green-400 font-semibold"><i class="fas fa-check mr-1"></i>No overdue</span>
                    @endif
                    <a href="{{ route('tasks.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">View all <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>

            <!-- Team Performance -->
            @can('view teams')
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md p-6 border border-gray-100 dark:border-gray-800">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Teams</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">Deliveries this month</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
                <div class="space-y-3">
                    @forelse($teams as $team)
                        <a href="{{ route('teams.show', $team) }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors border border-gray-100 dark:border-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-500/10 flex items-center justify-center">
                                    <i class="fas fa-users text-indigo-600 dark:text-indigo-400"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $team->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $team->orders_count }} delivered</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
                        </a>
                    @empty
                        <div class="text-center py-6">
                            <i class="fas fa-users text-3xl text-gray-200 dark:text-gray-700 mb-2"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">No teams yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
            @endcan

            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl shadow-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-4">
                    <i class="fas fa-bolt mr-2"></i>Quick Actions
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('orders.create') }}" class="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 rounded-xl transition-colors">
                        <i class="fas fa-plus w-6"></i>
                        <span class="font-medium">Create Order</span>
                    </a>
                    <a href="{{ route('tasks.create') }}" class="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 rounded-xl transition-colors">
                        <i class="fas fa-tasks w-6"></i>
                        <span class="font-medium">Create Task</span>
                    </a>
                    <a href="{{ route('teams.create') }}" class="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 rounded-xl transition-colors">
                        <i class="fas fa-user-plus w-6"></i>
                        <span class="font-medium">Create Team</span>
                    </a>
                </div>
            </div>

            <!-- Upcoming Deadlines -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md p-6 border border-gray-100 dark:border-gray-800">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Due Soon</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">Orders ending soon</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-500/10 flex items-center justify-center">
                        <i class="fas fa-clock text-red-600 dark:text-red-400"></i>
                    </div>
                </div>
                <div class="space-y-3">
                    @forelse($dueSoon as $order)
                        <a href="{{ route('orders.show', $order) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors border border-gray-100 dark:border-gray-800">
                            <div class="w-10 h-10 rounded-lg @if($order->end_date->isPast()) bg-red-100 dark:bg-red-500/10 @elseif($order->end_date->diffInDays(now()) <= 3) bg-orange-100 dark:bg-orange-500/10 @else bg-yellow-100 dark:bg-yellow-500/10 @endif flex items-center justify-center">
                                <i class="fas fa-calendar @if($order->end_date->isPast()) text-red-600 dark:text-red-400 @elseif($order->end_date->diffInDays(now()) <= 3) text-orange-600 dark:text-orange-400 @else text-yellow-600 dark:text-yellow-400 @endif"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $order->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $order->end_date->diffForHumans() }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-6">
                            <i class="fas fa-calendar-check text-3xl text-gray-200 dark:text-gray-700 mb-2"></i>
                            <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500">No upcoming deadlines</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Global chart instances to prevent duplicates
let revenueChartInstance = null;
let ordersChartInstance = null;
let statusChartInstance = null;
let teamRevenueChartInstance = null;
let taskStatusChartInstance = null;
let quarterTrendChartInstance = null;

// Theme-aware chart colors (Chart.js bakes colors into the canvas, so this
// is read once at render time; the theme toggle does a full page reload)
const isDarkMode = document.documentElement.classList.contains('dark');
const chartGridColor = isDarkMode ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.05)';
const chartTickColor = isDarkMode ? '#9ca3af' : '#6b7280';

// Store order status data
const orderStatusData = {
    delivered: {{ $ordersByStatus['delivered'] ?? 0 }},
    pending: {{ $ordersByStatus['pending'] ?? 0 }},
    inProgress: {{ $ordersByStatus['in-progress'] ?? 0 }},
    onHold: {{ $ordersByStatus['on-hold'] ?? 0 }}
};

// Daily revenue data
const dailyRevenueData = {{ Js::from($dailyRevenue) }};
const dailyOrdersData = {{ Js::from($dailyOrders) }};
const daysInMonth = {{ $daysInMonth }};

document.addEventListener('DOMContentLoaded', function() {
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js failed to load');
        return;
    }

    // Destroy existing charts if they exist
    if (revenueChartInstance) {
        revenueChartInstance.destroy();
    }
    if (ordersChartInstance) {
        ordersChartInstance.destroy();
    }
    if (statusChartInstance) {
        statusChartInstance.destroy();
    }
    if (teamRevenueChartInstance) {
        teamRevenueChartInstance.destroy();
    }
    if (taskStatusChartInstance) {
        taskStatusChartInstance.destroy();
    }
    if (quarterTrendChartInstance) {
        quarterTrendChartInstance.destroy();
    }

    // Generate day labels for the selected month
    const dayLabels = Array.from({length: daysInMonth}, (_, i) => i + 1);

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        revenueChartInstance = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: dayLabels,
                datasets: [{
                    label: 'Delivered Revenue',
                    data: dailyRevenueData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 750
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 10,
                        callbacks: {
                            title: function(context) {
                                return 'Day ' + context[0].label;
                            },
                            label: function(context) {
                                return 'Revenue: $' + (context.parsed.y || 0).toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: chartGridColor },
                        ticks: {
                            color: chartTickColor,
                            callback: function(value) {
                                return '$' + (value >= 1000 ? (value / 1000).toFixed(0) + 'k' : value);
                            },
                            font: { size: 10 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: chartTickColor,
                            font: { size: 10 },
                            maxRotation: 0,
                            autoSkip: true,
                            autoSkipPadding: 20
                        }
                    }
                }
            }
        });
    }

    // Daily Orders Chart
    const ordersCtx = document.getElementById('ordersChart');
    if (ordersCtx) {
        ordersChartInstance = new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels: dayLabels,
                datasets: [{
                    label: 'Orders',
                    data: dailyOrdersData,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: '#3b82f6',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 750
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 10,
                        callbacks: {
                            title: function(context) {
                                return 'Day ' + context[0].label;
                            },
                            label: function(context) {
                                return 'Orders: ' + (context.parsed.y || 0);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: chartGridColor },
                        ticks: {
                            color: chartTickColor,
                            stepSize: 1,
                            font: { size: 10 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: chartTickColor,
                            font: { size: 10 },
                            maxTicksLimit: 10
                        }
                    }
                }
            }
        });
    }

    // Status Chart - Delivered vs Workload
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        const delivered = orderStatusData.delivered;
        const workload = orderStatusData.pending + orderStatusData.inProgress + orderStatusData.onHold;
        const total = delivered + workload;

        // Only show chart if there's data
        if (total > 0) {
            statusChartInstance = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Delivered (Revenue)', 'Workload (Active)'],
                    datasets: [{
                        data: [delivered, workload],
                        backgroundColor: ['#10b981', '#3b82f6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 750
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: chartTickColor,
                                padding: 12,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
    }

    // Revenue by Team Chart (horizontal bar)
    const teamRevenueCtx = document.getElementById('teamRevenueChart');
    if (teamRevenueCtx) {
        const revenueByTeam = @json($revenueByTeam->map(fn ($t) => ['label' => $t['team_name'], 'value' => (float) $t['revenue']]));
        if (revenueByTeam.length) {
            teamRevenueChartInstance = new Chart(teamRevenueCtx, {
                type: 'bar',
                data: {
                    labels: revenueByTeam.map(r => r.label),
                    datasets: [{
                        label: 'Revenue',
                        data: revenueByTeam.map(r => r.value),
                        backgroundColor: 'rgba(255, 90, 31, 0.8)',
                        borderColor: '#FF5A1F',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    animation: { duration: 750 },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: $' + (context.parsed.x || 0).toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { color: chartGridColor },
                            ticks: {
                                color: chartTickColor,
                                callback: function(value) {
                                    return '$' + (value >= 1000 ? (value / 1000).toFixed(0) + 'k' : value);
                                },
                                font: { size: 10 }
                            }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: chartTickColor, font: { size: 11 } }
                        }
                    }
                }
            });
        }
    }

    // Task Status Chart (doughnut)
    const taskStatusCtx = document.getElementById('taskStatusChart');
    if (taskStatusCtx) {
        const taskStatusData = [
            {{ $taskStats['status']['pending'] }},
            {{ $taskStats['status']['in-progress'] }},
            {{ $taskStats['status']['on-hold'] }},
            {{ $taskStats['status']['completed'] }}
        ];
        const taskTotal = taskStatusData.reduce((a, b) => a + b, 0);
        if (taskTotal > 0) {
            taskStatusChartInstance = new Chart(taskStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'In Progress', 'On Hold', 'Completed'],
                    datasets: [{
                        data: taskStatusData,
                        backgroundColor: ['#f59e0b', '#3b82f6', '#f97316', '#10b981'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 750 },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: chartTickColor,
                                padding: 12,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed || 0;
                                    const percentage = ((value / taskTotal) * 100).toFixed(1);
                                    return context.label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
        }

        const quarterCtx = document.getElementById('quarterTrendChart');
        if (quarterCtx) {
            const qBreakdown = @json($quarterBreakdown);
            const qNames = @json($quarterNames);
            
            quarterTrendChartInstance = new Chart(quarterCtx, {
                type: 'bar',
                data: {
                    labels: qBreakdown.map(s => s.name),
                    datasets: [
                        {
                            label: '1st Month (' + qNames.q1 + ')',
                            data: qBreakdown.map(s => s.q1_amount),
                            backgroundColor: 'rgba(59, 130, 246, 0.85)',
                            borderRadius: 4,
                        },
                        {
                            label: '2nd Month (' + qNames.q2 + ')',
                            data: qBreakdown.map(s => s.q2_amount),
                            backgroundColor: 'rgba(245, 158, 11, 0.85)',
                            borderRadius: 4,
                        },
                        {
                            label: '3rd Month (' + qNames.q3 + ')',
                            data: qBreakdown.map(s => s.q3_amount),
                            backgroundColor: 'rgba(245, 154, 15, 0.85)',
                            borderRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                color: chartTickColor,
                                boxWidth: 10,
                                boxHeight: 10,
                                usePointStyle: true,
                                font: { size: 10 }
                            },
                        },
                        tooltip: {
                            callbacks: {
                                label: item => ` ${item.dataset.label}: $${item.parsed.y.toLocaleString()}`,
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: chartTickColor, font: { size: 10, weight: '600' } },
                        },
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { color: chartGridColor },
                            ticks: {
                                color: chartTickColor,
                                font: { size: 10 },
                                callback: value => '$' + (value >= 1000 ? (value / 1000) + 'k' : value),
                            },
                        },
                    },
                }
            });
        }
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (revenueChartInstance) revenueChartInstance.destroy();
    if (ordersChartInstance) ordersChartInstance.destroy();
    if (statusChartInstance) statusChartInstance.destroy();
    if (teamRevenueChartInstance) teamRevenueChartInstance.destroy();
    if (taskStatusChartInstance) taskStatusChartInstance.destroy();
    if (quarterTrendChartInstance) quarterTrendChartInstance.destroy();
});
</script>
@endpush
@endsection
