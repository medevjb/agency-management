@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Project Management Dashboard</h1>
            <p class="text-gray-600 mt-2">Welcome back, {{ Auth::user()->name }}! You are logged in as a <span class="font-semibold">{{ Auth::user()->role }}</span>.</p>
        </div>
        @can('manage all projects')
            <a href="{{ route('projects.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-folder-plus mr-2"></i>View All Projects
            </a>
        @endcan
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-dashboard-card
            title="Total Projects"
            value="{{ \App\Models\Project::count() }}"
            icon="fas fa-folder"
            color="blue"
            trend="up"
            trendValue="New projects this month"
        />
        <x-dashboard-card
            title="In Progress"
            value="{{ \App\Models\Project::where('status', 'in-progress')->count() }}"
            icon="fas fa-spinner"
            color="yellow"
            trend="stable"
            trendValue="Currently active"
        />
        <x-dashboard-card
            title="Completed"
            value="{{ \App\Models\Project::where('status', 'completed')->count() }}"
            icon="fas fa-check-circle"
            color="green"
            trend="up"
            trendValue="Successfully delivered"
        />
        <x-dashboard-card
            title="Total Tasks"
            value="{{ \App\Models\Task::count() }}"
            icon="fas fa-tasks"
            color="purple"
            trend="up"
            trendValue="Across all projects"
        />
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <x-dashboard-chart
            title="Projects by Status"
            type="bar"
        />
        <x-dashboard-chart
            title="Tasks Completion Rate"
            type="line"
        />
    </div>

    <!-- Recent Projects Table -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Recent Projects</h3>
            <a href="{{ route('projects.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $recentProjects = \App\Models\Project::with(['team', 'createdBy'])->latest()->take(5)->get();
                    @endphp
                    @forelse($recentProjects as $project)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $project->name }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($project->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($project->status === 'completed') bg-green-100 text-green-800
                                @elseif($project->status === 'in-progress') bg-blue-100 text-blue-800
                                @elseif($project->status === 'on-hold') bg-yellow-100 text-yellow-800
                                @elseif($project->status === 'planning') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst(str_replace('-', ' ', $project->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($project->priority === 'urgent') bg-red-100 text-red-800
                                @elseif($project->priority === 'high') bg-orange-100 text-orange-800
                                @elseif($project->priority === 'medium') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($project->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $project->team?->name ?? 'Unassigned' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $project->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            No projects found. <a href="{{ route('projects.create') }}" class="text-indigo-600 hover:text-indigo-800">Create your first project</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection