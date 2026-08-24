<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    protected const PER_PAGE = 15;

    /**
     * Display the full, paginated notifications panel.
     */
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->paginate(self::PER_PAGE);

        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Return the next chunk of notifications as rendered HTML, for the
     * panel's "Load more" button.
     */
    public function loadMore(Request $request): JsonResponse
    {
        $notifications = $request->user()->notifications()->paginate(self::PER_PAGE);

        return response()->json([
            'html' => view('notifications._items', ['notifications' => $notifications])->render(),
            'has_more' => $notifications->hasMorePages(),
            'next_page' => $notifications->currentPage() + 1,
        ]);
    }

    /**
     * Mark a single notification read and redirect to what it's about.
     */
    public function open(Request $request, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);

        if (! $notification->read_at) {
            $notification->markAsRead();
        }

        return redirect($notification->data['url'] ?? route('notifications.index'));
    }

    /**
     * Mark every unread notification read.
     */
    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }
}
