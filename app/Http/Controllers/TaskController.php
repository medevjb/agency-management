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
        $user = auth()->user();

        // Get tasks based on user role
        $tasks = Task::with(['project', 'assignedTo', 'createdBy'])
            ->when($user->role !== 'admin', function ($query) use ($user) {
                // Non-admin users see their assigned tasks + tasks they created
                $query->where(function ($q) use ($user) {
                    $q->where('assigned_to', $user->id)
                        ->orWhere('created_by', $user->id);
                });
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
            'pending' => Task::where('status', 'pending')->when($user->role !== 'admin', function ($q) use ($user) {
                $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
            })->count(),
            'in_progress' => Task::where('status', 'in-progress')->when($user->role !== 'admin', function ($q) use ($user) {
                $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
            })->count(),
            'completed' => Task::where('status', 'completed')->when($user->role !== 'admin', function ($q) use ($user) {
                $q->where('assigned_to', $user->id)->orWhere('created_by', $user->id);
            })->count(),
        ];

        return view('tasks.index', compact('tasks', 'projects', 'users', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $projects = Project::all();
        $users = User::all();

        return view('tasks.create', compact('projects', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
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

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): View
    {
        $this->authorizeView($task);

        $task->load(['project', 'assignedTo', 'createdBy']);

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): View
    {
        $this->authorizeView($task);

        $projects = Project::all();
        $users = User::all();

        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorizeView($task);

        $task->update($request->validated());

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Update task status.
     */
    public function updateStatus(Request $request, Task $task)
    {
        $this->authorizeView($task);

        $request->validate([
            'status' => 'required|in:pending,in-progress,completed,on-hold',
        ]);

        $task->update(['status' => $request->status]);

        return back()->with('success', 'Task status updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $this->authorizeView($task);

        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Get tasks for DataTable.
     */
    public function getTasks(Request $request)
    {
        $user = auth()->user();

        $tasks = Task::with(['project', 'assignedTo', 'createdBy'])
            ->when($user->role !== 'admin', function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('assigned_to', $user->id)
                        ->orWhere('created_by', $user->id);
                });
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
            ->get();

        return response()->json(['data' => $tasks]);
    }

    /**
     * Authorize view - check if user can view/manage the task.
     */
    protected function authorizeView(Task $task): void
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return;
        }

        // Users can see tasks they created or are assigned to
        if ($task->assigned_to !== $user->id && $task->created_by !== $user->id) {
            abort(403, 'You do not have permission to access this task.');
        }
    }
}
