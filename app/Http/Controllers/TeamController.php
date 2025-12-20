<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
        if (!auth()->user()->can('manage teams')) {
            abort(403, 'Unauthorized');
        }
        return view('teams.index');
    }

    /**
     * Get teams data for DataTable.
     */
    public function getTeams(Request $request): JsonResponse
    {
        $query = Team::with(['teamLeader', 'members', 'projects'])->latest();

        return DataTables::of($query)
            ->editColumn('team_leader', function ($team) {
                $leader = $team->teamLeader;
                return $leader ? [
                    'name' => $leader->name,
                    'employee_id' => $leader->employee_id
                ] : null;
            })
            ->addColumn('members_count', function ($team) {
                $count = $team->members->count();
                $activeCount = $team->members->where('is_active', true)->count();
                return $count . ' ' . $activeCount . ' active';
            })
            ->addColumn('projects_count', function ($team) {
                $count = $team->projects->count();
                $activeCount = $team->projects->whereIn('status', ['planning', 'in-progress'])->count();
                return $count . ' ' . $activeCount . ' active';
            })
            ->setRowId('id')
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('is_active', true)->get();
        return view('teams.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
            if (!$leader->hasRole('team-leader')) {
                $leader->assignRole('team-leader');
            }
        }

        // Add team members
        if (!empty($validated['members'])) {
            foreach ($validated['members'] as $memberId) {
                User::where('id', $memberId)->update(['team_id' => $team->id]);
            }
        }

        return redirect()->route('teams.index')
            ->with('success', 'Team created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $team = Team::with(['teamLeader', 'members', 'projects'])->findOrFail($id);
        return view('teams.show', compact('team'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $team = Team::with(['teamLeader', 'members'])->findOrFail($id);
        $users = User::where('is_active', true)->get();
        return view('teams.edit', compact('team', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $team = Team::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:teams,name,' . $team->id,
            'description' => 'nullable|string',
            'team_leader_id' => 'nullable|exists:users,id',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
        ]);

        $team->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'team_leader_id' => $validated['team_leader_id'],
        ]);

        // Update team leader
        if ($validated['team_leader_id']) {
            $leader = User::find($validated['team_leader_id']);
            $leader->update(['team_id' => $team->id]);
            if (!$leader->hasRole('team-leader')) {
                $leader->assignRole('team-leader');
            }
        }

        // Update team members - remove all current members and add new ones
        User::where('team_id', $team->id)->update(['team_id' => null]);

        if (!empty($validated['members'])) {
            foreach ($validated['members'] as $memberId) {
                User::where('id', $memberId)->update(['team_id' => $team->id]);
            }
        }

        return redirect()->route('teams.index')
            ->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $team = Team::findOrFail($id);

        // Check if team has active projects
        $activeProjects = $team->projects()->whereIn('status', ['planning', 'in-progress'])->count();
        if ($activeProjects > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete team with active projects.'
            ], 422);
        }

        // Remove team assignments from users
        User::where('team_id', $team->id)->update(['team_id' => null]);

        $team->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team deleted successfully.'
        ]);
    }

    /**
     * Add members to team
     */
    public function addMembers(Request $request, string $id)
    {
        $team = Team::findOrFail($id);

        $validated = $request->validate([
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
        ]);

        foreach ($validated['members'] as $memberId) {
            User::where('id', $memberId)->update(['team_id' => $team->id]);
        }

        return redirect()->route('teams.show', $team->id)
            ->with('success', 'Members added to team successfully.');
    }

    /**
     * Remove member from team
     */
    public function removeMember(string $id, string $user_id)
    {
        $team = Team::findOrFail($id);
        $user = User::findOrFail($user_id);

        if ($user->team_id !== $team->id) {
            return redirect()->route('teams.show', $team->id)
                ->with('error', 'User is not a member of this team.');
        }

        $user->update(['team_id' => null]);

        return redirect()->route('teams.show', $team->id)
            ->with('success', 'Member removed from team successfully.');
    }
}