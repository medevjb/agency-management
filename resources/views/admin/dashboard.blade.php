@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="text-gray-600 mt-2">Welcome, Super Admin! Manage your entire application from here.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <x-dashboard-card
            title="Total Users"
            value="{{ $stats['total_users'] }}"
            icon="fas fa-users"
            color="blue"
            trend="up"
            trendValue="Active users"
        />
        <x-dashboard-card
            title="Total Projects"
            value="{{ $stats['total_projects'] }}"
            icon="fas fa-folder"
            color="green"
            trend="stable"
            trendValue="All projects"
        />
        <x-dashboard-card
            title="Total Tasks"
            value="{{ $stats['total_tasks'] }}"
            icon="fas fa-tasks"
            color="yellow"
            trend="up"
            trendValue="Active tasks"
        />
        <x-dashboard-card
            title="Teams"
            value="{{ $stats['total_teams'] }}"
            icon="fas fa-people-group"
            color="purple"
            trend="up"
            trendValue="Departments"
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Role Distribution Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Role Distribution</h3>
            <div class="space-y-3">
                @foreach($roleStats as $role => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('-', ' ', $role)) }}</span>
                        <div class="flex items-center">
                            <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ ($count / $stats['total_users']) * 100 }}%"></div>
                            </div>
                            <span class="text-sm text-gray-600">{{ $count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Project Status Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Project Status</h3>
            <div class="space-y-3">
                @foreach($projectStatusStats as $status => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('-', ' ', $status)) }}</span>
                        <div class="flex items-center">
                            <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="@if($status === 'completed') bg-green-500 @elseif($status === 'in-progress') bg-blue-500 @elseif($status === 'on-hold') bg-yellow-500 @else bg-gray-500 @endif h-2 rounded-full" style="width: {{ ($count / $stats['total_projects']) * 100 }}%"></div>
                            </div>
                            <span class="text-sm text-gray-600">{{ $count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Recent Users</h3>
                <a href="{{ route('admin.users') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentUsers as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ $user->name }}&background=4F46E5&color=fff" alt="{{ $user->name }}">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Recent Projects</h3>
                <a href="{{ route('projects.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentProjects as $project)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $project->name }}</div>
                                <div class="text-sm text-gray-500">{{ $project->createdBy->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($project->status === 'completed') bg-green-100 text-green-800
                                    @elseif($project->status === 'in-progress') bg-blue-100 text-blue-800
                                    @elseif($project->status === 'on-hold') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('-', ' ', $project->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $project->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection