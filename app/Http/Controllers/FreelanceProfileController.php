<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Order;
use App\Models\Profile;
use App\Services\ProfileAnalytics;
use App\Support\GlobalMonth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class FreelanceProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view profiles')->only(['index', 'data']);
        $this->middleware('permission:create profile')->only(['create', 'store']);
        $this->middleware('permission:edit profile')->only(['edit', 'update']);
        $this->middleware('permission:delete profile')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $profileQuery = Profile::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $profileQuery->where(function ($q) use ($search) {
                $q->where('profile_name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('platform')) {
            $profileQuery->where('platform', $request->platform);
        }

        if ($request->filled('status')) {
            $profileQuery->where('status', $request->status);
        }

        $month = GlobalMonth::current();
        $window = GlobalMonth::periodFor($month);

        // Profile ids only — no orders joined yet. Analytics are derived from
        // grouped aggregates (bounded by profile count) rather than by
        // hydrating every order of every profile into PHP.
        $allIds = (clone $profileQuery)->latest()->pluck('id')->all();

        $sort = $request->get('sort', 'revenue');
        $perPage = 12;
        $page = max((int) $request->get('page', 1), 1);

        if ($allIds === []) {
            $profiles = new LengthAwarePaginator([], 0, $perPage, $page, ['path' => $request->url(), 'query' => $request->query()]);

            return view('freelance-profiles.index', [
                'profiles' => $profiles,
                'profileStats' => collect(),
                'summary' => [
                    'total_profiles' => 0,
                    'active_profiles' => 0,
                    'month_revenue' => 0.0,
                    'month_orders' => 0,
                    'month_repeat_orders' => 0,
                    'month_repeat_rate' => 0.0,
                    'top_profile' => null,
                ],
                'sort' => $sort,
            ]);
        }

        $totals = Order::query()
            ->selectRaw('freelance_profile_id, COUNT(*) as orders_count, COALESCE(SUM(value), 0) as revenue')
            ->whereIn('freelance_profile_id', $allIds)
            ->groupBy('freelance_profile_id')
            ->get()
            ->keyBy('freelance_profile_id');

        $monthTotals = Order::query()
            ->selectRaw('freelance_profile_id, COUNT(*) as orders_count, COALESCE(SUM(value), 0) as revenue')
            ->whereIn('freelance_profile_id', $allIds)
            ->whereBetween('created_at', $window)
            ->groupBy('freelance_profile_id')
            ->get()
            ->keyBy('freelance_profile_id');

        $repeatCounts = $this->repeatCountsByProfile($allIds);
        $monthRepeatCounts = $this->repeatCountsByProfile($allIds, $window);

        $monthRevenue = $monthTotals->sum(fn ($row) => (float) $row->revenue);
        $monthOrders = $monthTotals->sum(fn ($row) => (int) $row->orders_count);
        $monthRepeatOrders = $monthRepeatCounts->sum(fn ($row) => (int) $row->repeat_count);

        $summary = [
            'total_profiles' => count($allIds),
            'active_profiles' => Profile::query()->whereIn('id', $allIds)->where('status', 'active')->count(),
            'month_revenue' => $monthRevenue,
            'month_orders' => $monthOrders,
            'month_repeat_orders' => $monthRepeatOrders,
        ];
        $summary['month_repeat_rate'] = $monthOrders > 0
            ? round(($monthRepeatOrders / $monthOrders) * 100, 1)
            : 0.0;

        $topId = $monthTotals
            ->filter(fn ($row) => (float) $row->revenue > 0)
            ->sortByDesc(fn ($row) => (float) $row->revenue)
            ->keys()
            ->first();
        $summary['top_profile'] = $topId ? Profile::find($topId) : null;

        $ranked = collect($allIds)->sortByDesc(function ($id) use ($totals, $repeatCounts, $sort) {
            $orders = (int) ($totals[$id]->orders_count ?? 0);
            $revenue = (float) ($totals[$id]->revenue ?? 0);
            $repeats = (int) ($repeatCounts[$id]->repeat_count ?? 0);

            return match ($sort) {
                'repeat_rate' => $orders > 0 ? $repeats / $orders : 0.0,
                'orders' => $orders,
                default => $revenue,
            };
        })->values();

        $pageIds = $ranked->slice(($page - 1) * $perPage, $perPage)->values();

        $pageProfiles = Profile::query()
            ->with(['user'])
            ->with(['orders' => function ($ordersQuery) {
                $ordersQuery->select(['id', 'freelance_profile_id', 'name', 'client_key', 'value', 'status', 'created_at', 'is_repeat']);
            }])
            ->whereIn('id', $pageIds)
            ->get()
            ->keyBy('id');

        // Preserve the ranked order for the current page.
        $ordered = $pageIds->map(fn ($id) => $pageProfiles[$id] ?? null)->filter()->values();

        $profiles = new LengthAwarePaginator(
            $ordered,
            $ranked->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $profileStats = $ordered->mapWithKeys(function (Profile $profile) use ($month) {
            $a = new ProfileAnalytics($profile);

            return [$profile->id => [
                'total_orders' => $a->totalOrders(),
                'total_revenue' => $a->totalRevenue(),
                'month_orders' => $a->monthOrderCount($month),
                'month_revenue' => $a->monthRevenue($month),
                'repeat_rate' => $a->repeatOrderRate(),
                'repeat_clients' => $a->repeatClientCount(),
                'unique_clients' => $a->uniqueClientCount(),
            ]];
        });

        return view('freelance-profiles.index', compact('profiles', 'profileStats', 'summary', 'sort'));
    }

    /**
     * Repeat-order counts per profile, from the stored manual is_repeat flag
     * (set when the order was entered) rather than inferred client history.
     *
     * @return Collection<int, Order>
     */
    private function repeatCountsByProfile(array $ids, ?array $window = null): Collection
    {
        $query = Order::query()
            ->selectRaw('freelance_profile_id, COUNT(*) as repeat_count')
            ->whereIn('freelance_profile_id', $ids)
            ->where('is_repeat', true);

        if ($window !== null) {
            $query->whereBetween('created_at', $window);
        }

        return $query->groupBy('freelance_profile_id')
            ->get()
            ->keyBy('freelance_profile_id');
    }

    public function store(StoreProfileRequest $request)
    {
        $this->authorize('create', Profile::class);

        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('profiles', 'public');
        }

        Profile::create($data);

        return redirect()->route('freelance-profiles.index')
            ->with('success', 'Profile created successfully.');
    }

    public function show(Profile $profile)
    {
        $this->authorize('view', $profile);

        $profile->load(['user', 'orders' => function ($ordersQuery) {
            $ordersQuery->select(['id', 'freelance_profile_id', 'order_id', 'name', 'client_key', 'value', 'status', 'created_at', 'is_repeat'])
                ->with('salesShares.user');
        }]);

        if (request()->expectsJson()) {
            return response()->json([
                'profile' => $profile,
            ]);
        }

        $analytics = new ProfileAnalytics($profile);
        $month = GlobalMonth::current();

        return view('freelance-profiles.show', [
            'profile' => $profile,
            'analytics' => $analytics,
            'month' => $month,
        ]);
    }

    public function edit(Profile $profile)
    {
        $this->authorize('update', $profile);

        if (request()->expectsJson()) {
            return response()->json([
                'profile' => $profile,
            ]);
        }

        return view('freelance-profiles.edit', compact('profile'));
    }

    public function update(UpdateProfileRequest $request, Profile $profile)
    {
        $this->authorize('update', $profile);

        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('profiles', 'public');
        }

        $profile->update($data);

        return redirect()->route('freelance-profiles.index')
            ->with('success', 'Profile updated successfully.');
    }

    public function toggleStatus(Request $request, Profile $profile)
    {
        $this->authorize('update', $profile);

        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $profile->update(['status' => $request->status]);

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Profile status updated successfully.',
                'profile' => $profile,
            ]);
        }

        return redirect()->route('freelance-profiles.index')
            ->with('success', 'Profile status updated successfully.');
    }

    public function destroy(Profile $profile)
    {
        $this->authorize('delete', $profile);

        if ($profile->avatar) {
            Storage::disk('public')->delete($profile->avatar);
        }

        $profile->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Profile deleted successfully.',
            ]);
        }

        return redirect()->route('freelance-profiles.index')
            ->with('success', 'Profile deleted successfully.');
    }
}
