<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class NotificationRecipients
{
    /**
     * Admin, operation-manager, and project-manager hold global oversight of
     * every order and bug report (see OrderPolicy/BugReportPolicy) — they are
     * the audience for events that need management attention.
     *
     * Spatie's role() scope throws if a role name doesn't exist at all yet
     * (e.g. roles not seeded in a test or a fresh environment) — that must
     * never abort the action that triggered the notification, so we only
     * query roles we've confirmed exist.
     *
     * @return Collection<int, User>
     */
    public static function management(): Collection
    {
        $roles = Role::whereIn('name', ['admin', 'operation-manager', 'project-manager'])->pluck('name');

        if ($roles->isEmpty()) {
            return new Collection;
        }

        return User::role($roles->all())->get();
    }
}
