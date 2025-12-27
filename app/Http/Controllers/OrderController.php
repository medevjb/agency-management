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

        return view('orders.index', compact('teams', 'statuses', 'techStacks'));
    }

    public function getOrders(Request $request)
    {
        $query = Order::query()->with(['team', 'techStack', 'creator', 'users']);

        // Search
        if ($request->filled('search.value')) {
            $search = $request->search.value;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
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

        // Filter by priority
        if ($request->filled('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }

        // Order by column (DataTables column index)
        if ($request->filled('order')) {
            $orderColumn = $request->input('order.0.column');
            $orderDir = $request->input('order.0.dir');

            $columns = ['id', 'name', 'status', 'priority', 'actual_revenue', 'team_id', 'created_at'];
            if (isset($columns[$orderColumn])) {
                $query->orderBy($columns[$orderColumn], $orderDir);
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
                'name' => $order->name,
                'description' => $order->description,
                'status' => $order->status,
                'priority' => $order->priority,
                'actual_revenue' => $order->actual_revenue,
                'start_date' => $order->start_date ? $order->start_date->format('Y-m-d') : null,
                'end_date' => $order->end_date ? $order->end_date->format('Y-m-d') : null,
                'team' => $order->team ? $order->team->name : 'N/A',
                'tech_stack' => $order->techStack ? $order->techStack->name : 'N/A',
                'users' => $order->users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                    ];
                })->toArray(),
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

        return view('orders.create', compact('teams', 'techStacks', 'statuses', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_revenue' => 'nullable|numeric|min:0',
            'team_id' => 'nullable|exists:teams,id',
            'tech_stack_id' => 'nullable|exists:tech_stacks,id',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
        ]);

        // Calculate revenue after 20% split
        $actualRevenue = $validated['actual_revenue'] ?? null;
        $revenueAfterSplit = $actualRevenue ? $actualRevenue * 0.80 : null;

        $order = Order::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'actual_revenue' => $revenueAfterSplit,
            'team_id' => $validated['team_id'] ?? null,
            'tech_stack_id' => $validated['tech_stack_id'] ?? null,
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
        $order->load('users');

        return view('orders.edit', compact('order', 'teams', 'techStacks', 'statuses', 'users'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'actual_revenue' => 'nullable|numeric|min:0',
            'team_id' => 'nullable|exists:teams,id',
            'tech_stack_id' => 'nullable|exists:tech_stacks,id',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $order->status;
        $oldRevenue = $order->actual_revenue;

        // Calculate revenue after 20% split
        $actualRevenue = $validated['actual_revenue'] ?? null;
        $revenueAfterSplit = $actualRevenue ? $actualRevenue * 0.80 : null;

        $order->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'actual_revenue' => $revenueAfterSplit,
            'team_id' => $validated['team_id'] ?? null,
            'tech_stack_id' => $validated['tech_stack_id'] ?? null,
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
}
