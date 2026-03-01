<?php

namespace App\Http\Controllers;

use App\Mail\OrderAssignmentNotification;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Notifications\OrderStatusNotification;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Order::class);

        $user = auth()->user();

        // Get selected month from request (no default - show all orders initially)
        $selectedMonth = $request->input('month', '');

        // Initialize dates as null
        $startDate = null;
        $endDate = null;

        // Parse the selected month only if provided
        if ($selectedMonth) {
            $monthDate = \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth);
            $startDate = $monthDate->startOfMonth()->format('Y-m-d');
            $endDate = $monthDate->endOfMonth()->format('Y-m-d');
        }

        // Generate list of available months (last 12 months + current)
        $availableMonths = collect();
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $availableMonths->put($date->format('Y-m'), $date->format('F Y'));
        }

        $statuses = \App\Models\OrderStatus::orderBy('sort_order')->get();

        // Scope filter options based on role
        if ($user->hasRole(['team-leader', 'stack-leader'])) {
            $teams = \App\Models\Team::where('id', $user->team_id)->get();
            $techStacks = \App\Models\TechStack::where('team_id', $user->team_id)->get();
            $profiles = \App\Models\Profile::active()->get();
        } else {
            $teams = \App\Models\Team::all();
            $techStacks = \App\Models\TechStack::all();
            $profiles = \App\Models\Profile::active()->get();
        }

        // Filter users for assignment modal based on role
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            $users = \App\Models\User::all();
        } elseif ($user->hasRole('team-leader')) {
            $users = \App\Models\User::where('team_id', $user->team_id)->get();
        } else {
            $users = \App\Models\User::where('team_id', $user->team_id)->get();
        }

        return view('orders.index', compact('teams', 'statuses', 'techStacks', 'profiles', 'users', 'selectedMonth', 'availableMonths', 'startDate', 'endDate'));
    }

    public function getOrders(Request $request)
    {
        $this->authorize('viewAny', Order::class);

        $query = Order::query()->with(['team', 'techStack', 'freelanceProfile', 'creator', 'users']);

        $user = auth()->user();

        // Team leaders and stack leaders only see their own team's orders
        if ($user->hasRole(['team-leader', 'stack-leader'])) {
            $query->where('team_id', $user->team_id);
        }
        // Team members see all orders (with badge for assigned ones)
        // Admin, OM, PM see all orders

        // Filter by month (only in created_at)
        if ($request->filled('month') && $request->month !== '') {
            $monthName = Carbon::parse($request->month)->format('F Y');

            $query->whereMonth('start_date', Carbon::parse($request->month)->month);
        }

        // Custom search field or DataTables search
        $searchTerm = $request->input('search') ?? $request->input('search.value');
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('order_id', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%")
                    ->orWhere('status', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by team
        if ($request->filled('team_id') && $request->team_id !== '') {
            $query->where('team_id', $request->team_id);
        }

        // Filter by tech stack
        if ($request->filled('tech_stack_id') && $request->tech_stack_id !== '') {
            $query->where('tech_stack_id', $request->tech_stack_id);
        }

        // Filter by freelance profile
        if ($request->filled('freelance_profile_id') && $request->freelance_profile_id !== '') {
            $query->where('freelance_profile_id', $request->freelance_profile_id);
        }

        // Filter by priority
        if ($request->filled('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        // Order by column (DataTables column index)
        if ($request->filled('order')) {
            $orderColumn = $request->input('order.0.column');
            $orderDir = $request->input('order.0.dir');

            // Note: Column 0 is checkbox (null data), so subtract 1 from client-side column index
            $adjustedColumn = $orderColumn - 1;
            $columns = ['order_id', 'name', 'status', 'priority', 'actual_revenue', 'team_id', 'tech_stack_id', 'freelance_profile_id', 'end_date', 'created_at', 'delivered_at'];
            if (isset($columns[$adjustedColumn])) {
                $query->orderBy($columns[$adjustedColumn], $orderDir);
            }
        } else {
            $query->latest();
        }

        // Pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);

        $totalRecords = $query->count();
        $orders = $query->skip($start)->take($length)->get();

        $data = [];
        $currentUserId = auth()->id();
        foreach ($orders as $order) {
            $data[] = [
                'id' => $order->id,
                'order_id' => $order->order_id,
                'name' => $order->name,
                'description' => $order->description,
                'status' => $order->status,
                'priority' => $order->priority,
                'value' => $order->value,
                'actual_revenue' => $order->actual_revenue,
                'start_date' => $order->start_date ? $order->start_date->format('Y-m-d') : null,
                'end_date' => $order->end_date ? $order->end_date->format('Y-m-d') : null,
                'created_at' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $order->updated_at ? $order->updated_at->format('Y-m-d H:i:s') : null,
                'delivered_at' => $order->delivered_at ? $order->delivered_at->format('Y-m-d H:i:s') : null,
                'team' => $order->team ? $order->team->name : 'N/A',
                'tech_stack' => $order->techStack ? $order->techStack->name : 'N/A',
                'freelance_profile' => $order->freelanceProfile ? $order->freelanceProfile->username : 'N/A',
                'freelance_profile_id' => $order->freelance_profile_id,
                'order_sheet_url' => $order->order_sheet_url,
                'special_status' => $order->special_status?->value,
                'last_update_date' => $order->last_update_date ? $order->last_update_date->format('Y-m-d H:i:s') : null,
                'is_assigned' => $order->users->contains('id', $currentUserId),
                'is_my_team' => $order->team_id === auth()->user()->team_id,
                'users' => $order->users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                    ];
                })->toArray(),
                'users_count' => $order->users->count(),
                'users_names' => $order->users->pluck('name')->join(', '),
            ];
        }

        return response()->json([
            'draw' => $request->input('draw', 0),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data,
        ]);
    }

    public function searchMembers(Request $request)
    {
        $this->authorize('create', Order::class);

        $user = auth()->user();
        $search = $request->get('q', '');
        $teamId = $request->get('team_id');

        $query = \App\Models\User::query();

        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            if ($teamId) {
                $query->where('team_id', $teamId);
            }
        } else {
            $query->where('team_id', $user->team_id);
        }

        // Search by name, email, employee_id, or position
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        }

        $members = $query->orderBy('created_at', 'desc')
            ->with('team')
            ->limit(10)
            ->get(['id', 'name', 'email', 'employee_id', 'position', 'team_id']);

        return response()->json([
            'results' => $members->map(function ($member) {
                return [
                    'id' => $member->id,
                    'text' => $member->name.' ('.$member->email.')',
                    'employee_id' => $member->employee_id,
                    'position' => $member->position,
                    'team_name' => $member->team?->name,
                ];
            }),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Order::class);

        $user = auth()->user();

        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            $teams = \App\Models\Team::all();
            $techStacks = \App\Models\TechStack::all();
            $users = \App\Models\User::all();
        } else {
            $teams = \App\Models\Team::where('id', $user->team_id)->get();
            $techStacks = \App\Models\TechStack::where('team_id', $user->team_id)->get();
            $users = \App\Models\User::where('team_id', $user->team_id)->get();
        }

        $statuses = \App\Models\OrderStatus::orderBy('sort_order')->get();
        $isTeamLeader = $user->hasRole('team-leader');
        $profiles = \App\Models\Profile::active()->get();

        return view('orders.create', compact('teams', 'techStacks', 'statuses', 'users', 'profiles', 'isTeamLeader'));
    }

    public function store(Request $request)
    {

        $this->authorize('create', Order::class);

        $user = auth()->user();

        $validated = $request->validate([
            'order_id' => 'nullable|string|unique:orders,order_id',
            'name' => 'required|string|max:255|unique:orders,name',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_revenue' => 'nullable|numeric|min:0',
            'team_id' => 'nullable|exists:teams,id',
            'tech_stack_id' => 'nullable|exists:tech_stacks,id',
            'freelance_profile_id' => 'nullable|exists:profiles,id',
            'order_sheet_url' => 'nullable|url|max:2048',
            'special_status' => 'nullable|in:selected_for_delivery,continue_for_next,stuck_order',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        // For team leaders and stack leaders, set appropriate filters
        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager']) && $user->team_id) {
            $validated['team_id'] = $user->team_id;
        }

        // Auto-calculate net revenue (80% of order amount after Fiverr's 20% fee)
        $orderValue = $validated['value'] ?? null;
        $actualRevenue = $orderValue ? round($orderValue * 0.80, 2) : ($validated['actual_revenue'] ?? null);

        $order = Order::create([
            'order_id' => $validated['order_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'value' => $orderValue,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'actual_revenue' => $actualRevenue,
            'team_id' => $validated['team_id'] ?? null,
            'tech_stack_id' => $validated['tech_stack_id'] ?? null,
            'freelance_profile_id' => $validated['freelance_profile_id'] ?? null,
            'order_sheet_url' => $validated['order_sheet_url'] ?? null,
            'special_status' => $validated['special_status'] ?? null,
            'created_by' => auth()->id(),
        ]);

        // Attach users
        if (! empty($validated['users'])) {
            $order->users()->attach($validated['users']);

            // Send notifications to assigned users
            foreach ($validated['users'] as $userId) {
                $assignedUser = \App\Models\User::find($userId);
                if ($assignedUser) {
                    EmailService::sendTo($assignedUser->email, new OrderAssignmentNotification($order, $assignedUser, false));
                }
            }
        }

        // Send notification to team leader if team is assigned
        if ($order->team && $order->team->teamLeader) {
            EmailService::sendTo($order->team->teamLeader->email, new OrderAssignmentNotification($order, $order->team->teamLeader, true));
        }

        // Create initial status history
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $order->status,
            'changed_by' => auth()->id(),
            'actual_revenue' => $order->actual_revenue,
        ]);

        return redirect()->route('orders.index')
            ->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['team', 'techStack', 'creator', 'users', 'statusHistory.changedByUser']);

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $this->authorize('update', $order);

        $user = auth()->user();

        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            $teams = \App\Models\Team::all();
            $techStacks = \App\Models\TechStack::all();
            $users = \App\Models\User::all();
        } else {
            $teams = \App\Models\Team::where('id', $user->team_id)->get();
            $techStacks = \App\Models\TechStack::where('team_id', $user->team_id)->get();
            $users = \App\Models\User::where('team_id', $user->team_id)->get();
        }

        $statuses = \App\Models\OrderStatus::orderBy('sort_order')->get();
        $profiles = \App\Models\Profile::active()->get();
        $order->load('users');

        return view('orders.edit', compact('order', 'teams', 'techStacks', 'statuses', 'users', 'profiles'));
    }

    public function update(Request $request, Order $order)
    {
        $this->authorize('update', $order);
        $validated = $request->validate([
            'order_id' => 'nullable|string|unique:orders,order_id,'.$order->id,
            'name' => 'required|string|max:255|unique:orders,name,'.$order->id,
            'description' => 'nullable|string',
            'status' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_revenue' => 'nullable|numeric|min:0',
            'team_id' => 'nullable|exists:teams,id',
            'tech_stack_id' => 'nullable|exists:tech_stacks,id',
            'freelance_profile_id' => 'nullable|exists:profiles,id',
            'order_sheet_url' => 'nullable|url|max:2048',
            'special_status' => 'nullable|in:selected_for_delivery,continue_for_next,stuck_order',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $order->status;
        $oldRevenue = $order->actual_revenue;

        // Auto-calculate net revenue (80% of order amount after Fiverr's 20% fee)
        $orderValue = $validated['value'] ?? null;
        $actualRevenue = $orderValue ? round($orderValue * 0.80, 2) : ($validated['actual_revenue'] ?? null);

        $order->update([
            'order_id' => $validated['order_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'value' => $orderValue,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'actual_revenue' => $actualRevenue,
            'team_id' => $validated['team_id'] ?? null,
            'tech_stack_id' => $validated['tech_stack_id'] ?? null,
            'freelance_profile_id' => $validated['freelance_profile_id'] ?? null,
            'order_sheet_url' => $validated['order_sheet_url'] ?? null,
            'special_status' => $validated['special_status'] ?? null,
        ]);

        // Sync users
        if (isset($validated['users'])) {
            $order->users()->sync($validated['users']);
        }

        // Create status history if status or revenue changed
        if ($oldStatus !== $order->status || $oldRevenue != $order->actual_revenue) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'changed_by' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
                'actual_revenue' => $order->actual_revenue,
            ]);
        }

        // Notify assigned users about status change
        if ($oldStatus !== $order->status) {
            $order->users->each(function ($user) use ($order, $oldStatus) {
                if ($user->id !== auth()->id()) {
                    $user->notify(new OrderStatusNotification($order, $oldStatus, $order->status));
                }
            });
        }

        return redirect()->route('orders.index')
            ->with('success', 'Order updated successfully.');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:orders,id',
            'action' => 'required|string|in:mark-completed,mark-in-progress,mark-pending,mark-on-hold,mark-cancelled,delete',
        ]);

        $ids = $validated['ids'];
        $action = $validated['action'];

        $orders = Order::whereIn('id', $ids);

        // Only admin/OM/PM and team leaders (for their team) can perform bulk actions
        $user = auth()->user();
        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            if ($user->hasRole('team-leader')) {
                $orders->where('team_id', $user->team_id);
            } else {
                return response()->json(['message' => 'You do not have permission to perform bulk actions.'], 403);
            }
        }

        $count = $orders->count();

        if ($action === 'delete') {
            $orders->delete();

            return response()->json([
                'message' => "{$count} order(s) deleted successfully.",
            ]);
        }

        $statusMap = [
            'mark-completed' => 'completed',
            'mark-in-progress' => 'in-progress',
            'mark-pending' => 'pending',
            'mark-on-hold' => 'on-hold',
            'mark-cancelled' => 'cancelled',
        ];

        $newStatus = $statusMap[$action];
        $orders->update(['status' => $newStatus]);

        $statusLabel = ucfirst(str_replace('-', ' ', $newStatus));

        return response()->json([
            'message' => "{$count} order(s) marked as {$statusLabel}.",
        ]);
    }

    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);

        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    public function quickUpdate(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'status' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'delivered_at' => 'nullable|date',
            'start_date' => 'nullable|date',
            'special_status' => 'nullable|in:selected_for_delivery,continue_for_next,stuck_order',
            'last_update_date' => 'nullable|date',
        ]);

        $order->update($validated);

        return response()->json([
            'message' => 'Order updated successfully.',
            'order' => $order->fresh(),
        ]);
    }

    public function getUsers(Order $order)
    {
        $this->authorize('view', $order);

        $users = $order->users()->get(['users.id', 'users.name', 'users.email']);

        return response()->json([
            'users' => $users,
        ]);
    }

    public function assignUsers(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        // Sync users to the order
        $order->users()->sync($validated['users']);

        // Send notifications to newly assigned users
        foreach ($validated['users'] as $userId) {
            $assignedUser = \App\Models\User::find($userId);
            if ($assignedUser) {
                EmailService::sendTo($assignedUser->email, new OrderAssignmentNotification($order, $assignedUser, false));
            }
        }

        return response()->json([
            'message' => 'User assignments updated successfully.',
            'users' => $order->users()->get(['users.id', 'users.name', 'users.email']),
        ]);
    }

    public function searchUsers(Request $request)
    {
        $search = $request->input('search', '');
        $excludeIds = $request->input('exclude');

        // Convert comma-separated string to array if needed
        if (is_string($excludeIds)) {
            $excludeIds = explode(',', $excludeIds);
        }
        if (! is_array($excludeIds)) {
            $excludeIds = [];
        }

        // Filter out empty values
        $excludeIds = array_filter($excludeIds, 'strlen');
        $excludeIds = array_map('intval', $excludeIds);

        $user = auth()->user();

        $query = \App\Models\User::query();

        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            // Can see all users
        } else {
            $query->where('team_id', $user->team_id);
        }

        // Exclude already selected users
        if (! empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        // Apply search filter - search by name, email, employee_id, position
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        }

        // Limit results and include team relationship
        $users = $query->with('team:id,name')
            ->select('id', 'name', 'email', 'employee_id', 'position', 'team_id')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json([
            'users' => $users,
        ]);
    }
}
