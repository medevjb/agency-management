@php
    $months = $dashboard['months'];
    $quarter = $dashboard['quarter'];
    $period = $dashboard['period'];
@endphp

<div class="s-panel p-5 sm:p-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <p class="s-eyebrow">{{ $period['quarter'] }} qualification</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Bonus qualifies month by month. Only sales above a month's target count toward it.
            </p>
        </div>
        <a href="{{ route('orders.index') }}" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
            My orders
        </a>
    </div>

    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($months as $month)
            <div class="rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 p-4">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $month['name'] }}</p>
                    @include('sales.partials.status-badge', ['status' => $month['status']])
                </div>

                <div class="flex items-baseline justify-between gap-3">
                    <span class="s-stat-label">Sales</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100 s-num">${{ number_format($month['sales'], 2) }}</span>
                </div>
                <div class="flex items-baseline justify-between gap-3 mt-1.5">
                    <span class="s-stat-label">Target</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100 s-num">${{ number_format($month['target'], 2) }}</span>
                </div>

                <div class="s-share mt-2">
                    <span class="{{ ($month['achievement'] ?? 0) >= 100 ? 'is-emerald' : 'is-amber' }}" style="width: {{ min(100, $month['achievement'] ?? 0) }}%"></span>
                </div>
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500 s-num">{{ $month['achievement'] === null ? 'No target set' : number_format($month['achievement'], 1).'% of target' }}</p>

                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                    @if($month['remaining'] > 0)
                        <div class="flex items-baseline justify-between gap-3">
                            <span class="s-stat-label">Remaining to target</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-gray-100 s-num">${{ number_format($month['remaining'], 2) }}</span>
                        </div>
                    @else
                        <div class="flex items-baseline justify-between gap-3">
                            <span class="s-stat-label">Extra sales</span>
                            <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400 s-num">${{ number_format($month['extra'], 2) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-5 pt-5 border-t border-gray-100 dark:border-gray-800">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <p class="s-eyebrow">Quarter summary</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">An aggregate — quarter achievement does not determine bonus qualification.</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <p class="s-stat-label">Quarter sales</p>
                <p class="mt-0.5 text-lg font-extrabold text-gray-900 dark:text-gray-100 s-num">${{ number_format($quarter['sales'], 2) }}</p>
            </div>
            <div>
                <p class="s-stat-label">Quarter target</p>
                <p class="mt-0.5 text-lg font-extrabold text-gray-900 dark:text-gray-100 s-num">${{ number_format($quarter['target'], 2) }}</p>
            </div>
            <div>
                <p class="s-stat-label">Quarter achievement</p>
                <p class="mt-0.5 text-lg font-extrabold text-gray-900 dark:text-gray-100 s-num">{{ $quarter['achievement'] === null ? '—' : number_format($quarter['achievement'], 1).'%' }}</p>
            </div>
            <div>
                <p class="s-stat-label">Qualified extra sales</p>
                <p class="mt-0.5 text-lg font-extrabold text-emerald-600 dark:text-emerald-400 s-num">${{ number_format($quarter['qualifiedExtra'], 2) }}</p>
            </div>
            <div>
                <p class="s-stat-label">{{ $period['quarter'] }} bonus</p>
                <p class="mt-0.5 text-lg font-extrabold text-emerald-600 dark:text-emerald-400 s-num">৳{{ number_format($quarter['bonusBdt'], 2) }}</p>
            </div>
        </div>
    </div>
</div>
