@extends('layouts.dashboard')

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('teams.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Teams
                    </a>
                </li>
            </ol>
        </nav>
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start gap-6">
            <div class="flex-1">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Edit Team</h1>
                <p class="text-gray-600 text-lg">Update team information and manage team members</p>
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('teams.show', $team->id) }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                    <i class="fas fa-eye mr-2"></i>
                    View Team
                </a>
            </div>
        </div>
    </div>

    <!-- Edit Team Form -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 p-6 border-b border-gray-200">
            <div class="flex items-center space-x-3">
                <div class="bg-indigo-100 rounded-lg p-3">
                    <i class="fas fa-edit text-indigo-600 text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Edit Team Information</h2>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('teams.update', $team->id) }}" id="editTeamForm">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Team Information -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <div class="bg-blue-100 rounded-lg p-2 mr-3">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                </div>
                                Basic Information
                            </h3>
                            <div class="space-y-5">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-tag mr-2 text-indigo-500"></i>
                                        Team Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" required
                                           value="{{ old('name', $team->name) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200"
                                           placeholder="Enter team name">
                                    <div id="nameError" class="mt-2 text-sm text-red-600 items-center hidden">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        <span></span>
                                    </div>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-align-left mr-2 text-indigo-500"></i>
                                        Description
                                    </label>
                                    <textarea id="description" name="description" rows="5"
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 resize-none"
                                              placeholder="Enter team description">{{ old('description', $team->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Team Leadership & Members -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <div class="bg-green-100 rounded-lg p-2 mr-3">
                                    <i class="fas fa-user-tie text-green-600"></i>
                                </div>
                                Leadership & Members
                            </h3>
                            <div class="space-y-5">
                                <!-- Custom Searchable Leader Dropdown -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-user-shield mr-2 text-green-500"></i>
                                        Team Leader
                                    </label>
                                    <div class="relative" id="leaderDropdownContainer">
                                        <input type="hidden" name="team_leader_id" id="team_leader_id" value="{{ old('team_leader_id', $team->team_leader_id) }}">

                                        <button type="button" id="leaderDropdownTrigger"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm transition-all duration-200 text-left flex items-center justify-between">
                                            <div id="leaderDisplay" class="flex items-center flex-1 min-w-0">
                                                <span class="text-gray-400">Select a team leader</span>
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
                                    <div id="leaderError" class="mt-2 text-sm text-red-600 items-center hidden">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        <span></span>
                                    </div>
                                    @error('team_leader_id')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Team Members -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-users mr-2 text-green-500"></i>
                                        Team Members
                                    </label>
                                    <div class="relative mb-2">
                                        <input type="text" id="members_search" placeholder="Search members..."
                                               class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm text-sm"
                                               autocomplete="off">
                                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                    </div>
                                    <div id="members_list" class="border border-gray-200 rounded-lg p-4 bg-gray-50 max-h-64 overflow-y-auto">
                                        @foreach($users as $user)
                                            @if($user->id !== auth()->id())
                                            <label class="member-item flex items-center p-3 hover:bg-white rounded-lg cursor-pointer transition-colors duration-200"
                                                   data-name="{{ strtolower($user->name) }}"
                                                   data-employee="{{ strtolower($user->employee_id ?? '') }}"
                                                   data-position="{{ strtolower($user->position ?? '') }}">
                                                <input type="checkbox" name="members[]" value="{{ $user->id }}"
                                                       {{ in_array($user->id, old('members', $team->members->pluck('id')->toArray())) ? 'checked' : '' }}
                                                       class="mr-3 w-4 h-4 text-indigo-600 bg-indigo-100 border-indigo-300 rounded focus:ring-2 focus:ring-indigo-500">
                                                <div class="flex-1">
                                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $user->employee_id ?? 'No ID' }} &bull; {{ $user->position ?? 'No position' }}</div>
                                                </div>
                                            </label>
                                            @endif
                                        @endforeach
                                    </div>
                                    @error('members')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <!-- Current Team Members Display -->
                                @if($team->members->where('id', '!=', auth()->id())->count() > 0)
                                <div class="mt-6">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-users mr-2 text-blue-500"></i>
                                        Current Members ({{ $team->members->where('id', '!=', auth()->id())->count() }})
                                    </label>
                                    <div class="space-y-2">
                                        @foreach($team->members as $member)
                                            @if($member->id !== auth()->id())
                                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                                                <div class="flex items-center">
                                                    <img class="h-10 w-10 rounded-full object-cover mr-3"
                                                         src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=3B82F6&color=fff"
                                                         alt="{{ $member->name }}">
                                                    <div>
                                                        <div class="font-medium text-gray-900">{{ $member->name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $member->employee_id }}</div>
                                                    </div>
                                                </div>
                                                <form action="{{ route('teams.remove-member', [$team->id, $member->id]) }}"
                                                      method="POST" onsubmit="return confirm('Remove {{ $member->name }} from team?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition-colors duration-200"
                                                            title="Remove member">
                                                        <i class="fas fa-user-minus"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('teams.index') }}"
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transform transition-all duration-200 hover:scale-105">
                        <i class="fas fa-save mr-2"></i>
                        Update Team
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast Notifications Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

