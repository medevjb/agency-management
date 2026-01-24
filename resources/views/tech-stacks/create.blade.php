@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('tech-stacks.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Tech Stacks
                    </a>
                </li>
            </ol>
        </nav>
        <h1 class="text-3xl font-bold text-gray-900 mt-4">Create New Tech Stack</h1>
        <p class="text-gray-600 mt-2">Create a new technology stack and assign stack leader and members</p>
    </div>

    <!-- Create Tech Stack Form -->
    <div class="bg-white rounded-xl shadow-lg">
        <div class="p-8">
            <form method="POST" action="{{ route('tech-stacks.store') }}">
                @csrf
                <div class="space-y-8">
                    <!-- Tech Stack Information -->
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-layer-group text-white"></i>
                            </div>
                            Tech Stack Information
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-tag mr-2 text-indigo-500"></i>Tech Stack Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" required
                                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                       placeholder="e.g., Full Stack Web Development">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            @if(auth()->user()->hasRole('team-leader'))
                                <!-- Hidden for team leaders -->
                                <input type="hidden" name="team_id" value="{{ auth()->user()->team_id }}">
                            @else
                                <div>
                                    <label for="team_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-users mr-2 text-indigo-500"></i>Team <span class="text-red-500">*</span>
                                    </label>
                                    <select id="team_id" name="team_id" required
                                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                        <option value="">Select a team</option>
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('team_id')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-2 text-indigo-500"></i>Description
                            </label>
                            <textarea id="description" name="description" rows="4"
                                      class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all resize-none"
                                      placeholder="Describe the technologies and focus of this stack..."></textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Stack Leadership & Members -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Stack Leader -->
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-user-tie text-white"></i>
                                </div>
                                Stack Leader
                            </h2>

                            <div>
                                <label for="stack_leader_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-crown mr-2 text-purple-500"></i>Select Leader
                                </label>

                                <div class="relative">
                                    <select id="stack_leader_id" name="stack_leader_id"
                                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all appearance-none bg-white">
                                        <option value="">Select a stack leader</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('stack_leader_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->employee_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>

                                @error('stack_leader_id')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Stack Members -->
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                    <i class="fas fa-users text-white"></i>
                                </div>
                                Stack Members
                            </h2>

                            <div id="memberSelectorContainer">
                                <label for="memberSearch" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user-plus mr-2 text-blue-500"></i>Search & Add Members
                                </label>

                                <!-- Selected Members Display -->
                                <div id="selectedMembersDisplay" class="border border-gray-300 rounded-xl p-3 min-h-[60px] bg-gray-50 mb-2">
                                    <span class="text-gray-400 text-sm">No members selected</span>
                                </div>

                                <!-- Hidden inputs for selected members will be added dynamically -->

                                <!-- Search and Add Members -->
                                <div class="relative">
                                    <input type="text" id="memberSearch"
                                           class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                           placeholder="Search members by name or email..."
                                           autocomplete="off">

                                    <!-- Dropdown List -->
                                    <div id="memberDropdown" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                        <!-- Members will be populated here by JavaScript -->
                                    </div>
                                </div>

                                <p class="mt-1 text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Start typing to search and add team members
                                </p>
                                @error('members')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-4">
                    <a href="{{ route('tech-stacks.index') }}"
                       class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all duration-200 font-semibold">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-plus mr-2"></i>Create Tech Stack
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .search-result-item {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .search-result-item:hover {
        background-color: #f3f4f6;
    }
    .member-chip {
        animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@push('scripts')
<script>
(function() {
    // State
    let selectedMembers = [];
    let searchTimeout = null;
    let currentMembers = []; // Store currently displayed members

    // DOM Elements
    const searchInput = document.getElementById('memberSearch');
    const dropdown = document.getElementById('memberDropdown');
    const selectedDisplay = document.getElementById('selectedMembersDisplay');

    // Get team ID
    const teamId = {{ auth()->user()->hasRole('team-leader') ? auth()->user()->team_id : 'null' }};
    const teamSelect = document.getElementById('team_id');

    // Fetch team members from server with search
    async function fetchMembers(search = '') {
        try {
            const currentTeamId = teamId || (teamSelect ? teamSelect.value : null);

            if (!currentTeamId) {
                dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-yellow-600 text-center"><i class="fas fa-exclamation-triangle mr-2"></i>Please select a team first</div>';
                return [];
            }

            const excludeIds = selectedMembers.map(m => m.id);
            const params = new URLSearchParams();
            if (search) {
                params.append('search', search);
            }
            if (excludeIds.length > 0) {
                params.append('exclude', excludeIds.join(','));
            }

            const response = await axios.get(`/tech-stacks/search-members/${currentTeamId}?${params}`);
            currentMembers = response.data.users;
            return currentMembers;
        } catch (error) {
            console.error('Error fetching members:', error);
            return [];
        }
    }

    // Render dropdown with members
    function renderDropdown(members) {
        if (members.length === 0) {
            dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 text-center">No team members found</div>';
        } else {
            dropdown.innerHTML = members.map(member => `
                <div class="member-option flex items-center gap-3 p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0 transition-colors"
                     data-member-id="${member.id}"
                     data-member-name="${member.name}"
                     data-member-email="${member.email}"
                     data-member-employee-id="${member.employee_id}">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                        ${member.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                            <div class="font-semibold text-sm text-gray-900 truncate">${member.name}</div>
                            <span class="text-xs text-white bg-blue-500 px-2 py-0.5 rounded-full flex-shrink-0">${member.employee_id || 'N/A'}</span>
                            ${member.position ? `<span class="text-xs text-gray-600 px-2 py-0.5 rounded-full bg-gray-100 flex-shrink-0">${member.position}</span>` : ''}
                        </div>
                        <div class="text-xs text-gray-500 truncate">
                            ${member.email}
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full bg-blue-100 hover:bg-blue-200 flex items-center justify-center transition-colors">
                            <i class="fas fa-plus text-blue-600 text-xs"></i>
                        </div>
                    </div>
                </div>
            `).join('');

            // Add click handlers
            dropdown.querySelectorAll('.member-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent closing dropdown
                    const member = {
                        id: parseInt(this.dataset.memberId),
                        name: this.dataset.memberName,
                        email: this.dataset.memberEmail,
                        employee_id: this.dataset.memberEmployeeId
                    };
                    addMember(member);
                });
            });
        }
    }

    // Add member to selected list
    function addMember(member) {
        if (!selectedMembers.find(m => m.id === member.id)) {
            selectedMembers.push(member);
            updateSelectedDisplay();

            // Refresh search results but keep dropdown open
            if (searchInput.value.trim()) {
                searchMembers(searchInput.value);
            } else {
                // Just re-render current members excluding selected
                const availableMembers = currentMembers.filter(m => !selectedMembers.find(s => s.id === m.id));
                renderDropdown(availableMembers);
            }
        }
    }

    // Remove member from selected list
    function removeMember(memberId) {
        selectedMembers = selectedMembers.filter(m => m.id !== memberId);
        updateSelectedDisplay();

        // Refresh search results
        if (searchInput.value.trim()) {
            searchMembers(searchInput.value);
        } else {
            // Fetch fresh list
            fetchMembers('').then(members => renderDropdown(members));
        }
    }

    // Update selected members display
    function updateSelectedDisplay() {
        if (selectedMembers.length === 0) {
            selectedDisplay.innerHTML = '<span class="text-gray-400 text-sm">No members selected</span>';
        } else {
            selectedDisplay.innerHTML = selectedMembers.map(member => `
                <span class="inline-flex items-center gap-1 px-3 py-1 m-1 bg-blue-100 text-blue-800 rounded-lg text-sm">
                    <span>${member.name}</span>
                    <button type="button" class="remove-member-btn hover:text-red-600 transition-colors" data-member-id="${member.id}">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </span>
            `).join('');

            // Add click handlers for remove buttons
            selectedDisplay.querySelectorAll('.remove-member-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const memberId = parseInt(this.dataset.memberId);
                    removeMember(memberId);
                });
            });
        }

        // Update hidden inputs
        const container = document.getElementById('memberSelectorContainer');
        const oldInputs = container.querySelectorAll('input[name="members[]"]');
        oldInputs.forEach(input => input.remove());

        // Add new inputs
        selectedMembers.forEach(member => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'members[]';
            input.value = member.id;
            container.appendChild(input);
        });
    }

    // Search and display members (debounced)
    function searchMembers(query) {
        // Clear previous timeout
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        // Debounce search
        searchTimeout = setTimeout(async () => {
            const members = await fetchMembers(query);
            renderDropdown(members);
        }, 300);
    }

    // Event Listeners
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();

        // Always show dropdown when typing or focused
        dropdown.classList.remove('hidden');

        // Clear previous timeout
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        // Debounce and fetch members
        searchTimeout = setTimeout(async () => {
            const members = await fetchMembers(query);
            renderDropdown(members);
        }, 300);
    });

    searchInput.addEventListener('focus', async function() {
        // Show dropdown when focused - load default members
        dropdown.classList.remove('hidden');
        const members = await fetchMembers('');
        renderDropdown(members);
    });

    // Team selection change handler (for admin/managers)
    if (teamSelect) {
        teamSelect.addEventListener('change', function() {
            // Clear selected members when team changes
            selectedMembers = [];
            updateSelectedDisplay();
            dropdown.classList.add('hidden');
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target) && e.target !== searchInput) {
            dropdown.classList.add('hidden');
        }
    });

    // Initialize
    updateSelectedDisplay();
})();
</script>
@endpush
@endsection
