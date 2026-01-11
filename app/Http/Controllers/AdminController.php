<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Task;
use App\Models\Team;
use App\Models\TechStack;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        $now = now();
        $thisMonth = $now->startOfMonth();

        $stats = [
            'total_users' => User::count(),
            'total_orders' => Order::count(),
            'total_tasks' => Task::count(),
            'total_teams' => Team::count(),
            'total_tech_stacks' => TechStack::count(),
            'new_users_this_month' => User::where('created_at', '>=', $thisMonth)->count(),
            'active_orders' => Order::where('status', 'in-progress')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'completed_tasks' => Task::where('status', 'completed')->count(),
            'total_team_members' => User::whereNotNull('team_id')->count(),
            'pending_jobs' => 0, // Can be updated if queue jobs are tracked
        ];

        $roleStats = Role::withCount('users')->get()->mapWithKeys(function ($role) {
            return [$role->name => $role->users_count];
        });

        $orderStatusStats = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentUsers = User::latest()->take(5)->get();
        $recentOrders = Order::with(['team', 'creator'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'roleStats', 'orderStatusStats', 'recentUsers', 'recentOrders'));
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
     * Show the form for creating a new role.
     */
    public function createRole()
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode(' ', $permission->name)[0];
        });

        return view('admin.create-role', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $request->name]);
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles')
            ->with('success', 'Role created successfully.');
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

    /**
     * Delete the specified role.
     */
    public function deleteRole(string $roleName)
    {
        $role = Role::where('name', $roleName)->firstOrFail();

        if ($role->name === 'admin') {
            return redirect()->route('admin.roles')
                ->with('error', 'Cannot delete the admin role.');
        }

        $role->delete();

        return redirect()->route('admin.roles')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Show the form for editing user roles.
     */
    public function editUserRoles(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();

        return view('admin.edit-user-roles', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the user's roles.
     */
    public function updateUserRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user->syncRoles($request->roles ?? []);

        // Update primary role column for backward compatibility
        if (! empty($request->roles)) {
            $user->update(['role' => $request->roles[0]]);
        } else {
            $user->update(['role' => null]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User roles updated successfully.');
    }

    /**
     * Assign a user to a role.
     */
    public function assignUserToRole(Request $request, string $roleName, User $user)
    {
        $role = Role::where('name', $roleName)->firstOrFail();

        if ($user->hasRole($role->name)) {
            return response()->json([
                'success' => false,
                'message' => 'User already has this role.',
            ], 400);
        }

        $user->assignRole($role);

        return response()->json([
            'success' => true,
            'message' => 'User assigned to role successfully.',
        ]);
    }

    /**
     * Remove a user from a role.
     */
    public function removeUserFromRole(string $roleName, User $user)
    {
        $role = Role::where('name', $roleName)->firstOrFail();

        if (! $user->hasRole($role->name)) {
            return response()->json([
                'success' => false,
                'message' => 'User does not have this role.',
            ], 400);
        }

        $user->removeRole($role);

        return response()->json([
            'success' => true,
            'message' => 'User removed from role successfully.',
        ]);
    }
}
