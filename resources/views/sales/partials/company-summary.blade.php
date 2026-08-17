@php
    $company = $dashboard['company'];

    // Millions only once the figure earns them; a small company reading
    // "$0.00M" tells the reader nothing.
    $money = fn (float $amount) => $amount >= 1000000
        ? '$'.number_format($amount / 1000000, 2).'M'
        : '$'.number_format($amount, 0);
@endphp

<div class="flex flex-wrap items-baseline gap-x-6 gap-y-1 px-1 text-sm text-gray-500 dark:text-gray-400">
    <span class="s-eyebrow">Company</span>
    <span class="s-num">
        {{ $money($company['grossSales']) }} of {{ $money($company['target']) }}
        @if($company['achievement'] !== null)· {{ number_format($company['achievement'], 1) }}%@endif
    </span>
    <span class="s-num">
        your contribution <span class="font-bold text-gray-700 dark:text-gray-300">{{ $company['contribution'] === null ? '—' : number_format($company['contribution'], 1).'%' }}</span>
    </span>
</div>
