@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Task</h1>
        <p class="text-gray-600 mt-2">Update task information</p>
    </div>

    <div class="max-w-4xl">
        <form method="POST" action="{{ route('tasks.update', $task->id) }}" class="bg-white rounded-lg shadow-md p-8">
            @csrf
            @method('PATCH')

            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Task Title *</label>
                <input type="text"
                       name="title"
                       id="title"
                       value="{{ old('title', $task->title) }}"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('title')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description"
                          id="description"
                          rows="4"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('description', $task->description) }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status"
                            id="status"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="pending" {{ old('status', $task->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in-progress" {{ old('status', $task->status) === 'in-progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="on-hold" {{ old('status', $task->status) === 'on-hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ old('status', $task->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority *</label>
                    <select name="priority"
                            id="priority"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="low" {{ old('priority', $task->priority) === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $task->priority) === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority', $task->priority) === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                    @error('priority')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                    <input type="date"
                           name="due_date"
                           id="due_date"
                           value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('due_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Project -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                    <select name="project_id"
                            id="project_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Select Project (Optional)</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Assign To -->
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Assign To</label>
                    <select name="assigned_to"
                            id="assigned_to"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Select User (Optional)</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->role }})</option>
                        @endforeach
                    </select>
                    @error('assigned_to')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estimated Hours -->
                <div>
                    <label for="estimated_hours" class="block text-sm font-medium text-gray-700 mb-2">Estimated Hours</label>
                    <input type="number"
                           name="estimated_hours"
                           id="estimated_hours"
                           value="{{ old('estimated_hours', $task->estimated_hours) }}"
                           min="1"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('estimated_hours')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actual Hours -->
            <div class="mb-6">
                <label for="actual_hours" class="block text-sm font-medium text-gray-700 mb-2">Actual Hours</label>
                <input type="number"
                       name="actual_hours"
                       id="actual_hours"
                       value="{{ old('actual_hours', $task->actual_hours) }}"
                       min="0"
                       class="w-full md:w-64 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('actual_hours')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('tasks.show', $task->id) }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>Update Task
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
