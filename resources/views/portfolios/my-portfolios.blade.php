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
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
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
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">My Portfolio Projects</h1>
            <p class="text-gray-600 text-lg">Showcase your completed work and achievements</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Total Projects</p>
                    <p class="text-3xl font-bold">{{ $portfolios->total() }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-folder text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Published</p>
                    <p class="text-3xl font-bold">{{ auth()->user()->portfolios()->where('status', 'published')->count() }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl shadow-lg p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium mb-1">Draft</p>
                    <p class="text-3xl font-bold">{{ auth()->user()->portfolios()->where('status', 'draft')->count() }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-edit text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl shadow-lg p-5 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">Featured</p>
                    <p class="text-3xl font-bold">{{ auth()->user()->portfolios()->where('is_featured', true)->count() }}</p>
                </div>
                <div class="bg-white/20 p-3 rounded-lg">
                    <i class="fas fa-star text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('portfolios.my') }}" class="space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <!-- Search Input -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-xs font-semibold text-gray-700 mb-1">
                        <i class="fas fa-search mr-1 text-indigo-500"></i>Search My Projects
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

                <!-- Status Filter -->
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

                <!-- Actions -->
                <div class="flex items-end space-x-2 md:col-span-3">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium rounded-lg transition-all duration-200 text-sm shadow hover:shadow-lg">
                        <i class="fas fa-search mr-1"></i>
                        Search
                    </button>
                    <a href="{{ route('portfolios.my') }}"
                        class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 text-sm">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="text-sm text-gray-600">
            <span class="font-semibold text-gray-900" id="totalCount">{{ $portfolios->total() }}</span> projects in your portfolio
        </div>
        <div class="flex gap-3">
            <a href="{{ route('portfolios.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-white font-medium rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105">
                <i class="fas fa-users mr-2"></i>
                All Portfolios
            </a>
            @can('create portfolio')
            <a href="{{ route('portfolios.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-medium rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105">
                <i class="fas fa-plus mr-2"></i>
                Add Project
            </a>
            @endcan
        </div>
    </div>

    <!-- Portfolio Grid -->
    <div id="portfolioGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($portfolios as $portfolio)
            @include('portfolios.partials.my-card', ['portfolio' => $portfolio])
        @empty
            <div class="col-span-full text-center py-16 bg-white rounded-xl shadow-md">
                <div class="mb-4">
                    <i class="fas fa-folder-open text-gray-300 text-7xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Portfolio Projects Yet</h3>
                <p class="text-gray-500 mb-6">Start showcasing your completed work to the team.</p>
                @can('create portfolio')
                <a href="{{ route('portfolios.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform transition-all duration-200 hover:scale-105">
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
const ROUTES = {
    destroy: @js(route('portfolios.destroy', ['portfolio' => '__ID__'])),
    my: @js(route('portfolios.my')),
};

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

        const response = await axios.get(`{{ route('portfolios.my') }}?${params.toString()}`, {
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

    const dateSection = portfolio.project_date ? `
        <div class="text-xs text-gray-500 mb-4 pb-3 border-b border-gray-100">
            <span class="flex items-center">
                <i class="fas fa-calendar mr-1"></i>
                ${new Date(portfolio.project_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long' })}
            </span>
        </div>
    ` : '<div class="mb-4 pb-3 border-b border-gray-100"></div>';

    return `
        <div class="portfolio-card bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden relative">
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
                ${dateSection}
                <div class="flex gap-2">
                    <a href="/portfolios/${portfolio.id}"
                        class="flex-1 text-center px-3 py-2 bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700 rounded-lg hover:from-indigo-100 hover:to-purple-100 transition-all text-sm font-semibold">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                    <a href="/portfolios/${portfolio.id}/edit"
                        class="flex-1 text-center px-3 py-2 bg-gradient-to-r from-blue-50 to-cyan-50 text-blue-700 rounded-lg hover:from-blue-100 hover:to-cyan-100 transition-all text-sm font-semibold">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <button onclick="deletePortfolio(${portfolio.id})"
                        class="px-3 py-2 bg-gradient-to-r from-red-50 to-pink-50 text-red-600 rounded-lg hover:from-red-100 hover:to-pink-100 transition-all text-sm font-semibold">
                        <i class="fas fa-trash"></i>
                    </button>
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

function deletePortfolio(portfolioId) {
    event.preventDefault();

    if (typeof Swal === 'undefined') {
        if (confirm('Are you sure you want to delete this portfolio project?')) {
            axios.delete(ROUTES.destroy.replace('__ID__', portfolioId))
                .then(response => {
                    if (response.data.success) {
                        window.location.href = response.data.redirect || ROUTES.my;
                    }
                })
                .catch(error => {
                    alert('Error deleting portfolio project');
                });
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
            axios.delete(ROUTES.destroy.replace('__ID__', portfolioId))
                .then(response => {
                    if (response.data.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Portfolio project has been deleted.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.data.redirect || ROUTES.my;
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to delete portfolio project',
                        icon: 'error'
                    });
                });
        }
    });
}
</script>
@endsection
