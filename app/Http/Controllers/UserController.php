<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Mail\WelcomeEmail;
use App\Models\SalesTarget;
use App\Models\Team;
use App\Models\TechStack;
use App\Models\User;
use App\Services\EmailService;
use App\Services\SalesTeamSync;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|operation-manager|project-manager']);
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with(['team', 'techStack', 'roles'])
            ->where('id', '!=', auth()->id())
            ->latest();

        // Apply filters
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('employee_id', 'like', "%{$searchTerm}%")
                    ->orWhere('position', 'like', "%{$searchTerm}%")
                    ->orWhere('department', 'like', "%{$searchTerm}%");
            });
        }

        // Specific employee ID filter
        if ($request->filled('employee_id')) {
            $query->where('employee_id', 'like', "%{$request->employee_id}%");
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        if ($request->filled('tech_stack_id')) {
            $query->where('tech_stack_id', $request->tech_stack_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $users = $query->paginate(15)->withQueryString();

        $teams = Team::all();
        $techStacks = TechStack::all();
        $roles = Role::all();
        $departments = User::whereNotNull('department')->distinct()->pluck('department');

        return view('users.index', compact('users', 'teams', 'techStacks', 'roles', 'departments'));
    }

    /**
     * Get users data for AJAX requests.
     */
    public function getUsers(Request $request): JsonResponse
    {
        $query = User::with(['team', 'techStack', 'roles'])
            ->where('id', '!=', auth()->id());

        return DataTables::of($query)
            ->addColumn('name', function ($user) {
                return '<div class="flex items-center">
                    <img src="https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=4F46E5&color=fff" alt="'.$user->name.'" class="w-8 h-8 rounded-full mr-3">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-gray-100">'.$user->name.'</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">ID: '.$user->employee_id.'</div>
                    </div>
                </div>';
            })
            ->addColumn('employee_id', function ($user) {
                return $user->employee_id ?: 'N/A';
            })
            ->addColumn('phone', function ($user) {
                return $user->phone ?: 'N/A';
            })
            ->addColumn('department', function ($user) {
                return $user->department ?: 'N/A';
            })
            ->addColumn('position', function ($user) {
                return $user->position ?: 'N/A';
            })
            ->addColumn('team', function ($user) {
                return $user->team ? '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 dark:bg-blue-500/10 text-blue-800 dark:text-blue-400">'.$user->team->name.'</span>' : 'N/A';
            })
            ->addColumn('tech_stack', function ($user) {
                return $user->techStack ? '<span class="px-2 py-1 text-xs rounded-full bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400">'.$user->techStack->name.'</span>' : 'N/A';
            })
            ->addColumn('role', function ($user) {
                $roles = $user->roles->pluck('name')->map(function ($role) {
                    return '<span class="px-2 py-1 text-xs rounded-full bg-purple-100 dark:bg-purple-500/10 text-purple-800 dark:text-purple-400">'.ucfirst($role).'</span>';
                })->implode(' ');

                return $roles ?: 'N/A';
            })
            ->addColumn('status', function ($user) {
                $isChecked = $user->is_active ? 'checked' : '';
                $disabledPermission = ! auth()->user()->hasPermissionTo('edit users') ? 'disabled' : '';

                // Color classes based on status
                $bgColor = $user->is_active ? 'bg-green-600' : 'bg-red-600';
                $focusRingColor = $user->is_active ? 'peer-focus:ring-green-300' : 'peer-focus:ring-red-300';
                $bgInactiveColor = $user->is_active ? 'bg-gray-200 dark:bg-gray-700' : 'bg-red-200 dark:bg-red-900';

                return '<div class="flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox"
                               class="sr-only peer status-switcher"
                               data-user-id="'.$user->id.'"
                               data-user-name="'.htmlspecialchars($user->name).'"
                               '.$isChecked.'
                               '.$disabledPermission.'>
                        <div class="w-11 h-6 '.$bgInactiveColor.' peer-focus:outline-none peer-focus:ring-4 '.$focusRingColor.' rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all '.$bgColor.'"></div>
                    </label>
                   </div>';
            })
            ->addColumn('created_at', function ($user) {
                return $user->created_at->format('M j, Y');
            })
            ->addColumn('actions', function ($user) {
                return '<div class="flex items-center space-x-2">
                    <a href="'.route('users.show', $user->id).'" class="group inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-r from-indigo-100 to-indigo-200 dark:from-indigo-500/10 dark:to-indigo-500/10 text-indigo-600 dark:text-indigo-400 hover:from-indigo-200 hover:to-indigo-300 dark:hover:from-indigo-500/20 dark:hover:to-indigo-500/20 transition-all duration-200 hover:scale-110 shadow-sm" title="View">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="'.route('admin.users.roles', $user->id).'" class="group inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-r from-purple-100 to-purple-200 dark:from-purple-500/10 dark:to-purple-500/10 text-purple-600 dark:text-purple-400 hover:from-purple-200 hover:to-purple-300 dark:hover:from-purple-500/20 dark:hover:to-purple-500/20 transition-all duration-200 hover:scale-110 shadow-sm" title="Manage Roles">
                        <i class="fas fa-user-tag"></i>
                    </a>
                    <a href="'.route('users.edit', $user->id).'" class="group inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-r from-emerald-100 to-emerald-200 dark:from-emerald-500/10 dark:to-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:from-emerald-200 hover:to-emerald-300 dark:hover:from-emerald-500/20 dark:hover:to-emerald-500/20 transition-all duration-200 hover:scale-110 shadow-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button onclick="deleteUser('.$user->id.', \''.$user->name.'\')" class="group inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-r from-red-100 to-red-200 dark:from-red-500/10 dark:to-red-500/10 text-red-600 dark:text-red-400 hover:from-red-200 hover:to-red-300 dark:hover:from-red-500/20 dark:hover:to-red-500/20 transition-all duration-200 hover:scale-110 shadow-sm" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>';
            })
            ->setRowId(function ($user) {
                return 'user-'.$user->id;
            })
            ->filter(function ($query) use ($request) {
                // Global search
                if ($request->filled('search') && ! empty($request->search)) {
                    $searchTerm = $request->search;
                    $query->where(function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%")
                            ->orWhere('email', 'like', "%{$searchTerm}%")
                            ->orWhere('employee_id', 'like', "%{$searchTerm}%")
                            ->orWhere('phone', 'like', "%{$searchTerm}%")
                            ->orWhere('position', 'like', "%{$searchTerm}%")
                            ->orWhere('department', 'like', "%{$searchTerm}%");
                    });
                }

                // Column filters
                if ($request->filled('employee_id')) {
                    $query->where('employee_id', 'like', "%{$request->employee_id}%");
                }

                if ($request->filled('role')) {
                    $query->whereHas('roles', function ($q) use ($request) {
                        $q->where('name', $request->role);
                    });
                }

                if ($request->filled('team_id')) {
                    $query->where('team_id', $request->team_id);
                }

                if ($request->filled('tech_stack_id')) {
                    $query->where('tech_stack_id', $request->tech_stack_id);
                }

                if ($request->filled('status')) {
                    $query->where('is_active', $request->status === 'active');
                }

                if ($request->filled('department')) {
                    $query->where('department', $request->department);
                }
            })
            ->order(function ($query) {
                $query->latest();
            })
            ->rawColumns(['name', 'team', 'tech_stack', 'role', 'status', 'actions'])
            ->toJson();
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $teams = Team::where('id', '!=', auth()->user()->team_id)->get();
        $techStacks = TechStack::all();
        $roles = Role::all();

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

        return view('users.create', compact('teams', 'techStacks', 'roles', 'permissions'));
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $this->applyTrack($request->validated());

        // Generate unique employee ID if not provided
        if (empty($validated['employee_id'])) {
            $validated['employee_id'] = $this->generateUniqueEmployeeId();
        }

        try {
            // Use database transaction to ensure atomicity
            // If any part fails, everything will be rolled back
            $user = DB::transaction(function () use ($validated) {
                // Create user
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'employee_id' => $validated['employee_id'],
                    'phone' => $validated['phone'] ?? null,
                    'password' => Hash::make($validated['password']),
                    'department' => $validated['department'] ?? null,
                    'position' => $validated['position'] ?? null,
                    'hire_date' => $validated['hire_date'] ?? null,
                    'salary' => $validated['salary'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'is_active' => $validated['is_active'] ?? true,
                    'team_id' => $validated['team_id'] ?? null,
                    'tech_stack_id' => $validated['tech_stack_id'] ?? null,
                    'sales_leader_id' => $validated['sales_leader_id'] ?? null,
                ]);

                $this->recordSalesTarget($user, $validated);

                // Assign role
                if (! empty($validated['role'])) {
                    $user->assignRole($validated['role']);
                }

                // Assign additional permissions
                if (! empty($validated['permissions'])) {
                    $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                    $user->syncPermissions($permissions);
                }

                // Assign managed teams (only for managers)
                if (! empty($validated['managed_teams'])) {
                    $user->managedTeams()->sync($validated['managed_teams']);
                }

                if ($user->isSales()) {
                    SalesTeamSync::sync($user);
                }

                return $user;
            });

            // Send welcome email with credentials
            EmailService::sendTo($user->email, new WelcomeEmail($user, $validated['password']));

            // Check if request expects JSON (AJAX)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully!',
                    'user' => $user->load(['team', 'techStack', 'roles']),
                    'redirect' => route('users.index'),
                ]);
            }

            // For regular form submissions, redirect with success message
            return redirect()
                ->route('users.index')
                ->with('success', 'User created successfully!');

        } catch (\Exception $e) {
            // Check if request expects JSON (AJAX)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create user: '.$e->getMessage(),
                ], 500);
            }

            // For regular form submissions, redirect back with error
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create user: '.$e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('profile.edit')
                ->with('info', 'You are viewing your own profile.');
        }

        $user->load(['team', 'techStack', 'roles', 'permissions', 'projects', 'assignedTasks']);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('profile.edit')
                ->with('info', 'You are editing your own profile.');
        }

        $teams = Team::all();
        $techStacks = TechStack::all();
        $roles = Role::all();

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

        $user->load(['roles', 'permissions', 'managedTeams']);

        return view('users.edit', compact('user', 'teams', 'techStacks', 'roles', 'permissions'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit your own account through user management.',
            ], 403);
        }

        $validated = $request->validate([
            'track' => 'nullable|in:operation,sales',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'employee_id' => 'nullable|string|max:13|unique:users,employee_id,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date|before_or_equal:today',
            'salary' => 'nullable|numeric|min:0|max:999999.99',
            'address' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'team_id' => 'nullable|exists:teams,id',
            'tech_stack_id' => 'nullable|exists:tech_stacks,id',
            'sales_leader_id' => 'nullable|exists:users,id',
            'sales_target' => 'nullable|numeric|min:0|max:99999999.99',
            'sales_target_month' => 'nullable|date_format:Y-m',
            'role' => 'nullable|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'managed_teams' => 'nullable|array',
            'managed_teams.*' => 'exists:teams,id',
        ]);

        // Sales roles cannot access tech stack functionality
        $currentUser = auth()->user();
        if ($currentUser && $currentUser->isSales()) {
            $validated['tech_stack_id'] = null;
        }

        // Sales leaders cannot create team members
        if ($currentUser && $currentUser->hasRole('sales-leader') && $user->exists) {
            // Remove team creation permissions
            if (isset($validated['permissions'])) {
                $validated['permissions'] = array_filter($validated['permissions'], function ($perm) {
                    return ! in_array($perm, ['manage team members', 'create users']);
                });
            }
        }

        // Team leaders and members can only view teams, not edit
        if ($currentUser && $currentUser->hasRole(['team-leader', 'team-member']) && $user->exists) {
            // Prevent changing team_id for team leaders/members
            unset($validated['team_id']);
        }

        $validated = $this->applyTrack($validated, $user);

        // Ensure permissions array exists (empty array if not provided)
        $validated['permissions'] = $validated['permissions'] ?? [];

        // Ensure managed_teams array exists (empty array if not provided)
        $validated['managed_teams'] = $validated['managed_teams'] ?? [];

        try {
            // Use database transaction to ensure atomicity
            $user = DB::transaction(function () use ($user, $validated) {
                // Update user basic info
                $updateData = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'employee_id' => $validated['employee_id'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'department' => $validated['department'] ?? null,
                    'position' => $validated['position'] ?? null,
                    'hire_date' => $validated['hire_date'] ?? null,
                    'salary' => $validated['salary'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'team_id' => $validated['team_id'] ?? null,
                    'tech_stack_id' => $validated['tech_stack_id'] ?? null,
                    'sales_leader_id' => $validated['sales_leader_id'] ?? null,
                ];

                // Only update password if provided
                if (! empty($validated['password'])) {
                    $updateData['password'] = Hash::make($validated['password']);
                }

                // Update active status if provided
                if (isset($validated['is_active'])) {
                    $updateData['is_active'] = $validated['is_active'];
                }

                $user->update($updateData);

                $this->recordSalesTarget($user, $validated);

                // Update role
                if (isset($validated['role'])) {
                    $user->syncRoles([$validated['role']]);
                }

                if ($user->isSales()) {
                    SalesTeamSync::sync($user);
                }

                // Update permissions - always sync, even if empty
                if (! empty($validated['permissions'])) {
                    $permissions = Permission::whereIn('id', $validated['permissions'])->get();
                    $user->syncPermissions($permissions);
                } else {
                    // Explicitly remove all direct permissions
                    $user->permissions()->detach();
                }

                // Update managed teams (only for managers)
                if (! empty($validated['managed_teams'])) {
                    $user->managedTeams()->sync($validated['managed_teams']);
                } else {
                    $user->managedTeams()->sync([]);
                }

                return $user;
            });

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'user' => $user->load(['team', 'techStack', 'roles']),
                'redirect' => route('users.index'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Drop whatever does not belong to the chosen track — the sales
     * team_id is derived from the sales leader by SalesTeamSync instead
     * of being taken from the client, and an operational hire never gets a
     * sales leader or target — no matter what the client sent.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    protected function applyTrack(array $validated, ?User $user = null): array
    {
        // Editing without the track field present (a raw API call, or an
        // update made before this field existed) should not silently wipe
        // whichever side the person is already on.
        $track = $validated['track'] ?? ($user?->isSales() ? 'sales' : 'operation');

        if ($track === 'sales') {
            unset($validated['team_id'], $validated['tech_stack_id']);
        } else {
            // The monthly target is shared by both tracks now — only the
            // sales-leader relationship is exclusive to the sales side.
            unset($validated['sales_leader_id']);
        }

        return $validated;
    }

    /**
     * Store the monthly sales target the admin entered, if any. Targets are
     * kept per month and carry forward until a newer one is set.
     *
     * @param  array<string, mixed>  $validated
     */
    protected function recordSalesTarget(User $user, array $validated): void
    {
        if (! isset($validated['sales_target']) || $validated['sales_target'] === '') {
            return;
        }

        $month = ! empty($validated['sales_target_month'])
            ? Carbon::createFromFormat('Y-m', $validated['sales_target_month'])
            : now();

        SalesTarget::setFor($user->id, $month->startOfMonth(), (float) $validated['sales_target']);
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        // Check if user has assigned tasks or projects
        if ($user->assignedTasks()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete user with assigned tasks. Please reassign tasks first.',
            ], 422);
        }

        if ($user->projects()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete user with assigned projects. Please reassign projects first.',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own account status.',
            ], 403);
        }

        $user->update(['is_active' => ! $user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'message' => "User {$status} successfully.",
            'status' => $user->is_active,
        ]);
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully.',
        ]);
    }

    /**
     * Get user statistics.
     */
    public function getStats(): JsonResponse
    {
        // One grouped pass instead of five full COUNT scans.
        $rows = User::query()
            ->where('id', '!=', auth()->id())
            ->selectRaw('
                COUNT(*) as total,
                COALESCE(SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END), 0) as active,
                COALESCE(SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END), 0) as inactive,
                COALESCE(SUM(CASE WHEN team_id IS NOT NULL THEN 1 ELSE 0 END), 0) as with_team,
                COALESCE(SUM(CASE WHEN team_id IS NULL THEN 1 ELSE 0 END), 0) as without_team
            ')
            ->first();

        $stats = [
            'total_users' => (int) $rows->total,
            'active_users' => (int) $rows->active,
            'inactive_users' => (int) $rows->inactive,
            'users_with_team' => (int) $rows->with_team,
            'users_without_team' => (int) $rows->without_team,
        ];

        $roleStats = Role::withCount('users')->get()->mapWithKeys(function ($role) {
            return [$role->name => $role->users_count];
        });

        return response()->json([
            'stats' => $stats,
            'role_stats' => $roleStats,
        ]);
    }

    /**
     * Export users to Excel/CSV.
     */
    public function export(Request $request): BinaryFileResponse
    {
        $query = User::with(['team', 'techStack', 'roles'])
            ->where('id', '!=', auth()->id());

        // Handle selected user IDs
        if ($request->filled('user_ids')) {
            $userIds = explode(',', $request->user_ids);
            $query->whereIn('id', $userIds);
        }

        // Apply same filters as index method
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('position', 'like', "%{$searchTerm}%")
                    ->orWhere('department', 'like', "%{$searchTerm}%");
            });
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', 'like', "%{$request->employee_id}%");
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('team_id')) {
            $query->where('team_id', $request->team_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('tech_stack_id')) {
            $query->where('tech_stack_id', $request->tech_stack_id);
        }

        $users = $query->get();

        // Create CSV content
        $csvContent = "\xEF\xBB\xBF"; // UTF-8 BOM
        $csvContent .= "Employee ID,Name,Email,Phone,Department,Position,Team,Tech Stack,Role,Status,Hire Date,Salary\n";

        foreach ($users as $user) {
            $csvContent .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $user->employee_id ?? 'N/A',
                '"'.str_replace('"', '""', $user->name).'"',
                $user->email,
                $user->phone ?? 'N/A',
                $user->department ?? 'N/A',
                '"'.str_replace('"', '""', $user->position ?? 'N/A').'"',
                $user->team?->name ?? 'N/A',
                $user->techStack?->name ?? 'N/A',
                $user->roles->pluck('name')->implode(', ') ?? 'N/A',
                $user->is_active ? 'Active' : 'Inactive',
                $user->hire_date?->format('Y-m-d') ?? 'N/A',
                $user->salary ? '$'.number_format($user->salary, 2) : 'N/A'
            );
        }

        $filename = 'users_export_'.now()->format('Y-m-d_H-i-s').'.csv';

        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    /**
     * Show bulk import form.
     */
    public function bulkImportForm()
    {
        $roles = Role::all();
        $teams = Team::all();
        $techStacks = TechStack::all();

        return view('users.bulk-import', compact('roles', 'teams', 'techStacks'));
    }

    /**
     * Handle bulk import of users.
     */
    public function bulkImport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
            'default_role' => 'nullable|exists:roles,name',
            'default_team' => 'nullable|exists:teams,id',
            'default_tech_stack' => 'nullable|exists:tech_stacks,id',
            'send_welcome_email' => 'boolean',
        ]);

        try {
            $file = $request->file('csv_file');
            $filePath = $file->getRealPath();

            // Read CSV file
            $csvData = $this->readCsvFile($filePath);

            if (empty($csvData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSV file is empty or invalid format.',
                ], 400);
            }

            $importedUsers = [];
            $errors = [];
            $duplicates = [];
            $row = 1;

            foreach ($csvData as $userData) {
                $row++;

                try {
                    // Validate required fields
                    if (empty($userData['name']) || empty($userData['email'])) {
                        $errors[] = "Row {$row}: Name and email are required.";

                        continue;
                    }

                    // Check for duplicates
                    if (User::where('email', $userData['email'])->exists()) {
                        $duplicates[] = "Row {$row}: User with email {$userData['email']} already exists.";

                        continue;
                    }

                    // Generate employee ID if not provided
                    if (empty($userData['employee_id'])) {
                        $userData['employee_id'] = $this->generateUniqueEmployeeId();
                    }

                    // Create user
                    $user = User::create([
                        'name' => trim($userData['name']),
                        'email' => trim(strtolower($userData['email'])),
                        'employee_id' => trim($userData['employee_id']),
                        'phone' => $userData['phone'] ?? null,
                        'department' => $userData['department'] ?? null,
                        'position' => $userData['position'] ?? null,
                        'hire_date' => ! empty($userData['hire_date']) ? date('Y-m-d', strtotime($userData['hire_date'])) : null,
                        'salary' => ! empty($userData['salary']) ? (float) str_replace(['$', ','], '', $userData['salary']) : null,
                        'address' => $userData['address'] ?? null,
                        'is_active' => ! empty($userData['is_active']) && strtolower($userData['is_active']) === 'yes',
                        'team_id' => ! empty($userData['team_id']) ? $userData['team_id'] : $validated['default_team'],
                        'tech_stack_id' => ! empty($userData['tech_stack_id']) ? $userData['tech_stack_id'] : $validated['default_tech_stack'],
                        'password' => Hash::make('password123'), // Default password
                    ]);

                    // Assign role
                    $role = $userData['role'] ?? $validated['default_role'];
                    if ($role) {
                        $user->assignRole($role);
                    }

                    // Send welcome email with credentials
                    EmailService::sendTo($user->email, new WelcomeEmail($user, 'password123'));

                    $importedUsers[] = $user->name;

                } catch (\Exception $e) {
                    $errors[] = "Row {$row}: ".$e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Bulk import completed. Imported '.count($importedUsers).' users.',
                'imported_count' => count($importedUsers),
                'imported_users' => $importedUsers,
                'errors' => $errors,
                'duplicates' => $duplicates,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing file: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Read CSV file and return array of data.
     */
    private function readCsvFile($filePath): array
    {
        $data = [];
        $header = null;

        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (! $header) {
                    $header = array_map('strtolower', $row);

                    continue;
                }

                if (count($header) !== count($row)) {
                    continue; // Skip rows with mismatched column count
                }

                $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * Bulk operations on users.
     */
    public function bulkOperation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'operation' => 'required|in:activate,deactivate,delete,assign_role,remove_role,assign_team,remove_team',
            'role' => 'required_if:operation,assign_role,remove_role|exists:roles,name',
            'team_id' => 'required_if:operation,assign_team|exists:teams,id',
        ]);

        $userIds = collect($validated['user_ids'])->filter(fn ($id) => $id != auth()->id());
        $operation = $validated['operation'];

        if ($userIds->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No valid users selected for operation.',
            ], 400);
        }

        $affectedCount = 0;

        switch ($operation) {
            case 'activate':
                User::whereIn('id', $userIds)->update(['is_active' => true]);
                $affectedCount = $userIds->count();
                $message = "Successfully activated {$affectedCount} users.";
                break;

            case 'deactivate':
                User::whereIn('id', $userIds)->update(['is_active' => false]);
                $affectedCount = $userIds->count();
                $message = "Successfully deactivated {$affectedCount} users.";
                break;

            case 'assign_role':
                $role = Role::findOrFail($validated['role']);
                User::whereIn('id', $userIds)->get()->each->assignRole($role);
                $affectedCount = $userIds->count();
                $message = "Successfully assigned {$role->name} role to {$affectedCount} users.";
                break;

            case 'remove_role':
                $role = Role::findOrFail($validated['role']);
                User::whereIn('id', $userIds)->get()->each->removeRole($role);
                $affectedCount = $userIds->count();
                $message = "Successfully removed {$role->name} role from {$affectedCount} users.";
                break;

            case 'assign_team':
                User::whereIn('id', $userIds)->update(['team_id' => $validated['team_id']]);
                $affectedCount = $userIds->count();
                $team = Team::find($validated['team_id']);
                $message = "Successfully assigned {$affectedCount} users to {$team->name} team.";
                break;

            case 'remove_team':
                User::whereIn('id', $userIds)->update(['team_id' => null]);
                $affectedCount = $userIds->count();
                $message = "Successfully removed {$affectedCount} users from their teams.";
                break;

            case 'delete':
                // Check for assigned tasks and projects before deletion
                $usersWithTasks = User::whereIn('id', $userIds)->has('assignedTasks')->count();
                $usersWithProjects = User::whereIn('id', $userIds)->has('projects')->count();

                if ($usersWithTasks > 0 || $usersWithProjects > 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Cannot delete users with assigned tasks or projects. Found {$usersWithTasks} users with tasks and {$usersWithProjects} users with projects.",
                    ], 422);
                }

                User::whereIn('id', $userIds)->delete();
                $affectedCount = $userIds->count();
                $message = "Successfully deleted {$affectedCount} users.";
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'affected_count' => $affectedCount,
        ]);
    }

    /**
     * Generate unique employee ID.
     */
    private function generateUniqueEmployeeId(): string
    {
        do {
            // Generate a random 13-character alphanumeric string
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $employeeId = '';
            for ($i = 0; $i < 13; $i++) {
                $employeeId .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (User::where('employee_id', $employeeId)->exists());

        return $employeeId;
    }
}
