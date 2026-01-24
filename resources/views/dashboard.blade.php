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
        border-color: #6366f1;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }
    .progress-bar {
        transition: width 0.6s ease;
    }
</style>
@endpush

@section('content')
<div class="p-6 space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 mt-1">Welcome back, {{ Auth::user()->name }}! Here's your order overview.</p>
        </div>
        <div class="flex flex-wrap gap-3 items-center">
            <!-- Month Filter -->
            <div class="relative">
                <select id="monthFilter" class="appearance-none bg-white border border-gray-200 text-gray-700 py-2.5 pl-4 pr-10 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent cursor-pointer shadow-sm font-medium text-sm">
                    @foreach($availableMonths as $value => $label)
                        <option value="{{ $value }}" {{ $selectedMonth === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                    <i class="fas fa-calendar-alt text-sm"></i>
                </div>
            </div>
            <a href="{{ route('orders.create') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-all shadow-lg shadow-indigo-200 hover:shadow-xl">
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $myTasks = \App\Models\Task::where('assigned_to', Auth::id())->where('status', '!=', 'completed')->count();
        @endphp

        <!-- Total Orders -->
        <div class="stat-card bg-white rounded-2xl shadow-md p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Orders</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                    <p class="text-xs text-gray-500 mt-2"><i class="fas fa-folder mr-1"></i>All orders</p>
                </div>
                <div class="gradient-icon w-14 h-14 rounded-2xl flex items-center justify-center" style="--icon-from: #6366f1; --icon-to: #8b5cf6;">
                    <i class="fas fa-folder text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Delivered Orders (Revenue Generating) -->
        <div class="stat-card bg-white rounded-2xl shadow-md p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Delivered</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['delivered']) }}</p>
                    <p class="text-xs text-green-600 mt-2"><i class="fas fa-check-circle mr-1"></i>Revenue earned</p>
                </div>
                <div class="gradient-icon w-14 h-14 rounded-2xl flex items-center justify-center" style="--icon-from: #10b981; --icon-to: #34d399;">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Workload (Active Orders) -->
        <div class="stat-card bg-white rounded-2xl shadow-md p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Workload</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['workload']) }}</p>
                    <p class="text-xs text-blue-600 mt-2"><i class="fas fa-spinner mr-1"></i>Active orders</p>
                </div>
                <div class="gradient-icon w-14 h-14 rounded-2xl flex items-center justify-center" style="--icon-from: #3b82f6; --icon-to: #60a5fa;">
                    <i class="fas fa-spinner text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="stat-card bg-white rounded-2xl shadow-md p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Revenue</p>
                    <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['revenue'], 0) }}</p>
                    <p class="text-xs text-purple-600 mt-2"><i class="fas fa-dollar-sign mr-1"></i>From delivered</p>
                </div>
                <div class="gradient-icon w-14 h-14 rounded-2xl flex items-center justify-center" style="--icon-from: #8b5cf6; --icon-to: #a78bfa;">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - 2/3 width -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Daily Revenue Chart - Full Width -->
            <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Delivered Revenue</h3>
                        <p class="text-sm text-gray-500">{{ $availableMonths[$selectedMonth] ?? 'Selected Month' }} - Daily (Days 1-{{ $daysInMonth }})</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                </div>
                <div class="chart-container" style="position: relative; height: 250px; width: 100%;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Daily Orders & Delivered vs Workload - Side by Side -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Daily Orders Chart -->
                <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Daily Orders</h3>
                            <p class="text-sm text-gray-500">{{ $availableMonths[$selectedMonth] ?? 'Selected Month' }} - All Orders</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-blue-600"></i>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 200px; width: 100%;">
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>

                <!-- Delivered vs Workload -->
                <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Delivered vs Workload</h3>
                            <p class="text-sm text-gray-500">Revenue-generating vs Active</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                            <i class="fas fa-chart-pie text-indigo-600"></i>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 200px; width: 100%;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Table - Delivered & Workload -->
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 ">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Recent Delivered</h3>
                        <p class="text-sm text-gray-500">Revenue-generating orders</p>
                    </div>
                    <a href="{{ route('orders.index') }}?status=completed" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentDelivered as $order)
                        <a href="{{ route('orders.show', $order->id) }}" class="order-card block px-6 py-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-green-100">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $order->name }}</p>
                                            @if($order->priority === 'high')
                                                <span class="flex-shrink-0 px-2 py-0.5 bg-red-100 text-red-700 text-xs font-medium rounded-full">High</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3 text-xs text-gray-500">
                                            @if($order->team)
                                                <span><i class="fas fa-users mr-1"></i>{{ $order->team->name }}</span>
                                            @endif
                                            @if($order->actual_revenue && $order->actual_revenue > 0)
                                                <span><i class="fas fa-dollar-sign mr-1"></i>{{ number_format($order->actual_revenue) }}</span>
                                            @endif
                                            @if($order->end_date)
                                                <span><i class="far fa-calendar mr-1"></i>{{ $order->end_date->format('M d') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 ml-4">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                        Delivered
                                    </span>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-8 text-center">
                            <i class="fas fa-check-circle text-4xl text-gray-200 mb-3"></i>
                            <p class="text-gray-500">No delivered orders yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Workload Orders -->
            <div class="bg-white rounded-2xl shadow-md border border-gray-100">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Current Workload</h3>
                        <p class="text-sm text-gray-500">Active orders in progress</p>
                    </div>
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentWorkload as $order)
                        <a href="{{ route('orders.show', $order->id) }}" class="order-card block px-6 py-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center
                                        @if($order->status === 'in-progress') bg-blue-100
                                        @elseif($order->status === 'on-hold') bg-yellow-100
                                        @else bg-gray-100 @endif">
                                        <i class="fas fa-spinner
                                            @if($order->status === 'in-progress') text-blue-600
                                            @elseif($order->status === 'on-hold') text-yellow-600
                                            @else text-gray-600 @endif"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $order->name }}</p>
                                            @if($order->priority === 'high')
                                                <span class="flex-shrink-0 px-2 py-0.5 bg-red-100 text-red-700 text-xs font-medium rounded-full">High</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3 text-xs text-gray-500">
                                            @if($order->team)
                                                <span><i class="fas fa-users mr-1"></i>{{ $order->team->name }}</span>
                                            @endif
                                            @if($order->end_date)
                                                <span><i class="far fa-calendar mr-1"></i>{{ $order->end_date->format('M d') }}</span>
                                                @if($order->end_date->isPast())
                                                    <span class="text-red-600"><i class="fas fa-exclamation-triangle mr-1"></i>Overdue</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 ml-4">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        @if($order->status === 'in-progress') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'on-hold') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst(str_replace('-', ' ', $order->status)) }}
                                    </span>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-8 text-center">
                            <i class="fas fa-spinner text-4xl text-gray-200 mb-3"></i>
                            <p class="text-gray-500">No active orders</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- My Tasks Section -->
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">My Tasks</h3>
                        <p class="text-sm text-gray-500">{{ $myTasks }} tasks assigned to you</p>
                    </div>
                    <a href="{{ route('tasks.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="divide-y divide-gray-100">
                    @php
                        $myTasks = \App\Models\Task::with(['order', 'assignedTo'])
                            ->where('assigned_to', Auth::id())
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp
                    @forelse($myTasks as $task)
                        <a href="{{ route('tasks.show', $task->id) }}" class="block px-6 py-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                        @if($task->status === 'completed') bg-green-100
                                        @elseif($task->priority === 'urgent') bg-red-100
                                        @elseif($task->priority === 'high') bg-orange-100
                                        @elseif($task->status === 'in-progress') bg-blue-100
                                        @else bg-gray-100 @endif">
                                        @if($task->status === 'completed')
                                            <i class="fas fa-check text-green-600"></i>
                                        @elseif($task->priority === 'urgent')
                                            <i class="fas fa-exclamation text-red-600"></i>
                                        @elseif($task->priority === 'high')
                                            <i class="fas fa-arrow-up text-orange-600"></i>
                                        @elseif($task->status === 'in-progress')
                                            <i class="fas fa-spinner text-blue-600"></i>
                                        @else
                                            <i class="fas fa-circle text-gray-400"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $task->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $task->order?->name ?? 'No order' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($task->due_date)
                                        <p class="text-xs text-gray-500 mb-1">
                                            <i class="far fa-calendar mr-1"></i>{{ $task->due_date->format('M d') }}
                                        </p>
                                    @endif
                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full
                                        @if($task->status === 'completed') bg-green-100 text-green-800
                                        @elseif($task->status === 'in-progress') bg-blue-100 text-blue-800
                                        @elseif($task->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-8 text-center">
                            <i class="fas fa-tasks text-4xl text-gray-200 mb-3"></i>
                            <p class="text-gray-500">No tasks assigned to you</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column - 1/3 width -->
        <div class="space-y-6">
            <!-- Order Priority Breakdown -->
            <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Priority</h3>
                        <p class="text-sm text-gray-500">By urgency</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                        <i class="fas fa-flag text-orange-600"></i>
                    </div>
                </div>
                @php
                    $priorityStats = [
                        'urgent' => \App\Models\Order::where('priority', 'urgent')->count(),
                        'high' => \App\Models\Order::where('priority', 'high')->count(),
                        'medium' => \App\Models\Order::where('priority', 'medium')->count(),
                        'low' => \App\Models\Order::where('priority', 'low')->count(),
                    ];
                    $totalOrders = max(array_sum($priorityStats), 1);
                @endphp
                <div class="space-y-4">
                    @foreach(['urgent' => ['color' => 'red', 'icon' => 'fas fa-fire'], 'high' => ['color' => 'orange', 'icon' => 'fas fa-arrow-up'], 'medium' => ['color' => 'yellow', 'icon' => 'fas fa-minus'], 'low' => ['color' => 'green', 'icon' => 'fas fa-arrow-down']] as $priority => $config)
                        @if($priorityStats[$priority] > 0)
                            <div>
                                <div class="flex items-center justify-between text-sm mb-2">
                                    <span class="flex items-center gap-2 text-gray-700">
                                        <i class="{{ $config['icon'] }} text-{{ $config['color'] }}-600"></i>
                                        <span class="font-medium capitalize">{{ $priority }}</span>
                                    </span>
                                    <span class="font-bold text-gray-900">{{ $priorityStats[$priority] }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="progress-bar h-2 rounded-full bg-{{ $config['color'] }}-500"
                                         style="width: {{ ($priorityStats[$priority] / $totalOrders) * 100 }}%"></div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Team Performance -->
            @can('view teams')
            <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Teams</h3>
                        <p class="text-sm text-gray-500">Active teams</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                </div>
                @php
                    $teams = \App\Models\Team::withCount('orders')->latest()->take(4)->get();
                @endphp
                <div class="space-y-3">
                    @forelse($teams as $team)
                        <a href="{{ route('teams.show', $team->id) }}" class="flex items-center justify-between p-3 rounded-xl hover:bg-gray-50 transition-colors border border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-users text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $team->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $team->orders_count }} orders</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </a>
                    @empty
                        <div class="text-center py-6">
                            <i class="fas fa-users text-3xl text-gray-200 mb-2"></i>
                            <p class="text-sm text-gray-500">No teams yet</p>
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
            <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Due Soon</h3>
                        <p class="text-sm text-gray-500">Orders ending soon</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                        <i class="fas fa-clock text-red-600"></i>
                    </div>
                </div>
                @php
                    $dueSoon = \App\Models\Order::with(['team'])
                        ->whereNotIn('status', ['completed', 'cancelled'])
                        ->whereNotNull('end_date')
                        ->where('end_date', '>=', now())
                        ->where('end_date', '<=', now()->addDays(14))
                        ->orderBy('end_date')
                        ->take(4)
                        ->get();
                @endphp
                <div class="space-y-3">
                    @forelse($dueSoon as $order)
                        <a href="{{ route('orders.show', $order->id) }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors border border-gray-100">
                            <div class="w-10 h-10 rounded-lg @if($order->end_date->isPast()) bg-red-100 @elseif($order->end_date->diffInDays(now()) <= 3) bg-orange-100 @else bg-yellow-100 @endif flex items-center justify-center">
                                <i class="fas fa-calendar @if($order->end_date->isPast()) text-red-600 @elseif($order->end_date->diffInDays(now()) <= 3) text-orange-600 @else text-yellow-600 @endif"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $order->name }}</p>
                                <p class="text-xs text-gray-500">{{ $order->end_date->diffForHumans() }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-6">
                            <i class="fas fa-calendar-check text-3xl text-gray-200 mb-2"></i>
                            <p class="text-sm text-gray-500">No upcoming deadlines</p>
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
// Month filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const monthFilter = document.getElementById('monthFilter');
    if (monthFilter) {
        monthFilter.addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('month', this.value);
            window.location = url.toString();
        });
    }
});
// Global chart instances to prevent duplicates
let revenueChartInstance = null;
let ordersChartInstance = null;
let statusChartInstance = null;

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
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: {
                            callback: function(value) {
                                return '$' + (value >= 1000 ? (value / 1000).toFixed(0) + 'k' : value);
                            },
                            font: { size: 10 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
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
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: {
                            stepSize: 1,
                            font: { size: 10 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
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
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (revenueChartInstance) revenueChartInstance.destroy();
    if (ordersChartInstance) ordersChartInstance.destroy();
    if (statusChartInstance) statusChartInstance.destroy();
});
</script>
@endpush
@endsection
