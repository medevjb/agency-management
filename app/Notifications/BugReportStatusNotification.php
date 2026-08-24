<?php

namespace App\Notifications;

use App\Models\BugReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BugReportStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public BugReport $bugReport, public string $oldStatus) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Bug report status updated',
            'message' => $this->bugReport->title.' is now '.str_replace('_', ' ', $this->bugReport->status),
            'icon' => 'fa-bug',
            'color' => 'aqua',
            'url' => route('bug-reports.show', $this->bugReport),
        ];
    }
}
