<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
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
        $tasks = Task::with(['project', 'assignedTo', 'createdBy'])
            ->when(! $user->hasRole(['admin', 'operation-manager', 'project-manager']), function ($query) use ($user) {
                if ($user->hasRole('team-leader') && $user->team_id) {
                    // Team leaders see tasks from their team's orders
                    $query->whereHas('order', function ($q) use ($user) {
                        $q->where('team_id', $user->team_id);
                    });
                } elseif ($user->hasRole('stack-leader') && $user->tech_stack_id) {
                    // Stack leaders see tasks from orders that use their tech stack
                    $query->whereHas('order', function ($q) use ($user) {
                        $q->where('tech_stack_id', $user->tech_stack_id);
                    });
                } else {
                    // Team members see all tasks from their team's orders
                    if ($user->team_id) {
                        $query->whereHas('order', function ($q) use ($user) {
                            $q->where('team_id', $user->team_id);
                        });
                    } else {
                        // Fallback to assigned/created tasks if no team
                        $query->where(function ($q) use ($user) {
                            $q->where('assigned_to', $user->id)
                                ->orWhere('created_by', $user->id);
                        });
                    }
                }
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->priority, function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when($request->project_id, function ($query, $projectId) {
                $query->where('project_id', $projectId);
            })
            ->latest()
            ->paginate(10);

        $projects = Project::all();
        $users = User::all();

        $stats = [
            'total' => $tasks->total(),
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

        return view('tasks.index', compact('tasks', 'projects', 'users', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Task::class);

        $projects = Project::all();
        $users = User::all();

        return view('tasks.create', compact('projects', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $this->authorize('create', Task::class);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
            'priority' => $request->priority ?? 'medium',
            'due_date' => $request->due_date,
            'estimated_hours' => $request->estimated_hours,
            'project_id' => $request->project_id,
            'assigned_to' => $request->assigned_to ?? auth()->id(),
            'created_by' => auth()->id(),
        ]);

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
    public function show(Task $task): View
    {
        $this->authorize('view', $task);

        $task->load(['project', 'assignedTo', 'createdBy']);

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): View|\Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $task);

        // Return JSON for AJAX requests (modal)
        if (request()->expectsJson()) {
            return response()->json([
                'task' => $task,
                'projects' => Project::all(),
                'users' => User::all(),
            ]);
        }

        $projects = Project::all();
        $users = User::all();

        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

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
    public function updateStatus(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'status' => 'required|in:pending,in-progress,completed,on-hold',
        ]);

        $task->update(['status' => $request->status]);

        return back()->with('success', 'Task status updated successfully.');
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
     * Get tasks for DataTable.
     */
    public function getTasks(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $user = auth()->user();

        $query = Task::with(['project', 'assignedTo', 'createdBy'])
            ->when(! $user->hasRole(['admin', 'operation-manager', 'project-manager']), function ($query) use ($user) {
                if ($user->hasRole('team-leader') && $user->team_id) {
                    // Team leaders see tasks from their team's orders
                    $query->whereHas('order', function ($q) use ($user) {
                        $q->where('team_id', $user->team_id);
                    });
                } elseif ($user->hasRole('stack-leader') && $user->tech_stack_id) {
                    // Stack leaders see tasks from orders that use their tech stack
                    $query->whereHas('order', function ($q) use ($user) {
                        $q->where('tech_stack_id', $user->tech_stack_id);
                    });
                } else {
                    // Team members see all tasks from their team's orders
                    if ($user->team_id) {
                        $query->whereHas('order', function ($q) use ($user) {
                            $q->where('team_id', $user->team_id);
                        });
                    } else {
                        // Fallback to assigned/created tasks if no team
                        $query->where(function ($q) use ($user) {
                            $q->where('assigned_to', $user->id)
                                ->orWhere('created_by', $user->id);
                        });
                    }
                }
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->priority, function ($query, $priority) {
                $query->where('priority', $priority);
            })
            ->when($request->project_id, function ($query, $projectId) {
                $query->where('project_id', $projectId);
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
