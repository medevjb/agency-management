@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-tachometer-alt text-indigo-600 mr-2"></i>Admin Dashboard
                </h1>
                <p class="text-gray-600 mt-2">Complete overview of your application metrics and activities.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('settings.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-cog mr-2"></i>Settings
                </a>
                <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg font-medium transition-colors">
                    <i class="fas fa-users-cog mr-2"></i>Manage Users
                </a>
            </div>
        </div>
    </div>

    <!-- Key Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <x-dashboard-card
            title="Total Users"
            value="{{ $stats['total_users'] }}"
            icon="fas fa-users"
            color="blue"
            trend="up"
            trendValue="{{ $stats['new_users_this_month'] }} new this month"
        />
        <x-dashboard-card
            title="Total Projects"
            value="{{ $stats['total_projects'] }}"
            icon="fas fa-folder"
            color="green"
            trend="up"
            trendValue="{{ $stats['active_projects'] }} active"
        />
        <x-dashboard-card
            title="Total Tasks"
            value="{{ $stats['total_tasks'] }}"
            icon="fas fa-tasks"
            color="yellow"
            trend="stable"
            trendValue="{{ $stats['completed_tasks'] }} completed"
        />
        <x-dashboard-card
            title="Teams"
            value="{{ $stats['total_teams'] }}"
            icon="fas fa-people-group"
            color="purple"
            trend="up"
            trendValue="{{ $stats['total_team_members'] }} members"
        />
        <x-dashboard-card
            title="Tech Stacks"
            value="{{ $stats['total_tech_stacks'] }}"
            icon="fas fa-layer-group"
            color="orange"
            trend="up"
            trendValue="Technologies"
        />
    </div>

    <!-- System Overview & Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- System Health -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-heartbeat text-red-500 mr-2"></i>System Health
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Application Status</p>
                            <p class="text-xs text-gray-500">All systems operational</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Healthy</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-database text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Database</p>
                            <p class="text-xs text-gray-500">Connected</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">OK</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                            <i class="fas fa-hdd text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Storage</p>
                            <p class="text-xs text-gray-500">Normal usage</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Good</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                            <i class="fas fa-clock text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Queue</p>
                            <p class="text-xs text-gray-500">{{ $stats['pending_jobs'] ?? 0 }} pending</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Active</span>
                </div>
            </div>
        </div>

        <!-- Role Distribution -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-user-shield text-indigo-600 mr-2"></i>Role Distribution
            </h3>
            <div class="space-y-3">
                @foreach($roleStats as $role => $count)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700">{{ ucfirst(str_replace('-', ' ', $role)) }}</span>
                            <span class="font-medium text-gray-900">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500"
                                 style="width: {{ ($count / max($stats['total_users'], 1)) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-1"></i>
                    Total users: <span class="font-semibold">{{ $stats['total_users'] }}</span>
                </p>
            </div>
        </div>

        <!-- Project Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-pie text-green-600 mr-2"></i>Project Status
            </h3>
            <div class="space-y-3">
                @foreach($projectStatusStats as $status => $count)
                    @if($count > 0)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700">{{ ucfirst(str_replace('-', ' ', $status)) }}</span>
                            <span class="font-medium text-gray-900">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="@if($status === 'completed') bg-green-500 @elseif($status === 'in-progress') bg-blue-500 @elseif($status === 'on-hold') bg-yellow-500 @elseif($status === 'planning') bg-purple-500 @else bg-gray-500 @endif h-2 rounded-full transition-all duration-500"
                                 style="width: {{ ($count / max($stats['total_projects'], 1)) * 100 }}%"></div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    <i class="fas fa-folder mr-1"></i>
                    Total projects: <span class="font-semibold">{{ $stats['total_projects'] }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 mb-8 text-white">
        <h3 class="text-lg font-semibold mb-4">
            <i class="fas fa-bolt mr-2"></i>Admin Quick Actions
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <a href="{{ route('users.index') }}" class="flex flex-col items-center p-4 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                <i class="fas fa-users text-2xl mb-2"></i>
                <span class="text-sm font-medium">Users</span>
            </a>
            <a href="{{ route('admin.roles') }}" class="flex flex-col items-center p-4 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                <i class="fas fa-shield-alt text-2xl mb-2"></i>
                <span class="text-sm font-medium">Roles</span>
            </a>
            <a href="{{ route('orders.index') }}" class="flex flex-col items-center p-4 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                <i class="fas fa-folder text-2xl mb-2"></i>
                <span class="text-sm font-medium">Orders</span>
            </a>
            <a href="{{ route('teams.index') }}" class="flex flex-col items-center p-4 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                <i class="fas fa-users text-2xl mb-2"></i>
                <span class="text-sm font-medium">Teams</span>
            </a>
            <a href="{{ route('tech-stacks.index') }}" class="flex flex-col items-center p-4 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                <i class="fas fa-layer-group text-2xl mb-2"></i>
                <span class="text-sm font-medium">Tech Stacks</span>
            </a>
            <a href="{{ route('settings.index') }}" class="flex flex-col items-center p-4 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                <i class="fas fa-cog text-2xl mb-2"></i>
                <span class="text-sm font-medium">Settings</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Recent Users</h3>
                    <p class="text-sm text-gray-500">Newly registered users</p>
                </div>
                <a href="{{ route('users.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($recentUsers as $user)
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ $user->name }}&background=4F46E5&color=fff&size=64" alt="{{ $user->name }}">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($user->role === 'admin') bg-red-100 text-red-800
                                    @elseif($user->role === 'manager') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                    <p class="text-sm text-gray-500">Latest order activities</p>
                </div>
                <a href="{{ route('orders.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($recentOrders as $order)
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-folder text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $order->name }}</p>
                                    <p class="text-xs text-gray-500">by {{ $order->creator?->name ?? 'System' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($order->status === 'completed') bg-green-100 text-green-800
                                    @elseif($order->status === 'in-progress') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'on-hold') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('-', ' ', $order->status)) }}
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $order->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Activity & Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Activity Log -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-stream text-indigo-600 mr-2"></i>Recent Activity
            </h3>
            @php
                $activities = \App\Models\Project::with('createdBy')
                    ->latest()
                    ->take(8)
                    ->get();
            @endphp
            <div class="space-y-4">
                @foreach($activities as $activity)
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <i class="fas fa-folder-plus text-indigo-600"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900">
                                <span class="font-medium">{{ $activity->createdBy?->name ?? 'System' }}</span>
                                created project
                                <a href="{{ route('projects.show', $activity->id) }}" class="text-indigo-600 hover:underline font-medium">{{ $activity->name }}</a>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="space-y-6">
            <!-- User Growth -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-chart-line text-green-600 mr-2"></i>User Growth
                </h3>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</div>
                <p class="text-xs text-gray-500 mt-1">Total registered users</p>
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-green-600"><i class="fas fa-arrow-up"></i> {{ $stats['new_users_this_month'] }}</span>
                        <span class="text-gray-500">this month</span>
                    </div>
                </div>
            </div>

            <!-- Active Projects -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-rocket text-blue-600 mr-2"></i>Active Projects
                </h3>
                <div class="text-3xl font-bold text-gray-900">{{ $stats['active_projects'] }}</div>
                <p class="text-xs text-gray-500 mt-1">Currently in progress</p>
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-blue-600">{{ $stats['completed_projects'] }}</span>
                        <span class="text-gray-500">completed</span>
                    </div>
                </div>
            </div>

            <!-- Task Completion -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-check-double text-purple-600 mr-2"></i>Task Completion
                </h3>
                <div class="text-3xl font-bold text-gray-900">
                    @if($stats['total_tasks'] > 0)
                        {{ round(($stats['completed_tasks'] / $stats['total_tasks']) * 100) }}%
                    @else
                        0%
                    @endif
                </div>
                <p class="text-xs text-gray-500 mt-1">Completion rate</p>
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="text-green-600">{{ $stats['completed_tasks'] }}</span>
                        <span class="text-gray-500">of {{ $stats['total_tasks'] }} tasks</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
