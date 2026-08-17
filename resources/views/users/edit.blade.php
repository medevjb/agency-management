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
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-2">Edit User</h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Update {{ $user->name }}'s information, roles, and permissions</p>
        </div>
    </div>

    <!-- Edit User Form -->
    <div class="max-w-6xl mx-auto">
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 p-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl mb-4">
                    <i class="fas fa-user-edit text-3xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Edit User Information</h2>
                <p class="text-emerald-100">Update user details and permissions</p>
            </div>

            <form method="POST" action="{{ route('users.update', $user) }}" id="editUserForm" novalidate>
                @csrf
                @method('PATCH')
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
                                    <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" required
                                           value="{{ old('name', $user->name) }}"
                                           class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base"
                                           placeholder="Enter full name">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" required
                                           value="{{ old('email', $user->email) }}"
                                           class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base"
                                           placeholder="user@company.com">
                                </div>

                                <div>
                                    <label for="employee_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Employee ID
                                    </label>
                                    <input type="text" id="employee_id" name="employee_id"
                                           value="{{ old('employee_id', $user->employee_id) }}"
                                           maxlength="13"
                                           class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base"
                                           placeholder="13 characters (optional)">
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Phone Number
                                    </label>
                                    <input type="tel" id="phone" name="phone"
                                           value="{{ old('phone', $user->phone) }}"
                                           class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base"
                                           placeholder="+1 (555) 123-4567">
                                </div>

                                <div class="lg:col-span-2">
                                    <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Password (leave blank to keep current)
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="password" name="password"
                                               class="w-full px-4 py-2.5 pr-12 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base"
                                               placeholder="Enter new password (optional)">
                                        <button type="button" onclick="togglePasswordVisibility('password')"
                                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                                            <i id="password-toggle-icon" class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" onclick="goToStep(2)"
                                    class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                                Next <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Professional Information -->
                    <div id="step2Content" class="space-y-8 hidden">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
                                <div class="bg-purple-100 dark:bg-purple-500/10 rounded-lg p-3 mr-3">
                                    <i class="fas fa-briefcase text-purple-600 dark:text-purple-400"></i>
                                </div>
                                Professional Information
                            </h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="department" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Department
                                    </label>
                                    <input type="text" id="department" name="department"
                                           value="{{ old('department', $user->department) }}"
                                           class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base"
                                           placeholder="Engineering">
                                </div>

                                <div>
                                    <label for="position" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Position
                                    </label>
                                    <input type="text" id="position" name="position"
                                           value="{{ old('position', $user->position) }}"
                                           class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base"
                                           placeholder="Senior Developer">
                                </div>
                                <div class="lg:col-span-2">
                                    <label for="address" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Address
                                    </label>
                                    <textarea id="address" name="address" rows="2"
                                              class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base resize-none"
                                              placeholder="Enter full address">{{ old('address', $user->address) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <button type="button" onclick="goToStep(1)"
                                    class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200">
                                <i class="fas fa-arrow-left mr-2"></i> Previous
                            </button>
                            <button type="button" onclick="goToStep(3)"
                                    class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-all duration-200">
                                Next <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Assignments -->
                    <div id="step3Content" class="space-y-8 hidden">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
                                <div class="bg-green-100 dark:bg-green-500/10 rounded-lg p-3 mr-3">
                                    <i class="fas fa-sitemap text-green-600 dark:text-green-400"></i>
                                </div>
                                Assignment
                            </h3>

                            @php $currentTrack = old('track', $user->isSales() ? 'sales' : 'operation'); @endphp
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Which side of the business is this person on?</p>
                            <div class="track-choice mb-6">
                                <label class="track-option">
                                    <input type="radio" name="track" value="operation" {{ $currentTrack === 'operation' ? 'checked' : '' }}>
                                    <span class="track-card">
                                        <span class="track-icon"><i class="fas fa-screwdriver-wrench"></i></span>
                                        <span>
                                            <span class="track-name">Operations</span>
                                            <span class="track-hint">Delivers the work — gets a team and a tech stack</span>
                                        </span>
                                    </span>
                                </label>
                                <label class="track-option">
                                    <input type="radio" name="track" value="sales" {{ $currentTrack === 'sales' ? 'checked' : '' }}>
                                    <span class="track-card">
                                        <span class="track-icon"><i class="fas fa-handshake"></i></span>
                                        <span>
                                            <span class="track-name">Sales</span>
                                            <span class="track-hint">Brings the work in — gets a sales leader and a target</span>
                                        </span>
                                    </span>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 {{ $currentTrack !== 'operation' ? 'hidden' : '' }}" data-track-panel="operation">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Team</label>
                                    <x-ajax-select name="team_id"
                                                   :url="route('search.teams')"
                                                   :value="$user->team_id"
                                                   :label="$user->team?->name"
                                                   placeholder="Search teams..." />
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tech Stack</label>
                                    <x-ajax-select name="tech_stack_id"
                                                   :url="route('search.tech-stacks')"
                                                   :value="$user->tech_stack_id"
                                                   :label="$user->techStack?->name"
                                                   placeholder="Search tech stacks..." />
                                </div>

                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 {{ $currentTrack !== 'sales' ? 'hidden' : '' }}" data-track-panel="sales">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Sales Leader</label>
                                    <select name="sales_leader_id"
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
                                        <option value="">No sales leader</option>
                                        @foreach(\App\Models\User::withRoleName('sales-leader')->orderBy('name')->get() as $leader)
                                            <option value="{{ $leader->id }}" {{ (int) old('sales_leader_id', $user->sales_leader_id) === $leader->id ? 'selected' : '' }}>
                                                {{ $leader->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Only used for sales members — the leader sees every order their members sell.
                                    </p>
                                </div>

                            </div>
                        </div>

                        <div class="mt-6 max-w-md">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Monthly Minimum Target</label>
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                    <input type="number" step="0.01" min="0" name="sales_target"
                                           value="{{ old('sales_target', $user->salesTargetFor()) }}"
                                           class="w-full pl-8 pr-4 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm"
                                           placeholder="0.00">
                                </div>
                                <input type="month" name="sales_target_month"
                                       value="{{ old('sales_target_month', now()->format('Y-m')) }}"
                                       class="w-40 px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
                            </div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Optional. The minimum this person should sell or be credited from that month on — it carries forward until you set a new one.
                            </p>

                            @if($user->salesTargets->isNotEmpty())
                                <div class="mt-2 rounded-lg border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-800">
                                    @foreach($user->salesTargets->take(6) as $target)
                                        <div class="flex items-center justify-between px-3 py-1.5 text-xs">
                                            <span class="text-gray-500 dark:text-gray-400">From {{ $target->month->format('F Y') }}</span>
                                            <span class="font-semibold text-gray-900 dark:text-gray-100">${{ number_format((float) $target->amount, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @error('sales_target')
                                <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-between">
                            <button type="button" onclick="goToStep(2)"
                                    class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200">
                                <i class="fas fa-arrow-left mr-2"></i> Previous
                            </button>
                            <button type="button" onclick="goToStep(4)"
                                    class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-all duration-200">
                                Next <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Permissions -->
                    <div id="step4Content" class="space-y-8 hidden">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6 flex items-center">
                                <div class="bg-red-100 dark:bg-red-500/10 rounded-lg p-3 mr-3">
                                    <i class="fas fa-shield-alt text-red-600 dark:text-red-400"></i>
                                </div>
                                Role & Permissions
                            </h3>

                            <div class="space-y-6">
                                <div>
                                    <label for="role" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Primary Role
                                    </label>
                                    <select id="role" name="role"
                                            class="w-full px-4 py-2.5 border-2 border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm transition-all duration-200 text-base">
                                        <option value="">No Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) == $role->name ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                                           {{ in_array($team->id, old('managed_teams', $user->managedTeams->pluck('id')->toArray())) ? 'checked' : '' }}
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
                        </div>

                        <div class="flex justify-between">
                            <button type="button" onclick="goToStep(3)"
                                    class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200">
                                <i class="fas fa-arrow-left mr-2"></i> Previous
                            </button>
                            <div class="flex gap-3">
                                <a href="{{ route('users.index') }}"
                                   class="px-5 py-2 border-2 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200 text-sm">
                                    <i class="fas fa-times mr-1.5 text-xs"></i> Cancel
                                </a>
                                <button type="submit"
                                        class="px-6 py-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-semibold rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105 text-sm">
                                    <i class="fas fa-check mr-1.5 text-xs"></i> Update User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

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

        function applyTrack(clearHidden) {
            const track = currentTrack();

            panels.forEach(panel => {
                const isActive = panel.dataset.trackPanel === track;
                panel.classList.toggle('hidden', !isActive);

                if (!isActive && clearHidden) {
                    panel.querySelectorAll('input:not([type="month"]), select').forEach(input => {
                        input.value = '';
                    });
                }
            });

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

        // On load, keep whatever the person already has — only clear fields
        // once they actively switch tracks.
        applyTrack(false);
        tracks.forEach(track => track.addEventListener('change', () => applyTrack(true)));
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Edit user page loaded');

    // Handle role change to show/hide managed teams section
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

    // Initial check
    toggleManagedTeamsSection();

    // Listen for role changes
    roleSelect.addEventListener('change', toggleManagedTeamsSection);

    for (let i = 1; i <= 4; i++) {
        const stepContent = document.getElementById('step' + i + 'Content');
        const stepIndicator = document.getElementById('step' + i);
        const progressElement = document.getElementById('progress' + i);

        if (!stepContent) console.error(`Step ${i} content not found!`);
        if (!stepIndicator) console.error(`Step ${i} indicator not found!`);
        if (i < 4 && !progressElement) console.error(`Progress ${i} not found!`);
    }

    goToStep(1);

    // Same as the create wizard: Enter should never submit before the last
    // step, since the Submit button lives on a step that may still be hidden.
    const wizardForm = document.getElementById('editUserForm');
    wizardForm?.addEventListener('keydown', function(event) {
        if (event.key !== 'Enter' || event.target.tagName === 'TEXTAREA') {
            return;
        }

        if (currentWizardStep < 4) {
            event.preventDefault();
        }
    });
});

let currentWizardStep = 1;

function goToStep(stepNumber) {
    currentWizardStep = stepNumber;
    for (let i = 1; i <= 4; i++) {
        const stepContent = document.getElementById('step' + i + 'Content');
        const stepIndicator = document.getElementById('step' + i);
        const progressElement = document.getElementById('progress' + i);

        if (stepContent) stepContent.classList.add('hidden');
        if (stepIndicator) {
            stepIndicator.classList.remove('bg-indigo-600', 'text-white');
            stepIndicator.classList.add('bg-gray-300', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300');
        }
        if (progressElement) progressElement.classList.add('bg-gray-300', 'dark:bg-gray-700');
    }

    const currentStepContent = document.getElementById('step' + stepNumber + 'Content');
    const currentStepIndicator = document.getElementById('step' + stepNumber);

    if (currentStepContent) currentStepContent.classList.remove('hidden');
    if (currentStepIndicator) {
        currentStepIndicator.classList.remove('bg-gray-300', 'dark:bg-gray-700', 'text-gray-600', 'dark:text-gray-300');
        currentStepIndicator.classList.add('bg-indigo-600', 'text-white');
    }

    for (let i = 1; i < stepNumber; i++) {
        const progressElement = document.getElementById('progress' + i);
        if (progressElement) {
            progressElement.classList.remove('bg-gray-300', 'dark:bg-gray-700');
            progressElement.classList.add('bg-indigo-600');
        }
    }

    const form = document.getElementById('editUserForm');
    if (form) form.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

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

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('input', function(e) {
        if (e.target.id && e.target.tagName === 'INPUT') {
            clearFieldError(e.target.id);
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target.id && e.target.tagName === 'SELECT') {
            clearFieldError(e.target.id);
        }
    });
});

function showFieldError(fieldId, errorMessage) {
    let actualFieldId = fieldId;
    const fieldMapping = { 'password_confirmation': 'password' };
    actualFieldId = fieldMapping[fieldId] || fieldId;

    const fieldElement = document.getElementById(actualFieldId);
    if (!fieldElement) {
        console.warn(`Field not found: ${actualFieldId} (original: ${fieldId})`);
        return;
    }

    let errorContainer = fieldElement.parentNode.querySelector('.field-error');
    if (!errorContainer) {
        errorContainer = document.createElement('div');
        errorContainer.className = 'field-error mt-1.5 text-sm text-red-600 flex items-center font-medium';
        errorContainer.innerHTML = '<i class="fas fa-exclamation-circle mr-1.5 text-xs"></i><span class="error-message"></span>';
        fieldElement.parentNode.appendChild(errorContainer);
    }

    const errorSpan = errorContainer.querySelector('.error-message');
    errorSpan.textContent = errorMessage;

    fieldElement.classList.add('border-red-500', 'focus:ring-red-500/20', 'focus:border-red-500');
    fieldElement.classList.remove('border-gray-200', 'focus:ring-indigo-500/20', 'focus:border-indigo-500');

    errorContainer.style.display = 'flex';
}

function clearFieldErrors() {
    document.querySelectorAll('.field-error').forEach(container => container.remove());
    document.querySelectorAll('[id]').forEach(field => {
        field.classList.remove('border-red-500', 'focus:ring-red-500/20', 'focus:border-red-500');
        field.classList.add('border-gray-200', 'focus:ring-indigo-500/20', 'focus:border-indigo-500');
    });
}

function clearFieldError(fieldId) {
    let actualFieldId = fieldId;
    const fieldMapping = { 'password_confirmation': 'password' };
    actualFieldId = fieldMapping[fieldId] || fieldId;

    const fieldElement = document.getElementById(actualFieldId);
    if (!fieldElement) return;

    const errorContainer = fieldElement.parentNode.querySelector('.field-error');
    if (errorContainer) errorContainer.remove();

    fieldElement.classList.remove('border-red-500', 'focus:ring-red-500/20', 'focus:border-red-500');
    fieldElement.classList.add('border-gray-200', 'focus:ring-indigo-500/20', 'focus:border-indigo-500');
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editUserForm');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();

            if (!name) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Name is required',
                    confirmButtonColor: '#ef4444'
                });
                goToStep(1);
                document.getElementById('name').focus();
                return;
            }

            if (!email) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Email address is required',
                    confirmButtonColor: '#ef4444'
                });
                goToStep(1);
                document.getElementById('email').focus();
                return;
            }

            submitUserForm();
        });
    }
});

