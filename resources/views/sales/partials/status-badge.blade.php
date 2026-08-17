@php
    // Three accents only: emerald is money earned, indigo is the goal being
    // pursued, amber is still accruing. Gray carries settled history.
    $statuses = [
        'achieved' => ['label' => 'Target Achieved', 'icon' => 'fa-check-circle', 'tone' => 'is-emerald'],
        'bonus-active' => ['label' => 'Bonus Active', 'icon' => 'fa-bolt', 'tone' => 'is-emerald'],
        'in-progress' => ['label' => 'In Progress', 'icon' => 'fa-spinner', 'tone' => 'is-indigo'],
        'upcoming' => ['label' => 'Upcoming', 'icon' => 'fa-clock', 'tone' => 'is-gray'],
        'accruing' => ['label' => 'Accruing', 'icon' => 'fa-hourglass-half', 'tone' => 'is-amber'],
        'paid' => ['label' => 'Paid', 'icon' => 'fa-circle-check', 'tone' => 'is-gray'],
    ];

    $current = $statuses[$status] ?? ['label' => $status, 'icon' => 'fa-circle', 'tone' => 'is-gray'];
@endphp

<span class="s-badge {{ $current['tone'] }}">
    <i class="fas {{ $current['icon'] }} text-[9px]"></i>{{ $current['label'] }}
</span>
