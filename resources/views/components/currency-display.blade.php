@props([
    'amount' => 0,
    'showCode' => false,
    'class' => 'font-medium text-gray-900'
])

<span class="{{ $class }}">
    @if($showCode)
        {{ \App\Helpers\CurrencyHelper::getCode() }}
    @else
        {{ \App\Helpers\CurrencyHelper::format($amount) }}
    @endif
</span>
