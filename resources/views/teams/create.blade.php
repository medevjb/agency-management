@extends('layouts.dashboard')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('teams.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2 text-sm"></i>
                        <span class="text-sm font-medium">Back to Teams</span>
                    </a>
                </li>
            </ol>
        </nav>
        <div class="mt-6">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Create New Team</h1>
            <p class="text-gray-600 text-lg">Build a high-performing team by adding members and defining leadership</p>
        </div>
    </div>

    <!-- Create Team Form -->
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Form Header -->
            <div class="bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 p-8 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl mb-4">
                    <i class="fas fa-users text-3xl text-white"></i>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Team Details</h2>
                <p class="text-indigo-100">Fill in the information below to create your team</p>
            </div>

            <form method="POST" action="{{ route('teams.store') }}" id="createTeamForm">
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
                                <span class="ml-2 font-medium text-gray-500">Leadership</span>
                            </div>
                            <div class="w-12 h-1 bg-gray-300 rounded" id="progress2"></div>
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 bg-gray-300 rounded-full text-gray-600 font-semibold" id="step3">3</div>
                                <span class="ml-2 font-medium text-gray-500">Members</span>
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
                                Basic Team Information
                            </h3>
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <div>
                                    <label for="name" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-tag mr-2 text-indigo-500"></i>
                                        Team Name <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" id="name" name="name" required
                                               value="{{ old('name') }}"
                                               class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-lg"
                                               placeholder="e.g., Development Team Alpha">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                            <i class="fas fa-users text-gray-400"></i>
                                        </div>
                                    </div>
                                    @error('name')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                    <p class="mt-2 text-sm text-gray-500">Give your team a memorable and descriptive name</p>
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-align-left mr-2 text-indigo-500"></i>
                                        Team Description
                                    </label>
                                    <div class="relative">
                                        <textarea id="description" name="description" rows="6"
                                                  class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-lg resize-none"
                                                  placeholder="Describe the team's purpose, goals, and responsibilities...">{{ old('description') }}</textarea>
                                        <div class="absolute bottom-4 right-4 text-sm text-gray-400" id="charCount">0/500</div>
                                    </div>
                                    @error('description')
                                        <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                            <i class="fas fa-exclamation-circle mr-2"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                    <p class="mt-2 text-sm text-gray-500">Optional: Briefly explain what this team does</p>
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

                    <!-- Step 2: Leadership (Hidden by default) -->
                    <div id="step2Content" class="space-y-8 hidden">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <div class="bg-green-100 rounded-lg p-3 mr-3">
                                    <i class="fas fa-user-tie text-green-600"></i>
                                </div>
                                Team Leadership
                            </h3>
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                                <div class="flex items-center mb-4">
                                    <div class="bg-blue-100 rounded-full p-3 mr-3">
                                        <i class="fas fa-crown text-blue-600 text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Assign Team Leader</h4>
                                        <p class="text-sm text-gray-600">Choose who will lead and coordinate this team</p>
                                    </div>
                                </div>
                                <div>
                                    <label for="team_leader_id" class="block text-sm font-bold text-gray-700 mb-3">
                                        Select Team Leader
                                    </label>
                                    <div class="relative">
                                        <select id="team_leader_id" name="team_leader_id"
                                                class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm appearance-none transition-all duration-200 text-lg">
                                            <option value="">Choose a team leader...</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('team_leader_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} @if($user->employee_id) ({{ $user->employee_id }}) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                    @error('team_leader_id')
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

                    <!-- Step 3: Team Members (Hidden by default) -->
                    <div id="step3Content" class="space-y-8 hidden">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <div class="bg-purple-100 rounded-lg p-3 mr-3">
                                    <i class="fas fa-users text-purple-600"></i>
                                </div>
                                Team Members
                            </h3>
                            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-100">
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <h4 class="font-bold text-gray-900">Select Team Members</h4>
                                        <p class="text-sm text-gray-600">Choose who will be part of this team</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button type="button" onclick="selectAllMembers()"
                                                class="px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors duration-200 text-sm font-medium">
                                            Select All
                                        </button>
                                        <button type="button" onclick="deselectAllMembers()"
                                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm font-medium">
                                            Deselect All
                                        </button>
                                    </div>
                                </div>
                                <div class="max-h-80 overflow-y-auto space-y-2 border-2 border-purple-100 rounded-xl p-4 bg-white">
                                    @foreach($users as $user)
                                        <label class="flex items-center p-4 rounded-xl hover:bg-purple-50 cursor-pointer transition-all duration-200 border-2 border-transparent hover:border-purple-200">
                                            <input type="checkbox" name="members[]" value="{{ $user->id }}"
                                                   {{ in_array($user->id, old('members', [])) ? 'checked' : '' }}
                                                   class="mr-4 w-5 h-5 text-purple-600 bg-purple-100 border-purple-300 rounded focus:ring-purple-500 focus:ring-2"
                                                   onchange="updateSelectedCount()">
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-600 flex items-center mt-1">
                                                    @if($user->employee_id)
                                                        <i class="fas fa-id-badge mr-1 text-purple-500"></i>
                                                        {{ $user->employee_id }}
                                                    @else
                                                        <i class="fas fa-exclamation-triangle mr-1 text-yellow-500"></i>
                                                        No ID
                                                    @endif
                                                    @if($user->position)
                                                        <span class="mx-2">•</span>
                                                        <i class="fas fa-briefcase mr-1 text-purple-500"></i>
                                                        {{ $user->position }}
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('members')
                                    <p class="mt-3 text-sm text-red-600 flex items-center font-medium">
                                        <i class="fas fa-exclamation-circle mr-2"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <div class="mt-4 flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-600">
                                        <span id="selectedCount">0</span> members selected
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $users->count() }} available users</span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex flex-col sm:flex-row justify-between gap-4">
                            <button type="button" onclick="goToStep(2)"
                                    class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors duration-200">
                                <i class="fas fa-arrow-left mr-2"></i> Previous
                            </button>
                            <div class="flex gap-4">
                                <a href="{{ route('teams.index') }}"
                                   class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-colors duration-200">
                                    <i class="fas fa-times mr-2"></i> Cancel
                                </a>
                                <button type="submit"
                                        class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                                    <i class="fas fa-check mr-2"></i> Create Team
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

