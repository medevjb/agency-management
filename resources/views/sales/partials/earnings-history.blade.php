@php
    $history = $dashboard['history'];

    // One scale across all twelve bars, so heights compare across quarters.
    $peak = collect($history['quarters'])
        ->flatMap(fn (array $quarter) => array_column($quarter['months'], 'sales'))
        ->max() ?: 1;
@endphp

<div class="s-panel p-5 sm:p-6">
    <div class="flex flex-wrap items-end justify-between gap-3 mb-5">
        <div>
            <p class="s-eyebrow">Earnings history</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Each bar is a month against its target. Bonus qualifies monthly and is paid by quarter.
            </p>
        </div>
        <div class="text-right">
            <p class="s-stat-label">Earned to date</p>
            <p class="mt-0.5 text-xl font-extrabold text-emerald-600 dark:text-emerald-400 s-num">
                ৳{{ number_format($history['earnedToDate'], 2) }}
            </p>
        </div>
    </div>

    <div class="s-history">
        @foreach($history['quarters'] as $quarter)
            @php $isCurrent = $quarter['status'] !== 'paid'; @endphp

            <div class="s-quarter {{ $isCurrent ? 'is-current' : '' }}">
                <div class="s-spark">
                    @foreach($quarter['months'] as $month)
                        @php
                            $height = max(6, round($month['sales'] / $peak * 100));
                            // A month with no target has no bar to clear, so
                            // it is not money — 0 >= 0 must not read as met.
                            $qualified = $month['target'] > 0 && $month['sales'] >= $month['target'];
                        @endphp
                        <div class="s-spark-bar {{ $qualified ? 'is-qualified' : '' }}"
                             style="height: {{ $height }}%"
                             title="{{ $month['name'] }} — ${{ number_format($month['sales'], 0) }} of ${{ number_format($month['target'], 0) }}"></div>
                    @endforeach
                </div>

                <div class="s-spark-months">
                    @foreach($quarter['months'] as $month)
                        <span>{{ $month['name'] }}</span>
                    @endforeach
                </div>

                <p class="mt-3 text-xs font-bold text-gray-900 dark:text-gray-100">{{ $quarter['label'] }}</p>
                <p class="mt-0.5 text-sm font-extrabold {{ $isCurrent ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-700 dark:text-gray-300' }} s-num">
                    ৳{{ number_format($quarter['payoutBdt'], 2) }}
                </p>
                <div class="mt-2">
                    @include('sales.partials.status-badge', ['status' => $quarter['status']])
                </div>
            </div>
        @endforeach
    </div>
</div>
