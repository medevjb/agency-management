@php
    $bonus = $dashboard['bonus'];
    $period = $dashboard['period'];
@endphp

<section class="s-hero">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="s-eyebrow">{{ $period['quarter'] }} bonus so far</p>
            <p class="mt-2 s-bonus-num s-num">৳{{ number_format($bonus['bonusBdt'], 2) }}</p>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Accrued across {{ $period['quarterMonths'] }}, paid at the end of the quarter.
            </p>
        </div>
        @include('sales.partials.status-badge', ['status' => $bonus['status']])
    </div>

    @if($bonus['configured'])
        <div class="s-formula mt-6">
            <div class="s-formula-row">
                <span class="s-stat-label">Qualified extra sales</span>
                <span class="font-bold text-gray-900 dark:text-gray-100 s-num">${{ number_format($bonus['qualifiedExtra'], 2) }}</span>
            </div>
            <div class="s-formula-row">
                <span class="s-stat-label s-num">× {{ number_format($bonus['rate'], 2) }}% bonus rate</span>
                <span class="font-bold text-gray-900 dark:text-gray-100 s-num">${{ number_format($bonus['bonusUsd'], 2) }}</span>
            </div>
            <div class="s-formula-row">
                <span class="s-stat-label s-num">× ৳{{ number_format($bonus['exchangeRate'], 2) }} per USD</span>
                <span class="font-extrabold text-emerald-600 dark:text-emerald-400 s-num">৳{{ number_format($bonus['bonusBdt'], 2) }}</span>
            </div>
        </div>

        <div class="s-callout is-emerald mt-4">
            <i class="fas fa-bolt text-emerald-500 mt-0.5"></i>
            <p class="text-sm text-emerald-800 dark:text-emerald-300">
                Every additional <span class="s-num font-bold">${{ number_format($bonus['motivationUsd'], 0) }}</span> in qualified sales adds
                <span class="s-num font-bold">৳{{ number_format($bonus['motivationBdt'], 2) }}</span> to your bonus.
            </p>
        </div>
    @else
        <div class="s-callout is-amber mt-6">
            <i class="fas fa-circle-info text-amber-500 mt-0.5"></i>
            <div>
                <p class="text-sm font-bold text-amber-800 dark:text-amber-300">Bonus rates not configured yet</p>
                <p class="mt-0.5 text-sm text-amber-700 dark:text-amber-400">
                    An admin has not set the bonus percentage and USD rate. Your sales and target figures below are live —
                    your bonus will apply as soon as the rates are configured.
                </p>
                @if(($isAdmin ?? false) && ! ($isPreview ?? false))
                    <a href="{{ route('settings.sales-bonus.index') }}" class="mt-2 inline-block text-sm font-bold text-amber-900 dark:text-amber-200 underline hover:no-underline">
                        Configure bonus rates
                    </a>
                @endif
            </div>
        </div>
    @endif
</section>
