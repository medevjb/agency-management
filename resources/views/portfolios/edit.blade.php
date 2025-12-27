@extends('layouts.dashboard')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('portfolios.my') }}" class="hover:text-indigo-600">My Portfolios</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a href="{{ route('portfolios.show', $portfolio) }}" class="hover:text-indigo-600">{{ $portfolio->title }}</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900">Edit</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Edit Portfolio Project</h1>
        <p class="text-gray-600 mt-1">Update your portfolio project details</p>
    </div>

    <!-- Form -->
    <div class="max-w-4xl">
        <form method="POST" action="{{ route('portfolios.update', $portfolio) }}" enctype="multipart/form-data" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Project Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $portfolio->title) }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="e.g., E-commerce Platform">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="Describe your project, technologies used, challenges faced, and outcomes...">{{ old('description', $portfolio->description) }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Briefly describe your project and its key features (max 2000 characters)</p>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tech Stack & Date -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="tech_stack_id" class="block text-sm font-medium text-gray-700 mb-2">Tech Stack</label>
                    <select name="tech_stack_id" id="tech_stack_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Tech Stack</option>
                        @foreach($techStacks as $stack)
                            <option value="{{ $stack->id }}" {{ old('tech_stack_id', $portfolio->tech_stack_id) == $stack->id ? 'selected' : '' }}>
                                {{ $stack->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('tech_stack_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="project_date" class="block text-sm font-medium text-gray-700 mb-2">Project Completion Date</label>
                    <input type="date" name="project_date" id="project_date" value="{{ old('project_date', $portfolio->project_date?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('project_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- URLs -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="project_url" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-link mr-1"></i> Live Project URL
                    </label>
                    <input type="url" name="project_url" id="project_url" value="{{ old('project_url', $portfolio->project_url) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="https://example.com">
                    <p class="mt-1 text-sm text-gray-500">Link to your live project</p>
                    @error('project_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="github_url" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fab fa-github mr-1"></i> GitHub Repository URL
                    </label>
                    <input type="url" name="github_url" id="github_url" value="{{ old('github_url', $portfolio->github_url) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="https://github.com/username/repo">
                    <p class="mt-1 text-sm text-gray-500">Link to your source code</p>
                    @error('github_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Current Screenshot & Upload New -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Project Screenshot</label>
                @if($portfolio->screenshot)
                    <div class="mb-3">
                        <p class="text-sm text-gray-600 mb-2">Current Screenshot:</p>
                        <img src="{{ asset('storage/' . $portfolio->screenshot) }}" alt="Current screenshot" class="h-48 rounded-lg border border-gray-200">
                    </div>
                @endif
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50">
                    <div class="space-y-1 text-center">
                        <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-2"></i>
                        <div class="flex text-sm text-gray-600">
                            <label for="screenshot" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none">
                                <span>Upload a new file</span>
                                <input id="screenshot" name="screenshot" type="file" accept="image/*" class="sr-only">
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF, WebP up to 2MB</p>
                    </div>
                </div>
                @error('screenshot')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" id="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="draft" {{ old('status', $portfolio->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $portfolio->status) == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ old('status', $portfolio->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                <p class="mt-1 text-sm text-gray-500">Draft: Only you can see it. Published: Visible to everyone.</p>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Featured -->
            @can('feature portfolio')
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $portfolio->is_featured) ? 'checked' : '' }}
                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">
                        <i class="fas fa-star text-yellow-500 mr-1"></i> Mark as Featured
                    </span>
                </label>
                <p class="mt-1 text-sm text-gray-500 ml-6">Featured projects are highlighted on the portfolio page</p>
            </div>
            @endcan

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200">
                <a href="{{ route('portfolios.show', $portfolio) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-save mr-2"></i> Update Project
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
