@extends('layouts.dashboard')

@section('title', 'My Team Members')

@php
    use App\Services\TeamMemberService;
    $currentUser = auth()->user();
    $canManage = $currentUser->hasRole(['team-leader', 'stack-leader', 'admin', 'operation-manager', 'project-manager']);
    $activeCount = $members->where('is_active', true)->count();
    $inactiveCount = $members->where('is_active', false)->count();
    $teamMemberCount = $members->filter(function($m) { return $m->hasRole('team-member'); })->count();
    $stackLeaderCount = $members->filter(function($m) { return $m->hasRole('stack-leader'); })->count();
@endphp

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-7xl mx-auto">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                @if($currentUser->hasRole('team-leader'))
                    My Team Members
                @elseif($currentUser->hasRole('stack-leader'))
                    My Stack Members
                @else
                    Team Members
                @endif
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                {{ $members->count() }} {{ Str::plural('member', $members->count()) }}
                @if($currentUser->team)
                    in <span class="font-medium text-indigo-600">{{ $currentUser->team->name }}</span>
                @endif
            </p>
        </div>
        @if($canManage)
            <a href="{{ route('users.create-team-member') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                <i class="fas fa-user-plus text-xs"></i>
                Add Member
            </a>
        @endif
    </div>

    <!-- Stats Strip -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-indigo-600 text-sm"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $members->count() }}</p>
                <p class="text-xs text-gray-500">Total</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-check-circle text-green-600 text-sm"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $activeCount }}</p>
                <p class="text-xs text-gray-500">Active</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-layer-group text-purple-600 text-sm"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $stackLeaderCount }}</p>
                <p class="text-xs text-gray-500">Stack Leaders</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user text-blue-600 text-sm"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900">{{ $teamMemberCount }}</p>
                <p class="text-xs text-gray-500">Members</p>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="memberSearch" placeholder="Search by name, email, position..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white">
            </div>
            <select id="roleFilter"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white">
                <option value="">All Roles</option>
                <option value="team-leader">Team Leader</option>
                <option value="stack-leader">Stack Leader</option>
                <option value="team-member">Team Member</option>
            </select>
            <select id="statusFilter"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <!-- Members Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="membersGrid">
        @forelse($members as $member)
            <div class="member-card bg-white rounded-xl border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all duration-200 overflow-hidden"
                 data-name="{{ strtolower($member->name) }}"
                 data-email="{{ strtolower($member->email) }}"
                 data-position="{{ strtolower($member->position ?? '') }}"
                 data-role="{{ $member->roles->first()?->name ?? '' }}"
                 data-status="{{ $member->is_active ? 'active' : 'inactive' }}">

                <!-- Card Top -->
                <div class="p-5">
                    <div class="flex items-start gap-4">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $member->name }}</h3>
                                @if($member->is_active)
                                    <span class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0" title="Active"></span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-red-400 flex-shrink-0" title="Inactive"></span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 truncate mt-0.5">{{ $member->email }}</p>
                            @if($member->position)
                                <p class="text-xs font-medium text-indigo-600 mt-1">{{ $member->position }}</p>
                            @endif
                        </div>

                        <!-- Role Badge -->
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold flex-shrink-0 uppercase tracking-wider
                            @if($member->hasRole('admin')) bg-red-50 text-red-700
                            @elseif($member->hasRole('operation-manager')) bg-orange-50 text-orange-700
                            @elseif($member->hasRole('project-manager')) bg-yellow-50 text-yellow-700
                            @elseif($member->hasRole('team-leader')) bg-blue-50 text-blue-700
                            @elseif($member->hasRole('stack-leader')) bg-purple-50 text-purple-700
                            @elseif($member->hasRole('team-member')) bg-green-50 text-green-700
                            @else bg-gray-50 text-gray-700
                            @endif">
                            {{ str_replace('-', ' ', $member->roles->first()?->name ?? 'No Role') }}
                        </span>
                    </div>

                    <!-- Details Row -->
                    <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                        @if($member->employee_id)
                            <span class="flex items-center gap-1">
                                <i class="fas fa-id-badge text-gray-400"></i>
                                {{ $member->employee_id }}
                            </span>
                        @endif
                        @if($member->phone)
                            <span class="flex items-center gap-1">
                                <i class="fas fa-phone text-gray-400"></i>
                                {{ $member->phone }}
                            </span>
                        @endif
                        @if($member->team)
                            <span class="flex items-center gap-1">
                                <i class="fas fa-users text-gray-400"></i>
                                {{ $member->team->name }}
                            </span>
                        @endif
                        @if($member->techStacks->isNotEmpty())
                            <span class="flex items-center gap-1">
                                <i class="fas fa-layer-group text-gray-400"></i>
                                {{ $member->techStacks->pluck('name')->join(', ') }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Card Footer -->
                @if(TeamMemberService::canManageUser($currentUser, $member) && $member->id !== $currentUser->id)
                    <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-2">
                        <button onclick="openEditModal({{ $member->id }})"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-colors">
                            <i class="fas fa-pen text-[10px]"></i>
                            Edit
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <i class="fas fa-users text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900">No team members found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by adding a new member to your team.</p>
                    @if($canManage)
                        <a href="{{ route('users.create-team-member') }}"
                           class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-user-plus text-xs"></i>
                            Add Member
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <!-- No Results Message (hidden by default) -->
    <div id="noResults" class="hidden">
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center mt-4">
            <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-4">
                <i class="fas fa-search text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-sm font-medium text-gray-900">No members match your filters</h3>
            <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
        </div>
    </div>
</div>

<!-- Single Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div onclick="closeEditModal()" class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-user-edit text-indigo-600 text-sm"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit Member</h3>
                </div>
                <button onclick="closeEditModal()" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="editForm" method="POST">
                @csrf
                @method('PATCH')

                <div class="px-6 py-5 space-y-4 max-h-[60vh] overflow-y-auto">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="name" id="editName" required
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" id="editEmail" required
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" name="phone" id="editPhone"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                                   placeholder="Optional">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                            <input type="text" name="position" id="editPosition"
                                   class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                                   placeholder="e.g. Senior Developer">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <input type="text" name="department" id="editDepartment"
                               class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                               placeholder="e.g. Engineering">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea name="address" id="editAddress" rows="2"
                                  class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 resize-none"
                                  placeholder="Optional"></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <i class="fas fa-save mr-1.5 text-xs"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('memberSearch');
        const roleFilter = document.getElementById('roleFilter');
        const statusFilter = document.getElementById('statusFilter');
        const noResults = document.getElementById('noResults');
        const membersGrid = document.getElementById('membersGrid');

        function filterMembers() {
            const search = searchInput.value.toLowerCase().trim();
            const role = roleFilter.value;
            const status = statusFilter.value;

            const cards = document.querySelectorAll('.member-card');
            let visibleCount = 0;

            cards.forEach(function(card) {
                const name = card.dataset.name;
                const email = card.dataset.email;
                const position = card.dataset.position;
                const cardRole = card.dataset.role;
                const cardStatus = card.dataset.status;

                let matchesSearch = !search || name.includes(search) || email.includes(search) || position.includes(search);
                let matchesRole = !role || cardRole === role;
                let matchesStatus = !status || cardStatus === status;

                if (matchesSearch && matchesRole && matchesStatus) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            if (visibleCount === 0 && cards.length > 0) {
                noResults.classList.remove('hidden');
            } else {
                noResults.classList.add('hidden');
            }
        }

        searchInput.addEventListener('input', filterMembers);
        roleFilter.addEventListener('change', filterMembers);
        statusFilter.addEventListener('change', filterMembers);
    });

    const memberData = @json($membersJson);

    function openEditModal(memberId) {
        const member = memberData[memberId];
        if (!member) return;

        document.getElementById('editForm').action = '/team-members/' + memberId;
        document.getElementById('editName').value = member.name || '';
        document.getElementById('editEmail').value = member.email || '';
        document.getElementById('editPhone').value = member.phone || '';
        document.getElementById('editPosition').value = member.position || '';
        document.getElementById('editDepartment').value = member.department || '';
        document.getElementById('editAddress').value = member.address || '';

        document.getElementById('editModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeEditModal();
    });
</script>
@endpush
