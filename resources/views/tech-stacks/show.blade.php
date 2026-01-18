@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('tech-stacks.index') }}" class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Tech Stacks
                    </a>
                </li>
            </ol>
        </nav>
        <div class="flex items-start justify-between mt-4">
            <div>
                <div class="flex items-center">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $techStack->name }}</h1>
                    <span class="ml-4 px-3 py-1 text-xs font-bold rounded-full bg-indigo-100 text-indigo-800">
                        Tech Stack
                    </span>
                </div>
                <p class="text-gray-600 mt-2">{{ $techStack->description ?: 'No description provided' }}</p>
            </div>
            @canany(['update', 'delete'], $techStack)
            <div class="flex space-x-3">
                <a href="{{ route('tech-stacks.edit', $techStack->id) }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <button onclick="deleteTechStack({{ $techStack->id }})"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            </div>
            @endcan
        </div>
    </div>

    <!-- Tech Stack Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Stack Leader Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-crown text-white"></i>
                        </div>
                        Stack Leader
                    </h2>
                    @if($techStack->stackLeader)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-xl">
                            <div class="flex items-center">
                                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center mr-4 shadow-md">
                                    <span class="text-white font-bold text-xl">{{ $techStack->stackLeader->name->charAt(0) }}</span>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-lg">{{ $techStack->stackLeader->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $techStack->stackLeader->employee_id }}</p>
                                    <p class="text-sm text-purple-700 font-medium mt-1">
                                        <i class="fas fa-briefcase mr-1"></i>{{ $techStack->stackLeader->position ?: 'Team Leader' }}
                                    </p>
                                </div>
                            </div>
                            <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-tie text-white"></i>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-center p-8 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl">
                            <div class="text-center">
                                <i class="fas fa-user-slash text-gray-400 text-4xl mb-3"></i>
                                <p class="text-gray-500">No stack leader assigned</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stack Members Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            Stack Members
                            <span class="ml-3 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $techStack->members->count() }}
                            </span>
                        </h2>
                        @can('addMembers', $techStack)
                            <button onclick="showAddMemberModal()"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                                <i class="fas fa-plus mr-2"></i>Add Member
                            </button>
                        @endcan
                    </div>

                    @if($techStack->members->count() > 0)
                        <div class="grid grid-cols-1 gap-3">
                            @foreach($techStack->members as $member)
                                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-cyan-50 border border-blue-200 rounded-xl hover:shadow-md transition-all">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center mr-4 shadow-md">
                                            <span class="text-white font-bold">{{ $member->name->charAt(0) }}</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $member->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $member->employee_id }}</p>
                                            @if($member->position)
                                                <p class="text-xs text-blue-700 font-medium mt-0.5">
                                                    <i class="fas fa-briefcase mr-1"></i>{{ $member->position }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    @can('removeMember', $techStack)
                                        <button type="button"
                                                onclick="removeMember({{ $techStack->id }}, {{ $member->id }}, '{{ $member->name }}')"
                                                class="p-2 text-red-600 hover:text-red-800 hover:bg-red-100 rounded-lg transition-all"
                                                title="Remove member">
                                            <i class="fas fa-times-circle text-xl"></i>
                                        </button>
                                    @endcan
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex items-center justify-center p-8 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl">
                            <div class="text-center">
                                <i class="fas fa-users-slash text-gray-400 text-4xl mb-3"></i>
                                <p class="text-gray-500">No members assigned yet</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Orders/Projects Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-folder-open text-white"></i>
                        </div>
                        Associated Projects
                        <span class="ml-3 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $techStack->projects->count() }}
                        </span>
                    </h2>
                    @if($techStack->projects->count() > 0)
                        <div class="space-y-3">
                            @foreach($techStack->projects as $project)
                                <a href="{{ route('orders.show', $project->id) }}"
                                   class="block flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl hover:shadow-md transition-all">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center mr-4 shadow-md">
                                            <i class="fas fa-laptop-code text-white"></i>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $project->name }}</p>
                                            <p class="text-sm text-gray-600">
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full
                                                    {{ $project->status === 'completed' ? 'bg-green-100 text-green-800' :
                                                       ($project->status === 'in-progress' ? 'bg-blue-100 text-blue-800' :
                                                       ($project->status === 'planning' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                                    {{ ucfirst($project->status) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-green-700 hover:text-green-800">
                                        <span class="mr-2 text-sm font-medium">View Details</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="flex items-center justify-center p-8 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl">
                            <div class="text-center">
                                <i class="fas fa-folder-open text-gray-400 text-4xl mb-3"></i>
                                <p class="text-gray-500">No projects assigned yet</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Stats -->
        <div class="space-y-6">
            <!-- Stats Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-chart-bar mr-2"></i>Statistics
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Total Members -->
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <span class="font-medium text-gray-700">Total Members</span>
                        </div>
                        <span class="text-2xl font-bold text-blue-700">{{ $techStack->members->count() }}</span>
                    </div>

                    <!-- Active Projects -->
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-rocket text-white"></i>
                            </div>
                            <span class="font-medium text-gray-700">Active Projects</span>
                        </div>
                        <span class="text-2xl font-bold text-green-700">{{ $techStack->projects->whereIn('status', ['planning', 'in-progress'])->count() }}</span>
                    </div>

                    <!-- Completed Projects -->
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                            <span class="font-medium text-gray-700">Completed</span>
                        </div>
                        <span class="text-2xl font-bold text-purple-700">{{ $techStack->projects->where('status', 'completed')->count() }}</span>
                    </div>

                    <!-- Stack Leader -->
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-crown text-white"></i>
                            </div>
                            <span class="font-medium text-gray-700">Leader</span>
                        </div>
                        @if($techStack->stackLeader)
                            <div class="text-right">
                                <p class="font-bold text-indigo-700 text-sm">{{ $techStack->stackLeader->name }}</p>
                                <p class="text-xs text-gray-600">{{ $techStack->stackLeader->employee_id }}</p>
                            </div>
                        @else
                            <span class="text-sm font-medium text-gray-500">Not Assigned</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Created Info -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center text-sm">
                            <i class="fas fa-calendar-plus text-gray-400 w-5"></i>
                            <span class="ml-2 text-gray-600">Created: {{ $techStack->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-clock text-gray-400 w-5"></i>
                            <span class="ml-2 text-gray-600">Updated: {{ $techStack->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
@can('addMembers', $techStack)
<div id="addMemberModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative mx-auto p-6 border-0 shadow-xl rounded-2xl bg-white w-full max-w-lg mx-4">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900 flex items-center">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-user-plus text-white"></i>
                </div>
                Add Tech Stack Members
            </h3>
            <button onclick="hideAddMemberModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Search -->
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-search mr-2 text-blue-500"></i>Search Members
            </label>
            <div class="relative">
                <input type="text" id="memberSearchInput"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                       placeholder="Search by name or employee ID..."
                       autocomplete="off">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>

        <!-- Member List -->
        <form action="{{ route('tech-stacks.add-members', $techStack->id) }}" method="POST" id="addMemberForm">
            @csrf
            <div class="max-h-80 overflow-y-auto border border-gray-200 rounded-xl p-2">
                <div id="memberList">
                    @php
                        $availableUsers = \App\Models\User::whereNotIn('id', $techStack->members->pluck('id'))
                            ->where('is_active', true)
                            ->get();
                    @endphp
                    @foreach($availableUsers as $user)
                        <label class="member-item flex items-center p-3 hover:bg-gray-50 cursor-pointer rounded-lg transition-colors" data-name="{{ $user->name }}" data-employee-id="{{ $user->employee_id }}">
                            <input type="checkbox" name="members[]" value="{{ $user->id }}"
                                   class="mr-3 w-4 h-4 text-blue-600 focus:ring-blue-500 rounded">
                            <div class="flex items-center flex-1">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white font-semibold text-sm">{{ $user->name->charAt(0) }}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->employee_id }} @if($user->position) • {{ $user->position }} @endif</div>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                <div id="noMembersFound" class="hidden p-8 text-center text-gray-500">
                    <i class="fas fa-user-slash text-4xl mb-3"></i>
                    <p>No members found</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <button type="button" onclick="hideAddMemberModal()"
                        class="px-6 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 font-semibold transition-all">
                    <i class="fas fa-times mr-2"></i>Cancel
                </button>
                <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-xl font-semibold transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-plus mr-2"></i>Add Members
                </button>
            </div>
        </form>
    </div>
</div>
@endcan

@push('scripts')
<script>
// Member Search in Modal
const memberSearchInput = document.getElementById('memberSearchInput');
const memberList = document.getElementById('memberList');
const noMembersFound = document.getElementById('noMembersFound');

if (memberSearchInput) {
    memberSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const memberItems = memberList.querySelectorAll('.member-item');
        let visibleCount = 0;

        memberItems.forEach(item => {
            const name = item.getAttribute('data-name').toLowerCase();
            const employeeId = item.getAttribute('data-employee-id').toLowerCase();

            if (name.includes(searchTerm) || employeeId.includes(searchTerm)) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        if (visibleCount === 0) {
            noMembersFound.classList.remove('hidden');
        } else {
            noMembersFound.classList.add('hidden');
        }
    });
}

function showAddMemberModal() {
    document.getElementById('addMemberModal').classList.remove('hidden');
    document.getElementById('memberSearchInput').value = '';
    const memberItems = memberList.querySelectorAll('.member-item');
    memberItems.forEach(item => item.classList.remove('hidden'));
    noMembersFound.classList.add('hidden');
}

function hideAddMemberModal() {
    document.getElementById('addMemberModal').classList.add('hidden');
}

// Delete Tech Stack
function deleteTechStack(techStackId) {
    Swal.fire({
        title: 'Delete Tech Stack?',
        text: "Are you sure you want to delete this tech stack? This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        showLoaderOnConfirm: true
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/tech-stacks/${techStackId}`)
            .then(response => {
                if (response.data.success) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: response.data.message || 'Tech stack has been deleted successfully.',
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '{{ route('tech-stacks.index') }}';
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.data.message || 'Failed to delete tech stack',
                        icon: 'error',
                        confirmButtonColor: '#3B82F6'
                    });
                }
            })
            .catch(error => {
                console.error('Delete tech stack error:', error);
                const errorMessage = error.response?.data?.message ||
                                     error.response?.data?.error ||
                                     error.message ||
                                     'An error occurred while deleting the tech stack';
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonColor: '#3B82F6'
                });
            });
        }
    });
}

function removeMember(techStackId, memberId, memberName) {
    Swal.fire({
        title: 'Remove Member?',
        text: `Are you sure you want to remove ${memberName} from this tech stack?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Yes, remove!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/tech-stacks/${techStackId}/members/${memberId}`, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (response.data.success) {
                    Swal.fire({
                        title: 'Removed!',
                        text: response.data.message || 'Member has been removed successfully.',
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.data.message || 'Failed to remove member',
                        icon: 'error',
                        confirmButtonColor: '#3B82F6'
                    });
                }
            })
            .catch(error => {
                console.error('Remove member error:', error);
                const errorMessage = error.response?.data?.message ||
                                     error.response?.data?.error ||
                                     error.message ||
                                     'An error occurred while removing the member';
                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonColor: '#3B82F6'
                });
            });
        }
    });
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideAddMemberModal();
    }
});
</script>
@endpush
@endsection
