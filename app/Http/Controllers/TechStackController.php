<?php

namespace App\Http\Controllers;

use App\Models\TechStack;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TechStackController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', TechStack::class);

        $user = auth()->user();

        $techStacks = TechStack::with(['stackLeader', 'members', 'projects'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            })
            ->when($request->filled('leader'), function ($q) use ($request) {
                $q->where('stack_leader_id', $request->leader);
            })
            ->when(! $user->hasRole(['admin', 'stack-leader']), function ($q) use ($user) {
                // Team leaders see tech stacks used by their team's orders
                if ($user->hasRole('team-leader') && $user->team_id) {
                    $q->whereHas('orders', function ($query) use ($user) {
                        $query->where('team_id', $user->team_id);
                    });
                } else {
                    // Team members see their assigned tech stack
                    if ($user->tech_stack_id) {
                        $q->where('id', $user->tech_stack_id);
                    } else {
                        $q->whereRaw('1 = 0'); // Show nothing if no tech stack assigned
                    }
                }
            })
            ->latest()
            ->paginate(10);

        // Get all unique stack leaders for filter
        $leaders = User::whereIn('id', TechStack::whereNotNull('stack_leader_id')->pluck('stack_leader_id'))
            ->orderBy('name')
            ->get();

        return view('tech-stacks.index', compact('techStacks', 'leaders'));
    }

    /**
     * Get tech stacks data for DataTable.
     */
    public function getTechStacks(Request $request): JsonResponse
    {
        $this->authorize('viewAny', TechStack::class);

        $user = auth()->user();

        $query = TechStack::with(['stackLeader', 'members', 'projects'])
            ->when(! $user->hasRole(['admin', 'stack-leader']), function ($q) use ($user) {
                // Team leaders see tech stacks used by their team's orders
                if ($user->hasRole('team-leader') && $user->team_id) {
                    $q->whereHas('orders', function ($query) use ($user) {
                        $query->where('team_id', $user->team_id);
                    });
                } else {
                    // Team members see their assigned tech stack
                    if ($user->tech_stack_id) {
                        $q->where('id', $user->tech_stack_id);
                    } else {
                        $q->whereRaw('1 = 0'); // Show nothing if no tech stack assigned
                    }
                }
            });

        return DataTables::of($query)
            ->editColumn('name', function ($techStack) {
                return [
                    'name' => $techStack->name,
                    'description' => $techStack->description,
                ];
            })
            ->editColumn('stack_leader', function ($techStack) {
                $leader = $techStack->stackLeader;

                return $leader ? [
                    'name' => $leader->name,
                    'employee_id' => $leader->employee_id,
                ] : null;
            })
            ->addColumn('members_count', function ($techStack) {
                $count = $techStack->members->count();
                $activeCount = $techStack->members->where('is_active', true)->count();

                return $count.' '.$activeCount.' active';
            })
            ->addColumn('projects_count', function ($techStack) {
                $count = $techStack->projects->count();
                $activeCount = $techStack->projects->whereIn('status', ['planning', 'in-progress'])->count();

                return $count.' '.$activeCount.' active';
            })
            ->orderColumn('members_count', function ($query, $order) {
                $query->withCount('members')->orderBy('members_count', $order);
            })
            ->orderColumn('projects_count', function ($query, $order) {
                $query->withCount('projects')->orderBy('projects_count', $order);
            })
            ->setRowId('id')
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', TechStack::class);

        $users = User::where('is_active', true)->get();

        return view('tech-stacks.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', TechStack::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tech_stacks',
            'description' => 'nullable|string',
            'stack_leader_id' => 'nullable|exists:users,id',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $techStack = TechStack::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'stack_leader_id' => $validated['stack_leader_id'],
        ]);

        // Assign stack leader role if specified
        if ($validated['stack_leader_id']) {
            $leader = User::find($validated['stack_leader_id']);
            $leader->update(['tech_stack_id' => $techStack->id]);
            if (! $leader->hasRole('stack-leader')) {
                $leader->assignRole('stack-leader');
            }
        }

        // Add tech stack members
        if (! empty($validated['members'])) {
            foreach ($validated['members'] as $memberId) {
                User::where('id', $memberId)->update(['tech_stack_id' => $techStack->id]);
            }
        }

        return redirect()->route('tech-stacks.index')
            ->with('success', 'Tech stack created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $techStack = TechStack::with(['stackLeader', 'members', 'projects'])->findOrFail($id);

        $this->authorize('view', $techStack);

        return view('tech-stacks.show', compact('techStack'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $techStack = TechStack::with(['stackLeader', 'members'])->findOrFail($id);

        $this->authorize('update', $techStack);

        $users = User::where('is_active', true)->get();

        return view('tech-stacks.edit', compact('techStack', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $techStack = TechStack::findOrFail($id);

        $this->authorize('update', $techStack);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tech_stacks,name,'.$techStack->id,
            'description' => 'nullable|string',
            'stack_leader_id' => 'nullable|exists:users,id',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $techStack->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'stack_leader_id' => $validated['stack_leader_id'],
        ]);

        // Update stack leader
        if ($validated['stack_leader_id']) {
            $leader = User::find($validated['stack_leader_id']);
            $leader->update(['tech_stack_id' => $techStack->id]);
            if (! $leader->hasRole('stack-leader')) {
                $leader->assignRole('stack-leader');
            }
        }

        // Update tech stack members - remove all current members and add new ones
        User::where('tech_stack_id', $techStack->id)->update(['tech_stack_id' => null]);

        if (! empty($validated['members'])) {
            foreach ($validated['members'] as $memberId) {
                User::where('id', $memberId)->update(['tech_stack_id' => $techStack->id]);
            }
        }

        return redirect()->route('tech-stacks.index')
            ->with('success', 'Tech stack updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $techStack = TechStack::findOrFail($id);

        $this->authorize('delete', $techStack);

        // Check if tech stack has active projects
        $activeProjects = $techStack->projects()->whereIn('status', ['planning', 'in-progress'])->count();
        if ($activeProjects > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete tech stack with active projects.',
            ], 422);
        }

        // Remove tech stack assignments from users
        User::where('tech_stack_id', $techStack->id)->update(['tech_stack_id' => null]);

        $techStack->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tech stack deleted successfully.',
        ]);
    }

    /**
     * Add members to tech stack
     */
    public function addMembers(Request $request, string $id)
    {
        $techStack = TechStack::findOrFail($id);

        $this->authorize('addMembers', $techStack);

        $validated = $request->validate([
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
        ]);

        foreach ($validated['members'] as $memberId) {
            User::where('id', $memberId)->update(['tech_stack_id' => $techStack->id]);
        }

        return redirect()->route('tech-stacks.show', $techStack->id)
            ->with('success', 'Members added to tech stack successfully.');
    }

    /**
     * Remove member from tech stack
     */
    public function removeMember(string $id, string $user_id)
    {
        $techStack = TechStack::findOrFail($id);

        $this->authorize('removeMember', $techStack);

        $user = User::findOrFail($user_id);

        if ($user->tech_stack_id !== $techStack->id) {
            return redirect()->route('tech-stacks.show', $techStack->id)
                ->with('error', 'User is not a member of this tech stack.');
        }

        $user->update(['tech_stack_id' => null]);

        return redirect()->route('tech-stacks.show', $techStack->id)
            ->with('success', 'Member removed from tech stack successfully.');
    }
}
