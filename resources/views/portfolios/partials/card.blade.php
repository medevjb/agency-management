<div class="portfolio-card bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
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

        <!-- Author & Date -->
        @if(isset($portfolio->user))
        <div class="flex items-center justify-between text-xs text-gray-500 mb-4 pb-3 border-b border-gray-100">
            <div class="flex items-center">
                <img class="h-6 w-6 rounded-full ring-2 ring-indigo-100" src="https://ui-avatars.com/api/?name={{ $portfolio->user->name }}&background=4F46E5&color=fff" alt="{{ $portfolio->user->name }}">
                <span class="ml-2 font-medium">{{ $portfolio->user->name }}</span>
            </div>
            @if($portfolio->project_date)
                <span class="flex items-center">
                    <i class="fas fa-calendar mr-1"></i>
                    {{ $portfolio->project_date->format('M Y') }}
                </span>
            @endif
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex gap-2">
            <a href="{{ route('portfolios.show', $portfolio) }}"
                class="flex-1 text-center px-3 py-2 bg-gradient-to-r from-indigo-50 to-purple-50 text-indigo-700 rounded-lg hover:from-indigo-100 hover:to-purple-100 transition-all text-sm font-semibold">
                <i class="fas fa-eye mr-1"></i> View
            </a>
            @if($portfolio->project_url)
            <a href="{{ $portfolio->project_url }}" target="_blank"
                class="flex-1 text-center px-3 py-2 bg-gradient-to-r from-green-50 to-emerald-50 text-green-700 rounded-lg hover:from-green-100 hover:to-emerald-100 transition-all text-sm font-semibold">
                <i class="fas fa-external-link-alt mr-1"></i> Live
            </a>
            @endif
        </div>
    </div>
</div>
