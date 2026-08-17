{{--
    The settled (already distributed) list. Shared by the server-rendered page
    and the AJAX lists endpoint.

    Expects: $orders (LengthAwarePaginator).
--}}
<div class="space-y-3">
    @foreach($orders as $order)
        @include('value-distribution.partials.order-card', ['order' => $order, 'defaultOpen' => false])
    @endforeach
</div>