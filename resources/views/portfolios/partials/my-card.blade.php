<div class="portfolio-card bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden relative">
    <!-- Image Section -->
    <div class="relative h-52 overflow-hidden bg-gray-100">
        @if($portfolio->screenshot)
            <img src="{{ asset('storage/' . $portfolio->screenshot) }}" alt="{{ $portfolio->title }}" class="portfolio-image w-full h-full object-cover">
        @else
            <div class="w-full h-full gradient-overlay flex items-center justify-center">
                <i class="fas fa-laptop-code text-white text-7xl opacity-80"></i>
            </div>
        @endif

        <!-- Status Badge -->
        <div class="absolute top-3 right-3">
            <span class="px-3 py-1 text-xs font-bold rounded-full shadow-lg backdrop-blur-sm
                {{ $portfolio->status === 'published' ? 'bg-green-500/90 text-white' :
                   ($portfolio->status === 'draft' ? 'bg-yellow-500/90 text-white' : 'bg-gray-500/90 text-white') }}">
                {{ ucfirst($portfolio->status) }}
            </span>
        </div>

        <!-- Featured Badge -->
        @if($portfolio->is_featured)
            <div class="absolute top-3 left-3">
                <span class="flex items-center px-2 py-1 text-xs font-bold rounded-full shadow-lg bg-yellow-500 text-white">
                    <i class="fas fa-star mr-1"></i>
                    Featured
                </span>
            </div>
        @endif
    </div>

    <!-- Content Section -->
    <div class="p-5">
        <!-- Tech Stack Badge -->
        @if($portfolio->techStack)
            <div class="mb-3">
                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                    <i class="fas fa-layer-group mr-1.5"></i>
                    {{ $portfolio->techStack->name }}
                </span>
            </div>
        @endif

        <!-- Title -->
        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-1">{{ $portfolio->title }}</h3>

        <!-- Description -->
        <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $portfolio->description ?: 'No description provided.' }}</p>

        <!-- Date -->
        @if($portfolio->project_date)
            <div class="text-xs text-gray-500 mb-4 pb-3 border-b border-gray-100">
                <span class="flex items-center">
                    <i class="fas fa-calendar mr-1"></i>
                    {{ $portfolio->project_date->format('F Y') }}
                </span>
            </div>
        @else
            <div class="mb-4 pb-3 border-b border-gray-100"></div>
        @endif

        <!-- Action Buttons -->
        <div class="flex gap-2">
            <a href="{{ route('portfolios.show', $portfolio) }}"
                class="flex-1 text-center px-3 py-2 bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700 rounded-lg hover:from-indigo-100 hover:to-purple-100 transition-all text-sm font-semibold">
                <i class="fas fa-eye mr-1"></i> View
            </a>
            <a href="{{ route('portfolios.edit', $portfolio) }}"
                class="flex-1 text-center px-3 py-2 bg-gradient-to-r from-blue-50 to-cyan-50 text-blue-700 rounded-lg hover:from-blue-100 hover:to-cyan-100 transition-all text-sm font-semibold">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            @can('delete own portfolio')
            <button onclick="deletePortfolio({{ $portfolio->id }})"
                class="px-3 py-2 bg-gradient-to-r from-red-50 to-pink-50 text-red-600 rounded-lg hover:from-red-100 hover:to-pink-100 transition-all text-sm font-semibold">
                <i class="fas fa-trash"></i>
            </button>
            @endcan
        </div>
    </div>
</div>
