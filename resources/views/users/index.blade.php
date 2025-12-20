@extends('layouts.dashboard')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-6">
            <div class="flex-1">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">User Management</h1>
                <p class="text-gray-600 text-lg">Manage users, roles, and permissions</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('users.bulk-import.form') }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                    <i class="fas fa-file-import mr-2"></i>
                    Bulk Import
                </a>
                <button onclick="exportSelectedUsers()"
                        class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                    <i class="fas fa-file-export mr-2"></i>
                    Export
                </button>
                <a href="{{ route('users.create') }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                    <i class="fas fa-user-plus mr-2"></i>
                    Add New User
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                    <i class="fas fa-filter text-indigo-600"></i>
                </div>
                Advanced Filters
            </h2>
            <button onclick="toggleFilters()" class="text-indigo-600 hover:text-indigo-800 transition-colors">
                <i class="fas fa-chevron-down" id="filterToggle"></i>
            </button>
        </div>

        <form id="filterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="filterFields">
            <!-- Search Input -->
            <div>
                <label for="search" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-search mr-2 text-indigo-500"></i>Search Users
                </label>
                <div class="relative">
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                           placeholder="Search by name, email..."
                           class="w-full px-5 py-3 pl-12 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Employee ID Filter -->
            <div>
                <label for="employee_id" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-id-badge mr-2 text-indigo-500"></i>Employee ID
                </label>
                <div class="relative">
                    <input type="text" id="employee_id" name="employee_id" value="{{ request('employee_id') }}"
                           placeholder="Search by employee ID..."
                           class="w-full px-5 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200">
                </div>
            </div>

            <!-- Role Filter -->
            <div>
                <label for="role" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-user-tag mr-2 text-indigo-500"></i>Role
                </label>
                <div class="relative">
                    <select id="role" name="role"
                            class="w-full px-5 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm appearance-none transition-all duration-200">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-toggle-on mr-2 text-indigo-500"></i>Status
                </label>
                <div class="relative">
                    <select id="status" name="status"
                            class="w-full px-5 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm appearance-none transition-all duration-200">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Team Filter -->
            <div>
                <label for="team_id" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-users mr-2 text-indigo-500"></i>Team
                </label>
                <div class="relative">
                    <select id="team_id" name="team_id"
                            class="w-full px-5 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm appearance-none transition-all duration-200">
                        <option value="">All Teams</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                {{ $team->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Department Filter -->
            <div>
                <label for="department" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-building mr-2 text-indigo-500"></i>Department
                </label>
                <div class="relative">
                    <select id="department" name="department"
                            class="w-full px-5 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm appearance-none transition-all duration-200">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department }}" {{ request('department') == $department ? 'selected' : '' }}>
                                {{ $department }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Tech Stack Filter -->
            <div>
                <label for="tech_stack_id" class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fas fa-code mr-2 text-indigo-500"></i>Tech Stack
                </label>
                <div class="relative">
                    <select id="tech_stack_id" name="tech_stack_id"
                            class="w-full px-5 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm appearance-none transition-all duration-200">
                        <option value="">All Tech Stacks</option>
                        @foreach($techStacks as $techStack)
                            <option value="{{ $techStack->id }}" {{ request('tech_stack_id') == $techStack->id ? 'selected' : '' }}>
                                {{ $techStack->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-end gap-3">
                <button type="button" onclick="applyFilters()"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all duration-200">
                    <i class="fas fa-search mr-2"></i> Apply Filters
                </button>
                <button type="button" onclick="clearFilters()"
                       class="flex-1 px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-700 font-semibold rounded-xl transition-all duration-200">
                    <i class="fas fa-times mr-2"></i> Clear
                </button>
            </div>
        </form>
    </div>

    <!-- Bulk Operations Section -->
    <div id="bulkOperationsSection" class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-lg border border-blue-100 p-6 mb-8 hidden">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-lg p-3 mr-4">
                    <i class="fas fa-tasks text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Bulk Operations</h3>
                    <p class="text-sm text-gray-600">Selected <span id="selectedCount">0</span> users</p>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <!-- Bulk Role Assignment -->
                <div class="flex items-center gap-2">
                    <select id="bulkRoleSelect" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Assign Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    <button onclick="bulkOperation('assign_role')" class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-user-tag mr-1"></i>Assign
                    </button>
                </div>

                <!-- Bulk Team Assignment -->
                <div class="flex items-center gap-2">
                    <select id="bulkTeamSelect" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Assign Team</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    <button onclick="bulkOperation('assign_team')" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-users mr-1"></i>Assign
                    </button>
                </div>

                <!-- Quick Actions -->
                <button onclick="bulkOperation('activate')" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    <i class="fas fa-toggle-on mr-1"></i>Activate
                </button>
                <button onclick="bulkOperation('deactivate')" class="px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
                    <i class="fas fa-toggle-off mr-1"></i>Deactivate
                </button>
                <button onclick="bulkOperation('delete')" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-1"></i>Delete
                </button>

                <button onclick="clearBulkSelection()" class="px-4 py-2 bg-gray-500 text-white text-sm font-medium rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-1"></i>Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Users DataTable -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="usersTable" class="display table table-striped" style="width:100%">
                <thead class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">
                            User
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">
                            Contact
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">
                            Role & Position
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">
                            Team & Department
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-indigo-700 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Statistics Footer -->
    <div class="mt-8">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                    <i class="fas fa-chart-bar text-indigo-600"></i>
                </div>
                User Statistics
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-gray-900" id="totalUsers">0</div>
                    <div class="text-sm text-gray-600 mt-1">Total Users</div>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-green-600" id="activeUsers">0</div>
                    <div class="text-sm text-gray-600 mt-1">Active</div>
                </div>
                <div class="bg-red-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-red-600" id="inactiveUsers">0</div>
                    <div class="text-sm text-gray-600 mt-1">Inactive</div>
                </div>
                <div class="bg-purple-50 rounded-lg p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600" id="adminUsers">0</div>
                    <div class="text-sm text-gray-600 mt-1">Admins</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let usersTable;
let selectedRows = [];

$(document).ready(function() {
    // Initialize DataTable
    usersTable = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/users/data',
            type: 'GET',
            data: function(d) {
                // Add current filter values to request
                d.search = $('#search').val() || '';
                d.employee_id = $('#employee_id').val() || '';
                d.role = $('#role').val() || '';
                d.status = $('#status').val() || '';
                d.team_id = $('#team_id').val() || '';
                d.department = $('#department').val() || '';
                d.tech_stack_id = $('#tech_stack_id').val() || '';
                return d;
            }
        },
        columns: [
            {
                data: 'checkbox',
                orderable: false,
                searchable: false,
                width: '30px',
                render: function(data, type, row) {
                    return `
                        <input type="checkbox" class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                               value="${row.id}" onchange="updateBulkSelection('${row.id}')">
                    `;
                }
            },
            {
                data: 'user',
                render: function(data, type, row) {
                    return `
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-12 w-12">
                                <img class="h-12 w-12 rounded-full object-cover border-2 border-gray-200"
                                     src="https://ui-avatars.com/api/?name=${encodeURIComponent(row.name)}&background=4F46E5&color=fff&size=48"
                                     alt="${row.name}">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">${row.name}</div>
                                <div class="text-sm text-gray-500">ID: ${row.employee_id || 'N/A'}</div>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: 'email',
                render: function(data, type, row) {
                    return `
                        <div class="text-sm text-gray-900">${row.email}</div>
                        <div class="text-sm text-gray-500">${row.phone || 'No phone'}</div>
                    `;
                }
            },
            {
                data: 'roles',
                render: function(data, type, row) {
                    let rolesHtml = '';
                    if (row.roles && row.roles.length > 0) {
                        rolesHtml = '<div class="flex flex-wrap gap-1 mb-2">';
                        row.roles.forEach(role => {
                            const colorClass = role.name === 'admin' ? 'bg-red-100 text-red-800' :
                                              (role.name === 'operation-manager' ? 'bg-blue-100 text-blue-800' :
                                              (role.name === 'stack-leader' ? 'bg-purple-100 text-purple-800' :
                                              (role.name === 'team-leader' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')));
                            rolesHtml += `<span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${colorClass}">${role.name}</span>`;
                        });
                        rolesHtml += '</div>';
                    }
                    return rolesHtml + `<div class="text-sm text-gray-900 font-medium">${row.position || 'N/A'}</div>`;
                }
            },
            {
                data: 'team',
                render: function(data, type, row) {
                    return `
                        <div class="text-sm text-gray-900 font-medium">${row.team ? row.team.name : 'No Team'}</div>
                        <div class="text-sm text-gray-500">${row.department || 'N/A'}</div>
                        <div class="text-sm text-gray-500">${row.tech_stack ? row.tech_stack.name : 'No Tech Stack'}</div>
                    `;
                }
            },
            {
                data: 'status',
                render: function(data, type, row) {
                    if (row.is_active) {
                        return '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Active</span>';
                    } else {
                        return '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Inactive</span>';
                    }
                }
            },
            {
                data: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let actions = '';
                    actions += `<a href="/users/${row.id}" class="text-indigo-600 hover:text-indigo-900 transition-colors" title="View"><i class="fas fa-eye"></i></a>`;
                    actions += `<a href="/users/${row.id}/edit" class="text-blue-600 hover:text-blue-900 transition-colors ml-2" title="Edit"><i class="fas fa-edit"></i></a>`;

                    if (row.id !== {{ auth()->id() }}) {
                        actions += `<button onclick="toggleUserStatus(${row.id})" class="text-orange-600 hover:text-orange-900 transition-colors ml-2" title="Toggle Status"><i class="fas fa-toggle-on"></i></button>`;
                        actions += `<button onclick="deleteUser(${row.id})" class="text-red-600 hover:text-red-900 transition-colors ml-2" title="Delete"><i class="fas fa-trash"></i></button>`;
                        actions += `<button onclick="resetUserPassword(${row.id})" class="text-purple-600 hover:text-purple-900 transition-colors ml-2" title="Reset Password"><i class="fas fa-key"></i></button>`;
                    }

                    return actions;
                }
            }
        ],
        order: [[1, 'desc']], // Sort by name column
        language: {
            processing: '<i class="fas fa-spinner fa-spin fa-2x"></i> Loading...',
            lengthMenu: 'Show _MENU_ entries',
            zeroRecords: 'No matching records found',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            infoFiltered: '(filtered from _MAX_ total entries)',
            search: 'Search:',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            },
            aria: {
                sortAscending: ': activate to sort column ascending',
                sortDescending: ': activate to sort column descending'
            }
        },
        initComplete: function(settings, json) {
            // Load statistics
            loadUserStats();
        }
    });

    // Select all functionality
    $('#selectAll').change(function() {
        const checkboxes = $('.user-checkbox');
        checkboxes.prop('checked', $(this).prop('checked'));
        updateBulkSelection();
    });

    // Auto-hide filters on mobile when page loads
    if (window.innerWidth < 768) {
        $('#filterFields').hide();
        $('#filterToggle').removeClass('fa-chevron-down').addClass('fa-chevron-right');
    }
});

