@extends('layouts.dashboard')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2 text-sm"></i>
                        <span class="text-sm font-medium">Back to Users</span>
                    </a>
                </li>
            </ol>
        </nav>
        <div class="mt-6">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Create New User</h1>
            <p class="text-gray-600 text-lg">Add a new user to the system with appropriate roles and permissions</p>
        </div>
    </div>

    <!-- Create User Form -->
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 p-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl mb-4">
                    <i class="fas fa-user-plus text-3xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">User Information</h2>
                <p class="text-purple-100">Fill in the user details and assign appropriate permissions</p>
            </div>

            <form method="POST" action="{{ route('users.store') }}" id="createUserForm">
                @csrf
                <div class="p-8">
                    <!-- Progress Steps -->
                    <div class="mb-8">
                        <div class="flex items-center justify-center space-x-4">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 bg-indigo-600 rounded-full text-white font-semibold" id="step1">1</div>
                                <span class="ml-2 font-medium text-gray-900">Basic Info</span>
                            </div>
                            <div class="w-12 h-1 bg-gray-300 rounded" id="progress1"></div>
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 bg-gray-300 rounded-full text-gray-600 font-semibold" id="step2">2</div>
                                <span class="ml-2 font-medium text-gray-500">Professional</span>
                            </div>
                            <div class="w-12 h-1 bg-gray-300 rounded" id="progress2"></div>
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 bg-gray-300 rounded-full text-gray-600 font-semibold" id="step3">3</div>
                                <span class="ml-2 font-medium text-gray-500">Assignments</span>
                            </div>
                            <div class="w-12 h-1 bg-gray-300 rounded" id="progress3"></div>
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 bg-gray-300 rounded-full text-gray-600 font-semibold" id="step4">4</div>
                                <span class="ml-2 font-medium text-gray-500">Permissions</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Basic Information -->
                    <div id="step1Content" class="space-y-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <div class="bg-blue-100 rounded-lg p-3 mr-3">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                </div>
                                Basic User Information
                            </h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <div>
                                    <label for="name" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-user mr-2 text-indigo-500"></i>
                                        Full Name <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="name" name="name" required
                                               value="{{ old('name') }}"
                                               class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-lg"
                                               placeholder="Enter full name">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                    </div>
                                    @error('name')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-envelope mr-2 text-indigo-500"></i>
                                        Email Address <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="email" id="email" name="email" required
                                               value="{{ old('email') }}"
                                               class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-lg"
                                               placeholder="user@company.com">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                            <i class="fas fa-envelope text-gray-400"></i>
                                        </div>
                                    </div>
                                    @error('email')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="employee_id" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-id-badge mr-2 text-indigo-500"></i>
                                        Employee ID
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="employee_id" name="employee_id"
                                               value="{{ old('employee_id') }}"
                                               pattern="[0-9]{6,8}"
                                               class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-lg"
                                               placeholder="6-8 digit number (optional)">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                            <i class="fas fa-id-badge text-gray-400"></i>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">Leave empty to auto-generate</p>
                                    @error('employee_id')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-phone mr-2 text-indigo-500"></i>
                                        Phone Number
                                    </label>
                                    <div class="relative">
                                        <input type="tel" id="phone" name="phone"
                                               value="{{ old('phone') }}"
                                               class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-lg"
                                               placeholder="+1 (555) 123-4567">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                            <i class="fas fa-phone text-gray-400"></i>
                                        </div>
                                    </div>
                                    @error('phone')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-lock mr-2 text-indigo-500"></i>
                                        Password <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="password" name="password" required
                                               class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-lg"
                                               placeholder="•••••••••">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                    </div>
                                    @error('password')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-lock mr-2 text-indigo-500"></i>
                                        Confirm Password <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="password_confirmation" name="password_confirmation" required
                                               class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-lg"
                                               placeholder="•••••••••">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                    </div>
                                    @error('password_confirmation')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex justify-end">
                            <button type="button" onclick="goToStep(2)"
                                    class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                                Next Step <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Professional Information -->
                    <div id="step2Content" class="space-y-8 hidden">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <div class="bg-green-100 rounded-lg p-3 mr-3">
                                    <i class="fas fa-briefcase text-green-600"></i>
                                </div>
                                Professional Information
                            </h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <div>
                                    <label for="department" class="block text-sm font-bold text-gray-700 mb-3">
                                        Department
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="department" name="department"
                                               value="{{ old('department') }}"
                                               class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-lg"
                                               placeholder="Engineering, Marketing, etc.">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                            <i class="fas fa-building text-gray-400"></i>
                                        </div>
                                    </div>
                                    @error('department')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="position" class="block text-sm font-bold text-gray-700 mb-3">
                                        Position
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="position" name="position"
                                               value="{{ old('position') }}"
                                               class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-lg"
                                               placeholder="Senior Developer, Manager, etc.">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                            <i class="fas fa-user-tag text-gray-400"></i>
                                        </div>
                                    </div>
                                    @error('position')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="hire_date" class="block text-sm font-bold text-gray-700 mb-3">
                                        Hire Date
                                    </label>
                                    <div class="relative">
                                        <input type="date" id="hire_date" name="hire_date"
                                               value="{{ old('hire_date') }}"
                                               class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-lg">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                            <i class="fas fa-calendar text-gray-400"></i>
                                        </div>
                                    </div>
                                    @error('hire_date')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="salary" class="block text-sm font-bold text-gray-700 mb-3">
                                        Salary
                                    </label>
                                    <div class="relative">
                                        <input type="number" id="salary" name="salary" step="0.01" min="0"
                                               value="{{ old('salary') }}"
                                               class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-lg"
                                               placeholder="0.00">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                            <span class="text-gray-500">$</span>
                                        </div>
                                    </div>
                                    @error('salary')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="lg:col-span-2">
                                    <label for="address" class="block text-sm font-bold text-gray-700 mb-3">
                                        Address
                                    </label>
                                    <div class="relative">
                                        <textarea id="address" name="address" rows="3"
                                                  class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-lg resize-none"
                                                  placeholder="Enter full address">{{ old('address') }}</textarea>
                                        <div class="absolute top-3 right-3 pointer-events-none">
                                            <i class="fas fa-map-marker-alt text-gray-400"></i>
                                        </div>
                                    </div>
                                    @error('address')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex justify-between">
                            <button type="button" onclick="goToStep(1)"
                                    class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors duration-200">
                                <i class="fas fa-arrow-left mr-2"></i> Previous
                            </button>
                            <button type="button" onclick="goToStep(3)"
                                    class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                                Next Step <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Team and Tech Stack Assignments -->
                    <div id="step3Content" class="space-y-8 hidden">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <div class="bg-purple-100 rounded-lg p-3 mr-3">
                                    <i class="fas fa-users text-purple-600"></i>
                                </div>
                                Team and Tech Stack Assignments
                            </h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <div>
                                    <label for="team_id" class="block text-sm font-bold text-gray-700 mb-3">
                                        Assign to Team
                                    </label>
                                    <div class="relative">
                                        <select id="team_id" name="team_id"
                                                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm appearance-none transition-all duration-200 text-lg">
                                            <option value="">Select a team (optional)</option>
                                            @foreach($teams as $team)
                                                <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>
                                                    {{ $team->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                    @error('team_id')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="tech_stack_id" class="block text-sm font-bold text-gray-700 mb-3">
                                        Assign to Tech Stack
                                    </label>
                                    <div class="relative">
                                        <select id="tech_stack_id" name="tech_stack_id"
                                                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm appearance-none transition-all duration-200 text-lg">
                                            <option value="">Select a tech stack (optional)</option>
                                            @foreach($techStacks as $techStack)
                                                <option value="{{ $techStack->id }}" {{ old('tech_stack_id') == $techStack->id ? 'selected' : '' }}>
                                                    {{ $techStack->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                    @error('tech_stack_id')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex justify-between">
                            <button type="button" onclick="goToStep(2)"
                                    class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors duration-200">
                                <i class="fas fa-arrow-left mr-2"></i> Previous
                            </button>
                            <button type="button" onclick="goToStep(4)"
                                    class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                                Next Step <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Role and Permissions -->
                    <div id="step4Content" class="space-y-8 hidden">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <div class="bg-orange-100 rounded-lg p-3 mr-3">
                                    <i class="fas fa-shield-alt text-orange-600"></i>
                                </div>
                                Role and Permissions
                            </h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <div>
                                    <label for="role" class="block text-sm font-bold text-gray-700 mb-3">
                                        Assign Primary Role
                                    </label>
                                    <div class="relative">
                                        <select id="role" name="role"
                                                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm appearance-none transition-all duration-200 text-lg">
                                            <option value="">Select a role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                                    {{ ucfirst($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                    @error('role')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" id="is_active" name="is_active" value="1"
                                               {{ old('is_active', '1') ? 'checked' : '' }}
                                               class="mr-3 w-5 h-5 text-indigo-600 bg-indigo-100 border-indigo-300 rounded focus:ring-indigo-500 focus:ring-2">
                                        <span class="text-sm font-bold text-gray-700">Active User</span>
                                    </label>
                                    <p class="mt-2 text-sm text-gray-500 ml-8">Uncheck to create inactive user</p>
                                </div>
                            </div>

                            <!-- Additional Permissions -->
                            <div class="mt-8">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Additional Permissions</h4>
                                <div class="space-y-4">
                                    @foreach($permissions as $group => $groupPermissions)
                                        <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 border border-orange-200">
                                            <h5 class="font-semibold text-gray-900 mb-3 text-capitalize">{{ $group }} Permissions</h5>
                                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                                @foreach($groupPermissions as $permission)
                                                    <label class="flex items-center p-3 hover:bg-white rounded-lg cursor-pointer transition-colors duration-200">
                                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                                               class="mr-3 w-4 h-4 text-orange-600 bg-orange-100 border-orange-300 rounded focus:ring-orange-500 focus:ring-2">
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('-', ' ', $permission->name)) }}</div>
                                                            <div class="text-xs text-gray-500">{{ $permission->name }}</div>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex flex-col sm:flex-row justify-between gap-4">
                            <button type="button" onclick="goToStep(3)"
                                    class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors duration-200">
                                <i class="fas fa-arrow-left mr-2"></i> Previous
                            </button>
                            <div class="flex gap-4">
                                <a href="{{ route('users.index') }}"
                                   class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors duration-200">
                                    <i class="fas fa-times mr-2"></i> Cancel
                                </a>
                                <button type="submit"
                                        class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                                    <i class="fas fa-check mr-2"></i> Create User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast Notifications Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>
</div>

@push('styles')
<style>
    /* Custom scrollbar for permissions */
    .max-h-80::-webkit-scrollbar {
        width: 6px;
    }
    .max-h-80::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .max-h-80::-webkit-scrollbar-thumb {
        background: #f97316;
        border-radius: 10px;
    }
    .max-h-80::-webkit-scrollbar-thumb:hover {
        background: #ea580c;
    }

    /* Toast styles */
    .toast {
        transform: translateX(400px);
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        min-width: 320px;
        max-width: 480px;
    }

    .toast.show {
        transform: translateX(0);
    }

    .toast-success {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        border-left: 4px solid #047857;
    }

    .toast-error {
        background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
        border-left: 4px solid #B91C1C;
    }

    .toast-warning {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        border-left: 4px solid #B45309;
    }

    .toast-progress {
        animation: toast-progress 5s linear;
    }

    @keyframes toast-progress {
        from { width: 100%; }
        to { width: 0%; }
    }

    @keyframes slide-in-right {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slide-out-right {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    .toast-enter {
        animation: slide-in-right 0.3s ease-out;
    }

    .toast-exit {
        animation: slide-out-right 0.3s ease-in;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Create user page loaded');
});

function goToStep(stepNumber) {
    // Hide all steps
    for (let i = 1; i <= 4; i++) {
        document.getElementById('step' + i + 'Content').classList.add('hidden');
        document.getElementById('step' + i).classList.remove('bg-indigo-600', 'text-white');
        document.getElementById('step' + i).classList.add('bg-gray-300', 'text-gray-600');
        document.getElementById('progress' + i).classList.remove('bg-indigo-600');
        document.getElementById('progress' + i).classList.add('bg-gray-300');
    }

    // Show current step
    document.getElementById('step' + stepNumber + 'Content').classList.remove('hidden');
    document.getElementById('step' + stepNumber).classList.remove('bg-gray-300', 'text-gray-600');
    document.getElementById('step' + stepNumber).classList.add('bg-indigo-600', 'text-white');

    // Update progress bar
    for (let i = 1; i < stepNumber; i++) {
        document.getElementById('progress' + i).classList.remove('bg-gray-300');
        document.getElementById('progress' + i).classList.add('bg-indigo-600');
    }

    // Scroll to top of form
    document.getElementById('createUserForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// Form validation and submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createUserForm');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Client-side validation
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;
            const employeeId = document.getElementById('employee_id').value.trim();

            // Validation checks
            if (!name) {
                showToast('Please enter a name', 'error');
                goToStep(1);
                document.getElementById('name').focus();
                return;
            }

            if (name.length < 2) {
                showToast('Name must be at least 2 characters long', 'error');
                goToStep(1);
                document.getElementById('name').focus();
                return;
            }

            if (!email) {
                showToast('Please enter an email address', 'error');
                goToStep(1);
                document.getElementById('email').focus();
                return;
            }

            if (!validateEmail(email)) {
                showToast('Please enter a valid email address', 'error');
                goToStep(1);
                document.getElementById('email').focus();
                return;
            }

            if (!password) {
                showToast('Please enter a password', 'error');
                goToStep(1);
                document.getElementById('password').focus();
                return;
            }

            if (password.length < 8) {
                showToast('Password must be at least 8 characters long', 'error');
                goToStep(1);
                document.getElementById('password').focus();
                return;
            }

            if (password !== passwordConfirm) {
                showToast('Password confirmation does not match', 'error');
                goToStep(1);
                document.getElementById('password_confirmation').focus();
                return;
            }

            if (employeeId && !/^[0-9]{6,8}$/.test(employeeId)) {
                showToast('Employee ID must be a 6-8 digit number', 'error');
                goToStep(1);
                document.getElementById('employee_id').focus();
                return;
            }

            // If validation passes, submit form
            submitUserForm();
        });
    }
});

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function submitUserForm() {
    const form = document.getElementById('createUserForm');
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');

    // Show loading state
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating User...';
    submitButton.disabled = true;

    // Submit via AJAX for better UX
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('User created successfully!', 'success');
            setTimeout(() => {
                window.location.href = '/users';
            }, 1500);
        } else {
            showToast(data.message || 'Failed to create user', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Network error. Please check your connection and try again.', 'error');
    })
    .finally(() => {
        // Restore button state
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}

// Toast notification functions
function showToast(message, type = 'info', duration = 5000) {
    const toastContainer = document.getElementById('toastContainer');

    const toast = document.createElement('div');
    toast.className = `toast ${type} rounded-lg shadow-2xl p-4 text-white flex items-center space-x-3 toast-enter`;

    let icon = '';
    switch(type) {
        case 'success':
            icon = '<i class="fas fa-check-circle text-xl"></i>';
            break;
        case 'error':
            icon = '<i class="fas fa-exclamation-circle text-xl"></i>';
            break;
        case 'warning':
            icon = '<i class="fas fa-exclamation-triangle text-xl"></i>';
            break;
        default:
            icon = '<i class="fas fa-info-circle text-xl"></i>';
    }

    toast.innerHTML = `
        <div class="flex items-center space-x-3 flex-1">
            <div class="flex-shrink-0">
                ${icon}
            </div>
            <div class="flex-1">
                <p class="font-semibold text-sm">${message}</p>
            </div>
            <div class="flex-shrink-0">
                <button onclick="closeToast(this)" class="text-white hover:text-gray-200 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 h-1 bg-white/20 rounded-b-lg toast-progress" style="width: 100%;"></div>
    `;

    toast.style.position = 'relative';
    toast.style.overflow = 'hidden';

    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.classList.remove('toast-enter');
        toast.classList.add('show');
    }, 10);

    if (duration > 0) {
        setTimeout(() => {
            closeToast(toast.querySelector('button'));
        }, duration);
    }
}

function closeToast(button) {
    const toast = button.closest('.toast');
    if (toast) {
        toast.classList.remove('show');
        toast.classList.add('toast-exit');

        setTimeout(() => {
            toast.remove();
        }, 300);
    }
}
</script>
@endpush
@endsection