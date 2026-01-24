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
        <h1 class="text-3xl font-bold text-gray-900 mt-4">Edit Tech Stack</h1>
        <p class="text-gray-600 mt-2">Update tech stack information and assignments</p>
    </div>

    <!-- Edit Tech Stack Form -->
    <div class="bg-white rounded-xl shadow-lg">
        <div class="p-8">
            <form method="POST" action="{{ route('tech-stacks.update', $techStack->id) }}">
                @csrf
                @method('PATCH')
                <div class="space-y-8">
                    <!-- Tech Stack Information -->
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-layer-group text-white"></i>
                            </div>
                            Tech Stack Information
                        </h2>

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-tag mr-2 text-indigo-500"></i>Tech Stack Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" required
                                       value="{{ old('name', $techStack->name) }}"
                                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                       placeholder="e.g., Full Stack Web Development">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-align-left mr-2 text-indigo-500"></i>Description
                                </label>
                                <textarea id="description" name="description" rows="4"
                                          class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all resize-none"
                                          placeholder="Describe the technologies and focus of this stack...">{{ old('description', $techStack->description) }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </p>
                                @enderror
                            </div>
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

                                @if(auth()->user()->hasRole('team-leader'))
                                    <!-- For team leaders, show selected user info (auto-assigned) -->
                                    @php $currentLeader = $users->firstWhere('id', $techStack->stack_leader_id) ?? auth()->user(); @endphp
                                    <div class="p-4 bg-purple-50 border border-purple-200 rounded-xl">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                                <span class="text-white font-semibold text-sm">{{ strtoupper(substr($currentLeader->name, 0, 1)) }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-gray-900 truncate">{{ $currentLeader->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $currentLeader->employee_id }}</p>
                                            </div>
                                            <input type="hidden" name="stack_leader_id" value="{{ $currentLeader->id }}">
                                        </div>
                                    </div>
                                @else
                                    <!-- For admins and managers, dropdown with all users -->
                                    <div class="relative">
                                        <select id="stack_leader_id" name="stack_leader_id"
                                                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all appearance-none bg-white">
                                            <option value="">Select a stack leader</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('stack_leader_id', $techStack->stack_leader_id) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->employee_id }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                @endif

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

                            <div>
                                <label for="memberSearch" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-user-plus mr-2 text-blue-500"></i>Search & Add Members
                                </label>
                                <div class="relative">
                                    <input type="text" id="memberSearch"
                                           class="w-full border border-gray-300 rounded-xl px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                                           placeholder="Search for team members..."
                                           autocomplete="off">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                </div>
                                <div id="memberSearchResults" class="relative z-10 mt-1 bg-white border border-gray-300 rounded-xl shadow-lg max-h-60 overflow-y-auto hidden">
                                </div>

                                <!-- Selected Members -->
                                <div id="selectedMembers" class="mt-4 space-y-2 max-h-64 overflow-y-auto">
                                </div>
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
                        <i class="fas fa-save mr-2"></i>Update Tech Stack
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
const allUsers = @json($users);
let selectedMemberIds = {{ old('members', $techStack->users->pluck('id')->toArray()) }};

// Member Search Functionality
const memberSearch = document.getElementById('memberSearch');
const memberSearchResults = document.getElementById('memberSearchResults');
const selectedMembersDiv = document.getElementById('selectedMembers');

memberSearch.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();

    if (searchTerm.length < 2) {
        memberSearchResults.classList.add('hidden');
        return;
    }

    const filtered = allUsers.filter(user =>
        !selectedMemberIds.includes(user.id) &&
        (user.name.toLowerCase().includes(searchTerm) ||
        user.employee_id.toLowerCase().includes(searchTerm))
    );

    if (filtered.length === 0) {
        memberSearchResults.innerHTML = '<div class="p-4 text-gray-500 text-center">No users found</div>';
    } else {
        memberSearchResults.innerHTML = filtered.map(user => `
            <div class="search-result-item p-4 border-b border-gray-100 last:border-b-0 cursor-pointer hover:bg-gray-50" onclick="addMember(${user.id}, '${user.name.replace(/'/g, "\\'")}', '${user.employee_id}')">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                        <span class="text-white font-semibold text-sm">${user.name.charAt(0)}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 truncate">${user.name}</p>
                        <p class="text-sm text-gray-600">${user.employee_id}</p>
                    </div>
                </div>
            </div>
        `).join('');
    }

    memberSearchResults.classList.remove('hidden');
});

memberSearch.addEventListener('focus', function() {
    if (this.value.length >= 2) {
        memberSearchResults.classList.remove('hidden');
    }
});

document.addEventListener('click', function(e) {
    if (!memberSearch.contains(e.target) && !memberSearchResults.contains(e.target)) {
        memberSearchResults.classList.add('hidden');
    }
});

function addMember(id, name, employeeId) {
    if (selectedMemberIds.includes(id)) return;

    selectedMemberIds.push(id);
    memberSearch.value = '';
    memberSearchResults.classList.add('hidden');

    renderSelectedMembers();
}

function removeMember(id) {
    selectedMemberIds = selectedMemberIds.filter(memberId => memberId !== id);
    renderSelectedMembers();
}

function renderSelectedMembers() {
    if (selectedMemberIds.length === 0) {
        selectedMembersDiv.innerHTML = '<p class="text-sm text-gray-500 italic">No members selected yet</p>';
        return;
    }

    selectedMembersDiv.innerHTML = selectedMemberIds.map(id => {
        const user = allUsers.find(u => u.id === id);
        if (!user) return '';

        return `
            <div class="member-chip flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-xl">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                        <span class="text-white font-semibold text-xs">${user.name.charAt(0)}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 text-sm truncate">${user.name}</p>
                        <p class="text-xs text-gray-600">${user.employee_id}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2 flex-shrink-0">
                    <input type="checkbox" name="members[]" value="${user.id}" checked class="sr-only">
                    <button type="button" onclick="removeMember(${user.id})" class="text-red-600 hover:text-red-800 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    renderSelectedMembers();
});
</script>
@endpush
@endsection
