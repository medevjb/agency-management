@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Admin - User Management</h1>
            <p class="text-gray-600 mt-1">Manage system users and their administrative access</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Add User
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Total Users</p>
                    <p class="text-lg font-semibold">{{ $users->total() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Active</p>
                    <p class="text-lg font-semibold">{{ $users->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-user-times text-red-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Inactive</p>
                    <p class="text-lg font-semibold">{{ $users->where('is_active', false)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-user-shield text-purple-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Admins</p>
                    <p class="text-lg font-semibold">{{ App\Models\User::role('admin')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Users</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Role</label>
                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Team</label>
                <select name="team_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Teams</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                            {{ $team->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-search mr-2"></i> Filter
                    </button>
                    <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        <i class="fas fa-times mr-2"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ $user->name }}&background=4F46E5&color=fff" alt="{{ $user->name }}">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->employee_id ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @foreach($user->roles as $role)
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $role->name == 'admin' ? 'bg-red-100 text-red-800' :
                                           ($role->name == 'operation-manager' ? 'bg-blue-100 text-blue-800' :
                                           ($role->name == 'stack-leader' ? 'bg-purple-100 text-purple-800' :
                                           ($role->name == 'team-leader' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))) }}">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->team?->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_active)
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <button onclick="deleteUser({{ $user->id }})" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500 text-lg">No users found</p>
                                <p class="text-gray-400">Get started by creating your first user.</p>
                                <a href="{{ route('users.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add User
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $users->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ $users->firstItem() }}</span>
                            to
                            <span class="font-medium">{{ $users->lastItem() }}</span>
                            of
                            <span class="font-medium">{{ $users->total() }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
const ROUTES = {
    show: @js(route('users.show', ['user' => '__ID__'])),
    destroy: @js(route('users.destroy', ['user' => '__ID__'])),
};

function deleteUser(userId) {
    if (typeof Swal === 'undefined') {
        if (confirm('Are you sure you want to delete this user?')) {
            window.location.href = ROUTES.show.replace('__ID__', userId);
        }
        return;
    }

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
            // Create form for DELETE request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = ROUTES.destroy.replace('__ID__', userId);
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