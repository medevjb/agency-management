@extends('layouts.dashboard')

@section('content')
<div class="pt-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('portfolios.my') }}" class="hover:text-indigo-600 flex items-center">
                <i class="fas fa-arrow-left mr-1"></i>
                My Portfolios
            </a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900">Add New Project</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Add Portfolio Project</h1>
        <p class="text-gray-600 mt-1">Showcase your completed project to the team</p>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Form (2/3 width) -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('portfolios.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                @csrf

                <!-- Form Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <div class="flex items-center">
                        <div class="bg-white/20 p-3 rounded-lg mr-4">
                            <i class="fas fa-laptop-code text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Project Information</h2>
                            <p class="text-indigo-100 text-sm">Fill in the details about your completed project</p>
                        </div>
                    </div>
                </div>

                <!-- Form Body -->
                <div class="p-6 space-y-6">
                    <!-- Title -->
                    <div class="bg-gradient-to-r from-gray-50 to-indigo-50 p-5 rounded-lg border border-gray-200">
                        <label for="title" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-heading mr-1 text-indigo-600"></i>
                            Project Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white transition-all text-lg font-semibold"
                               placeholder="e.g., E-commerce Platform">
                        @error('title')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="bg-white p-5 rounded-lg border-2 border-gray-200">
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-align-left mr-1 text-indigo-600"></i>
                            Project Description
                        </label>
                        <textarea name="description" id="description" rows="5"
                                  class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white transition-all"
                                  placeholder="Describe your project, technologies used, challenges faced, and outcomes...">{{ old('description') }}</textarea>
                        <p class="mt-2 text-sm text-gray-500 flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            Briefly describe your project and its key features (max 2000 characters)
                        </p>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Two Column Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tech Stack -->
                        <div class="bg-white p-5 rounded-lg border-2 border-gray-200">
                            <label for="tech_stack_id" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-layer-group mr-1 text-indigo-600"></i>
                                Tech Stack
                            </label>
                            <x-ajax-select name="tech_stack_id"
                                           url="{{ route('search.tech-stacks') }}"
                                           placeholder="Search tech stack..." />
                            @error('tech_stack_id')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Project Date -->
                        <div class="bg-white p-5 rounded-lg border-2 border-gray-200">
                            <label for="project_date" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-1 text-indigo-600"></i>
                                Completion Date
                            </label>
                            <input type="text" name="project_date" id="project_date" value="{{ old('project_date') }}"
                                   class="datepicker w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white transition-all cursor-pointer"
                                   placeholder="Select completion date..."
                                   readonly>
                            @error('project_date')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- URLs Section -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-5 rounded-lg border border-blue-200">
                        <h3 class="text-sm font-bold text-gray-700 mb-4">
                            <i class="fas fa-link mr-1 text-blue-600"></i>
                            Project Links
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Live URL -->
                            <div>
                                <label for="project_url" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-globe mr-1 text-green-600"></i>
                                    Live Project URL
                                </label>
                                <div class="relative">
                                    <input type="url" name="project_url" id="project_url" value="{{ old('project_url') }}"
                                           class="w-full px-4 py-3 pl-10 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white transition-all"
                                           placeholder="https://example.com">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <i class="fas fa-globe text-gray-400"></i>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Link to your live project</p>
                                @error('project_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- GitHub URL -->
                            <div>
                                <label for="github_url" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fab fa-github mr-1 text-gray-700"></i>
                                    GitHub Repository URL
                                </label>
                                <div class="relative">
                                    <input type="url" name="github_url" id="github_url" value="{{ old('github_url') }}"
                                           class="w-full px-4 py-3 pl-10 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white transition-all"
                                           placeholder="https://github.com/username/repo">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <i class="fab fa-github text-gray-400"></i>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Link to your source code</p>
                                @error('github_url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Screenshot Upload -->
                    <div class="bg-white p-5 rounded-lg border-2 border-dashed border-indigo-300 hover:border-indigo-500 transition-colors">
                        <label for="screenshot" class="block text-sm font-bold text-gray-700 mb-3">
                            <i class="fas fa-image mr-1 text-indigo-600"></i>
                            Project Screenshot
                        </label>
                        <div class="flex items-center justify-center">
                            <div class="w-full">
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-cloud-upload-alt text-indigo-500 text-5xl mb-3"></i>
                                    <div class="flex flex-col items-center">
                                        <label for="screenshot" class="cursor-pointer">
                                            <span class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                                                Click to upload
                                            </span>
                                            <span class="text-sm text-gray-500"> or drag and drop</span>
                                        </label>
                                        <input id="screenshot" name="screenshot" type="file" accept="image/*" class="sr-only">
                                        <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF, WebP up to 2MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('screenshot')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="bg-white p-5 rounded-lg border-2 border-gray-200">
                        <label for="status" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-toggle-on mr-1 text-indigo-600"></i>
                            Publication Status
                        </label>
                        <div class="relative">
                            <select name="status" id="status"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white appearance-none transition-all">
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft - Only visible to you</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published - Visible to everyone</option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived - Hidden from listings</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500 flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            Draft: Only you can see it. Published: Visible to everyone.
                        </p>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-end space-x-3">
                    <a href="{{ route('portfolios.my') }}"
                       class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105 flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Save Project
                    </button>
                </div>
            </form>
        </div>

        <!-- Right Column - Tips & Guidelines (1/3 width) -->
        <div class="space-y-6">
            <!-- Tips Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-lightbulb mr-2"></i>Tips for Great Portfolios
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-check text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Use Clear Titles</p>
                            <p class="text-xs text-gray-600 mt-1">Make your project name descriptive and memorable</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-image text-purple-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Add Screenshots</p>
                            <p class="text-xs text-gray-600 mt-1">Visuals make your portfolio more engaging</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-link text-green-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Include Live Links</p>
                            <p class="text-xs text-gray-600 mt-1">Let others see your work in action</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-code text-orange-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Share Code</p>
                            <p class="text-xs text-gray-600 mt-1">GitHub links show your coding practices</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-star text-pink-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Highlight Achievements</p>
                            <p class="text-xs text-gray-600 mt-1">Mention challenges and how you solved them</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Card -->
            @php
                $userPortfolioCount = auth()->user()->portfolios()->count();
                $publishedCount = auth()->user()->portfolios()->where('status', 'published')->count();
            @endphp
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h3 class="text-lg font-bold text-white flex items-center">
                        <i class="fas fa-chart-pie mr-2"></i>Your Stats
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg border border-blue-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-folder text-white text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-700 text-sm">Total Projects</span>
                        </div>
                        <span class="text-xl font-bold text-blue-700">{{ $userPortfolioCount }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-globe text-white text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-700 text-sm">Published</span>
                        </div>
                        <span class="text-xl font-bold text-green-700">{{ $publishedCount }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg border border-yellow-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-edit text-white text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-700 text-sm">Drafts</span>
                        </div>
                        <span class="text-xl font-bold text-yellow-700">{{ $userPortfolioCount - $publishedCount }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Links Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-bold text-gray-900 flex items-center">
                        <i class="fas fa-bolt mr-2 text-yellow-500"></i>Quick Links
                    </h3>
                </div>
                <div class="p-4 space-y-2">
                    <a href="{{ route('portfolios.my') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-list text-gray-400 w-5"></i>
                        <span class="ml-3 text-sm font-medium text-gray-700">View My Portfolios</span>
                    </a>
                    <a href="{{ route('portfolios.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-th text-gray-400 w-5"></i>
                        <span class="ml-3 text-sm font-medium text-gray-700">Browse All Portfolios</span>
                    </a>
                    @if(auth()->user()->can('manage portfolios'))
                    <a href="{{ route('dashboard') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chart-line text-gray-400 w-5"></i>
                        <span class="ml-3 text-sm font-medium text-gray-700">Dashboard</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
