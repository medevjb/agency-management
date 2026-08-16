<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Mail\OrderAssignmentNotification;
use App\Models\Order;
use App\Models\OrderAudit;
use App\Models\OrderStatus;
use App\Models\OrderStatusHistory;
use App\Models\Profile;
use App\Models\Team;
use App\Models\TechStack;
use App\Models\User;
use App\Notifications\OrderStatusNotification;
use App\Services\EmailService;
use App\Services\OrderDocumentService;
use App\Services\PlatformFeeCalculator;
use App\Services\RichTextSanitizer;
use App\Services\SalesCreditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth);
            $startDate = $monthDate->startOfMonth()->format('Y-m-d');
            $endDate = $monthDate->endOfMonth()->format('Y-m-d');
        }

        // Generate list of available months (last 12 months + current)
        $availableMonths = collect();
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $availableMonths->put($date->format('Y-m'), $date->format('F Y'));
        }

        $statuses = OrderStatus::orderBy('sort_order')->get();

        // Scope filter options based on role
        if ($user->hasRole(['team-leader', 'stack-leader'])) {
            $teams = Team::where('id', $user->team_id)->get();
            $techStacks = TechStack::where('team_id', $user->team_id)->get();
            $profiles = Profile::active()->get();
        } else {
            $teams = Team::all();
            $techStacks = TechStack::all();
            $profiles = Profile::active()->get();
        }

        // Filter users for assignment modal based on role
        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            $users = User::all();
        } elseif ($user->hasRole('team-leader')) {
            $users = User::where('team_id', $user->team_id)->get();
        } else {
            $users = User::where('team_id', $user->team_id)->get();
        }

        return view('orders.index', compact('teams', 'statuses', 'techStacks', 'profiles', 'users', 'selectedMonth', 'availableMonths', 'startDate', 'endDate'));
    }

    public function getOrders(Request $request)
    {
        $this->authorize('viewAny', Order::class);

        $query = Order::query()->with(['team', 'techStack', 'freelanceProfile', 'creator', 'users', 'valueDistributions']);

        $user = auth()->user();

        // Sales leaders and sales members can see all running orders
        // (like admin, OM, PM) but with their own orders highlighted — even
        // if they also hold an operational role like team-leader, since a
        // sales-side account's team_id points at its auto-created sales
        // team (see SalesTeamSync), not an engineering delivery team.
        if (! $user->isSales() && $user->hasRole(['team-leader', 'stack-leader'])) {
            $query->where('team_id', $user->team_id);
        }
        // Team members see all orders (with badge for assigned ones)
        // Admin, OM, PM see all orders

        // Filter by month with carry-forward logic.
        // An order belongs to month M if it started on/before end of M
        // AND was not finished before M began.
        $finishedStatuses = ['delivered', 'completed'];

        if ($request->filled('month') && $request->month !== '') {
            $monthDate = Carbon::parse($request->month);
            $monthStart = $monthDate->copy()->startOfMonth();
            $monthEnd = $monthDate->copy()->endOfMonth();
        } else {
            $monthStart = now()->copy()->startOfMonth();
            $monthEnd = now()->copy()->endOfMonth();
        }

        $query->inMonth($monthStart, $monthEnd);

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
            $columns = ['order_id', 'name', 'status', 'priority', 'actual_revenue', 'team_id', 'tech_stack_id', 'freelance_profile_id', 'end_date', 'created_at', 'completed_at'];

            // Sales people do not get the team and assignee columns, so the
            // client side indexes shift by two from that point on.
            if ($user->isSales()) {
                unset($columns[5], $columns[6]);
                $columns = array_values($columns);
            }
            if (isset($columns[$adjustedColumn])) {
                $query->orderBy($columns[$adjustedColumn], $orderDir);
            }
        } else {
            // Default ordering: overdue orders first, then due soon, then the
            // rest by upcoming deadline, and finally delivered/completed/cancelled orders.
            $finishedOrCancelled = array_merge($finishedStatuses, ['cancelled']);
            $now = now();
            $dueSoonThreshold = now()->addDays(4);

            // Sales users see every order, but theirs come first within that.
            if ($user->isSales()) {
                $query->orderByRaw('CASE WHEN sales_user_id = ? THEN 0 ELSE 1 END', [$user->id]);
            }

            $query->orderByRaw(
                'CASE
                    WHEN status IN (?, ?, ?) THEN 3
                    WHEN end_date IS NULL THEN 2
                    WHEN end_date < ? THEN 0
                    WHEN end_date <= ? THEN 1
                    ELSE 2
                END',
                [...$finishedOrCancelled, $now, $dueSoonThreshold]
            )
                ->orderByRaw('end_date IS NULL')
                ->orderBy('end_date', 'asc');
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
                'completed_at' => $order->completed_at ? $order->completed_at->format('Y-m-d H:i:s') : null,
                'team' => $order->team ? $order->team->name : 'N/A',
                'tech_stack' => $order->techStack ? $order->techStack->name : 'N/A',
                'freelance_profile' => $order->freelanceProfile ? $order->freelanceProfile->username : 'N/A',
                'freelance_profile_id' => $order->freelance_profile_id,
                'progress' => $order->progress ?? 0,
                'documents_count' => count($order->documents()),
                'special_status' => $order->special_status?->value,
                'last_update_date' => $order->last_update_date ? $order->last_update_date->format('Y-m-d H:i:s') : null,
                'is_assigned' => $order->users->contains('id', $currentUserId),
                'is_my_team' => $order->team_id === auth()->user()->team_id,
                'is_my_sale' => $user->isSales() && $order->sales_user_id === $currentUserId,
                'needs_value_distribution' => $order->status === 'completed' && ! $order->isValueFullyDistributed(),
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

    /**
     * Open an order from the sales side: the deal facts only. Operations picks
     * it up from there and fills in team, dates and delivery.
     *
     * @param  array<string, mixed>  $validated
     */
    protected function storeSalesOrder(array $validated, StoreOrderRequest $request, OrderDocumentService $documentService)
    {
        $orderValue = $validated['value'] ?? null;
        $freelanceProfileId = $validated['freelance_profile_id'] ?? null;

        $order = Order::create([
            'order_id' => $validated['order_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'credentials' => RichTextSanitizer::clean($validated['credentials'] ?? null),
            'value' => $orderValue,
            'actual_revenue' => $orderValue ? PlatformFeeCalculator::netFor($orderValue, $freelanceProfileId) : null,
            'freelance_profile_id' => $freelanceProfileId,
            'status' => 'pending',
            'is_repeat' => (bool) ($validated['is_repeat'] ?? false),
            'repeat_client_key' => $validated['repeat_client_key'] ?? null,
            'progress' => 0,
            'priority' => 'medium',
            'start_date' => now()->toDateString(),
            'created_by' => auth()->id(),
            'sales_user_id' => auth()->id(),
        ]);

        $documentService->store($order, $request->file('client_documents', []));
        app(SalesCreditService::class)->apply($order, $validated['sales_shares'] ?? [], auth()->id());

        return redirect()->route('orders.index')
            ->with('success', 'Order added. Operations will pick it up from here.');
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    protected function updateSalesOrder(array $validated, UpdateOrderRequest $request, Order $order, OrderDocumentService $documentService)
    {
        $orderValue = $validated['value'] ?? null;
        $freelanceProfileId = $validated['freelance_profile_id'] ?? null;

        $order->update([
            'order_id' => $validated['order_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'credentials' => RichTextSanitizer::clean($validated['credentials'] ?? null),
            'value' => $orderValue,
            'actual_revenue' => $orderValue ? PlatformFeeCalculator::netFor($orderValue, $freelanceProfileId) : $order->actual_revenue,
            'freelance_profile_id' => $freelanceProfileId,
            'is_repeat' => (bool) ($validated['is_repeat'] ?? false),
            'repeat_client_key' => $validated['repeat_client_key'] ?? null,
        ]);

        $documentService->store($order, $request->file('client_documents', []));
        app(SalesCreditService::class)->apply($order, $validated['sales_shares'] ?? [], $order->sales_user_id);

        return redirect()->route('orders.index')
            ->with('success', 'Order updated successfully.');
    }

    public function searchMembers(Request $request)
    {
        $this->authorize('create', Order::class);

        $user = auth()->user();
        $search = $request->get('q', '');
        $teamId = $request->get('team_id');

        $query = User::query();

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
            $teams = Team::all();
            $techStacks = TechStack::all();
            $users = User::all();
        } else {
            $teams = Team::where('id', $user->team_id)->get();
            $techStacks = TechStack::where('team_id', $user->team_id)->get();
            $users = User::where('team_id', $user->team_id)->get();
        }

        $statuses = OrderStatus::orderBy('sort_order')->get();
        $isTeamLeader = $user->hasRole('team-leader');
        $profiles = Profile::active()->get();
        $profilePlatforms = $profiles->pluck('platform', 'id');

        $salesPeople = app(SalesCreditService::class)->creditableUsers($user);

        return view('orders.create', compact('teams', 'techStacks', 'statuses', 'users', 'profiles', 'isTeamLeader', 'profilePlatforms', 'salesPeople'));
    }

    public function store(StoreOrderRequest $request, OrderDocumentService $documentService)
    {
        $user = auth()->user();

        $validated = $request->validated();

        if ($user->isSales()) {
            return $this->storeSalesOrder($validated, $request, $documentService);
        }

        // For team leaders and stack leaders, set appropriate filters
        if (! $user->hasRole(['admin', 'operation-manager', 'project-manager']) && $user->team_id) {
            $validated['team_id'] = $user->team_id;
        }

        // Net revenue after the freelance profile's platform fee (Fiverr 20%, Upwork 10%).
        $orderValue = $validated['value'] ?? null;
        $actualRevenue = $orderValue
            ? PlatformFeeCalculator::netFor($orderValue, $validated['freelance_profile_id'] ?? null)
            : ($validated['actual_revenue'] ?? null);

        $order = Order::create([
            'order_id' => $validated['order_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'credentials' => RichTextSanitizer::clean($validated['credentials'] ?? null),
            'status' => $validated['status'] ?? 'nra',
            'is_repeat' => (bool) ($validated['is_repeat'] ?? false),
            'repeat_client_key' => $validated['repeat_client_key'] ?? null,
            'progress' => $validated['progress'] ?? 0,
            'priority' => $validated['priority'],
            'value' => $orderValue,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'actual_revenue' => $actualRevenue,
            'team_id' => $validated['team_id'] ?? null,
            'tech_stack_id' => $validated['tech_stack_id'] ?? null,
            'freelance_profile_id' => $validated['freelance_profile_id'] ?? null,
            'special_status' => $validated['special_status'] ?? null,
            'created_by' => auth()->id(),
        ]);

        $documentService->store($order, $request->file('client_documents', []));

        if (! empty($validated['sales_shares'])) {
            app(SalesCreditService::class)->apply($order, $validated['sales_shares']);
        }

        // Attach users
        if (! empty($validated['users'])) {
            $order->users()->attach($validated['users']);

            $assignedUsers = User::whereIn('id', $validated['users'])->get();

            OrderAudit::record($order, 'members_updated', [], [
                'members' => $assignedUsers->pluck('name')->all(),
            ]);

            // Send notifications to assigned users
            foreach ($assignedUsers as $assignedUser) {
                EmailService::sendTo($assignedUser->email, new OrderAssignmentNotification($order, $assignedUser, false));
            }
        }

        // Send notification to team leader if team is assigned
        $order->load('team.teamLeader');
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

    /**
     * Change log entries loaded per page, on first paint and per "load more".
     */
    private const AUDITS_PER_PAGE = 15;

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load([
            'team',
            'techStack',
            'freelanceProfile',
            'creator',
            'users',
            'statusHistory.changedByUser',
            'salesShares.user',
        ]);

        $audits = $order->audits()->with('user')->paginate(self::AUDITS_PER_PAGE);
        $auditsTotal = $audits->total();

        return view('orders.show', compact('order', 'audits', 'auditsTotal'));
    }

    /**
     * A page of change log entries, rendered server side so the markup stays
     * identical to first paint.
     */
    public function audits(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        $validated = $request->validate([
            'filter' => 'nullable|string|in:all,fields,people,files',
            'page' => 'nullable|integer|min:1',
        ]);

        $filter = $validated['filter'] ?? 'all';

        $query = $order->audits()->with('user');

        if ($filter !== 'all') {
            $query->whereIn('event', OrderAudit::filterGroups()[$filter]);
        }

        $audits = $query->paginate(self::AUDITS_PER_PAGE, ['*'], 'page', $validated['page'] ?? 1);

        return response()->json([
            'html' => view('orders.partials.audit-entries', compact('audits', 'order'))->render(),
            'total' => $audits->total(),
            'per_page' => $audits->perPage(),
            'current_page' => $audits->currentPage(),
            'last_page' => $audits->lastPage(),
            'from' => $audits->firstItem(),
            'to' => $audits->lastItem(),
        ]);
    }

    public function edit(Order $order)
    {
        $this->authorize('update', $order);

        $user = auth()->user();

        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            $teams = Team::all();
            $techStacks = TechStack::all();
            $users = User::all();
        } else {
            $teams = Team::where('id', $user->team_id)->get();
            $techStacks = TechStack::where('team_id', $user->team_id)->get();
            $users = User::where('team_id', $user->team_id)->get();
        }

        $statuses = OrderStatus::orderBy('sort_order')->get();
        $profiles = Profile::active()->get();
        $profilePlatforms = $profiles->pluck('platform', 'id');
        $order->load(['users', 'salesShares.user']);
        $salesPeople = app(SalesCreditService::class)->creditableUsers($user);

        return view('orders.edit', compact('order', 'teams', 'techStacks', 'statuses', 'users', 'profiles', 'profilePlatforms', 'salesPeople'));
    }

    public function update(UpdateOrderRequest $request, Order $order, OrderDocumentService $documentService)
    {
        $validated = $request->validated();

        if (auth()->user()->isSales()) {
            return $this->updateSalesOrder($validated, $request, $order, $documentService);
        }

        $oldStatus = $order->status;
        $oldRevenue = $order->actual_revenue;
        $oldMembers = $order->users()->pluck('name', 'users.id');

        // Net revenue after the freelance profile's platform fee (Fiverr 20%, Upwork 10%).
        $orderValue = $validated['value'] ?? null;
        $actualRevenue = $orderValue
            ? PlatformFeeCalculator::netFor($orderValue, $validated['freelance_profile_id'] ?? null)
            : ($validated['actual_revenue'] ?? null);

        $order->update([
            'order_id' => $validated['order_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'credentials' => RichTextSanitizer::clean($validated['credentials'] ?? null),
            'status' => $validated['status'],
            'is_repeat' => (bool) ($validated['is_repeat'] ?? false),
            'repeat_client_key' => $validated['repeat_client_key'] ?? null,
            'progress' => $validated['progress'] ?? $order->progress,
            'priority' => $validated['priority'],
            'value' => $orderValue,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'actual_revenue' => $actualRevenue,
            'team_id' => $validated['team_id'] ?? null,
            'tech_stack_id' => $validated['tech_stack_id'] ?? null,
            'freelance_profile_id' => $validated['freelance_profile_id'] ?? null,
            'special_status' => $validated['special_status'] ?? null,
        ]);

        $documentService->store($order, $request->file('client_documents', []));

        if (! empty($validated['sales_shares'])) {
            app(SalesCreditService::class)->apply($order, $validated['sales_shares']);
        }

        // Sync users
        if (isset($validated['users'])) {
            $order->users()->sync($validated['users']);

            $newMembers = $order->users()->pluck('name', 'users.id');

            if ($oldMembers->keys()->sort()->values()->all() !== $newMembers->keys()->sort()->values()->all()) {
                OrderAudit::record($order, 'members_updated',
                    ['members' => $oldMembers->values()->all()],
                    ['members' => $newMembers->values()->all()],
                );
            }
        }

        // Create status history if status or revenue changed
        if ($oldStatus !== $order->status || $oldRevenue != $order->actual_revenue) {
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'changed_by' => auth()->id(),
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

        if (auth()->user()->isSales()) {
            return response()->json(['message' => 'Sales cannot change order status or delete orders.'], 403);
        }

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

        // Acted on one by one so every change lands in the order's change log.
        $orders = $orders->get();
        $count = $orders->count();

        if ($action === 'delete') {
            $orders->each->delete();

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
        $orders->each->update(['status' => $newStatus]);

        $statusLabel = ucfirst(str_replace('-', ' ', $newStatus));

        return response()->json([
            'message' => "{$count} order(s) marked as {$statusLabel}.",
        ]);
    }

    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);

        $order->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Order deleted successfully.']);
        }

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    public function quickUpdate(Request $request, Order $order)
    {
        // Each field group is guarded by its own ability, so a request can
        // only change the fields its sender is allowed to touch.
        $requested = array_keys($request->all());

        if (in_array('users', $requested, true)) {
            $this->authorize('manageAssignment', $order);
        }

        if (array_intersect($requested, ['status', 'priority', 'completed_at', 'start_date', 'end_date', 'special_status', 'last_update_date', 'team_id', 'tech_stack_id', 'freelance_profile_id', 'order_id'])) {
            $this->authorize('manageOperations', $order);
        }

        if (in_array('progress', $requested, true)) {
            $this->authorize('updateProgress', $order);
        }

        if (array_intersect($requested, ['notes', 'credentials', 'description'])) {
            $this->authorize('editContent', $order);
        }

        $validated = $request->validate([
            'status' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'progress' => 'nullable|integer|min:0|max:100',
            'completed_at' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'special_status' => 'nullable|in:selected_for_delivery,continue_for_next,stuck_order',
            'last_update_date' => 'nullable|date',
            // Additional inline editable fields
            'notes' => 'nullable|string',
            'credentials' => 'nullable|string',
            'description' => 'nullable|string',
            'team_id' => 'nullable|exists:teams,id',
            'tech_stack_id' => 'nullable|exists:tech_stacks,id',
            'freelance_profile_id' => 'nullable|exists:profiles,id',
            'order_id' => 'nullable|string|max:255|unique:orders,order_id,'.$order->id,
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        // Handle special fields
        if (isset($validated['credentials'])) {
            $validated['credentials'] = RichTextSanitizer::clean($validated['credentials']);
        }

        $order->update(array_diff_key($validated, ['users' => null]));

        // Handle user assignments
        if (isset($validated['users'])) {
            $currentUsers = $order->users->pluck('id')->toArray();
            $newUsers = $validated['users'];

            $removedUsers = array_diff($currentUsers, $newUsers);
            $addedUsers = array_diff($newUsers, $currentUsers);

            if (! empty($removedUsers) || ! empty($addedUsers)) {
                $order->users()->sync($newUsers);

                OrderAudit::record($order, 'members_updated', [
                    'members' => $order->users->pluck('name')->all(),
                ], [
                    'members' => User::whereIn('id', $currentUsers)->pluck('name')->all(),
                ]);
            }
        }

        return response()->json([
            'message' => 'Order updated successfully.',
            'order' => $order->fresh()->load(['users', 'team', 'techStack', 'freelanceProfile']),
        ]);
    }

    /**
     * Upload client documents straight from the details page. Open to anyone
     * who can edit the order's content — including the people assigned to it.
     */
    public function storeDocument(Request $request, Order $order, OrderDocumentService $documentService)
    {
        $this->authorize('editContent', $order);

        $validated = $request->validate([
            'client_documents' => 'required|array|max:20',
            'client_documents.*' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,csv,txt,md,json,zip,png,jpg,jpeg,gif,webp',
        ]);

        $stored = $documentService->store($order, $request->file('client_documents', []));

        return redirect()->back()
            ->with('success', count($stored).' document(s) uploaded successfully.');
    }

    /**
     * Stream a stored client document back to the browser.
     */
    public function downloadDocument(Order $order, string $document): StreamedResponse
    {
        $this->authorize('view', $order);

        $file = $order->findDocument($document);

        abort_if($file === null, 404);

        $disk = Storage::disk($file['disk'] ?? config('filesystems.default'));

        abort_unless($disk->exists($file['path']), 404);

        return $disk->download($file['path'], $file['name']);
    }

    public function destroyDocument(Order $order, string $document, OrderDocumentService $documentService)
    {
        $this->authorize('deleteDocuments', $order);

        $removed = $documentService->delete($order, $document);

        if ($removed === null) {
            return response()->json(['message' => 'Document not found.'], 404);
        }

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Document removed successfully.',
                'documents' => $order->documents(),
            ]);
        }

        return back()->with('success', 'Document removed successfully.');
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
        $this->authorize('manageAssignment', $order);

        $validated = $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        $oldMembers = $order->users()->pluck('name')->all();

        // Sync users to the order
        $order->users()->sync($validated['users']);

        $newMembers = $order->users()->pluck('name')->all();

        if ($oldMembers !== $newMembers) {
            OrderAudit::record($order, 'members_updated', ['members' => $oldMembers], ['members' => $newMembers]);
        }

        // Send notifications to newly assigned users
        $assignedUsers = User::whereIn('id', $validated['users'])->get();

        foreach ($assignedUsers as $assignedUser) {
            EmailService::sendTo($assignedUser->email, new OrderAssignmentNotification($order, $assignedUser, false));
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

        $query = User::query();

        if ($user->hasRole(['admin', 'operation-manager', 'project-manager'])) {
            // Can see all users
        } else {
            $query->where('team_id', $user->team_id);
        }

        // Exclude already selected users
        if (! empty($excludeIds)) {
            $query->whereNotIn('id', $excludeIds);
        }

        // Callers that are crediting delivery work (value distribution) pass
        // this to keep sales and admin accounts — who don't do delivery
        // work — out of the results, without changing the plain "assign
        // someone to work on this order" search other callers still use.
        $excludeRoles = array_filter(explode(',', (string) $request->input('exclude_roles', '')));
        if (! empty($excludeRoles)) {
            $query->whereDoesntHave('roles', fn ($q) => $q->whereIn('name', $excludeRoles));
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

        // Limit results and include team and tech stack relationships
        $users = $query->with('team:id,name', 'techStack:id,name')
            ->select('id', 'name', 'email', 'employee_id', 'position', 'team_id', 'tech_stack_id')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json([
            'users' => $users,
        ]);
    }

    /**
     * Existing clients for the repeat-order picker: one entry per client
     * identity (client_key), searching client names directly on the orders
     * table — no profile selection required. Each entry carries the client_key
     * (so the order is attributed to the same client even if the typed name
     * differs) plus the profile of the client's latest order, which the form
     * uses to fill in the freelance profile when it is left empty.
     */
    public function searchClients(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:255',
            'exclude' => 'nullable|integer|exists:orders,id',
        ]);

        $query = Order::query()
            ->whereNotNull('client_key')
            ->where('client_key', '!=', '');

        if (filled($validated['exclude'] ?? null)) {
            $query->where('id', '!=', $validated['exclude']);
        }

        if (filled($validated['q'] ?? null)) {
            $query->where('name', 'like', '%'.$validated['q'].'%');
        }

        $clientKeys = $query
            ->selectRaw('client_key, MAX(created_at) as latest_at')
            ->groupBy('client_key')
            ->orderByDesc('latest_at')
            ->limit(5)
            ->pluck('client_key');

        if ($clientKeys->isEmpty()) {
            return response()->json(['clients' => []]);
        }

        $clients = Order::query()
            ->with('freelanceProfile')
            ->whereIn('client_key', $clientKeys)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('client_key')
            ->map(fn ($orders) => $orders->first())
            ->map(function (Order $order) {
                $profile = $order->freelanceProfile;

                return [
                    'key' => $order->client_key,
                    'name' => $order->name,
                    'profile_id' => $order->freelance_profile_id,
                    'profile_name' => $profile
                        ? $profile->username.' ('.ucfirst($profile->platform).')'
                        : '',
                ];
            })
            ->values();

        return response()->json(['clients' => $clients]);
    }
}
