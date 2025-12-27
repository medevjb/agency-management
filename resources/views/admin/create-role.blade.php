@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-start gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create New Role</h1>
            <p class="text-gray-600 mt-2">Create a new role and assign permissions</p>
        </div>
        <a href="{{ route('admin.roles') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to Roles
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('admin.roles.store') }}" class="bg-white rounded-lg shadow-md p-8">
                @csrf

                <!-- Role Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Role Name *</label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name') }}"
                           required
                           placeholder="e.g., content-manager"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">Use lowercase letters and hyphens only (e.g., project-manager)</p>
                </div>

                <!-- Permissions -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-sm font-medium text-gray-700">Permissions</label>
                        <div class="flex gap-2">
                            <button type="button" onclick="selectAll()" class="text-sm text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-check-double mr-1"></i>Select All
                            </button>
                            <button type="button" onclick="deselectAll()" class="text-sm text-gray-600 hover:text-gray-800">
                                <i class="fas fa-times mr-1"></i>Deselect All
                            </button>
                        </div>
                    </div>
                    <div class="space-y-4">
                        @foreach($permissions as $category => $categoryPermissions)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ ucfirst($category) }}</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach($categoryPermissions as $permission)
                                        <label class="flex items-center space-x-3 cursor-pointer p-2 rounded hover:bg-gray-50">
                                            <input type="checkbox"
                                                   name="permissions[]"
                                                   value="{{ $permission->id }}"
                                                   class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                            <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('permissions')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.roles') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-save mr-2"></i>Create Role
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Info Card -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-info-circle text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold">Role Creation Tips</h3>
                    </div>
                </div>
                <ul class="space-y-2 text-sm text-white/90">
                    <li class="flex items-start">
                        <i class="fas fa-check mt-1 mr-2"></i>
                        Use descriptive, lowercase names with hyphens
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check mt-1 mr-2"></i>
                        Assign only necessary permissions
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check mt-1 mr-2"></i>
                        Users can have multiple roles
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check mt-1 mr-2"></i>
                        After creating, you can assign users
                    </li>
                </ul>
            </div>

            <!-- Available Users Preview -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Available Users</h3>
                @php
                    $allUsers = \App\Models\User::all()->take(5);
                @endphp
                <div class="space-y-3">
                    @forelse($allUsers as $previewUser)
                        <div class="flex items-center p-2 rounded-lg hover:bg-gray-50">
                            <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ $previewUser->name }}&background=random" alt="">
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $previewUser->name }}</p>
                                <p class="text-xs text-gray-500">{{ $previewUser->email }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No users available</p>
                    @endforelse
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                    <p class="text-xs text-gray-500">After creating the role, you can assign users to it.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectAll() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = true);
}

function deselectAll() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
}
</script>
@endsection
