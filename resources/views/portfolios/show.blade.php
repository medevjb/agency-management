@extends('layouts.dashboard')

@push('styles')
    <style>
        .gradient-overlay {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.9) 0%, rgba(139, 92, 246, 0.9) 100%);
        }
    </style>
@endpush

@section('content')
<div class="pt-4 sm:p-6 lg:p-8">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <a href="{{ route('portfolios.index') }}" class="hover:text-indigo-600">All Portfolios</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900">{{ $portfolio->title }}</span>
            </div>
            @if(auth()->user()->can('edit any portfolio') || (auth()->user()->can('edit own portfolio') && $portfolio->user_id === auth()->id()))
            <a href="{{ route('portfolios.edit', $portfolio) }}"
               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105">
                <i class="fas fa-edit mr-2"></i>
                Edit Project
            </a>
            @endif
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (Left Column) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Screenshot -->
            @if($portfolio->screenshot)
                <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                    <img src="{{ asset('storage/' . $portfolio->screenshot) }}" alt="{{ $portfolio->title }}" class="w-full">
                </div>
            @else
                <div class="gradient-overlay rounded-xl shadow-lg p-16 flex items-center justify-center">
                    <i class="fas fa-laptop-code text-white text-9xl opacity-80"></i>
                </div>
            @endif

            <!-- Description -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-indigo-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-align-left text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">About This Project</h2>
                        <p class="text-sm text-gray-500">Project details and description</p>
                    </div>
                </div>
                <div class="prose prose-indigo max-w-none">
                    <p class="text-gray-700 leading-relaxed text-lg">{{ $portfolio->description ?: 'No description provided for this project.' }}</p>
                </div>
            </div>

            <!-- Quick Links -->
            @if($portfolio->project_url || $portfolio->github_url)
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-link text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Quick Links</h2>
                        <p class="text-sm text-gray-500">Access project resources</p>
                    </div>
                </div>
                <div class="space-y-3">
                    @if($portfolio->project_url)
                    <a href="{{ $portfolio->project_url }}" target="_blank"
                       class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 rounded-xl hover:from-green-100 hover:to-emerald-100 transition-all group">
                        <div class="flex items-center">
                            <div class="bg-green-500 p-2 rounded-lg mr-4">
                                <i class="fas fa-external-link-alt text-white"></i>
                            </div>
                            <div>
                                <p class="font-semibold">Live Project</p>
                                <p class="text-sm text-green-600">View the deployed application</p>
                            </div>
                        </div>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    @endif
                    @if($portfolio->github_url)
                    <a href="{{ $portfolio->github_url }}" target="_blank"
                       class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-slate-50 text-gray-700 rounded-xl hover:from-gray-100 hover:to-slate-100 transition-all group">
                        <div class="flex items-center">
                            <div class="bg-gray-800 p-2 rounded-lg mr-4">
                                <i class="fab fa-github text-white"></i>
                            </div>
                            <div>
                                <p class="font-semibold">Source Code</p>
                                <p class="text-sm text-gray-600">View on GitHub</p>
                            </div>
                        </div>
                        <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar (Right Column) -->
        <div class="space-y-6">
            <!-- Project Info Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white">Project Information</h2>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Title -->
                    <div class="pb-4 border-b border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Title</p>
                        <p class="text-gray-900 font-semibold text-lg">{{ $portfolio->title }}</p>
                    </div>

                    <!-- Status -->
                    <div class="pb-4 border-b border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Status</p>
                        <span class="inline-flex items-center px-3 py-1 text-sm font-bold rounded-full
                            {{ $portfolio->status === 'published' ? 'bg-green-100 text-green-800' :
                               ($portfolio->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            <i class="fas {{ $portfolio->status === 'published' ? 'fa-check-circle' :
                                          ($portfolio->status === 'draft' ? 'fa-edit' : 'fa-archive') }} mr-1.5"></i>
                            {{ ucfirst($portfolio->status) }}
                        </span>
                    </div>

                    <!-- Tech Stack -->
                    @if($portfolio->techStack)
                    <div class="pb-4 border-b border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Tech Stack</p>
                        <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800">
                            <i class="fas fa-layer-group mr-1.5"></i>
                            {{ $portfolio->techStack->name }}
                        </span>
                    </div>
                    @endif

                    <!-- Completion Date -->
                    @if($portfolio->project_date)
                    <div class="pb-4 border-b border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Completion Date</p>
                        <div class="flex items-center text-gray-900">
                            <i class="fas fa-calendar text-indigo-600 mr-2"></i>
                            <span class="font-semibold">{{ $portfolio->project_date->format('F Y') }}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Featured Badge -->
                    @if($portfolio->is_featured)
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Showcase</p>
                        <div class="flex items-center text-yellow-600 bg-yellow-50 px-3 py-2 rounded-lg">
                            <i class="fas fa-star mr-2"></i>
                            <span class="font-semibold">Featured Project</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Author Card -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white">Project Author</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center">
                        <img class="h-16 w-16 rounded-xl ring-4 ring-indigo-100" src="https://ui-avatars.com/api/?name={{ $portfolio->user->name }}&background=4F46E5&color=fff&size=128" alt="{{ $portfolio->user->name }}">
                        <div class="ml-4">
                            <p class="text-lg font-bold text-gray-900">{{ $portfolio->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $portfolio->user->email }}</p>
                            @if($portfolio->user->position)
                                <p class="text-sm text-indigo-600 mt-1">{{ $portfolio->user->position }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Card -->
            @if(auth()->user()->can('edit any portfolio') || (auth()->user()->can('edit own portfolio') && $portfolio->user_id === auth()->id()))
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl shadow-lg border border-indigo-200 p-6">
                <h3 class="text-sm font-bold text-gray-700 mb-4">Manage Project</h3>
                <div class="space-y-2">
                    <a href="{{ route('portfolios.edit', $portfolio) }}"
                       class="flex items-center justify-center w-full px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-lg transition-all">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Project
                    </a>
                    @if(auth()->id() === $portfolio->user_id || auth()->user()->can('delete any portfolio'))
                    <button onclick="deletePortfolio({{ $portfolio->id }})"
                            class="flex items-center justify-center w-full px-4 py-3 bg-white hover:bg-red-50 text-red-600 font-semibold rounded-lg border-2 border-red-200 hover:border-red-300 transition-all">
                        <i class="fas fa-trash mr-2"></i>
                        Delete Project
                    </button>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
const ROUTES = {
    destroy: @js(route('portfolios.destroy', ['portfolio' => '__ID__'])),
};

function deletePortfolio(portfolioId) {
    event.preventDefault();

    if (typeof Swal === 'undefined') {
        if (confirm('Are you sure you want to delete this portfolio project?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = ROUTES.destroy.replace('__ID__', portfolioId);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            const tokenField = document.createElement('input');
            tokenField.type = 'hidden';
            tokenField.name = '_token';
            tokenField.value = csrfToken;
            form.appendChild(methodField);
            form.appendChild(tokenField);
            document.body.appendChild(form);
            form.submit();
        }
        return;
    }

    Swal.fire({
        title: 'Delete Portfolio Project',
        text: "Are you sure you want to delete this portfolio project? This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = ROUTES.destroy.replace('__ID__', portfolioId);
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            const tokenField = document.createElement('input');
            tokenField.type = 'hidden';
            tokenField.name = '_token';
            tokenField.value = csrfToken;
            form.appendChild(methodField);
            form.appendChild(tokenField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endsection
