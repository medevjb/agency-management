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
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Orders Dashboard</h1>
                        <p class="mt-1 text-sm text-gray-600">Track orders, revenue, and performance metrics</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-semibold transition-all">
                    <i class="fas fa-list mr-2"></i>
                    All Orders
                </a>
                <a href="{{ route('orders.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all shadow-lg">
                    <i class="fas fa-plus mr-2"></i>
                    New Order
                </a>
            </div>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('orders.dashboard') }}" class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <i class="fas fa-calendar text-indigo-500"></i>
                <span class="text-sm font-semibold text-gray-700">Date Range:</span>
            </div>
            <div class="relative">
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            </div>
            <span class="text-gray-500">to</span>
            <div class="relative">
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg font-semibold transition-all">
                <i class="fas fa-filter mr-1"></i>
                Apply Filter
            </button>
            <a href="{{ route('orders.dashboard') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-semibold transition-all">
                <i class="fas fa-times mr-1"></i>
                Clear
            </a>
        </form>
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
                <span class="text-purple-100">{{ $startDate }} to {{ $endDate }}</span>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="stat-card bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Total Revenue</p>
                    <p class="text-4xl font-bold">${{ number_format($stats['totalRevenue'], 2) }}</p>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <i class="fas fa-dollar-sign text-3xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <i class="fas fa-chart-line mr-2"></i>
                <span class="text-green-100">After 20% split</span>
            </div>
        </div>

        <!-- Active Orders -->
        <div class="stat-card bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl shadow-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Active Orders</p>
                    <p class="text-4xl font-bold">{{ $stats['activeOrders'] }}</p>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <i class="fas fa-spinner text-3xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <i class="fas fa-tasks mr-2"></i>
                <span class="text-blue-100">{{ $stats['pendingOrders'] }} pending, {{ $stats['activeOrders'] - $stats['pendingOrders'] }} in progress</span>
            </div>
        </div>

        <!-- Average Revenue -->
        <div class="stat-card bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl shadow-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">Avg Revenue</p>
                    <p class="text-4xl font-bold">${{ number_format($stats['averageRevenue'], 2) }}</p>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <i class="fas fa-calculator text-3xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <i class="fas fa-receipt mr-2"></i>
                <span class="text-orange-100">Per order</span>
            </div>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <!-- Completed -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium mb-1">Completed</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['completedOrders'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Overdue -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium mb-1">Overdue</p>
                    <p class="text-3xl font-bold text-red-600">{{ $stats['overdueOrders'] }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-xl">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm font-medium mb-1">Completion Rate</p>
                    <p class="text-3xl font-bold text-blue-600">
                        @php
                            $rate = $stats['totalOrders'] > 0 ? round(($stats['completedOrders'] / $stats['totalOrders']) * 100, 1) : 0;
                        @endphp
                        {{ $rate }}%
                    </p>
                </div>
                <div class="bg-blue-100 p-3 rounded-xl">
                    <i class="fas fa-percentage text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Monthly Revenue Chart -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-chart-area text-indigo-500 mr-2"></i>
                Monthly Revenue ({{ date('Y') }})
            </h3>
            <div class="chart-container">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Orders by Status -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                <i class="fas fa-chart-pie text-purple-500 mr-2"></i>
                Orders by Status
            </h3>
            <div class="chart-container">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Lists Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Orders Due Soon -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-orange-50">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-clock text-yellow-600 mr-2"></i>
                    Due Soon (Next 7 Days)
                </h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($ordersDueSoon as $order)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <a href="{{ route('orders.show', $order) }}" class="font-semibold text-gray-900 hover:text-indigo-600">
                                    {{ $order->name }}
                                </a>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-building mr-1"></i>{{ $order->team?->name ?? 'No team' }}
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-calendar mr-1"></i>{{ $order->end_date?->format('M d Y') }}
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                @php
                                    $daysLeft = now()->diffInDays($order->end_date);
                                @endphp
                                {{ $daysLeft }} day{{ $daysLeft > 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-check-circle text-4xl text-green-200 mb-3"></i>
                        <p>No orders due soon</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Overdue Orders -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-red-50 to-pink-50">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                    Overdue Orders
                </h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($overdueOrders as $order)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <a href="{{ route('orders.show', $order) }}" class="font-semibold text-gray-900 hover:text-indigo-600">
                                    {{ $order->name }}
                                </a>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-building mr-1"></i>{{ $order->team?->name ?? 'No team' }}
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-calendar mr-1"></i>Due: {{ $order->end_date?->format('M d Y') }}
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                @php
                                    $daysOverdue = now()->diffInDays($order->end_date);
                                @endphp
                                {{ $daysOverdue }} day{{ $daysOverdue > 1 ? 's' : '' }} overdue
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-trophy text-4xl text-green-200 mb-3"></i>
                        <p>No overdue orders!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- High Priority & Recent Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- High Priority Orders -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-red-50 to-orange-50">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-flag text-red-600 mr-2"></i>
                    High Priority Orders
                </h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($highPriorityOrders as $order)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <a href="{{ route('orders.show', $order) }}" class="font-semibold text-gray-900 hover:text-indigo-600">
                                    {{ $order->name }}
                                </a>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-building mr-1"></i>{{ $order->team?->name ?? 'No team' }}
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-layer-group mr-1"></i>{{ $order->techStack?->name ?? 'No tech stack' }}
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                🔴 High
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-check-double text-4xl text-green-200 mb-3"></i>
                        <p>No high priority orders</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                <h3 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-history text-indigo-600 mr-2"></i>
                    Recent Orders
                </h3>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($recentOrders as $order)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <a href="{{ route('orders.show', $order) }}" class="font-semibold text-gray-900 hover:text-indigo-600">
                                    {{ $order->name }}
                                </a>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-calendar mr-1"></i>{{ $order->created_at->format('M d Y') }}
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-dollar-sign mr-1"></i>${{ number_format($order->actual_revenue, 2) }}
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                @if($order->status === 'completed') bg-green-100 text-green-800
                                @elseif($order->status === 'in-progress') bg-blue-100 text-blue-800
                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst(str_replace('-', ' ', $order->status)) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl text-gray-200 mb-3"></i>
                        <p>No recent orders</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Revenue by Team -->
    @if($revenueByTeam->count() > 0)
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-8">
        <h3 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-users text-indigo-500 mr-2"></i>
            Revenue by Team
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($revenueByTeam->take(8) as $team)
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border-2 border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-semibold text-gray-900">{{ $team['team_name'] }}</h4>
                        <span class="text-xs font-medium text-gray-600">{{ $team['orders_count'] }} order{{ $team['orders_count'] > 1 ? 's' : '' }}</span>
                    </div>
                    <p class="text-2xl font-bold text-green-600">${{ number_format($team['revenue'], 2) }}</p>
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
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const monthlyRevenue = {{ json_encode($monthlyRevenue) }};
    const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Revenue',
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
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const ordersByStatus = {{ json_encode($ordersByStatus) }};

    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['NRA', 'Pending', 'In Progress', 'Completed', 'On Hold', 'Cancelled'],
            datasets: [{
                data: [
                    ordersByStatus.nra,
                    ordersByStatus.pending,
                    ordersByStatus['in-progress'],
                    ordersByStatus.completed,
                    ordersByStatus['on-hold'],
                    ordersByStatus.cancelled
                ],
                backgroundColor: [
                    '#9ca3af',
                    '#fbbf24',
                    '#60a5fa',
                    '#34d399',
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
                        padding: 20,
                        usePointStyle: true
                    }
                }
            },
            cutout: '65%'
        }
    });
});
</script>
@endpush
