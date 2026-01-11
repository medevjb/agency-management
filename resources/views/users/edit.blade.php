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
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Edit User</h1>
            <p class="text-gray-600 text-lg">Update {{ $user->name }}'s information, roles, and permissions</p>
        </div>
    </div>

    <!-- Edit User Form -->
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 p-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl mb-4">
                    <i class="fas fa-user-edit text-3xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Edit User Information</h2>
                <p class="text-emerald-100">Update user details and permissions</p>
            </div>

            <form method="POST" action="{{ route('users.update', $user->id) }}" id="editUserForm" novalidate>
                @csrf
                @method('PATCH')
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
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" required
                                           value="{{ old('name', $user->name) }}"
                                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-base"
                                           placeholder="Enter full name">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" required
                                           value="{{ old('email', $user->email) }}"
                                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-base"
                                           placeholder="user@company.com">
                                </div>

                                <div>
                                    <label for="employee_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Employee ID
                                    </label>
                                    <input type="text" id="employee_id" name="employee_id"
                                           value="{{ old('employee_id', $user->employee_id) }}"
                                           maxlength="13"
                                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-base"
                                           placeholder="13 characters (optional)">
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Phone Number
                                    </label>
                                    <input type="tel" id="phone" name="phone"
                                           value="{{ old('phone', $user->phone) }}"
                                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-base"
                                           placeholder="+1 (555) 123-4567">
                                </div>

                                <div class="lg:col-span-2">
                                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Password (leave blank to keep current)
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="password" name="password"
                                               class="w-full px-4 py-2.5 pr-12 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-base"
                                               placeholder="Enter new password (optional)">
                                        <button type="button" onclick="togglePasswordVisibility('password')"
                                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600">
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
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <div class="bg-purple-100 rounded-lg p-3 mr-3">
                                    <i class="fas fa-briefcase text-purple-600"></i>
                                </div>
                                Professional Information
                            </h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="department" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Department
                                    </label>
                                    <input type="text" id="department" name="department"
                                           value="{{ old('department', $user->department) }}"
                                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-base"
                                           placeholder="Engineering">
                                </div>

                                <div>
                                    <label for="position" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Position
                                    </label>
                                    <input type="text" id="position" name="position"
                                           value="{{ old('position', $user->position) }}"
                                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-base"
                                           placeholder="Senior Developer">
                                </div>

                                <div>
                                    <label for="hire_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Hire Date
                                    </label>
                                    <input type="text" id="hire_date" name="hire_date"
                                           value="{{ old('hire_date', $user->hire_date ? $user->hire_date->format('Y-m-d') : '') }}"
                                           class="datepicker w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-base cursor-pointer"
                                           placeholder="Select hire date..."
                                           readonly>
                                </div>

                                <div>
                                    <label for="salary" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Salary
                                    </label>
                                    <input type="number" id="salary" name="salary" step="0.01" min="0"
                                           value="{{ old('salary', $user->salary) }}"
                                           class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-base"
                                           placeholder="75000.00">
                                </div>

                                <div class="lg:col-span-2">
                                    <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Address
                                    </label>
                                    <textarea id="address" name="address" rows="2"
                                              class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-base resize-none"
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
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <div class="bg-green-100 rounded-lg p-3 mr-3">
                                    <i class="fas fa-sitemap text-green-600"></i>
                                </div>
                                Team & Tech Stack Assignments
                            </h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div>
                                    <label for="team_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Team
                                    </label>
                                    <select id="team_id" name="team_id"
                                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-base">
                                        <option value="">No Team</option>
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}" {{ old('team_id', $user->team_id) == $team->id ? 'selected' : '' }}>
                                                {{ $team->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="tech_stack_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Tech Stack
                                    </label>
                                    <select id="tech_stack_id" name="tech_stack_id"
                                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-base">
                                        <option value="">No Tech Stack</option>
                                        @foreach($techStacks as $stack)
                                            <option value="{{ $stack->id }}" {{ old('tech_stack_id', $user->tech_stack_id) == $stack->id ? 'selected' : '' }}>
                                                {{ $stack->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
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
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <div class="bg-red-100 rounded-lg p-3 mr-3">
                                    <i class="fas fa-shield-alt text-red-600"></i>
                                </div>
                                Role & Permissions
                            </h3>

                            <div class="space-y-6">
                                <div>
                                    <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Primary Role
                                    </label>
                                    <select id="role" name="role"
                                            class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-base">
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
                                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                                        <div class="flex items-start mb-3">
                                            <i class="fas fa-users-cog text-indigo-600 mt-1 mr-2"></i>
                                            <div class="flex-1">
                                                <h4 class="text-sm font-semibold text-indigo-900 mb-1">Managed Teams</h4>
                                                <p class="text-xs text-indigo-700">Select which teams this manager can oversee. Managers will only have access to teams they're assigned to.</p>
                                            </div>
                                        </div>
                                        <div class="space-y-2">
                                            @foreach($teams as $team)
                                                <label class="flex items-center p-2 rounded hover:bg-white transition-colors cursor-pointer">
                                                    <input type="checkbox"
                                                           name="managed_teams[]"
                                                           value="{{ $team->id }}"
                                                           {{ in_array($team->id, old('managed_teams', $user->managedTeams->pluck('id')->toArray())) ? 'checked' : '' }}
                                                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                                    <span class="ml-3 text-sm text-gray-700">{{ $team->name }}</span>
                                                    @if($team->description)
                                                        <span class="ml-2 text-xs text-gray-500">({{ $team->description }})</span>
                                                    @endif
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 pt-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-lg font-semibold text-gray-900">Additional Permissions</h4>
                                        <button type="button" onclick="toggleAllAccordions()"
                                                class="text-sm px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200">
                                            <i class="fas fa-expand-alt mr-1"></i>
                                            <span id="toggleAllText">Expand All</span>
                                        </button>
                                    </div>

                                    <div class="space-y-4" id="permissionsContainer">
                                        @foreach($permissions as $group => $groupPermissions)
                                            <div class="permissions-accordion-item border border-gray-200 rounded-lg overflow-hidden">
                                                <button type="button" onclick="toggleAccordion(this)"
                                                        class="w-full px-6 py-4 bg-gray-50 hover:bg-gray-100 flex items-center justify-between transition-colors duration-200">
                                                    <div class="flex items-center">
                                                        <i class="fas fa-folder text-indigo-500 mr-3"></i>
                                                        <span class="font-semibold text-gray-900">{{ ucfirst($group) }}</span>
                                                    </div>
                                                    <div class="flex items-center space-x-4">
                                                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">
                                                            {{ $user->permissions->whereIn('id', $groupPermissions->pluck('id'))->count() }}/{{ $groupPermissions->count() }}
                                                        </span>
                                                        <span class="text-gray-400 text-sm">{{ round($user->permissions->whereIn('id', $groupPermissions->pluck('id'))->count() / max($groupPermissions->count(), 1) * 100) }}% selected</span>
                                                        <i class="fas fa-chevron-down accordion-icon transition-transform duration-200"></i>
                                                    </div>
                                                </button>

                                                <div class="accordion-content hidden border-t border-gray-200 bg-white p-6">
                                                    <div class="flex items-center justify-between mb-4">
                                                        <p class="text-sm text-gray-600">Select additional {{ $group }} permissions for this user</p>
                                                        <button type="button" onclick="toggleAllPermissions(this, '{{ $group }}')"
                                                                class="text-sm px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 rounded-lg transition-colors duration-200">
                                                            <span class="toggle-all-text">Select All {{ ucfirst($group) }}</span>
                                                        </button>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                                        @foreach($groupPermissions as $permission)
                                                            <label class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 cursor-pointer transition-all duration-200">
                                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                                       {{ $user->permissions->contains($permission->id) ? 'checked' : '' }}
                                                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                                                <span class="text-sm text-gray-700">{{ ucfirst(str_replace('-', ' ', str_replace($group . ' ', '', $permission->name))) }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
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
                                   class="px-5 py-2 border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200 text-sm">
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

@push('scripts')
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
});

function goToStep(stepNumber) {
    for (let i = 1; i <= 4; i++) {
        const stepContent = document.getElementById('step' + i + 'Content');
        const stepIndicator = document.getElementById('step' + i);
        const progressElement = document.getElementById('progress' + i);

        if (stepContent) stepContent.classList.add('hidden');
        if (stepIndicator) {
            stepIndicator.classList.remove('bg-indigo-600', 'text-white');
            stepIndicator.classList.add('bg-gray-300', 'text-gray-600');
        }
        if (progressElement) progressElement.classList.add('bg-gray-300');
    }

    const currentStepContent = document.getElementById('step' + stepNumber + 'Content');
    const currentStepIndicator = document.getElementById('step' + stepNumber);

    if (currentStepContent) currentStepContent.classList.remove('hidden');
    if (currentStepIndicator) {
        currentStepIndicator.classList.remove('bg-gray-300', 'text-gray-600');
        currentStepIndicator.classList.add('bg-indigo-600', 'text-white');
    }

    for (let i = 1; i < stepNumber; i++) {
        const progressElement = document.getElementById('progress' + i);
        if (progressElement) {
            progressElement.classList.remove('bg-gray-300');
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

function toggleAccordion(button) {
    const accordionItem = button.closest('.permissions-accordion-item');
    const content = accordionItem.querySelector('.accordion-content');
    const icon = button.querySelector('.accordion-icon');

    if (content.classList.contains('hidden')) {
        document.querySelectorAll('.accordion-content').forEach(item => item.classList.add('hidden'));
        document.querySelectorAll('.accordion-icon').forEach(item => item.classList.remove('rotate-180'));

        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}

function toggleAllPermissions(button, groupName) {
    const accordionItem = button.closest('.accordion-content');
    const checkboxes = accordionItem.querySelectorAll('input[name="permissions[]"]');
    const anyChecked = Array.from(checkboxes).some(cb => cb.checked);

    checkboxes.forEach(checkbox => checkbox.checked = !anyChecked);

    const buttonText = button.querySelector('.toggle-all-text');
    if (anyChecked) {
        buttonText.textContent = 'Deselect All ' + ucfirst(groupName);
    } else {
        buttonText.textContent = 'Select All ' + ucfirst(groupName);
    }

    updatePermissionCounters();
}

function toggleAllAccordions() {
    const accordions = document.querySelectorAll('.accordion-content');
    const toggleAllText = document.getElementById('toggleAllText');
    const allOpen = Array.from(accordions).every(acc => !acc.classList.contains('hidden'));

    if (allOpen) {
        accordions.forEach(accordion => accordion.classList.add('hidden'));
        document.querySelectorAll('.accordion-icon').forEach(icon => icon.classList.remove('rotate-180'));
        toggleAllText.textContent = 'Expand All';
    } else {
        accordions.forEach(accordion => accordion.classList.remove('hidden'));
        document.querySelectorAll('.accordion-icon').forEach(icon => icon.classList.add('rotate-180'));
        toggleAllText.textContent = 'Collapse All';
    }
}

function updatePermissionCounters() {
    document.querySelectorAll('.permissions-accordion-item').forEach(item => {
        const checkboxes = item.querySelectorAll('input[name="permissions[]"]');
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        const totalCount = checkboxes.length;

        const counter = item.querySelector('.bg-gray-200');
        if (counter) counter.textContent = `${checkedCount}/${totalCount}`;

        const percentage = item.querySelector('.text-gray-400');
        if (percentage) percentage.textContent = `${Math.round(checkedCount / totalCount * 100)}% selected`;
    });
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

        if (e.target.name === 'permissions[]') {
            updatePermissionCounters();
        }
    });
});

function showFieldError(fieldId, errorMessage) {
    let actualFieldId = fieldId;
    const fieldMapping = { 'password_confirmation': 'password', 'permissions': 'permissions_0' };
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
    const fieldMapping = { 'password_confirmation': 'password', 'permissions': 'permissions_0' };
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
                window.location.href = data.redirect || '/users';
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
                        const step2Fields = ['department', 'position', 'hire_date', 'salary', 'address'];
                        const step3Fields = ['team_id', 'tech_stack_id'];
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