@push('styles')
<style>
    .max-h-64::-webkit-scrollbar {
        width: 6px;
    }
    .max-h-64::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .max-h-64::-webkit-scrollbar-thumb {
        background: #6366f1;
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
    var leaderSelectedId = '{{ old("team_leader_id", $team->team_leader_id ?? "") }}';
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
                selectLeader(this.getAttribute('data-id'));
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
        displayEl.innerHTML = '<span class="text-gray-400">Select a team leader</span>';
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
        leaderDropdownOpen ? closeLeaderDropdown() : openLeaderDropdown();
    });

    searchInput.addEventListener('input', function() {
        renderLeaderOptions(this.value);
    });

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLeaderDropdown();
    });

    clearBtn.addEventListener('click', clearLeader);

    document.addEventListener('click', function(e) {
        var container = document.getElementById('leaderDropdownContainer');
        if (!container.contains(e.target)) closeLeaderDropdown();
    });

    if (leaderSelectedId) {
        selectLeader(leaderSelectedId);
    }

    // --- Members search ---
    document.getElementById('members_search').addEventListener('input', function() {
        var search = this.value.toLowerCase();
        var members = document.getElementsByClassName('member-item');
        for (var i = 0; i < members.length; i++) {
            var member = members[i];
            var text = (member.getAttribute('data-name') || '') + ' ' +
                       (member.getAttribute('data-employee') || '') + ' ' +
                       (member.getAttribute('data-position') || '');
            member.style.display = text.includes(search) ? '' : 'none';
        }
    });

    // --- AJAX Form Submission ---
    var form = document.getElementById('editTeamForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            var teamName = document.getElementById('name').value.trim();
            if (!teamName) {
                showToast('Please enter a team name', 'error');
                document.getElementById('name').focus();
                return;
            }

            var submitButton = form.querySelector('button[type="submit"]');
            var originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating Team...';
            submitButton.disabled = true;

            var formData = new FormData(form);

            axios.post(form.action, formData)
                .then(function(response) {
                    if (response.data && response.data.success) {
                        showToast(response.data.message || 'Team updated successfully!', 'success');
                        setTimeout(function() {
                            window.location.href = response.data.redirect || '{{ route("teams.index") }}';
                        }, 1000);
                    } else {
                        showToast('Team updated successfully!', 'success');
                        setTimeout(function() {
                            window.location.href = '{{ route("teams.index") }}';
                        }, 1000);
                    }
                })
                .catch(function(error) {
                    if (error.response && error.response.status === 422) {
                        var errors = error.response.data.errors || {};
                        Object.keys(errors).forEach(function(key) {
                            errors[key].forEach(function(msg) {
                                showToast(msg, 'error');
                            });
                        });
                    } else {
                        showToast('An error occurred. Please try again.', 'error');
                    }
                })
                .finally(function() {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                });
        });
    }
});

// --- Toast Notifications ---
function showToast(message, type, duration) {
    type = type || 'info';
    duration = duration || 5000;
    var toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;

    var toast = document.createElement('div');
    toast.className = 'toast toast-' + type + ' rounded-lg shadow-2xl p-4 text-white flex items-start space-x-3';
    toast.style.cssText = 'min-width:320px;max-width:480px;transform:translateX(400px);transition:all 0.3s cubic-bezier(0.68,-0.55,0.265,1.55);position:relative;overflow:hidden;';

    var icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
    var icon = icons[type] || icons.info;
    var bgColors = { success: 'background:linear-gradient(135deg,#10B981,#059669);border-left:4px solid #047857;',
                     error: 'background:linear-gradient(135deg,#EF4444,#DC2626);border-left:4px solid #B91C1C;',
                     warning: 'background:linear-gradient(135deg,#F59E0B,#D97706);border-left:4px solid #B45309;',
                     info: 'background:linear-gradient(135deg,#3B82F6,#2563EB);border-left:4px solid #1D4ED8;' };

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
