@extends('layouts.dashboard')

@section('title', 'Sales Dashboard')

@push('styles')
<style>
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

    /* The one number this page exists for. */
    .s-hero-amount {
        font-size: clamp(2.75rem, 7vw, 4.25rem);
        line-height: 1;
        font-weight: 800;
        letter-spacing: -0.045em;
        color: #111827;
    }

    .dark .s-hero-amount {
        color: #ffffff;
    }

    .s-delta {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 800;
    }

    .s-delta.is-up {
        background-color: #d1fae5;
        color: #047857;
    }

    .s-delta.is-down {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    .s-delta.is-flat {
        background-color: #f3f4f6;
        color: #6b7280;
    }

    .dark .s-delta.is-up { background-color: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
    .dark .s-delta.is-down { background-color: rgba(239, 68, 68, 0.15); color: #fca5a5; }
    .dark .s-delta.is-flat { background-color: #1f2937; color: #9ca3af; }

    .s-stat-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #6b7280;
    }

    .dark .s-stat-label {
        color: #9ca3af;
    }

    .s-stat-value {
        margin-top: 0.25rem;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #111827;
        font-variant-numeric: tabular-nums;
    }

    .dark .s-stat-value {
        color: #f3f4f6;
    }

    .s-stat-foot {
        margin-top: 0.125rem;
        font-size: 0.75rem;
        color: #9ca3af;
    }

    .s-grid-4 {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 1rem;
    }

    @media (min-width: 640px) {
        .s-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    @media (min-width: 1024px) {
        .s-grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }

    .s-split {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 1.25rem;
        align-items: start;
    }

    @media (min-width: 1280px) {
        .s-split { grid-template-columns: minmax(0, 2fr) minmax(20rem, 1fr); }
    }

    .s-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .s-row:last-child { border-bottom: 0; }
    .dark .s-row { border-bottom-color: #1f2937; }

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
        background-image: linear-gradient(90deg, #4f46e5 0%, #8b5cf6 100%);
    }

    .s-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.625rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        background-color: #eef2ff;
        color: #4338ca;
    }

    .dark .s-chip { background-color: rgba(99, 102, 241, 0.15); color: #a5b4fc; }

    .s-team-context {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 1rem;
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px dashed #e5e7eb;
    }

    @media (min-width: 640px) {
        .s-team-context {
            grid-template-columns: auto minmax(12rem, 20rem);
            align-items: end;
            justify-content: space-between;
        }
    }

    .dark .s-team-context {
        border-top-color: #1f2937;
    }

    /* Target achievement */
    .s-target {
        margin-top: 1.5rem;
        padding-top: 1.25rem;
        border-top: 1px dashed #e5e7eb;
    }

    .dark .s-target {
        border-top-color: #1f2937;
    }

    .s-target-head {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.5rem;
    }

    .s-target-pct {
        font-size: 1.375rem;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .s-target-pct.is-met { color: #059669; }
    .s-target-pct.is-close { color: #d97706; }
    .s-target-pct.is-behind { color: #dc2626; }

    .dark .s-target-pct.is-met { color: #34d399; }
    .dark .s-target-pct.is-close { color: #fbbf24; }
    .dark .s-target-pct.is-behind { color: #f87171; }

    .s-target-bar {
        position: relative;
        height: 0.625rem;
        border-radius: 9999px;
        background-color: #eef2f7;
        overflow: hidden;
    }

    .s-target-bar.is-slim {
        height: 0.3125rem;
        margin-top: 0.375rem;
    }

    .dark .s-target-bar {
        background-color: #1f2937;
    }

    .s-target-bar span {
        display: block;
        height: 100%;
        border-radius: 9999px;
        transition: width 400ms ease;
    }

    .s-target-bar span.is-met { background-image: linear-gradient(90deg, #059669 0%, #10b981 100%); }
    .s-target-bar span.is-close { background-image: linear-gradient(90deg, #d97706 0%, #f59e0b 100%); }
    .s-target-bar span.is-behind { background-image: linear-gradient(90deg, #dc2626 0%, #f87171 100%); }
    .s-target-bar span.is-none { background-color: #d1d5db; }

    .s-target-foot {
        margin-top: 0.5rem;
        font-size: 0.75rem;
        color: #6b7280;
    }

    .dark .s-target-foot {
        color: #9ca3af;
    }

    .s-target-tag {
        padding: 0.0625rem 0.4375rem;
        border-radius: 9999px;
        font-weight: 700;
    }

    .s-target-tag.is-met { background-color: #d1fae5; color: #047857; }
    .s-target-tag.is-close { background-color: #fef3c7; color: #b45309; }
    .s-target-tag.is-behind { background-color: #fee2e2; color: #b91c1c; }
    .s-target-tag.is-none { background-color: #f3f4f6; color: #9ca3af; }

    .dark .s-target-tag.is-met { background-color: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
    .dark .s-target-tag.is-close { background-color: rgba(245, 158, 11, 0.15); color: #fcd34d; }
    .dark .s-target-tag.is-behind { background-color: rgba(239, 68, 68, 0.15); color: #fca5a5; }
    .dark .s-target-tag.is-none { background-color: #1f2937; color: #6b7280; }

    .s-primary-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: #ffffff;
        background-image: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%);
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

    .s-empty {
        border: 1px dashed #e5e7eb;
        border-radius: 0.875rem;
        padding: 2rem 1.5rem;
        text-align: center;
    }

    .dark .s-empty { border-color: #1f2937; }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $isPreview = $isPreview ?? false;
    // A member leads with their own number and reads the team total as context;
    // leaders and admins lead with the group.
    $heroStats = $mine && ! $showsBreakdown ? $mine : $group;
    $heroLabel = $mine && ! $showsBreakdown ? 'You sold' : ($isAdmin ? 'All sales' : 'Your team sold');
    $delta = $heroStats['changePercent'];
    $deltaClass = $delta === null ? 'is-flat' : ($delta > 0 ? 'is-up' : ($delta < 0 ? 'is-down' : 'is-flat'));
    $topAmount = max(1, (float) $trend->max('amount'));
    $myShare = $mine && $group['amount'] > 0 ? round($mine['amount'] / $group['amount'] * 100) : 0;
    $showsTeamContext = $mine !== null && ! $showsBreakdown && count($trend) > 0;
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-7xl mx-auto space-y-5">

    @if($isPreview)
        <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 rounded-xl bg-indigo-600 text-white text-sm font-semibold">
            <span><i class="fas fa-eye mr-2"></i>Preview — sample data. This is what a sales member's own dashboard looks like; no real account is shown.</span>
            <a href="{{ route('sales.leadership') }}" class="underline hover:no-underline">Back to Leadership Overview</a>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="s-eyebrow">{{ $isPreview ? 'Preview' : ($isAdmin ? 'All sales' : ($user->isSalesLeader() ? 'Sales team' : 'My sales')) }}</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">
                {{ $isPreview ? 'Sales member dashboard' : ($isAdmin ? 'Sales performance' : ($user->isSalesLeader() ? 'Sales performance' : 'Hello, '.explode(' ', $user->name)[0])) }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ $monthDate->format('F Y') }}
                @if($isPreview)
                    · sample figures, for layout only
                @elseif($isAdmin)
                    · every sales person
                @elseif($user->isSalesLeader())
                    · you and your sales members
                @else
                    · your orders, next to your team's
                @endif
            </p>
        </div>

        @unless($isPreview)
            <div class="flex flex-wrap items-center gap-2">
                @can('create', \App\Models\Order::class)
                    <a href="{{ route('orders.create') }}" class="s-primary-btn">
                        <i class="fas fa-plus mr-2"></i>{{ $user->isSales() ? 'New sale' : 'New order' }}
                    </a>
                @endcan

                <a href="{{ route('orders.index') }}" class="s-secondary-btn">
                    <i class="fas fa-list mr-2"></i>{{ $user->isSales() ? 'My orders' : 'All orders' }}
                </a>
            </div>
        @endunless
    </div>

    <!-- Sales amount: the headline -->
    <div class="s-panel p-6 sm:p-8">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div>
                <p class="s-eyebrow">{{ $heroLabel }} in {{ $monthDate->format('F') }}</p>
                <p class="s-hero-amount s-num mt-2">${{ number_format($heroStats['amount'], 2) }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <span class="s-delta {{ $deltaClass }}">
                        @if($delta === null)
                            No sales last month
                        @else
                            <i class="fas fa-arrow-{{ $delta >= 0 ? 'up' : 'down' }} text-[10px]"></i>
                            {{ abs($delta) }}% vs {{ $monthDate->copy()->subMonth()->format('F') }}
                        @endif
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 s-num">
                        {{ $heroStats['count'] }} {{ \Illuminate\Support\Str::plural('order', $heroStats['count']) }} ·
                        ${{ number_format($heroStats['averageDeal'], 2) }} average deal
                    </span>
                </div>
            </div>

            <div class="text-left lg:text-right">
                <p class="s-eyebrow">Best month in the last year</p>
                <p class="mt-1 text-lg font-bold text-gray-900 dark:text-gray-100 s-num">
                    ${{ number_format((float) ($bestMonth['amount'] ?? 0), 2) }}
                    <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">in {{ $bestMonth['label'] ?? '—' }}</span>
                </p>
            </div>
        </div>

        @if($heroStats['target'] > 0)
            @php
                $achieved = $heroStats['achievement'];
                $barWidth = min(100, $achieved);
                $tone = $achieved >= 100 ? 'is-met' : ($achieved >= 60 ? 'is-close' : 'is-behind');
            @endphp
            <div class="s-target">
                <div class="s-target-head">
                    <p class="s-eyebrow">{{ $showsBreakdown ? 'Team target' : 'Your target' }} · {{ $monthDate->format('F') }}</p>
                    <p class="s-target-pct {{ $tone }} s-num">{{ $achieved }}%</p>
                </div>
                <div class="s-target-bar">
                    <span class="{{ $tone }}" style="width: {{ $barWidth }}%"></span>
                </div>
                <p class="s-target-foot s-num">
                    ${{ number_format($heroStats['amount'], 2) }} of ${{ number_format($heroStats['target'], 2) }} minimum
                    @if($heroStats['remaining'] > 0)
                        · <strong>${{ number_format($heroStats['remaining'], 2) }} to go</strong>
                    @else
                        · target met
                    @endif
                </p>
            </div>
        @endif

        @if($showsTeamContext)
            <div class="s-team-context">
                <div>
                    <p class="s-eyebrow">Your team sold</p>
                    <p class="mt-1 text-xl font-bold text-gray-900 dark:text-gray-100 s-num">${{ number_format($group['amount'], 2) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 s-num">
                        {{ $group['count'] }} {{ \Illuminate\Support\Str::plural('order', $group['count']) }} across the team
                    </p>
                </div>
                <div class="s-context-bar">
                    <div class="flex items-baseline justify-between gap-3 mb-1">
                        <span class="text-xs font-bold text-gray-600 dark:text-gray-400">Your share</span>
                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 s-num">{{ $myShare }}%</span>
                    </div>
                    <div class="s-share">
                        <span style="width: {{ $myShare }}%"></span>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-6">
            <canvas id="salesTrendChart" height="90"></canvas>
        </div>
    </div>

    <!-- Supporting numbers -->
    <div class="s-grid-4">
        <div class="s-panel p-5">
            <p class="s-stat-label">Orders sold</p>
            <p class="s-stat-value">{{ $heroStats['count'] }}</p>
            <p class="s-stat-foot">in {{ $monthDate->format('F Y') }}</p>
        </div>
        <div class="s-panel p-5">
            <p class="s-stat-label">Average deal</p>
            <p class="s-stat-value">${{ number_format($heroStats['averageDeal'], 2) }}</p>
            <p class="s-stat-foot">sold value ÷ orders</p>
        </div>
        <div class="s-panel p-5">
            <p class="s-stat-label">Delivered this month</p>
            <p class="s-stat-value">${{ number_format($heroStats['deliveredAmount'], 2) }}</p>
            <p class="s-stat-foot s-num">{{ $heroStats['deliveredCount'] }} {{ \Illuminate\Support\Str::plural('order', $heroStats['deliveredCount']) }} handed over</p>
        </div>
        <div class="s-panel p-5">
            <p class="s-stat-label">Still in delivery</p>
            <p class="s-stat-value">${{ number_format($heroStats['openAmount'], 2) }}</p>
            <div class="s-share mt-2">
                <span style="width: {{ $heroStats['openProgress'] }}%"></span>
            </div>
            <p class="s-stat-foot s-num">
                {{ $heroStats['openCount'] }} {{ \Illuminate\Support\Str::plural('order', $heroStats['openCount']) }} · {{ $heroStats['openProgress'] }}% average progress
            </p>
        </div>
    </div>

    <!-- Fresh vs. Repeat -->
    <div class="s-panel p-5 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
            <div>
                <p class="s-eyebrow">Fresh vs. Repeat · {{ $monthDate->format('F Y') }}</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Green is fresh (new clients), yellow is repeat (clients who already bought through the same profile).</p>
            </div>
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 text-xs font-bold s-num">
                <i class="fas fa-rotate-right text-[10px]"></i>{{ $heroStats['repeatRate'] }}% repeat orders
            </span>
        </div>

        @php
            $splitTotal = $heroStats['freshAmount'] + $heroStats['repeatAmount'];
            $freshPct = $splitTotal > 0 ? round($heroStats['freshAmount'] / $splitTotal * 100) : 0;
            $repeatPct = 100 - $freshPct;
        @endphp
        <div class="grid grid-cols-2 gap-4">
            <div class="rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 p-4">
                <p class="flex items-center gap-2 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>Fresh
                </p>
                <p class="mt-1 text-xl font-extrabold text-gray-900 dark:text-gray-100 s-num">${{ number_format($heroStats['freshAmount'], 2) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 s-num">{{ $heroStats['freshCount'] }} {{ \Illuminate\Support\Str::plural('order', $heroStats['freshCount']) }}</p>
            </div>
            <div class="rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 p-4">
                <p class="flex items-center gap-2 text-xs font-bold text-amber-700 dark:text-amber-400">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>Repeat
                </p>
                <p class="mt-1 text-xl font-extrabold text-gray-900 dark:text-gray-100 s-num">${{ number_format($heroStats['repeatAmount'], 2) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 s-num">{{ $heroStats['repeatCount'] }} {{ \Illuminate\Support\Str::plural('order', $heroStats['repeatCount']) }}</p>
            </div>
        </div>
        <div class="mt-3 h-2 rounded-full overflow-hidden flex">
            <div class="bg-emerald-500" style="width: {{ $freshPct }}%"></div>
            <div class="bg-amber-400" style="width: {{ $repeatPct }}%"></div>
        </div>
    </div>

    <div class="s-split">
        <!-- Recent orders -->
        <div class="s-panel p-5 sm:p-6">
            <div class="flex items-center justify-between mb-2">
                <p class="s-eyebrow">Running orders — everyone</p>
                <a href="{{ route('orders.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                    All orders
                </a>
            </div>

            @forelse($recentOrders as $order)
                <a href="{{ route('orders.show', $order) }}" class="s-row block sm:grid hover:opacity-80 transition-opacity">
                    <span>
                        <span class="block text-sm font-bold text-gray-900 dark:text-gray-100 truncate">
                            {{ $order->name }}
                            @if($order->sales_user_id === $user->id)
                                <span class="s-chip ml-1">You</span>
                            @endif
                        </span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $order->created_at->format('M d, Y') }}
                            @if($showsBreakdown && $order->salesUser)
                                · {{ $order->salesUser->name }}
                            @endif
                        </span>
                    </span>
                    <span class="text-right">
                        <span class="block text-sm font-bold text-gray-900 dark:text-gray-100 s-num">${{ number_format((float) $order->value, 2) }}</span>
                        <span class="s-chip mt-1">{{ ucfirst(str_replace('-', ' ', $order->status)) }}</span>
                    </span>
                </a>
            @empty
                <div class="s-empty">
                    @if($isPreview)
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">A real sales member's recent orders appear here</p>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">This preview doesn't show real order data.</p>
                    @else
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">No orders yet</p>
                        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Add the first one and operations takes it from there.</p>
                        @unless($isAdmin)
                            <a href="{{ route('orders.create') }}"
                               class="mt-3 inline-flex items-center px-3.5 py-2 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-plus mr-1.5"></i>Add an order
                            </a>
                        @endunless
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Who sold what (leaders only) -->
        <div class="space-y-5">
            @if($memberBreakdown->isNotEmpty())
                @php $breakdownTop = max(1, (float) $memberBreakdown->max('amount')); @endphp
                <div class="s-panel p-5">
                    <p class="s-eyebrow mb-2">{{ $isAdmin ? 'Every sales person' : 'Who sold what' }} · {{ $monthDate->format('F') }}</p>
                    @foreach($memberBreakdown as $seller)
                        <div class="py-2.5 border-b border-gray-100 dark:border-gray-800 last:border-0">
                            <div class="flex items-baseline justify-between gap-3">
                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                    {{ $seller['name'] }}
                                    @if($seller['is_you'])
                                        <span class="s-chip ml-1">You</span>
                                    @elseif($seller['is_leader'])
                                        <span class="s-chip ml-1">Leader</span>
                                    @endif
                                    @if($isAdmin && $seller['leader'])
                                        <span class="text-xs font-normal text-gray-400 dark:text-gray-500">· under {{ $seller['leader'] }}</span>
                                    @endif
                                </span>
                                <span class="text-sm font-bold text-gray-900 dark:text-gray-100 s-num">${{ number_format($seller['amount'], 2) }}</span>
                            </div>
                            @php
                                $sellerTone = $seller['achievement'] === null
                                    ? 'is-none'
                                    : ($seller['achievement'] >= 100 ? 'is-met' : ($seller['achievement'] >= 60 ? 'is-close' : 'is-behind'));
                            @endphp
                            <div class="s-target-bar is-slim">
                                <span class="{{ $sellerTone }}"
                                      style="width: {{ $seller['achievement'] === null ? round($seller['amount'] / $breakdownTop * 100) : min(100, $seller['achievement']) }}%"></span>
                            </div>
                            <p class="mt-1 flex flex-wrap items-center gap-x-2 text-xs text-gray-400 dark:text-gray-500 s-num">
                                <span>{{ $seller['count'] }} {{ \Illuminate\Support\Str::plural('order', $seller['count']) }}</span>
                                @if($seller['achievement'] === null)
                                    <span class="s-target-tag is-none">No target set</span>
                                @else
                                    <span class="s-target-tag {{ $sellerTone }}">{{ $seller['achievement'] }}% of ${{ number_format($seller['target'], 2) }}</span>
                                    @if($seller['remaining'] > 0)
                                        <span>${{ number_format($seller['remaining'], 2) }} to go</span>
                                    @endif
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="s-panel p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="s-eyebrow">Month by month</p>
                    <span class="flex items-center gap-2 text-[10px] font-bold text-gray-500 dark:text-gray-400">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Fresh</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400"></span>Repeat</span>
                    </span>
                </div>
                <div class="space-y-2">
                    @foreach($trend->reverse()->take(6) as $point)
                        <div>
                            <div class="flex items-baseline justify-between gap-3">
                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ $point['label'] }}</span>
                                <span class="text-xs font-bold text-gray-900 dark:text-gray-100 s-num">${{ number_format($point['amount'], 2) }}</span>
                            </div>
                            <div class="s-share">
                                <span style="width: {{ round($point['amount'] / $topAmount * 100) }}%"></span>
                            </div>
                            @php
                                $pointFreshPct = $point['amount'] > 0 ? round($point['fresh'] / $point['amount'] * 100) : 0;
                            @endphp
                            <div class="mt-1 h-1.5 rounded-full overflow-hidden flex">
                                <div class="bg-emerald-500" style="width: {{ $pointFreshPct }}%"></div>
                                <div class="bg-amber-400" style="width: {{ max(0, 100 - $pointFreshPct) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('salesTrendChart');

        if (!canvas || typeof Chart === 'undefined') {
            return;
        }

        const trend = @json($trend);
        const isDark = document.documentElement.classList.contains('dark');
        const context = canvas.getContext('2d');
        const gradient = context.createLinearGradient(0, 0, 0, 220);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.95)');
        gradient.addColorStop(1, 'rgba(139, 92, 246, 0.55)');

        new Chart(context, {
            type: 'bar',
            data: {
                labels: trend.map(point => point.short),
                datasets: [
                    {
                        label: @json($showsTeamContext ? 'Team' : 'Sold'),
                        data: trend.map(point => point.amount),
                        backgroundColor: gradient,
                        borderRadius: 6,
                        maxBarThickness: 42,
                    },
                    @if($showsTeamContext)
                    {
                        label: 'You',
                        data: trend.map(point => point.mine),
                        backgroundColor: 'rgba(16, 185, 129, 0.9)',
                        borderRadius: 6,
                        maxBarThickness: 42,
                    },
                    @endif
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: @json($showsTeamContext),
                        position: 'bottom',
                        labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, font: { size: 11 } },
                    },
                    tooltip: {
                        callbacks: {
                            title: items => trend[items[0].dataIndex].label,
                            label: item => item.dataset.label + ': $' +
                                item.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 2 }),
                        },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: isDark ? '#9ca3af' : '#6b7280', font: { size: 11, weight: '600' } },
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: isDark ? '#1f2937' : '#f1f5f9' },
                        ticks: {
                            color: isDark ? '#6b7280' : '#9ca3af',
                            font: { size: 11 },
                            callback: value => '$' + (value >= 1000 ? (value / 1000) + 'k' : value),
                        },
                    },
                },
            },
        });
    });
</script>
@endpush
@endsection
