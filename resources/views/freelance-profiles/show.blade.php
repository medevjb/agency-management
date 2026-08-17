@extends('layouts.dashboard')

@section('title', $profile->profile_name)

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

    .s-stat-value {
        margin-top: 0.25rem;
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #111827;
        font-variant-numeric: tabular-nums;
    }

    .dark .s-stat-value { color: #f3f4f6; }

    .s-stat-foot { margin-top: 0.125rem; font-size: 0.75rem; color: #9ca3af; }

    .s-grid-4 {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 1rem;
    }

    @media (min-width: 640px) { .s-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (min-width: 1024px) { .s-grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); } }

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

    .s-chip.is-new { background-color: #f3f4f6; color: #4b5563; }
    .dark .s-chip.is-new { background-color: #1f2937; color: #9ca3af; }

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
        margin-top: 0.5rem;
        height: 0.625rem;
        border-radius: 9999px;
        background-color: #f3f4f6;
        overflow: hidden;
        display: flex;
    }

    .dark .s-share { background-color: #1f2937; }

    .s-share span.is-new { display: block; height: 100%; background-image: linear-gradient(90deg, #059669 0%, #10b981 100%); }
    .s-share span.is-repeat {
        display: block;
        height: 100%;
        background-image: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);
        margin-left: 2px;
    }
</style>
@endpush

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('freelance-profiles.index') }}"
           class="inline-flex items-center text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-semibold transition-all">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Profiles
        </a>
        <span class="text-sm text-gray-500 dark:text-gray-400">Showing data for <span class="font-semibold text-gray-700 dark:text-gray-300">{{ \App\Support\GlobalMonth::label() }}</span></span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Profile Header Card -->
            <div class="s-panel overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 pt-8 pb-20 relative">
                    @if($profile->is_featured)
                        <div class="absolute top-4 right-4">
                            <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-4 py-2 rounded-full shadow-lg">
                                <i class="fas fa-star mr-1"></i>Featured
                            </span>
                        </div>
                    @endif
                </div>

                <div class="px-6 pb-6">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between -mt-16 mb-6">
                        <div class="flex flex-col sm:flex-row sm:items-end space-y-4 sm:space-y-0 sm:space-x-6 z-20">
                            @if($profile->avatar)
                                <img src="{{ asset('storage/' . $profile->avatar) }}"
                                     alt="{{ $profile->profile_name }}"
                                     class="w-32 h-32 rounded-2xl border-4 border-white dark:border-gray-900 shadow-xl object-cover">
                            @else
                                <div class="w-32 h-32 rounded-2xl border-4 border-white dark:border-gray-900 shadow-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-400 dark:text-gray-600 text-4xl"></i>
                                </div>
                            @endif
                            <div class="pb-2">
                                <div class="flex items-center space-x-3">
                                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $profile->profile_name }}</h1>
                                    @if($profile->is_verified)
                                        <i class="fas fa-badge-check text-blue-500 text-xl" title="Verified"></i>
                                    @endif
                                </div>
                                <p class="text-purple-600 dark:text-purple-400 font-semibold">{{ '@'.$profile->username }}
                                    <span class="text-gray-400 dark:text-gray-600 font-normal">&middot; {{ ucfirst($profile->platform ?? 'fiverr') }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-2 pb-2">
                            @can('edit profile')
                                <a href="{{ route('freelance-profiles.edit', $profile) }}"
                                   class="px-4 py-2 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/20 font-semibold text-sm transition-all">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </a>
                            @endcan
                            @can('delete profile')
                                <button onclick="deleteProfile('{{ $profile->getRouteKey() }}')"
                                        class="px-4 py-2 bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-500/20 font-semibold text-sm transition-all">
                                    <i class="fas fa-trash mr-2"></i>Delete
                                </button>
                            @endcan
                        </div>
                    </div>

                    <div class="mb-4">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $profile->status_badge }}">
                            <span class="w-2 h-2 rounded-full bg-current mr-2"></span>
                            {{ ucfirst($profile->status) }}
                        </span>
                    </div>

                    @if($profile->bio)
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">
                                <i class="fas fa-align-left mr-2 text-purple-600 dark:text-purple-400"></i>
                                About
                            </h3>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $profile->bio }}</p>
                        </div>
                    @endif

                    @if($profile->skills)
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">
                                <i class="fas fa-tools mr-2 text-purple-600 dark:text-purple-400"></i>
                                Skills
                            </h3>
                            <div class="flex flex-wrap gap-3">
                                @foreach(explode(',', $profile->skills) as $skill)
                                    <span class="px-4 py-2 bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 rounded-xl font-medium">
                                        {{ trim($skill) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Analytics -->
            <div class="s-grid-4">
                <div class="s-panel p-5">
                    <p class="s-eyebrow">Revenue</p>
                    <p class="s-stat-value">${{ number_format($analytics->monthRevenue($month), 2) }}</p>
                    <p class="s-stat-foot">{{ $analytics->monthOrderCount($month) }} orders this month</p>
                </div>
                <div class="s-panel p-5">
                    <p class="s-eyebrow">Orders</p>
                    <p class="s-stat-value">{{ $analytics->monthOrderCount($month) }}</p>
                    <p class="s-stat-foot">this month</p>
                </div>
                <div class="s-panel p-5">
                    <p class="s-eyebrow">Repeat Order Rate</p>
                    <p class="s-stat-value text-indigo-600 dark:text-indigo-400">{{ $analytics->monthRepeatOrderRate($month) }}%</p>
                    <p class="s-stat-foot">{{ $analytics->monthRepeatOrderCount($month) }} of {{ $analytics->monthOrderCount($month) }} orders were repeat</p>
                </div>
                <div class="s-panel p-5">
                    <p class="s-eyebrow">Avg. Order Value</p>
                    <p class="s-stat-value">${{ number_format($analytics->monthAverageOrderValue($month), 2) }}</p>
                    <p class="s-stat-foot">this month</p>
                </div>
            </div>

            <!-- New vs Repeat Business -->
            <div class="s-panel p-6">
                @php($split = $analytics->monthFreshRepeatSplit($month))
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-users mr-2 text-purple-600 dark:text-purple-400"></i>
                        Fresh vs. Repeat Business
                    </h3>
                    <span class="s-num text-sm font-bold text-gray-500 dark:text-gray-400">{{ $analytics->monthUniqueClientCount($month) }} unique clients</span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Share of this month's sales — green is fresh (new clients), yellow is repeat (clients who already ordered through this profile).</p>
                <div class="s-share">
                    @php($monthRevenue = $split['fresh']['revenue'] + $split['repeat']['revenue'])
                    @php($freshPct = $monthRevenue > 0 ? ($split['fresh']['revenue'] / $monthRevenue) * 100 : 0)
                    @php($repeatPct = 100 - $freshPct)
                    <span class="is-new" style="width: {{ $freshPct }}%"></span>
                    <span class="is-repeat" style="width: {{ $repeatPct }}%"></span>
                </div>
                <div class="flex items-center gap-6 mt-3 text-sm">
                    <span class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">${{ number_format($split['fresh']['revenue'], 2) }}</span>
                            Fresh — {{ $split['fresh']['orders'] }} orders / {{ $analytics->monthNewClientCount($month) }} clients
                        </span>
                    </span>
                    <span class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                        <span>
                            <span class="font-bold text-gray-900 dark:text-gray-100">${{ number_format($split['repeat']['revenue'], 2) }}</span>
                            Repeat — {{ $split['repeat']['orders'] }} orders / {{ $analytics->monthRepeatClientCount($month) }} clients
                        </span>
                    </span>
                </div>
            </div>

            <!-- Trend Charts -->
            <div class="s-panel p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-chart-line mr-2 text-purple-600 dark:text-purple-400"></i>
                    Revenue — Last 6 Months
                </h3>
                <canvas id="revenueTrendChart" height="90"></canvas>
            </div>

            <div class="s-panel p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-chart-bar mr-2 text-purple-600 dark:text-purple-400"></i>
                    Orders — Fresh vs. Repeat, Last 6 Months
                </h3>
                <canvas id="ordersSplitChart" height="90"></canvas>
            </div>

            <div class="s-panel p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-chart-line mr-2 text-purple-600 dark:text-purple-400"></i>
                    Revenue — Fresh vs. Repeat, Last 6 Months
                </h3>
                <canvas id="revenueSplitChart" height="90"></canvas>
            </div>

            <!-- Top Repeat Clients -->
            <div class="s-panel p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-crown mr-2 text-purple-600 dark:text-purple-400"></i>
                    Top Repeat Clients
                </h3>
                @php($topClients = $analytics->monthTopRepeatClients($month, 5))
                @if(empty($topClients))
                    <p class="text-sm text-gray-500 dark:text-gray-400">No repeat clients this month.</p>
                @else
                    <div>
                        @foreach($topClients as $client)
                            <div class="s-row">
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-gray-100 truncate">{{ \Illuminate\Support\Str::title($client['name']) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Last order {{ $client['last_order_at']?->diffForHumans() }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="s-num font-bold text-gray-900 dark:text-gray-100">${{ number_format($client['total'], 2) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $client['orders'] }} orders</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Sales Leaderboard -->
            <div class="s-panel p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-trophy mr-2 text-purple-600 dark:text-purple-400"></i>
                    Sales Leaderboard
                </h3>
                @php($salesRanking = $analytics->monthSalesRanking($month, 5))
                @if(empty($salesRanking))
                    <p class="text-sm text-gray-500 dark:text-gray-400">No sales credited to this profile yet this month.</p>
                @else
                    <div>
                        @foreach($salesRanking as $index => $seller)
                            @php($rank = $index + 1)
                            <div class="s-row">
                                <div class="min-w-0 flex items-center gap-3">
                                    <span class="s-num flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                                        {{ $rank === 1 ? 'bg-yellow-100 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400' :
                                           ($rank === 2 ? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' :
                                           ($rank === 3 ? 'bg-orange-100 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400' :
                                           'bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400')) }}">
                                        {{ $rank }}
                                    </span>
                                    <p class="font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $seller['name'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="s-num font-bold text-gray-900 dark:text-gray-100">${{ number_format($seller['amount'], 2) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $seller['orders'] }} {{ \Illuminate\Support\Str::plural('order', $seller['orders']) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Recent Orders -->
            <div class="s-panel p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-receipt mr-2 text-purple-600 dark:text-purple-400"></i>
                    Recent Orders
                </h3>
                @php($recentOrders = $analytics->monthRecentOrders($month, 8))
                @if($recentOrders->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">No orders placed through this profile this month.</p>
                @else
                    <div>
                        @foreach($recentOrders as $order)
                            <div class="s-row">
                                <div class="min-w-0">
                                    <a href="{{ route('orders.show', $order) }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:text-purple-600 dark:hover:text-purple-400 truncate block">
                                        {{ $order->name }}
                                    </a>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }} &middot; {{ ucfirst(str_replace('-', ' ', $order->status)) }}</p>
                                </div>
                                <div class="text-right flex items-center gap-3">
                                    <span class="s-chip {{ $order->is_repeat ? '' : 'is-new' }}">{{ $order->is_repeat ? 'Repeat' : 'New' }}</span>
                                    <span class="s-num font-bold text-gray-900 dark:text-gray-100">${{ number_format((float) $order->value, 2) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Breakdown -->
            <div class="s-panel p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-layer-group mr-2 text-purple-600 dark:text-purple-400"></i>
                    Order Status
                </h3>
                @php($statusColors = ['pending' => 'bg-gray-400', 'in-progress' => 'bg-blue-500', 'delivered' => 'bg-indigo-500', 'completed' => 'bg-emerald-500', 'cancelled' => 'bg-red-500'])
                @php($statuses = $analytics->monthStatusBreakdown($month))
                @if(empty($statuses))
                    <p class="text-sm text-gray-500 dark:text-gray-400">No orders this month.</p>
                @else
                    <div class="space-y-3">
                        @foreach($statuses as $status => $count)
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2 text-gray-700 dark:text-gray-300">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $statusColors[$status] ?? 'bg-gray-400' }}"></span>
                                    {{ ucfirst(str_replace('-', ' ', $status)) }}
                                </span>
                                <span class="s-num font-bold text-gray-900 dark:text-gray-100">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="s-panel p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-bolt mr-2 text-purple-600 dark:text-purple-400"></i>
                    Quick Actions
                </h3>
                <div class="space-y-3">
                    @can('edit profile')
                        <button onclick="toggleStatus('{{ $profile->getRouteKey() }}')"
                                class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-500/10 dark:to-indigo-500/10 text-purple-700 dark:text-purple-400 rounded-xl hover:from-purple-100 hover:to-indigo-100 dark:hover:from-purple-500/20 dark:hover:to-indigo-500/20 transition-all group">
                            <div class="flex items-center">
                                <div class="bg-purple-500 p-3 rounded-lg mr-4">
                                    <i class="fas fa-power-off text-white"></i>
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold">Toggle Status</p>
                                    <p class="text-sm text-purple-600 dark:text-purple-400">Switch between active/inactive</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    @endcan

                    @can('edit profile')
                        <button onclick="toggleVerify('{{ $profile->getRouteKey() }}')"
                                class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-500/10 dark:to-cyan-500/10 text-blue-700 dark:text-blue-400 rounded-xl hover:from-blue-100 hover:to-cyan-100 dark:hover:from-blue-500/20 dark:hover:to-cyan-500/20 transition-all group">
                            <div class="flex items-center">
                                <div class="bg-blue-500 p-3 rounded-lg mr-4">
                                    <i class="fas fa-badge-check text-white"></i>
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold">{{ $profile->is_verified ? 'Unverify' : 'Verify' }} Profile</p>
                                    <p class="text-sm text-blue-600 dark:text-blue-400">{{ $profile->is_verified ? 'Remove verification badge' : 'Add verification badge' }}</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    @endcan

                    @can('edit profile')
                        <button onclick="toggleFeatured('{{ $profile->getRouteKey() }}')"
                                class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-500/10 dark:to-orange-500/10 text-yellow-700 dark:text-yellow-400 rounded-xl hover:from-yellow-100 hover:to-orange-100 dark:hover:from-yellow-500/20 dark:hover:to-orange-500/20 transition-all group">
                            <div class="flex items-center">
                                <div class="bg-yellow-500 p-3 rounded-lg mr-4">
                                    <i class="fas fa-star text-white"></i>
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold">{{ $profile->is_featured ? 'Remove from' : 'Add to' }} Featured</p>
                                    <p class="text-sm text-yellow-600 dark:text-yellow-400">{{ $profile->is_featured ? 'Remove from featured list' : 'Show on featured list' }}</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    @endcan
                </div>
            </div>

            <!-- Profile Info Card -->
            <div class="s-panel p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-info-circle mr-2 text-purple-600 dark:text-purple-400"></i>
                    Profile Info
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-800">
                        <span class="text-gray-600 dark:text-gray-400">Status</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $profile->status_badge }}">
                            {{ ucfirst($profile->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-800">
                        <span class="text-gray-600 dark:text-gray-400">Verified</span>
                        <span class="{{ $profile->is_verified ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-600' }}">
                            <i class="fas {{ $profile->is_verified ? 'fa-check-circle' : 'fa-times-circle' }} text-xl"></i>
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100 dark:border-gray-800">
                        <span class="text-gray-600 dark:text-gray-400">Featured</span>
                        <span class="{{ $profile->is_featured ? 'text-yellow-600 dark:text-yellow-400' : 'text-gray-400 dark:text-gray-600' }}">
                            <i class="fas fa-star text-xl"></i>
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-gray-600 dark:text-gray-400">Member Since</span>
                        <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ $profile->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Author Card -->
            @if($profile->user)
                <div class="s-panel p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                        <i class="fas fa-user mr-2 text-purple-600 dark:text-purple-400"></i>
                        Owner
                    </h3>
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-purple-100 dark:bg-purple-500/10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-gray-100">{{ $profile->user->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $profile->user->email }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Timestamps Card -->
            <div class="s-panel p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4">
                    <i class="fas fa-clock mr-2 text-purple-600 dark:text-purple-400"></i>
                    Timestamps
                </h3>
                <div class="space-y-4">
                    <div class="py-3 border-b border-gray-100 dark:border-gray-800">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Created</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100 font-semibold">{{ $profile->created_at->format('M d, Y - g:i A') }}</p>
                    </div>
                    <div class="py-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Last Updated</p>
                        <p class="text-sm text-gray-900 dark:text-gray-100 font-semibold">{{ $profile->updated_at->format('M d, Y - g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ROUTES = {
    destroy: @js(route('freelance-profiles.destroy', ['profile' => '__ID__'])),
    toggleStatus: @js(route('freelance-profiles.toggle-status', ['profile' => '__ID__'])),
};

document.addEventListener('DOMContentLoaded', function () {
    const trend = @json($analytics->monthlyTrend(6));
    const split = @json($analytics->monthlyOrderSplit(6));
    const revenueSplit = @json($analytics->monthlyRevenueSplit(6));

    const isDarkMode = document.documentElement.classList.contains('dark');
    const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.08)' : '#f3f4f6';
    const tickColor = isDarkMode ? '#9ca3af' : '#6b7280';

    new Chart(document.getElementById('revenueTrendChart'), {
        type: 'bar',
        data: {
            labels: trend.map(m => m.label),
            datasets: [{
                label: 'Revenue',
                data: trend.map(m => m.revenue),
                backgroundColor: 'rgba(99,102,241,0.7)',
                borderColor: 'rgba(99,102,241,1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: ctx => '$' + ctx.parsed.y.toLocaleString(undefined, {minimumFractionDigits: 2}) } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: tickColor, callback: v => '$' + v.toLocaleString() } },
                x: { grid: { display: false }, ticks: { color: tickColor } }
            }
        }
    });

    new Chart(document.getElementById('ordersSplitChart'), {
        type: 'bar',
        data: {
            labels: split.map(m => m.label),
            datasets: [
                {
                    label: 'Fresh',
                    data: split.map(m => m.new),
                    backgroundColor: 'rgba(16,185,129,0.75)',
                    borderRadius: 4,
                },
                {
                    label: 'Repeat',
                    data: split.map(m => m.repeat),
                    backgroundColor: 'rgba(245,158,11,0.85)',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { color: tickColor, usePointStyle: true } }
            },
            scales: {
                y: { beginAtZero: true, stacked: true, grid: { color: gridColor }, ticks: { color: tickColor, precision: 0 } },
                x: { stacked: true, grid: { display: false }, ticks: { color: tickColor } }
            }
        }
    });

    new Chart(document.getElementById('revenueSplitChart'), {
        type: 'bar',
        data: {
            labels: revenueSplit.map(m => m.label),
            datasets: [
                {
                    label: 'Fresh',
                    data: revenueSplit.map(m => m.fresh),
                    backgroundColor: 'rgba(16,185,129,0.75)',
                    borderRadius: 4,
                },
                {
                    label: 'Repeat',
                    data: revenueSplit.map(m => m.repeat),
                    backgroundColor: 'rgba(245,158,11,0.85)',
                    borderRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { color: tickColor, usePointStyle: true } },
                tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': $' + ctx.parsed.y.toLocaleString(undefined, {minimumFractionDigits: 2}) } }
            },
            scales: {
                y: { beginAtZero: true, stacked: true, grid: { color: gridColor }, ticks: { color: tickColor, callback: v => '$' + v.toLocaleString() } },
                x: { stacked: true, grid: { display: false }, ticks: { color: tickColor } }
            }
        }
    });
});

function deleteProfile(id) {
    event.preventDefault();
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#9333ea',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(ROUTES.destroy.replace('__ID__', id))
                .then(response => {
                    Swal.fire({
                        title: 'Deleted!',
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonColor: '#9333ea'
                    }).then(() => {
                        window.location.href = '{{ route('freelance-profiles.index') }}';
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.response.data.message || 'Something went wrong.',
                        icon: 'error',
                        confirmButtonColor: '#9333ea'
                    });
                });
        }
    });
}

