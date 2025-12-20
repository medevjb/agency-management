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
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6">
            <form method="POST" action="{{ route('tech-stacks.update', $techStack->id) }}">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Tech Stack Information -->
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Tech Stack Information</h2>

                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Tech Stack Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="name" name="name" required
                                           value="{{ old('name', $techStack->name) }}"
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                           placeholder="Enter tech stack name">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                        Description
                                    </label>
                                    <textarea id="description" name="description" rows="4"
                                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                              placeholder="Enter tech stack description">{{ old('description', $techStack->description) }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stack Leadership & Members -->
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Stack Leadership</h2>

                            <div class="space-y-4">
                                <div>
                                    <label for="stack_leader_id" class="block text-sm font-medium text-gray-700 mb-1">
                                        Stack Leader
                                    </label>
                                    <select id="stack_leader_id" name="stack_leader_id"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <option value="">Select a stack leader</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('stack_leader_id', $techStack->stack_leader_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->employee_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('stack_leader_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 mb-4">Stack Members</h2>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Select Stack Members
                                    </label>
                                    <div class="max-h-60 overflow-y-auto border border-gray-300 rounded-md">
                                        @foreach($users as $user)
                                            <label class="flex items-center p-3 hover:bg-gray-50 cursor-pointer">
                                                <input type="checkbox" name="members[]" value="{{ $user->id }}"
                                                       {{ in_array($user->id, old('members', $techStack->members->pluck('id')->toArray())) ? 'checked' : '' }}
                                                       class="mr-3 text-indigo-600 focus:ring-indigo-500">
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $user->employee_id }} • {{ $user->position ?? 'No position' }}</div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('members')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('tech-stacks.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-medium transition-colors duration-200">
                        Update Tech Stack
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection