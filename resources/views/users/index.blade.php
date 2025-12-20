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
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                    <i class="fas fa-filter text-indigo-600"></i>
                </div>
                Filters
            </h2>
            <button onclick="toggleFilters()" class="text-indigo-600 hover:text-indigo-800 transition-colors">
                <i class="fas fa-chevron-right" id="filterToggle"></i>
                <span class="ml-2 text-sm">Advanced</span>
            </button>
        </div>

        <!-- Basic Search (Always Visible) -->
        <form id="filterForm" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-search mr-2 text-indigo-500"></i>Search Users
                    </label>
                    <div class="relative">
                        <input type="text" id="search" name="search" value="{{ request('search') }}"
                               placeholder="Search by name, email, employee ID..."
                               class="w-full px-5 py-3 pl-12 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>

                <!-- Quick Status Filter -->
                <div>
                    <label for="quick_status" class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fas fa-toggle-on mr-2 text-indigo-500"></i>Status
                    </label>
                    <div class="relative">
                        <select id="quick_status" name="status"
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

                <!-- Quick Actions -->
                <div class="flex items-end space-x-2">
                    <button type="button" onclick="applyFilters()"
                            class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-all duration-200">
                        <i class="fas fa-search mr-2"></i>
                        Search
                    </button>
                    <button type="button" onclick="clearFilters()"
                            class="px-4 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-xl transition-all duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Advanced Filters (Hidden by default) -->
            <div id="advancedFilters" class="hidden mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-md font-semibold text-gray-800 mb-4">Advanced Filters</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Operations Section -->
    <div id="bulkOperations" class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4 mb-6 hidden">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-blue-900">
                    <span id="selectedCount">0</span> users selected
                </span>
                <button onclick="clearSelection()" class="text-sm text-blue-600 hover:text-blue-800 transition-colors">
                    Clear Selection
                </button>
            </div>
            <div class="flex flex-wrap gap-3">
                <!-- Bulk Role Assignment -->
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-blue-900">Assign Role:</label>
                    <select id="bulkRole" class="px-3 py-1 text-sm border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Bulk Team Assignment -->
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-blue-900">Assign Team:</label>
                    <select id="bulkTeam" class="px-3 py-1 text-sm border border-blue-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Select Team</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Apply Bulk Changes Button -->
                <button onclick="applyBulkOperations()"
                        class="px-4 py-1 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-check mr-1"></i> Apply
                </button>

                <!-- Delete Selected Button -->
                <button onclick="deleteSelectedUsers()"
                        class="px-4 py-1 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-1"></i> Delete Selected
                </button>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="usersTable">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-4 text-left">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tech Stack</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-4 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
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
let usersTable;
let selectedRows = [];

$(document).ready(function() {
    // Initialize DataTable
    usersTable = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 25,
        ajax: {
            url: '/users/data',
            type: 'GET',
            data: function(d) {
                // Add current filter values to request
                d.search = $('#search').val() || '';
                d.employee_id = $('#employee_id').val() || '';
                d.role = $('#role').val() || '';
                d.status = $('#quick_status').val() || '';
                d.team_id = $('#team_id').val() || '';
                d.tech_stack_id = $('#tech_stack_id').val() || '';
                d.department = $('#department').val() || '';
            }
        },
        columns: [
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return '<input type="checkbox" class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="' + row.id + '">';
                }
            },
            { data: 'name', orderable: true },
            { data: 'email', orderable: true },
            { data: 'employee_id', orderable: true },
            { data: 'phone', orderable: true },
            { data: 'department', orderable: true },
            { data: 'position', orderable: true },
            { data: 'team', orderable: true },
            { data: 'tech_stack', orderable: true },
            { data: 'role', orderable: true },
            { data: 'status', orderable: true },
            { data: 'created_at', orderable: true },
            {
                data: 'actions',
                orderable: false,
                className: 'text-center'
            }
        ],
        order: [[11, 'desc']], // Default sort by created_at desc
        language: {
            processing: '<div class="flex items-center"><i class="fas fa-spinner fa-spin mr-2"></i> Loading...</div>',
            emptyTable: 'No users found',
            zeroRecords: 'No matching users found'
        },
        drawCallback: function(settings) {
            // Re-attach event handlers after each draw
            attachCheckboxHandlers();
        }
    });

    // Auto-apply filters on input change (debounced)
    let searchTimeout;
    $('#search, #quick_status').on('input change', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            applyFilters();
        }, 500);
    });

    // Attach initial checkbox handlers
    attachCheckboxHandlers();

    // Select all functionality
    $('#selectAll').change(function() {
        const checkboxes = $('.user-checkbox');
        checkboxes.prop('checked', $(this).prop('checked'));
        updateBulkSelection();
    });
});