function toggleStatus(id) {
    axios.patch(ROUTES.toggleStatus.replace('__ID__', id))
        .then(response => {
            Swal.fire({
                title: 'Success!',
                text: response.data.message,
                icon: 'success',
                confirmButtonColor: '#9333ea'
            }).then(() => {
                window.location.reload();
            });
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: error.response.data.message || 'Something went wrong.',
                icon: 'error',
                confirmButtonColor: '#9333ea'
            });
        });
}

function toggleVerify(id) {
    axios.patch(`/freelance-profiles/${id}/toggle-verify`)
        .then(response => {
            Swal.fire({
                title: 'Success!',
                text: response.data.message,
                icon: 'success',
                confirmButtonColor: '#9333ea'
            }).then(() => {
                window.location.reload();
            });
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: error.response.data.message || 'Something went wrong.',
                icon: 'error',
                confirmButtonColor: '#9333ea'
            });
        });
}

function toggleFeatured(id) {
    axios.patch(`/freelance-profiles/${id}/toggle-featured`)
        .then(response => {
            Swal.fire({
                title: 'Success!',
                text: response.data.message,
                icon: 'success',
                confirmButtonColor: '#9333ea'
            }).then(() => {
                window.location.reload();
            });
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: error.response.data.message || 'Something went wrong.',
                icon: 'error',
                confirmButtonColor: '#9333ea'
            });
        });
}
</script>
@endsection
