{{--
    The pending distribution list, including empty states. Shared by the
    server-rendered page and the AJAX lists endpoint.

    Expects: $orders (LengthAwarePaginator), $filters (array), $hasAnyOrders,
             $monthDate (Carbon).
--}}
<div class="space-y-3">
    @forelse($orders as $order)
        @include('value-distribution.partials.order-card', ['order' => $order, 'defaultOpen' => $orders->total() <= 3])
    @empty
        @if(($filters['search'] ?? '') !== '')
            <div class="vd-empty">
                <i class="fas fa-magnifying-glass text-2xl text-gray-300 dark:text-gray-700"></i>
                <p class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">No pending orders match "{{ $filters['search'] }}"</p>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                    <button type="button" class="text-indigo-600 dark:text-indigo-400 hover:underline" data-clear-filters>Clear the filters</button> to see everything pending.
                </p>
            </div>
        @elseif($hasAnyOrders)
            <div class="vd-empty">
                <i class="fas fa-circle-check text-2xl text-emerald-300 dark:text-emerald-700"></i>
                <p class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">Nothing waiting on you.</p>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Every order completed in {{ $monthDate->format('F') }} has been fully distributed.</p>
            </div>
        @else
            <div class="vd-empty">
                <i class="fas fa-inbox text-2xl text-gray-300 dark:text-gray-700"></i>
                <p class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">No completed orders yet.</p>
                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Nothing's been marked complete in {{ $monthDate->format('F') }} so far.</p>
            </div>
        @endif
    @endforelse
</div>