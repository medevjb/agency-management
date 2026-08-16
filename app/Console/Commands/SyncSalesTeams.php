<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SalesTeamSync;
use Illuminate\Console\Command;

class SyncSalesTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:sync-teams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill team_id for existing sales leaders and members, so the Teams pages show their sales group';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $users = User::withRoleName(['sales-leader', 'sales-member'])->get();

        $synced = 0;
        foreach ($users as $user) {
            $before = $user->team_id;
            SalesTeamSync::sync($user->fresh());

            if ($user->fresh()->team_id !== $before) {
                $synced++;
            }
        }

        $this->info("Checked {$users->count()} sales user(s), updated {$synced}.");

        return Command::SUCCESS;
    }
}
