<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePortfolioRequest;
use App\Http\Requests\UpdatePortfolioRequest;
use App\Models\Portfolio;
use App\Models\TechStack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view any portfolio')->only(['index', 'data']);
        $this->middleware('permission:view own portfolio')->only(['myPortfolios']);
        $this->middleware('permission:create portfolio')->only(['create', 'store']);
        $this->middleware('permission:edit own portfolio')->only(['edit', 'update']);
        $this->middleware('permission:edit any portfolio')->only(['edit', 'update']);
        $this->middleware('permission:delete own portfolio')->only(['destroy']);
        $this->middleware('permission:delete any portfolio')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Portfolio::class);

        $query = Portfolio::query()->with(['user', 'techStack']);

        if ($request->filled('tech_stack_id')) {
            $query->where('tech_stack_id', $request->tech_stack_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('user_id')) {
            $search = $request->user_id;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->where('project_date', '>=', $request->date_from);
        }

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            $portfolios = $query->latest()->paginate(12);

            return response()->json([
                'data' => $portfolios->items(),
                'has_more' => $portfolios->hasMorePages(),
                'next_page' => $portfolios->currentPage() + 1,
                'total' => $portfolios->total(),
            ]);
        }

        $portfolios = $query->latest()->paginate(12);
        $techStacks = TechStack::all();

        return view('portfolios.index', compact('portfolios', 'techStacks'));
    }

    public function myPortfolios(Request $request)
    {
        $query = auth()->user()->portfolios()->with(['techStack']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            $portfolios = $query->latest()->paginate(12);

            return response()->json([
                'data' => $portfolios->items(),
                'has_more' => $portfolios->hasMorePages(),
                'next_page' => $portfolios->currentPage() + 1,
                'total' => $portfolios->total(),
            ]);
        }

        $portfolios = $query->latest()->paginate(12);

        return view('portfolios.my-portfolios', compact('portfolios'));
    }

    public function create()
    {
        $this->authorize('create', Portfolio::class);

        $techStacks = TechStack::all();

        return view('portfolios.create', compact('techStacks'));
    }

    public function store(StorePortfolioRequest $request)
    {
        $this->authorize('create', Portfolio::class);

        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('screenshot')) {
            $data['screenshot'] = $request->file('screenshot')->store('portfolios', 'public');
        }

        Portfolio::create($data);

        return redirect()->route('portfolios.my')
            ->with('success', 'Portfolio project created successfully.');
    }

    public function show(Portfolio $portfolio)
    {
        $this->authorize('view', $portfolio);

        $portfolio->load(['user', 'techStack']);

        return view('portfolios.show', compact('portfolio'));
    }

    public function edit(Portfolio $portfolio)
    {
        $this->authorize('update', $portfolio);

        $techStacks = TechStack::all();

        return view('portfolios.edit', compact('portfolio', 'techStacks'));
    }

    public function update(UpdatePortfolioRequest $request, Portfolio $portfolio)
    {
        $this->authorize('update', $portfolio);

        $data = $request->validated();

        if ($request->hasFile('screenshot')) {
            if ($portfolio->screenshot) {
                Storage::disk('public')->delete($portfolio->screenshot);
            }
            $data['screenshot'] = $request->file('screenshot')->store('portfolios', 'public');
        }

        $portfolio->update($data);

        return redirect()->route('portfolios.my')
            ->with('success', 'Portfolio project updated successfully.');
    }

    public function destroy(Portfolio $portfolio)
    {
        $this->authorize('delete', $portfolio);

        if ($portfolio->screenshot) {
            Storage::disk('public')->delete($portfolio->screenshot);
        }

        $portfolio->delete();

        return redirect()->route('portfolios.my')
            ->with('success', 'Portfolio project deleted successfully.');
    }

    public function toggleStatus(Portfolio $portfolio)
    {
        $this->authorize('update', $portfolio);

        $newStatus = $portfolio->status === 'published' ? 'draft' : 'published';
        $portfolio->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Portfolio project {$newStatus} successfully.",
            'status' => $newStatus,
        ]);
    }

    public function toggleFeatured(Portfolio $portfolio)
    {
        $this->authorize('feature', $portfolio);

        $portfolio->update(['is_featured' => ! $portfolio->is_featured]);

        return response()->json([
            'success' => true,
            'message' => $portfolio->is_featured ? 'Portfolio marked as featured.' : 'Portfolio removed from featured.',
            'is_featured' => $portfolio->is_featured,
        ]);
    }
}
