{{--
    One page of change log entries, grouped by day.
    Expects: $audits (iterable of App\Models\OrderAudit), $order (App\Models\Order).

    Rendered both on first paint and by the load-more endpoint, so the markup
    must stand alone — day sections carry data-log-day so the client can merge
    a page that continues the day it already has on screen.
--}}
@php
    // Operations never learns who on the sales side touched an order, so a
    // sales author is credited generically. Computed here rather than passed
    // in, because the load-more endpoint renders this partial on its own.
    $viewer = auth()->user();
    $showsSalesActor = $viewer->isSales() || $viewer->hasRole('admin');
    $salesOwnedOrder = $order->sales_user_id !== null || $order->creator?->isSales();
@endphp
@foreach($audits->groupBy(fn ($audit) => $audit->created_at->toDateString()) as $day => $entries)
    @php $date = \Illuminate\Support\Carbon::parse($day); @endphp
    <section class="log-day" data-log-day="{{ $day }}">
        <h3 class="log-day-label">
            {{ $date->isToday() ? 'Today' : ($date->isYesterday() ? 'Yesterday' : $date->format('M d, Y')) }}
        </h3>

        <div class="log-day-entries">
            @foreach($entries as $audit)
                @php
                    $actor = ($showsSalesActor || ! $audit->user?->isSales())
                        ? ($audit->user?->name ?? 'System')
                        : 'Sales';

                    // Same rule for the diff rows: the seller's identity is not
                    // operational data.
                    $blockedFields = $showsSalesActor ? [] : ['sales_user_id'];
                    if (! $showsSalesActor && $salesOwnedOrder) {
                        $blockedFields[] = 'created_by';
                    }

                    $changes = array_values(array_filter(
                        $audit->formattedChanges(),
                        fn (array $change) => ! in_array($change['field'], $blockedFields, true)
                    ));
                    $visibleCount = $audit->event === 'created' ? 0 : 3;
                    $visible = array_slice($changes, 0, $visibleCount);
                    $hidden = array_slice($changes, $visibleCount);
                @endphp
                <article class="log-entry" data-log-entry>
                    <time class="log-time" datetime="{{ $audit->created_at->toIso8601String() }}"
                          title="{{ $audit->created_at->format('M d, Y g:i:s A') }}">
                        {{ $audit->created_at->format('g:i') }}<span class="log-meridiem">{{ $audit->created_at->format('A') }}</span>
                    </time>

                    <div class="log-body">
                        <span class="log-dot tone-{{ $audit->eventTone() }}">
                            <i class="fas {{ $audit->eventIcon() }}"></i>
                        </span>

                        <p class="log-headline">
                            <span class="log-actor">{{ $actor }}</span>
                            {{ $audit->summary() }}
                        </p>

                        @if($changes)
                            <div class="log-changes">
                                @foreach($visible as $change)
                                    <div class="log-change">
                                        <span class="log-change-field">{{ $change['label'] }}</span>
                                        <span class="log-change-old">{{ $change['old'] }}</span>
                                        <i class="fas fa-arrow-right log-change-arrow"></i>
                                        <span class="log-change-new">{{ $change['new'] }}</span>
                                    </div>
                                @endforeach

                                @if($hidden)
                                    <div class="log-change-rest" hidden>
                                        @foreach($hidden as $change)
                                            <div class="log-change">
                                                <span class="log-change-field">{{ $change['label'] }}</span>
                                                <span class="log-change-old">{{ $change['old'] }}</span>
                                                <i class="fas fa-arrow-right log-change-arrow"></i>
                                                <span class="log-change-new">{{ $change['new'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>

                                    <button type="button" class="log-expand" data-log-expand
                                            data-more-label="{{ $audit->event === 'created' ? 'Show the '.count($hidden).' opening values' : 'Show '.count($hidden).' more' }}"
                                            data-less-label="Show less">
                                        {{ $audit->event === 'created' ? 'Show the '.count($hidden).' opening values' : 'Show '.count($hidden).' more' }}
                                    </button>
                                @endif
                            </div>
                        @endif

                        @if($audit->ip_address)
                            <p class="log-ip">from {{ $audit->ip_address }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endforeach
