@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Tasks</h1>
            <p class="text-gray-600 mt-2">Manage and track your tasks</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Create Task
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Tasks</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tasks text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">In Progress</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['in_progress'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-spinner text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Completed</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md mb-6 p-6">
        <form method="GET" action="{{ route('tasks.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in-progress" {{ request('status') === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="on-hold" {{ request('status') === 'on-hold' ? 'selected' : '' }}>On Hold</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                <select name="priority" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Priorities</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                <select name="project_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="tasks-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tasks as $task)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                            <div class="text-sm text-gray-500 truncate max-w-xs">{{ $task->description }}</div>
                            @if($task->created_by !== auth()->id() && $task->createdBy)
                                <div class="text-xs text-gray-400 mt-1">
                                    <i class="fas fa-user mr-1"></i>Created by {{ $task->createdBy->name }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <select onchange="updateStatus({{ $task->id }}, this.value)" class="px-3 py-1 text-xs rounded-full border-0 cursor-pointer
                                @if($task->status === 'completed') bg-green-100 text-green-800
                                @elseif($task->status === 'in-progress') bg-blue-100 text-blue-800
                                @elseif($task->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in-progress" {{ $task->status === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="on-hold" {{ $task->status === 'on-hold' ? 'selected' : '' }}>On Hold</option>
                                <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($task->priority === 'urgent') bg-red-100 text-red-800
                                @elseif($task->priority === 'high') bg-orange-100 text-orange-800
                                @elseif($task->priority === 'medium') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($task->assignedTo)
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ $task->assignedTo->name }}&background=random" alt="{{ $task->assignedTo->name }}">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $task->assignedTo->name }}</div>
                                    </div>
                                </div>
                            @else
                                <span class="text-sm text-gray-400">Unassigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($task->due_date)
                                <span class="{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-red-600 font-medium' : '' }}">
                                    {{ $task->due_date->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-gray-400"></span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($task->project)
                                <a href="{{ route('projects.show', $task->project->id) }}" class="text-indigo-600 hover:text-indigo-900">{{ $task->project->name }}</a>
                            @else
                                <span class="text-gray-400"></span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('tasks.show', $task->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($task->created_by === auth()->id() || auth()->user()->role === 'admin')
                                <a href="{{ route('tasks.edit', $task->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-tasks text-5xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No tasks found</h3>
                                <p class="text-gray-500 mb-4">Get started by creating your first task</p>
                                <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Create Task
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tasks->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $tasks->appends(request()->except('page'))->links() }}
        </div>
        @endif
    </div>
</div>

<script>
// Set default Axios CSRF token
axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';

function updateStatus(taskId, status) {
    axios.patch(`/tasks/${taskId}/status`, { status: status })
        .then(response => {
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            alert.textContent = 'Status updated successfully!';
            document.body.appendChild(alert);
            setTimeout(() => alert.remove(), 3000);
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating status');
        });
}
</script>
@endsection
