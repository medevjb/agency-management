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

                                <!-- Custom Searchable Leader Dropdown -->
                                <div class="relative" id="leaderDropdownContainer">
                                    <input type="hidden" name="team_leader_id" id="team_leader_id" value="{{ old('team_leader_id') }}">

                                    <button type="button" id="leaderDropdownTrigger"
                                            class="w-full px-5 py-4 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-left flex items-center justify-between">
                                        <div id="leaderDisplay" class="flex items-center flex-1 min-w-0">
                                            <span class="text-gray-400 text-lg">Choose a team leader...</span>
                                        </div>
                                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200 ml-2" id="leaderChevron"></i>
                                    </button>

                                    <div id="leaderDropdown" class="hidden absolute z-50 w-full mt-2 bg-white rounded-xl shadow-2xl border border-gray-200 overflow-hidden">
                                        <div class="p-3 border-b border-gray-100">
                                            <div class="relative">
                                                <input type="text" id="leaderSearchInput" placeholder="Search by name or employee ID..."
                                                       class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                                       autocomplete="off">
                                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                            </div>
                                        </div>
                                        <div id="leaderOptions" class="max-h-64 overflow-y-auto p-2"></div>
                                        <div class="p-2 border-t border-gray-100">
                                            <button type="button" id="clearLeaderBtn"
                                                    class="w-full px-3 py-2 text-sm text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                <i class="fas fa-times mr-2"></i>Clear selection
                                            </button>
                                        </div>
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
                                <div class="flex items-center justify-between mb-4">
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
                                <div class="relative mb-4">
                                    <input type="text" id="members_search" placeholder="Search members..."
                                           class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm text-sm"
                                           autocomplete="off">
                                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                                <div class="max-h-80 overflow-y-auto space-y-2 border-2 border-purple-100 rounded-xl p-4 bg-white">
                                    @foreach($users as $user)
                                        @if($user->id !== auth()->id())
                                        <label class="member-item flex items-center p-4 rounded-xl hover:bg-purple-50 cursor-pointer transition-all duration-200 border-2 border-transparent hover:border-purple-200"
                                               data-name="{{ strtolower($user->name) }}"
                                               data-employee="{{ strtolower($user->employee_id ?? '') }}"
                                               data-position="{{ strtolower($user->position ?? '') }}">
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
                                                        <span class="mx-2">&bull;</span>
                                                        <i class="fas fa-briefcase mr-1 text-purple-500"></i>
                                                        {{ $user->position }}
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                        @endif
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
                                    <span class="text-xs text-gray-500">{{ $users->where('id', '!=', auth()->id())->count() }} available users</span>
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
    .max-h-80::-webkit-scrollbar,
    .max-h-64::-webkit-scrollbar {
        width: 6px;
    }
    .max-h-80::-webkit-scrollbar-track,
    .max-h-64::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .max-h-80::-webkit-scrollbar-thumb,
    .max-h-64::-webkit-scrollbar-thumb {
        background: #9333ea;
        border-radius: 10px;
    }

    .leader-option {
        transition: all 0.15s ease;
    }
    .leader-option:hover {
        background-color: #eef2ff;
    }
    .leader-option.selected {
        background-color: #e0e7ff;
        border-color: #6366f1;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Leader Searchable Dropdown ---
    var leaderUsers = @json($usersJson);
    var leaderSelectedId = '{{ old("team_leader_id", "") }}';
    var leaderDropdownOpen = false;

    var triggerBtn = document.getElementById('leaderDropdownTrigger');
    var dropdown = document.getElementById('leaderDropdown');
    var searchInput = document.getElementById('leaderSearchInput');
    var optionsContainer = document.getElementById('leaderOptions');
    var hiddenInput = document.getElementById('team_leader_id');
    var displayEl = document.getElementById('leaderDisplay');
    var chevron = document.getElementById('leaderChevron');
    var clearBtn = document.getElementById('clearLeaderBtn');

    function renderLeaderOptions(filter) {
        var search = (filter || '').toLowerCase();
        var html = '';
        var count = 0;

        leaderUsers.forEach(function(user) {
            var name = user.name.toLowerCase();
            var empId = (user.employee_id || '').toLowerCase();
            var position = (user.position || '').toLowerCase();

            if (search && !name.includes(search) && !empId.includes(search) && !position.includes(search)) {
                return;
            }

            var isSelected = String(user.id) === String(leaderSelectedId);
            var selectedClass = isSelected ? 'selected bg-indigo-50 border-indigo-200' : 'border-transparent';
            var checkIcon = isSelected ? '<i class="fas fa-check text-indigo-600 ml-auto"></i>' : '';

            html += '<div class="leader-option flex items-center p-3 rounded-lg cursor-pointer border-2 ' + selectedClass + ' mb-1" data-id="' + user.id + '">' +
                '<img src="https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=4F46E5&color=fff&size=40" class="w-10 h-10 rounded-full mr-3 flex-shrink-0">' +
                '<div class="flex-1 min-w-0">' +
                    '<div class="font-semibold text-gray-900 truncate">' + user.name + '</div>' +
                    '<div class="text-sm text-gray-500">' + (user.employee_id || 'No ID') + (user.position ? ' &bull; ' + user.position : '') + '</div>' +
                '</div>' +
                checkIcon +
            '</div>';
            count++;
        });

        if (count === 0) {
            html = '<div class="p-4 text-center text-gray-500"><i class="fas fa-search mr-2"></i>No users found</div>';
        }

        optionsContainer.innerHTML = html;

        optionsContainer.querySelectorAll('.leader-option').forEach(function(el) {
            el.addEventListener('click', function() {
                var userId = this.getAttribute('data-id');
                selectLeader(userId);
            });
        });
    }

    function selectLeader(userId) {
        leaderSelectedId = userId;
        hiddenInput.value = userId;

        var user = leaderUsers.find(function(u) { return String(u.id) === String(userId); });
        if (user) {
            displayEl.innerHTML =
                '<img src="https://ui-avatars.com/api/?name=' + encodeURIComponent(user.name) + '&background=4F46E5&color=fff&size=40" class="w-8 h-8 rounded-full mr-3 flex-shrink-0">' +
                '<div class="min-w-0">' +
                    '<div class="font-semibold text-gray-900 truncate">' + user.name + '</div>' +
                    '<div class="text-xs text-gray-500">' + (user.employee_id || 'No ID') + '</div>' +
                '</div>';
        }

        closeLeaderDropdown();
    }

    function clearLeader() {
        leaderSelectedId = '';
        hiddenInput.value = '';
        displayEl.innerHTML = '<span class="text-gray-400 text-lg">Choose a team leader...</span>';
        closeLeaderDropdown();
    }

    function openLeaderDropdown() {
        leaderDropdownOpen = true;
        dropdown.classList.remove('hidden');
        chevron.classList.add('rotate-180');
        searchInput.value = '';
        renderLeaderOptions('');
        setTimeout(function() { searchInput.focus(); }, 50);
    }

    function closeLeaderDropdown() {
        leaderDropdownOpen = false;
        dropdown.classList.add('hidden');
        chevron.classList.remove('rotate-180');
    }

    triggerBtn.addEventListener('click', function() {
        if (leaderDropdownOpen) {
            closeLeaderDropdown();
        } else {
            openLeaderDropdown();
        }
    });

    searchInput.addEventListener('input', function() {
        renderLeaderOptions(this.value);
    });

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLeaderDropdown();
        }
    });

    clearBtn.addEventListener('click', function() {
        clearLeader();
    });

    document.addEventListener('click', function(e) {
        var container = document.getElementById('leaderDropdownContainer');
        if (!container.contains(e.target)) {
            closeLeaderDropdown();
        }
    });

    if (leaderSelectedId) {
        selectLeader(leaderSelectedId);
    }

    // --- Character counter ---
    var descriptionTextarea = document.getElementById('description');
    var charCount = document.getElementById('charCount');

    if (descriptionTextarea && charCount) {
        descriptionTextarea.addEventListener('input', function() {
            var count = this.value.length;
            charCount.textContent = count + '/500';
            charCount.classList.toggle('text-red-500', count > 500);
            charCount.classList.toggle('text-gray-400', count <= 500);
        });
    }

    updateSelectedCount();

    // --- Members search ---
    document.getElementById('members_search').addEventListener('input', function() {
        filterTeamMembers(this.value);
    });

    // --- Form submission ---
    var form = document.getElementById('createTeamForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            var teamName = document.getElementById('name').value.trim();
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

            submitTeamForm();
        });
    }
});

