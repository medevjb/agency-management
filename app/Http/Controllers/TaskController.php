<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Mail\TaskAssignmentNotification;
use App\Models\Order;
use App\Models\Task;
use App\Models\TaskAudit;
use App\Models\User;
use App\Notifications\TaskUpdatedNotification;
use App\Services\EmailService;
use App\Services\TaskAssignmentService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Task::class);

        $user = auth()->user();

        // Get team member IDs if user is a team leader
        $teamMemberIds = [];
        if ($user->hasRole('team-leader') && $user->team_id) {
            $teamMemberIds = User::where('team_id', $user->team_id)->pluck('id')->toArray();
        }

        $visibleTo = $this->visibleTo($user, $teamMemberIds);

        // Get tasks based on user role
        $tasks = Task::with(['order', 'assignedTo', 'createdBy'])
            ->when(! $user->hasRole(['admin', 'operation-manager', 'project-manager']), $visibleTo)
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->priority, function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when($request->project_id, function ($query, $projectId) {
                $query->where('order_id', $projectId);
            })
            ->latest()
            ->get();

        $orders = Order::all();
        $users = User::all();

        // Status tallies in a single grouped query — the same role scope
        // applies, but one GROUP BY replaces four full COUNT scans.
        $counts = Task::query()
            ->when(! $user->hasRole(['admin', 'operation-manager', 'project-manager']), $visibleTo)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'total' => (int) $counts->sum(),
            'pending' => (int) ($counts['pending'] ?? 0),
            'in_progress' => (int) ($counts['in-progress'] ?? 0),
            'completed' => (int) ($counts['completed'] ?? 0),
        ];

        return view('tasks.index', compact('tasks', 'orders', 'users', 'stats'));
    }

    /**
     * The role-scoping applied to both the task list and its status tallies:
     * non-admin users only see tasks they are assigned to or created, plus
     * tasks on their team's / tech stack's orders.
     *
     * @param  array<int, int>  $teamMemberIds
     */
    private function visibleTo(User $user, array $teamMemberIds): Closure
    {
        return function ($query) use ($user, $teamMemberIds) {
            $query->where(function ($q) use ($user, $teamMemberIds) {
                $q->where(function ($query) use ($user) {
                    $query->where('assigned_to', $user->id)
                        ->orWhere('created_by', $user->id);
                });

                // Team leaders see their team members' tasks
                if ($user->hasRole('team-leader') && ! empty($teamMemberIds)) {
                    $q->orWhereIn('assigned_to', $teamMemberIds);
                }

                // OR tasks from their team's orders (if they have a team)
                if ($user->team_id) {
                    $q->orWhereHas('order', function ($orderQuery) use ($user) {
                        $orderQuery->where('team_id', $user->team_id);
                    });
                }

                // OR tasks from their tech stack orders (if they are stack-leader)
                if ($user->hasRole('stack-leader') && $user->tech_stack_id) {
                    $q->orWhereHas('order', function ($orderQuery) use ($user) {
                        $orderQuery->where('tech_stack_id', $user->tech_stack_id);
                    });
                }
            });
        };
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('tasks.index');
    }

    /**
     * Search orders for AJAX order picker.
     */
    public function searchOrders(Request $request): JsonResponse
    {
        $query = Order::query()->select(['id', 'name', 'status']);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        } else {
            $query->latest();
        }

        $orders = $query->take(10)->get();

        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Task::class);

        $user = auth()->user();
        $assignedTo = $request->assigned_to ?? $user->id;

        // Validate assignment
        if ($assignedTo != $user->id) {
            $assignee = User::find($assignedTo);
            if (! TaskAssignmentService::canAssignTo($user, $assignee)) {
                return back()->with('error', 'You are not authorized to assign tasks to this user.');
            }
        }

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'pending',
            'priority' => $request->priority ?? 'medium',
            'due_date' => $request->due_date,
            'estimated_hours' => $request->estimated_hours,
            'actual_hours' => $request->actual_hours,
            'order_id' => $request->order_id,
            'team_id' => TaskAssignmentService::getDefaultTeamId($user),
            'assigned_to' => $assignedTo,
            'created_by' => $user->id,
        ]);

        // Send email notification to assigned user
        if ($task->assigned_to && $task->assignedTo) {
            EmailService::sendTo($task->assignedTo->email, new TaskAssignmentNotification($task, $task->assignedTo));
        }

        // Load relationships for response
        $task->load('order', 'assignedTo', 'createdBy');

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Task created successfully!',
                'task' => $task,
                'redirect_url' => route('tasks.show', $task),
            ]);
        }

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): View|JsonResponse
    {
        $this->authorize('view', $task);

        $task->load(['order', 'assignedTo', 'createdBy']);

        // Return JSON for AJAX requests
        if (request()->expectsJson()) {
            return response()->json([
                'task' => $task,
            ]);
        }

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $task);

        if (! request()->expectsJson()) {
            return redirect()->route('tasks.index');
        }

        $task->load('order');

        return response()->json([
            'task' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $task);

        $user = auth()->user();
        $oldAssignedTo = $task->assigned_to;
        $data = $request->validated();

        // Validate reassignment
        if (isset($data['assigned_to']) && $data['assigned_to'] != $oldAssignedTo) {
            $assignee = User::find($data['assigned_to']);
            if (! TaskAssignmentService::canAssignTo($user, $assignee)) {
                return back()->with('error', 'You are not authorized to assign tasks to this user.');
            }
        }

        $task->update($data);

        // Send email notification to newly assigned user if assignment changed
        if ($task->assigned_to && $task->assigned_to !== $oldAssignedTo && $task->assignedTo) {
            EmailService::sendTo($task->assignedTo->email, new TaskAssignmentNotification($task, $task->assignedTo));
        }

        // Notify assigned user about important updates
        if ($task->assigned_to && $task->assigned_to !== $user->id) {
            if ($task->wasChanged('status')) {
                $task->assignedTo->notify(new TaskUpdatedNotification($task, 'status'));
            } elseif ($task->wasChanged('priority')) {
                $task->assignedTo->notify(new TaskUpdatedNotification($task, 'priority'));
            } elseif ($task->wasChanged('due_date')) {
                $task->assignedTo->notify(new TaskUpdatedNotification($task, 'due_date'));
            }
        }

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully!',
                'task' => $task,
            ]);
        }

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Update task status.
     */
    public function updateStatus(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        $this->authorize('updateStatus', $task);

        $user = auth()->user();

        // Team members (not leaders) can only submit or mark in-progress their own assigned tasks
        if ($user->hasRole('team-member') &&
            ! $user->hasRole(['team-leader', 'stack-leader']) &&
            $task->assigned_to === $user->id) {
            if ($request->status !== 'completed' && $request->status !== 'in-progress') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You can only submit tasks as completed or mark them as in progress.',
                    ], 403);
                }

                return back()->with('error', 'You can only submit tasks as completed or mark them as in progress.');
            }
        }

        $request->validate([
            'status' => 'required|in:pending,in-progress,completed,on-hold,archived',
        ]);

        $data = ['status' => $request->status];

        // Set completed_at when status changes to completed
        if ($request->status === 'completed' && $task->status !== 'completed') {
            $data['completed_at'] = now();
        } elseif ($request->status !== 'completed') {
            $data['completed_at'] = null;
        }

        $task->update($data);

        // Notify assigned user about status change
        if ($task->assigned_to && $task->assigned_to !== auth()->id()) {
            $task->assignedTo->notify(new TaskUpdatedNotification($task, 'status'));
        }

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Task status updated successfully!',
                'task' => $task,
            ]);
        }

        return back()->with('success', 'Task status updated successfully.');
    }

    /**
     * Bulk update tasks.
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'task_ids' => 'required|array',
            'task_ids.*' => 'exists:tasks,id',
            'status' => 'sometimes|required|in:pending,in-progress,completed,on-hold',
        ]);

        $taskIds = $request->input('task_ids');
        $data = $request->only(['status']);

        $tasks = Task::whereIn('id', $taskIds)->get();

        foreach ($tasks as $task) {
            $this->authorize('update', $task);
        }

        Task::whereIn('id', $taskIds)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Tasks updated successfully!',
        ]);
    }

    /**
     * Archive the specified task.
     */
    public function archive(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $task);

        $task->update([
            'status' => 'archived',
            'archived_at' => now(),
        ]);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Task archived successfully!',
                'task' => $task,
            ]);
        }

        return back()->with('success', 'Task archived successfully.');
    }

    /**
     * Unarchive the specified task.
     */
    public function unarchive(Request $request, Task $task): RedirectResponse|JsonResponse
    {
        $this->authorize('update', $task);

        $task->update([
            'status' => 'pending',
            'archived_at' => null,
        ]);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Task unarchived successfully!',
                'task' => $task,
            ]);
        }

        return back()->with('success', 'Task unarchived successfully.');
    }

    /**
     * Display archived tasks.
     */
    public function archived(): View
    {
        $this->authorize('viewAny', Task::class);

        $user = auth()->user();

        // Get team member IDs if user is a team leader
        $teamMemberIds = [];
        if ($user->hasRole('team-leader') && $user->team_id) {
            $teamMemberIds = User::where('team_id', $user->team_id)->pluck('id')->toArray();
        }

        $tasks = Task::with(['order', 'assignedTo', 'createdBy'])
            ->where('status', 'archived')
            ->when(! $user->hasRole(['admin', 'operation-manager', 'project-manager']), $this->visibleTo($user, $teamMemberIds))
            ->orderBy('archived_at', 'desc')
            ->get();

        $stats = [
            'total' => $tasks->count(),
        ];

        $orders = Order::all();
        $users = User::all();

        return view('tasks.archived', compact('tasks', 'stats', 'orders', 'users'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        // Return JSON for AJAX requests
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully!',
            ]);
        }

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Get assignable users for the current user.
     */
    public function getAssignableUsers(Request $request): JsonResponse
    {
        $user = auth()->user();
        $users = TaskAssignmentService::getAssignableUsers($user);

        return response()->json([
            'users' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ];
            }),
        ]);
    }

    /**
     * Get tasks for DataTable.
     */
    public function getTasks(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $user = auth()->user();

        // Get team member IDs if user is a team leader
        $teamMemberIds = [];
        if ($user->hasRole('team-leader') && $user->team_id) {
            $teamMemberIds = User::where('team_id', $user->team_id)->pluck('id')->toArray();
        }

        $query = Task::with(['order', 'assignedTo', 'createdBy'])
            ->when(! $user->hasRole(['admin', 'operation-manager', 'project-manager']), $this->visibleTo($user, $teamMemberIds))
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->priority, function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when($request->project_id, function ($query, $projectId) {
                $query->where('order_id', $projectId);
            })
            ->when($request->due_date, function ($query, $dueDate) {
                $query->whereDate('due_date', '>=', $dueDate);
            });

        // DataTables parameters
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search', []);
        $order = $request->get('order', []);
        $draw = $request->get('draw', 1);

        // Global search
        if (! empty($search['value'])) {
            $searchValue = $search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('title', 'like', "%{$searchValue}%")
                    ->orWhere('description', 'like', "%{$searchValue}%")
                    ->orWhere('status', 'like', "%{$searchValue}%")
                    ->orWhere('priority', 'like', "%{$searchValue}%");
            });
        }

        // Column-specific search (from filters)
        if ($request->has('columns')) {
            foreach ($request->get('columns') as $index => $column) {
                if (! empty($column['search']['value'])) {
                    $searchValue = $column['search']['value'];
                    $columnData = $column['data'];

                    switch ($columnData) {
                        case 'title':
                            $query->where('title', 'like', "%{$searchValue}%");
                            break;
                        case 'status':
                            $query->where('status', $searchValue);
                            break;
                        case 'priority':
                            $query->where('priority', $searchValue);
                            break;
                        case 'due_date':
                            $query->whereDate('due_date', '>=', $searchValue);
                            break;
                    }
                }
            }
        }

        // Ordering
        if (! empty($order)) {
            $columnIndex = $order[0]['column'];
            $columnDir = $order[0]['dir'];
            $columns = $request->get('columns');

            if (isset($columns[$columnIndex]['data'])) {
                $columnName = $columns[$columnIndex]['data'];

                // Validate column name to prevent SQL injection
                $allowedColumns = ['id', 'title', 'status', 'priority', 'due_date', 'created_at'];
                if (in_array($columnName, $allowedColumns)) {
                    $query->orderBy($columnName, $columnDir);
                }
            }
        } else {
            $query->latest('created_at');
        }

        // Get total records before pagination
        $recordsTotal = $query->count();
        $recordsFiltered = $recordsTotal;

        // Apply pagination
        if ($length != -1) {
            $tasks = $query->offset($start)->limit($length)->get();
        } else {
            $tasks = $query->get();
        }

        // Format data for DataTables
        $data = $tasks->map(function ($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status,
                'priority' => $task->priority,
                'due_date' => $task->due_date ? $task->due_date->format('M d, Y') : 'N/A',
                'assigned_to' => $task->assignedTo ? $task->assignedTo->name : 'Unassigned',
                'project' => $task->order ? $task->order->name : 'N/A',
                'created_at' => $task->created_at->format('M d, Y'),
                'actions' => view('tasks.partials.actions', ['task' => $task])->render(),
            ];
        });

        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Get users assigned to a task.
     */
    public function getUsers(Task $task): JsonResponse
    {
        $this->authorize('view', $task);

        $users = $task->users()->get(['users.id', 'users.name', 'users.email']);

        return response()->json([
            'users' => $users,
        ]);
    }

    /**
     * Assign users to a task.
     */
    public function assignUsers(Request $request, Task $task): JsonResponse
    {
        $this->authorize('manageAssignment', $task);

        $validated = $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        $oldMembers = $task->users()->pluck('name')->all();

        // Clear existing assignments and add new ones
        $task->users()->detach();

        foreach ($validated['users'] as $userId) {
            $task->users()->attach($userId, [
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
            ]);
        }

        $newMembers = $task->users()->pluck('name')->all();

        if ($oldMembers !== $newMembers) {
            TaskAudit::record($task, 'members_updated', ['members' => $oldMembers], ['members' => $newMembers]);
        }

        // Send notifications to newly assigned users
        $assignedUsers = User::whereIn('id', $validated['users'])->get();

        foreach ($assignedUsers as $assignedUser) {
            EmailService::sendTo($assignedUser->email, new TaskAssignmentNotification($task, $assignedUser));
        }

        return response()->json([
            'message' => 'User assignments updated successfully.',
            'users' => $task->users()->get(['users.id', 'users.name', 'users.email']),
        ]);
    }

    /**
     * Search for users to assign to a task.
     */
    public function searchUsers(Request $request): JsonResponse
    {
        $search = $request->input('search', '');
        $excludeIds = $request->input('exclude');

        // Convert comma-separated string to array if needed
        if (is_string($excludeIds)) {
            $excludeIds = explode(',', $excludeIds);
        }
        if (! is_array($excludeIds)) {
            $excludeIds = [];
        }

        // Filter out empty values
        $excludeIds = array_filter($excludeIds, 'strlen');
        $excludeIds = array_map('intval', $excludeIds);

        $user = auth()->user();

        $query = User::query();

        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            // Can see all users
        } else {
            $query->where('team_id', $user->team_id);
        }

        // Exclude already selected users
        if (! empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        // Apply search filter - search by name, email, employee_id, position
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        }

        // Limit results and include team relationship
        $users = $query->with('team:id,name')
            ->select('id', 'name', 'email', 'employee_id', 'position', 'team_id')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json([
            'users' => $users,
        ]);
    }
}
