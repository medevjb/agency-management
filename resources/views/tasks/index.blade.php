@extends('layouts.dashboard')

@push('styles')
    <style>
        /* Custom DataTables Styling */
        .dataTables_wrapper {
            padding: 1.5rem;
        }

        .dataTables_wrapper .dataTables_length {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }

        .dataTables_wrapper .dataTables_length label {
            color: #374151;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
        }

        .dataTables_wrapper .dataTables_length select {
            padding: 0.75rem 2.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            background-color: white;
            color: #374151;
            font-size: 0.875rem;
            font-weight: 500;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem 1rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .dataTables_wrapper .dataTables_length select:hover {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .dataTables_wrapper .dataTables_length select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
            display: flex;
            justify-content: flex-end;
        }

        .dataTables_wrapper .dataTables_filter label {
            color: #374151;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            padding: 0.75rem 2.5rem 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            background-color: white;
            color: #374151;
            font-size: 0.875rem;
            font-weight: 500;
            width: 300px;
            transition: all 0.2s;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'%3e%3ccircle cx='11' cy='11' r='8' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3e%3cpath d='m21 21-4.35-4.35' stroke='%236b7280' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: left 0.75rem center;
            background-size: 1rem 1rem;
            padding-left: 2.5rem;
        }

        .dataTables_wrapper .dataTables_filter input::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'%3e%3ccircle cx='11' cy='11' r='8' stroke='%23636f1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3e%3cpath d='m21 21-4.35-4.35' stroke='%23636f1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3e%3c/svg%3e");
            background-color: #f9fafb;
        }

        .dataTables_wrapper .dataTables_info {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .dataTables_wrapper .dataTables_paginate {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 0;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.75rem 1rem;
            margin: 0;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            background-color: white;
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 3rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            border-color: #d1d5db;
            color: #374151;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #6366f1 0%, #5558dd 100%);
            border-color: #6366f1;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
            transform: translateY(-1px);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: linear-gradient(135deg, #5558dd 0%, #4c51bf 100%);
            border-color: #4c51bf;
            box-shadow: 0 6px 16px rgba(85, 83, 221, 0.4);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.first,
        .dataTables_wrapper .dataTables_paginate .paginate_button.last {
            font-weight: 600;
            padding: 0.75rem 1.5rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next {
            padding: 0.75rem 1.25rem;
            font-weight: 600;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.ellipsis {
            padding: 0.75rem 1rem;
            background: transparent;
            border: none;
            color: #6b7280;
            cursor: default;
            box-shadow: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.ellipsis:hover {
            background: transparent;
            border: none;
            color: #6b7280;
            transform: none;
            box-shadow: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: white;
            border-color: #e5e7eb;
            color: #6b7280;
            transform: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Table styling */
        .display.dataTable {
            border-collapse: collapse;
        }

        .display.dataTable th {
            background-color: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
            color: #374151;
            font-weight: 600;
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.875rem;
        }

        .display.dataTable td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
            font-size: 0.875rem;
        }

        .display.dataTable tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Processing overlay */
        .dataTables_processing {
            background-color: rgba(255, 255, 255, 0.9);
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem 2rem;
            font-weight: 600;
            color: #374151;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
<div class="pt-4 sm:p-6 lg:p-8 lg:pb-3">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="mb-4">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">My Tasks</h1>
            <p class="text-gray-600 text-lg">Manage and track your tasks</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
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

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
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

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">In Progress</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $stats['in_progress'] }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-spinner text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
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

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 mb-6">
        <form id="filterForm" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-xs font-semibold text-gray-700 mb-1">
                        <i class="fas fa-toggle-on mr-1 text-indigo-500"></i>Status
                    </label>
                    <div class="relative">
                        <select id="status" name="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white appearance-none transition-all duration-200 text-sm">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="in-progress">In Progress</option>
                            <option value="on-hold">On Hold</option>
                            <option value="completed">Completed</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Priority Filter -->
                <div>
                    <label for="priority" class="block text-xs font-semibold text-gray-700 mb-1">
                        <i class="fas fa-flag mr-1 text-indigo-500"></i>Priority
                    </label>
                    <div class="relative">
                        <select id="priority" name="priority"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white appearance-none transition-all duration-200 text-sm">
                            <option value="">All Priorities</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Project Filter -->
                <div>
                    <label for="project_id" class="block text-xs font-semibold text-gray-700 mb-1">
                        <i class="fas fa-folder mr-1 text-indigo-500"></i>Project
                    </label>
                    <div class="relative">
                        <select id="project_id" name="project_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white appearance-none transition-all duration-200 text-sm">
                            <option value="">All Projects</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-end space-x-2">
                    <button type="button" onclick="applyFilters()"
                            class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-all duration-200 text-sm">
                        <i class="fas fa-search mr-1"></i>
                        Filter
                    </button>
                    <button type="button" onclick="clearFilters()"
                            class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 text-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden datatableprimary mb-5">
        <div class="overflow-x-auto">
            <table class="display w-full" id="tasksTable" style="width:100%">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-4 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Table rows will be populated by DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Simple DataTables initialization with jQuery from CDN
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Initializing tasks DataTable...');

            // Check if jQuery is loaded, if not load from CDN
            if (typeof $ === 'undefined') {
                console.log('Loading jQuery from CDN...');
                const script = document.createElement('script');
                script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
                script.onload = initializeDataTable;
                document.head.appendChild(script);
            } else {
                initializeDataTable();
            }
        });

        function initializeDataTable() {
            // Load DataTables CSS if not already loaded
            if (!document.querySelector('link[href*="dataTables"]')) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css';
                document.head.appendChild(link);
            }

            // Load DataTables JS if not available
            if (typeof $.fn.DataTable === 'undefined') {
                console.log('Loading DataTables from CDN...');
                const script = document.createElement('script');
                script.src = 'https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js';
                script.onload = createDataTable;
                document.head.appendChild(script);
            } else {
                createDataTable();
            }
        }

        function createDataTable() {
            console.log('Creating DataTable...');

            // Initialize DataTable
            $('#tasksTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('tasks.data') }}',
                    type: 'GET',
                    data: function(d) {
                        // Add custom filters from form
                        d.status = $('#status').val();
                        d.priority = $('#priority').val();
                        d.project_id = $('#project_id').val();
                        d.due_date = $('#due_date').val();
                    }
                },
                columns: [
                    {
                        data: 'title',
                        name: 'title',
                        render: function(data, type, row, meta) {
                            return '<div class="text-sm font-semibold text-gray-900">' + data + '</div>';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row, meta) {
                            const statusColors = {
                                'pending': 'bg-yellow-100 text-yellow-800',
                                'in-progress': 'bg-indigo-100 text-indigo-800',
                                'completed': 'bg-green-100 text-green-800',
                                'on-hold': 'bg-gray-100 text-gray-800'
                            };
                            const statusLabel = data.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase());
                            return '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm ' + statusColors[data] + '">' + statusLabel + '</span>';
                        }
                    },
                    {
                        data: 'priority',
                        name: 'priority',
                        render: function(data, type, row, meta) {
                            const priorityColors = {
                                'urgent': 'bg-red-100 text-red-800',
                                'high': 'bg-orange-100 text-orange-800',
                                'medium': 'bg-blue-100 text-blue-800',
                                'low': 'bg-gray-100 text-gray-800'
                            };
                            return '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm ' + priorityColors[data] + '">' + data.charAt(0).toUpperCase() + data.slice(1) + '</span>';
                        }
                    },
                    {
                        data: 'assigned_to',
                        name: 'assigned_to',
                        render: function(data, type, row, meta) {
                            return '<span class="text-sm text-gray-900">' + data + '</span>';
                        }
                    },
                    {
                        data: 'due_date',
                        name: 'due_date'
                    },
                    {
                        data: 'project',
                        name: 'project'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [6, 'desc']
                ], // Sort by created_at by default
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                responsive: true,
                language: {
                    processing: '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...',
                    emptyTable: 'No tasks found',
                    zeroRecords: 'No matching tasks found',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    infoFiltered: '(filtered from _MAX_ total entries)',
                    search: 'Search:',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
                },
                dom: 'lfrtip',
                initComplete: function() {
                    console.log('✅ DataTable initialized successfully');

                    // Add action button above the table
                    const actionButtons = `
                        <div class="flex flex-wrap items-center justify-end gap-2 mb-4 createbutton">
                            <a href="{{ route('tasks.create') }}"
                                class="inline-flex items-center px-3 py-1.5 h-8 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105 whitespace-nowrap">
                                <i class="fas fa-plus mr-1.5 text-xs"></i>
                                Create Task
                            </a>
                        </div>
                    `;

                    // Insert buttons before the table container
                    $('#tasksTable_wrapper').prepend(actionButtons);

                    // Filter form handlers - real-time search
                    $('#filterForm select').on('change', function() {
                        $('#tasksTable').DataTable().ajax.reload();
                    });
                }
            });
        }

        // Global functions
        function applyFilters() {
            // Reload the table with current filter values
            if (typeof $('#tasksTable').DataTable() !== 'undefined') {
                console.log('Applying filters...');
                $('#tasksTable').DataTable().ajax.reload(null, false); // false = no reset paging
                console.log('Filters applied successfully');
            }
        }

        function clearFilters() {
            // Clear all filter values
            $('#filterForm')[0].reset();

            // Reload the table
            if (typeof $('#tasksTable').DataTable() !== 'undefined') {
                $('#tasksTable').DataTable().ajax.reload();
            }

            console.log('Filters cleared successfully');
        }

        function deleteTask(taskId, taskTitle) {
            Swal.fire({
                title: 'Delete Task',
                text: `Are you sure you want to delete "${taskTitle}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete('/tasks/' + taskId, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire(
                                'Deleted!',
                                response.data.message,
                                'success'
                            );
                            if (typeof $('#tasksTable').DataTable() !== 'undefined') {
                                $('#tasksTable').DataTable().ajax.reload();
                            }
                        } else {
                            Swal.fire(
                                'Error',
                                response.data.message,
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        console.error('Delete error:', error);
                        const errorMessage = error.response?.data?.message ||
                                             error.response?.data?.error ||
                                             error.message ||
                                             'An error occurred while deleting the task';
                        Swal.fire(
                            'Error',
                            errorMessage,
                            'error'
                        );
                    });
                }
            });
        }
    </script>
@endpush
@endsection