function goToStep(stepNumber) {
    for (var i = 1; i <= 3; i++) {
        var stepContent = document.getElementById('step' + i + 'Content');
        var stepIndicator = document.getElementById('step' + i);
        var stepLabel = stepIndicator ? stepIndicator.nextElementSibling : null;
        var progress = document.getElementById('progress' + i);

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

    var currentStepContent = document.getElementById('step' + stepNumber + 'Content');
    var currentStepIndicator = document.getElementById('step' + stepNumber);
    var currentStepLabel = currentStepIndicator ? currentStepIndicator.nextElementSibling : null;

    if (currentStepContent) currentStepContent.classList.remove('hidden');
    if (currentStepIndicator) {
        currentStepIndicator.classList.remove('bg-gray-300', 'text-gray-600');
        currentStepIndicator.classList.add('bg-indigo-600', 'text-white');
    }
    if (currentStepLabel) {
        currentStepLabel.classList.remove('text-gray-500');
        currentStepLabel.classList.add('text-gray-900');
    }

    for (var j = 1; j < stepNumber; j++) {
        var p = document.getElementById('progress' + j);
        if (p) {
            p.classList.remove('bg-gray-300');
            p.classList.add('bg-indigo-600');
        }
    }

    var formEl = document.getElementById('createTeamForm');
    if (formEl) {
        formEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function selectAllMembers() {
    document.querySelectorAll('input[name="members[]"]').forEach(function(cb) { cb.checked = true; });
    updateSelectedCount();
}

function deselectAllMembers() {
    document.querySelectorAll('input[name="members[]"]').forEach(function(cb) { cb.checked = false; });
    updateSelectedCount();
}

function updateSelectedCount() {
    var count = document.querySelectorAll('input[name="members[]"]:checked').length;
    var el = document.getElementById('selectedCount');
    if (el) el.textContent = count;
}

function filterTeamMembers(searchValue) {
    var search = (searchValue || '').toLowerCase();
    var members = document.getElementsByClassName('member-item');

    for (var i = 0; i < members.length; i++) {
        var member = members[i];
        var text = (member.getAttribute('data-name') || '') + ' ' +
                   (member.getAttribute('data-employee') || '') + ' ' +
                   (member.getAttribute('data-position') || '');

        member.style.display = text.includes(search) ? '' : 'none';
    }
}

function submitTeamForm() {
    var form = document.getElementById('createTeamForm');
    var formData = new FormData(form);
    var submitButton = form.querySelector('button[type="submit"]');

    var originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating Team...';
    submitButton.disabled = true;

    axios.post(form.action, formData)
        .then(function(response) {
            if (response.data && response.data.success) {
                showToast(response.data.message || 'Team created successfully!', 'success');
                setTimeout(function() {
                    window.location.href = response.data.redirect || '{{ route("teams.index") }}';
                }, 1000);
            } else {
                showToast('Team created successfully!', 'success');
                setTimeout(function() {
                    window.location.href = '{{ route("teams.index") }}';
                }, 1000);
            }
        })
        .catch(function(error) {
            if (error.response && error.response.status === 422) {
                var errors = error.response.data.errors || {};
                var errorKeys = Object.keys(errors);

                errorKeys.forEach(function(key) {
                    errors[key].forEach(function(msg) {
                        showToast(msg, 'error');
                    });
                });

                if (errors.name) {
                    goToStep(1);
                } else if (errors.team_leader_id) {
                    goToStep(2);
                } else if (errors.members) {
                    goToStep(3);
                }
            } else {
                showToast('An error occurred. Please try again.', 'error');
            }
        })
        .finally(function() {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
}

// --- Toast Notifications ---
function showToast(message, type, duration) {
    type = type || 'info';
    duration = duration || 5000;
    var toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;

    var toast = document.createElement('div');
    toast.className = 'toast rounded-lg shadow-2xl p-4 text-white flex items-start space-x-3';
    toast.style.cssText = 'min-width:320px;max-width:480px;transform:translateX(400px);transition:all 0.3s cubic-bezier(0.68,-0.55,0.265,1.55);position:relative;overflow:hidden;';

    var icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
    var bgColors = { success: 'background:linear-gradient(135deg,#10B981,#059669);border-left:4px solid #047857;',
                     error: 'background:linear-gradient(135deg,#EF4444,#DC2626);border-left:4px solid #B91C1C;',
                     warning: 'background:linear-gradient(135deg,#F59E0B,#D97706);border-left:4px solid #B45309;',
                     info: 'background:linear-gradient(135deg,#3B82F6,#2563EB);border-left:4px solid #1D4ED8;' };
    var icon = icons[type] || icons.info;
    toast.style.cssText += bgColors[type] || bgColors.info;

    toast.innerHTML =
        '<div class="flex-shrink-0 mt-0.5"><i class="fas ' + icon + ' text-xl"></i></div>' +
        '<div class="flex-1"><p class="font-semibold text-sm">' + message + '</p></div>' +
        '<button onclick="this.closest(\'.toast\').remove()" class="flex-shrink-0 text-white hover:text-gray-200"><i class="fas fa-times"></i></button>';

    toastContainer.appendChild(toast);

    setTimeout(function() { toast.style.transform = 'translateX(0)'; }, 10);
    if (duration > 0) {
        setTimeout(function() {
            toast.style.transform = 'translateX(400px)';
            setTimeout(function() { toast.remove(); }, 300);
        }, duration);
    }
}
</script>
@endpush
@endsection
