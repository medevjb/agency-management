@extends('layouts.dashboard')

@section('title', 'Sales Leadership Overview')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    /* Premium Dashboard Styles */
    .sb-dashboard {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background-color: #f8fafc;
    }
    
    .dark .sb-dashboard {
        background-color: #0f172a;
    }

    .sb-card {
        background-color: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 1.25rem;
        box-shadow: 0 4px 20px -2px rgba(16, 24, 40, 0.02), 0 2px 8px -1px rgba(16, 24, 40, 0.02);
        transition: all 0.2s ease-in-out;
    }

    .dark .sb-card {
        background-color: #1e293b;
        border-color: #334155;
        box-shadow: none;
    }

    .sb-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px -4px rgba(16, 24, 40, 0.04), 0 4px 12px -2px rgba(16, 24, 40, 0.04);
    }

    .sb-icon-box {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 0.75rem;
        background-color: #f1f5f9;
        color: #475569;
        font-size: 1.125rem;
    }

    .dark .sb-icon-box {
        background-color: #334155;
        color: #cbd5e1;
    }

    /* Gradients */
    .bg-gradient-purple {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }

    .text-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: 'tnum';
    }

    /* Mini bar graph container */
    .mini-bar-chart {
        display: flex;
        align-items: flex-end;
        gap: 2px;
        height: 20px;
    }

    .mini-bar {
        width: 3px;
        background-color: #cbd5e1;
        border-radius: 9999px;
    }

    .mini-bar.active-green { background-color: #10b981; }
    .mini-bar.active-blue { background-color: #3b82f6; }
    .mini-bar.active-red { background-color: #ef4444; }

    /* Custom custom scrolls */
    .custom-scroll::-webkit-scrollbar {
        width: 5px;
    }
    .custom-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .dark .custom-scroll::-webkit-scrollbar-thumb {
        background: #475569;
    }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $attainmentTone = $company['attainment'] === null
        ? 'none'
        : ($company['attainment'] >= 100 ? 'met' : ($company['attainment'] >= 60 ? 'close' : 'behind'));
        
    $companyFreshPct = $company['sold'] > 0 ? round($company['freshAmount'] / $company['sold'] * 100) : 0;
    $companyRepeatPct = 100 - $companyFreshPct;

    // Helper to generate dynamic smooth SVG sparkline paths based on actual trend data
    $generateSparkline = function($trendData, $key = 'amount') {
        $values = collect($trendData)->take(-6)->pluck($key)->values();
        if ($values->isEmpty()) {
            return 'M 0 25 L 120 25';
        }
        $max = $values->max();
        $min = $values->min();
        $range = max(1, $max - $min);
        
        $points = [];
        $width = 120;
        $height = 40;
        $padding = 4;
        
        foreach ($values as $i => $val) {
            $x = ($i / 5) * $width;
            $y = $height - (($val - $min) / $range * ($height - 2 * $padding) + $padding);
            $points[] = "{$x},{$y}";
        }
        
        return 'M ' . implode(' L ', $points);
    };

    $soldSparkline = $generateSparkline($trend, 'amount');
    $freshSparkline = $generateSparkline($trend, 'fresh');
    $repeatSparkline = $generateSparkline($trend, 'repeat');
@endphp

<div class="sb-dashboard min-h-screen px-4 sm:px-6 lg:px-8 py-6 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                Hi, {{ explode(' ', $user->name)[0] }}
            </h1>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">
                Welcome back to your Sales Leadership overview.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 text-xs font-bold shadow-sm">
                <i class="fas fa-calendar-alt"></i>
                {{ $availableMonths[$selectedMonth] ?? $selectedMonth }}
            </span>
        </div>
    </div>

    <!-- Main Grid Layout (Reference styled columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- Column 1: Stacked Trend Cards (Left side of reference) -->
        <div class="lg:col-span-1 flex flex-col gap-6">
            
            <!-- Card 1: Total Gross Sales -->
            <div class="sb-card p-5 relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Gross Sold</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1.5 text-num">
                            ${{ number_format($company['sold'], 2) }}
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                        <i class="fas fa-arrow-up"></i>
                        {{ $company['change'] ?? 0 }}%
                    </span>
                </div>
                <div class="mt-4">
                    <svg class="w-full h-12" viewBox="0 0 120 40" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="grad-gross" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#10b981" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="#10b981" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <path d="{{ $soldSparkline }}" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="{{ $soldSparkline }} L 120 40 L 0 40 Z" fill="url(#grad-gross)" />
                    </svg>
                </div>
            </div>

            <!-- Card 2: Distributed Net -->
            <div class="sb-card p-5 relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Distributed Net</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1.5 text-num">
                            ${{ number_format($bridge['distributedThisMonth'], 2) }}
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 dark:text-blue-400">
                        <i class="fas fa-arrow-trend-up"></i>
                        Net
                    </span>
                </div>
                <div class="mt-4">
                    <svg class="w-full h-12" viewBox="0 0 120 40" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="grad-net" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="#3b82f6" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <path d="{{ $freshSparkline }}" fill="none" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="{{ $freshSparkline }} L 120 40 L 0 40 Z" fill="url(#grad-net)" />
                    </svg>
                </div>
            </div>

            <!-- Card 3: Pending Net (Value in Delivery) -->
            <div class="sb-card p-5 relative overflow-hidden">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Pending Pipeline</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1.5 text-num">
                            ${{ number_format($bridge['pendingNet'], 2) }}
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-600 dark:text-amber-400">
                        <i class="fas fa-clock"></i>
                        Pending
                    </span>
                </div>
                <div class="mt-4">
                    <svg class="w-full h-12" viewBox="0 0 120 40" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="grad-pending" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="#f59e0b" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <path d="{{ $repeatSparkline }}" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="{{ $repeatSparkline }} L 120 40 L 0 40 Z" fill="url(#grad-pending)" />
                    </svg>
                </div>
            </div>

        </div>

        <!-- Column 2 & 3: Quick Stats + Main Analytics Chart (Center of reference) -->
        <div class="lg:col-span-2 flex flex-col gap-6">
            
            <!-- Quick Stats Sub-row (Orders, Active, Attainment) -->
            <div class="grid grid-cols-3 gap-4">
                
                <!-- Orders Card -->
                <div class="sb-card p-4 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Orders Sold</span>
                        <p class="text-lg font-bold text-slate-955 dark:text-white mt-1 text-num">
                            {{ $company['ordersSold'] }}
                        </p>
                    </div>
                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400">Total</span>
                        <div class="mini-bar-chart">
                            <div class="mini-bar h-1.5"></div>
                            <div class="mini-bar h-2"></div>
                            <div class="mini-bar h-3.5 active-green"></div>
                            <div class="mini-bar h-2.5 active-green"></div>
                            <div class="mini-bar h-4 active-green"></div>
                        </div>
                    </div>
                </div>

                <!-- Active Sellers Card -->
                <div class="sb-card p-4 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Active Sellers</span>
                        <p class="text-lg font-bold text-slate-955 dark:text-white mt-1 text-num">
                            {{ $company['activeSellers'] }}
                        </p>
                    </div>
                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-[9px] font-bold text-blue-600 dark:text-blue-400">Active</span>
                        <div class="mini-bar-chart">
                            <div class="mini-bar h-2 active-blue"></div>
                            <div class="mini-bar h-3 active-blue"></div>
                            <div class="mini-bar h-1.5"></div>
                            <div class="mini-bar h-3.5 active-blue"></div>
                            <div class="mini-bar h-4 active-blue"></div>
                        </div>
                    </div>
                </div>

                <!-- Target Attainment Card -->
                <div class="sb-card p-4 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Attainment</span>
                        <p class="text-lg font-bold text-slate-955 dark:text-white mt-1 text-num">
                            {{ $company['attainment'] ?? 0 }}%
                        </p>
                    </div>
                    <div class="flex flex-col gap-1 mt-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-indigo-600 h-full rounded-full" style="width: {{ min(100, $company['attainment'] ?? 0) }}%"></div>
                        </div>
                        <span class="text-[9px] text-slate-400 text-right font-medium">Goal: ${{ number_format($company['target'], 0) }}</span>
                    </div>
                </div>

            </div>

            <!-- Main Sales Analytics Area Chart Card -->
            <div class="sb-card p-6 flex flex-col justify-between flex-1">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Sales Analytics</p>
                        <p class="text-lg font-extrabold text-slate-900 dark:text-white mt-0.5">Month over Month Trend</p>
                    </div>
                    <span class="text-xs text-slate-400 font-semibold">Last 12 months</span>
                </div>
                <div class="relative h-[220px]">
                    <canvas id="companyTrendChart"></canvas>
                </div>
            </div>

        </div>

        <!-- Column 4: Sidebar Details & Action Banners (Right side of reference) -->
        <div class="lg:col-span-1 flex flex-col gap-6">
            
            <!-- What a great achievement alert card -->
            <div class="sb-card p-5 bg-gradient-purple text-white relative overflow-hidden flex flex-col justify-between h-[150px]">
                <div class="relative z-10">
                    <p class="text-xs font-bold text-indigo-100 uppercase tracking-wide">Target Highlight</p>
                    <p class="text-base font-extrabold mt-1">What a great achievement!</p>
                    <p class="text-xs text-indigo-100/90 mt-1 leading-relaxed">
                        @if(($company['attainment'] ?? 0) >= 100)
                            The company target of ${{ number_format($company['target'], 2) }} has been exceeded.
                        @else
                            We have completed {{ $company['attainment'] ?? 0 }}% of the target for this month.
                        @endif
                    </p>
                </div>
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-white/10 relative z-10">
                    <span class="text-lg font-bold">${{ number_format($company['sold'], 0) }}</span>
                    <a href="{{ route('orders.index') }}" class="text-xs font-bold text-white hover:underline flex items-center gap-1">
                        See orders <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
                <!-- Backdrop icon -->
                <i class="fas fa-trophy absolute right-[-15px] bottom-[-15px] text-8xl text-white/5 pointer-events-none"></i>
            </div>

            <!-- Top Performers List (Styled as modern chats/messages box) -->
            <div class="sb-card p-5 flex-1 flex flex-col justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Top Performers</p>
                    <div class="space-y-3 max-h-[260px] overflow-y-auto custom-scroll pr-1">
                        @forelse($topPerformers->take(5) as $index => $person)
                            <div class="flex items-center justify-between py-1.5">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-bold text-xs text-slate-700 dark:text-slate-200 border-2 border-white dark:border-slate-700 shadow-sm shrink-0">
                                        {{ collect(explode(' ', $person['name']))->map(fn($n) => mb_substr($n, 0, 1))->join('') }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                            {{ $person['name'] }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 truncate">
                                            {{ $person['orders'] }} {{ \Illuminate\Support\Str::plural('deal', $person['orders']) }}
                                        </p>
                                    </div>
                                </div>
                                <span class="text-xs font-extrabold text-slate-900 dark:text-white text-num">
                                    ${{ number_format($person['amount'], 0) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-4">Nobody has sold anything this month yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Bottom Row (Table + Donut Breakdown) -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <!-- Left Table: Leaders comparison -->
        <div class="sb-card p-6 xl:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Team Leadership</p>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white mt-0.5">Leader Performance</h3>
                </div>
                <span class="text-xs font-medium text-slate-400">Attainment Overview</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-[10px] uppercase font-bold tracking-wider text-slate-400">
                            <th class="pb-3 pl-2">Leader</th>
                            <th class="pb-3">Orders</th>
                            <th class="pb-3">Amount</th>
                            <th class="pb-3">Attainment Goal</th>
                            <th class="pb-3 text-right pr-2">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($leaderComparison as $index => $leader)
                            <tr class="text-xs text-slate-700 dark:text-slate-300">
                                <td class="py-3.5 pl-2 font-semibold text-slate-900 dark:text-white">
                                    <div>{{ $leader['name'] }}</div>
                                    <div class="mt-0.5 text-[10px] font-normal text-slate-400 dark:text-slate-500">
                                        {{ $leader['members'] }} {{ \Illuminate\Support\Str::plural('seller', $leader['members']) }}
                                        @if($leader['attainment'] !== null)
                                            · {{ $leader['attainment'] }}% of ${{ number_format($leader['target'], 2) }}
                                        @else
                                            · no target set
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 text-num">{{ $leader['orders'] }}</td>
                                <td class="py-3.5 font-bold text-slate-955 dark:text-white text-num">
                                    ${{ number_format($leader['amount'], 2) }}
                                </td>
                                <td class="py-3.5 w-1/4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-full bg-slate-100 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                            <div class="bg-emerald-500 h-full" style="width: {{ min(100, $leader['attainment'] ?? 0) }}%"></div>
                                        </div>
                                        <span class="text-num text-[10px] font-bold">{{ $leader['attainment'] ?? 0 }}%</span>
                                    </div>
                                </td>
                                <td class="py-3.5 text-right pr-2">
                                    @if(($leader['attainment'] ?? 0) >= 100)
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-800 dark:text-emerald-200 text-[10px] font-bold">On Track</span>
                                    @elseif(($leader['attainment'] ?? 0) >= 60)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950 text-amber-800 dark:text-amber-200 text-[10px] font-bold">Close</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-rose-100 dark:bg-rose-950 text-rose-800 dark:text-rose-200 text-[10px] font-bold">Behind</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-400">No leaders comparison data</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Side: Fresh vs Repeat breakdown (Analytics Overview in reference) -->
        <div class="sb-card p-6 xl:col-span-1 flex flex-col justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Analytics Overview</p>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mt-0.5">Fresh vs. Repeat Split</h3>
            </div>
            
            <div class="relative h-[160px] my-4 flex items-center justify-center">
                <canvas id="freshRepeatDonutChart"></canvas>
            </div>

            <div class="flex items-center justify-around text-xs pt-4 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <div>
                        <p class="text-slate-400 font-semibold text-[10px]">FRESH</p>
                        <p class="font-extrabold text-slate-900 dark:text-white mt-0.5 text-num">${{ number_format($company['freshAmount'], 2) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                    <div>
                        <p class="text-slate-400 font-semibold text-[10px]">REPEAT</p>
                        <p class="font-extrabold text-slate-950 dark:text-white mt-0.5 text-num">${{ number_format($company['repeatAmount'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Team Quarter Progress -->
    @if($quarterBreakdown->isNotEmpty())
        <div class="sb-card p-6 mt-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Team Quarter Progress</p>
                    <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mt-0.5">Quarterly Sales Progress</h3>
                    <p class="text-xs text-slate-400 mt-1">Performance of each team member across the current quarter's monthly cycles.</p>
                </div>
            </div>

            <!-- Chart (Full Width on Top) -->
            <div class="w-full h-[320px] relative mb-8">
                <canvas id="quarterTrendChart"></canvas>
            </div>

            <!-- Table (Below Chart, Full Width) -->
            <div class="pt-6 border-t border-slate-100 dark:border-slate-800 overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-slate-800 text-[10px] uppercase font-bold tracking-wider text-slate-400 bg-slate-50/50 dark:bg-slate-800/20">
                            <th class="py-3 px-4 pl-3">Sales Person</th>
                            <th class="py-3 px-4 text-right">1st Month ({{ $quarterNames['q1'] }})</th>
                            <th class="py-3 px-4 text-right">2nd Month ({{ $quarterNames['q2'] }})</th>
                            <th class="py-3 px-4 text-right">3rd Month ({{ $quarterNames['q3'] }})</th>
                            <th class="py-3 px-4 pr-3 text-right">Quarter</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($quarterBreakdown as $seller)
                            <tr class="text-slate-700 dark:text-slate-300 hover:bg-slate-50/50 dark:hover:bg-slate-800/10 transition-colors">
                                <td class="py-3 px-4 pl-3 font-semibold text-slate-900 dark:text-white">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-extrabold text-[10px] text-slate-600 dark:text-slate-300 border-2 border-white dark:border-slate-700 shadow-sm shrink-0">
                                            {{ collect(explode(' ', $seller['name']))->map(fn($n) => mb_substr($n, 0, 1))->join('') }}
                                        </div>
                                        <span class="truncate max-w-[200px]">{{ $seller['name'] }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right font-semibold text-blue-600 dark:text-blue-400 text-num">${{ number_format($seller['q1_amount']) }}</td>
                                <td class="py-3 px-4 text-right font-semibold text-amber-600 dark:text-amber-500 text-num">${{ number_format($seller['q2_amount']) }}</td>
                                <td class="py-3 px-4 text-right font-semibold text-purple-600 dark:text-purple-400 text-num">${{ number_format($seller['q3_amount']) }}</td>
                                <td class="py-3 px-4 pr-3 text-right font-extrabold text-indigo-600 dark:text-indigo-400 text-sm text-num">${{ number_format($seller['quarter_amount']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trend = @json($trend);
        const isDark = document.documentElement.classList.contains('dark');

        // 1. Month-over-Month Trend Line/Area Chart
        const lineCanvas = document.getElementById('companyTrendChart');
        if (lineCanvas) {
            const ctx = lineCanvas.getContext('2d');
            
            // Create nice gradients for the area charts
            const freshGrad = ctx.createLinearGradient(0, 0, 0, 200);
            freshGrad.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
            freshGrad.addColorStop(1, 'rgba(16, 185, 129, 0)');

            const repeatGrad = ctx.createLinearGradient(0, 0, 0, 200);
            repeatGrad.addColorStop(0, 'rgba(245, 158, 11, 0.4)');
            repeatGrad.addColorStop(1, 'rgba(245, 158, 11, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: trend.map(point => point.short),
                    datasets: [
                        {
                            label: 'Fresh Sales',
                            data: trend.map(point => point.fresh),
                            borderColor: '#10b981',
                            borderWidth: 3,
                            backgroundColor: freshGrad,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                        },
                        {
                            label: 'Repeat Sales',
                            data: trend.map(point => point.repeat),
                            borderColor: '#f59e0b',
                            borderWidth: 3,
                            backgroundColor: repeatGrad,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 3,
                            pointHoverRadius: 6,
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
                                boxWidth: 10,
                                font: { size: 11, family: 'Plus Jakarta Sans' },
                                color: isDark ? '#94a3b8' : '#475569'
                            }
                        },
                        tooltip: {
                            backgroundColor: isDark ? '#1e293b' : '#0f172a',
                            titleFont: { family: 'Plus Jakarta Sans', size: 12 },
                            bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                            callbacks: {
                                label: item => ` ${item.dataset.label}: $${item.parsed.y.toLocaleString()}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                color: isDark ? '#64748b' : '#94a3b8',
                                font: { size: 11, family: 'Plus Jakarta Sans' }
                            }
                        },
                        y: {
                            border: { display: false },
                            grid: { color: isDark ? '#334155' : '#f1f5f9' },
                            ticks: {
                                color: isDark ? '#64748b' : '#94a3b8',
                                font: { size: 11, family: 'Plus Jakarta Sans' },
                                callback: val => `$${val >= 1000 ? (val / 1000) + 'k' : val}`
                            }
                        }
                    }
                }
            });
        }

        // 2. Fresh vs Repeat Donut Chart
        const donutCanvas = document.getElementById('freshRepeatDonutChart');
        if (donutCanvas) {
            new Chart(donutCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Fresh Sales', 'Repeat Sales'],
                    datasets: [{
                        data: [@json($company['freshAmount']), @json($company['repeatAmount'])],
                        backgroundColor: ['#10b981', '#f59e0b'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }

        // 3. Quarterly Sales Progress Chart
        const quarterCanvas = document.getElementById('quarterTrendChart');
        if (quarterCanvas && typeof Chart !== 'undefined') {
            const qBreakdown = @json($quarterBreakdown);
            const qNames = @json($quarterNames);
            const qContext = quarterCanvas.getContext('2d');
            
            new Chart(qContext, {
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
                            backgroundColor: 'rgba(139, 92, 246, 0.85)',
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
                            labels: { boxWidth: 10, boxHeight: 10, usePointStyle: true, font: { size: 10, family: 'Plus Jakarta Sans' } },
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
                            ticks: { color: isDark ? '#94a3b8' : '#475569', font: { size: 10, family: 'Plus Jakarta Sans', weight: '600' } },
                        },
                        y: {
                            beginAtZero: true,
                            border: { display: false },
                            grid: { color: isDark ? '#334155' : '#f1f5f9' },
                            ticks: {
                                color: isDark ? '#64748b' : '#94a3b8',
                                font: { size: 10, family: 'Plus Jakarta Sans' },
                                callback: value => '$' + (value >= 1000 ? (value / 1000) + 'k' : value),
                            },
                        },
                    },
                }
            });
        }
    });
</script>
@endpush
@endsection
