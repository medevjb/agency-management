<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBugReportRequest;
use App\Http\Requests\UpdateBugReportRequest;
use App\Jobs\TriageBugReportJob;
use App\Models\BugCategory;
use App\Models\BugImage;
use App\Models\BugPriority;
use App\Models\BugReport;
use App\Notifications\BugReportStatusNotification;
use App\Notifications\BugReportSubmittedNotification;
use App\Services\BugTriageService;
use App\Support\NotificationRecipients;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BugReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', BugReport::class);

        $user = auth()->user();

        $bugReports = BugReport::with(['user', 'category', 'priority', 'images'])
            ->when(! $user->hasRole('admin'), function ($query) use ($user) {
                // Non-admin users only see their own bug reports
                $query->where('user_id', $user->id);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->category, function ($query, $category) {
                $query->where('bug_category_id', $category);
            })
            ->when($request->priority, function ($query, $priority) {
                $query->where('bug_priority_id', $priority);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10);

        $categories = BugCategory::all();
        $priorities = BugPriority::orderBy('level')->get();

        $stats = [
            'total' => BugReport::when(! $user->hasRole('admin'), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
            'pending' => BugReport::when(! $user->hasRole('admin'), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'pending')->count(),
            'in_progress' => BugReport::when(! $user->hasRole('admin'), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'in_progress')->count(),
            'resolved' => BugReport::when(! $user->hasRole('admin'), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'resolved')->count(),
            'published' => BugReport::when(! $user->hasRole('admin'), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'published')->count(),
        ];

        return view('bug-reports.index', compact('bugReports', 'categories', 'priorities', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(BugTriageService $bugTriageService): View
    {
        $this->authorize('create', BugReport::class);

        $categories = BugCategory::all();
        $priorities = BugPriority::orderBy('level')->get();
        $aiTriageAvailable = $bugTriageService->isConfigured();

        return view('bug-reports.create', compact('categories', 'priorities', 'aiTriageAvailable'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBugReportRequest $request, BugTriageService $bugTriageService): RedirectResponse
    {
        $this->authorize('create', BugReport::class);

        $bugReport = BugReport::create([
            'user_id' => auth()->id(),
            'bug_category_id' => $request->bug_category_id,
            'bug_priority_id' => $request->bug_priority_id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('bug-reports', 'public');

                BugImage::create([
                    'bug_report_id' => $bugReport->id,
                    'image_path' => $path,
                    'image_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
                    'sort_order' => $index,
                ]);
            }
        }

        if ($bugTriageService->isConfigured() && (! $request->bug_category_id || ! $request->bug_priority_id)) {
            TriageBugReportJob::dispatch($bugReport->fresh(['images']));
        }

        Notification::send(NotificationRecipients::management(), new BugReportSubmittedNotification($bugReport));

        return redirect()->route('bug-reports.show', $bugReport)
            ->with('success', 'Bug report submitted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BugReport $bugReport): View
    {
        $this->authorize('view', $bugReport);

        $bugReport->load(['user', 'category', 'priority', 'images', 'resolvedBy']);

        return view('bug-reports.show', compact('bugReport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BugReport $bugReport): View
    {
        $this->authorize('update', $bugReport);

        $bugReport->load(['images']);
        $categories = BugCategory::all();
        $priorities = BugPriority::orderBy('level')->get();

        return view('bug-reports.edit', compact('bugReport', 'categories', 'priorities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBugReportRequest $request, BugReport $bugReport): RedirectResponse
    {
        $this->authorize('update', $bugReport);

        $oldStatus = $bugReport->status;

        $bugReport->update([
            'bug_category_id' => $request->bug_category_id,
            'bug_priority_id' => $request->bug_priority_id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        if ($bugReport->status !== $oldStatus && $bugReport->user_id !== auth()->id()) {
            $bugReport->user->notify(new BugReportStatusNotification($bugReport, $oldStatus));
        }

        // Handle status transitions
        if ($request->status === 'resolved' && ! $bugReport->resolved_at) {
            $bugReport->update([
                'resolved_at' => now(),
                'resolved_by' => auth()->id(),
            ]);
        }

        if ($request->status === 'published' && ! $bugReport->published_at) {
            $bugReport->update([
                'published_at' => now(),
            ]);
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $currentMaxOrder = $bugReport->images()->max('sort_order') ?? 0;

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('bug-reports', 'public');

                BugImage::create([
                    'bug_report_id' => $bugReport->id,
                    'image_path' => $path,
                    'image_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
                    'sort_order' => $currentMaxOrder + $index + 1,
                ]);
            }
        }

        return redirect()->route('bug-reports.show', $bugReport)
            ->with('success', 'Bug report updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BugReport $bugReport): RedirectResponse
    {
        $this->authorize('delete', $bugReport);

        // Delete associated images from storage
        foreach ($bugReport->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $bugReport->delete();

        return redirect()->route('bug-reports.index')
            ->with('success', 'Bug report deleted successfully.');
    }

    /**
     * Remove a specific image from a bug report.
     */
    public function deleteImage(BugReport $bugReport, BugImage $image): RedirectResponse
    {
        $this->authorize('update', $bugReport);

        if ($image->bug_report_id !== $bugReport->id) {
            abort(404);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return redirect()->back()
            ->with('success', 'Image deleted successfully.');
    }
}
