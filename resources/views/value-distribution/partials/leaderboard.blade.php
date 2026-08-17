{{--
    Distribution achievement board. Shared by the server-rendered page and the
    AJAX lists endpoint.

    Expects: $leaderboard (collection of arrays), $monthDate (Carbon).
--}}
@if($leaderboard->isNotEmpty())
    <div class="vd-panel p-5" id="vdLeaderboard">
        <p class="vd-eyebrow mb-3"><i class="fas fa-trophy text-amber-500 mr-1"></i> Distribution achievement · {{ $monthDate->format('F') }}</p>
        @foreach($leaderboard as $index => $person)
            @php
                $tone = $person['achievement'] === null ? 'is-none' : ($person['achievement'] >= 100 ? 'is-met' : ($person['achievement'] >= 60 ? 'is-close' : 'is-behind'));
            @endphp
            <div class="vd-board-row">
                <span class="vd-board-rank {{ $index < 3 ? 'rank-'.($index + 1) : '' }}">{{ $index + 1 }}</span>
                <div class="min-w-0">
                    <p class="vd-board-name">{{ $person['name'] }}</p>
                    <div class="vd-progress">
                        <span class="{{ $tone }}" style="width: {{ $person['achievement'] === null ? 0 : min(100, $person['achievement']) }}%"></span>
                    </div>
                    <p class="vd-board-meta vd-mono">
                        {{ $person['orders'] }} {{ \Illuminate\Support\Str::plural('order', $person['orders']) }}
                        @if($person['achievement'] !== null)
                            · {{ $person['achievement'] }}% of ${{ number_format($person['target'], 2) }} target
                        @else
                            · no target set
                        @endif
                    </p>
                </div>
                <p class="vd-board-amount vd-mono">${{ number_format($person['amount'], 2) }}</p>
            </div>
        @endforeach
    </div>
@else
    <div class="vd-panel p-5 vd-empty" id="vdLeaderboard">
        <i class="fas fa-trophy text-2xl text-gray-300 dark:text-gray-700"></i>
        <p class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">No one credited yet</p>
        <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Distribute an order and this board fills in.</p>
    </div>
@endif