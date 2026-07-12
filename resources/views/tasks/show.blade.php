@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-start gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $task->title }}</h1>
            <p class="text-gray-600 mt-2">Created {{ $task->created_at->diffForHumans() }}</p>
        </div>
        <div class="flex gap-3">
            @if($task->created_by === auth()->id() || auth()->user()->role === 'admin')
                <a href="{{ route('tasks.edit', $task->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit Task
                </a>
                <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
                    @csrf
                    @method('delete')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            @endif
            <a href="{{ route('tasks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Tasks
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Task Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status & Priority -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Status & Priority</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select onchange="updateStatus(this.value)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in-progress" {{ $task->status === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="on-hold" {{ $task->status === 'on-hold' ? 'selected' : '' }}>On Hold</option>
                            <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <span class="px-3 py-2 inline-flex text-sm leading-5 font-semibold rounded-full
                            @if($task->priority === 'urgent') bg-red-100 text-red-800
                            @elseif($task->priority === 'high') bg-orange-100 text-orange-800
                            @elseif($task->priority === 'medium') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($task->priority) }} Priority
                        </span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Description</h2>
                <div class="prose prose-gray max-w-none">
                    @if($task->description)
                        <p>{{ $task->description }}</p>
                    @else
                        <p class="text-gray-400 italic">No description provided</p>
                    @endif
                </div>
            </div>

            <!-- Dates & Hours -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Timeline & Hours</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Due Date</p>
                        <p class="text-lg font-semibold text-gray-900">
                            @if($task->due_date)
                                <span class="{{ $task->due_date->isPast() && $task->status !== 'completed' ? 'text-red-600' : '' }}">
                                    {{ $task->due_date->format('M d, Y') }}
                                </span>
                            @else
                                <span class="text-gray-400"></span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Created</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $task->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Estimated Hours</p>
                        <p class="text-lg font-semibold text-gray-900">
                            @if($task->estimated_hours)
                                {{ $task->estimated_hours }}h
                            @else
                                <span class="text-gray-400"></span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Actual Hours</p>
                        <p class="text-lg font-semibold text-gray-900">
                            @if($task->actual_hours)
                                {{ $task->actual_hours }}h
                            @else
                                <span class="text-gray-400"></span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Assigned To -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Assigned To</h2>
                @if($task->assignedTo)
                    <div class="flex items-center">
                        <img class="h-12 w-12 rounded-full" src="https://ui-avatars.com/api/?name={{ $task->assignedTo->name }}&background=random" alt="{{ $task->assignedTo->name }}">
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">{{ $task->assignedTo->name }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($task->assignedTo->role) }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-400">Myself</p>
                @endif
            </div>

            <!-- Created By -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Created By</h2>
                @if($task->createdBy)
                    <div class="flex items-center">
                        <img class="h-12 w-12 rounded-full" src="https://ui-avatars.com/api/?name={{ $task->createdBy->name }}&background=4F46E5&color=fff" alt="{{ $task->createdBy->name }}">
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">{{ $task->createdBy->name }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst($task->createdBy->role) }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Project -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Order</h2>
                @if($task->project)
                    <a href="{{ route('orders.show', $task->project->id) }}" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-folder text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $task->project->name }}</p>
                            <p class="text-xs text-gray-500">View Order</p>
                        </div>
                    </a>
                @else
                    <p class="text-gray-400">No order assigned</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Set default Axios CSRF token
axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';

function updateStatus(status) {
    axios.patch(`/tasks/{{ $task->id }}/status`, { status: status })
        .then(response => {
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
