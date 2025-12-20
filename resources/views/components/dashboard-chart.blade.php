<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $title }}</h3>
    <div style="height: {{ $height }}">
        <canvas id="chart-{{ \Illuminate\Support\Str::random(8) }}"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple chart visualization using CSS
    const chartContainers = document.querySelectorAll('[id^="chart-"]');
    chartContainers.forEach(container => {
        if (!container.hasAttribute('data-initialized')) {
            const chartId = container.id;

            // Create a simple bar chart using CSS
            container.parentElement.innerHTML = `
                <div class="flex items-end justify-between h-full space-x-2" style="height: ${container.style.height || '300px'}">
                    ${[65, 80, 45, 90, 70, 85, 60, 75, 55, 95].map(value => `
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-gradient-to-t from-indigo-500 to-indigo-400 rounded-t hover:from-indigo-600 hover:to-indigo-500 transition-all duration-200"
                                 style="height: ${value}%"></div>
                            <span class="text-xs text-gray-600 mt-2">${Math.floor(Math.random() * 30) + 1}</span>
                        </div>
                    `).join('')}
                </div>
            `;
            container.setAttribute('data-initialized', 'true');
        }
    });
});
</script>