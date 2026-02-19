<?php

namespace App\Policies;

use App\Models\BugReport;
use App\Models\User;

class BugReportPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BugReport $bugReport): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        return $bugReport->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Admin/OM/PM can update any bug report (change status, add notes).
     */
    public function update(User $user, BugReport $bugReport): bool
    {
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    public function delete(User $user, BugReport $bugReport): bool
    {
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    public function restore(User $user, BugReport $bugReport): bool
    {
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    public function forceDelete(User $user, BugReport $bugReport): bool
    {
        return $user->hasRole('admin');
    }
}
