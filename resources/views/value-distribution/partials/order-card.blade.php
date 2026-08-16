{{--
    One completed order's distribution card. Multi-instance safe: every hook
    is scoped by data attributes, not ids, since many of these render on the
    same page at once.

    Expects: $order (with valueDistributions.user and users eager loaded),
             $defaultOpen (bool).
--}}
@php
    $net = $order->netValue();
    $gross = (float) ($order->value ?? 0);
    $feeRate = $gross > 0 ? round((1 - $net / $gross) * 100) : 0;
    $platformLabel = $order->freelanceProfile?->platform ? ucfirst($order->freelanceProfile->platform) : 'Default';
    $isComplete = $order->isValueFullyDistributed();
    $assignedIds = $order->users->pluck('id');
    $creditedIds = $order->valueDistributions->pluck('user_id');
@endphp

<div class="dist-card {{ $isComplete ? 'is-complete' : 'is-pending' }}"
     data-distribution-card
     data-order-id="{{ $order->id }}"
     data-net-value="{{ $net }}"
     data-store-url="{{ route('value-distribution.store', $order) }}"
     data-search-url="{{ route('orders.search-users', ['exclude_roles' => 'sales-leader,sales-member,admin']) }}">

    <button type="button" class="dist-summary" data-dist-toggle aria-expanded="{{ $defaultOpen ? 'true' : 'false' }}">
        <span class="dist-status-dot {{ $isComplete ? 'is-complete' : 'is-pending' }}"></span>

        <span class="dist-summary-main">
            <span class="dist-order-name">{{ $order->name }}</span>
            <span class="dist-order-meta">
                {{ $order->order_id ?: '#'.$order->id }}
                @if($order->team) · {{ $order->team->name }} @endif
                · completed {{ $order->completed_at?->format('M d, Y') }}
            </span>
        </span>

        <span class="dist-summary-figures">
            <span class="dist-net-amount vd-mono">${{ number_format($net, 2) }}</span>
            @if($isComplete)
                <span class="dist-badge is-complete"><i class="fas fa-check"></i> Distributed</span>
            @else
                <span class="dist-badge is-pending vd-mono">${{ number_format($order->undistributedValue(), 2) }} left</span>
            @endif
        </span>

        <i class="fas fa-chevron-down dist-chevron"></i>
    </button>

    <div class="dist-body" data-dist-body {{ $defaultOpen ? '' : 'hidden' }}>
        <p class="dist-fee-line">
            <i class="fas fa-circle-info"></i>
            <span class="vd-mono">${{ number_format($gross, 2) }}</span> gross · {{ $platformLabel }} fee {{ $feeRate }}% · <strong class="vd-mono">${{ number_format($net, 2) }} net to distribute</strong>
        </p>

        <div class="dist-rows" data-dist-rows>
            @foreach($order->valueDistributions as $share)
                <div class="dist-row" data-dist-row data-user-id="{{ $share->user_id }}">
                    <span class="dist-person">
                        {{ $share->user?->name ?? 'Removed user' }}
                        @if($assignedIds->contains($share->user_id))
                            <span class="dist-tag">Assigned</span>
                        @endif
                    </span>
                    <div class="dist-amount-wrap">
                        <span class="dist-currency">$</span>
                        <input type="number" step="0.01" min="0" class="dist-amount vd-mono" data-dist-amount
                               value="{{ number_format((float) $share->amount, 2, '.', '') }}">
                    </div>
                    <button type="button" class="dist-remove" data-dist-remove aria-label="Remove"><i class="fas fa-xmark"></i></button>
                </div>
            @endforeach

            @foreach($order->users as $assignee)
                @continue($creditedIds->contains($assignee->id))
                <div class="dist-row is-suggested" data-dist-row data-user-id="{{ $assignee->id }}">
                    <span class="dist-person">{{ $assignee->name }} <span class="dist-tag">Assigned</span></span>
                    <div class="dist-amount-wrap">
                        <span class="dist-currency">$</span>
                        <input type="number" step="0.01" min="0" class="dist-amount vd-mono" data-dist-amount value="" placeholder="0.00">
                    </div>
                    <button type="button" class="dist-remove" data-dist-remove aria-label="Remove"><i class="fas fa-xmark"></i></button>
                </div>
            @endforeach

            @if($order->valueDistributions->isEmpty() && $order->users->isEmpty())
                <p class="dist-empty-hint">Nobody is assigned to this order yet — search below to credit someone.</p>
            @endif
        </div>

        <div class="dist-controls">
            <div class="dist-search-wrap">
                <input type="text" class="dist-search" data-dist-search placeholder="Add anyone else…" autocomplete="off">
                <div class="dist-search-results" data-dist-results hidden></div>
            </div>
            <button type="button" class="dist-balance-btn" data-dist-balance>Give the rest to the first person</button>
        </div>

        <div class="dist-footer">
            <span class="dist-remaining" data-dist-remaining></span>
            <div class="dist-footer-actions">
                <a href="{{ route('orders.show', $order) }}" class="dist-view-link">View order</a>
                <button type="button" class="dist-save-btn" data-dist-save>Save distribution</button>
            </div>
        </div>
    </div>
</div>
