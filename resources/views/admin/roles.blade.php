@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Admin - Roles & Permissions</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage user roles and system permissions</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i>Create Role
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 dark:bg-purple-500/10 rounded-lg">
                    <i class="fas fa-user-tag text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Roles</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \Spatie\Permission\Models\Role::count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 dark:bg-blue-500/10 rounded-lg">
                    <i class="fas fa-key text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Permissions</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ \Spatie\Permission\Models\Permission::count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-900 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 dark:bg-green-500/10 rounded-lg">
                    <i class="fas fa-link text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Role Assignments</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        {{ \Spatie\Permission\Models\Role::withCount('users')->get()->sum('users_count') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">System Roles</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage user roles and their permissions</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Users</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Permissions</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    @php
                        $roles = \Spatie\Permission\Models\Role::withCount('users')->with('permissions')->get();
                    @endphp
                    @forelse($roles as $role)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full
                                        {{ $role->name == 'admin' ? 'bg-red-100 dark:bg-red-500/10' :
                                           ($role->name == 'operation-manager' ? 'bg-blue-100 dark:bg-blue-500/10' :
                                           ($role->name == 'stack-leader' ? 'bg-purple-100 dark:bg-purple-500/10' :
                                           ($role->name == 'team-leader' ? 'bg-green-100 dark:bg-green-500/10' : 'bg-gray-100 dark:bg-gray-800'))) }}">
                                        <i class="fas fa-user-tag
                                            {{ $role->name == 'admin' ? 'text-red-600 dark:text-red-400' :
                                               ($role->name == 'operation-manager' ? 'text-blue-600 dark:text-blue-400' :
                                               ($role->name == 'stack-leader' ? 'text-purple-600 dark:text-purple-400' :
                                               ($role->name == 'team-leader' ? 'text-green-600 dark:text-green-400' : 'text-gray-600 dark:text-gray-300'))) }}"></i>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ ucfirst($role->name) }}</div>
                                        @if($role->name === 'admin')
                                            <span class="text-xs text-red-600 dark:text-red-400">System Administrator</span>
                                        @elseif($role->name === 'operation-manager')
                                            <span class="text-xs text-blue-600 dark:text-blue-400">Operations Manager</span>
                                        @elseif($role->name === 'stack-leader')
                                            <span class="text-xs text-purple-600 dark:text-purple-400">Technical Leader</span>
                                        @elseif($role->name === 'team-leader')
                                            <span class="text-xs text-green-600 dark:text-green-400">Team Leader</span>
                                        @else
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Regular User</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-900 dark:text-gray-100">{{ $role->users_count }}</span>
                                    @if($role->users_count > 0)
                                        <div class="ml-2 -space-x-1">
                                            @php
                                                $roleUsers = \App\Models\User::role($role->name)->take(3)->get();
                                            @endphp
                                            @foreach($roleUsers as $user)
                                                <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-gray-900"
                                                     src="https://ui-avatars.com/api/?name={{ $user->name }}&background=4F46E5&color=fff"
                                                     alt="{{ $user->name }}" title="{{ $user->name }}">
                                            @endforeach
                                            @if($role->users_count > 3)
                                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full ring-2 ring-white dark:ring-gray-900 bg-gray-200 dark:bg-gray-700 text-xs text-gray-600 dark:text-gray-300">
                                                    +{{ $role->users_count - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @if($role->permissions->count() > 0)
                                        @foreach($role->permissions->take(3) as $permission)
                                            <span class="px-2 py-1 text-xs bg-blue-100 dark:bg-blue-500/10 text-blue-800 dark:text-blue-400 rounded-full">
                                                {{ $permission->name }}
                                            </span>
                                        @endforeach
                                        @if($role->permissions->count() > 3)
                                            <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 rounded-full">
                                                +{{ $role->permissions->count() - 3 }} more
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-400 dark:text-gray-500">No permissions assigned</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $role->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.roles.edit', $role->name) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($role->name !== 'admin')
                                        <button onclick="deleteRole('{{ $role->name }}')" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <i class="fas fa-user-tag text-gray-400 dark:text-gray-500 text-4xl mb-4"></i>
                                <p class="text-gray-500 dark:text-gray-400 text-lg">No roles found</p>
                                <p class="text-gray-400 dark:text-gray-500">Roles are automatically created by the system.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Permissions Overview -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">System Permissions</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Available permissions in the system</p>
        </div>
        <div class="p-6">
            @php
                $permissions = \Spatie\Permission\Models\Permission::all();
            @endphp
            @if($permissions->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($permissions as $permission)
                        <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <i class="fas fa-key text-blue-600 dark:text-blue-400 mr-3"></i>
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $permission->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Used by {{ $permission->roles->count() }} role(s)
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-key text-gray-400 dark:text-gray-500 text-4xl mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">No permissions found</p>
                    <p class="text-gray-400 dark:text-gray-500">Permissions are automatically created based on system features.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function deleteRole(roleName) {
    if (typeof Swal === 'undefined') {
        if (confirm('Are you sure you want to delete this role?')) {
            window.location.href = `/admin/roles/${roleName}/edit`;
        }
        return;
    }

    Swal.fire({
        title: 'Delete Role',
        text: "Are you sure you want to delete this role? This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete role',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form for DELETE request
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