@extends('layouts.dashboard')

@section('title', $team->name)

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }
    .member-card {
        transition: all 0.2s ease;
    }
    .member-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }
    .order-card {
        transition: all 0.2s ease;
    }
    .order-card:hover {
        border-color: #6366f1;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }
</style>
@endpush

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('teams.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Teams
                    </a>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-6 mt-6">
            <div class="flex-1">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900">{{ $team->name }}</h1>
                        <p class="text-gray-600 mt-2">{{ $team->description ?? 'No description provided' }}</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                @can('update', \App\Models\Team::class)
                <a href="{{ route('teams.edit', $team->id) }}" class="inline-flex items-center px-4 py-2.5 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-semibold transition-all">
                    <i class="fas fa-edit mr-2"></i>Edit Team
                </a>
                @endcan
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Members -->
        <div class="stat-card bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Total Members</p>
                    <p class="text-4xl font-bold">{{ $team->members->count() }}</p>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <i class="fas fa-users text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Team Leader -->
        <div class="stat-card bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Team Leader</p>
                    <p class="text-xl font-bold truncate">{{ $team->teamLeader?->name ?? 'Not Assigned' }}</p>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <i class="fas fa-user-tie text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Orders -->
        <div class="stat-card bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm font-medium mb-1">Active Orders</p>
                    <p class="text-4xl font-bold">{{ $team->orders->whereIn('status', ['pending', 'in-progress', 'on-hold'])->count() }}</p>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <i class="fas fa-project-diagram text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="stat-card bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl shadow-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm font-medium mb-1">Total Revenue</p>
                    <p class="text-2xl font-bold">${{ number_format($team->orders->where('status', 'completed')->sum('actual_revenue'), 0) }}</p>
                </div>
                <div class="bg-white/20 p-4 rounded-2xl">
                    <i class="fas fa-dollar-sign text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Team Leader Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 border-b border-green-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <div class="bg-green-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-user-tie text-green-600 text-lg"></i>
                            </div>
                            Team Leader
                        </h2>
                    </div>
                </div>
                <div class="p-6">
                    @if($team->teamLeader)
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0">
                                <img class="h-20 w-20 rounded-full border-4 border-green-100 shadow-md"
                                     src="https://ui-avatars.com/api/?name={{ urlencode($team->teamLeader->name) }}&background=10B981&color=fff&size=128"
                                     alt="{{ $team->teamLeader->name }}">
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-xl font-bold text-gray-900">{{ $team->teamLeader->name }}</h3>
                                    @if($team->teamLeader->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <i class="fas fa-circle text-[8px] mr-1"></i>Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            <i class="fas fa-circle text-[8px] mr-1"></i>Inactive
                                        </span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                    <span class="flex items-center">
                                        <i class="fas fa-id-badge mr-1.5 text-gray-400"></i>
                                        {{ $team->teamLeader->employee_id }}
                                    </span>
                                    @if($team->teamLeader->position)
                                    <span class="flex items-center">
                                        <i class="fas fa-briefcase mr-1.5 text-gray-400"></i>
                                        {{ $team->teamLeader->position }}
                                    </span>
                                    @endif
                                    <span class="flex items-center">
                                        <i class="fas fa-envelope mr-1.5 text-gray-400"></i>
                                        {{ $team->teamLeader->email }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-3">
                                <i class="fas fa-user-slash text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 font-medium">No team leader assigned</p>
                            <p class="text-sm text-gray-400 mt-1">Assign a team leader to manage this team</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Team Members Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b border-blue-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <div class="bg-blue-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-users text-blue-600 text-lg"></i>
                            </div>
                            Team Members ({{ $team->members->count() }})
                        </h2>
                        @can('addMembers', \App\Models\Team::class)
                        <button onclick="showAddMemberModal()"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-md transition-all">
                            <i class="fas fa-plus mr-2"></i>Add Members
                        </button>
                        @endcan
                    </div>
                </div>
                <div class="p-6">
                    @if($team->members->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($team->members as $member)
                                <div class="member-card flex items-center gap-4 p-4 border-2 border-gray-100 rounded-xl hover:border-indigo-300 bg-white">
                                    <div class="flex-shrink-0">
                                        <img class="h-14 w-14 rounded-full border-2 border-gray-100"
                                             src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=6366F1&color=fff&size=128"
                                             alt="{{ $member->name }}">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h3 class="text-base font-bold text-gray-900 truncate">{{ $member->name }}</h3>
                                            @if($member->id === $team->team_leader_id)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 flex-shrink-0">
                                                    Leader
                                                </span>
                                            @endif
                                        </div>
                                        <div class="space-y-1 text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-id-badge mr-2 text-gray-400 text-xs"></i>
                                                <span>{{ $member->employee_id }}</span>
                                            </div>
                                            @if($member->position)
                                            <div class="flex items-center">
                                                <i class="fas fa-briefcase mr-2 text-gray-400 text-xs"></i>
                                                <span>{{ $member->position }}</span>
                                            </div>
                                            @endif
                                            <div class="flex items-center">
                                                <i class="fas fa-envelope mr-2 text-gray-400 text-xs"></i>
                                                <span class="truncate">{{ $member->email }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @can('removeMember', \App\Models\Team::class)
                                        @if($member->id !== $team->team_leader_id)
                                        <form action="{{ route('teams.remove-member', [$team->id, $member->id]) }}"
                                              method="POST" onsubmit="return confirm('Remove {{ $member->name }} from this team?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="flex-shrink-0 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all"
                                                    title="Remove member">
                                                <i class="fas fa-user-minus"></i>
                                            </button>
                                        </form>
                                        @endif
                                    @endcan
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                                <i class="fas fa-users text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 font-medium text-lg">No team members yet</p>
                            <p class="text-sm text-gray-400 mt-1">Add members to start collaborating</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Team Orders -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 p-6 border-b border-purple-100">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <div class="bg-purple-100 rounded-lg p-2 mr-3">
                            <i class="fas fa-project-diagram text-purple-600 text-lg"></i>
                        </div>
                        Team Orders ({{ $team->orders->count() }})
                    </h2>
                </div>
                <div class="p-6">
                    @if($team->orders->count() > 0)
                        <div class="space-y-3">
                            @foreach($team->orders->sortByDesc('created_at')->take(10) as $order)
                                <div class="order-card flex items-center justify-between p-4 border-2 border-gray-100 rounded-xl hover:border-purple-300 bg-white">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 mb-2">
                                            <a href="{{ route('orders.show', $order->id) }}" class="text-lg font-semibold text-gray-900 hover:text-purple-600 truncate">
                                                {{ $order->name }}
                                            </a>
                                            @if($order->status === 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 flex-shrink-0">
                                                    <i class="fas fa-check-circle mr-1"></i>{{ str_replace('-', ' ', $order->status) }}
                                                </span>
                                            @elseif($order->status === 'in-progress')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 flex-shrink-0">
                                                    <i class="fas fa-spinner mr-1"></i>{{ str_replace('-', ' ', $order->status) }}
                                                </span>
                                            @elseif($order->status === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 flex-shrink-0">
                                                    <i class="fas fa-clock mr-1"></i>{{ str_replace('-', ' ', $order->status) }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 flex-shrink-0">
                                                    {{ str_replace('-', ' ', $order->status) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                            @if($order->end_date)
                                            <span class="flex items-center">
                                                <i class="fas fa-calendar mr-1.5 text-gray-400"></i>
                                                Due: {{ $order->end_date->format('M d, Y') }}
                                            </span>
                                            @endif
                                            @if($order->techStack)
                                            <span class="flex items-center">
                                                <i class="fas fa-layer-group mr-1.5 text-gray-400"></i>
                                                {{ $order->techStack->name }}
                                            </span>
                                            @endif
                                            @if($order->actual_revenue && $order->status === 'completed')
                                            <span class="flex items-center">
                                                <i class="fas fa-dollar-sign mr-1.5 text-gray-400"></i>
                                                ${{ number_format($order->actual_revenue, 2) }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('orders.show', $order->id) }}"
                                           class="inline-flex items-center px-3 py-2 bg-purple-50 text-purple-600 hover:bg-purple-100 text-sm font-medium rounded-lg transition-all">
                                            <i class="fas fa-eye mr-1.5"></i>View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($team->orders->count() > 10)
                            <div class="text-center mt-6">
                                <a href="{{ route('orders.index') }}?team_id={{ $team->id }}"
                                   class="inline-flex items-center px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition-all">
                                    View All {{ $team->orders->count() }} Orders
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                                <i class="fas fa-folder-open text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 font-medium text-lg">No orders assigned</p>
                            <p class="text-sm text-gray-400 mt-1">Orders will appear here when assigned</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Statistics -->
        <div class="space-y-8">
            <!-- Detailed Statistics -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-slate-50 p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <div class="bg-gray-200 rounded-lg p-2 mr-3">
                            <i class="fas fa-chart-bar text-gray-600 text-lg"></i>
                        </div>
                        Detailed Statistics
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Total Orders -->
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-folder text-blue-600"></i>
                            </div>
                            <span class="font-medium text-gray-900">Total Orders</span>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">{{ $team->orders->count() }}</span>
                    </div>

                    <!-- Active Orders -->
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-spinner text-blue-600"></i>
                            </div>
                            <span class="font-medium text-gray-900">Active</span>
                        </div>
                        <span class="text-2xl font-bold text-blue-600">{{ $team->orders->whereIn('status', ['pending', 'in-progress', 'on-hold'])->count() }}</span>
                    </div>

                    <!-- Completed Orders -->
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="bg-green-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-check-circle text-green-600"></i>
                            </div>
                            <span class="font-medium text-gray-900">Completed</span>
                        </div>
                        <span class="text-2xl font-bold text-green-600">{{ $team->orders->where('status', 'completed')->count() }}</span>
                    </div>

                    <!-- Total Revenue -->
                    <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-xl">
                        <div class="flex items-center">
                            <div class="bg-emerald-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-dollar-sign text-emerald-600"></i>
                            </div>
                            <span class="font-medium text-gray-900">Revenue</span>
                        </div>
                        <span class="text-2xl font-bold text-emerald-600">${{ number_format($team->orders->where('status', 'completed')->sum('actual_revenue'), 0) }}</span>
                    </div>

                    <!-- Tech Stack (if any) -->
                    @if($team->orders->whereNotNull('tech_stack_id')->count() > 0)
                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-sm font-semibold text-gray-700 mb-3">Tech Stacks Used</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($team->orders->whereNotNull('tech_stack_id')->pluck('techStack.name')->unique() as $stackName)
                                <span class="inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">
                                    <i class="fas fa-layer-group mr-1.5"></i>{{ $stackName }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            @can('update', \App\Models\Team::class)
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border-b border-indigo-100">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                            <i class="fas fa-bolt text-indigo-600 text-lg"></i>
                        </div>
                        Quick Actions
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('teams.edit', $team->id) }}"
                       class="flex items-center w-full px-4 py-3 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl font-medium transition-all">
                        <i class="fas fa-edit mr-3"></i>Edit Team
                    </a>
                    @can('delete', \App\Models\Team::class)
                    <form action="{{ route('teams.destroy', $team->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this team? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="flex items-center w-full px-4 py-3 bg-red-50 text-red-700 hover:bg-red-100 rounded-xl font-medium transition-all">
                            <i class="fas fa-trash mr-3"></i>Delete Team
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>

<!-- Add Member Modal -->
@can('addMembers', \App\Models\Team::class)
<div id="addMemberModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-user-plus text-indigo-600 mr-3"></i>
                    Add Team Members
                </h3>
                <button onclick="hideAddMemberModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('teams.add-members', $team->id) }}" method="POST">
            @csrf
            <div class="p-6">
                <div class="max-h-96 overflow-y-auto border-2 border-gray-200 rounded-xl p-4">
                    @if($availableUsers = \App\Models\User::whereNotIn('id', $team->members->pluck('id'))->where('is_active', true)->get()->count() > 0)
                        <div class="space-y-3">
                            @foreach($availableUsers as $user)
                                <label class="flex items-center p-4 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-indigo-300 hover:bg-indigo-50 transition-all">
                                    <input type="checkbox" name="members[]" value="{{ $user->id }}"
                                           class="w-5 h-5 text-indigo-600 bg-indigo-100 border-indigo-300 rounded focus:ring-2 focus:ring-indigo-500">
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center gap-3 mb-1">
                                            <img class="h-10 w-10 rounded-full border-2 border-gray-200"
                                                 src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366F1&color=fff&size=64"
                                                 alt="{{ $user->name }}">
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->employee_id }} @if($user->position) • {{ $user->position }} @endif</div>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <i class="fas fa-envelope mr-1"></i>{{ $user->email }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-3">
                                <i class="fas fa-user-check text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 font-medium">All active users are already members</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex justify-end gap-4 p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button type="button" onclick="hideAddMemberModal()"
                        class="px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                    Cancel
                </button>
                <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-medium shadow-md transition-all">
                    <i class="fas fa-plus mr-2"></i>Add Members
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@push('scripts')
<script>
function showAddMemberModal() {
    document.getElementById('addMemberModal').classList.remove('hidden');
}

function hideAddMemberModal() {
    document.getElementById('addMemberModal').classList.add('hidden');
}
</script>
@endpush
@endsection
