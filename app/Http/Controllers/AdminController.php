<?php

namespace App\Http\Controllers;

use App\Models\BugReport;
use App\Models\Order;
use App\Models\Project;
use App\Models\Setting;
use App\Models\Task;
use App\Models\Team;
use App\Models\TechStack;
use App\Models\User;
use App\Support\GlobalMonth;
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
        // "This month" is the globally selected month, not necessarily the
        // calendar's current one — every figure below reads relative to it.
        $monthDate = GlobalMonth::current();
        $selectedMonth = GlobalMonth::key();
        $availableMonths = GlobalMonth::available();

        [$thisMonthStart, $thisMonthEnd] = GlobalMonth::periodFor($monthDate);
        [$lastMonthStart, $lastMonthEnd] = GlobalMonth::periodFor($monthDate->copy()->subMonth());
        $thisYearStart = $monthDate->copy()->startOfYear()->startOfDay();

        // The heavy aggregate block is cached per selected month — it only
        // changes when orders, users, teams or settings change, not per
        // request. Recent users/orders/activity below stay uncached.
        $aggregates = $this->remember('admin.dashboard.aggregates.'.$selectedMonth, function () use (
            $thisMonthStart, $thisMonthEnd, $lastMonthStart, $lastMonthEnd, $thisYearStart, $monthDate
        ) {
            $deliveredStatuses = ['delivered', 'completed'];

            // Basic stats
            $stats = [
                'total_users' => User::count(),
                'total_projects' => Order::count(),
                'total_tasks' => Task::count(),
                'total_teams' => Team::count(),
                'total_tech_stacks' => TechStack::count(),
                'new_users_this_month' => User::whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])->count(),
                'active_projects' => Order::whereNotIn('status', array_merge($deliveredStatuses, ['cancelled']))->count(),
                'completed_projects' => Order::whereIn('status', $deliveredStatuses)->count(),
                'completed_tasks' => Task::where('status', 'completed')->count(),
                'total_team_members' => User::whereNotNull('team_id')->count(),
                'pending_jobs' => 0,
            ];

            // Revenue analytics — use completed_at (when the order was actually
            // completed, not delivered or created). Only 'completed' orders count.
            $revenueThisMonth = Order::where('status', 'completed')
                ->whereNotNull('completed_at')
                ->whereBetween('completed_at', [$thisMonthStart, $thisMonthEnd])
                ->sum('actual_revenue');

            $revenueLastMonth = Order::where('status', 'completed')
                ->whereNotNull('completed_at')
                ->whereBetween('completed_at', [$lastMonthStart, $lastMonthEnd])
                ->sum('actual_revenue');

            $revenueThisYear = Order::where('status', 'completed')
                ->whereNotNull('completed_at')
                ->whereBetween('completed_at', [$thisYearStart, $thisMonthEnd])
                ->sum('actual_revenue');

            $stats['revenue_this_month'] = $revenueThisMonth;
            $stats['revenue_last_month'] = $revenueLastMonth;
            $stats['revenue_this_year'] = $revenueThisYear;
            $stats['revenue_growth'] = $revenueLastMonth > 0
                ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
                : 0;

            // Monthly active users (users created this month)
            $stats['active_users_this_month'] = User::whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])->count();

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

            // Monthly revenue for chart — by completed_at (when money was earned),
            // for the selected month's year. Grouped in PHP rather than with
            // MONTH(), which only exists on MySQL.
            $revenueRows = Order::where('status', 'completed')
                ->whereNotNull('completed_at')
                ->whereYear('completed_at', $monthDate->year)
                ->get(['completed_at', 'actual_revenue']);

            $revenueByMonth = $revenueRows->groupBy(fn (Order $order) => (int) $order->completed_at->format('n'));

            $monthlyRevenue = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthlyRevenue[$i] = (float) $revenueByMonth->get($i, collect())->sum('actual_revenue');
            }

            // Monthly orders for chart — by start_date (when work began)
            $orderRows = Order::whereNotNull('start_date')
                ->whereYear('start_date', $monthDate->year)
                ->get(['start_date']);

            $ordersByMonth = $orderRows->groupBy(fn (Order $order) => (int) $order->start_date->format('n'));

            $monthlyOrders = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthlyOrders[$i] = $ordersByMonth->get($i, collect())->count();
            }

            // Top performing teams — revenue from completed orders, summed in
            // one grouped query rather than once per team.
            $teamRevenue = Order::where('status', 'completed')
                ->whereNotNull('completed_at')
                ->whereNotNull('team_id')
                ->selectRaw('team_id, SUM(actual_revenue) as revenue')
                ->groupBy('team_id')
                ->pluck('revenue', 'team_id');

            $topTeams = Team::withCount('orders')
                ->has('orders')
                ->get()
                ->map(function ($team) use ($teamRevenue) {
                    return [
                        'name' => $team->name,
                        'orders_count' => $team->orders_count,
                        'revenue' => (float) ($teamRevenue[$team->id] ?? 0),
                    ];
                })
                ->sortByDesc('revenue')
                ->take(5)
                ->values();

            // Monthly task creation + completion by month (current year), from
            // one fetch. Grouped in PHP rather than with MONTH(), which only
            // exists on MySQL.
            $taskRows = Task::whereNull('archived_at')
                ->whereYear('created_at', $monthDate->year)
                ->get(['created_at', 'completed_at']);

            $monthlyTasksCreated = [];
            $monthlyTasksCompleted = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthlyTasksCreated[$i] = $taskRows->filter(fn (Task $task) => (int) $task->created_at->format('n') === $i)->count();
                $monthlyTasksCompleted[$i] = $taskRows->filter(fn (Task $task) => $task->completed_at && (int) $task->completed_at->format('n') === $i)->count();
            }

            // Bug reports by status
            $bugStatusStats = BugReport::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            // New users per month (current year)
            $userRows = User::whereYear('created_at', $monthDate->year)->get(['created_at']);

            $monthlyNewUsers = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthlyNewUsers[$i] = $userRows->filter(fn (User $user) => (int) $user->created_at->format('n') === $i)->count();
            }

            // Orders by priority (all-time)
            $ordersByPriority = Order::selectRaw('priority, COUNT(*) as count')
                ->groupBy('priority')
                ->pluck('count', 'priority');

            return [
                'stats' => $stats,
                'roleStats' => $roleStats,
                'projectStatusStats' => $projectStatusStats,
                'monthlyRevenue' => $monthlyRevenue,
                'monthlyOrders' => $monthlyOrders,
                'topTeams' => $topTeams,
                'monthlyTasksCreated' => $monthlyTasksCreated,
                'monthlyTasksCompleted' => $monthlyTasksCompleted,
                'bugStatusStats' => $bugStatusStats,
                'monthlyNewUsers' => $monthlyNewUsers,
                'ordersByPriority' => $ordersByPriority,
            ];
        });

        $stats = $aggregates['stats'];
        $roleStats = $aggregates['roleStats'];
        $projectStatusStats = $aggregates['projectStatusStats'];
        $monthlyRevenue = $aggregates['monthlyRevenue'];
        $monthlyOrders = $aggregates['monthlyOrders'];
        $topTeams = $aggregates['topTeams'];
        $monthlyTasksCreated = $aggregates['monthlyTasksCreated'];
        $monthlyTasksCompleted = $aggregates['monthlyTasksCompleted'];
        $bugStatusStats = $aggregates['bugStatusStats'];
        $monthlyNewUsers = $aggregates['monthlyNewUsers'];
        $ordersByPriority = $aggregates['ordersByPriority'];

        $recentUsers = User::latest()->take(5)->get();
        $recentOrders = Order::with(['team', 'creator'])->latest()->take(5)->get();
        $recentBugs = BugReport::with('user')->latest()->take(5)->get();

        // Recent activity
        $activities = Project::with('createdBy')
            ->latest()
            ->take(8)
            ->get();

        // Settings for display — one query instead of one per key.
        $settings = Setting::getMany(
            ['currency_code', 'currency_symbol', 'currency_position', 'decimal_places', 'thousand_separator', 'decimal_separator', 'company_name', 'company_email', 'company_phone', 'business_hours', 'maintenance_mode', 'site_active', 'site_name'],
            [
                'currency_code' => 'USD',
                'currency_symbol' => '$',
                'currency_position' => 'before',
                'decimal_places' => 2,
                'thousand_separator' => ',',
                'decimal_separator' => '.',
                'company_name' => config('app.name'),
                'company_email' => '',
                'company_phone' => '',
                'business_hours' => '',
                'maintenance_mode' => false,
                'site_active' => true,
                'site_name' => config('app.name'),
            ]
        );

        $currencyCode = $settings['currency_code'];
        $currencySymbol = $settings['currency_symbol'];
        $currencyPosition = $settings['currency_position'];
        $decimalPlaces = (int) $settings['decimal_places'];
        $thousandSeparator = $settings['thousand_separator'];
        $decimalSeparator = $settings['decimal_separator'];
        $companyName = $settings['company_name'];
        $companyEmail = $settings['company_email'];
        $companyPhone = $settings['company_phone'];
        $businessHours = $settings['business_hours'];
        $maintenanceMode = (bool) $settings['maintenance_mode'];
        $siteActive = (bool) $settings['site_active'];
        $siteName = $settings['site_name'];

        return view('admin.dashboard', compact(
            'stats',
            'roleStats',
            'projectStatusStats',
            'recentUsers',
            'recentOrders',
            'recentBugs',
            'monthlyRevenue',
            'monthlyOrders',
            'topTeams',
            'monthlyTasksCreated',
            'monthlyTasksCompleted',
            'bugStatusStats',
            'monthlyNewUsers',
            'ordersByPriority',
            'activities',
            'selectedMonth',
            'availableMonths',
            'monthDate',
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
