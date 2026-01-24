<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskDueSoonNotification;
use Illuminate\Console\Command;

class SendDueTaskNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-due-task-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications to users for tasks due within 3 days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for tasks due soon...');

        // Get tasks due within 3 days that are not completed or archived
        $tasks = Task::with('assignedTo')
            ->whereIn('status', ['pending', 'in-progress', 'on-hold'])
            ->whereNotNull('assigned_to')
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [now(), now()->addDays(3)])
            ->get();

        $notificationsSent = 0;

        foreach ($tasks as $task) {
            $daysRemaining = now()->diffInDays($task->due_date, false);

            // Only send notification if we haven't already sent one today
            $cacheKey = "task_due_notification_{$task->id}_{$task->assigned_to}_".now()->format('Y-m-d');

            if (! cache()->has($cacheKey)) {
                $task->assignedTo->notify(new TaskDueSoonNotification($task, $daysRemaining));

                // Cache for 24 hours to avoid duplicate notifications
                cache()->put($cacheKey, true, now()->endOfDay());

                $notificationsSent++;
                $this->info("Notification sent for task: {$task->title} (Due in {$daysRemaining} days)");
            }
        }

        $this->info("Total notifications sent: {$notificationsSent}");

        return self::SUCCESS;
    }
}
