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
        <div class="mt-6">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-2">Create New User</h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Add a new user to the system with appropriate roles and permissions</p>
        </div>
    </div>

    <!-- Create User Form -->
    <div class="max-w-6xl mx-auto">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 p-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl mb-4">
                    <i class="fas fa-user-plus text-3xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">User Information</h2>
                <p class="text-purple-100">Fill in the user details and assign appropriate permissions</p>
            </div>

            <form method="POST" action="{{ route('users.store') }}" id="createUserForm" novalidate>
                @csrf
                <div class="p-8">
                    <!-- Progress Steps -->
                    <div class="mb-8">
                        <div class="flex items-center justify-center space-x-4">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 bg-indigo-600 rounded-full text-white font-semibold" id="step1">1</div>
                                <span class="ml-2 font-medium text-gray-900 dark:text-gray-100">Basic Info</span>
                            </div>
                            <div class="w-12 h-1 bg-gray-300 dark:bg-gray-700 rounded" id="progress1"></div>
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 bg-gray-300 dark:bg-gray-700 rounded-full text-gray-600 dark:text-gray-300 font-semibold" id="step2">2</div>
                                <span class="ml-2 font-medium text-gray-500 dark:text-gray-400">Professional</span>
                            </div>
                            <div class="w-12 h-1 bg-gray-300 dark:bg-gray-700 rounded" id="progress2"></div>
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 bg-gray-300 dark:bg-gray-700 rounded-full text-gray-600 dark:text-gray-300 font-semibold" id="step3">3</div>
                                <span class="ml-2 font-medium text-gray-500 dark:text-gray-400">Assignments</span>
                            </div>
                            <div class="w-12 h-1 bg-gray-300 dark:bg-gray-700 rounded" id="progress3"></div>
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 bg-gray-300 dark:bg-gray-700 rounded-full text-gray-600 dark:text-gray-300 font-semibold" id="step4">4</div>
                                <span class="ml-2 font-medium text-gray-500 dark:text-gray-400">Permissions</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 1: Basic Information -->
                    <div id="step1Content" class="space-y-8">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
                                <div class="bg-blue-100 dark:bg-blue-500/10 rounded-lg p-3 mr-3">
                                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                                </div>
                                Basic User Information
                            </h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <i class="fas fa-user mr-2 text-indigo-500 dark:text-indigo-400 text-sm"></i>
                                        Full Name <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="name" name="name" required
                                               value="{{ old('name') }}"
                                               class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base"
                                               placeholder="Enter full name">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-user text-gray-400 dark:text-gray-500 text-sm"></i>
                                        </div>
                                    </div>
                                    @error('name')
                                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <i class="fas fa-envelope mr-2 text-indigo-500 dark:text-indigo-400 text-sm"></i>
                                        Email Address <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="email" id="email" name="email" required
                                               value="{{ old('email') }}"
                                               class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base"
                                               placeholder="user@company.com">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-envelope text-gray-400 dark:text-gray-500 text-sm"></i>
                                        </div>
                                    </div>
                                    @error('email')
                                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="employee_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <i class="fas fa-id-badge mr-2 text-indigo-500 dark:text-indigo-400 text-sm"></i>
                                        Employee ID
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="employee_id" name="employee_id"
                                               value="{{ old('employee_id') }}"
                                               maxlength="13"
                                               class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base"
                                               placeholder="13 characters (optional)">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-id-badge text-gray-400 dark:text-gray-500 text-sm"></i>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Letters and numbers, leave empty to auto-generate</p>
                                    @error('employee_id')
                                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <i class="fas fa-phone mr-2 text-indigo-500 dark:text-indigo-400 text-sm"></i>
                                        Phone Number
                                    </label>
                                    <div class="relative">
                                        <input type="tel" id="phone" name="phone"
                                               value="{{ old('phone') }}"
                                               class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base"
                                               placeholder="+1 (555) 123-4567">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-phone text-gray-400 dark:text-gray-500 text-sm"></i>
                                        </div>
                                    </div>
                                    @error('phone')
                                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <i class="fas fa-lock mr-2 text-indigo-500 dark:text-indigo-400 text-sm"></i>
                                        Password <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="password" name="password" required
                                               class="w-full px-4 py-2.5 pr-12 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base"
                                               placeholder="•••••••••">
                                        <button type="button"
                                                onclick="togglePasswordVisibility('password')"
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:text-indigo-600 dark:focus:text-indigo-400 transition-colors duration-200"
                                                aria-label="Toggle password visibility">
                                            <i id="password-toggle-icon" class="fas fa-eye text-sm"></i>
                                        </button>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Must contain at least 8 characters with uppercase, lowercase, and numbers</p>
                                    @error('password')
                                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <i class="fas fa-lock mr-2 text-indigo-500 dark:text-indigo-400 text-sm"></i>
                                        Confirm Password <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="password_confirmation" name="password_confirmation" required
                                               class="w-full px-4 py-2.5 pr-12 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base"
                                               placeholder="•••••••••">
                                        <button type="button"
                                                onclick="togglePasswordVisibility('password_confirmation')"
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:text-indigo-600 dark:focus:text-indigo-400 transition-colors duration-200"
                                                aria-label="Toggle password confirmation visibility">
                                            <i id="password_confirmation-toggle-icon" class="fas fa-eye text-sm"></i>
                                        </button>
                                    </div>
                                    @error('password_confirmation')
                                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex justify-end">
                            <button type="button" onclick="goToStep(2)"
                                    class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105 text-sm">
                                Next Step <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Professional Information -->
                    <div id="step2Content" class="space-y-8 hidden">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
                                <div class="bg-green-100 dark:bg-green-500/10 rounded-lg p-3 mr-3">
                                    <i class="fas fa-briefcase text-green-600 dark:text-green-400"></i>
                                </div>
                                Professional Information
                            </h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="department" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <i class="fas fa-building mr-2 text-indigo-500 dark:text-indigo-400 text-sm"></i>
                                        Department
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="department" name="department"
                                               value="{{ old('department') }}"
                                               class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base"
                                               placeholder="Engineering, Marketing, etc.">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-building text-gray-400 dark:text-gray-500 text-sm"></i>
                                        </div>
                                    </div>
                                    @error('department')
                                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="position" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <i class="fas fa-user-tag mr-2 text-indigo-500 dark:text-indigo-400 text-sm"></i>
                                        Position
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="position" name="position"
                                               value="{{ old('position') }}"
                                               class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base"
                                               placeholder="Senior Developer, Manager, etc.">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-user-tag text-gray-400 dark:text-gray-500 text-sm"></i>
                                        </div>
                                    </div>
                                    @error('position')
                                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="lg:col-span-2">
                                    <label for="address" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <i class="fas fa-map-marker-alt mr-2 text-indigo-500 dark:text-indigo-400 text-sm"></i>
                                        Address
                                    </label>
                                    <div class="relative">
                                        <textarea id="address" name="address" rows="3"
                                                  class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base resize-none"
                                                  placeholder="Enter full address">{{ old('address') }}</textarea>
                                        <div class="absolute top-2.5 right-2.5 pointer-events-none">
                                            <i class="fas fa-map-marker-alt text-gray-400 dark:text-gray-500 text-sm"></i>
                                        </div>
                                    </div>
                                    @error('address')
                                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex justify-between">
                            <button type="button" onclick="goToStep(1)"
                                    class="px-5 py-2 border-2 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 text-sm">
                                <i class="fas fa-arrow-left mr-1.5 text-xs"></i> Previous
                            </button>
                            <button type="button" onclick="goToStep(3)"
                                    class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105 text-sm">
                                Next Step <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Team and Tech Stack Assignments -->
                    <div id="step3Content" class="space-y-8 hidden">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
                                <div class="bg-purple-100 dark:bg-purple-500/10 rounded-lg p-3 mr-3">
                                    <i class="fas fa-users text-purple-600 dark:text-purple-400"></i>
                                </div>
                                Assignment
                            </h3>

                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Which side of the business is this person on?</p>
                            <div class="track-choice mb-6">
                                <label class="track-option">
                                    <input type="radio" name="track" value="operation" {{ old('track', 'operation') === 'operation' ? 'checked' : '' }}>
                                    <span class="track-card">
                                        <span class="track-icon"><i class="fas fa-screwdriver-wrench"></i></span>
                                        <span>
                                            <span class="track-name">Operations</span>
                                            <span class="track-hint">Delivers the work — gets a team and a tech stack</span>
                                        </span>
                                    </span>
                                </label>
                                <label class="track-option">
                                    <input type="radio" name="track" value="sales" {{ old('track') === 'sales' ? 'checked' : '' }}>
                                    <span class="track-card">
                                        <span class="track-icon"><i class="fas fa-handshake"></i></span>
                                        <span>
                                            <span class="track-name">Sales</span>
                                            <span class="track-hint">Brings the work in — gets a sales leader and a target</span>
                                        </span>
                                    </span>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" data-track-panel="operation">
                                <div>
                                    <label for="team_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <i class="fas fa-users mr-2 text-indigo-500 dark:text-indigo-400 text-sm"></i>
                                        Assign to Team
                                    </label>
                                    <x-ajax-select name="team_id"
                                                   :url="route('search.teams')"
                                                   placeholder="Search teams..." />
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Assign user to a team for better collaboration</p>
                                    @error('team_id')
                                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="tech_stack_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <i class="fas fa-code mr-2 text-indigo-500 dark:text-indigo-400 text-sm"></i>
                                        Assign to Tech Stack
                                    </label>
                                    <x-ajax-select name="tech_stack_id"
                                                   :url="route('search.tech-stacks')"
                                                   placeholder="Search tech stacks..." />
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Define the user's primary technology stack</p>
                                    @error('tech_stack_id')
                                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 hidden" data-track-panel="sales">
                                <div>
                                    <label for="sales_leader_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <i class="fas fa-user-tie mr-2 text-indigo-500 dark:text-indigo-400 text-sm"></i>
                                        Sales Leader
                                    </label>
                                    <select name="sales_leader_id" id="sales_leader_id"
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
                                        <option value="">No sales leader</option>
                                        @foreach(\App\Models\User::withRoleName('sales-leader')->orderBy('name')->get() as $leader)
                                            <option value="{{ $leader->id }}" {{ (int) old('sales_leader_id') === $leader->id ? 'selected' : '' }}>
                                                {{ $leader->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Only used for sales members — the leader sees every order their members sell.</p>
                                    @error('sales_leader_id')
                                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-6 max-w-md" data-target-field>
                                <label for="sales_target" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                    <i class="fas fa-bullseye mr-2 text-indigo-500 dark:text-indigo-400 text-sm"></i>
                                    Monthly Minimum Target
                                </label>
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                        <input type="number" step="0.01" min="0" name="sales_target" id="sales_target"
                                               value="{{ old('sales_target') }}"
                                               class="w-full pl-8 pr-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm"
                                               placeholder="0.00">
                                    </div>
                                    <input type="month" name="sales_target_month"
                                           value="{{ old('sales_target_month', now()->format('Y-m')) }}"
                                           class="w-40 px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400" data-target-hint>
                                    Optional. The minimum this person should sell or be credited from that month on — it carries forward until changed.
                                </p>
                                @error('sales_target')
                                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center font-medium">
                                        <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="flex justify-between">
                            <button type="button" onclick="goToStep(2)"
                                    class="px-5 py-2 border-2 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 text-sm">
                                <i class="fas fa-arrow-left mr-1.5 text-xs"></i> Previous
                            </button>
                            <button type="button" onclick="goToStep(4)"
                                    class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105 text-sm">
                                Next Step <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Role and Permissions -->
                    <div id="step4Content" class="space-y-8 hidden">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
                                <div class="bg-orange-100 dark:bg-orange-500/10 rounded-lg p-3 mr-3">
                                    <i class="fas fa-shield-alt text-orange-600 dark:text-orange-400"></i>
                                </div>
                                Role and Permissions
                            </h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="role" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                                        <i class="fas fa-user-shield mr-2 text-indigo-500 dark:text-indigo-400 text-sm"></i>
                                        Assign Primary Role
                                    </label>
                                    <div class="relative">
                                        <select id="role" name="role"
                                                class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm appearance-none transition-all duration-200 text-base">
                                            <option value="">Select a role</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                                    {{ ucfirst($role->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400 dark:text-gray-500 text-sm"></i>
                                        </div>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Primary role determines basic user permissions</p>
                                    @error('role')
                                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Managed Teams Section (only for managers) -->
                                <div id="managedTeamsSection" class="hidden">
                                    <div class="bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-900 rounded-lg p-4">
                                        <div class="flex items-start mb-3">
                                            <i class="fas fa-users-cog text-indigo-600 dark:text-indigo-400 mt-1 mr-2"></i>
                                            <div class="flex-1">
                                                <h4 class="text-sm font-semibold text-indigo-900 dark:text-indigo-300 mb-1">Managed Teams</h4>
                                                <p class="text-xs text-indigo-700 dark:text-indigo-400">Select which teams this manager can oversee. Managers will only have access to teams they're assigned to.</p>
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            @foreach($teams as $team)
                                                <label class="flex items-center p-2 rounded hover:bg-white dark:hover:bg-gray-800 transition-colors cursor-pointer">
                                                    <input type="checkbox"
                                                           name="managed_teams[]"
                                                           value="{{ $team->id }}"
                                                           {{ in_array($team->id, old('managed_teams', [])) ? 'checked' : '' }}
                                                           class="w-4 h-4 text-indigo-600 bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-indigo-500">
                                                    <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">{{ $team->name }}</span>
                                                    @if($team->description)
                                                        <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $team->description }})</span>
                                                    @endif
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Status Section -->
                            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <i class="fas fa-toggle-on text-2xl text-green-600 dark:text-green-400 mr-3" id="activeStatusIcon"></i>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Account Status</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400" id="activeStatusText">User can login and access the system</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="is_active" name="is_active" value="1"
                                               {{ old('is_active', '1') ? 'checked' : '' }}
                                               class="sr-only peer"
                                               onchange="toggleActiveStatus()">
                                        <div class="w-14 h-7 bg-gray-300 dark:bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:transition-all peer-checked:bg-green-600"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300" id="activeStatusLabel">Active</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Additional Permissions -->
                            <div class="mt-6">
                                <div class="flex items-center justify-between mb-4">
                                    <button type="button"
                                            class="flex items-center text-base font-semibold text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                                            onclick="toggleAdditionalPermissions()">
                                        <i class="fas fa-key mr-2 text-indigo-500 dark:text-indigo-400 text-sm"></i>
                                        Additional Permissions
                                        <i class="fas fa-chevron-down ml-2 text-sm transition-transform duration-200" id="additionalPermissionsIcon"></i>
                                    </button>
                                    <button type="button"
                                            class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors"
                                            onclick="toggleAllAccordions()">
                                        <i class="fas fa-expand-alt mr-1"></i>
                                        <span id="toggleAllText">Expand All</span>
                                    </button>
                                </div>
                                <div class="space-y-2 hidden" id="permissionsAccordion">
                                    @foreach($permissions as $group => $groupPermissions)
                                        <div class="permissions-accordion-item border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden transition-all duration-200">
                                            <button type="button"
                                                    class="accordion-header w-full px-4 py-3 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-800 hover:from-indigo-50 hover:to-purple-50 dark:hover:from-indigo-500/10 dark:hover:to-purple-500/10 flex items-center justify-between text-left transition-all duration-200"
                                                    onclick="toggleAccordion(this)">
                                                <div class="flex items-center">
                                                    <div class="flex items-center justify-center w-6 h-6 bg-indigo-500 text-white rounded mr-3">
                                                        <i class="fas fa-chevron-down text-xs transition-transform duration-200 accordion-icon"></i>
                                                    </div>
                                                    <div class="text-left">
                                                        <h5 class="text-sm font-semibold text-gray-900 dark:text-gray-100 capitalize">{{ $group }}</h5>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ count($groupPermissions) }} permissions</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    @php
                                                        $checkedCount = collect(old('permissions', []))->filter(function($perm) use ($groupPermissions) {
                                                            return collect($groupPermissions)->pluck('id')->contains($perm);
                                                        })->count();
                                                    @endphp
                                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 px-2 py-1 rounded">
                                                        {{ $checkedCount }}/{{ count($groupPermissions) }}
                                                    </span>
                                                    <i class="fas fa-shield-alt text-indigo-400 dark:text-indigo-400 text-sm"></i>
                                                </div>
                                            </button>
                                            <div class="accordion-content hidden">
                                                <div class="px-4 py-3 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                                        @foreach($groupPermissions as $permission)
                                                            <label class="flex items-center p-2 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 rounded cursor-pointer transition-colors duration-200 border border-transparent hover:border-indigo-200 dark:hover:border-indigo-800 group">
                                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                                       {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                                                       class="mr-2 w-3.5 h-3.5 text-indigo-600 bg-indigo-100 dark:bg-gray-800 border-indigo-300 dark:border-gray-600 rounded focus:ring-indigo-500 focus:ring-2">
                                                                <div class="flex-1 min-w-0">
                                                                    <div class="text-xs font-medium text-gray-900 dark:text-gray-100 truncate">{{ ucfirst(str_replace('-', ' ', $permission->name)) }}</div>
                                                                    <div class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $permission->name }}</div>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                    @if(count($groupPermissions) > 4)
                                                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                                                            <button type="button"
                                                                    class="text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors"
                                                                    onclick="toggleAllPermissions(this, '{{ $group }}')">
                                                                <i class="fas fa-check-square mr-1"></i>
                                                                <span class="toggle-all-text">Select All {{ ucfirst($group) }}</span>
                                                            </button>
                                                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                                                {{ round(collect(old('permissions', []))->filter(function($perm) use ($groupPermissions) {
                                                                    return collect($groupPermissions)->pluck('id')->contains($perm);
                                                                })->count() / count($groupPermissions) * 100) }}% selected
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-500/10 rounded-lg border border-blue-200 dark:border-blue-900">
                                    <p class="text-xs text-blue-700 dark:text-blue-400 flex items-center">
                                        <i class="fas fa-info-circle mr-2 text-blue-500 dark:text-blue-400"></i>
                                        Default permissions are automatically assigned based on the selected role. Additional permissions are optional extras beyond the role defaults.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex flex-col sm:flex-row justify-between gap-3">
                            <button type="button" onclick="goToStep(3)"
                                    class="px-5 py-2 border-2 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 text-sm">
                                <i class="fas fa-arrow-left mr-1.5 text-xs"></i> Previous
                            </button>
                            <div class="flex gap-3">
                                <a href="{{ route('users.index') }}"
                                   class="px-5 py-2 border-2 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 text-sm">
                                    <i class="fas fa-times mr-1.5 text-xs"></i> Cancel
                                </a>
                                <button type="submit"
                                        class="px-6 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105 text-sm">
                                    <i class="fas fa-check mr-1.5 text-xs"></i> Create User
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
@push('styles')
<style>
    .track-choice {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 0.75rem;
    }

    @media (min-width: 640px) {
        .track-choice { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    .track-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .track-card {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.875rem;
        cursor: pointer;
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }

    .dark .track-card { border-color: #374151; }

    .track-card:hover { border-color: #a5b4fc; }

    .track-option input:checked + .track-card {
        border-color: #4f46e5;
        background-color: #eef2ff;
    }

    .dark .track-option input:checked + .track-card {
        background-color: rgba(99, 102, 241, 0.1);
    }

    .track-option input:focus-visible + .track-card {
        outline: 2px solid #6366f1;
        outline-offset: 2px;
    }

    .track-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
        background-color: #f3f4f6;
        color: #4f46e5;
        flex-shrink: 0;
    }

    .dark .track-icon { background-color: #1f2937; color: #a5b4fc; }

    .track-option input:checked + .track-card .track-icon {
        background-image: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: #ffffff;
    }

    .track-name {
        display: block;
        font-size: 0.9375rem;
        font-weight: 700;
        color: #111827;
    }

    .dark .track-name { color: #f3f4f6; }

    .track-hint {
        display: block;
        margin-top: 0.125rem;
        font-size: 0.75rem;
        color: #6b7280;
    }

    .dark .track-hint { color: #9ca3af; }
</style>
@endpush

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
        background: #ffffff;
        border-left: 4px solid #6366F1;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(10px);
    }

    .toast.show {
        transform: translateX(0);
    }

    .toast-success {
        background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
        border-left: 4px solid #10B981;
    }

    .toast-success .toast-icon {
        color: #10B981;
        background: #ecfdf5;
    }

    .toast-error {
        background: linear-gradient(135deg, #ffffff 0%, #fef2f2 100%);
        border-left: 4px solid #EF4444;
    }

    .toast-error .toast-icon {
        color: #EF4444;
        background: #fef2f2;
    }

    .toast-warning {
        background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%);
        border-left: 4px solid #F59E0B;
    }

    .toast-warning .toast-icon {
        color: #F59E0B;
        background: #fffbeb;
    }

    .toast-info {
        background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%);
        border-left: 4px solid #3B82F6;
    }

    .toast-info .toast-icon {
        color: #3B82F6;
        background: #eff6ff;
    }

    .toast-progress {
        height: 3px;
        background: #6366F1;
        animation: toast-progress 5s linear;
        border-radius: 0 0 0 4px;
    }

    .toast-success .toast-progress {
        background: #10B981;
    }

    .toast-error .toast-progress {
        background: #EF4444;
    }

    .toast-warning .toast-progress {
        background: #F59E0B;
    }

    .toast-info .toast-progress {
        background: #3B82F6;
    }

    @keyframes toast-progress {
        from { width: 100%; opacity: 0.8; }
        to { width: 0%; opacity: 0.3; }
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

    /* Toast icon styling */
    .toast-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .toast-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .toast-message {
        color: #6b7280;
        font-size: 14px;
    }

    .toast-close {
        color: #9ca3af;
        background: transparent;
        border: none;
        font-size: 18px;
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .toast-close:hover {
        color: #6b7280;
        background: #f3f4f6;
    }

    /* Accordion Styles */
    .permissions-accordion-item {
        transition: all 0.2s ease;
    }

    .permissions-accordion-item:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .accordion-header {
        transition: all 0.2s ease;
    }

    .accordion-content {
        transition: all 0.3s ease;
    }

    .rotate-180 {
        transform: rotate(180deg);
    }

    /* Permission checkbox hover effect */
    .permissions-accordion-item label:hover input[type="checkbox"] {
        border-color: #4f46e5;
    }

    .permissions-accordion-item label:has(input:checked) {
        background-color: #eef2ff;
        border-color: #4f46e5;
    }

    .dark .permissions-accordion-item label:has(input:checked) {
        background-color: rgba(99, 102, 241, 0.1);
        border-color: #6366f1;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tracks = document.querySelectorAll('input[name="track"]');
        const panels = document.querySelectorAll('[data-track-panel]');
        const roleSelect = document.getElementById('role');

        if (tracks.length === 0) {
            return;
        }

        function currentTrack() {
            return document.querySelector('input[name="track"]:checked')?.value ?? 'operation';
        }

        const targetHint = document.querySelector('[data-target-hint]');

        function applyTrack() {
            const track = currentTrack();

            if (targetHint) {
                targetHint.textContent = track === 'sales'
                    ? 'Optional. The minimum this person should sell from that month on — it carries forward until changed.'
                    : 'Optional. The minimum net value this person should be credited from that month on — it carries forward until changed.';
            }

            panels.forEach(panel => {
                const isActive = panel.dataset.trackPanel === track;
                panel.classList.toggle('hidden', !isActive);

                // Clear what is hidden, so a stray value can't be submitted.
                if (!isActive) {
                    panel.querySelectorAll('input:not([type="month"]), select').forEach(input => {
                        input.value = '';
                    });
                }
            });

            // Offer only the roles that belong to the chosen side.
            if (roleSelect) {
                let selectedStillValid = false;

                Array.from(roleSelect.options).forEach(option => {
                    if (option.value === '') {
                        return;
                    }

                    const isSalesRole = option.value.startsWith('sales-');
                    const belongs = track === 'sales' ? isSalesRole : !isSalesRole;

                    option.hidden = !belongs;
                    option.disabled = !belongs;

                    if (belongs && option.selected) {
                        selectedStillValid = true;
                    }
                });

                if (!selectedStillValid) {
                    roleSelect.value = '';
                }
            }
        }

        tracks.forEach(track => track.addEventListener('change', applyTrack));
        applyTrack();
    });
</script>
<script>
// Define default permissions for each role
const roleDefaultPermissions = {
    'admin': [
        // Admin gets most permissions by default
        'view users', 'create users', 'edit users', 'delete users',
        'view profiles', 'create profile', 'edit profile', 'delete profile',
        'view teams', 'create teams', 'edit teams', 'delete teams',
        'view orders', 'create orders', 'edit orders', 'delete orders',
        'view tasks', 'create tasks', 'edit tasks', 'delete tasks', 'assign tasks',
        'view bug reports', 'create bug reports', 'edit bug reports', 'delete bug reports',
        'view tech stacks', 'create tech stacks', 'edit tech stacks', 'delete tech stacks'
    ],
    'operation-manager': [
        // Operation manager permissions
        'view users', 'create users', 'edit users',
        'view profiles', 'create profile', 'edit profile',
        'view teams', 'create teams', 'edit teams',
        'view orders', 'create orders', 'edit orders',
        'view tasks', 'create tasks', 'edit tasks', 'assign tasks',
        'view bug reports', 'create bug reports', 'edit bug reports',
        'view tech stacks', 'create tech stacks', 'edit tech stacks'
    ],
    'project-manager': [
        // Project manager permissions
        'view users',
        'view profiles',
        'view teams',
        'view orders', 'create orders', 'edit orders',
        'view tasks', 'create tasks', 'edit tasks', 'assign tasks',
        'view bug reports',
        'view tech stacks'
    ],
    'team-lead': [
        // Team lead permissions
        'view users',
        'view profiles',
        'view teams',
        'view orders',
        'view tasks', 'create tasks', 'edit tasks',
        'view bug reports', 'create bug reports',
        'view tech stacks'
    ],
    'employee': [
        // Employee permissions
        'view tasks',
        'view bug reports', 'create bug reports',
        'view tech stacks'
    ]
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('Create user page loaded');

    // Handle role change to show/hide managed teams section and auto-assign permissions
    const roleSelect = document.getElementById('role');
    const managedTeamsSection = document.getElementById('managedTeamsSection');

    function toggleManagedTeamsSection() {
        const selectedRole = roleSelect.value;
        if (selectedRole === 'operation-manager' || selectedRole === 'project-manager') {
            managedTeamsSection.classList.remove('hidden');
        } else {
            managedTeamsSection.classList.add('hidden');
            // Uncheck all managed teams when not a manager role
            document.querySelectorAll('input[name="managed_teams[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    }

    function autoAssignPermissions() {
        const selectedRole = roleSelect.value;
        if (!selectedRole || !roleDefaultPermissions[selectedRole]) {
            return;
        }

        // Get all permission checkboxes
        const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');

        // Only proceed if permissions exist
        if (permissionCheckboxes.length === 0) {
            return;
        }

        // Uncheck all permissions first
        permissionCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });

        // Check permissions that match the role defaults
        permissionCheckboxes.forEach(checkbox => {
            const permissionLabel = checkbox.closest('label').querySelector('.text-gray-400')?.textContent || '';
            const permissionName = permissionLabel.toLowerCase().replace(/ /g, '-');

            // Check if this permission should be enabled for the role
            if (roleDefaultPermissions[selectedRole].some(perm => {
                const permLower = perm.toLowerCase().replace(/ /g, '-');
                return permissionName.includes(permLower) || permLower.includes(permissionName);
            })) {
                checkbox.checked = true;
            }
        });

        // Update counters
        updatePermissionCounters();
    }

    // Initial check
    toggleManagedTeamsSection();

    // Listen for role changes
    roleSelect.addEventListener('change', function() {
        toggleManagedTeamsSection();
        autoAssignPermissions();
    });

    // Test if all step elements exist
    for (let i = 1; i <= 4; i++) {
        const stepContent = document.getElementById('step' + i + 'Content');
        const stepIndicator = document.getElementById('step' + i);
        const progressElement = document.getElementById('progress' + i);

        console.log(`Step ${i} elements:`, {
            content: !!stepContent,
            indicator: !!stepIndicator,
            progress: !!progressElement
        });

        if (!stepContent) {
            console.error(`Step ${i} content not found!`);
        }
        if (!stepIndicator) {
            console.error(`Step ${i} indicator not found!`);
        }
        if (i < 4 && !progressElement) {
            console.error(`Progress ${i} not found!`);
        }
    }

    // Initialize first step and active status
    goToStep(1);
    toggleActiveStatus();

    // Enter inside any field only submits on the last step — otherwise it
    // would fire the browser's default "submit nearest form" behavior even
    // though the actual Submit button lives on a step the user hasn't seen.
    const wizardForm = document.getElementById('createUserForm');
    wizardForm?.addEventListener('keydown', function(event) {
        if (event.key !== 'Enter' || event.target.tagName === 'TEXTAREA') {
            return;
        }

        if (currentWizardStep < 4) {
            event.preventDefault();
        }
    });
});

// Toggle active status visual feedback
function toggleActiveStatus() {
    const isActive = document.getElementById('is_active').checked;
    const icon = document.getElementById('activeStatusIcon');
    const text = document.getElementById('activeStatusText');
    const label = document.getElementById('activeStatusLabel');

    if (isActive) {
        icon.className = 'fas fa-toggle-on text-2xl text-green-600 mr-3';
        text.textContent = 'User can login and access the system';
        label.textContent = 'Active';
    } else {
        icon.className = 'fas fa-toggle-off text-2xl text-gray-400 mr-3';
        text.textContent = 'User account is disabled and cannot login';
        label.textContent = 'Inactive';
    }
}

let currentWizardStep = 1;

function goToStep(stepNumber) {
    console.log('Going to step:', stepNumber);
    currentWizardStep = stepNumber;

    // Hide all steps and reset step indicators
    for (let i = 1; i <= 4; i++) {
        const stepContent = document.getElementById('step' + i + 'Content');
        const stepIndicator = document.getElementById('step' + i);
        const progressElement = document.getElementById('progress' + i);

        if (stepContent) {
            stepContent.classList.add('hidden');
        }

        if (stepIndicator) {
            stepIndicator.classList.remove('bg-indigo-600', 'text-white');
            stepIndicator.classList.add('bg-gray-300', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300');
        }

        if (progressElement) {
            progressElement.classList.remove('bg-indigo-600');
            progressElement.classList.add('bg-gray-300', 'dark:bg-gray-700');
        }
    }

    // Show current step and highlight it
    const currentStepContent = document.getElementById('step' + stepNumber + 'Content');
    const currentStepIndicator = document.getElementById('step' + stepNumber);

    if (currentStepContent) {
        currentStepContent.classList.remove('hidden');
        console.log('Step content shown:', stepNumber);
    } else {
        console.error('Step content not found:', stepNumber);
    }

    if (currentStepIndicator) {
        currentStepIndicator.classList.remove('bg-gray-300', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300');
        currentStepIndicator.classList.add('bg-indigo-600', 'text-white');
    }

    // Update progress bars for completed steps
    for (let i = 1; i < stepNumber; i++) {
        const progressElement = document.getElementById('progress' + i);
        if (progressElement) {
            progressElement.classList.remove('bg-gray-300', 'dark:bg-gray-700');
            progressElement.classList.add('bg-indigo-600');
        }
    }

    // Scroll to top of form
    const form = document.getElementById('createUserForm');
    if (form) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Form validation and submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createUserForm');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Basic client-side validation for required fields only
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;

            // Check required fields
            if (!name) {
                showToast('Name is required', 'error');
                goToStep(1);
                document.getElementById('name').focus();
                return;
            }

            if (!email) {
                showToast('Email address is required', 'error');
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
                showToast('Password is required', 'error');
                goToStep(1);
                document.getElementById('password').focus();
                return;
            }

            if (!passwordConfirm) {
                showToast('Password confirmation is required', 'error');
                goToStep(1);
                document.getElementById('password_confirmation').focus();
                return;
            }

            if (password !== passwordConfirm) {
                showToast('Password confirmation does not match', 'error');
                goToStep(1);
                document.getElementById('password_confirmation').focus();
                return;
            }

            // Submit form - server will handle all other validation
            submitUserForm();
        });
    }
});

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Toggle password visibility
function togglePasswordVisibility(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const toggleIcon = document.getElementById(fieldId + '-toggle-icon');

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Toggle additional permissions section
function toggleAdditionalPermissions() {
    const permissionsAccordion = document.getElementById('permissionsAccordion');
    const icon = document.getElementById('additionalPermissionsIcon');

    if (permissionsAccordion.classList.contains('hidden')) {
        permissionsAccordion.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        permissionsAccordion.classList.add('hidden');
        icon.classList.remove('rotate-180');

        // Close all individual accordions when closing the main section
        document.querySelectorAll('.accordion-content').forEach(item => {
            item.classList.add('hidden');
        });
        document.querySelectorAll('.accordion-icon').forEach(item => {
            item.classList.remove('rotate-180');
        });
    }
}

// Accordion functionality
function toggleAccordion(button) {
    const accordionItem = button.closest('.permissions-accordion-item');
    const content = accordionItem.querySelector('.accordion-content');
    const icon = button.querySelector('.accordion-icon');

    if (content.classList.contains('hidden')) {
        // Close all other accordions
        document.querySelectorAll('.accordion-content').forEach(item => {
            item.classList.add('hidden');
        });
        document.querySelectorAll('.accordion-icon').forEach(item => {
            item.classList.remove('rotate-180');
        });

        // Open this accordion
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        // Close this accordion
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}

// Toggle all permissions in a group
function toggleAllPermissions(button, groupName) {
    const accordionItem = button.closest('.accordion-content');
    const checkboxes = accordionItem.querySelectorAll('input[name="permissions[]"]');
    const anyChecked = Array.from(checkboxes).some(cb => cb.checked);

    checkboxes.forEach(checkbox => {
        checkbox.checked = !anyChecked;
    });

    // Update button text
    const buttonText = button.querySelector('.toggle-all-text');
    if (anyChecked) {
        buttonText.textContent = 'Deselect All ' + ucfirst(groupName);
    } else {
        buttonText.textContent = 'Select All ' + ucfirst(groupName);
    }

    // Update counter
    updatePermissionCounters();
}

// Toggle all accordions
function toggleAllAccordions() {
    const accordions = document.querySelectorAll('.accordion-content');
    const toggleAllText = document.getElementById('toggleAllText');
    const allOpen = Array.from(accordions).every(acc => !acc.classList.contains('hidden'));

    if (allOpen) {
        // Close all
        accordions.forEach(accordion => {
            accordion.classList.add('hidden');
        });
        document.querySelectorAll('.accordion-icon').forEach(icon => {
            icon.classList.remove('rotate-180');
        });
        toggleAllText.textContent = 'Expand All';
    } else {
        // Open all
        accordions.forEach(accordion => {
            accordion.classList.remove('hidden');
        });
        document.querySelectorAll('.accordion-icon').forEach(icon => {
            icon.classList.add('rotate-180');
        });
        toggleAllText.textContent = 'Collapse All';
    }
}

// Update permission counters
function updatePermissionCounters() {
    document.querySelectorAll('.permissions-accordion-item').forEach(item => {
        const checkboxes = item.querySelectorAll('input[name="permissions[]"]');
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        const totalCount = checkboxes.length;

        const counter = item.querySelector('.bg-gray-200');
        if (counter) {
            counter.textContent = `${checkedCount}/${totalCount}`;
        }

        // Update percentage
        const percentage = item.querySelector('.text-gray-400');
        if (percentage) {
            percentage.textContent = `${Math.round(checkedCount / totalCount * 100)}% selected`;
        }
    });
}

// Add event listeners for permission checkboxes and field input
document.addEventListener('DOMContentLoaded', function() {
    // Clear field error when user starts typing
    document.addEventListener('input', function(e) {
        if (e.target.id && e.target.tagName === 'INPUT') {
            clearFieldError(e.target.id);
        }
    });

    // Clear field error when user changes select
    document.addEventListener('change', function(e) {
        if (e.target.id && e.target.tagName === 'SELECT') {
            clearFieldError(e.target.id);
        }

        // Handle permission checkboxes
        if (e.target.name === 'permissions[]') {
            updatePermissionCounters();
        }
    });
});

// Function to show field-level error
function showFieldError(fieldId, errorMessage) {
    // Handle Laravel field name mapping
    let actualFieldId = fieldId;

    // Map Laravel validation field names to actual field IDs
    const fieldMapping = {
        'password_confirmation': 'password',
        'permissions': 'permissions_0', // Handle array field differently
    };

    actualFieldId = fieldMapping[fieldId] || fieldId;

    const fieldElement = document.getElementById(actualFieldId);
    if (!fieldElement) {
        console.warn(`Field not found: ${actualFieldId} (original: ${fieldId})`);
        return;
    }

    // Find or create error container
    let errorContainer = fieldElement.parentNode.querySelector('.field-error');
    if (!errorContainer) {
        errorContainer = document.createElement('div');
        errorContainer.className = 'field-error mt-1.5 text-sm text-red-600 flex items-center font-medium';
        errorContainer.innerHTML = `
            <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>
            <span class="error-message"></span>
        `;
        fieldElement.parentNode.appendChild(errorContainer);
    }

    // Set error message and style
    const errorSpan = errorContainer.querySelector('.error-message');
    errorSpan.textContent = errorMessage;

    // Add error styling to field
    fieldElement.classList.add('border-red-500', 'focus:ring-red-500/20', 'focus:border-red-500');
    fieldElement.classList.remove('border-gray-200', 'focus:ring-indigo-500/20', 'focus:border-indigo-500');

    // Make error visible
    errorContainer.style.display = 'flex';

    console.log(`Field error shown for: ${actualFieldId} - ${errorMessage}`);
}

// Function to clear field-level errors
function clearFieldErrors() {
    // Remove all field error containers
    document.querySelectorAll('.field-error').forEach(container => {
        container.remove();
    });

    // Reset field styling
    document.querySelectorAll('[id]').forEach(field => {
        field.classList.remove('border-red-500', 'focus:ring-red-500/20', 'focus:border-red-500');
        field.classList.add('border-gray-200', 'focus:ring-indigo-500/20', 'focus:border-indigo-500');
    });

    // Clear Laravel validation session errors if they exist
    document.querySelectorAll('.validation-error').forEach(error => {
        error.remove();
    });
}

// Function to clear specific field error
function clearFieldError(fieldId) {
    // Handle Laravel field name mapping
    let actualFieldId = fieldId;

    // Map Laravel validation field names to actual field IDs
    const fieldMapping = {
        'password_confirmation': 'password',
        'permissions': 'permissions_0', // Handle array field differently
    };

    actualFieldId = fieldMapping[fieldId] || fieldId;

    const fieldElement = document.getElementById(actualFieldId);
    if (!fieldElement) return;

    // Remove error container
    const errorContainer = fieldElement.parentNode.querySelector('.field-error');
    if (errorContainer) {
        errorContainer.remove();
    }

    // Reset field styling
    fieldElement.classList.remove('border-red-500', 'focus:ring-red-500/20', 'focus:border-red-500');
    fieldElement.classList.add('border-gray-200', 'focus:ring-indigo-500/20', 'focus:border-indigo-500');
}

function submitUserForm() {
    const form = document.getElementById('createUserForm');
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');

    // Clear any previous errors
    clearFieldErrors();

    // Show loading state
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating User...';
    submitButton.disabled = true;

    // Submit via Axios for better UX
    axios.post(form.action, formData, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'multipart/form-data',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('✅ Axios response:', response);
        return response.data;
    })
    .then(data => {
        if (data.success) {
            showToast('User created successfully! Redirecting to users list...', 'success');

            // Optional: Show user details in success message
            if (data.user) {
                console.log('Created user:', data.user);
            }

            // Redirect after showing success message
            setTimeout(() => {
                window.location.href = data.redirect || @js(route('users.index'));
            }, 2000);
        } else {
            showToast(data.message || 'Failed to create user', 'error');
        }
    })
    .catch(error => {
        console.error('Axios error:', error);

        // Check if error has response data (Laravel validation errors)
        if (error.response && error.response.data) {
            const errorData = error.response.data;

            if (errorData.errors) {
                // Handle Laravel validation errors - show field-level errors
                const errorKeys = Object.keys(errorData.errors);

                console.log('🔍 Validation errors received:', errorKeys);
                console.log('🔍 Error details:', errorData.errors);

                // Clear previous field errors
                clearFieldErrors();

                // Show errors for each field
                errorKeys.forEach(fieldKey => {
                    const fieldErrors = errorData.errors[fieldKey];
                    const errorMessage = Array.isArray(fieldErrors) ? fieldErrors[0] : fieldErrors;
                    console.log(`🔨 Showing error for field: ${fieldKey} - ${errorMessage}`);
                    showFieldError(fieldKey, errorMessage);
                });

                // Focus on the first field with error and navigate to correct step
                if (errorKeys.length > 0) {
                    const firstKey = errorKeys[0];
                    const fieldElement = document.getElementById(firstKey);

                    console.log(`🎯 First error field: ${firstKey}, Element found:`, !!fieldElement);

                    if (fieldElement) {
                        // Determine which step contains this field
                        const step1Fields = ['name', 'email', 'employee_id', 'phone', 'password'];
                        const step2Fields = ['department', 'position', 'address'];
                        const step3Fields = ['team_id', 'tech_stack_id', 'sales_leader_id', 'sales_target', 'sales_target_month'];
                        const step4Fields = ['role'];

                        // Handle password confirmation field mapping
                        const actualFieldId = firstKey === 'password_confirmation' ? 'password' : firstKey;

                        setTimeout(() => {
                            if (step1Fields.includes(actualFieldId)) {
                                goToStep(1);
                            } else if (step2Fields.includes(actualFieldId)) {
                                goToStep(2);
                            } else if (step3Fields.includes(actualFieldId)) {
                                goToStep(3);
                            } else if (step4Fields.includes(actualFieldId)) {
                                goToStep(4);
                            }

                            // Focus on the actual field after step transition
                            setTimeout(() => {
                                const actualField = document.getElementById(actualFieldId);
                                if (actualField) {
                                    actualField.focus();
                                    actualField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            }, 300);
                        }, 100);
                    }
                }
            } else {
                // Show general error from server response
                showToast(errorData.message || errorData.error || 'An error occurred while creating the user', 'error');
            }
        } else if (error.request) {
            // Request was made but no response received
            showToast('Network error. Please check your connection and try again.', 'error');
        } else {
            // Something else happened
            showToast(error.message || 'An unexpected error occurred', 'error');
        }
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
    const toastId = 'toast-' + Date.now();

    const toast = document.createElement('div');
    toast.className = `toast ${type === 'error' ? 'toast-error' : type === 'success' ? 'toast-success' : type === 'warning' ? 'toast-warning' : 'toast-info'} flex items-center p-4 mb-4 rounded-lg shadow-lg relative overflow-hidden toast-enter`;
    toast.id = toastId;

    // Icon and title based on type
    let icon = '';
    let title = '';
    switch(type) {
        case 'success':
            icon = '<i class="fas fa-check-circle"></i>';
            title = 'Success';
            break;
        case 'error':
            icon = '<i class="fas fa-exclamation-circle"></i>';
            title = 'Error';
            break;
        case 'warning':
            icon = '<i class="fas fa-exclamation-triangle"></i>';
            title = 'Warning';
            break;
        default:
            icon = '<i class="fas fa-info-circle"></i>';
            title = 'Information';
    }

    toast.innerHTML = `
        <div class="toast-icon mr-3">
            ${icon}
        </div>
        <div class="flex-1">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button type="button" class="toast-close ml-4" onclick="closeToast(this)">
            <i class="fas fa-times"></i>
        </button>
        <div class="toast-progress"></div>
    `;

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