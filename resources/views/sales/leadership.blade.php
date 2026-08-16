@extends('layouts.dashboard')

@section('title', 'Sales Leadership')

@push('styles')
<style>
    .s-panel {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
    }

    .dark .s-panel { background-color: #111827; border-color: #1f2937; box-shadow: none; }

    .s-eyebrow {
        font-size: 0.6875rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #6b7280;
    }

    .dark .s-eyebrow { color: #9ca3af; }

    .s-num { font-variant-numeric: tabular-nums; font-feature-settings: 'tnum'; }

    .s-hero-amount {
        font-size: clamp(2.5rem, 6vw, 3.75rem);
        line-height: 1;
        font-weight: 800;
        letter-spacing: -0.04em;
        color: #111827;
    }

    .dark .s-hero-amount { color: #ffffff; }

    .s-delta {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 800;
    }

    .s-delta.is-up { background-color: #d1fae5; color: #047857; }
    .s-delta.is-down { background-color: #fee2e2; color: #b91c1c; }
    .s-delta.is-flat { background-color: #f3f4f6; color: #6b7280; }
    .dark .s-delta.is-up { background-color: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
    .dark .s-delta.is-down { background-color: rgba(239, 68, 68, 0.15); color: #fca5a5; }
    .dark .s-delta.is-flat { background-color: #1f2937; color: #9ca3af; }

    .s-grid-4 { display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); gap: 1rem; }
    @media (min-width: 640px) { .s-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (min-width: 1024px) { .s-grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); } }

    .s-stat-label { font-size: 0.75rem; font-weight: 700; color: #6b7280; }
    .dark .s-stat-label { color: #9ca3af; }
    .s-stat-value { margin-top: 0.25rem; font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; color: #111827; }
    .dark .s-stat-value { color: #f3f4f6; }
    .s-stat-foot { margin-top: 0.125rem; font-size: 0.75rem; color: #9ca3af; }

    .s-target-bar {
        position: relative;
        height: 0.5rem;
        border-radius: 9999px;
        background-color: #eef2f7;
        overflow: hidden;
    }

    .s-target-bar.is-slim { height: 0.3125rem; margin-top: 0.375rem; }
    .dark .s-target-bar { background-color: #1f2937; }

    .s-target-bar span {
        display: block;
        height: 100%;
        border-radius: 9999px;
        transition: width 400ms ease;
    }

    .s-target-bar span.is-met { background-image: linear-gradient(90deg, #059669, #10b981); }
    .s-target-bar span.is-close { background-image: linear-gradient(90deg, #d97706, #f59e0b); }
    .s-target-bar span.is-behind { background-image: linear-gradient(90deg, #dc2626, #f87171); }
    .s-target-bar span.is-none { background-color: #cbd5e1; }

    .s-target-tag {
        padding: 0.0625rem 0.4375rem;
        border-radius: 9999px;
        font-weight: 700;
        font-size: 0.6875rem;
    }

    .s-target-tag.is-met { background-color: #d1fae5; color: #047857; }
    .s-target-tag.is-close { background-color: #fef3c7; color: #b45309; }
    .s-target-tag.is-behind { background-color: #fee2e2; color: #b91c1c; }
    .s-target-tag.is-none { background-color: #f3f4f6; color: #9ca3af; }
    .dark .s-target-tag.is-met { background-color: rgba(16, 185, 129, 0.15); color: #6ee7b7; }
    .dark .s-target-tag.is-close { background-color: rgba(245, 158, 11, 0.15); color: #fcd34d; }
    .dark .s-target-tag.is-behind { background-color: rgba(239, 68, 68, 0.15); color: #fca5a5; }
    .dark .s-target-tag.is-none { background-color: #1f2937; color: #6b7280; }

    /* Rank rows shared by leader comparison + top performers */
    .rank-row {
        display: grid;
        grid-template-columns: 1.75rem minmax(0, 1fr) auto;
        align-items: center;
        gap: 0.875rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .rank-row:last-child { border-bottom: 0; }
    .dark .rank-row { border-bottom-color: #1f2937; }

    .rank-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 800;
        background-color: #f3f4f6;
        color: #6b7280;
    }

    .dark .rank-badge { background-color: #1f2937; color: #9ca3af; }
    .rank-badge.rank-1 { background-image: linear-gradient(135deg, #f59e0b, #fbbf24); color: #fff; }
    .rank-badge.rank-2 { background-image: linear-gradient(135deg, #94a3b8, #cbd5e1); color: #fff; }
    .rank-badge.rank-3 { background-image: linear-gradient(135deg, #b45309, #d97706); color: #fff; }

    .rank-name { font-size: 0.875rem; font-weight: 700; color: #111827; }
    .dark .rank-name { color: #f3f4f6; }

    .rank-chip {
        margin-left: 0.375rem;
        padding: 0.0625rem 0.375rem;
        border-radius: 9999px;
        font-size: 0.5625rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        background-color: #eef2ff;
        color: #4338ca;
    }

    .dark .rank-chip { background-color: rgba(99, 102, 241, 0.15); color: #a5b4fc; }

    .rank-meta { margin-top: 0.25rem; font-size: 0.6875rem; color: #9ca3af; }
    .rank-amount { font-size: 0.9375rem; font-weight: 800; color: #111827; }
    .dark .rank-amount { color: #f3f4f6; }

    .s-empty { border: 1px dashed #e5e7eb; border-radius: 0.875rem; padding: 2rem 1.5rem; text-align: center; }
    .dark .s-empty { border-color: #1f2937; }

    /* Sold vs distributed bridge */
    .bridge-flow {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1rem;
    }

    .bridge-node {
        flex: 1;
        min-width: 10rem;
        padding: 1rem;
        border-radius: 0.875rem;
        background-color: #f9fafb;
        border: 1px solid #f1f5f9;
    }

    .dark .bridge-node { background-color: rgba(31, 41, 55, 0.5); border-color: #1f2937; }

    .bridge-arrow { color: #cbd5e1; font-size: 1.25rem; flex-shrink: 0; }
    .dark .bridge-arrow { color: #4b5563; }

    .bridge-value { font-size: 1.375rem; font-weight: 800; color: #111827; }
    .dark .bridge-value { color: #f3f4f6; }

    .bridge-label { font-size: 0.75rem; font-weight: 700; color: #6b7280; }
    .dark .bridge-label { color: #9ca3af; }
</style>
@endpush

@section('content')
@php
    $attainmentTone = $company['attainment'] === null
        ? 'is-none'
        : ($company['attainment'] >= 100 ? 'is-met' : ($company['attainment'] >= 60 ? 'is-close' : 'is-behind'));
    $deltaClass = $company['change'] === null ? 'is-flat' : ($company['change'] >= 0 ? 'is-up' : 'is-down');
    $topLeaderAmount = max(1, (float) $leaderComparison->max('amount'));
    $topPerformerAmount = max(1, (float) $topPerformers->max('amount'));
    $companyFreshPct = $company['sold'] > 0 ? round($company['freshAmount'] / $company['sold'] * 100) : 0;
    $companyRepeatPct = 100 - $companyFreshPct;
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-7xl mx-auto space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="s-eyebrow">Company-wide</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">Sales Leadership</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Every sales leader and their team, side by side — {{ $monthDate->format('F Y') }}.
            </p>
        </div>
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 text-sm font-semibold">
            <i class="fas fa-calendar-alt text-xs"></i>{{ $availableMonths[$selectedMonth] ?? $selectedMonth }}
        </span>
    </div>

    <!-- Company summary -->
    <div class="s-panel p-6 sm:p-8">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div>
                <p class="s-eyebrow">Company sold in {{ $monthDate->format('F') }}</p>
                <p class="s-hero-amount s-num mt-2">${{ number_format($company['sold'], 2) }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <span class="s-delta {{ $deltaClass }}">
                        @if($company['change'] === null)
                            No sales last month
                        @else
                            <i class="fas fa-arrow-{{ $company['change'] >= 0 ? 'up' : 'down' }} text-[10px]"></i>
                            {{ abs($company['change']) }}% vs {{ $monthDate->copy()->subMonth()->format('F') }}
                        @endif
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 s-num">
                        {{ $company['ordersSold'] }} {{ \Illuminate\Support\Str::plural('order', $company['ordersSold']) }} ·
                        {{ $company['activeSellers'] }} active {{ \Illuminate\Support\Str::plural('seller', $company['activeSellers']) }}
                    </span>
                </div>
            </div>

            @if($company['target'] > 0)
                <div class="text-left lg:text-right lg:min-w-[16rem]">
                    <div class="flex items-center justify-between lg:justify-end gap-3 mb-2">
                        <span class="s-eyebrow">Company target</span>
                        <span class="s-target-tag {{ $attainmentTone }}">{{ $company['attainment'] }}%</span>
                    </div>
                    <div class="s-target-bar">
                        <span class="{{ $attainmentTone }}" style="width: {{ min(100, $company['attainment']) }}%"></span>
                    </div>
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400 s-num">
                        ${{ number_format($company['sold'], 2) }} of ${{ number_format($company['target'], 2) }}
                        @if($company['remaining'] > 0)
                            · <strong>${{ number_format($company['remaining'], 2) }} to go</strong>
                        @else
                            · target met
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <div class="mt-6">
            <canvas id="companyTrendChart" height="90"></canvas>
        </div>

        <!-- Fresh vs Repeat -->
        <div class="mt-6 pt-5 border-t border-dashed border-gray-200 dark:border-gray-800">
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2">
                <span class="s-eyebrow">Fresh vs. Repeat</span>
                <span class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    Fresh — <span class="font-bold text-gray-900 dark:text-gray-100 s-num">${{ number_format($company['freshAmount'], 2) }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500 s-num">{{ $company['freshOrders'] }} {{ \Illuminate\Support\Str::plural('order', $company['freshOrders']) }}</span>
                </span>
                <span class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                    Repeat — <span class="font-bold text-gray-900 dark:text-gray-100 s-num">${{ number_format($company['repeatAmount'], 2) }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500 s-num">{{ $company['repeatOrders'] }} {{ \Illuminate\Support\Str::plural('order', $company['repeatOrders']) }}</span>
                </span>
            </div>
            <div class="mt-2.5 h-2 rounded-full overflow-hidden flex">
                <div class="bg-emerald-500" style="width: {{ $companyFreshPct }}%"></div>
                <div class="bg-amber-400" style="width: {{ $companyRepeatPct }}%"></div>
            </div>
        </div>
    </div>

    <!-- Sold vs distributed bridge -->
    <div class="s-panel p-6">
        <p class="s-eyebrow mb-4"><i class="fas fa-arrows-turn-to-dots text-indigo-500 mr-1"></i> Sold vs. delivered value</p>
        <div class="bridge-flow">
            <div class="bridge-node">
                <p class="bridge-label">Sold this month (gross)</p>
                <p class="bridge-value s-num">${{ number_format($bridge['soldThisMonth'], 2) }}</p>
            </div>
            <i class="fas fa-arrow-right bridge-arrow"></i>
            <div class="bridge-node">
                <p class="bridge-label">Distributed this month (net)</p>
                <p class="bridge-value s-num">${{ number_format($bridge['distributedThisMonth'], 2) }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">orders completed this month</p>
            </div>
            <i class="fas fa-ellipsis bridge-arrow"></i>
            <div class="bridge-node">
                <p class="bridge-label">Still in the pipeline</p>
                <p class="bridge-value s-num">${{ number_format($bridge['pendingNet'], 2) }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">net value sold, not yet distributed</p>
            </div>
        </div>

        <div class="mt-5 pt-4 border-t border-dashed border-gray-200 dark:border-gray-800 flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Lifetime: ${{ number_format($bridge['allTimeSoldGross'], 2) }} sold (gross) →
                ${{ number_format($bridge['allTimeDistributedNet'], 2) }} distributed (net)
            </p>
            @if($bridge['conversionPercent'] !== null)
                <span class="s-target-tag is-met">{{ $bridge['conversionPercent'] }}% of sold value has reached distribution</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
        <!-- Leader vs leader -->
        <div class="s-panel p-5 sm:p-6">
            <p class="s-eyebrow mb-2"><i class="fas fa-users-rectangle text-indigo-500 mr-1"></i> Leader vs. leader</p>
            @forelse($leaderComparison as $index => $leader)
                @php
                    $tone = $leader['attainment'] === null ? 'is-none' : ($leader['attainment'] >= 100 ? 'is-met' : ($leader['attainment'] >= 60 ? 'is-close' : 'is-behind'));
                    $leaderFreshPct = $leader['amount'] > 0 ? round($leader['freshAmount'] / $leader['amount'] * 100) : 0;
                @endphp
                <div class="rank-row">
                    <span class="rank-badge {{ $index < 3 ? 'rank-'.($index + 1) : '' }}">{{ $index + 1 }}</span>
                    <div class="min-w-0">
                        <p class="rank-name">{{ $leader['name'] }}<span class="rank-chip">{{ $leader['members'] }} {{ \Illuminate\Support\Str::plural('member', $leader['members']) }}</span></p>
                        <div class="s-target-bar is-slim">
                            <span class="{{ $tone }}" style="width: {{ $leader['attainment'] === null ? round($leader['amount'] / $topLeaderAmount * 100) : min(100, $leader['attainment']) }}%"></span>
                        </div>
                        <div class="mt-1 h-1 rounded-full overflow-hidden flex">
                            <div class="bg-emerald-500" style="width: {{ $leaderFreshPct }}%"></div>
                            <div class="bg-amber-400" style="width: {{ max(0, 100 - $leaderFreshPct) }}%"></div>
                        </div>
                        <p class="rank-meta s-num">
                            {{ $leader['orders'] }} {{ \Illuminate\Support\Str::plural('order', $leader['orders']) }}
                            @if($leader['attainment'] !== null)
                                · {{ $leader['attainment'] }}% of ${{ number_format($leader['target'], 2) }}
                            @else
                                · no target set
                            @endif
                        </p>
                    </div>
                    <p class="rank-amount s-num">${{ number_format($leader['amount'], 2) }}</p>
                </div>
            @empty
                <div class="s-empty">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No sales leaders yet.</p>
                </div>
            @endforelse
        </div>

        <!-- Top performers, everyone -->
        <div class="s-panel p-5 sm:p-6">
            <div class="flex items-center justify-between mb-2">
                <p class="s-eyebrow"><i class="fas fa-trophy text-amber-500 mr-1"></i> Top performers</p>
                <span class="text-xs text-gray-400 dark:text-gray-500">every group</span>
            </div>
            @forelse($topPerformers->take(10) as $index => $person)
                @php
                    $tone = $person['achievement'] === null ? 'is-none' : ($person['achievement'] >= 100 ? 'is-met' : ($person['achievement'] >= 60 ? 'is-close' : 'is-behind'));
                    $personFreshPct = $person['amount'] > 0 ? round($person['freshAmount'] / $person['amount'] * 100) : 0;
                @endphp
                <div class="rank-row">
                    <span class="rank-badge {{ $index < 3 ? 'rank-'.($index + 1) : '' }}">{{ $index + 1 }}</span>
                    <div class="min-w-0">
                        <p class="rank-name">
                            {{ $person['name'] }}
                            @if($person['is_leader'])
                                <span class="rank-chip">Leader</span>
                            @endif
                        </p>
                        <div class="s-target-bar is-slim">
                            <span class="{{ $tone }}" style="width: {{ $person['achievement'] === null ? round($person['amount'] / $topPerformerAmount * 100) : min(100, $person['achievement']) }}%"></span>
                        </div>
                        <div class="mt-1 h-1 rounded-full overflow-hidden flex">
                            <div class="bg-emerald-500" style="width: {{ $personFreshPct }}%"></div>
                            <div class="bg-amber-400" style="width: {{ max(0, 100 - $personFreshPct) }}%"></div>
                        </div>
                        <p class="rank-meta s-num">
                            {{ $person['orders'] }} {{ \Illuminate\Support\Str::plural('order', $person['orders']) }}
                            @if(! $person['is_leader'] && $person['leader'])
                                · under {{ $person['leader'] }}
                            @endif
                            @if($person['achievement'] !== null)
                                · {{ $person['achievement'] }}% of target
                            @else
                                · no target set
                            @endif
                        </p>
                    </div>
                    <p class="rank-amount s-num">${{ number_format($person['amount'], 2) }}</p>
                </div>
            @empty
                <div class="s-empty">
                    <i class="fas fa-trophy text-2xl text-gray-200 dark:text-gray-700"></i>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Nobody has sold anything this month yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('companyTrendChart');

        if (!canvas || typeof Chart === 'undefined') {
            return;
        }

        const trend = @json($trend);
        const isDark = document.documentElement.classList.contains('dark');
        const context = canvas.getContext('2d');

        new Chart(context, {
            type: 'bar',
            data: {
                labels: trend.map(point => point.short),
                datasets: [
                    {
                        label: 'Fresh',
                        data: trend.map(point => point.fresh),
                        backgroundColor: 'rgba(16,185,129,0.8)',
                        borderRadius: 6,
                        maxBarThickness: 42,
                    },
                    {
                        label: 'Repeat',
                        data: trend.map(point => point.repeat),
                        backgroundColor: 'rgba(245,158,11,0.85)',
                        borderRadius: 6,
                        maxBarThickness: 42,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, font: { size: 11 } } },
                    tooltip: {
                        callbacks: {
                            title: items => trend[items[0].dataIndex].label,
                            label: item => item.dataset.label + ': $' +
                                item.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 2 }) +
                                ' · ' + trend[item.dataIndex].count + ' orders',
                        },
                    },
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { color: isDark ? '#9ca3af' : '#6b7280', font: { size: 11, weight: '600' } },
                    },
                    y: {
                        stacked: true,
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
