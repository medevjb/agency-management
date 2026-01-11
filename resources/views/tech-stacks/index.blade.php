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
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Tech Stacks</h1>
            <p class="text-gray-600 text-lg">Manage and track all your technology stacks</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 mb-6">
        <form id="filterForm" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <!-- Leader Filter -->
                <div>
                    <label for="leaderFilter" class="block text-xs font-semibold text-gray-700 mb-1">
                        <i class="fas fa-user-tie mr-1 text-indigo-500"></i>Stack Leader
                    </label>
                    <div class="relative">
                        <select id="leaderFilter" name="leader"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white appearance-none transition-all duration-200 text-sm">
                            <option value="">All Leaders</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Member Count Filter -->
                <div>
                    <label for="memberCountFilter" class="block text-xs font-semibold text-gray-700 mb-1">
                        <i class="fas fa-users mr-1 text-indigo-500"></i>Members
                    </label>
                    <div class="relative">
                        <select id="memberCountFilter" name="member_count"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white appearance-none transition-all duration-200 text-sm">
                            <option value="">All Counts</option>
                            <option value="1-3">1-3 Members</option>
                            <option value="4-6">4-6 Members</option>
                            <option value="7+">7+ Members</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-end space-x-2">
                    <button type="button" onclick="clearFilters()"
                            class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 text-sm">
                        <i class="fas fa-times mr-1"></i>
                        Clear
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tech Stacks Table -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden datatableprimary mb-5">
        <div class="overflow-x-auto">
            <table class="display w-full" id="techStacksTable" style="width:100%">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tech Stack</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stack Leader</th>
                        <th class="px-4 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Members</th>
                        <th class="px-4 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Projects</th>
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
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Initializing tech stacks DataTable...');

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
            $('#techStacksTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('tech-stacks.data') }}',
                    type: 'GET',
                    data: function(d) {
                        // Add custom filters from form
                        d.leader = $('#leaderFilter').val();
                        d.member_count = $('#memberCountFilter').val();
                    },
                    dataSrc: function(json) {
                        console.log('Data received:', json);

                        // Populate leader filter on first load
                        if ($('#leaderFilter').find('option').length === 1) {
                            populateLeaderFilter(json.data);
                        }

                        return json.data;
                    }
                },
                columns: [
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            return `
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-md">
                                            <i class="fas fa-layer-group text-white text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">${data.name || 'No name'}</div>
                                        <div class="text-sm text-gray-500 truncate max-w-xs">${data.description || 'No description'}</div>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'stack_leader',
                        render: function(data, type, row, meta) {
                            if (data && data.name) {
                                return `
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-md">
                                                <i class="fas fa-user-tie text-white text-xs"></i>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">${data.name}</div>
                                            <div class="text-xs text-gray-500">${data.employee_id || 'N/A'}</div>
                                        </div>
                                    </div>
                                `;
                            }
                            return `
                                <div class="flex items-center text-gray-400">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-user-slash text-gray-500 text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="ml-2 text-sm">Not Assigned</div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'members_count',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            const memberCount = data ? parseInt(data.split(' ')[0]) : 0;
                            return `
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 shadow-sm">
                                    <i class="fas fa-users mr-1"></i>
                                    ${memberCount}
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'projects_count',
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            const projectCount = data ? parseInt(data.split(' ')[0]) : 0;
                            return `
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 shadow-sm">
                                    <i class="fas fa-folder-open mr-1"></i>
                                    ${projectCount}
                                </div>
                            `;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            const techStackId = data.DT_RowId || data.id;
                            return `
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="/tech-stacks/${techStackId}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 hover:bg-indigo-200 text-indigo-600 hover:text-indigo-800 transition-all duration-200 shadow-sm"
                                       title="View Tech Stack">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    <a href="/tech-stacks/${techStackId}/edit"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 hover:bg-blue-200 text-blue-600 hover:text-blue-800 transition-all duration-200 shadow-sm"
                                       title="Edit Tech Stack">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <button onclick="deleteTechStack(${techStackId})"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 hover:text-red-800 transition-all duration-200 shadow-sm"
                                            title="Delete Tech Stack">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                order: [
                    [0, 'asc']
                ],
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                responsive: true,
                language: {
                    processing: '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...',
                    emptyTable: 'No tech stacks available',
                    zeroRecords: 'No matching tech stacks found',
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
                    @can('create', \App\Models\TechStack::class)
                    const actionButtons = `
                        <div class="flex flex-wrap items-center justify-end gap-2 mb-4 createbutton">
                            <a href="{{ route('tech-stacks.create') }}"
                                class="inline-flex items-center px-3 py-1.5 h-8 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-medium rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105 whitespace-nowrap">
                                <i class="fas fa-plus mr-1.5 text-xs"></i>
                                New Tech Stack
                            </a>
                        </div>
                    `;
                    $('#techStacksTable_wrapper').prepend(actionButtons);
                    @endcan

                    // Filter form handlers - real-time search
                    $('#filterForm select').on('change', function() {
                        $('#techStacksTable').DataTable().ajax.reload(null, false);
                    });
                }
            });
        }

        function populateLeaderFilter(data) {
            const leaders = new Set();

            if (Array.isArray(data)) {
                data.forEach(techStack => {
                    if (techStack.stack_leader && techStack.stack_leader.name) {
                        leaders.add(techStack.stack_leader.name);
                    }
                });
            }

            const leaderFilter = $('#leaderFilter');
            leaderFilter.find('option:not(:first)').remove();

            Array.from(leaders).sort().forEach(leaderName => {
                const option = $('<option>', {
                    value: leaderName,
                    text: leaderName
                });
                leaderFilter.append(option);
            });
        }

        // Global functions
        function clearFilters() {
            $('#filterForm')[0].reset();

            if (typeof $('#techStacksTable').DataTable() !== 'undefined') {
                $('#techStacksTable').DataTable().ajax.reload();
            }

            console.log('Filters cleared successfully');
        }

        function deleteTechStack(techStackId) {
            Swal.fire({
                title: 'Delete Tech Stack?',
                text: 'Are you sure you want to delete this tech stack? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete('/tech-stacks/' + techStackId, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.data.message || 'Tech stack has been deleted successfully.',
                                icon: 'success',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });

                            if (typeof $('#techStacksTable').DataTable() !== 'undefined') {
                                $('#techStacksTable').DataTable().ajax.reload();
                            }
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.data.message || 'Failed to delete tech stack',
                                icon: 'error',
                                confirmButtonColor: '#3B82F6'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Delete error:', error);
                        const errorMessage = error.response?.data?.message ||
                                             error.response?.data?.error ||
                                             error.message ||
                                             'An error occurred while deleting the tech stack';
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonColor: '#3B82F6'
                        });
                    });
                }
            });
        }
    </script>
@endpush
@endsection
