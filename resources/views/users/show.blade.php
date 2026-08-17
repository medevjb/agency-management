@extends('layouts.dashboard')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2 text-sm"></i>
                        <span class="text-sm font-medium">Back to Users</span>
                    </a>
                </li>
            </ol>
        </nav>
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-2">User Profile</h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg">View detailed information about {{ $user->name }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('users.edit', $user) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-edit mr-2"></i> Edit User
                </a>
                @if(auth()->id() !== $user->id)
                <button onclick="deleteUser('{{ $user->getRouteKey() }}', '{{ $user->name }}')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Profile Card -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Profile Card -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
                <!-- Cover Image -->
                <div class="h-32 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 relative">
                    <div class="absolute -bottom-16 left-1/2 transform -translate-x-1/2">
                        <div class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-900 shadow-lg overflow-hidden bg-white dark:bg-gray-800">
                            <img class="w-full h-full object-cover"
                                 src="https://ui-avatars.com/api/?name={{ $user->name }}&background=4F46E5&color=fff&size=256&font-size=0.4"
                                 alt="{{ $user->name }}">
                        </div>
                    </div>
                    <div class="absolute top-4 right-4">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold shadow-sm {{ $user->is_active ? 'bg-green-400 text-green-900' : 'bg-red-400 text-red-900' }}">
                            <i class="fas fa-circle text-[6px] mr-1.5"></i>
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <!-- Profile Info -->
                <div class="pt-20 pb-6 px-6 text-center">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">{{ $user->name }}</h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">{{ $user->email }}</p>

                    @if($user->position)
                    <div class="inline-flex items-center px-4 py-2 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 rounded-full text-sm font-medium mb-4">
                        <i class="fas fa-briefcase mr-2"></i>
                        {{ $user->position }}
                    </div>
                    @endif

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-3 gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-800">
                        <div class="text-center">
                            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-500/10 rounded-xl flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-user-tag text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $user->roles->count() }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Roles</p>
                        </div>
                        <div class="text-center">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-500/10 rounded-xl flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-key text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $user->permissions->count() }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Perms</p>
                        </div>
                        <div class="text-center">
                            <div class="w-10 h-10 bg-pink-100 dark:bg-pink-500/10 rounded-xl flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-calendar text-pink-600 dark:text-pink-400"></i>
                            </div>
                            <p class="text-lg font-bold text-gray-900 dark:text-gray-100 mt-1">{{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Member Since</p>
                        </div>
                    </div>
                </div>

                <!-- Profile Details -->
                <div class="px-6 pb-6 space-y-1">
                    @if($user->employee_id)
                    <div class="flex items-center justify-between py-3 px-4 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-id-badge w-5 text-indigo-500 dark:text-indigo-400 mr-3"></i>
                            Employee ID
                        </div>
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $user->employee_id }}</span>
                    </div>
                    @endif

                    @if($user->phone)
                    <div class="flex items-center justify-between py-3 px-4 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-phone w-5 text-green-500 dark:text-green-400 mr-3"></i>
                            Phone
                        </div>
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $user->phone }}</span>
                    </div>
                    @endif

                    @if($user->department)
                    <div class="flex items-center justify-between py-3 px-4 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-building w-5 text-blue-500 dark:text-blue-400 mr-3"></i>
                            Department
                        </div>
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $user->department }}</span>
                    </div>
                    @endif

                    @if($user->hire_date)
                    <div class="flex items-center justify-between py-3 px-4 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-calendar-alt w-5 text-purple-500 dark:text-purple-400 mr-3"></i>
                            Hire Date
                        </div>
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $user->hire_date->format('M j, Y') }}</span>
                    </div>
                    @endif

                    @if($user->salary)
                    <div class="flex items-center justify-between py-3 px-4 rounded-xl bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-dollar-sign w-5 text-green-500 dark:text-green-400 mr-3"></i>
                            Salary
                        </div>
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">${{ number_format($user->salary, 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 p-6">
                <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center uppercase tracking-wide">
                    <i class="fas fa-info-circle text-indigo-600 dark:text-indigo-400 mr-2"></i>
                    Account Info
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Member Since</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $user->created_at->format('F j, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Last Updated</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                    @if($user->email_verified_at)
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Email Verified</span>
                        <span class="inline-flex items-center text-sm font-semibold text-green-600 dark:text-green-400">
                            <i class="fas fa-check-circle mr-1.5"></i> Verified
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Team & Tech Stack -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                    <i class="fas fa-sitemap text-indigo-600 dark:text-indigo-400 mr-2"></i>
                    Team & Tech Stack Assignments
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($user->team)
                    <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-500/10 dark:to-blue-500/10 rounded-xl border border-blue-200 dark:border-blue-900">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <div>
                                <span class="text-xs text-blue-600 dark:text-blue-400 font-semibold uppercase tracking-wide">Team</span>
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $user->team->name }}</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-gray-300 dark:bg-gray-700 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wide">Team</span>
                                <p class="text-gray-500 dark:text-gray-400">No team assigned</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($user->techStack)
                    <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-500/10 dark:to-green-500/10 rounded-xl border border-green-200 dark:border-green-900">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-layer-group text-white"></i>
                            </div>
                            <div>
                                <span class="text-xs text-green-600 dark:text-green-400 font-semibold uppercase tracking-wide">Tech Stack</span>
                                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $user->techStack->name }}</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-gray-300 dark:bg-gray-700 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-layer-group text-white"></i>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wide">Tech Stack</span>
                                <p class="text-gray-500 dark:text-gray-400">No tech stack assigned</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Roles -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                    <i class="fas fa-shield-alt text-purple-600 dark:text-purple-400 mr-2"></i>
                    Assigned Roles
                </h3>
                @if($user->roles->count() > 0)
                <div class="flex flex-wrap gap-3">
                    @foreach($user->roles as $role)
                    <div class="inline-flex items-center px-4 py-3 bg-gradient-to-r from-purple-100 to-purple-200 dark:from-purple-500/10 dark:to-purple-500/10 text-purple-800 dark:text-purple-400 rounded-xl text-sm font-semibold shadow-sm">
                        <i class="fas fa-user-tag mr-2"></i>
                        {{ ucfirst($role->name) }}
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <i class="fas fa-shield-alt text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">No role assigned</p>
                </div>
                @endif
            </div>

            <!-- Permissions -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center">
                        <i class="fas fa-key text-amber-600 dark:text-amber-400 mr-2"></i>
                        Permissions ({{ $user->permissions->count() }})
                    </h3>
                    @if($user->permissions->count() > 10)
                    <button onclick="togglePermissionsList()" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">
                        <i class="fas fa-chevron-down mr-1"></i>
                        <span id="togglePermissionsText">Show All</span>
                    </button>
                    @endif
                </div>
                @if($user->permissions->count() > 0)
                <div id="permissionsList" class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($user->permissions->sortBy('name')->groupBy(function ($item) {
                        return explode(' ', $item->name)[0];
                    }) as $group => $permissions)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                        <div class="bg-gray-50 dark:bg-gray-800 px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">{{ ucfirst($group) }}</span>
                        </div>
                        <div class="p-3">
                            <div class="flex flex-wrap gap-2">
                                @foreach($permissions as $permission)
                                <span class="inline-flex items-center px-3 py-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-xs shadow-sm">
                                    {{ ucfirst(str_replace('-', ' ', str_replace($group . ' ', '', $permission->name))) }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <i class="fas fa-key text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">No direct permissions assigned</p>
                </div>
                @endif
            </div>

            <!-- Address -->
            @if($user->address)
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-red-600 dark:text-red-400 mr-2"></i>
                    Address
                </h3>
                <div class="flex items-start p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <i class="fas fa-map-marker-alt text-red-500 dark:text-red-400 mt-1 mr-3"></i>
                    <p class="text-gray-700 dark:text-gray-300 flex-1">{{ $user->address }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
    
@endsection
@push('scripts')
<script>
const ROUTES = {
    destroy: @js(route('users.destroy', ['user' => '__ID__'])),
    index: @js(route('users.index')),
};

function deleteUser(userId, userName) {
    Swal.fire({
        title: 'Delete User?',
        text: `Are you sure you want to delete ${userName}? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(ROUTES.destroy.replace('__ID__', userId), {
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
                    ).then(() => {
                        window.location.href = ROUTES.index;
                    });
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
                                     'An error occurred while deleting the user';
                Swal.fire(
                    'Error',
                    errorMessage,
                    'error'
                );
            });
        }
    });
}

function togglePermissionsList() {
    const permissionsList = document.getElementById('permissionsList');
    const toggleText = document.getElementById('togglePermissionsText');

    if (permissionsList.style.maxHeight === 'none') {
        permissionsList.style.maxHeight = '24rem';
        permissionsList.style.overflowY = 'auto';
        toggleText.textContent = 'Show All';
    } else {
        permissionsList.style.maxHeight = 'none';
        permissionsList.style.overflowY = 'visible';
        toggleText.textContent = 'Show Less';
    }
}
</script>
@endpush
