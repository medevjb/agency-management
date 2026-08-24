@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Projects</h1>
            <p class="text-gray-600 mt-2">Manage and track all your projects</p>
        </div>
        @can('manage team projects')
            <a href="{{ route('projects.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>New Project
            </a>
        @endcan
    </div>

    <!-- Quick Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-center">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quick Filter</label>
                <select id="statusFilter" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Status</option>
                    <option value="planning">Planning</option>
                    <option value="in-progress">In Progress</option>
                    <option value="on-hold">On Hold</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <select id="priorityFilter" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Priorities</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="searchInput" placeholder="Search projects..." class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex items-end">
                <button id="resetFilters" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    <i class="fas fa-redo mr-2"></i>Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Projects DataTable -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Projects List</h3>
        </div>

        <div class="overflow-x-auto">
            <table id="projects-table" class="display table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Team</th>
                        <th>Tech Stack</th>
                        <th>Progress</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@push('styles')
<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #F0430A !important;
        color: white !important;
        border: 1px solid #F0430A !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #FF5A1F !important;
        color: white !important;
        border: 1px solid #FF5A1F !important;
    }

    .dt-buttons {
        margin-bottom: 1rem;
    }

    .dt-buttons .btn {
        margin-right: 0.5rem;
        background-color: #6b7280;
        border-color: #6b7280;
        color: white;
    }

    .dt-buttons .btn:hover {
        background-color: #4b5563;
        border-color: #4b5563;
    }

    #projects-table {
        font-size: 0.875rem;
    }

    #projects-table th {
        background-color: #f9fafb;
        font-weight: 600;
        color: #374151;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const $ = window.jQuery;

    if (!$ || !$.fn || !$.fn.DataTable) {
        console.error('jQuery or DataTables failed to load.');
        return;
    }

    var table = $('#projects-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '{{ route('projects.data') }}',
            type: 'GET',
            data: function(d) {
                d.status = $('#statusFilter').val();
                d.priority = $('#priorityFilter').val();
                d.search = $('#searchInput').val();
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables AJAX error:', {
                    status: xhr.status,
                    response: xhr.responseText,
                    error: error,
                    thrown: thrown
                });
            }
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'status', name: 'status' },
            { data: 'priority', name: 'priority' },
            { data: 'team.name', name: 'team.name' },
            { data: 'techStack.name', name: 'techStack.name' },
            { data: 'progress', name: 'progress', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            },
            emptyTable: "No projects available",
            zeroRecords: "No matching projects found"
        },
        pageLength: 25,
        order: [[0, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-secondary btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 6]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-secondary btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 6]
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-secondary btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 6]
                }
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Columns',
                className: 'btn btn-secondary btn-sm'
            }
        ]
    });

    $('#statusFilter, #priorityFilter').on('change', function() {
        table.ajax.reload();
    });

    let searchTimeout;
    $('#searchInput').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            table.ajax.reload();
        }, 500);
    });

    $('#resetFilters').on('click', function() {
        $('#statusFilter').val('');
        $('#priorityFilter').val('');
        $('#searchInput').val('');
        table.search('').columns().search('').ajax.reload();
    });
});
</script>
@endpush
@endsection