function submitUserForm() {
    const form = document.getElementById('editUserForm');
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');

    clearFieldErrors();

    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating User...';
    submitButton.disabled = true;

    axios.post(form.action, formData, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'multipart/form-data'
        }
    })
    .then(response => {
        console.log('✅ Axios response:', response);
        return response.data;
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message || 'User updated successfully!',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            }).then(() => {
                window.location.href = data.redirect || @js(route('users.index'));
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to update user',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .catch(error => {
        console.error('Axios error:', error);

        if (error.response && error.response.data) {
            const errorData = error.response.data;

            if (errorData.errors) {
                const errorKeys = Object.keys(errorData.errors);

                console.log('🔍 Validation errors received:', errorKeys);
                console.log('🔍 Error details:', errorData.errors);

                clearFieldErrors();

                errorKeys.forEach(fieldKey => {
                    const fieldErrors = errorData.errors[fieldKey];
                    const errorMessage = Array.isArray(fieldErrors) ? fieldErrors[0] : fieldErrors;
                    console.log(`🔨 Showing error for field: ${fieldKey} - ${errorMessage}`);
                    showFieldError(fieldKey, errorMessage);
                });

                if (errorKeys.length > 0) {
                    const firstKey = errorKeys[0];
                    const fieldElement = document.getElementById(firstKey);

                    console.log(`🎯 First error field: ${firstKey}, Element found:`, !!fieldElement);

                    if (fieldElement) {
                        const step1Fields = ['name', 'email', 'employee_id', 'phone', 'password'];
                        const step2Fields = ['department', 'position', 'address'];
                        const step3Fields = ['team_id', 'tech_stack_id', 'sales_leader_id', 'sales_target', 'sales_target_month'];
                        const step4Fields = ['role'];

                        const actualFieldId = firstKey === 'password_confirmation' ? 'password' : firstKey;

                        setTimeout(() => {
                            if (step1Fields.includes(actualFieldId)) goToStep(1);
                            else if (step2Fields.includes(actualFieldId)) goToStep(2);
                            else if (step3Fields.includes(actualFieldId)) goToStep(3);
                            else if (step4Fields.includes(actualFieldId)) goToStep(4);

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
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorData.message || errorData.error || 'Failed to update user',
                    confirmButtonColor: '#ef4444'
                });
            }
        } else if (error.request) {
            Swal.fire({
                icon: 'error',
                title: 'Network Error',
                text: 'Please check your connection and try again.',
                confirmButtonColor: '#ef4444'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'An unexpected error occurred',
                confirmButtonColor: '#ef4444'
            });
        }
    })
    .finally(() => {
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}
</script>
@endpush
