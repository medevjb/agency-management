<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;

class SalesTeamSync
{
    /**
     * A sales leader and their downline share one Team row, created lazily
     * the first time it's needed, so the generic (view-only) Team pages
     * show something real instead of "no team" for a sales member who only
     * ever had a sales_leader_id. Selecting a leader for a member — or a
     * user becoming a sales leader — assigns everyone involved to it.
     */
    public static function sync(User $user): void
    {
        $leader = $user->isSalesLeader() ? $user : $user->salesLeader;

        if (! $leader) {
            return;
        }

        $team = Team::firstOrCreate(
            ['team_leader_id' => $leader->id],
            ['name' => $leader->name."'s Sales Team"]
        );

        if ($leader->team_id !== $team->id) {
            $leader->update(['team_id' => $team->id]);
        }

        if ($user->id !== $leader->id && $user->team_id !== $team->id) {
            $user->update(['team_id' => $team->id]);
        }
    }
}
