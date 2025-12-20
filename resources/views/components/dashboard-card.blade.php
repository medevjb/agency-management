<div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
    <div class="flex items-center justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-600">{{ $title }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $value }}</p>
            @if($description)
                <p class="text-sm text-gray-500 mt-1">{{ $description }}</p>
            @endif
            @if($trend && $trendValue)
                <div class="flex items-center mt-2">
                    @if($trend === 'up')
                        <i class="fas fa-arrow-up text-green-500 text-xs mr-1"></i>
                        <span class="text-sm text-green-600">{{ $trendValue }}</span>
                    @elseif($trend === 'down')
                        <i class="fas fa-arrow-down text-red-500 text-xs mr-1"></i>
                        <span class="text-sm text-red-600">{{ $trendValue }}</span>
                    @else
                        <i class="fas fa-minus text-gray-500 text-xs mr-1"></i>
                        <span class="text-sm text-gray-600">{{ $trendValue }}</span>
                    @endif
                </div>
            @endif
        </div>
        @if($icon)
            <div class="ml-4">
                <div class="inline-flex items-center justify-center p-3 bg-{{ $color }}-100 rounded-full">
                    <i class="{{ $icon }} text-{{ $color }}-600 text-xl"></i>
                </div>
            </div>
        @endif
    </div>
</div>