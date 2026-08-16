<?php

namespace App\Http\Controllers;

use App\Models\Team;
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

        $techStacks = TechStack::with(['stackLeader', 'users', 'team', 'orders'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            })
            ->when($request->filled('leader'), function ($q) use ($request) {
                $q->where('stack_leader_id', $request->leader);
            })
            ->when(! $user->hasRole(['admin', 'operation-manager', 'project-manager']), function ($q) use ($user) {
                if ($user->hasRole('team-leader')) {
                    $q->where('team_id', $user->team_id);
                } elseif ($user->hasRole('stack-leader')) {
                    $q->where(function ($query) use ($user) {
                        $query->where('stack_leader_id', $user->id)
                            ->orWhere('team_id', $user->team_id);
                    });
                } else {
                    if ($user->team_id) {
                        $q->where('team_id', $user->team_id);
                    } else {
                        $q->whereRaw('1 = 0');
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

        $query = TechStack::with(['stackLeader', 'users', 'orders'])
            ->when(! $user->hasRole(['admin', 'operation-manager', 'project-manager']), function ($q) use ($user) {
                if ($user->hasRole('team-leader')) {
                    $q->where('team_id', $user->team_id);
                } elseif ($user->hasRole('stack-leader')) {
                    $q->where(function ($query) use ($user) {
                        $query->where('stack_leader_id', $user->id)
                            ->orWhere('team_id', $user->team_id);
                    });
                } else {
                    if ($user->team_id) {
                        $q->where('team_id', $user->team_id);
                    } else {
                        $q->whereRaw('1 = 0');
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
                $count = $techStack->users->count();
                $activeCount = $techStack->users->where('is_active', true)->count();

                return $count.' '.$activeCount.' active';
            })
            ->addColumn('projects_count', function ($techStack) {
                $count = $techStack->orders->count();
                $activeCount = $techStack->orders->whereIn('status', ['planning', 'in-progress'])->count();

                return $count.' '.$activeCount.' active';
            })
            ->orderColumn('members_count', function ($query, $order) {
                $query->withCount('users')->orderBy('users_count', $order);
            })
            ->orderColumn('projects_count', function ($query, $order) {
                $query->withCount('orders')->orderBy('orders_count', $order);
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

        $user = auth()->user();

        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            $users = User::where('is_active', true)->get();
            $teams = Team::all();
        } else {
            $users = User::where('team_id', $user->team_id)->where('is_active', true)->get();
            $teams = collect([Team::find($user->team_id)]);
        }

        return view('tech-stacks.create', compact('users', 'teams'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', TechStack::class);

        $user = auth()->user();

        // For team leaders, auto-assign their team
        if ($user->hasRole('team-leader') && ! $user->hasRole(['admin', 'operation-manager', 'project-manager']) && $user->team_id) {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'members' => 'nullable|array',
                'members.*' => 'exists:users,id',
            ]);

            $techStack = TechStack::create([
                'name' => $validated['name'],
                'team_id' => $user->team_id,
                'description' => $validated['description'] ?? null,
                'stack_leader_id' => $user->id,
            ]);

            // Assign stack leader role to the team leader
            if (! $user->hasRole('stack-leader')) {
                $user->assignRole('stack-leader');
            }

            // Add tech stack members using pivot table
            if (! empty($validated['members'])) {
                foreach ($validated['members'] as $memberId) {
                    $member = User::find($memberId);
                    // Only allow adding team members
                    if ($member && $member->team_id === $user->team_id) {
                        $techStack->users()->attach($memberId);
                    }
                }
            }
        } else {
            // Admin and operation manager
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'team_id' => 'required|exists:teams,id',
                'description' => 'nullable|string',
                'stack_leader_id' => 'nullable|exists:users,id',
                'members' => 'nullable|array',
                'members.*' => 'exists:users,id',
            ]);

            $techStack = TechStack::create([
                'name' => $validated['name'],
                'team_id' => $validated['team_id'],
                'description' => $validated['description'] ?? null,
                'stack_leader_id' => $validated['stack_leader_id'] ?? null,
            ]);

            // Assign stack leader role if specified
            if (! empty($validated['stack_leader_id'])) {
                $leader = User::find($validated['stack_leader_id']);
                if (! $leader->hasRole('stack-leader')) {
                    $leader->assignRole('stack-leader');
                }
            }

            // Add tech stack members using pivot table
            if (! empty($validated['members'])) {
                foreach ($validated['members'] as $memberId) {
                    $techStack->users()->attach($memberId);
                }
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
        $techStack = TechStack::with(['stackLeader', 'users', 'orders'])->findOrFail($id);

        $this->authorize('view', $techStack);

        return view('tech-stacks.show', compact('techStack'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $techStack = TechStack::with(['stackLeader', 'users'])->findOrFail($id);

        $this->authorize('update', $techStack);

        $user = auth()->user();

        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            $users = User::where('is_active', true)->get();
            $teams = Team::all();
        } else {
            $users = User::where('team_id', $user->team_id)->where('is_active', true)->get();
            $teams = collect([Team::find($user->team_id)]);
        }

        return view('tech-stacks.edit', compact('techStack', 'users', 'teams'));
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
            if (! $leader->hasRole('stack-leader')) {
                $leader->assignRole('stack-leader');
            }
        }

        // Update tech stack members - sync with pivot table
        $techStack->users()->sync($validated['members'] ?? []);

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
        $activeProjects = $techStack->orders()->whereIn('status', ['planning', 'in-progress'])->count();
        if ($activeProjects > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete tech stack with active projects.',
            ], 422);
        }

        // Remove tech stack assignments from pivot table
        $techStack->users()->detach();

        $techStack->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tech stack deleted successfully.',
        ]);
    }

    /**
     * Search members by team for tech stack assignment
     */
    public function searchMembers(Request $request, string $teamId): JsonResponse
    {
        $search = $request->get('search', '');
        $exclude = $request->get('exclude', []);

        $query = User::where('team_id', $teamId)
            ->where('is_active', true)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('employee_id', 'like', '%'.$search.'%');
                });
            })
            ->when(! empty($exclude), function ($q) use ($exclude) {
                $q->whereNotIn('id', $exclude);
            })
            ->select('id', 'name', 'email', 'employee_id', 'position', 'team_id')
            ->with('team:id,name')
            ->limit(20);

        $users = $query->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'employee_id' => $user->employee_id,
                'position' => $user->position,
                'team' => $user->team ? [
                    'id' => $user->team->id,
                    'name' => $user->team->name,
                ] : null,
            ];
        });

        return response()->json([
            'users' => $users,
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
            $techStack->users()->attach($memberId);
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

        if (! $techStack->users()->where('users.id', $user->id)->exists()) {
            return redirect()->route('tech-stacks.show', $techStack->id)
                ->with('error', 'User is not a member of this tech stack.');
        }

        $techStack->users()->detach($user->id);

        return redirect()->route('tech-stacks.show', $techStack->id)
            ->with('success', 'Member removed from tech stack successfully.');
    }
}
