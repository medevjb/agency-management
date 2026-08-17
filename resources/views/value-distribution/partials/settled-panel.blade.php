{{--
    The whole "Already distributed" disclosure panel — toggle, body, list and
    pager — so the AJAX layer can add/remove/replace it in one outerHTML swap
    (a save can move the first order into this bucket while it was hidden).

    Expects: $distributedOrders (LengthAwarePaginator), $distributedCount (int),
             $openOnLoad (bool).
--}}
@if($distributedCount > 0)
    <div class="vd-panel" id="vdSettledPanel">
        <button type="button" class="vd-settled-toggle" data-settled-toggle
                aria-expanded="{{ $openOnLoad ? 'true' : 'false' }}"
                aria-controls="vdSettledBody">
            <span class="vd-settled-toggle-label">Already distributed (<span id="vdDistributedCount">{{ $distributedCount }}</span>)</span>
            <span class="vd-settled-toggle-action">
                <span data-label>{{ $openOnLoad ? 'Hide' : 'Show' }}</span>
                <i class="fas fa-chevron-{{ $openOnLoad ? 'up' : 'down' }}"></i>
            </span>
        </button>
        <div id="vdSettledBody" class="vd-settled-body" {{ $openOnLoad ? '' : 'hidden' }}>
            <div id="vdDistributedList">
                @include('value-distribution.partials.distributed-list', ['orders' => $distributedOrders])
            </div>
            <div id="vdDistributedPager">
                @include('value-distribution.partials.pager', ['paginator' => $distributedOrders, 'itemLabel' => 'settled order', 'section' => 'distributed'])
            </div>
        </div>
    </div>
@endif