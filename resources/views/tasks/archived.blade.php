@extends('layouts.dashboard')

@section('title', 'Task Management')

@push('styles')
    <style>
        /* Task Board Layout */
        .task-board {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            min-height: calc(100vh - 300px);
        }

        @media (max-width: 1536px) {
            .task-board {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .task-board {
                grid-template-columns: 1fr;
            }
        }

        /* Task Column */
        .task-column {
            background: #f8fafc;
            border-radius: 16px;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            min-height: 500px;
        }

        .task-column-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 12px;
            font-weight: 700;
        }

        .column-pending .task-column-header {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }

        .column-in-progress .task-column-header {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }

        .column-on-hold .task-column-header {
            background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
            color: #6b21a8;
        }

        .column-completed .task-column-header {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }

        /* Task Card */
        .task-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.07);
            cursor: grab;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e5e7eb;
            position: relative;
            overflow: hidden;
        }

        .task-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-3px) scale(1.01);
            border-color: #d1d5db;
        }

        .task-card:active {
            cursor: grabbing;
        }

        .task-card.dragging {
            opacity: 0.5;
            transform: rotate(2deg) scale(0.95);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .task-card.drag-over {
            border: 2px dashed #6366f1;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }

        /* Priority Badge */
        .priority-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .priority-urgent {
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
            color: #991b1b;
        }

        .priority-high {
            background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
            color: #9a3412;
        }

        .priority-medium {
            background: linear-gradient(135deg, #fef08a 0%, #fde047 100%);
            color: #854d0e;
        }

        .priority-low {
            background: linear-gradient(135deg, #bbf7d0 0%, #86efac 100%);
            color: #166534;
        }

        /* Due Date Badge */
        .due-date-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .due-overdue {
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
            color: #991b1b;
        }

        .due-soon {
            background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
            color: #9a3412;
        }

        .due-normal {
            background: #f1f5f9;
            color: #64748b;
        }

        /* Quick Actions */
        .task-actions {
            display: flex;
            gap: 4px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .task-card:hover .task-actions {
            opacity: 1;
        }

        .action-btn {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card.active {
            border-color: #6366f1;
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
        }

        /* Filter Pills */
        .filter-pill {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid #e2e8f0;
            background: white;
        }

        .filter-pill:hover {
            transform: translateY(-2px);
            border-color: #6366f1;
        }

        .filter-pill.active {
            border-color: #6366f1;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
        }

        /* Avatar */
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 12px;
        }

        /* Bulk Selection */
        .task-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #6366f1;
        }

        .task-card.selected {
            ring: 2px solid #6366f1;
            ring-offset: 2px;
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Drop Zone */
        .drop-zone {
            min-height: 400px;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .drop-zone.drag-over {
            background: rgba(99, 102, 241, 0.1);
            border: 2px dashed #6366f1;
        }
    </style>
@endpush

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Task Board</h1>
            <p class="text-gray-600 mt-1">Drag and drop to organize your tasks</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openCreateModal()" class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                <i class="fas fa-plus mr-2"></i>New Task
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card" onclick="filterByStatus('all')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">All Tasks</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-tasks text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card" onclick="filterByStatus('pending')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card" onclick="filterByStatus('in-progress')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">In Progress</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['in_progress'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-spinner text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card" onclick="filterByStatus('completed')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Completed</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['completed'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[300px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchTasks" placeholder="Search tasks..." class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors">
                </div>
            </div>

            <div class="flex gap-2 flex-wrap">
                <button class="filter-pill active" data-filter="all" onclick="filterByStatus('all')">All</button>
                <button class="filter-pill" data-filter="pending" onclick="filterByStatus('pending')">Pending</button>
                <button class="filter-pill" data-filter="in-progress" onclick="filterByStatus('in-progress')">In Progress</button>
                <button class="filter-pill" data-filter="on-hold" onclick="filterByStatus('on-hold')">On Hold</button>
                <button class="filter-pill" data-filter="completed" onclick="filterByStatus('completed')">Completed</button>
            </div>

            <select id="filterPriority" onchange="applyFilters()" class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors font-semibold">
                <option value="">All Priorities</option>
                <option value="urgent">Urgent</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>

            <select id="filterProject" onchange="applyFilters()" class="px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors font-semibold">
                <option value="">All Projects</option>
                @foreach($orders as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>

            <button onclick="toggleBulkMode()" id="bulkModeBtn" class="px-4 py-3 border-2 border-gray-200 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                <i class="fas fa-check-square mr-2"></i>Select
            </button>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulkActionBar" class="hidden bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-4 mb-6">
        <div class="flex items-center justify-between text-white">
            <div class="flex items-center gap-4">
                <span class="font-semibold"><span id="selectedCount">0</span> tasks selected</span>
                <select id="bulkAction" class="px-4 py-2 rounded-lg text-gray-800 font-semibold">
                    <option value="">Bulk Actions</option>
                    <option value="mark-pending">Mark as Pending</option>
                    <option value="mark-in-progress">Mark as In Progress</option>
                    <option value="mark-on-hold">Mark as On Hold</option>
                    <option value="mark-completed">Mark as Completed</option>
                    <option value="delete">Delete Selected</option>
                </select>
                <button onclick="applyBulkAction()" class="px-4 py-2 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Apply
                </button>
            </div>
            <button onclick="clearSelection()" class="px-4 py-2 bg-white/20 rounded-lg font-semibold hover:bg-white/30 transition-colors">
                Clear Selection
            </button>
        </div>
    </div>

    <!-- Task Board -->
    <div class="task-board">
        <!-- Pending Column -->
        <div class="task-column column-pending">
            <div class="task-column-header">
                <div class="flex items-center gap-2">
                    <i class="fas fa-clock"></i>
                    <span>Pending</span>
                </div>
                <span class="bg-white/50 px-3 py-1 rounded-full text-sm">{{ count($tasks->where('status', 'pending')) }}</span>
            </div>
            <div class="drop-zone" data-status="pending" ondragover="handleDragOver(event)" ondrop="handleDrop(event, 'pending')" ondragleave="handleDragLeave(event)">
                @foreach($tasks->where('status', 'pending') as $task)
                    @include('tasks.partials.task-card')
                @endforeach
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="task-column column-in-progress">
            <div class="task-column-header">
                <div class="flex items-center gap-2">
                    <i class="fas fa-spinner"></i>
                    <span>In Progress</span>
                </div>
                <span class="bg-white/50 px-3 py-1 rounded-full text-sm">{{ count($tasks->where('status', 'in-progress')) }}</span>
            </div>
            <div class="drop-zone" data-status="in-progress" ondragover="handleDragOver(event)" ondrop="handleDrop(event, 'in-progress')" ondragleave="handleDragLeave(event)">
                @foreach($tasks->where('status', 'in-progress') as $task)
                    @include('tasks.partials.task-card')
                @endforeach
            </div>
        </div>

        <!-- On Hold Column -->
        <div class="task-column column-on-hold">
            <div class="task-column-header">
                <div class="flex items-center gap-2">
                    <i class="fas fa-pause-circle"></i>
                    <span>On Hold</span>
                </div>
                <span class="bg-white/50 px-3 py-1 rounded-full text-sm">{{ count($tasks->where('status', 'on-hold')) }}</span>
            </div>
            <div class="drop-zone" data-status="on-hold" ondragover="handleDragOver(event)" ondrop="handleDrop(event, 'on-hold')" ondragleave="handleDragLeave(event)">
                @foreach($tasks->where('status', 'on-hold') as $task)
                    @include('tasks.partials.task-card')
                @endforeach
            </div>
        </div>

        <!-- Completed Column -->
        <div class="task-column column-completed">
            <div class="task-column-header">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Completed</span>
                </div>
                <span class="bg-white/50 px-3 py-1 rounded-full text-sm">{{ count($tasks->where('status', 'completed')) }}</span>
            </div>
            <div class="drop-zone" data-status="completed" ondragover="handleDragOver(event)" ondrop="handleDrop(event, 'completed')" ondragleave="handleDragLeave(event)">
                @foreach($tasks->where('status', 'completed') as $task)
                    @include('tasks.partials.task-card')
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Task Modal -->
<div id="taskModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Create New Task</h3>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <form id="taskForm" class="p-6">
            <input type="hidden" id="taskId">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Title *</label>
                    <input type="text" id="taskTitle" required class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea id="taskDescription" rows="3" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                        <select id="taskStatus" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                            <option value="pending">Pending</option>
                            <option value="in-progress">In Progress</option>
                            <option value="on-hold">On Hold</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Priority</label>
                        <select id="taskPriority" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Due Date</label>
                        <input type="date" id="taskDueDate" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Assigned To</label>
                        <select id="taskAssignedTo" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Order</label>
                    <select id="taskProject" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                        <option value="">No Order</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}">{{ $order->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Estimated Hours</label>
                        <input type="number" id="taskEstimatedHours" min="0" step="0.5" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Actual Hours</label>
                        <input type="number" id="taskActualHours" min="0" step="0.5" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" class="px-6 py-3 border-2 border-gray-200 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all">
                    Save Task
                </button>
            </div>
        </form>
            </div>
        </div>
    </div>
</div>

<!-- View Task Modal -->
<div id="viewTaskModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeViewModal()"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div id="viewModalStatusIcon" class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                <!-- Status icon will be injected here -->
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white" id="viewModalTitle">Task Title</h3>
                                <p class="text-sm text-indigo-100" id="viewModalProject">Project Name</p>
                            </div>
                        </div>
                        <button onclick="closeViewModal()" class="text-white hover:text-gray-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-6">
                    <!-- Description -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Description</h4>
                        <div id="viewModalDescription" class="text-sm text-gray-600 bg-gray-50 rounded-xl p-4">
                            No description provided
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Status</h4>
                            <div id="viewModalStatus" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-semibold">
                                <!-- Status badge -->
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Priority</h4>
                            <div id="viewModalPriority" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-bold uppercase">
                                <!-- Priority badge -->
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Due Date</h4>
                            <div id="viewModalDueDate" class="text-sm text-gray-900 flex items-center gap-2">
                                <!-- Due date -->
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Assigned To</h4>
                            <div id="viewModalAssignedTo" class="flex items-center gap-2">
                                <!-- Assignee -->
                            </div>
                        </div>
                    </div>

                    <!-- Time Tracking -->
                    <div id="viewModalTimeTracking" class="mb-6 hidden">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Time Tracking</h4>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-lg">
                                <i class="fas fa-clock text-gray-500"></i>
                                <div>
                                    <p class="text-xs text-gray-500">Estimated</p>
                                    <p id="viewModalEstimatedHours" class="text-sm font-semibold text-gray-900">0h</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 px-4 py-2 bg-blue-50 rounded-lg border border-blue-200">
                                <i class="fas fa-stopwatch text-blue-600"></i>
                                <div>
                                    <p class="text-xs text-blue-600">Actual</p>
                                    <p id="viewModalActualHours" class="text-sm font-semibold text-blue-900">0h</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Created: <span id="viewModalCreatedAt" class="font-semibold"></span></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <button id="viewModalArchiveBtn" onclick="archiveTaskFromModal()" class="px-4 py-2 border border-orange-300 rounded-lg font-semibold text-orange-700 hover:bg-orange-50 transition-colors hidden">
                            <i class="fas fa-archive mr-2"></i>Archive
                        </button>
                        <button onclick="closeViewModal()" class="px-4 py-2 border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                            Close
                        </button>
                        <button id="viewModalEditBtn" class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all">
                            <i class="fas fa-edit mr-2"></i>Edit Task
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Task Management
let selectedTasks = new Set();
let bulkMode = false;
let draggedTask = null;

// Drag and Drop
function handleDragStart(event, taskId) {
    draggedTask = taskId;
    event.target.classList.add('dragging');
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', taskId);
}

function handleDragEnd(event) {
    event.target.classList.remove('dragging');
    document.querySelectorAll('.drop-zone').forEach(zone => {
        zone.classList.remove('drag-over');
    });
}

function handleDragOver(event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
    event.currentTarget.classList.add('drag-over');
}

function handleDragLeave(event) {
    event.currentTarget.classList.remove('drag-over');
}

function handleDrop(event, newStatus) {
    event.preventDefault();
    event.stopPropagation();
    event.currentTarget.classList.remove('drag-over');

    const taskId = event.dataTransfer.getData('text/plain');
    if (taskId) {
        updateTaskStatus(taskId, newStatus);
        draggedTask = null;
    }
}

// Update task status
function updateTaskStatus(taskId, status) {
    axios.patch(`/tasks/${taskId}/status`, { status: status })
        .then(response => {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Task status updated successfully!',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to update task status.',
            });
        });
}

// Filter by status
function filterByStatus(status) {
    const url = new URL(window.location);
    if (status === 'all') {
        url.searchParams.delete('status');
    } else {
        url.searchParams.set('status', status);
    }
    window.location = url.toString();
}

// Apply filters
function applyFilters() {
    const url = new URL(window.location);
    const priority = document.getElementById('filterPriority').value;
    const project = document.getElementById('filterProject').value;

    if (priority) {
        url.searchParams.set('priority', priority);
    } else {
        url.searchParams.delete('priority');
    }

    if (project) {
        url.searchParams.set('project_id', project);
    } else {
        url.searchParams.delete('project_id');
    }

    window.location = url.toString();
}

// Toggle bulk mode
function toggleBulkMode() {
    bulkMode = !bulkMode;
    const btn = document.getElementById('bulkModeBtn');

    if (bulkMode) {
        btn.classList.add('bg-indigo-100', 'border-indigo-500');
        document.querySelectorAll('.task-checkbox').forEach(cb => {
            cb.style.display = 'block';
        });
    } else {
        btn.classList.remove('bg-indigo-100', 'border-indigo-500');
        document.querySelectorAll('.task-checkbox').forEach(cb => {
            cb.style.display = 'none';
            cb.checked = false;
        });
        clearSelection();
    }
}

// Task selection
function toggleTaskSelection(taskId) {
    if (selectedTasks.has(taskId)) {
        selectedTasks.delete(taskId);
    } else {
        selectedTasks.add(taskId);
    }
    updateBulkActionBar();
}

function updateBulkActionBar() {
    const bulkBar = document.getElementById('bulkActionBar');
    const selectedCount = document.getElementById('selectedCount');

    if (selectedTasks.size > 0) {
        bulkBar.classList.remove('hidden');
        selectedCount.textContent = selectedTasks.size;
    } else {
        bulkBar.classList.add('hidden');
    }
}

function clearSelection() {
    selectedTasks.clear();
    document.querySelectorAll('.task-checkbox').forEach(cb => cb.checked = false);
    updateBulkActionBar();
}

// Apply bulk action
function applyBulkAction() {
    const action = document.getElementById('bulkAction').value;

    if (!action || selectedTasks.size === 0) {
        return;
    }

    if (action === 'delete') {
        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${selectedTasks.size} task(s)`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete them!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                executeBulkAction(action);
            }
        });
    } else {
        executeBulkAction(action);
    }
}

function executeBulkAction(action) {
    const statusMap = {
        'mark-pending': 'pending',
        'mark-in-progress': 'in-progress',
        'mark-completed': 'completed',
        'mark-on-hold': 'on-hold'
    };

    const data = { task_ids: Array.from(selectedTasks) };
    if (statusMap[action]) {
        data.status = statusMap[action];
    }

    axios.post('/tasks/bulk-update', data)
        .then(response => {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Bulk action completed successfully!',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to apply bulk action.',
            });
        });
}

// Delete task
function deleteTask(taskId, taskTitle) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to delete "${taskTitle}"`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/tasks/${taskId}`)
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Task has been deleted.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to delete task.',
                    });
                });
        }
    });
}

// Modal functions
function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Create New Task';
    document.getElementById('taskForm').reset();
    document.getElementById('taskId').value = '';
    document.getElementById('taskModal').classList.remove('hidden');
}

function openEditModal(taskId) {
    axios.get(`/tasks/${taskId}/edit`)
        .then(response => {
            const task = response.data.task;
            document.getElementById('modalTitle').textContent = 'Edit Task';
            document.getElementById('taskId').value = task.id;
            document.getElementById('taskTitle').value = task.title;
            document.getElementById('taskDescription').value = task.description || '';
            document.getElementById('taskStatus').value = task.status;
            document.getElementById('taskPriority').value = task.priority;
            document.getElementById('taskDueDate').value = task.due_date || '';
            document.getElementById('taskAssignedTo').value = task.assigned_to || '';
            document.getElementById('taskProject').value = task.project_id || '';
            document.getElementById('taskEstimatedHours').value = task.estimated_hours || '';
            document.getElementById('taskActualHours').value = task.actual_hours || '';

            document.getElementById('taskModal').classList.remove('hidden');
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to load task data.',
            });
        });
}

function closeModal() {
    document.getElementById('taskModal').classList.add('hidden');
}

// View Modal Functions
function openViewModal(taskId) {
    // Prevent opening when clicking action buttons
    if (event.target.closest('button')) {
        return;
    }

    axios.get(`/tasks/${taskId}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(response => {
            const task = response.data.task;

            // Set title and project
            document.getElementById('viewModalTitle').textContent = task.title;
            document.getElementById('viewModalProject').textContent = task.project ? task.project.name : 'No Project';

            // Set description
            document.getElementById('viewModalDescription').innerHTML = task.description || 'No description provided';

            // Set status
            const statusConfig = {
                'pending': { icon: 'fa-circle', color: 'bg-yellow-100 text-yellow-800', bg: 'bg-yellow-500' },
                'in-progress': { icon: 'fa-spinner fa-spin', color: 'bg-blue-100 text-blue-800', bg: 'bg-blue-500' },
                'on-hold': { icon: 'fa-pause', color: 'bg-purple-100 text-purple-800', bg: 'bg-purple-500' },
                'completed': { icon: 'fa-check', color: 'bg-green-100 text-green-800', bg: 'bg-green-500' }
            };
            const status = statusConfig[task.status];
            document.getElementById('viewModalStatus').innerHTML = `<i class="fas ${status.icon} mr-1"></i>${task.status.replace('-', ' ')}`;
            document.getElementById('viewModalStatus').className = `inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-semibold ${status.color}`;
            document.getElementById('viewModalStatusIcon').innerHTML = `<i class="fas ${status.icon} text-white"></i>`;

            // Set priority
            const priorityConfig = {
                'urgent': { color: 'bg-red-100 text-red-800', border: 'border-red-200' },
                'high': { color: 'bg-orange-100 text-orange-800', border: 'border-orange-200' },
                'medium': { color: 'bg-yellow-100 text-yellow-800', border: 'border-yellow-200' },
                'low': { color: 'bg-green-100 text-green-800', border: 'border-green-200' }
            };
            const priority = priorityConfig[task.priority];
            document.getElementById('viewModalPriority').textContent = task.priority;
            document.getElementById('viewModalPriority').className = `inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-bold uppercase border ${priority.color} ${priority.border}`;

            // Set due date
            if (task.due_date) {
                const dueDate = new Date(task.due_date);
                document.getElementById('viewModalDueDate').innerHTML = `<i class="far fa-calendar-alt text-gray-400"></i>${dueDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
            } else {
                document.getElementById('viewModalDueDate').innerHTML = '<span class="text-gray-400">No due date</span>';
            }

            // Set assignee
            if (task.assigned_to) {
                document.getElementById('viewModalAssignedTo').innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                        ${task.assigned_to.name[0]}
                    </div>
                    <span class="text-sm font-medium text-gray-700">${task.assigned_to.name}</span>
                `;
            } else {
                document.getElementById('viewModalAssignedTo').innerHTML = '<span class="text-sm text-gray-400">Unassigned</span>';
            }

            // Set time tracking
            if (task.estimated_hours || task.actual_hours) {
                document.getElementById('viewModalTimeTracking').classList.remove('hidden');
                document.getElementById('viewModalEstimatedHours').textContent = `${task.estimated_hours || 0}h`;
                document.getElementById('viewModalActualHours').textContent = `${task.actual_hours || 0}h`;
            } else {
                document.getElementById('viewModalTimeTracking').classList.add('hidden');
            }

            // Set created date
            if (task.created_at) {
                const createdDate = new Date(task.created_at);
                document.getElementById('viewModalCreatedAt').textContent = createdDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }

            // Show/hide archive button based on status
            const archiveBtn = document.getElementById('viewModalArchiveBtn');
            if (task.status === 'completed') {
                archiveBtn.classList.remove('hidden');
            } else {
                archiveBtn.classList.add('hidden');
            }

            // Store task ID for archive function
            window.currentViewTaskId = taskId;

            // Set edit button
            document.getElementById('viewModalEditBtn').onclick = function() {
                closeViewModal();
                openEditModal(taskId);
            };

            // Show modal
            document.getElementById('viewTaskModal').classList.remove('hidden');
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to load task details.',
            });
        });
}

function closeViewModal() {
    document.getElementById('viewTaskModal').classList.add('hidden');
}

function archiveTaskFromModal() {
    if (!window.currentViewTaskId) return;

    Swal.fire({
        title: 'Archive Task?',
        text: 'This task will be moved to the archive. You can restore it later from the Archived Tasks page.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, archive it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.post(`/tasks/${window.currentViewTaskId}/archive`)
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Archived!',
                        text: 'Task has been archived successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        closeViewModal();
                        location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to archive task.',
                    });
                });
        }
    });
}

// Form submission
document.getElementById('taskForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const taskId = document.getElementById('taskId').value;
    const data = {
        title: document.getElementById('taskTitle').value,
        description: document.getElementById('taskDescription').value,
        status: document.getElementById('taskStatus').value,
        priority: document.getElementById('taskPriority').value,
        due_date: document.getElementById('taskDueDate').value,
        assigned_to: document.getElementById('taskAssignedTo').value,
        project_id: document.getElementById('taskProject').value,
        estimated_hours: document.getElementById('taskEstimatedHours').value,
        actual_hours: document.getElementById('taskActualHours').value,
        _method: taskId ? 'PATCH' : 'POST'
    };

    const url = taskId ? `/tasks/${taskId}` : '/tasks';
    const method = 'post';

    axios.post(url, data)
        .then(response => {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: taskId ? 'Task updated successfully!' : 'Task created successfully!',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                closeModal();
                location.reload();
            });
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to save task.',
            });
        });
});

// Search with debounce
let searchTimeout;
document.getElementById('searchTasks').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const url = new URL(window.location);
        if (this.value) {
            url.searchParams.set('search', this.value);
        } else {
            url.searchParams.delete('search');
        }
        window.location = url.toString();
    }, 500);
});

// Set active filter on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');

    if (status) {
        document.querySelectorAll('.filter-pill').forEach(pill => {
            pill.classList.remove('active');
            if (pill.dataset.filter === status) {
                pill.classList.add('active');
            }
        });
    }

    // Hide checkboxes by default
    document.querySelectorAll('.task-checkbox').forEach(cb => {
        cb.style.display = 'none';
    });
});
</script>
@endpush
