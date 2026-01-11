<?php

namespace App\Policies;

use App\Models\BugReport;
use App\Models\User;

class BugReportPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins can view all bug reports
        if ($user->hasRole('admin')) {
            return true;
        }

        // All authenticated users can view their own bug reports
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BugReport $bugReport): bool
    {
        // Admins can view any bug report
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users can view their own bug reports
        return $bugReport->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create bug reports
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BugReport $bugReport): bool
    {
        // Only admins can update bug reports (change status, add admin notes)
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BugReport $bugReport): bool
    {
        // Only admins can delete bug reports
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BugReport $bugReport): bool
    {
        // Only admins can restore bug reports
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BugReport $bugReport): bool
    {
        // Only admins can force delete bug reports
        return $user->hasRole('admin');
    }
}
