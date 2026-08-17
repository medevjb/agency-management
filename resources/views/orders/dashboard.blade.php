@extends('layouts.dashboard')

@section('title', 'Orders Dashboard')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css">
    <style>
        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .chart-container {
            position: relative;
            height: 300px;
        }
    </style>
@endpush

@section('content')
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                            @if ($team)
                                <i class="fas fa-users text-white text-xl"></i>
                            @else
                                <i class="fas fa-chart-line text-white text-xl"></i>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                @if ($team)
                                    {{ $team->name }} Dashboard
                                @else
                                    Orders Dashboard
                                @endif
                            </h1>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                @if ($team)
                                    Track your team's orders, revenue, and performance metrics
                                @else
                                    Track orders, revenue, and performance metrics
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('orders.index') }}"
                        class="inline-flex items-center px-4 py-2 border-2 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 font-semibold transition-all">
                        <i class="fas fa-list mr-2"></i>
                        All Orders
                    </a>
                    @can('create', \App\Models\Order::class)
                        <a href="{{ route('orders.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all shadow-lg">
                            <i class="fas fa-plus mr-2"></i>
                            New Order
                        </a>
                    @endcan
                </div>
            </div>
        </div>

        @if ($team)
            <!-- Team Information Card -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-500/10 dark:to-indigo-500/10 rounded-xl shadow-md border border-blue-100 dark:border-blue-900 p-6 mb-8">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                                    <i class="fas fa-user-tie text-green-600 dark:text-green-400 text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Team Leader</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $team->teamLeader->name ?? 'Not Assigned' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                                    <i class="fas fa-users text-blue-600 dark:text-blue-400 text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Team Members</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $teamMembers->count() }} members</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="bg-white dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                                    <i class="fas fa-project-diagram text-purple-600 text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Active Orders</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $stats['workloadOrders'] }} orders</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Selected Month -->
        {{-- Chosen once from the header picker and shared by every dashboard;
             this just reports which month is active, it doesn't set it. --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-100 dark:border-gray-800 p-4 mb-6 flex items-center gap-3">
            <i class="fas fa-calendar-alt text-indigo-500"></i>
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Showing data for</span>
            <span class="px-3 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 text-sm font-bold">
                {{ $availableMonths[$selectedMonth] ?? $selectedMonth }}
            </span>
            <span class="text-xs text-gray-400 dark:text-gray-500">— change it from the month picker in the header</span>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Orders -->
            <div class="stat-card bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl shadow-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium mb-1">Total Orders</p>
                        <p class="text-4xl font-bold">{{ $stats['totalOrders'] }}</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-2xl">
                        <i class="fas fa-shopping-cart text-3xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <span class="text-purple-100">{{ $availableMonths[$selectedMonth] ?? $startDate.' to '.$endDate }}</span>
                </div>
            </div>

            <!-- Delivered Revenue -->
            <div class="stat-card bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium mb-1">Delivered Revenue</p>
                        <p class="text-4xl font-bold">${{ number_format($stats['totalRevenue'], 2) }}</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-2xl">
                        <i class="fas fa-dollar-sign text-3xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="text-green-100">{{ $stats['deliveredOrders'] }} orders delivered this month</span>
                </div>
            </div>

            <!-- Workload Orders -->
            <div class="stat-card bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl shadow-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-1">Workload</p>
                        <p class="text-4xl font-bold">{{ $stats['workloadOrders'] }}</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-2xl">
                        <i class="fas fa-spinner text-3xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <i class="fas fa-tasks mr-2"></i>
                    <span class="text-blue-100">{{ $stats['pendingOrders'] }} pending,
                        {{ $stats['inProgressOrders'] }} in progress,
                        {{ $stats['onHoldOrders'] }} on hold</span>
                </div>
            </div>

            <!-- Running Order Value -->
            <div class="stat-card bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl shadow-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-orange-100 text-sm font-medium mb-1">Running Value</p>
                        <p class="text-4xl font-bold">${{ number_format($stats['workloadValue'], 2) }}</p>
                    </div>
                    <div class="bg-white/20 p-4 rounded-2xl">
                        <i class="fas fa-calculator text-3xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <i class="fas fa-hourglass-half mr-2"></i>
                    <span class="text-orange-100">Active orders pipeline value</span>
                </div>
            </div>
        </div>

        <!-- Secondary Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <!-- Delivered -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-100 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">Delivered</p>
                        <p class="text-3xl font-bold text-green-600 dark:text-green-400 dash-num">{{ $stats['deliveredOrders'] }}</p>
                    </div>
                    <div class="bg-green-100 dark:bg-green-500/10 p-3 rounded-xl">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Overdue -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-100 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">Overdue</p>
                        <p class="text-3xl font-bold text-red-600 dark:text-red-400 dash-num">{{ $stats['overdueOrders'] }}</p>
                    </div>
                    <div class="bg-red-100 dark:bg-red-500/10 p-3 rounded-xl">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Completion Rate -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md border border-gray-100 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">Completion Rate</p>
                        <p class="text-3xl font-bold text-blue-600 dark:text-blue-400 dash-num">
                            @php
                                $rate =
                                    $stats['totalOrders'] > 0
                                        ? round(($stats['deliveredOrders'] / $stats['totalOrders']) * 100, 1)
                                        : 0;
                            @endphp
                            {{ $rate }}%
                        </p>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-500/10 p-3 rounded-xl">
                        <i class="fas fa-percentage text-blue-600 dark:text-blue-400 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Monthly Revenue Chart -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-chart-area text-indigo-500 mr-2"></i>
                    Monthly Value ({{ date('Y') }})
                </h3>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Orders by Status -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-chart-pie text-purple-500 mr-2"></i>
                    Orders by Status
                </h3>
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Priority & Revenue Trend Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Orders by Priority -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-flag text-orange-500 mr-2"></i>
                    Orders by Priority
                </h3>
                <div class="chart-container" style="height: 260px;">
                    <canvas id="priorityChart"></canvas>
                </div>
            </div>

            <!-- Revenue Trend (interactive) -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-chart-line text-indigo-500 mr-2"></i>
                        Revenue Trend
                    </h3>
                    <div class="flex gap-2">
                        <button onclick="loadTrend('week')" id="trendWeekBtn"
                            class="px-3 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-xs font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">Week</button>
                        <button onclick="loadTrend('month')" id="trendMonthBtn"
                            class="px-3 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-xs font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">Month</button>
                        <button onclick="loadTrend('year')" id="trendYearBtn"
                            class="px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-semibold hover:bg-indigo-700 transition-colors">Year</button>
                    </div>
                </div>
                <div class="chart-container" style="height: 260px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly & Yearly Delivery Analytics -->
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    <i class="fas fa-truck text-green-500 mr-2"></i>
                    Delivery Analytics
                </h3>
                <div class="flex gap-2">
                    <button onclick="showDeliveryView('monthly')" id="monthlyBtn"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition-colors">
                        Monthly ({{ date('Y') }})
                    </button>
                    <button onclick="showDeliveryView('yearly')" id="yearlyBtn"
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Yearly
                    </button>
                </div>
            </div>

            <!-- Monthly View -->
            <div id="monthlyView" class="delivery-view">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Month</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Orders</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wider">Delivered</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-green-700 dark:text-green-400 uppercase tracking-wider">Delivered Value</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wider">Cancelled</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Delivery Rate</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                            @php
                                $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                            @endphp
                            @for($i = 1; $i <= 12; $i++)
                                @php
                                    $data = $monthlyDeliveredData[$i] ?? ['delivered_count' => 0, 'delivered_amount' => 0, 'cancelled_count' => 0, 'total_orders' => 0];
                                    $deliveryRate = $data['total_orders'] > 0 ? round(($data['delivered_count'] / $data['total_orders']) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $monthNames[$i - 1] }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-700 dark:text-gray-300">
                                        {{ $data['total_orders'] }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400">
                                            {{ $data['delivered_count'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-semibold text-green-700 dark:text-green-400">
                                        ${{ number_format($data['delivered_amount'], 2) }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $data['cancelled_count'] > 0 ? 'bg-red-100 dark:bg-red-500/10 text-red-800 dark:text-red-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                                            {{ $data['cancelled_count'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="h-2 rounded-full {{ $deliveryRate >= 80 ? 'bg-green-500' : ($deliveryRate >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                                    style="width: {{ $deliveryRate }}%"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $deliveryRate }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-800 font-semibold">
                            @php
                                $yearlyTotal = array_sum(array_column($monthlyDeliveredData, 'total_orders'));
                                $monthlyYearlyDelivered = array_sum(array_column($monthlyDeliveredData, 'delivered_count'));
                                $yearlyDeliveredAmount = array_sum(array_column($monthlyDeliveredData, 'delivered_amount'));
                                $yearlyCancelled = array_sum(array_column($monthlyDeliveredData, 'cancelled_count'));
                                $yearlyRate = $yearlyTotal > 0 ? round(($monthlyYearlyDelivered / $yearlyTotal) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">Yearly Total</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-900 dark:text-gray-100">{{ $yearlyTotal }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400">
                                        {{ $monthlyYearlyDelivered }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-green-700 dark:text-green-400">
                                    ${{ number_format($yearlyDeliveredAmount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $yearlyCancelled > 0 ? 'bg-red-100 dark:bg-red-500/10 text-red-800 dark:text-red-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                                        {{ $yearlyCancelled }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $yearlyRate }}%</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Yearly View -->
            <div id="yearlyView" class="delivery-view hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Orders</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-green-600 dark:text-green-400 uppercase tracking-wider">Delivered</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-green-700 dark:text-green-400 uppercase tracking-wider">Delivered Value</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-red-600 dark:text-red-400 uppercase tracking-wider">Cancelled</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Delivery Rate</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                            @if($yearlyDelivered->count() > 0)
                                @foreach($yearlyDelivered as $year => $data)
                                    @php
                                        $totalOrders = (int)$data->total_orders;
                                        $deliveryRate = $totalOrders > 0 ? round(((int)$data->delivered_count / $totalOrders) * 100, 1) : 0;
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $data->year }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-700 dark:text-gray-300">
                                            {{ $totalOrders }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400">
                                                {{ $data->delivered_count }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center text-sm font-semibold text-green-700 dark:text-green-400">
                                            ${{ number_format($data->delivered_amount, 2) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold {{ $data->cancelled_count > 0 ? 'bg-red-100 dark:bg-red-500/10 text-red-800 dark:text-red-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }}">
                                                {{ $data->cancelled_count }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                    <div class="h-2 rounded-full {{ $deliveryRate >= 80 ? 'bg-green-500' : ($deliveryRate >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                                        style="width: {{ $deliveryRate }}%"></div>
                                                </div>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $deliveryRate }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-700 mb-2"></i>
                                        <p>No yearly data available</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Lists Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Orders Due Soon -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-amber-50 to-yellow-50 dark:from-amber-500/10 dark:to-yellow-500/10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center">
                            <i class="fas fa-clock text-amber-600 mr-2"></i>
                            Due Soon
                        </h3>
                        <span class="text-xs font-semibold text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-500/10 px-3 py-1 rounded-full">
                            Next 4 Days
                        </span>
                    </div>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800 max-h-96 overflow-y-auto">
                    @forelse($ordersDueSoon as $order)
                        <div class="p-4 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('orders.show', $order) }}"
                                        class="font-semibold text-gray-900 dark:text-gray-100 hover:text-amber-600 dark:hover:text-amber-400 block truncate">
                                        {{ $order->name }}
                                    </a>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        <span class="flex items-center">
                                            <i class="fas fa-building text-gray-400 dark:text-gray-500 mr-1.5 text-xs"></i>
                                            {{ $order->team?->name ?? 'No team' }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-calendar text-gray-400 dark:text-gray-500 mr-1.5 text-xs"></i>
                                            {{ $order->end_date?->format('M d, Y') }}
                                        </span>
                                        @if ($order->priority)
                                            <span class="flex items-center">
                                                <i class="fas fa-flag text-gray-400 dark:text-gray-500 mr-1.5 text-xs"></i>
                                                <span class="capitalize">{{ $order->priority }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @php
                                    $endDateTime = $order->end_date;
                                    $nowDateTime = now();

                                    // Calculate difference in seconds (order_date - current_date)
                                    $diffInSeconds = $endDateTime->timestamp - $nowDateTime->timestamp;

                                    // If negative, it's overdue
if ($diffInSeconds <= 0) {
    $badgeClass = 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400';
    $badgeText = 'Overdue';
    $badgeIcon = 'fas fa-exclamation-triangle';
} else {
    // Positive means time remaining
    $days = (int) floor($diffInSeconds / 86400);
    $hours = (int) floor(($diffInSeconds % 86400) / 3600);
    $minutes = (int) floor(($diffInSeconds % 3600) / 60);

    if ($days > 0) {
        // More than 24 hours - show days and hours
        if ($days <= 2) {
            $badgeClass = 'bg-yellow-100 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400';
        } else {
            $badgeClass = 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400';
        }
        $badgeText = $days . 'd ' . $hours . 'h left';
        $badgeIcon = 'fas fa-hourglass';
    } else {
        // 24 hours or less - show hours and minutes
        if ($hours == 0) {
            $badgeClass = 'bg-orange-100 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400';
            $badgeText = $minutes . 'm left';
        } else {
            $badgeClass = 'bg-orange-100 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400';
            $badgeText = $hours . 'h ' . $minutes . 'm left';
        }
        $badgeIcon = 'fas fa-clock';
                                        }
                                    }
                                @endphp
                                <div class="flex flex-col items-end gap-2">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold {{ $badgeClass }} whitespace-nowrap">
                                        <i class="{{ $badgeIcon }}"></i>
                                        {{ $badgeText }}
                                    </span>
                                    @if ($order->status)
                                        <span
                                            class="text-xs font-medium text-gray-500 dark:text-gray-400 capitalize">{{ str_replace('-', ' ', $order->status) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-500/10 rounded-full mb-4">
                                <i class="fas fa-check-circle text-3xl text-green-500"></i>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 font-medium">No orders due soon</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">All caught up!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Overdue Orders -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-500/10 dark:to-pink-500/10">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 mr-2"></i>
                            Overdue Orders
                        </h3>
                        <span class="text-xs font-semibold text-red-700 dark:text-red-400 bg-red-100 dark:bg-red-500/10 px-3 py-1 rounded-full">
                            Action Required
                        </span>
                    </div>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800 max-h-96 overflow-y-auto">
                    @forelse($overdueOrders as $order)
                        <div class="p-4 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('orders.show', $order) }}"
                                        class="font-semibold text-gray-900 dark:text-gray-100 hover:text-red-600 dark:hover:text-red-400 block truncate">
                                        {{ $order->name }}
                                    </a>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        <span class="flex items-center">
                                            <i class="fas fa-building text-gray-400 dark:text-gray-500 mr-1.5 text-xs"></i>
                                            {{ $order->team?->name ?? 'No team' }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-calendar-times text-red-400 mr-1.5 text-xs"></i>
                                            Due: {{ $order->end_date?->format('M d, Y') }}
                                        </span>
                                        @if ($order->priority)
                                            <span class="flex items-center">
                                                <i class="fas fa-flag text-gray-400 dark:text-gray-500 mr-1.5 text-xs"></i>
                                                <span class="capitalize">{{ $order->priority }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @php
                                    $endDateTime = $order->end_date;
                                    $nowDateTime = now();
                                    $diffInSeconds = $nowDateTime->diffInSeconds($endDateTime, false); // Negative for overdue

                                    $overdueSeconds = abs($diffInSeconds);

                                    if ($overdueSeconds <= 86400) {
                                        // 24 hours or less - show hours and minutes
                                        $hours = (int) floor($overdueSeconds / 3600);
                                        $minutes = (int) floor(($overdueSeconds % 3600) / 60);

                                        if ($hours == 0) {
                                            $badgeClass = 'bg-pink-100 dark:bg-pink-500/10 text-pink-700 dark:text-pink-400';
                                            $badgeText = $minutes . 'm overdue';
                                        } else {
                                            $badgeClass = 'bg-pink-100 dark:bg-pink-500/10 text-pink-700 dark:text-pink-400';
                                            $badgeText = $hours . 'h ' . $minutes . 'm overdue';
                                        }
                                        $badgeIcon = 'fas fa-clock';
                                    } else {
                                        // More than 24 hours - show days and hours
                                        $days = (int) floor($overdueSeconds / 86400);
                                        $remainingSeconds = $overdueSeconds % 86400;
                                        $hours = (int) floor($remainingSeconds / 3600);

                                        if ($days >= 7) {
                                            $badgeClass = 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400';
                                            $badgeIcon = 'fas fa-exclamation-triangle';
                                        } elseif ($days >= 3) {
                                            $badgeClass = 'bg-orange-100 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400';
                                            $badgeIcon = 'fas fa-exclamation-circle';
                                        } else {
                                            $badgeClass = 'bg-pink-100 dark:bg-pink-500/10 text-pink-700 dark:text-pink-400';
                                            $badgeIcon = 'fas fa-calendar-times';
                                        }
                                        $badgeText = $days . 'd ' . $hours . 'h overdue';
                                    }
                                @endphp
                                <div class="flex flex-col items-end gap-2">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold {{ $badgeClass }} whitespace-nowrap">
                                        <i class="{{ $badgeIcon }}"></i>
                                        {{ $badgeText }}
                                    </span>
                                    @if ($order->status)
                                        <span
                                            class="text-xs font-medium text-gray-500 dark:text-gray-400 capitalize">{{ str_replace('-', ' ', $order->status) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-500/10 rounded-full mb-4">
                                <i class="fas fa-trophy text-3xl text-green-500"></i>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 font-medium">No overdue orders!</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Great job staying on track</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- High Priority & Recent Orders -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- High Priority Orders -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-500/10 dark:to-orange-500/10">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-flag text-red-600 dark:text-red-400 mr-2"></i>
                        High Priority Orders
                    </h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($highPriorityOrders as $order)
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <a href="{{ route('orders.show', $order) }}"
                                        class="font-semibold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">
                                        {{ $order->name }}
                                    </a>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        <i class="fas fa-building mr-1"></i>{{ $order->team?->name ?? 'No team' }}
                                        <span class="mx-2">•</span>
                                        <i
                                            class="fas fa-layer-group mr-1"></i>{{ $order->techStack?->name ?? 'No tech stack' }}
                                    </p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-500/10 text-red-800 dark:text-red-400">
                                    🔴 High
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-check-double text-4xl text-green-200 dark:text-green-900 mb-3"></i>
                            <p>No high priority orders</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-800 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-500/10 dark:to-purple-500/10">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-history text-indigo-600 mr-2"></i>
                        Recent Orders
                    </h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($recentOrders as $order)
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <a href="{{ route('orders.show', $order) }}"
                                        class="font-semibold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400">
                                        {{ $order->name }}
                                    </a>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        @if(in_array($order->status, ['delivered', 'completed']) && $order->delivered_at)
                                            <i class="fas fa-check-circle text-green-500 mr-1"></i>Delivered {{ $order->delivered_at->format('M d, Y') }}
                                        @elseif($order->end_date)
                                            <i class="fas fa-calendar-alt mr-1"></i>Deadline {{ $order->end_date->format('M d, Y') }}
                                        @else
                                            <i class="fas fa-calendar mr-1"></i>Started {{ $order->start_date?->format('M d, Y') ?? '—' }}
                                        @endif
                                        @if($order->actual_revenue > 0)
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-dollar-sign mr-1"></i>${{ number_format($order->actual_revenue, 2) }}
                                        @endif
                                    </p>
                                </div>
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold
                                @if(in_array($order->status, ['delivered', 'completed'])) bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400
                                @elseif($order->status === 'in-progress') bg-blue-100 dark:bg-blue-500/10 text-blue-800 dark:text-blue-400
                                @elseif($order->status === 'pending') bg-yellow-100 dark:bg-yellow-500/10 text-yellow-800 dark:text-yellow-400
                                @elseif($order->status === 'on-hold') bg-yellow-100 dark:bg-yellow-500/10 text-yellow-800 dark:text-yellow-400
                                @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @endif">
                                    {{ ucfirst(str_replace('-', ' ', $order->status)) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox text-4xl text-gray-200 dark:text-gray-700 mb-3"></i>
                            <p>No recent orders</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Revenue by Team -->
        @if ($revenueByTeam->count() > 0)
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-users text-indigo-500 mr-2"></i>
                    Value by Team
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($revenueByTeam->take(8) as $team)
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-800 rounded-xl p-4 border-2 border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ $team['team_name'] }}</h4>
                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $team['orders_count'] }}
                                    order{{ $team['orders_count'] > 1 ? 's' : '' }}</span>
                            </div>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">${{ number_format($team['revenue'], 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Theme-aware chart colors (Chart.js bakes colors into the canvas, so
        // this is read once at render time; the theme toggle does a full page reload)
        const isDarkMode = document.documentElement.classList.contains('dark');
        const chartGridColor = isDarkMode ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
        const chartTickColor = isDarkMode ? '#9ca3af' : '#6b7280';

        let priorityChartInstance = null;
        let trendChartInstance = null;

        // Toggle between monthly and yearly delivery views
        function showDeliveryView(view) {
            const monthlyView = document.getElementById('monthlyView');
            const yearlyView = document.getElementById('yearlyView');
            const monthlyBtn = document.getElementById('monthlyBtn');
            const yearlyBtn = document.getElementById('yearlyBtn');

            const inactiveClasses = ['bg-gray-200', 'dark:bg-gray-700', 'text-gray-700', 'dark:text-gray-200'];
            const activeClasses = ['bg-indigo-600', 'text-white'];

            if (view === 'monthly') {
                monthlyView.classList.remove('hidden');
                yearlyView.classList.add('hidden');
                monthlyBtn.classList.remove(...inactiveClasses);
                monthlyBtn.classList.add(...activeClasses);
                yearlyBtn.classList.remove(...activeClasses);
                yearlyBtn.classList.add(...inactiveClasses);
            } else {
                monthlyView.classList.add('hidden');
                yearlyView.classList.remove('hidden');
                yearlyBtn.classList.remove(...inactiveClasses);
                yearlyBtn.classList.add(...activeClasses);
                monthlyBtn.classList.remove(...activeClasses);
                monthlyBtn.classList.add(...inactiveClasses);
            }
        }

        function loadTrend(period) {
            const buttons = { week: 'trendWeekBtn', month: 'trendMonthBtn', year: 'trendYearBtn' };
            Object.keys(buttons).forEach(function (p) {
                const btn = document.getElementById(buttons[p]);
                if (!btn) return;
                const active = p === period;
                btn.classList.toggle('bg-indigo-600', active);
                btn.classList.toggle('text-white', active);
                btn.classList.toggle('bg-gray-200', !active);
                btn.classList.toggle('dark:bg-gray-700', !active);
                btn.classList.toggle('text-gray-700', !active);
                btn.classList.toggle('dark:text-gray-200', !active);
            });

            fetch('{{ route('orders.dashboard.chart-data') }}?period=' + period)
                .then(function (r) { return r.json(); })
                .then(function (data) { renderTrend(data, period); })
                .catch(function () { console.error('Failed to load trend data'); });
        }

        function renderTrend(data, period) {
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const labels = data.map(function (d) {
                return period === 'year' ? (monthNames[(d.month || 1) - 1]) : (d.date || '');
            });
            const revenue = data.map(function (d) { return d.revenue; });
            const orders = data.map(function (d) { return d.orders; });

            if (trendChartInstance) trendChartInstance.destroy();

            const ctx = document.getElementById('trendChart');
            if (!ctx) return;

            trendChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue',
                        data: revenue,
                        backgroundColor: 'rgba(99, 102, 241, 0.75)',
                        borderColor: '#6366f1',
                        borderWidth: 1,
                        borderRadius: 4,
                        yAxisID: 'y'
                    }, {
                        label: 'Orders',
                        type: 'line',
                        data: orders,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 3,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: chartTickColor,
                                usePointStyle: true,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.dataset.label === 'Revenue') {
                                        return 'Revenue: $' + (context.parsed.y || 0).toLocaleString();
                                    }
                                    return 'Orders: ' + (context.parsed.y || 0);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            grid: { color: chartGridColor },
                            ticks: {
                                color: chartTickColor,
                                callback: function(value) { return '$' + (value >= 1000 ? (value / 1000).toFixed(0) + 'k' : value); }
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            grid: { display: false },
                            ticks: { color: chartTickColor, stepSize: 1 }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: chartTickColor }
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Dashboard loaded, Chart.js available:', typeof Chart !== 'undefined');

            try {
                // Revenue Chart
                const revenueCtx = document.getElementById('revenueChart');
                if (!revenueCtx) {
                    console.error('Revenue chart canvas not found!');
                    return;
                }

                const monthlyRevenue = {!! json_encode($monthlyRevenue) !!};
                const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                    'Dec'
                ];

                console.log('Monthly revenue data:', monthlyRevenue);

                new Chart(revenueCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: monthLabels,
                        datasets: [{
                            label: 'Value',
                            data: Object.values(monthlyRevenue),
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#6366f1',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Value: $' + context.parsed.y.toLocaleString();
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
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            },
                            x: {
                                grid: { color: chartGridColor },
                                ticks: { color: chartTickColor }
                            }
                        }
                    }
                });

                // Status Chart
                const statusCtx = document.getElementById('statusChart');
                if (!statusCtx) {
                    console.error('Status chart canvas not found!');
                    return;
                }

                const ordersByStatus = {!! json_encode($ordersByStatus) !!};
                console.log('Orders by status data:', ordersByStatus);

                new Chart(statusCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Delivered', 'Pending', 'In Progress', 'On Hold', 'Cancelled'],
                        datasets: [{
                            data: [
                                ordersByStatus.delivered || 0,
                                ordersByStatus.pending || 0,
                                ordersByStatus['in-progress'] || 0,
                                ordersByStatus['on-hold'] || 0,
                                ordersByStatus.cancelled || 0
                            ],
                            backgroundColor: [
                                '#34d399',
                                '#fbbf24',
                                '#60a5fa',
                                '#fb923c',
                                '#f87171'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: chartTickColor,
                                    padding: 20,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) *
                                            100) : 0;
                                        return label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });

                // Priority Chart
                const priorityCtx = document.getElementById('priorityChart');
                if (priorityCtx) {
                    const ordersByPriority = {!! json_encode($ordersByPriority) !!};
                    priorityChartInstance = new Chart(priorityCtx.getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: ['High', 'Medium', 'Low'],
                            datasets: [{
                                data: [
                                    ordersByPriority.high || 0,
                                    ordersByPriority.medium || 0,
                                    ordersByPriority.low || 0
                                ],
                                backgroundColor: ['#f87171', '#fbbf24', '#34d399'],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: chartTickColor,
                                        padding: 20,
                                        usePointStyle: true
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const value = context.parsed || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                            return context.label + ': ' + value + ' (' + percentage + '%)';
                                        }
                                    }
                                }
                            },
                            cutout: '65%'
                        }
                    });
                }

                // Initialize the interactive revenue trend chart (default: year)
                if (document.getElementById('trendChart')) {
                    loadTrend('year');
                }

                console.log('Charts initialized successfully');
            } catch (error) {
                console.error('Error initializing charts:', error);
            }
        });
    </script>
@endpush
