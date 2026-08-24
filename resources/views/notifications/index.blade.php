@extends('layouts.dashboard')

@section('content')
<div class="p-6 max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Notifications</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Everything relevant to your role, most recent first.</p>
        </div>
        @if($notifications->total() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit"
                    class="inline-flex items-center text-sm font-medium text-ember-600 dark:text-ember-400 hover:text-ember-700 dark:hover:text-ember-300">
                    <i class="fas fa-check-double mr-1.5"></i> Mark all read
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div id="notifications-list">
            @if($notifications->isEmpty())
                <div class="px-6 py-16 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-bell-slash text-3xl mb-3 opacity-40"></i>
                    <p>No notifications yet.</p>
                </div>
            @else
                @include('notifications._items', ['notifications' => $notifications])
            @endif
        </div>
    </div>

    @if($notifications->hasMorePages())
        <div class="text-center">
            <button id="load-more-btn" type="button" data-next-page="{{ $notifications->currentPage() + 1 }}"
                class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                Load more
            </button>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('load-more-btn');
        if (!btn) {
            return;
        }

        btn.addEventListener('click', function () {
            const page = btn.dataset.nextPage;
            const originalLabel = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Loading…';

            fetch(`{{ route('notifications.load-more') }}?page=${page}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
                .then((response) => response.json())
                .then((data) => {
                    document.getElementById('notifications-list').insertAdjacentHTML('beforeend', data.html);

                    if (data.has_more) {
                        btn.dataset.nextPage = data.next_page;
                        btn.disabled = false;
                        btn.textContent = originalLabel;
                    } else {
                        btn.remove();
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    btn.textContent = originalLabel;
                });
        });
    });
</script>
@endpush
@endsection
