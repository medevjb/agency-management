@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header with Quick Stats -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}! 👋</h1>
                <p class="text-gray-600 mt-2">Here's what's happening with your projects today.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>New Project
                </a>
                <a href="{{ route('teams.create') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-users mr-2"></i>New Team
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-dashboard-card
            title="My Projects"
            value="{{ \App\Models\Project::where('created_by', Auth::id())->count() }}"
            icon="fas fa-folder"
            color="blue"
            trend="up"
            trendValue="Created by me"
        />
        <x-dashboard-card
            title="Active Tasks"
            value="{{ \App\Models\Task::where('assigned_to', Auth::id())->where('status', '!=', 'completed')->count() }}"
            icon="fas fa-check-square"
            color="green"
            trend="up"
            trendValue="Assigned to me"
        />
        <x-dashboard-card
            title="My Teams"
            value="{{ \App\Models\Team::where('team_leader_id', Auth::id())->count() }}"
            icon="fas fa-users"
            color="purple"
            trend="stable"
            trendValue="Teams I lead"
        />
        <x-dashboard-card
            title="Pending Reviews"
            value="{{ \App\Models\Task::where('assigned_to', Auth::id())->where('status', 'pending')->count() }}"
            icon="fas fa-clock"
            color="yellow"
            trend="up"
            trendValue="Awaiting action"
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- My Tasks - 2 columns -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">My Tasks</h3>
                    <p class="text-sm text-gray-500">Tasks assigned to you</p>
                </div>
                <div class="flex gap-2">
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                        {{ \App\Models\Task::where('assigned_to', Auth::id())->where('status', 'pending')->count() }} Pending
                    </span>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                        {{ \App\Models\Task::where('assigned_to', Auth::id())->where('status', 'completed')->count() }} Completed
                    </span>
                </div>
            </div>
            <div class="p-6">
                @php
                    $myTasks = \App\Models\Task::with(['project', 'assignedTo'])
                        ->where('assigned_to', Auth::id())
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp
                @forelse($myTasks as $task)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                @if($task->status === 'completed')
                                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                @elseif($task->priority === 'urgent')
                                    <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                                @elseif($task->priority === 'high')
                                    <i class="fas fa-arrow-circle-up text-orange-500 text-xl"></i>
                                @else
                                    <i class="far fa-circle text-gray-400 text-xl"></i>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $task->title }}</p>
                                <p class="text-xs text-gray-500">{{ $task->project?->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($task->due_date)
                                <p class="text-xs text-gray-500">
                                    <i class="far fa-calendar mr-1"></i>{{ $task->due_date->format('M d') }}
                                </p>
                            @endif
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full
                                @if($task->status === 'completed') bg-green-100 text-green-800
                                @elseif($task->status === 'in-progress') bg-blue-100 text-blue-800
                                @elseif($task->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-tasks text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No tasks assigned to you yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions & Activity -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-lg shadow-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-4">
                    <i class="fas fa-bolt mr-2"></i>Quick Actions
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('projects.create') }}" class="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                        <i class="fas fa-folder-plus w-6"></i>
                        <span class="font-medium">Create Project</span>
                    </a>
                    <a href="{{ route('tasks.create') }}" class="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                        <i class="fas fa-plus w-6"></i>
                        <span class="font-medium">Create Task</span>
                    </a>
                    <a href="{{ route('teams.create') }}" class="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                        <i class="fas fa-user-plus w-6"></i>
                        <span class="font-medium">Create Team</span>
                    </a>
                    <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 rounded-lg transition-colors">
                        <i class="fas fa-cog w-6"></i>
                        <span class="font-medium">Settings</span>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-history mr-2 text-indigo-600"></i>Recent Activity
                </h3>
                @php
                    $recentActivities = \App\Models\Project::where('created_by', Auth::id())
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp
                <div class="space-y-4">
                    @forelse($recentActivities as $activity)
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 w-2 h-2 mt-2 bg-indigo-600 rounded-full"></div>
                            <div>
                                <p class="text-sm text-gray-900">
                                    Created project <span class="font-medium">{{ $activity->name }}</span>
                                </p>
                                <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No recent activity</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Overview & Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Projects by Status -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Projects Overview</h3>
            <div class="space-y-4">
                @php
                    $projectStats = [
                        'completed' => \App\Models\Project::where('status', 'completed')->count(),
                        'in-progress' => \App\Models\Project::where('status', 'in-progress')->count(),
                        'planning' => \App\Models\Project::where('status', 'planning')->count(),
                        'on-hold' => \App\Models\Project::where('status', 'on-hold')->count(),
                    ];
                    $totalProjects = array_sum($projectStats);
                @endphp
                @foreach($projectStats as $status => $count)
                    @if($count > 0)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700">{{ ucfirst(str_replace('-', ' ', $status)) }}</span>
                            <span class="font-medium text-gray-900">{{ $count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full @if($status === 'completed') bg-green-500 @elseif($status === 'in-progress') bg-blue-500 @elseif($status === 'planning') bg-purple-500 @else bg-yellow-500 @endif"
                                 style="width: {{ ($count / max($totalProjects, 1)) * 100 }}%"></div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Recent Projects</h3>
                <a href="{{ route('projects.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
            </div>
            <div class="divide-y divide-gray-200">
                @php
                    $recentProjects = \App\Models\Project::with(['team', 'createdBy'])->latest()->take(5)->get();
                @endphp
                @forelse($recentProjects as $project)
                    <a href="{{ route('projects.show', $project->id) }}" class="block px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-folder text-indigo-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $project->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $project->team?->name ?? 'No team' }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($project->status === 'completed') bg-green-100 text-green-800
                                @elseif($project->status === 'in-progress') bg-blue-100 text-blue-800
                                @elseif($project->status === 'on-hold') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('-', ' ', $project->status)) }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-8 text-center">
                        <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No projects yet.</p>
                        <a href="{{ route('projects.create') }}" class="inline-block mt-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium">Create your first project</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Team Overview -->
    @can('view teams')
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">My Teams</h3>
                <p class="text-sm text-gray-500">Teams you're a member of</p>
            </div>
            <a href="{{ route('teams.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
        </div>
        <div class="p-6">
            @php
                $myTeams = \App\Models\Team::with('teamLeader')
                    ->latest()
                    ->take(6)
                    ->get();
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($myTeams as $team)
                    <a href="{{ route('teams.show', $team->id) }}" class="block p-4 border border-gray-200 rounded-lg hover:border-indigo-300 hover:shadow-md transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-medium text-gray-900">{{ $team->name }}</h4>
                            <span class="text-xs text-gray-500">
                                @if($team->team_leader)
                                    Leader: {{ $team->team_leader->name }}
                                @else
                                    No leader
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <i class="fas fa-user"></i>
                            <span>{{ $team->team_leader ? $team->team_leader->name : 'Unassigned' }}</span>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No teams yet.</p>
                        <a href="{{ route('teams.create') }}" class="inline-block mt-2 text-indigo-600 hover:text-indigo-800 text-sm font-medium">Create a team</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @endcan
</div>
@endsection