// Toggle filters visibility
function toggleFilters() {
    const filterFields = $('#filterFields');
    const toggle = $('#filterToggle');

    if (filterFields.is(':hidden')) {
        filterFields.show();
        toggle.removeClass('fa-chevron-right').addClass('fa-chevron-down');
    } else {
        filterFields.hide();
        toggle.removeClass('fa-chevron-down').addClass('fa-chevron-right');
    }
}

// Apply filters
function applyFilters() {
    usersTable.ajax.reload();
}

// Clear filters
function clearFilters() {
    $('#filterForm')[0].reset();
    usersTable.ajax.reload();
}

// Bulk selection functionality
function updateBulkSelection(userId) {
    const checkbox = $(`input[value="${userId}"]`);

    if (checkbox.prop('checked')) {
        if (!selectedRows.includes(userId)) {
            selectedRows.push(userId);
        }
    } else {
        selectedRows = selectedRows.filter(id => id !== userId);
    }

    updateBulkOperationSection();
}

function updateBulkOperationSection() {
    const bulkSection = $('#bulkOperationsSection');
    const selectedCount = $('#selectedCount');

    if (selectedRows.length > 0) {
        bulkSection.removeClass('hidden');
        selectedCount.text(selectedRows.length);
    } else {
        bulkSection.addClass('hidden');
    }
}

