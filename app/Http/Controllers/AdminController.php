<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Project;
use App\Models\Setting;
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
        // Use copy() to avoid Carbon mutation — all three vars must be independent instances
        $thisMonthStart = now()->copy()->startOfMonth()->startOfDay();
        $thisMonthEnd = now()->copy()->endOfMonth()->endOfDay();
        $lastMonthStart = now()->copy()->subMonth()->startOfMonth()->startOfDay();
        $lastMonthEnd = now()->copy()->subMonth()->endOfMonth()->endOfDay();
        $thisYearStart = now()->copy()->startOfYear()->startOfDay();

        $deliveredStatuses = ['delivered', 'completed'];

        // Basic stats
        $stats = [
            'total_users' => User::count(),
            'total_projects' => Order::count(),
            'total_tasks' => Task::count(),
            'total_teams' => Team::count(),
            'total_tech_stacks' => TechStack::count(),
            'new_users_this_month' => User::where('created_at', '>=', $thisMonthStart)->count(),
            'active_projects' => Order::whereNotIn('status', array_merge($deliveredStatuses, ['cancelled']))->count(),
            'completed_projects' => Order::whereIn('status', $deliveredStatuses)->count(),
            'completed_tasks' => Task::where('status', 'completed')->count(),
            'total_team_members' => User::whereNotNull('team_id')->count(),
            'pending_jobs' => 0,
        ];

        // Revenue analytics — use delivered_at (when money was earned, not when order was created)
        $revenueThisMonth = Order::whereIn('status', $deliveredStatuses)
            ->whereNotNull('delivered_at')
            ->whereBetween('delivered_at', [$thisMonthStart, $thisMonthEnd])
            ->sum('actual_revenue');

        $revenueLastMonth = Order::whereIn('status', $deliveredStatuses)
            ->whereNotNull('delivered_at')
            ->whereBetween('delivered_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('actual_revenue');

        $revenueThisYear = Order::whereIn('status', $deliveredStatuses)
            ->whereNotNull('delivered_at')
            ->whereBetween('delivered_at', [$thisYearStart, $thisMonthEnd])
            ->sum('actual_revenue');

        $stats['revenue_this_month'] = $revenueThisMonth;
        $stats['revenue_last_month'] = $revenueLastMonth;
        $stats['revenue_this_year'] = $revenueThisYear;
        $stats['revenue_growth'] = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : 0;

        // Monthly active users (users created this month)
        $stats['active_users_this_month'] = User::where('created_at', '>=', $thisMonthStart)->count();

        // Project completion rate
        $totalProjects = Order::count();
        $stats['completion_rate'] = $totalProjects > 0
            ? round(($stats['completed_projects'] / $totalProjects) * 100, 1)
            : 0;

        $roleStats = Role::withCount('users')->get()->mapWithKeys(function ($role) {
            return [$role->name => $role->users_count];
        });

        $projectStatusStats = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentUsers = User::latest()->take(5)->get();
        $recentOrders = Order::with(['team', 'creator'])->latest()->take(5)->get();

        // Monthly revenue for chart — by delivered_at (when money was earned)
        $monthlyRevenue = Order::selectRaw('MONTH(delivered_at) as month, SUM(actual_revenue) as revenue')
            ->whereIn('status', $deliveredStatuses)
            ->whereNotNull('delivered_at')
            ->whereYear('delivered_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('revenue', 'month')
            ->toArray();

        for ($i = 1; $i <= 12; $i++) {
            if (! isset($monthlyRevenue[$i])) {
                $monthlyRevenue[$i] = 0;
            }
        }
        ksort($monthlyRevenue);

        // Monthly orders for chart — by start_date (when work began)
        $monthlyOrders = Order::selectRaw('MONTH(start_date) as month, COUNT(*) as count')
            ->whereNotNull('start_date')
            ->whereYear('start_date', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        for ($i = 1; $i <= 12; $i++) {
            if (! isset($monthlyOrders[$i])) {
                $monthlyOrders[$i] = 0;
            }
        }
        ksort($monthlyOrders);

        // Top performing teams — revenue from all delivered/completed orders
        $topTeams = Team::withCount('orders')
            ->has('orders')
            ->get()
            ->map(function ($team) use ($deliveredStatuses) {
                $completedRevenue = Order::where('team_id', $team->id)
                    ->whereIn('status', $deliveredStatuses)
                    ->whereNotNull('delivered_at')
                    ->sum('actual_revenue');

                return [
                    'name' => $team->name,
                    'orders_count' => $team->orders_count,
                    'revenue' => $completedRevenue,
                ];
            })
            ->sortByDesc('revenue')
            ->take(5)
            ->values();

        // Recent activity
        $activities = Project::with('createdBy')
            ->latest()
            ->take(8)
            ->get();

        // Settings for display
        $currencyCode = Setting::get('currency_code', 'USD');
        $currencySymbol = Setting::get('currency_symbol', '$');
        $currencyPosition = Setting::get('currency_position', 'before');
        $decimalPlaces = (int) Setting::get('decimal_places', 2);
        $thousandSeparator = Setting::get('thousand_separator', ',');
        $decimalSeparator = Setting::get('decimal_separator', '.');
        $companyName = Setting::get('company_name', config('app.name'));
        $companyEmail = Setting::get('company_email', '');
        $companyPhone = Setting::get('company_phone', '');
        $businessHours = Setting::get('business_hours', '');
        $maintenanceMode = (bool) Setting::get('maintenance_mode', false);
        $siteActive = (bool) Setting::get('site_active', true);
        $siteName = Setting::get('site_name', config('app.name'));

        return view('admin.dashboard', compact(
            'stats',
            'roleStats',
            'projectStatusStats',
            'recentUsers',
            'recentOrders',
            'monthlyRevenue',
            'monthlyOrders',
            'topTeams',
            'activities',
            'currencyCode',
            'currencySymbol',
            'currencyPosition',
            'decimalPlaces',
            'thousandSeparator',
            'decimalSeparator',
            'companyName',
            'companyEmail',
            'companyPhone',
            'businessHours',
            'maintenanceMode',
            'siteActive',
            'siteName'
        ));
    }

    /**
     * Display roles and permissions management page.
     */
    public function roles()
    {
        $roles = Role::with('permissions')->withCount('users')->get();

        // Group permissions by module
        $permissions = Permission::all()->groupBy(function ($permission) {
            $name = $permission->name;

            // Order Management
            if (str_contains($name, 'order')) {
                return 'Orders';
            }

            // User Management
            if (str_contains($name, 'user')) {
                return 'Users';
            }

            // Team Management
            if (str_contains($name, 'team')) {
                return 'Teams';
            }

            // Task Management
            if (str_contains($name, 'task')) {
                return 'Tasks';
            }

            // Profile Management
            if (str_contains($name, 'profile')) {
                return 'Profiles';
            }

            // Bug Report Management
            if (str_contains($name, 'bug report')) {
                return 'Bug Reports';
            }

            // Tech Stack Management
            if (str_contains($name, 'tech stack')) {
                return 'Tech Stacks';
            }

            // Portfolio Management
            if (str_contains($name, 'portfolio')) {
                return 'Portfolios';
            }

            // Settings
            if (str_contains($name, 'setting')) {
                return 'Settings';
            }

            return 'Other';
        })->sortKeys();

        return view('admin.roles', compact('roles', 'permissions'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function createRole()
    {
        // Group permissions by module
        $permissions = Permission::all()->groupBy(function ($permission) {
            $name = $permission->name;

            // Order Management
            if (str_contains($name, 'order')) {
                return 'Orders';
            }

            // User Management
            if (str_contains($name, 'user')) {
                return 'Users';
            }

            // Team Management
            if (str_contains($name, 'team')) {
                return 'Teams';
            }

            // Task Management
            if (str_contains($name, 'task')) {
                return 'Tasks';
            }

            // Profile Management
            if (str_contains($name, 'profile')) {
                return 'Profiles';
            }

            // Bug Report Management
            if (str_contains($name, 'bug report')) {
                return 'Bug Reports';
            }

            // Tech Stack Management
            if (str_contains($name, 'tech stack')) {
                return 'Tech Stacks';
            }

            // Portfolio Management
            if (str_contains($name, 'portfolio')) {
                return 'Portfolios';
            }

            // Settings
            if (str_contains($name, 'setting')) {
                return 'Settings';
            }

            return 'Other';
        })->sortKeys();

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
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
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

        // Group permissions by module
        $permissions = Permission::all()->groupBy(function ($permission) {
            $name = $permission->name;

            // Order Management
            if (str_contains($name, 'order')) {
                return 'Orders';
            }

            // User Management
            if (str_contains($name, 'user')) {
                return 'Users';
            }

            // Team Management
            if (str_contains($name, 'team')) {
                return 'Teams';
            }

            // Task Management
            if (str_contains($name, 'task')) {
                return 'Tasks';
            }

            // Profile Management
            if (str_contains($name, 'profile')) {
                return 'Profiles';
            }

            // Bug Report Management
            if (str_contains($name, 'bug report')) {
                return 'Bug Reports';
            }

            // Tech Stack Management
            if (str_contains($name, 'tech stack')) {
                return 'Tech Stacks';
            }

            // Portfolio Management
            if (str_contains($name, 'portfolio')) {
                return 'Portfolios';
            }

            // Settings
            if (str_contains($name, 'setting')) {
                return 'Settings';
            }

            return 'Other';
        })->sortKeys();

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

        $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
        $role->syncPermissions($permissions);

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
