@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-start gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Manage User Roles</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Assign roles to {{ $user->name }}</p>
        </div>
        <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Users
        </a>
    </div>

    <!-- User Info Card -->
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center">
            <img class="h-16 w-16 rounded-full" src="https://ui-avatars.com/api/?name={{ $user->name }}&background=4F46E5&color=fff&size=128" alt="{{ $user->name }}">
            <div class="ml-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $user->name }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    <i class="fas fa-building mr-1"></i>{{ $user->department ?? 'No department' }}
                    <span class="mx-2">•</span>
                    <i class="fas fa-briefcase mr-1"></i>{{ $user->position ?? 'No position' }}
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl">
        <form method="POST" action="{{ route('admin.users.roles.update', $user) }}" class="bg-white dark:bg-gray-900 rounded-lg shadow-md p-8">
            @csrf
            @method('PATCH')

            <!-- Current Roles Info -->
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Currently Assigned Roles</h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                            @if(count($userRoles) > 0)
                                {{ implode(', ', array_map(fn($r) => ucfirst($r), $userRoles)) }}
                            @else
                                No roles assigned yet
                            @endif
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-500/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-tag text-blue-600 dark:text-blue-400"></i>
                    </div>
                </div>
            </div>

            <!-- Roles Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">Assign Roles</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($roles as $role)
                        <label class="relative flex items-start p-4 border rounded-lg cursor-pointer transition-all
                            {{ in_array($role->name, $userRoles) ? 'border-indigo-500 dark:border-indigo-600 bg-indigo-50 dark:bg-indigo-500/10' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                            <input type="checkbox"
                                   name="roles[]"
                                   value="{{ $role->name }}"
                                   {{ in_array($role->name, $userRoles) ? 'checked' : '' }}
                                   class="sr-only peer">

                            <div class="flex items-center w-full">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center
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
                                </div>
                                <div class="ml-3 flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ ucfirst($role->name) }}</span>
                                        <div class="w-5 h-5 rounded border border-gray-300 dark:border-gray-600 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 flex items-center justify-center">
                                            <i class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $role->users_count }} user(s) • {{ $role->permissions->count() }} permission(s)
                                    </p>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('roles')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i>
                    Users can belong to multiple roles. The first selected role will be set as the primary role.
                </p>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-800">
                <a href="{{ route('users.index') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>Update Roles
                </button>
            </div>
        </form>
    </div>
</div>

<style>
input[type="checkbox"].sr-only:checked + div .w-5 {
    background-color: #F0430A;
    border-color: #F0430A;
}

input[type="checkbox"].sr-only:checked + div .w-5 i {
    opacity: 1;
}
</style>
@endsection
