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
        <div class="flex gap-3">
            <a href="{{ route('orders.create') }}" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium transition-all shadow-lg shadow-indigo-200 hover:shadow-xl">
                <i class="fas fa-plus mr-2"></i>New Order
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $stats = [
                'total' => \App\Models\Order::count(),
                'active' => \App\Models\Order::whereIn('status', ['pending', 'in-progress'])->count(),
                'completed' => \App\Models\Order::where('status', 'completed')->count(),
                'revenue' => \App\Models\Order::where('status', 'completed')->whereNotNull('actual_revenue')->sum('actual_revenue'),
            ];
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

        <!-- Active Orders -->
        <div class="stat-card bg-white rounded-2xl shadow-md p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Active Orders</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['active']) }}</p>
                    <p class="text-xs text-green-600 mt-2"><i class="fas fa-arrow-up mr-1"></i>In progress</p>
                </div>
                <div class="gradient-icon w-14 h-14 rounded-2xl flex items-center justify-center" style="--icon-from: #10b981; --icon-to: #34d399;">
                    <i class="fas fa-spinner text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Completed Orders -->
        <div class="stat-card bg-white rounded-2xl shadow-md p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Completed</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['completed']) }}</p>
                    <p class="text-xs text-blue-600 mt-2"><i class="fas fa-check mr-1"></i>Done</p>
                </div>
                <div class="gradient-icon w-14 h-14 rounded-2xl flex items-center justify-center" style="--icon-from: #3b82f6; --icon-to: #60a5fa;">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="stat-card bg-white rounded-2xl shadow-md p-6 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Revenue</p>
                    <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['revenue'], 0) }}</p>
                    <p class="text-xs text-purple-600 mt-2"><i class="fas fa-dollar-sign mr-1"></i>Collected</p>
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
            <!-- Revenue Chart & Status Distribution -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Monthly Revenue Chart -->
                <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Monthly Revenue</h3>
                            <p class="text-sm text-gray-500">Last 6 months</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-green-600"></i>
                        </div>
                    </div>
                    <div class="chart-container" style="position: relative; height: 200px; width: 100%;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Order Status Distribution -->
                <div class="bg-white rounded-2xl shadow-md p-6 border border-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Order Status</h3>
                            <p class="text-sm text-gray-500">Current distribution</p>
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

            <!-- Recent Orders Table -->
            <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                        <p class="text-sm text-gray-500">Latest order updates</p>
                    </div>
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="divide-y divide-gray-100">
                    @php
                        $recentOrders = \App\Models\Order::with(['team', 'techStack', 'creator'])
                            ->latest()
                            ->take(6)
                            ->get();
                    @endphp
                    @forelse($recentOrders as $order)
                        <a href="{{ route('orders.show', $order->id) }}" class="order-card block px-6 py-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4 flex-1">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center
                                        @if($order->status === 'completed') bg-green-100
                                        @elseif($order->status === 'in-progress') bg-blue-100
                                        @elseif($order->status === 'planning') bg-purple-100
                                        @elseif($order->priority === 'urgent') bg-red-100
                                        @else bg-gray-100 @endif">
                                        <i class="fas fa-folder
                                            @if($order->status === 'completed') text-green-600
                                            @elseif($order->status === 'in-progress') text-blue-600
                                            @elseif($order->status === 'planning') text-purple-600
                                            @elseif($order->priority === 'urgent') text-red-600
                                            @else text-gray-600 @endif"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $order->name }}</p>
                                            @if($order->priority === 'urgent')
                                                <span class="flex-shrink-0 px-2 py-0.5 bg-red-100 text-red-700 text-xs font-medium rounded-full">Urgent</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3 text-xs text-gray-500">
                                            @if($order->team)
                                                <span><i class="fas fa-users mr-1"></i>{{ $order->team->name }}</span>
                                            @endif
                                            @if($order->techStack)
                                                <span><i class="fas fa-code mr-1"></i>{{ $order->techStack->name }}</span>
                                            @endif
                                            @if($order->value && $order->value > 0)
                                                <span><i class="fas fa-dollar-sign mr-1"></i>{{ number_format($order->value) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 ml-4">
                                    <span class="px-3 py-1 text-xs font-medium rounded-full
                                        @if($order->status === 'completed') bg-green-100 text-green-800
                                        @elseif($order->status === 'in-progress') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'planning') bg-purple-100 text-purple-800
                                        @elseif($order->status === 'on-hold') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst(str_replace('-', ' ', $order->status)) }}
                                    </span>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <i class="fas fa-folder-open text-5xl text-gray-200 mb-4"></i>
                            <p class="text-gray-500 mb-2">No orders yet</p>
                            <a href="{{ route('orders.create') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                <i class="fas fa-plus mr-2"></i>Create your first order
                            </a>
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
// Global chart instances to prevent duplicates
let revenueChartInstance = null;
let statusChartInstance = null;

// Store order status data in a data attribute to avoid multiple PHP executions
const orderStatusData = {
    completed: {{ \App\Models\Order::where('status', 'completed')->count() }},
    inProgress: {{ \App\Models\Order::where('status', 'in-progress')->count() }},
    planning: {{ \App\Models\Order::where('status', 'planning')->count() }},
    onHold: {{ \App\Models\Order::where('status', 'on-hold')->count() }}
};

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
    if (statusChartInstance) {
        statusChartInstance.destroy();
    }

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        revenueChartInstance = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue',
                    data: [12000, 19000, 15000, 25000, 22000, 30000],
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
                        padding: 10
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: {
                            callback: function(value) {
                                return '$' + (value / 1000) + 'k';
                            },
                            font: { size: 10 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }

    // Status Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        const total = orderStatusData.completed + orderStatusData.inProgress + orderStatusData.planning + orderStatusData.onHold;

        // Only show chart if there's data
        if (total > 0) {
            statusChartInstance = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Completed', 'In Progress', 'Planning', 'On Hold'],
                    datasets: [{
                        data: [
                            orderStatusData.completed,
                            orderStatusData.inProgress,
                            orderStatusData.planning,
                            orderStatusData.onHold
                        ],
                        backgroundColor: ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b'],
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
                                font: { size: 10 }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 10
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
    if (statusChartInstance) statusChartInstance.destroy();
});
</script>
@endpush
@endsection
