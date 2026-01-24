<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin, Operation Manager, Project Manager can view all tasks
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        // Team leaders can view their team's tasks
        if ($user->hasRole('team-leader') && $user->team_id) {
            return true;
        }

        // Stack leaders can view tasks using their tech stack
        if ($user->hasRole('stack-leader') && $user->tech_stack_id) {
            return true;
        }

        // Team members can view their team's tasks
        if ($user->team_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // Admin, Operation Manager, Project Manager can view any task
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        // Get the order for this task
        $order = $task->order;

        if (! $order) {
            return false;
        }

        // Team leaders can view tasks from their team's orders
        if ($user->hasRole('team-leader') && $user->team_id === $order->team_id) {
            return true;
        }

        // Stack leaders can view tasks from orders that use their tech stack
        if ($user->hasRole('stack-leader') && $user->tech_stack_id === $order->tech_stack_id) {
            return true;
        }

        // Team members can view tasks from their team's orders
        if ($user->team_id === $order->team_id) {
            return true;
        }

        // Users assigned to the task can view it
        if ($task->assigned_to === $user->id) {
            return true;
        }

        // Users who created the task can view it
        if ($task->created_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin, Operation Manager, Project Manager can create tasks
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        // Team leaders can create tasks for their team
        if ($user->hasRole('team-leader')) {
            return true;
        }

        // Team members can create their own tasks
        if ($user->hasRole('team-member') && $user->team_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // Admin, Operation Manager, Project Manager can update any task
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        // Get the order for this task
        $order = $task->order;

        // Team leaders can update tasks from their team's orders
        if ($user->hasRole('team-leader') && $order && $user->team_id === $order->team_id) {
            return true;
        }

        // Stack leaders can update tasks from orders that use their tech stack
        if ($user->hasRole('stack-leader') && $order && $user->tech_stack_id === $order->tech_stack_id) {
            return true;
        }

        // Team members can update their own tasks only
        if ($user->hasRole('team-member') && $task->assigned_to === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        // Admin, Operation Manager, Project Manager can delete any task
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        // Get the order for this task
        $order = $task->order;

        // Team leaders can delete tasks from their team's orders
        if ($user->hasRole('team-leader') && $order && $user->team_id === $order->team_id) {
            return true;
        }

        // Stack leaders can delete tasks from orders that use their tech stack
        if ($user->hasRole('stack-leader') && $order && $user->tech_stack_id === $order->tech_stack_id) {
            return true;
        }

        // Team members cannot delete tasks
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        // Admin, Operation Manager, Project Manager can restore tasks
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        // Only admin can force delete tasks
        return $user->hasRole('admin');
    }
}