// Clear bulk selection
function clearBulkSelection() {
    $('.user-checkbox').prop('checked', false);
    $('#selectAll').prop('checked', false);
    selectedRows = [];
    updateBulkOperationSection();
}

// Export functions
function exportSelectedUsers() {
    const filters = {
        search: $('#search').val() || '',
        employee_id: $('#employee_id').val() || '',
        role: $('#role').val() || '',
        status: $('#status').val() || '',
        team_id: $('#team_id').val() || '',
        department: $('#department').val() || '',
        tech_stack_id: $('#tech_stack_id').val() || '',
        selected_users: selectedRows.length > 0 ? selectedRows : null
    };

    const queryString = new URLSearchParams(filters).toString();
    window.open(`/users/export?${queryString}`, '_blank');
}

// Load user statistics
function loadUserStats() {
    fetch('/users/stats')
        .then(response => response.json())
        .then(data => {
            $('#totalUsers').text(data.stats.total_users);
            $('#activeUsers').text(data.stats.active_users);
            $('#inactiveUsers').text(data.stats.inactive_users);
            $('#adminUsers').text(data.role_stats.admin || 0);
        });
}

// Toggle user status
function toggleUserStatus(userId) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Toggle User Status',
            text: 'Are you sure you want to toggle this user\'s status?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, toggle status'
        }).then((result) => {
            if (result.isConfirmed) {
                performToggleStatus(userId);
            }
        });
    } else {
        if (confirm('Are you sure you want to toggle this user\'s status?')) {
            performToggleStatus(userId);
        }
    }
}