@push('styles')
<style>
    /* Custom scrollbar for members list */
    .max-h-80::-webkit-scrollbar {
        width: 6px;
    }
    .max-h-80::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .max-h-80::-webkit-scrollbar-thumb {
        background: #9333ea;
        border-radius: 10px;
    }
    .max-h-80::-webkit-scrollbar-thumb:hover {
        background: #7c3aed;
    }

    /* Checkbox styling */
    input[type="checkbox"]:checked + label {
        background-color: #faf5ff;
        border-color: #9333ea;
    }

    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }

    /* Toast Notifications */
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

    .toast-info {
        background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
        border-left: 4px solid #1D4ED8;
    }

    .toast-progress {
        animation: toast-progress 5s linear;
    }

    @keyframes toast-progress {
        from { width: 100%; }
        to { width: 0%; }
    }

    /* Toast animations */
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
    console.log('Create team page loaded');

    // Character counter for description
    const descriptionTextarea = document.getElementById('description');
    const charCount = document.getElementById('charCount');

    if (descriptionTextarea && charCount) {
        descriptionTextarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count + '/500';

            if (count > 500) {
                charCount.classList.add('text-red-500');
                charCount.classList.remove('text-gray-400');
            } else {
                charCount.classList.remove('text-red-500');
                charCount.classList.add('text-gray-400');
            }
        });
    }

    // Initialize selected count
    updateSelectedCount();

    // Test step navigation
    console.log('Step 1 content:', document.getElementById('step1Content'));
    console.log('Step 2 content:', document.getElementById('step2Content'));
    console.log('Step 3 content:', document.getElementById('step3Content'));
});

