<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Check if the task's assignee belongs to the user's team.
     */
    private function assigneeIsInUserTeam(User $user, Task $task): bool
    {
        if (! $user->team_id || ! $task->assigned_to) {
            return false;
        }

        $assignee = $task->assignedTo;

        return $assignee && $assignee->team_id === $user->team_id;
    }

    /**
     * All authenticated users can access the tasks list.
     * Data scoping is handled in the controller.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Admin/OM/PM see all tasks.
     * Team leader sees tasks from their team (by task team_id, order team_id, or assignee team).
     * Stack leader sees tasks from their tech stack orders.
     * Team member sees tasks assigned to them, created by them, or from their team.
     */
    public function view(User $user, Task $task): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($task->assigned_to === $user->id || $task->created_by === $user->id) {
            return true;
        }

        $order = $task->order;

        if ($user->hasRole('team-leader') && $user->team_id) {
            if ($task->team_id === $user->team_id) {
                return true;
            }
            if ($order && $order->team_id === $user->team_id) {
                return true;
            }
            if ($this->assigneeIsInUserTeam($user, $task)) {
                return true;
            }
        }

        if ($user->hasRole('stack-leader')) {
            if ($order && $order->tech_stack_id) {
                $userStackIds = $user->techStacks()->pluck('tech_stacks.id')->toArray();
                if (in_array($order->tech_stack_id, $userStackIds)) {
                    return true;
                }
            }
        }

        if ($user->team_id && $task->team_id === $user->team_id) {
            return true;
        }

        if ($user->team_id && $order && $order->team_id === $user->team_id) {
            return true;
        }

        if ($this->assigneeIsInUserTeam($user, $task)) {
            return true;
        }

        return false;
    }

    /**
     * Admin/OM/PM can create tasks for anyone.
     * Team leader can create for themselves and team members.
     * Stack leader can create for themselves and stack members.
     * Team member can create for themselves.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Admin/OM/PM can update any task.
     * Team leader can update tasks in their team (by task team_id, order team_id, or assignee team).
     * Stack leader can update tasks related to their stack.
     * Creator/assignee can update their own task.
     * Team members can update tasks within their own team.
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        if ($task->created_by === $user->id || $task->assigned_to === $user->id) {
            return true;
        }

        $order = $task->order;

        if ($user->hasRole('team-leader') && $user->team_id) {
            if ($task->team_id === $user->team_id) {
                return true;
            }
            if ($order && $order->team_id === $user->team_id) {
                return true;
            }
            if ($this->assigneeIsInUserTeam($user, $task)) {
                return true;
            }
        }

        if ($user->hasRole('stack-leader')) {
            if ($order && $order->tech_stack_id) {
                $userStackIds = $user->techStacks()->pluck('tech_stacks.id')->toArray();
                if (in_array($order->tech_stack_id, $userStackIds)) {
                    return true;
                }
            }
        }

        if ($user->team_id && $task->team_id === $user->team_id) {
            return true;
        }

        if ($user->team_id && $order && $order->team_id === $user->team_id) {
            return true;
        }

        if ($this->assigneeIsInUserTeam($user, $task)) {
            return true;
        }

        return false;
    }

    /**
     * Admin/OM/PM can delete any task.
     * Team leader can delete tasks in their team.
     * Creator can delete their own task.
     */
    public function delete(User $user, Task $task): bool
    {
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            return true;
        }

        $order = $task->order;

        if ($user->hasRole('team-leader') && $user->team_id) {
            if ($task->team_id === $user->team_id) {
                return true;
            }
            if ($order && $order->team_id === $user->team_id) {
                return true;
            }
            if ($this->assigneeIsInUserTeam($user, $task)) {
                return true;
            }
        }

        if ($task->created_by === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Admin/OM/PM can change any status.
     * Team leader can change status for team tasks.
     * Stack leader can change status for stack tasks.
     * Creator/assignee can update status on their own tasks.
     */
    public function updateStatus(User $user, Task $task): bool
    {
        return $this->update($user, $task);
    }

    public function restore(User $user, Task $task): bool
    {
        return $user->hasRole(['admin', 'operation-manager', 'project-manager']);
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return $user->hasRole('admin');
    }
}
