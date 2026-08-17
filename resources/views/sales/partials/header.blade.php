@php
    $period = $dashboard['period'];
@endphp

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <p class="s-eyebrow">Sales Performance</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">
            Welcome back, {{ $firstName }}
        </h1>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Viewing {{ $period['month'] }} {{ $period['year'] }} · bonus accrues across {{ $period['quarter'] }} ({{ $period['quarterMonths'] }}).
        </p>
    </div>

    @unless($isPreview)
        <div class="flex flex-wrap items-center gap-2">
            @can('create', \App\Models\Order::class)
                <a href="{{ route('orders.create') }}" class="s-primary-btn">
                    <i class="fas fa-plus mr-2"></i>New sale
                </a>
            @endcan

            <a href="{{ route('orders.index') }}" class="s-secondary-btn">
                <i class="fas fa-list mr-2"></i>My orders
            </a>
        </div>
    @endunless
</div>
