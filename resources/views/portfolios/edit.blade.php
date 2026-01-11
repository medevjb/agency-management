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
            <a href="{{ route('portfolios.show', $portfolio) }}" class="hover:text-indigo-600">{{ $portfolio->title }}</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-900">Edit</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Portfolio Project</h1>
        <p class="text-gray-600 mt-1">Update your portfolio project details</p>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Form (2/3 width) -->
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('portfolios.update', $portfolio) }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                @csrf
                @method('PATCH')

                <!-- Form Header -->
                <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                    <div class="flex items-center">
                        <div class="bg-white/20 p-3 rounded-lg mr-4">
                            <i class="fas fa-edit text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Edit Project</h2>
                            <p class="text-blue-100 text-sm">Make changes to your portfolio project</p>
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
                        <input type="text" name="title" id="title" value="{{ old('title', $portfolio->title) }}" required
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
                                  placeholder="Describe your project, technologies used, challenges faced, and outcomes...">{{ old('description', $portfolio->description) }}</textarea>
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
                            <div class="relative">
                                <select name="tech_stack_id" id="tech_stack_id"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white appearance-none transition-all">
                                    <option value="">Select Tech Stack</option>
                                    @foreach($techStacks as $stack)
                                        <option value="{{ $stack->id }}" {{ old('tech_stack_id', $portfolio->tech_stack_id) == $stack->id ? 'selected' : '' }}>
                                            {{ $stack->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
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
                            <input type="text" name="project_date" id="project_date" value="{{ old('project_date', $portfolio->project_date?->format('Y-m-d')) }}"
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
                                    <input type="url" name="project_url" id="project_url" value="{{ old('project_url', $portfolio->project_url) }}"
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
                                    <input type="url" name="github_url" id="github_url" value="{{ old('github_url', $portfolio->github_url) }}"
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
                        <label class="block text-sm font-bold text-gray-700 mb-3">
                            <i class="fas fa-image mr-1 text-indigo-600"></i>
                            Project Screenshot
                        </label>

                        @if($portfolio->screenshot)
                            <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <p class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-image mr-2 text-indigo-500"></i>Current Screenshot
                                </p>
                                <img src="{{ asset('storage/' . $portfolio->screenshot) }}" alt="Current screenshot" class="h-48 rounded-lg border border-gray-300 shadow-sm">
                            </div>
                        @endif

                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:bg-gray-50 transition-colors">
                            <i class="fas fa-cloud-upload-alt text-indigo-500 text-5xl mb-3"></i>
                            <div class="flex flex-col items-center">
                                <label for="screenshot" class="cursor-pointer">
                                    <span class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                                        Click to upload new
                                    </span>
                                    <span class="text-sm text-gray-500"> or drag and drop</span>
                                </label>
                                <input id="screenshot" name="screenshot" type="file" accept="image/*" class="sr-only">
                                <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF, WebP up to 2MB</p>
                                <p class="text-xs text-gray-400 mt-1">Leave empty to keep current screenshot</p>
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
                                <option value="draft" {{ old('status', $portfolio->status) == 'draft' ? 'selected' : '' }}>Draft - Only visible to you</option>
                                <option value="published" {{ old('status', $portfolio->status) == 'published' ? 'selected' : '' }}>Published - Visible to everyone</option>
                                <option value="archived" {{ old('status', $portfolio->status) == 'archived' ? 'selected' : '' }}>Archived - Hidden from listings</option>
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

                    <!-- Featured -->
                    @can('feature portfolio')
                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 p-5 rounded-lg border border-yellow-200">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $portfolio->is_featured) ? 'checked' : '' }}
                                   class="w-5 h-5 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500">
                            <div class="ml-3">
                                <span class="font-semibold text-gray-900 flex items-center">
                                    <i class="fas fa-star text-yellow-500 mr-2"></i>
                                    Mark as Featured
                                </span>
                                <p class="text-sm text-gray-600 mt-1">Featured projects are highlighted on the portfolio page</p>
                            </div>
                        </label>
                        @error('is_featured')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>
                    @endcan
                </div>

                <!-- Form Actions -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-end space-x-3">
                    <a href="{{ route('portfolios.show', $portfolio) }}"
                       class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-all flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105 flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Update Project
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
                        <i class="fas fa-lightbulb mr-2"></i>Edit Tips
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-sync text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Keep It Updated</p>
                            <p class="text-xs text-gray-600 mt-1">Regularly update your project with new features</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-image text-purple-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Refresh Screenshots</p>
                            <p class="text-xs text-gray-600 mt-1">Update images to show latest UI changes</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-link text-green-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Check Links</p>
                            <p class="text-xs text-gray-600 mt-1">Ensure all URLs are still working properly</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-chart-line text-orange-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Track Progress</p>
                            <p class="text-xs text-gray-600 mt-1">Update description with new achievements</p>
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
                    <a href="{{ route('portfolios.show', $portfolio) }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-eye text-gray-400 w-5"></i>
                        <span class="ml-3 text-sm font-medium text-gray-700">View Project</span>
                    </a>
                    <a href="{{ route('portfolios.my') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-list text-gray-400 w-5"></i>
                        <span class="ml-3 text-sm font-medium text-gray-700">My Portfolios</span>
                    </a>
                    <a href="{{ route('portfolios.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-th text-gray-400 w-5"></i>
                        <span class="ml-3 text-sm font-medium text-gray-700">Browse All</span>
                    </a>
                    @if(auth()->user()->can('manage portfolios'))
                    <a href="{{ route('dashboard') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chart-line text-gray-400 w-5"></i>
                        <span class="ml-3 text-sm font-medium text-gray-700">Dashboard</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Project Info Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-bold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-indigo-500"></i>Project Info
                    </h3>
                </div>
                <div class="p-4 space-y-3">
                    <div class="flex items-center text-sm">
                        <i class="fas fa-calendar-plus text-gray-400 w-5"></i>
                        <span class="ml-2 text-gray-600">Created: {{ $portfolio->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-clock text-gray-400 w-5"></i>
                        <span class="ml-2 text-gray-600">Updated: {{ $portfolio->updated_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-eye text-gray-400 w-5"></i>
                        <span class="ml-2 text-gray-600">Status: <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full
                            {{ $portfolio->status === 'published' ? 'bg-green-100 text-green-800' :
                               ($portfolio->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($portfolio->status) }}
                        </span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