function goToStep(stepNumber) {
    console.log('Going to step:', stepNumber); // Debug log

    // Hide all steps and reset their styling
    for (let i = 1; i <= 3; i++) {
        const stepContent = document.getElementById('step' + i + 'Content');
        const stepIndicator = document.getElementById('step' + i);
        const stepLabel = stepIndicator.nextElementSibling;
        const progress = document.getElementById('progress' + i);

        if (stepContent) stepContent.classList.add('hidden');
        if (stepIndicator) {
            stepIndicator.classList.remove('bg-indigo-600', 'text-white');
            stepIndicator.classList.add('bg-gray-300', 'text-gray-600');
        }
        if (stepLabel) {
            stepLabel.classList.remove('text-gray-900');
            stepLabel.classList.add('text-gray-500');
        }
        if (progress) {
            progress.classList.remove('bg-indigo-600');
            progress.classList.add('bg-gray-300');
        }
    }

    // Show current step
    const currentStepContent = document.getElementById('step' + stepNumber + 'Content');
    const currentStepIndicator = document.getElementById('step' + stepNumber);
    const currentStepLabel = currentStepIndicator.nextElementSibling;

    if (currentStepContent) {
        currentStepContent.classList.remove('hidden');
        console.log('Step content shown:', currentStepContent.id);
    } else {
        console.error('Step content not found: step' + stepNumber + 'Content');
    }

    if (currentStepIndicator) {
        currentStepIndicator.classList.remove('bg-gray-300', 'text-gray-600');
        currentStepIndicator.classList.add('bg-indigo-600', 'text-white');
    }

    if (currentStepLabel) {
        currentStepLabel.classList.remove('text-gray-500');
        currentStepLabel.classList.add('text-gray-900');
    }

    // Update progress bar for completed steps
    for (let i = 1; i < stepNumber; i++) {
        const progress = document.getElementById('progress' + i);
        if (progress) {
            progress.classList.remove('bg-gray-300');
            progress.classList.add('bg-indigo-600');
        }
    }

    // Scroll to top of form
    const form = document.getElementById('createTeamForm');
    if (form) {
        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function selectAllMembers() {
    const checkboxes = document.querySelectorAll('input[name="members[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = true);
    updateSelectedCount();
}

function deselectAllMembers() {
    const checkboxes = document.querySelectorAll('input[name="members[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('input[name="members[]"]:checked');
    const count = checkboxes.length;
    document.getElementById('selectedCount').textContent = count;
}

// Toast Notification Functions
function showToast(message, type = 'info', duration = 5000) {
    const toastContainer = document.getElementById('toastContainer');

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast ${type} rounded-lg shadow-2xl p-4 text-white flex items-center space-x-3 toast-enter`;

    // Get icon based on type
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

    // Add relative positioning for progress bar
    toast.style.position = 'relative';
    toast.style.overflow = 'hidden';

    // Add to container
    toastContainer.appendChild(toast);

    // Trigger animation
    setTimeout(() => {
        toast.classList.remove('toast-enter');
        toast.classList.add('show');
    }, 10);

    // Auto remove after duration
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

// Form validation and submission
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createTeamForm');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Client-side validation
            const teamName = document.getElementById('name').value.trim();
            const teamLeader = document.getElementById('team_leader_id').value;
            const selectedMembers = document.querySelectorAll('input[name="members[]"]:checked');

            // Validation checks
            if (!teamName) {
                showToast('Please enter a team name', 'error');
                goToStep(1);
                document.getElementById('name').focus();
                return;
            }

            if (teamName.length < 3) {
                showToast('Team name must be at least 3 characters long', 'error');
                goToStep(1);
                document.getElementById('name').focus();
                return;
            }

            if (teamName.length > 100) {
                showToast('Team name cannot exceed 100 characters', 'error');
                goToStep(1);
                document.getElementById('name').focus();
                return;
            }

            // Check if team leader is selected as member (conflict)
            if (teamLeader && selectedMembers.length > 0) {
                const leaderIsMember = Array.from(selectedMembers).some(checkbox => checkbox.value === teamLeader);
                if (leaderIsMember) {
                    showToast('Team leader cannot also be selected as a team member', 'warning');
                    goToStep(2);
                    return;
                }
            }

            // If validation passes, submit form
            submitTeamForm();
        });
    }
});

function submitTeamForm() {
    const form = document.getElementById('createTeamForm');
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');

    // Show loading state
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating Team...';
    submitButton.disabled = true;

    // Submit via Axios for better UX
    axios.post(form.action, formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    .then(response => {
        const html = response.data;
        // Check if redirected (success) or returned validation errors
        if (html.includes('teams.index')) {
            showToast('Team created successfully!', 'success');
            setTimeout(() => {
                window.location.href = '/teams';
            }, 1500);
        } else {
            // Parse validation errors from returned HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const errorElements = tempDiv.querySelectorAll('.text-red-600');

            if (errorElements.length > 0) {
                const errors = Array.from(errorElements).map(el => el.textContent.trim()).filter(text => text);
                errors.forEach(error => {
                    showToast(error, 'error');
                });

                // Find which step has errors and navigate to it
                if (tempDiv.querySelector('#name')?.closest('.text-red-600')) {
                    goToStep(1);
                } else if (tempDiv.querySelector('#team_leader_id')?.closest('.text-red-600')) {
                    goToStep(2);
                } else if (tempDiv.querySelector('input[name="members[]"]')?.closest('.text-red-600')) {
                    goToStep(3);
                }
            } else {
                showToast('An unexpected error occurred. Please try again.', 'error');
            }
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
</script>
@endpush
@endsection