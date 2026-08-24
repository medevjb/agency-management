@php
    $notificationColors = [
        'ember' => 'bg-ember-500/15 text-ember-500 dark:text-ember-400',
        'gold' => 'bg-gold-500/15 text-gold-500 dark:text-gold-400',
        'aqua' => 'bg-aqua-500/15 text-aqua-500 dark:text-aqua-400',
        'red' => 'bg-red-500/15 text-red-500 dark:text-red-400',
    ];
@endphp

@foreach($notifications as $notification)
    <a href="{{ route('notifications.open', $notification->id) }}"
        class="flex items-start gap-3 px-4 sm:px-6 py-3.5 border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/60 transition-colors {{ $notification->read_at ? '' : 'bg-ember-50/60 dark:bg-ember-500/5' }}">
        <span class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center {{ $notificationColors[$notification->data['color'] ?? 'ember'] ?? $notificationColors['ember'] }}">
            <i class="fas {{ $notification->data['icon'] ?? 'fa-bell' }} text-sm"></i>
        </span>
        <span class="flex-1 min-w-0">
            <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100">
                {{ $notification->data['title'] ?? 'Notification' }}
            </span>
            <span class="block text-sm text-gray-600 dark:text-gray-400 truncate">
                {{ $notification->data['message'] ?? '' }}
            </span>
            <span class="block text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                {{ $notification->created_at->diffForHumans() }}
            </span>
        </span>
        @unless($notification->read_at)
            <span class="flex-shrink-0 mt-2 w-2 h-2 rounded-full bg-ember-500" aria-label="Unread"></span>
        @endunless
    </a>
@endforeach
