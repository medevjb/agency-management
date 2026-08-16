{{--
    Compact prev/next pager shared by the pending and settled lists.
    Expects: $paginator (LengthAwarePaginator), $itemLabel (singular noun).
--}}
@if($paginator->hasPages())
    <div class="vd-pager">
        <p class="vd-pager-range vd-mono">
            {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} <span class="vd-pager-of">of {{ $paginator->total() }} {{ \Illuminate\Support\Str::plural($itemLabel, $paginator->total()) }}</span>
        </p>
        <div class="vd-pager-nav">
            @if($paginator->onFirstPage())
                <span class="vd-pager-btn is-disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="vd-pager-btn"><i class="fas fa-chevron-left"></i></a>
            @endif
            <span class="vd-pager-page vd-mono">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="vd-pager-btn"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="vd-pager-btn is-disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
@endif