// Toggle filters visibility
function toggleFilters() {
    const advancedFilters = $('#advancedFilters');
    const toggle = $('#filterToggle');

    if (advancedFilters.hasClass('hidden')) {
        advancedFilters.removeClass('hidden');
        toggle.removeClass('fa-chevron-right').addClass('fa-chevron-down');
    } else {
        advancedFilters.addClass('hidden');
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

// Attach checkbox event handlers
function attachCheckboxHandlers() {
    // Individual checkbox change
    $('.user-checkbox').off('change').on('change', function() {
        updateBulkSelection();
    });
}

// Update bulk selection
function updateBulkSelection() {
    const checkboxes = $('.user-checkbox:checked');
    selectedRows = [];

    checkboxes.each(function() {
        selectedRows.push($(this).val());
    });

    // Update select all checkbox state
    const totalCheckboxes = $('.user-checkbox').length;
    const checkedCheckboxes = checkboxes.length;
    const selectAllCheckbox = $('#selectAll');

    if (checkedCheckboxes === 0) {
        selectAllCheckbox.prop('checked', false);
        selectAllCheckbox.prop('indeterminate', false);
    } else if (checkedCheckboxes === totalCheckboxes) {
        selectAllCheckbox.prop('checked', true);
        selectAllCheckbox.prop('indeterminate', false);
    } else {
        selectAllCheckbox.prop('indeterminate', true);
    }

    // Show/hide bulk operations
    const bulkOperations = $('#bulkOperations');
    if (selectedRows.length > 0) {
        bulkOperations.removeClass('hidden');
        $('#selectedCount').text(selectedRows.length);
    } else {
        bulkOperations.addClass('hidden');
    }
}

// Clear selection
function clearSelection() {
    $('.user-checkbox').prop('checked', false);
    $('#selectAll').prop('checked', false);
    selectedRows = [];
    updateBulkSelection();
}

// Apply bulk operations
function applyBulkOperations() {
    if (selectedRows.length === 0) {
        showToast('Please select users first', 'warning');
        return;
    }

    const role = $('#bulkRole').val();
    const team = $('#bulkTeam').val();

    if (!role && !team) {
        showToast('Please select a role or team to assign', 'warning');
        return;
    }

    Swal.fire({
        title: 'Apply Bulk Changes?',
        html: `This will update ${selectedRows.length} selected user(s).`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3B82F6',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Apply Changes',
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return fetch('/users/bulk-operation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    user_ids: selectedRows,
                    role: role,
                    team_id: team
                })
            })
            .then(response => response.json())
            .catch(error => {
                Swal.showValidationMessage('Request failed: ' + error);
            });
        }
    }).then(function(result) {
        if (result.isConfirmed) {
            if (result.value.success) {
                Swal.fire('Success!', result.value.message, 'success');
                usersTable.ajax.reload();
                clearSelection();
                $('#bulkRole').val('');
                $('#bulkTeam').val('');
            } else {
                Swal.fire('Error!', result.value.message, 'error');
            }
        }
    });
}

// Delete selected users
function deleteSelectedUsers() {
    if (selectedRows.length === 0) {
        showToast('Please select users first', 'warning');
        return;
    }

    Swal.fire({
        title: 'Delete Selected Users?',
        html: `This action cannot be undone. ${selectedRows.length} user(s) will be permanently deleted.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Delete Users',
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return fetch('/users/bulk-operation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    user_ids: selectedRows,
                    action: 'delete'
                })
            })
            .then(response => response.json())
            .catch(error => {
                Swal.showValidationMessage('Request failed: ' + error);
            });
        }
    }).then(function(result) {
        if (result.isConfirmed) {
            if (result.value.success) {
                Swal.fire('Deleted!', result.value.message, 'success');
                usersTable.ajax.reload();
                clearSelection();
            } else {
                Swal.fire('Error!', result.value.message, 'error');
            }
        }
    });
}

// Export selected users
function exportSelectedUsers() {
    if (selectedRows.length > 0) {
        window.location.href = '/users/export?user_ids=' + selectedRows.join(',');
    } else {
        window.location.href = '/users/export';
    }
}

// Delete user
function deleteUser(userId, userName) {
    Swal.fire({
        title: 'Delete User?',
        html: `Are you sure you want to delete <strong>${userName}</strong>?<br>This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Delete',
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return fetch(`/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .catch(error => {
                Swal.showValidationMessage('Request failed: ' + error);
            });
        }
    }).then(function(result) {
        if (result.isConfirmed) {
            if (result.value.success) {
                Swal.fire('Deleted!', result.value.message, 'success');
                usersTable.ajax.reload();
            } else {
                Swal.fire('Error!', result.value.message, 'error');
            }
        }
    });
}

// Toggle user status
function toggleUserStatus(userId, userName, status) {
    const statusText = status ? 'activate' : 'deactivate';

    Swal.fire({
        title: `${statusText.charAt(0).toUpperCase() + statusText.slice(1)} User?`,
        html: `Are you sure you want to ${statusText} <strong>${userName}</strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: status ? '#10B981' : '#F59E0B',
        cancelButtonColor: '#6B7280',
        confirmButtonText: statusText.charAt(0).toUpperCase() + statusText.slice(1),
        showLoaderOnConfirm: true,
        preConfirm: function() {
            return fetch(`/users/${userId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .catch(error => {
                Swal.showValidationMessage('Request failed: ' + error);
            });
        }
    }).then(function(result) {
        if (result.isConfirmed) {
            if (result.value.success) {
                Swal.fire('Success!', result.value.message, 'success');
                usersTable.ajax.reload();
            } else {
                Swal.fire('Error!', result.value.message, 'error');
            }
        }
    });
}

// Toast notification helper
function showToast(message, type = 'info') {
    // Simple toast implementation - you can replace with a better one
    const toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    toast.fire({
        icon: type,
        title: message
    });
}
</script>
@endpush
@endsection