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
        // Admins can view all tasks
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers can view tasks from teams they manage
        if ($user->isManager()) {
            return true;
        }

        // Team leaders, stack leaders, and members can view tasks from their team
        return $user->team_id !== null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // Admins can view any task
        if ($user->hasRole('admin')) {
            return true;
        }

        // Get the order for this task
        $order = $task->order;

        if (! $order) {
            return false;
        }

        // Managers can view tasks from teams they manage
        if ($user->isManager() && $order->team && $user->isManagerOfTeam($order->team)) {
            return true;
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
        // Admins can create tasks
        if ($user->hasRole('admin')) {
            return true;
        }

        // Managers can create tasks
        if ($user->isManager()) {
            return true;
        }

        // Team leaders can create tasks
        if ($user->hasRole('team-leader')) {
            return true;
        }

        // Team members can create tasks
        return $user->team_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        // Admins can update any task
        if ($user->hasRole('admin')) {
            return true;
        }

        // Get the order for this task
        $order = $task->order;

        if (! $order) {
            return false;
        }

        // Managers can update tasks from teams they manage
        if ($user->isManager() && $order->team && $user->isManagerOfTeam($order->team)) {
            return true;
        }

        // Team leaders can update tasks from their team's orders
        if ($user->hasRole('team-leader') && $user->team_id === $order->team_id) {
            return true;
        }

        // Stack leaders can update tasks from orders that use their tech stack
        if ($user->hasRole('stack-leader') && $user->tech_stack_id === $order->tech_stack_id) {
            return true;
        }

        // Users assigned to the task can update it
        if ($task->assigned_to === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        // Admins can delete any task
        if ($user->hasRole('admin')) {
            return true;
        }

        // Get the order for this task
        $order = $task->order;

        if (! $order) {
            return false;
        }

        // Managers can delete tasks from teams they manage
        if ($user->isManager() && $order->team && $user->isManagerOfTeam($order->team)) {
            return true;
        }

        // Team leaders can delete tasks from their team's orders
        if ($user->hasRole('team-leader') && $user->team_id === $order->team_id) {
            return true;
        }

        // Stack leaders can delete tasks from orders that use their tech stack
        if ($user->hasRole('stack-leader') && $user->tech_stack_id === $order->tech_stack_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        // Only admins can restore tasks
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        // Only admins can force delete tasks
        return $user->hasRole('admin');
    }
}
