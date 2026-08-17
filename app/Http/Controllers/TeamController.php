<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Support\IdEncrypter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Team::class);

        $canManage = auth()->user()->hasRole(['admin', 'operation-manager', 'project-manager']);

        return view('teams.index', compact('canManage'));
    }

    /**
     * Get teams data for DataTable.
     */
    public function getTeams(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Team::class);

        $user = auth()->user();

        $query = Team::with(['teamLeader', 'members', 'orders'])
            ->when(! $user->hasRole(['admin', 'operation-manager', 'project-manager']), function ($q) use ($user) {
                // Team leaders, stack leaders, and team members only see their own team
                $q->where('id', $user->team_id);
            })
            ->latest();

        return DataTables::of($query)
            ->editColumn('team_leader', function ($team) {
                $leader = $team->teamLeader;

                return $leader ? [
                    'name' => $leader->name,
                    'employee_id' => $leader->employee_id,
                ] : null;
            })
            ->addColumn('members_count', function ($team) {
                $count = $team->members->count();
                $activeCount = $team->members->where('is_active', true)->count();

                return $count.' '.$activeCount.' active';
            })
            ->addColumn('projects_count', function ($team) {
                $count = $team->orders->count();
                $activeCount = $team->orders->whereIn('status', ['planning', 'in-progress'])->count();

                return $count.' '.$activeCount.' active';
            })
            ->addColumn('key', function ($team) {
                return $team->getRouteKey();
            })
            ->setRowId('id')
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Team::class);

        $users = User::where('is_active', true)->get();

        $usersJson = $users->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'employee_id' => $u->employee_id ?? '',
                'position' => $u->position ?? '',
            ];
        })->values();

        return view('teams.create', compact('users', 'usersJson'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Team::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams',
            'description' => 'nullable|string',
            'team_leader_id' => 'nullable|exists:users,id',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'team_leader_id' => $validated['team_leader_id'],
        ]);

        // Assign team leader role if specified
        if ($validated['team_leader_id']) {
            $leader = User::find($validated['team_leader_id']);
            $leader->update(['team_id' => $team->id]);
            if (! $leader->hasRole('team-leader')) {
                $leader->assignRole('team-leader');
            }
        }

        // Add team members
        if (! empty($validated['members'])) {
            User::whereIn('id', $validated['members'])->update(['team_id' => $team->id]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Team created successfully.',
                'redirect' => route('teams.index'),
            ]);
        }

        return redirect()->route('teams.index')
            ->with('success', 'Team created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $team = Team::with(['teamLeader', 'members', 'orders'])->findOrFail(IdEncrypter::decrypt($id));

        $this->authorize('view', $team);

        return view('teams.show', compact('team'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $team = Team::with(['teamLeader', 'members'])->findOrFail(IdEncrypter::decrypt($id));

        $this->authorize('update', $team);

        $users = User::where('is_active', true)->get();

        $usersJson = $users->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'employee_id' => $u->employee_id ?? '',
                'position' => $u->position ?? '',
            ];
        })->values();

        return view('teams.edit', compact('team', 'users', 'usersJson'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $team = Team::findOrFail(IdEncrypter::decrypt($id));

        $this->authorize('update', $team);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,'.$team->id,
            'description' => 'nullable|string',
            'team_leader_id' => 'nullable|exists:users,id',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $teamLeaderId = $request->filled('team_leader_id') ? $validated['team_leader_id'] : null;

        $team->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'team_leader_id' => $teamLeaderId,
        ]);

        // Update team leader
        if ($teamLeaderId) {
            $leader = User::find($teamLeaderId);
            $leader->update(['team_id' => $team->id]);
            if (! $leader->hasRole('team-leader')) {
                $leader->assignRole('team-leader');
            }
        }

        // Update team members - remove all current members and add new ones
        // But keep the team leader
        User::where('team_id', $team->id)
            ->where('id', '!=', $teamLeaderId)
            ->update(['team_id' => null]);

        // Add team leader first
        if ($teamLeaderId) {
            User::where('id', $teamLeaderId)->update(['team_id' => $team->id]);
        }

        // Add selected members
        if (! empty($validated['members'])) {
            User::whereIn('id', $validated['members'])->update(['team_id' => $team->id]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Team updated successfully.',
                'redirect' => route('teams.index'),
            ]);
        }

        return redirect()->route('teams.index')
            ->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $team = Team::findOrFail(IdEncrypter::decrypt($id));

        $this->authorize('delete', $team);

        // Check if team has active projects
        $activeProjects = $team->orders()->whereIn('status', ['planning', 'in-progress'])->count();
        if ($activeProjects > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete team with active projects.',
            ], 422);
        }

        // Remove team assignments from users
        User::where('team_id', $team->id)->update(['team_id' => null]);

        $team->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team deleted successfully.',
        ]);
    }

    /**
     * Add members to team
     */
    public function addMembers(Request $request, string $id)
    {
        $team = Team::findOrFail(IdEncrypter::decrypt($id));

        $this->authorize('addMembers', $team);

        $validated = $request->validate([
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
        ]);

        User::whereIn('id', $validated['members'])->update(['team_id' => $team->id]);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Members added to team successfully.');
    }

    /**
     * Remove member from team
     */
    public function removeMember(string $id, string $user_id)
    {
        $team = Team::findOrFail(IdEncrypter::decrypt($id));

        $this->authorize('removeMember', $team);

        $user = User::findOrFail(IdEncrypter::decrypt($user_id));

        if ($user->team_id !== $team->id) {
            return redirect()->route('teams.show', $team)
                ->with('error', 'User is not a member of this team.');
        }

        $user->update(['team_id' => null]);

        return redirect()->route('teams.show', $team)
            ->with('success', 'Member removed from team successfully.');
    }
}
