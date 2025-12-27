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
        <h1 class="text-3xl font-bold text-gray-900 mt-4">{{ $techStack->name }}</h1>
        <p class="text-gray-600 mt-2">{{ $techStack->description ?? 'No description available' }}</p>
    </div>

    <!-- Tech Stack Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Tech Stack Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Stack Leader -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Stack Leader</h2>
                @if($techStack->stackLeader)
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            <img class="h-12 w-12 rounded-full"
                                 src="https://ui-avatars.com/api/?name={{ $techStack->stackLeader->name }}&background=8B5CF6&color=fff"
                                 alt="{{ $techStack->stackLeader->name }}">
                        </div>
                        <div class="ml-4">
                            <div class="text-lg font-medium text-gray-900">{{ $techStack->stackLeader->name }}</div>
                            <div class="text-sm text-gray-500">{{ $techStack->stackLeader->employee_id }}</div>
                            <div class="text-sm text-gray-500">{{ $techStack->stackLeader->position ?? 'No position' }}</div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">No stack leader assigned</p>
                @endif
            </div>

            <!-- Stack Members -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Stack Members</h2>
                    @can('manage tech stacks')
                        <button onclick="showAddMemberModal()"
                                class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-md">
                            <i class="fas fa-plus mr-1"></i> Add Member
                        </button>
                    @endcan
                </div>

                @if($techStack->members->count() > 0)
                    <div class="space-y-3">
                        @foreach($techStack->members as $member)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full"
                                             src="https://ui-avatars.com/api/?name={{ $member->name }}&background=10B981&color=fff"
                                             alt="{{ $member->name }}">
                                    </div>
                                    <div class="ml-3">
                                        <div class="font-medium text-gray-900">{{ $member->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $member->employee_id }} • {{ $member->position ?? 'No position' }}</div>
                                    </div>
                                </div>
                                @can('manage tech stacks')
                                    <button type="button"
                                            onclick="removeMember({{ $techStack->id }}, {{ $member->id }}, '{{ $member->name }}')"
                                            class="text-red-600 hover:text-red-800 text-sm">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endcan
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No tech stack members assigned</p>
                @endif
            </div>

            <!-- Projects -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Associated Projects</h2>
                @if($techStack->projects->count() > 0)
                    <div class="space-y-3">
                        @foreach($techStack->projects as $project)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $project->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $project->status }}</div>
                                </div>
                                <a href="{{ route('projects.show', $project->id) }}"
                                   class="text-indigo-600 hover:text-indigo-800 text-sm">
                                    View
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No projects assigned to this tech stack</p>
                @endif
            </div>
        </div>

        <!-- Tech Stack Stats -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Stack Statistics</h2>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Members</span>
                        <span class="font-semibold">{{ $techStack->members->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Active Projects</span>
                        <span class="font-semibold">{{ $techStack->projects->whereIn('status', ['planning', 'in-progress'])->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Completed Projects</span>
                        <span class="font-semibold">{{ $techStack->projects->where('status', 'completed')->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Stack Leader</span>
                        <span class="font-semibold">{{ $techStack->stackLeader ? $techStack->stackLeader->name : 'Not Assigned' }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @can('manage tech stacks')
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                <div class="space-y-3">
                    <a href="{{ route('tech-stacks.edit', $techStack->id) }}"
                       class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>Edit Tech Stack
                    </a>
                    <button onclick="deleteTechStack({{ $techStack->id }})"
                            class="block w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-medium transition-colors duration-200">
                        <i class="fas fa-trash mr-2"></i>Delete Tech Stack
                    </button>
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>

<!-- Add Member Modal -->
@can('manage tech stacks')
<div id="addMemberModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900">Add Tech Stack Members</h3>
            <form action="{{ route('tech-stacks.add-members', $techStack->id) }}" method="POST">
                @csrf
                <div class="mt-4">
                    <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-md">
                        @foreach(\App\Models\User::whereNotIn('id', $techStack->members->pluck('id'))->where('is_active', true)->get() as $user)
                            <label class="flex items-center p-3 hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="members[]" value="{{ $user->id }}"
                                       class="mr-3 text-indigo-600 focus:ring-indigo-500">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->employee_id }} • {{ $user->position ?? 'No position' }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" onclick="hideAddMemberModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md">
                        Add Members
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@push('scripts')
<script>
function showAddMemberModal() {
    document.getElementById('addMemberModal').classList.remove('hidden');
}

function hideAddMemberModal() {
    document.getElementById('addMemberModal').classList.add('hidden');
}

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
                        text: data.message || 'Failed to delete tech stack',
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
</script>
@endpush
@endsection