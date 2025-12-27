<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $teams = \App\Models\Team::all();
        $statuses = \App\Models\OrderStatus::orderBy('sort_order')->get();
        $techStacks = \App\Models\TechStack::all();
        $profiles = \App\Models\Profile::active()->get();

        return view('orders.index', compact('teams', 'statuses', 'techStacks', 'profiles'));
    }

    public function getOrders(Request $request)
    {
        $query = Order::query()->with(['team', 'techStack', 'freelanceProfile', 'creator', 'users']);

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
            $columns = ['order_id', 'name', 'status', 'priority', 'actual_revenue', 'team_id', 'tech_stack_id', 'freelance_profile_id', 'end_date'];
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
                'team' => $order->team ? $order->team->name : 'N/A',
                'tech_stack' => $order->techStack ? $order->techStack->name : 'N/A',
                'freelance_profile' => $order->freelanceProfile ? $order->freelanceProfile->username : 'N/A',
                'freelance_profile_id' => $order->freelance_profile_id,
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

    public function create()
    {
        $teams = \App\Models\Team::all();
        $techStacks = \App\Models\TechStack::all();
        $statuses = \App\Models\OrderStatus::orderBy('sort_order')->get();
        $users = \App\Models\User::all();
        $profiles = \App\Models\Profile::active()->get();

        return view('orders.create', compact('teams', 'techStacks', 'statuses', 'users', 'profiles'));
    }

    public function store(Request $request)
    {
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
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        // Calculate revenue after 20% split
        $actualRevenue = $validated['actual_revenue'] ?? null;

        $order = Order::create([
            'order_id' => $validated['order_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'value' => $validated['value'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'actual_revenue' => $actualRevenue,
            'team_id' => $validated['team_id'] ?? null,
            'tech_stack_id' => $validated['tech_stack_id'] ?? null,
            'freelance_profile_id' => $validated['freelance_profile_id'] ?? null,
            'created_by' => auth()->id(),
        ]);

        // Attach users
        if (! empty($validated['users'])) {
            $order->users()->attach($validated['users']);
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
        $order->load(['team', 'techStack', 'creator', 'users', 'statusHistory.changedByUser']);

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $teams = \App\Models\Team::all();
        $techStacks = \App\Models\TechStack::all();
        $statuses = \App\Models\OrderStatus::orderBy('sort_order')->get();
        $users = \App\Models\User::all();
        $profiles = \App\Models\Profile::active()->get();
        $order->load('users');

        return view('orders.edit', compact('order', 'teams', 'techStacks', 'statuses', 'users', 'profiles'));
    }

    public function update(Request $request, Order $order)
    {
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
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $order->status;
        $oldRevenue = $order->actual_revenue;

        // Calculate revenue after 20% split
        $actualRevenue = $validated['actual_revenue'] ?? null;

        $order->update([
            'order_id' => $validated['order_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'value' => $validated['value'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'actual_revenue' => $actualRevenue,
            'team_id' => $validated['team_id'] ?? null,
            'tech_stack_id' => $validated['tech_stack_id'] ?? null,
            'freelance_profile_id' => $validated['freelance_profile_id'] ?? null,
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
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    public function quickUpdate(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $order->update($validated);

        return response()->json([
            'message' => 'Order updated successfully.',
            'order' => $order->fresh(),
        ]);
    }
}
