<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\TechStack;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_projects' => Project::count(),
            'total_tasks' => Task::count(),
            'total_teams' => Team::count(),
            'total_tech_stacks' => TechStack::count(),
        ];

        $roleStats = Role::withCount('users')->get()->mapWithKeys(function ($role) {
            return [$role->name => $role->users_count];
        });

        $projectStatusStats = Project::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentUsers = User::latest()->take(5)->get();
        $recentProjects = Project::with(['team', 'createdBy'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'roleStats', 'projectStatusStats', 'recentUsers', 'recentProjects'));
    }

    /**
     * Display users management page.
     */
    public function users(Request $request)
    {
        $users = User::with(['team', 'techStack', 'roles'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->when($request->team_id, function ($query, $teamId) {
                $query->where('team_id', $teamId);
            })
            ->latest()
            ->paginate(15);

        $teams = Team::all();
        $roles = Role::all();

        return view('admin.users', compact('users', 'teams', 'roles'));
    }

    /**
     * Show the form for editing a user.
     */
    public function editUser(User $user)
    {
        $teams = Team::all();
        $techStacks = TechStack::all();
        $roles = Role::all();

        return view('admin.edit-user', compact('user', 'teams', 'techStacks', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
            'team_id' => 'nullable|exists:teams,id',
            'tech_stack_id' => 'nullable|exists:tech_stacks,id',
        ]);

        $user->update($request->all());

        // Sync Laravel Permission role
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Display roles and permissions management page.
     */
    public function roles()
    {
        $roles = Role::with('permissions')->withCount('users')->get();
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode(' ', $permission->name)[0];
        });

        return view('admin.roles', compact('roles', 'permissions'));
    }

    /**
     * Show the form for editing a role.
     */
    public function editRole(string $roleName)
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        $role->load('permissions');
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode(' ', $permission->name)[0];
        });

        return view('admin.edit-role', compact('role', 'permissions'));
    }

    /**
     * Update the specified role permissions.
     */
    public function updateRole(Request $request, string $roleName)
    {
        $role = Role::where('name', $roleName)->firstOrFail();

        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.roles')
            ->with('success', 'Role permissions updated successfully.');
    }
}
