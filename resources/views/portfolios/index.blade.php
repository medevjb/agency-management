@extends('layouts.dashboard')

@push('styles')
    <style>
        .portfolio-card {
            transition: all 0.3s ease;
        }
        .portfolio-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .portfolio-image {
            transition: transform 0.3s ease;
        }
        .portfolio-card:hover .portfolio-image {
            transform: scale(1.05);
        }
        .gradient-overlay {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.9) 0%, rgba(139, 92, 246, 0.9) 100%);
        }
        .portfolio-card {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
@endpush

@section('content')
<div class="pt-4 sm:p-6 lg:p-8 lg:pb-3">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="mb-4">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Portfolio Projects</h1>
            <p class="text-gray-600 text-lg">Browse all portfolio projects from team members</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 mb-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-base font-semibold text-gray-800 flex items-center">
                <i class="fas fa-filter text-indigo-500 mr-2"></i>
                Filters
            </h2>
            <!-- Toggle Switch -->
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">Basic</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="advancedToggle" class="sr-only peer" onchange="toggleFilters()">
                    <div
                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                    </div>
                </label>
                <span class="text-sm text-gray-600">Advanced</span>
            </div>
        </div>

        <!-- Basic Filters (Always Visible) -->
        <form method="GET" action="{{ route('portfolios.index') }}" id="filterForm" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-xs font-semibold text-gray-700 mb-1">
                        <i class="fas fa-search mr-1 text-indigo-500"></i>Search Projects
                    </label>
                    <div class="relative">
                        <input type="text" id="search" name="search" value="{{ request('search') }}"
                            placeholder="Search by title or description..."
                            class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white transition-all duration-200 text-sm">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="fas fa-search text-gray-400 text-sm"></i>
                        </div>
                    </div>
                </div>

                <!-- Quick Status Filter -->
                <div>
                    <label for="status" class="block text-xs font-semibold text-gray-700 mb-1">
                        <i class="fas fa-toggle-on mr-1 text-indigo-500"></i>Status
                    </label>
                    <div class="relative">
                        <select id="status" name="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white appearance-none transition-all duration-200 text-sm">
                            <option value="">All Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="flex items-end space-x-2">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium rounded-lg transition-all duration-200 text-sm shadow hover:shadow-lg">
                        <i class="fas fa-search mr-1"></i>
                        Search
                    </button>
                    <a href="{{ route('portfolios.index') }}"
                        class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 text-sm">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>

            <!-- Advanced Filters (Hidden by default) -->
            <div id="advancedFilters"
                class="overflow-hidden transition-all duration-500 ease-in-out max-h-0 opacity-0">
                <div class="pt-4 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                        <!-- Tech Stack Filter -->
                        <div>
                            <label for="tech_stack_id" class="block text-xs font-semibold text-gray-700 mb-1">
                                <i class="fas fa-layer-group mr-1 text-indigo-500"></i>Tech Stack
                            </label>
                            <div class="relative">
                                <select id="tech_stack_id" name="tech_stack_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white appearance-none transition-all duration-200 text-sm">
                                    <option value="">All Tech Stacks</option>
                                    @foreach($techStacks as $stack)
                                        <option value="{{ $stack->id }}"
                                            {{ request('tech_stack_id') == $stack->id ? 'selected' : '' }}>
                                            {{ $stack->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Author Filter -->
                        <div>
                            <label for="user_id" class="block text-xs font-semibold text-gray-700 mb-1">
                                <i class="fas fa-user mr-1 text-indigo-500"></i>Author
                            </label>
                            <div class="relative">
                                <input type="text" id="user_id" name="user_id"
                                    value="{{ request('user_id') }}" placeholder="Search by author name..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white transition-all duration-200 text-sm">
                            </div>
                        </div>

                        <!-- Featured Filter -->
                        <div>
                            <label for="featured" class="block text-xs font-semibold text-gray-700 mb-1">
                                <i class="fas fa-star mr-1 text-indigo-500"></i>Showcase
                            </label>
                            <div class="relative">
                                <select id="featured" name="featured"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white appearance-none transition-all duration-200 text-sm">
                                    <option value="">All Projects</option>
                                    <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Featured Only</option>
                                    <option value="0" {{ request('featured') == '0' ? 'selected' : '' }}>Regular Projects</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Date Range Filter -->
                        <div>
                            <label for="date_from" class="block text-xs font-semibold text-gray-700 mb-1">
                                <i class="fas fa-calendar mr-1 text-indigo-500"></i>Date From
                            </label>
                            <div class="relative">
                                <input type="date" id="date_from" name="date_from"
                                    value="{{ request('date_from') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white transition-all duration-200 text-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="text-sm text-gray-600">
            <span class="font-semibold text-gray-900" id="totalCount">{{ $portfolios->total() }}</span> portfolio projects found
        </div>
        <div class="flex gap-3">
            <a href="{{ route('portfolios.my') }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white font-medium rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105">
                <i class="fas fa-user-circle mr-2"></i>
                My Portfolios
            </a>
            @can('create portfolio')
            <a href="{{ route('portfolios.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105">
                <i class="fas fa-plus mr-2"></i>
                Add Portfolio
            </a>
            @endcan
        </div>
    </div>

    <!-- Portfolio Grid -->
    <div id="portfolioGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($portfolios as $portfolio)
            @include('portfolios.partials.card', ['portfolio' => $portfolio])
        @empty
            <div class="col-span-full text-center py-16 bg-white rounded-xl shadow-md">
                <div class="mb-4">
                    <i class="fas fa-folder-open text-gray-300 text-7xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Portfolio Projects Found</h3>
                <p class="text-gray-500 mb-6">Get started by adding your completed projects to showcase your work.</p>
                @can('create portfolio')
                <a href="{{ route('portfolios.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>
                    Add Your First Project
                </a>
                @endcan
            </div>
        @endforelse
    </div>

    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="hidden py-8 text-center">
        <div class="inline-flex items-center px-4 py-2 bg-white rounded-lg shadow-md">
            <i class="fas fa-spinner fa-spin text-indigo-600 mr-2"></i>
            <span class="text-gray-700 font-medium">Loading more portfolios...</span>
        </div>
    </div>

    <!-- No More Results -->
    <div id="noMoreResults" class="hidden py-8 text-center">
        <div class="inline-flex items-center px-4 py-2 bg-gray-100 rounded-lg">
            <i class="fas fa-check-circle text-green-600 mr-2"></i>
            <span class="text-gray-700 font-medium">You've reached the end</span>
        </div>
    </div>
</div>

<script>
let page = 1;
let loading = false;
let hasMore = true;

// Build query string from URL params
function getQueryParams() {
    const params = new URLSearchParams(window.location.search);
    const result = {};
    for (const [key, value] of params) {
        if (key !== 'page') {
            result[key] = value;
        }
    }
    return result;
}

// Load more portfolios
async function loadMore() {
    if (loading || !hasMore) return;

    loading = true;
    document.getElementById('loadingIndicator').classList.remove('hidden');

    try {
        const params = new URLSearchParams(getQueryParams());
        params.set('page', page + 1);

        const response = await axios.get(`{{ route('portfolios.index') }}?${params.toString()}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const portfolios = response.data.data;
        hasMore = response.data.has_more;

        if (portfolios.length > 0) {
            const grid = document.getElementById('portfolioGrid');

            portfolios.forEach(portfolio => {
                const cardHtml = createPortfolioCard(portfolio);
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = cardHtml;
                grid.appendChild(tempDiv.firstElementChild);
            });

            page++;
        }

        if (!hasMore) {
            document.getElementById('noMoreResults').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error loading more portfolios:', error);
    } finally {
        loading = false;
        document.getElementById('loadingIndicator').classList.add('hidden');
    }
}

// Create portfolio card HTML
function createPortfolioCard(portfolio) {
    const statusClass = portfolio.status === 'published' ? 'bg-green-500/90 text-white' :
                       (portfolio.status === 'draft' ? 'bg-yellow-500/90 text-white' : 'bg-gray-500/90 text-white');

    const featuredBadge = portfolio.is_featured ? `
        <div class="absolute top-3 left-3">
            <span class="flex items-center px-2 py-1 text-xs font-bold rounded-full shadow-lg bg-yellow-500 text-white">
                <i class="fas fa-star mr-1"></i>
                Featured
            </span>
        </div>
    ` : '';

    const imageHtml = portfolio.screenshot
        ? `<img src="/storage/${portfolio.screenshot}" alt="${portfolio.title}" class="portfolio-image w-full h-full object-cover">`
        : `<div class="w-full h-full gradient-overlay flex items-center justify-center">
            <i class="fas fa-laptop-code text-white text-7xl opacity-80"></i>
           </div>`;

    const techStackBadge = portfolio.tech_stack ? `
        <div class="mb-3">
            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                <i class="fas fa-layer-group mr-1.5"></i>
                ${portfolio.tech_stack.name}
            </span>
        </div>
    ` : '';

    const liveButton = portfolio.project_url ? `
        <a href="${portfolio.project_url}" target="_blank"
            class="flex-1 text-center px-3 py-2 bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 rounded-lg hover:from-green-100 hover:to-emerald-100 transition-all text-sm font-semibold">
            <i class="fas fa-external-link-alt mr-1"></i> Live
        </a>
    ` : '';

    return `
        <div class="portfolio-card bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
            <div class="relative h-52 overflow-hidden bg-gray-100">
                ${imageHtml}
                <div class="absolute top-3 right-3">
                    <span class="px-3 py-1 text-xs font-bold rounded-full shadow-lg backdrop-blur-sm ${statusClass}">
                        ${portfolio.status.charAt(0).toUpperCase() + portfolio.status.slice(1)}
                    </span>
                </div>
                ${featuredBadge}
            </div>
            <div class="p-5">
                ${techStackBadge}
                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-1">${portfolio.title}</h3>
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">${portfolio.description || 'No description provided.'}</p>
                <div class="flex items-center justify-between text-xs text-gray-500 mb-4 pb-3 border-b border-gray-100">
                    <div class="flex items-center">
                        <img class="h-6 w-6 rounded-full ring-2 ring-indigo-100" src="https://ui-avatars.com/api/?name=${portfolio.user?.name || 'User'}&background=4F46E5&color=fff" alt="${portfolio.user?.name || 'User'}">
                        <span class="ml-2 font-medium">${portfolio.user?.name || 'Unknown'}</span>
                    </div>
                    ${portfolio.project_date ? `<span class="flex items-center"><i class="fas fa-calendar mr-1"></i>${portfolio.project_date}</span>` : ''}
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('portfolios.index') }}/${portfolio.id}"
                        class="flex-1 text-center px-3 py-2 bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700 rounded-lg hover:from-indigo-100 hover:to-purple-100 transition-all text-sm font-semibold">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                    ${liveButton}
                </div>
            </div>
        </div>
    `;
}

// Infinite scroll handler
function handleScroll() {
    const scrollPosition = window.innerHeight + window.pageYOffset;
    const threshold = document.body.offsetHeight - 500;

    if (scrollPosition >= threshold && !loading && hasMore) {
        loadMore();
    }
}

// Initialize infinite scroll
document.addEventListener('DOMContentLoaded', function() {
    window.addEventListener('scroll', handleScroll);

    // Check if there are more results to load
    hasMore = {{ $portfolios->hasMorePages() ? 'true' : 'false' }};
    page = {{ $portfolios->currentPage() }};
});

function toggleFilters() {
    const advancedFilters = document.getElementById('advancedFilters');
    const toggle = document.getElementById('advancedToggle');

    if (toggle.checked) {
        advancedFilters.classList.remove('max-h-0', 'opacity-0');
        advancedFilters.classList.add('max-h-96', 'opacity-100');
    } else {
        advancedFilters.classList.remove('max-h-96', 'opacity-100');
        advancedFilters.classList.add('max-h-0', 'opacity-0');
    }
}

// Check if advanced filters have values and show them automatically
document.addEventListener('DOMContentLoaded', function() {
    const hasAdvancedValues = '{{ request('tech_stack_id') }}' || '{{ request('user_id') }}' || '{{ request('featured') }}' || '{{ request('date_from') }}';
    if (hasAdvancedValues) {
        document.getElementById('advancedToggle').checked = true;
        toggleFilters();
    }
});
</script>
@endsection
