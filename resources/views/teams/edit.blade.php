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
            <form method="POST" action="{{ route('teams.update', $team->id) }}">
                @csrf
                @method('PUT')
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
                                <div>
                                    <label for="team_leader_id" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-user-shield mr-2 text-green-500"></i>
                                        Team Leader
                                    </label>
                                    <select id="team_leader_id" name="team_leader_id"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm appearance-none transition-all duration-200">
                                        <option value="">Select a team leader</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('team_leader_id', $team->team_leader_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->employee_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                    @error('team_leader_id')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-users mr-2 text-green-500"></i>
                                        Team Members
                                    </label>
                                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 max-h-64 overflow-y-auto">
                                        @foreach($users as $user)
                                            <label class="flex items-center p-3 hover:bg-white rounded-lg cursor-pointer transition-colors duration-200">
                                                <input type="checkbox" name="members[]" value="{{ $user->id }}"
                                                       {{ in_array($user->id, old('members', $team->members->pluck('id')->toArray())) ? 'checked' : '' }}
                                                       class="mr-3 w-4 h-4 text-indigo-600 bg-indigo-100 border-indigo-300 rounded focus:ring-2 focus:ring-indigo-500">
                                                <div class="flex-1">
                                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $user->employee_id }} • {{ $user->position ?? 'No position' }}</div>
                                                </div>
                                            </label>
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
                                @if($team->members->count() > 0)
                                <div class="mt-6">
                                    <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <i class="fas fa-users mr-2 text-blue-500"></i>
                                        Current Members ({{ $team->members->count() }})
                                    </label>
                                    <div class="space-y-2">
                                        @foreach($team->members as $member)
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

@push('styles')
<style>
    /* Custom styles for form styling */
    select {
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='none'%3e%3cpath stroke='%236B7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 8l5 5L5 3'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.5rem center;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }
</style>
@endpush
@endsection