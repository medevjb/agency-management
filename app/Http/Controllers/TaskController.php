<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Order;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskUpdatedNotification;
use App\Services\TaskAssignmentService;
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

        // Get tasks based on user role
        $tasks = Task::with(['order', 'assignedTo', 'createdBy'])
            ->when(! $user->hasRole(['admin', 'operation-manager', 'project-manager']), function ($query) use ($user) {
                // For non-admin users, show tasks they are assigned to or created, OR tasks from their team's orders
                $query->where(function ($q) use ($user) {
                    $q->where(function ($query) use ($user) {
                        // Tasks assigned to or created by the user
                        $query->where('assigned_to', $user->id)
                            ->orWhere('created_by', $user->id);
                    });

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
            })
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

        $stats = [
            'total' => $tasks->count(),
            'pending' => Task::where('status', 'pending')
                ->when(! $user->hasRole(['admin', 'operation-manager', 'project-manager']), function ($q) use ($user) {
                    if ($user->hasRole('team-leader') && $user->team_id) {
                        $q->whereHas('order', function ($query) use ($user) {
                            $query->where('team_id', $user->team_id);
                        });
                    } elseif ($user->hasRole('stack-leader') && $user->tech_stack_id) {
                        $q->whereHas('order', function ($query) use ($user) {
                            $query->where('tech_stack_id', $user->tech_stack_id);
                        });
                    } elseif ($user->team_id) {
                        $q->whereHas('order', function ($query) use ($user) {
                            $query->where('team_id', $user->team_id);
                        });
                    } else {
                        $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
                    }
                })->count(),
            'in_progress' => Task::where('status', 'in-progress')
                ->when(! $user->hasRole(['admin', 'operation-manager', 'project-manager']), function ($q) use ($user) {
                    if ($user->hasRole('team-leader') && $user->team_id) {
                        $q->whereHas('order', function ($query) use ($user) {
                            $query->where('team_id', $user->team_id);
                        });
                    } elseif ($user->hasRole('stack-leader') && $user->tech_stack_id) {
                        $q->whereHas('order', function ($query) use ($user) {
                            $query->where('tech_stack_id', $user->tech_stack_id);
                        });
                    } elseif ($user->team_id) {
                        $q->whereHas('order', function ($query) use ($user) {
                            $query->where('team_id', $user->team_id);
                        });
                    } else {
                        $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
                    }
                })->count(),
            'completed' => Task::where('status', 'completed')
                ->when(! $user->hasRole(['admin', 'operation-manager', 'project-manager']), function ($q) use ($user) {
                    if ($user->hasRole('team-leader') && $user->team_id) {
                        $q->whereHas('order', function ($query) use ($user) {
                            $query->where('team_id', $user->team_id);
                        });
                    } elseif ($user->hasRole('stack-leader') && $user->tech_stack_id) {
                        $q->whereHas('order', function ($query) use ($user) {
                            $query->where('tech_stack_id', $user->tech_stack_id);
                        });
                    } elseif ($user->team_id) {
                        $q->whereHas('order', function ($query) use ($user) {
                            $query->where('team_id', $user->team_id);
                        });
                    } else {
                        $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
                    }
                })->count(),
        ];

        return view('tasks.index', compact('tasks', 'orders', 'users', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Task::class);

        $user = auth()->user();
        $orders = Order::all();
        $users = TaskAssignmentService::getAssignableUsers($user);

        return view('tasks.create', compact('orders', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
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

        // Send notification to assigned user
        if ($task->assigned_to && $task->assigned_to !== $user->id) {
            $task->assignedTo->notify(new TaskAssignedNotification($task));
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
    public function show(Task $task): View|\Illuminate\Http\JsonResponse
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
    public function edit(Task $task): View|\Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $task);

        $user = auth()->user();
        $orders = Order::all();
        $users = TaskAssignmentService::getAssignableUsers($user);

        // Return JSON for AJAX requests (modal)
        if (request()->expectsJson()) {
            return response()->json([
                'task' => $task,
                'orders' => $orders,
                'users' => $users,
            ]);
        }

        return view('tasks.edit', compact('task', 'orders', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
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

        // Notify newly assigned user if assignment changed
        if ($task->assigned_to && $task->assigned_to !== $oldAssignedTo && $task->assigned_to !== $user->id) {
            $task->assignedTo->notify(new TaskAssignedNotification($task));
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
    public function updateStatus(Request $request, Task $task): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $task);

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
    public function bulkUpdate(Request $request): \Illuminate\Http\JsonResponse
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
    public function archive(Request $request, Task $task): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
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
    public function unarchive(Request $request, Task $task): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
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

        $tasks = Task::with(['order', 'assignedTo'])
            ->where('status', 'archived')
            ->when(! $user->hasRole(['admin', 'operation-manager', 'project-manager']), function ($query) use ($user) {
                if ($user->hasRole('stack-leader')) {
                    $query->where('tech_stack_id', $user->tech_stack_id);
                } else {
                    $query->where('team_id', $user->team_id);
                }
            })
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
    public function destroy(Task $task): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
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
    public function getAssignableUsers(Request $request): \Illuminate\Http\JsonResponse
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

        $query = Task::with(['order', 'assignedTo', 'createdBy'])
            ->when(! $user->hasRole(['admin', 'operation-manager', 'project-manager']), function ($query) use ($user) {
                // For non-admin users, show tasks they are assigned to or created, OR tasks from their team's orders
                $query->where(function ($q) use ($user) {
                    $q->where(function ($query) use ($user) {
                        // Tasks assigned to or created by the user
                        $query->where('assigned_to', $user->id)
                            ->orWhere('created_by', $user->id);
                    });

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
            })
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
                'project' => $task->project ? $task->project->name : 'N/A',
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
}
