@extends('layouts.dashboard')

@section('content')
@php
    // Guarded: this view can render more than once per PHP process (tests
    // hitting the route twice, Octane/persistent workers), and a bare
    // function declaration would fatal with "cannot redeclare" the second time.
    if (! function_exists('fmtMoney')) {
        function fmtMoney($amount, $symbol, $position, $decimals, $thousands, $decimal) {
            $formatted = number_format($amount, $decimals, $decimal, $thousands);
            return $position === 'after' ? $formatted . $symbol : $symbol . $formatted;
        }
    }
@endphp
<div class="p-6 space-y-8">

    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                <i class="fas fa-tachometer-alt text-indigo-600 dark:text-indigo-400 mr-2"></i>Admin Dashboard
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">{{ $companyName }} — complete system overview</p>
        </div>
        <div class="flex flex-wrap gap-3 items-center">
            {{-- Set once from the header picker, shared by every dashboard. --}}
            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 text-sm font-semibold">
                <i class="fas fa-calendar-alt text-xs"></i>
                {{ $availableMonths[$selectedMonth] ?? $selectedMonth }}
            </span>
            @if($maintenanceMode)
                <span class="inline-flex items-center px-3 py-2 bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 rounded-lg text-sm font-medium">
                    <i class="fas fa-tools mr-2"></i>Maintenance Mode ON
                </span>
            @endif
            <a href="{{ route('settings.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors text-sm">
                <i class="fas fa-cog mr-2"></i>Settings
            </a>
            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg font-medium transition-colors text-sm">
                <i class="fas fa-users-cog mr-2"></i>Manage Users
            </a>
        </div>
    </div>

    {{-- Revenue Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        {{-- Revenue This Month --}}
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-xl p-5 text-white shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <p class="text-indigo-100 text-sm font-medium">Revenue This Month</p>
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-white"></i>
                </div>
            </div>
            <p class="text-3xl font-bold dash-num">{{ fmtMoney($stats['revenue_this_month'], $currencySymbol, $currencyPosition, $decimalPlaces, $thousandSeparator, $decimalSeparator) }}</p>
            <div class="mt-2 flex items-center gap-2 text-sm">
                @if($stats['revenue_growth'] > 0)
                    <span class="text-green-300"><i class="fas fa-arrow-up"></i> {{ $stats['revenue_growth'] }}%</span>
                @elseif($stats['revenue_growth'] < 0)
                    <span class="text-red-300"><i class="fas fa-arrow-down"></i> {{ abs($stats['revenue_growth']) }}%</span>
                @else
                    <span class="text-indigo-200"><i class="fas fa-minus"></i> 0%</span>
                @endif
                <span class="text-indigo-200">vs last month</span>
            </div>
        </div>

        {{-- Revenue Last Month --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl p-5 shadow-md border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between mb-3">
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Last Month</p>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-500/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-history text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 dash-num">{{ fmtMoney($stats['revenue_last_month'], $currencySymbol, $currencyPosition, $decimalPlaces, $thousandSeparator, $decimalSeparator) }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">Previous month delivered revenue</p>
        </div>

        {{-- Revenue This Year --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl p-5 shadow-md border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between mb-3">
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">This Year</p>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-500/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-600 dark:text-green-400"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 dash-num">{{ fmtMoney($stats['revenue_this_year'], $currencySymbol, $currencyPosition, $decimalPlaces, $thousandSeparator, $decimalSeparator) }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">YTD delivered revenue ({{ $monthDate->year }})</p>
        </div>

        {{-- Project Completion Rate --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl p-5 shadow-md border border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between mb-3">
                <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Completion Rate</p>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-500/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-double text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 dash-num">{{ $stats['completion_rate'] }}%</p>
            <div class="mt-2">
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                    <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $stats['completion_rate'] }}%"></div>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $stats['completed_projects'] }} of {{ $stats['total_projects'] }} orders</p>
            </div>
        </div>
    </div>

    {{-- Key Counts Row --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <x-dashboard-card
            title="Total Users"
            value="{{ $stats['total_users'] }}"
            icon="fas fa-users"
            color="blue"
            trend="up"
            trendValue="{{ $stats['new_users_this_month'] }} new this month"
        />
        <x-dashboard-card
            title="Total Orders"
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

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Monthly Revenue Chart --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100"><i class="fas fa-chart-bar text-indigo-500 mr-2"></i>Monthly Revenue</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $monthDate->year }} — by delivered date</p>
                </div>
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $currencySymbol }}</span>
            </div>
            <canvas id="revenueChart" height="200"></canvas>
        </div>

        {{-- Monthly Orders Chart --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100"><i class="fas fa-chart-line text-green-500 mr-2"></i>Monthly Orders Started</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $monthDate->year }} — by start date</p>
                </div>
            </div>
            <canvas id="ordersChart" height="200"></canvas>
        </div>
    </div>

    {{-- Tasks & Bug Reports Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Monthly Tasks Chart --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100"><i class="fas fa-tasks text-purple-500 mr-2"></i>Monthly Tasks</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $monthDate->year }} — created vs completed</p>
                </div>
            </div>
            <canvas id="tasksChart" height="200"></canvas>
        </div>

        {{-- Bug Reports --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100"><i class="fas fa-bug text-red-500 mr-2"></i>Bug Reports</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500">By status</p>
                </div>
                <a href="{{ route('bug-reports.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">View All</a>
            </div>
            <canvas id="bugsChart" height="150"></canvas>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 space-y-2">
                @forelse($recentBugs as $bug)
                    <a href="{{ route('bug-reports.show', $bug) }}" class="flex items-center justify-between gap-3 text-sm hover:bg-gray-50 dark:hover:bg-gray-800/60 rounded-lg px-2 py-1.5 transition-colors">
                        <span class="truncate text-gray-800 dark:text-gray-200 font-medium">{{ $bug->title }}</span>
                        <span class="shrink-0 text-xs font-semibold px-2 py-0.5 rounded-full
                            @if($bug->status === 'resolved') bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400
                            @elseif($bug->status === 'published') bg-purple-100 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400
                            @elseif($bug->status === 'in_progress') bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400
                            @else bg-yellow-100 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 @endif">
                            {{ ucwords(str_replace('_', ' ', $bug->status)) }}
                        </span>
                    </a>
                @empty
                    <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-3">No bug reports yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- User Growth & Order Priority Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- New Users Chart --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100"><i class="fas fa-user-plus text-blue-500 mr-2"></i>New Users</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $monthDate->year }} — by signup month</p>
                </div>
            </div>
            <canvas id="usersChart" height="200"></canvas>
        </div>

        {{-- Orders by Priority --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100"><i class="fas fa-flag text-orange-500 mr-2"></i>Orders by Priority</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500">All-time distribution</p>
                </div>
            </div>
            <canvas id="priorityChart" height="200"></canvas>
        </div>
    </div>

    {{-- Middle Row: System Health + Role Distribution + Project Status --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- System Health --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md p-6">
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-heartbeat text-red-500 mr-2"></i>System Health
            </h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full {{ $siteActive ? 'bg-green-100 dark:bg-green-500/10' : 'bg-red-100 dark:bg-red-500/10' }} flex items-center justify-center">
                            <i class="fas fa-{{ $siteActive ? 'check' : 'times' }} text-{{ $siteActive ? 'green' : 'red' }}-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Site Status</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $siteName }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 {{ $siteActive ? 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400' }} rounded-full text-xs font-medium">{{ $siteActive ? 'Active' : 'Inactive' }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full {{ $maintenanceMode ? 'bg-orange-100 dark:bg-orange-500/10' : 'bg-green-100 dark:bg-green-500/10' }} flex items-center justify-center">
                            <i class="fas fa-{{ $maintenanceMode ? 'tools' : 'shield-alt' }} text-{{ $maintenanceMode ? 'orange' : 'green' }}-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Maintenance</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $maintenanceMode ? 'System under maintenance' : 'No maintenance scheduled' }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 {{ $maintenanceMode ? 'bg-orange-100 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400' : 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400' }} rounded-full text-xs font-medium">{{ $maintenanceMode ? 'ON' : 'OFF' }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-500/10 flex items-center justify-center">
                            <i class="fas fa-database text-blue-600 dark:text-blue-400 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Database</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Connected</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400 rounded-full text-xs font-medium">OK</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-purple-100 dark:bg-purple-500/10 flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-purple-600 dark:text-purple-400 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Currency</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $currencyCode }} — {{ $currencySymbol }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 rounded-full text-xs font-medium">{{ $currencyCode }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-yellow-100 dark:bg-yellow-500/10 flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">Queue Jobs</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $stats['pending_jobs'] ?? 0 }} pending</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 rounded-full text-xs font-medium">Active</span>
                </div>
            </div>
        </div>

        {{-- Role Distribution --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md p-6">
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-user-shield text-indigo-600 dark:text-indigo-400 mr-2"></i>Role Distribution
            </h3>
            <div class="space-y-3">
                @foreach($roleStats as $role => $count)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('-', ' ', $role)) }}</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $stats['total_users'] > 0 ? round(($count / $stats['total_users']) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-info-circle mr-1"></i>Total: <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_users'] }}</span> users
            </div>
        </div>

        {{-- Project Status Breakdown --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md p-6">
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-chart-pie text-green-600 dark:text-green-400 mr-2"></i>Order Status Breakdown
            </h3>
            <div class="space-y-3">
                @php
                    $statusColors = [
                        'delivered'   => 'bg-green-500',
                        'completed'   => 'bg-emerald-400',
                        'in-progress' => 'bg-blue-500',
                        'pending'     => 'bg-yellow-400',
                        'on-hold'     => 'bg-orange-400',
                        'cancelled'   => 'bg-red-400',
                        'nra'         => 'bg-gray-400',
                    ];
                @endphp
                @foreach($projectStatusStats as $status => $count)
                    @if($count > 0)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('-', ' ', $status)) }}</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                            <div class="{{ $statusColors[$status] ?? 'bg-gray-400' }} h-2 rounded-full"
                                 style="width: {{ $stats['total_projects'] > 0 ? round(($count / $stats['total_projects']) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-folder mr-1"></i>Total: <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_projects'] }}</span> orders
            </div>
        </div>
    </div>

    {{-- Top Teams + Quick Actions Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Top Performing Teams --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                    <i class="fas fa-trophy text-yellow-500 mr-2"></i>Top Performing Teams
                </h3>
                <a href="{{ route('teams.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">View All</a>
            </div>
            @if($topTeams->isEmpty())
                <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-6">No team revenue data yet.</p>
            @else
                <div class="space-y-4">
                    @foreach($topTeams as $i => $team)
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                            @if($i === 0) bg-yellow-100 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400
                            @elseif($i === 1) bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300
                            @elseif($i === 2) bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400
                            @else bg-indigo-50 dark:bg-indigo-500/10 text-indigo-400 @endif">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $team['name'] }}</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-gray-100 ml-2">{{ fmtMoney($team['revenue'], $currencySymbol, $currencyPosition, $decimalPlaces, $thousandSeparator, $decimalSeparator) }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                                    <div class="bg-indigo-500 h-1.5 rounded-full"
                                         style="width: {{ $topTeams->max('revenue') > 0 ? round(($team['revenue'] / $topTeams->max('revenue')) * 100) : 0 }}%"></div>
                                </div>
                                <span class="text-xs text-gray-400 dark:text-gray-500 whitespace-nowrap">{{ $team['orders_count'] }} orders</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Company Info --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md p-6">
            <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-building text-indigo-500 mr-2"></i>Company Info
            </h3>
            <div class="space-y-3 text-sm">
                <div class="flex items-start gap-3">
                    <i class="fas fa-briefcase text-gray-400 dark:text-gray-500 mt-0.5 w-4 text-center"></i>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Company</p>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $companyName }}</p>
                    </div>
                </div>
                @if($companyEmail)
                <div class="flex items-start gap-3">
                    <i class="fas fa-envelope text-gray-400 dark:text-gray-500 mt-0.5 w-4 text-center"></i>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Email</p>
                        <p class="font-medium text-gray-800 dark:text-gray-200 break-all">{{ $companyEmail }}</p>
                    </div>
                </div>
                @endif
                @if($companyPhone)
                <div class="flex items-start gap-3">
                    <i class="fas fa-phone text-gray-400 dark:text-gray-500 mt-0.5 w-4 text-center"></i>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Phone</p>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $companyPhone }}</p>
                    </div>
                </div>
                @endif
                @if($businessHours)
                <div class="flex items-start gap-3">
                    <i class="fas fa-clock text-gray-400 dark:text-gray-500 mt-0.5 w-4 text-center"></i>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Business Hours</p>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $businessHours }}</p>
                    </div>
                </div>
                @endif
                <div class="flex items-start gap-3">
                    <i class="fas fa-coins text-gray-400 dark:text-gray-500 mt-0.5 w-4 text-center"></i>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Currency</p>
                        <p class="font-medium text-gray-800 dark:text-gray-200">{{ $currencyCode }} ({{ $currencySymbol }})</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                <a href="{{ route('settings.group', 'company') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">
                    <i class="fas fa-edit mr-1"></i>Edit company info
                </a>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-lg p-6 text-white">
        <h3 class="text-base font-semibold mb-4">
            <i class="fas fa-bolt mr-2"></i>Admin Quick Actions
        </h3>
        <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
            <a href="{{ route('users.index') }}" class="flex flex-col items-center p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-colors">
                <i class="fas fa-users text-2xl mb-2"></i>
                <span class="text-xs font-medium">Users</span>
            </a>
            <a href="{{ route('admin.roles') }}" class="flex flex-col items-center p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-colors">
                <i class="fas fa-shield-alt text-2xl mb-2"></i>
                <span class="text-xs font-medium">Roles</span>
            </a>
            <a href="{{ route('orders.index') }}" class="flex flex-col items-center p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-colors">
                <i class="fas fa-folder text-2xl mb-2"></i>
                <span class="text-xs font-medium">Orders</span>
            </a>
            <a href="{{ route('teams.index') }}" class="flex flex-col items-center p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-colors">
                <i class="fas fa-users text-2xl mb-2"></i>
                <span class="text-xs font-medium">Teams</span>
            </a>
            <a href="{{ route('tech-stacks.index') }}" class="flex flex-col items-center p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-colors">
                <i class="fas fa-layer-group text-2xl mb-2"></i>
                <span class="text-xs font-medium">Tech Stacks</span>
            </a>
            <a href="{{ route('settings.index') }}" class="flex flex-col items-center p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-colors">
                <i class="fas fa-cog text-2xl mb-2"></i>
                <span class="text-xs font-medium">Settings</span>
            </a>
        </div>
    </div>

    {{-- Recent Users & Orders Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Users --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Recent Users</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Newly registered</p>
                </div>
                <a href="{{ route('users.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">View All</a>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800">
                @foreach($recentUsers as $user)
                <div class="px-6 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img class="h-9 w-9 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4F46E5&color=fff&size=64" alt="{{ $user->name }}">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 text-xs rounded-full
                                @if($user->role === 'admin') bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400
                                @elseif(str_contains($user->role ?? '', 'manager')) bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400
                                @else bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 @endif">
                                {{ ucfirst(str_replace('-', ' ', $user->role ?? 'user')) }}
                            </span>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Recent Orders</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Latest activities</p>
                </div>
                <a href="{{ route('orders.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">View All</a>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-800">
                @foreach($recentOrders as $order)
                <div class="px-6 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-indigo-100 dark:bg-indigo-500/10 flex items-center justify-center">
                                <i class="fas fa-folder text-indigo-600 dark:text-indigo-400 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate max-w-[160px]">{{ $order->name }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500">{{ $order->creator?->name ?? 'System' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @php
                                $sBadge = match($order->status) {
                                    'delivered','completed' => 'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400',
                                    'in-progress' => 'bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400',
                                    'on-hold' => 'bg-yellow-100 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400',
                                    'cancelled' => 'bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400',
                                    default => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
                                };
                            @endphp
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $sBadge }}">
                                {{ ucfirst(str_replace('-', ' ', $order->status)) }}
                            </span>
                            @if($order->value)
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mt-1">{{ fmtMoney($order->value, $currencySymbol, $currencyPosition, $decimalPlaces, $thousandSeparator, $decimalSeparator) }}</p>
                            @else
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $order->created_at->diffForHumans() }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    const revenueData = @json(array_values($monthlyRevenue));
    const ordersData  = @json(array_values($monthlyOrders));

    // Theme-aware chart colors (Chart.js bakes colors into the canvas, so
    // this is read once at render time; the theme toggle does a full page reload)
    const isDarkMode = document.documentElement.classList.contains('dark');
    const chartGridColor = isDarkMode ? 'rgba(255, 255, 255, 0.08)' : '#f3f4f6';
    const chartTickColor = isDarkMode ? '#9ca3af' : '#6b7280';

    // Monthly Revenue Chart
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Revenue',
                data: revenueData,
                backgroundColor: 'rgba(255, 90, 31,0.7)',
                borderColor: 'rgba(255, 90, 31,1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => '{{ $currencySymbol }}' + ctx.parsed.y.toLocaleString(undefined, {minimumFractionDigits: {{ $decimalPlaces }}})
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: chartGridColor }, ticks: { color: chartTickColor, callback: v => '{{ $currencySymbol }}' + v.toLocaleString() } },
                x: { grid: { display: false }, ticks: { color: chartTickColor } }
            }
        }
    });

    // Monthly Orders Chart
    new Chart(document.getElementById('ordersChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Orders',
                data: ordersData,
                fill: true,
                backgroundColor: 'rgba(16,185,129,0.1)',
                borderColor: 'rgba(16,185,129,1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(16,185,129,1)',
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: chartGridColor }, ticks: { color: chartTickColor } },
                x: { grid: { display: false }, ticks: { color: chartTickColor } }
            }
        }
    });

    // Monthly Tasks Chart (created vs completed)
    const tasksCreated = @json(array_values($monthlyTasksCreated));
    const tasksCompleted = @json(array_values($monthlyTasksCompleted));
    new Chart(document.getElementById('tasksChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Created',
                data: tasksCreated,
                backgroundColor: 'rgba(245, 154, 15,0.7)',
                borderColor: 'rgba(245, 154, 15,1)',
                borderWidth: 1,
                borderRadius: 4,
            }, {
                label: 'Completed',
                data: tasksCompleted,
                backgroundColor: 'rgba(16,185,129,0.7)',
                borderColor: 'rgba(16,185,129,1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: chartTickColor, usePointStyle: true, font: { size: 11 } }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: chartGridColor }, ticks: { color: chartTickColor, stepSize: 1 } },
                x: { grid: { display: false }, ticks: { color: chartTickColor } }
            }
        }
    });

    // Bug Reports by Status (doughnut)
    const bugStatuses = @json($bugStatusStats);
    const bugLabels = Object.keys(bugStatuses).map(s => s.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase()));
    const bugValues = Object.values(bugStatuses);
    const bugColors = {
        pending: '#f59e0b', in_progress: '#3b82f6', resolved: '#10b981', published: '#F59A0F'
    };
    const bugBackground = Object.keys(bugStatuses).map(s => bugColors[s] || '#9ca3af');
    const bugTotal = bugValues.reduce((a, b) => a + b, 0);
    if (bugTotal > 0) {
        new Chart(document.getElementById('bugsChart'), {
            type: 'doughnut',
            data: {
                labels: bugLabels,
                datasets: [{
                    data: bugValues,
                    backgroundColor: bugBackground,
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: chartTickColor, padding: 12, usePointStyle: true, font: { size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed || 0;
                                const percentage = bugTotal > 0 ? Math.round((value / bugTotal) * 100) : 0;
                                return context.label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // New Users Chart (line)
    const usersData = @json(array_values($monthlyNewUsers));
    new Chart(document.getElementById('usersChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'New Users',
                data: usersData,
                fill: true,
                backgroundColor: 'rgba(59,130,246,0.1)',
                borderColor: 'rgba(59,130,246,1)',
                borderWidth: 2,
                pointBackgroundColor: 'rgba(59,130,246,1)',
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: chartGridColor }, ticks: { color: chartTickColor, stepSize: 1 } },
                x: { grid: { display: false }, ticks: { color: chartTickColor } }
            }
        }
    });

    // Orders by Priority (doughnut)
    const priorities = @json($ordersByPriority);
    const priorityLabels = Object.keys(priorities).map(p => p.charAt(0).toUpperCase() + p.slice(1));
    const priorityValues = Object.values(priorities);
    const priorityColors = {
        urgent: '#ef4444', high: '#f97316', medium: '#f59e0b', low: '#10b981'
    };
    const priorityBackground = Object.keys(priorities).map(p => priorityColors[p] || '#9ca3af');
    const priorityTotal = priorityValues.reduce((a, b) => a + b, 0);
    if (priorityTotal > 0) {
        new Chart(document.getElementById('priorityChart'), {
            type: 'doughnut',
            data: {
                labels: priorityLabels,
                datasets: [{
                    data: priorityValues,
                    backgroundColor: priorityBackground,
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: chartTickColor, padding: 12, usePointStyle: true, font: { size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed || 0;
                                const percentage = priorityTotal > 0 ? Math.round((value / priorityTotal) * 100) : 0;
                                return context.label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
