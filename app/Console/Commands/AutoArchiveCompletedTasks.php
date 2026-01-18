<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;

class AutoArchiveCompletedTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:auto-archive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically archive completed tasks older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Auto-archiving completed tasks...');

        // Find completed tasks older than 24 hours that aren't already archived
        $tasks = Task::where('status', 'completed')
            ->whereNotNull('completed_at')
            ->where('completed_at', '<', now()->subHours(24))
            ->get();

        $count = 0;
        foreach ($tasks as $task) {
            $task->update([
                'status' => 'archived',
                'archived_at' => now(),
            ]);
            $count++;
        }

        $this->info("Successfully archived {$count} task(s).");

        return Command::SUCCESS;
    }
}
