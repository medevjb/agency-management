<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\Team;
use App\Models\TechStack;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
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

        // Apply same filters as index method
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

        $users = $query->latest()->get();

        return response()->json($users);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $teams = Team::where('id', '!=', auth()->user()->team_id)->get();
        $techStacks = TechStack::all();
        $roles = Role::all();
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode(' ', $permission->name)[0];
        });

        return view('users.create', compact('teams', 'techStacks', 'roles', 'permissions'));
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        // Generate unique employee ID if not provided
        if (empty($validated['employee_id'])) {
            $validated['employee_id'] = $this->generateUniqueEmployeeId();
        }

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
        ]);

        // Assign role
        if (! empty($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        // Assign additional permissions
        if (! empty($validated['permissions'])) {
            $user->givePermissionTo($validated['permissions']);
        }

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'user' => $user->load(['team', 'techStack', 'roles']),
        ]);
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
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode(' ', $permission->name)[0];
        });

        $user->load(['roles', 'permissions']);

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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'employee_id' => 'nullable|string|regex:/^[0-9]{6,8}$/|unique:users,employee_id,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'team_id' => 'nullable|exists:teams,id',
            'tech_stack_id' => 'nullable|exists:tech_stacks,id',
            'role' => 'nullable|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Update user
        $user->update($validated);

        // Update role
        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        // Update permissions
        $user->syncPermissions($validated['permissions'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'user' => $user->load(['team', 'techStack', 'roles']),
        ]);
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
        $stats = [
            'total_users' => User::where('id', '!=', auth()->id())->count(),
            'active_users' => User::where('is_active', true)->where('id', '!=', auth()->id())->count(),
            'inactive_users' => User::where('is_active', false)->where('id', '!=', auth()->id())->count(),
            'users_with_team' => User::whereNotNull('team_id')->where('id', '!=', auth()->id())->count(),
            'users_without_team' => User::whereNull('team_id')->where('id', '!=', auth()->id())->count(),
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
    public function export(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $query = User::with(['team', 'techStack', 'roles'])
            ->where('id', '!=', auth()->id());

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
            $employeeId = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (User::where('employee_id', $employeeId)->exists());

        return $employeeId;
    }
}
