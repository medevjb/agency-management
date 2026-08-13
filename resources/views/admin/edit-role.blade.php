@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-start gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Edit Role: {{ ucfirst($role->name) }}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Manage permissions and users for this role</p>
        </div>
        <a href="{{ route('admin.roles') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Roles
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Role Info Card -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center
                            {{ $role->name == 'admin' ? 'bg-red-100 dark:bg-red-500/10' :
                               ($role->name == 'operation-manager' ? 'bg-blue-100 dark:bg-blue-500/10' :
                               ($role->name == 'stack-leader' ? 'bg-purple-100 dark:bg-purple-500/10' :
                               ($role->name == 'team-leader' ? 'bg-green-100 dark:bg-green-500/10' : 'bg-gray-100 dark:bg-gray-800'))) }}">
                            <i class="fas fa-user-tag text-2xl
                                {{ $role->name == 'admin' ? 'text-red-600 dark:text-red-400' :
                                   ($role->name == 'operation-manager' ? 'text-blue-600 dark:text-blue-400' :
                                   ($role->name == 'stack-leader' ? 'text-purple-600 dark:text-purple-400' :
                                   ($role->name == 'team-leader' ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-300'))) }}"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ ucfirst($role->name) }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span id="userCount">{{ $role->users_count }}</span> user(s) •
                                <span id="permissionCount">{{ $role->permissions->count() }}</span> permission(s)
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Created</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $role->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Permissions Form -->
            <form method="POST" action="{{ route('admin.roles.update', $role->name) }}" class="bg-white dark:bg-gray-900 rounded-lg shadow-md p-6">
                @csrf
                @method('PATCH')

                <div class="flex justify-between items-center mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign Permissions</label>
                    <div class="flex gap-2">
                        <button type="button" onclick="selectAllPermissions()" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                            <i class="fas fa-check-double mr-1"></i>Select All
                        </button>
                        <button type="button" onclick="deselectAllPermissions()" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                            <i class="fas fa-times mr-1"></i>Deselect All
                        </button>
                    </div>
                </div>

                <div class="space-y-4 mb-6">
                    @foreach($permissions as $category => $categoryPermissions)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">{{ ucfirst($category) }}</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach($categoryPermissions as $permission)
                                    <label class="flex items-center space-x-3 cursor-pointer p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <input type="checkbox"
                                               name="permissions[]"
                                               value="{{ $permission->id }}"
                                               {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}
                                               class="w-4 h-4 text-indigo-600 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('permissions')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-800">
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-save mr-2"></i>Update Permissions
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar - User Assignment -->
        <div class="space-y-6">
            <!-- Users with this Role -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Users</h3>
                    <span class="text-xs bg-indigo-100 dark:bg-indigo-500/10 text-indigo-800 dark:text-indigo-400 px-2 py-1 rounded-full" id="roleUserCount">{{ $role->users_count }}</span>
                </div>

                @php
                    $roleUsers = \App\Models\User::role($role->name)->get();
                    $allUsers = \App\Models\User::whereDoesntHave('roles', function($q) use ($role) {
                        $q->where('name', $role->name);
                    })->get();
                @endphp

                <!-- Current Users List -->
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Assigned ({{ $roleUsers->count() }})</span>
                    </div>
                    <div class="space-y-2 max-h-64 overflow-y-auto" id="assignedUsers">
                        @forelse($roleUsers as $user)
                            <div class="flex items-center justify-between p-2 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg group" data-user-id="{{ $user->id }}">
                                <div class="flex items-center flex-1 min-w-0">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ $user->name }}&background=4F46E5&color=fff" alt="">
                                    <div class="ml-2 min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <button onclick="removeUserFromRole({{ $user->id }}, '{{ $user->name }}')" class="opacity-0 group-hover:opacity-100 p-1 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20 rounded transition-all">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">No users assigned</p>
                        @endforelse
                    </div>
                </div>

                <!-- Add User Section -->
                <div class="border-t border-gray-200 dark:border-gray-800 pt-4">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase block mb-2">Add User</span>
                    <select id="userSelect" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
                        <option value="">Select a user...</option>
                        @foreach($allUsers as $availableUser)
                            <option value="{{ $availableUser->id }}">{{ $availableUser->name }} ({{ $availableUser->email }})</option>
                        @endforeach
                    </select>
                    <button onclick="addUserToRole()" class="w-full mt-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-1"></i>Add User
                    </button>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
                <h3 class="font-semibold mb-4">Role Statistics</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-white/80">Assigned Users</span>
                        <span class="font-semibold">{{ $roleUsers->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-white/80">Permissions</span>
                        <span class="font-semibold">{{ $role->permissions->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-white/80">Available Users</span>
                        <span class="font-semibold">{{ $allUsers->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Role Actions -->
            @if($role->name !== 'admin')
                <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Actions</h3>
                    <button onclick="deleteRole('{{ $role->name }}')" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-trash mr-2"></i>Delete Role
                    </button>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">This action cannot be undone.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Set CSRF token for Axios
axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';

function selectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = true);
}

function deselectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
}

function addUserToRole() {
    const select = document.getElementById('userSelect');
    const userId = select.value;

    if (!userId) {
        Swal.fire({
            icon: 'warning',
            title: 'Select User',
            text: 'Please select a user to add to this role.',
            confirmButtonColor: '#6366f1'
        });
        return;
    }

    axios.post(`/admin/roles/{{ $role->name }}/users/${userId}`)
        .then(response => {
            Swal.fire({
                icon: 'success',
                title: 'User Added',
                text: response.data.message,
                timer: 2000,
                showConfirmButton: false
            });
            location.reload();
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.response?.data?.message || 'Failed to add user to role.',
                confirmButtonColor: '#ef4444'
            });
        });
}

function removeUserFromRole(userId, userName) {
    Swal.fire({
        title: 'Remove User',
        html: `Are you sure you want to remove <strong>${userName}</strong> from this role?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, remove',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/admin/roles/{{ $role->name }}/users/${userId}`)
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: 'User Removed',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    location.reload();
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response?.data?.message || 'Failed to remove user from role.',
                        confirmButtonColor: '#ef4444'
                    });
                });
        }
    });
}

function deleteRole(roleName) {
    Swal.fire({
        title: 'Delete Role',
        text: "Are you sure you want to delete this role? This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/roles/${roleName}`;
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
    });
}
</script>
@endsection
