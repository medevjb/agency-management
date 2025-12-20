@extends('layouts.dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
                <li>
                    <a href="{{ route('teams.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Teams
                    </a>
                </li>
            </ol>
        </nav>
        <h1 class="text-3xl font-bold text-gray-900 mt-4">{{ $team->name }}</h1>
        <p class="text-gray-600 mt-2">{{ $team->description ?? 'No description available' }}</p>
    </div>

    <!-- Team Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Team Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Team Leader -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Team Leader</h2>
                @if($team->teamLeader)
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12">
                            <img class="h-12 w-12 rounded-full"
                                 src="https://ui-avatars.com/api/?name={{ $team->teamLeader->name }}&background=4F46E5&color=fff"
                                 alt="{{ $team->teamLeader->name }}">
                        </div>
                        <div class="ml-4">
                            <div class="text-lg font-medium text-gray-900">{{ $team->teamLeader->name }}</div>
                            <div class="text-sm text-gray-500">{{ $team->teamLeader->employee_id }}</div>
                            <div class="text-sm text-gray-500">{{ $team->teamLeader->position ?? 'No position' }}</div>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">No team leader assigned</p>
                @endif
            </div>

            <!-- Team Members -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Team Members</h2>
                    @can('manage team members')
                        <button onclick="showAddMemberModal()"
                                class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm rounded-md">
                            <i class="fas fa-plus mr-1"></i> Add Member
                        </button>
                    @endcan
                </div>

                @if($team->members->count() > 0)
                    <div class="space-y-3">
                        @foreach($team->members as $member)
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
                                @can('manage team members')
                                    <form action="{{ route('teams.remove-member', [$team->id, $member->id]) }}"
                                          method="POST" onsubmit="return confirm('Remove {{ $member->name }} from team?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-600 hover:text-red-800 text-sm">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No team members assigned</p>
                @endif
            </div>

            <!-- Projects -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Associated Projects</h2>
                @if($team->projects->count() > 0)
                    <div class="space-y-3">
                        @foreach($team->projects as $project)
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
                    <p class="text-gray-500">No projects assigned to this team</p>
                @endif
            </div>
        </div>

        <!-- Team Stats -->
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Team Statistics</h2>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Members</span>
                        <span class="font-semibold">{{ $team->members->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Active Projects</span>
                        <span class="font-semibold">{{ $team->projects->whereIn('status', ['planning', 'in-progress'])->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Completed Projects</span>
                        <span class="font-semibold">{{ $team->projects->where('status', 'completed')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
@can('manage team members')
<div id="addMemberModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-semibold text-gray-900">Add Team Members</h3>
            <form action="{{ route('teams.add-members', $team->id) }}" method="POST">
                @csrf
                <div class="mt-4">
                    <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-md">
                        @foreach(\App\Models\User::whereNotIn('id', $team->members->pluck('id'))->where('is_active', true)->get() as $user)
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
</script>
@endpush
@endsection