function performToggleStatus(userId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/users/${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            usersTable.ajax.reload();
            loadUserStats();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while toggling user status', 'error');
    });
}

// Delete user
function deleteUser(userId) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Delete User',
            text: "Are you sure you want to delete this user? This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete user',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                performDelete(userId);
            }
        });
    } else {
        if (confirm('Are you sure you want to delete this user?')) {
            performDelete(userId);
        }
    }
}

function performDelete(userId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/users/${userId}`;
    form.style.display = 'none';

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const methodField = document.createElement('input');
    methodField.type = 'hidden';
    methodField.name = '_method';
    methodField.value = 'DELETE';

    const tokenField = document.createElement('input');
    tokenField.type = 'hidden';
    tokenField.name = '_token';
    tokenField.value = csrfToken;

    form.appendChild(methodField);
    form.appendChild(tokenField);
    document.body.appendChild(form);
    form.submit();
}

// Reset user password
function resetUserPassword(userId) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Reset Password',
            text: 'Are you sure you want to reset this user\'s password?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reset password',
            input: 'password',
            inputPlaceholder: 'Enter new password',
            inputAttributes: {
                'minlength': 8,
                'autocomplete': 'new-password'
            },
            inputValidator: (value) => {
                if (!value) {
                    return 'Please enter a new password';
                }
                if (value.length < 8) {
                    return 'Password must be at least 8 characters long';
                }
                return null;
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                performPasswordReset(userId, result.value);
            }
        });
    } else {
        const newPassword = prompt('Enter new password (min 8 characters):');
        if (newPassword && newPassword.length >= 8) {
            performPasswordReset(userId, newPassword);
        } else if (newPassword) {
            alert('Password must be at least 8 characters long');
        }
    }
}

function performPasswordReset(userId, password) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/users/${userId}/reset-password`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            password: password,
            password_confirmation: password
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Password reset failed', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while resetting password', 'error');
    });
}

// Bulk operation function
function bulkOperation(operation) {
    if (selectedRows.length === 0) {
        showToast('Please select at least one user', 'warning');
        return;
    }

    let confirmMessage = '';
    let requestData = {
        user_ids: selectedRows,
        operation: operation
    };

    switch (operation) {
        case 'activate':
            confirmMessage = `Are you sure you want to activate ${selectedRows.length} user(s)?`;
            break;
        case 'deactivate':
            confirmMessage = `Are you sure you want to deactivate ${selectedRows.length} user(s)?`;
            break;
        case 'delete':
            confirmMessage = `Are you sure you want to delete ${selectedRows.length} user(s)? This action cannot be undone!`;
            break;
        case 'assign_role':
            const roleSelect = document.getElementById('bulkRoleSelect');
            if (!roleSelect.value) {
                showToast('Please select a role to assign', 'warning');
                return;
            }
            requestData.role = roleSelect.value;
            confirmMessage = `Assign role "${roleSelect.options[roleSelect.selectedIndex].text}" to ${selectedRows.length} user(s)?`;
            break;
        case 'assign_team':
            const teamSelect = document.getElementById('bulkTeamSelect');
            if (!teamSelect.value) {
                showToast('Please select a team to assign', 'warning');
                return;
            }
            requestData.team_id = teamSelect.value;
            confirmMessage = `Assign users to "${teamSelect.options[teamSelect.selectedIndex].text}" team?`;
            break;
    }

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Confirm Bulk Operation',
            text: confirmMessage,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                performBulkOperation(requestData);
            }
        });
    } else {
        if (confirm(confirmMessage)) {
            performBulkOperation(requestData);
        }
    }
}

// Perform bulk operation
function performBulkOperation(data) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/users/bulk-operation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showToast(result.message, 'success');
            clearBulkSelection();
            usersTable.ajax.reload();
            loadUserStats();
        } else {
            showToast(result.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while performing the operation', 'error');
    });
}

// Toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 flex items-center p-4 rounded-lg shadow-xl transform transition-all duration-300 translate-x-full`;

    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };

    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };

    toast.classList.add(...colors[type].split(' '));

    toast.innerHTML = `
        <div class="flex items-center text-white">
            <i class="fas ${icons[type]} mr-3"></i>
            <span class="font-medium">${message}</span>
        </div>
    `;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);

    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}
</script>
@endpush
@endsection