<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('projects.index');
    }

    /**
     * Get projects data for DataTable.
     */
    public function getProjects(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = Project::with(['team', 'techStack', 'createdBy']);

        // Apply role-based filtering only if user is authenticated
        if ($user && !$user->hasPermissionTo('view all projects')) {
            $query->where('team_id', $user->team_id)
                  ->orWhereHas('members', function($query) use ($user) {
                      $query->where('user_id', $user->id);
                  });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        return DataTables::of($query)
            ->editColumn('name', function ($project) {
                return '
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                <i class="fas fa-folder text-indigo-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">' . $project->name . '</div>
                            <div class="text-sm text-gray-500">' . \Illuminate\Support\Str::limit($project->description, 50) . '</div>
                        </div>
                    </div>
                ';
            })
            ->addColumn('status', function ($project) {
                $statusClass = match($project->status) {
                    'completed' => 'bg-green-100 text-green-800',
                    'in-progress' => 'bg-blue-100 text-blue-800',
                    'on-hold' => 'bg-yellow-100 text-yellow-800',
                    'planning' => 'bg-gray-100 text-gray-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                    default => 'bg-gray-100 text-gray-800'
                };

                return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $statusClass . '">' .
                       ucfirst(str_replace('-', ' ', $project->status)) . '</span>';
            })
            ->addColumn('priority', function ($project) {
                $priorityClass = match($project->priority) {
                    'urgent' => 'bg-red-100 text-red-800',
                    'high' => 'bg-orange-100 text-orange-800',
                    'medium' => 'bg-blue-100 text-blue-800',
                    'low' => 'bg-gray-100 text-gray-800',
                    default => 'bg-gray-100 text-gray-800'
                };

                return '<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' . $priorityClass . '">' .
                       ucfirst($project->priority) . '</span>';
            })
            ->editColumn('team.name', function ($project) {
                return $project->team?->name ?? 'Unassigned';
            })
            ->editColumn('techStack.name', function ($project) {
                return $project->techStack?->name ?? 'None';
            })
            ->addColumn('progress', function ($project) {
                $progress = rand(0, 100); // You can calculate real progress based on tasks
                return '
                    <div class="flex items-center">
                        <div class="flex-1 mr-2">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: ' . $progress . '%"></div>
                            </div>
                        </div>
                        <span class="text-sm text-gray-600">' . $progress . '%</span>
                    </div>
                ';
            })
            ->editColumn('created_at', function ($project) {
                return $project->created_at->diffForHumans();
            })
            ->addColumn('action', function ($project) {
                $actions = '<div class="text-sm font-medium space-x-3">';

                $actions .= '<a href="' . route('projects.show', $project->id) . '" class="text-indigo-600 hover:text-indigo-900">View</a>';

                if (auth()->user()->can('edit stack projects')) {
                    $actions .= '<a href="' . route('projects.edit', $project->id) . '" class="text-blue-600 hover:text-blue-900">Edit</a>';
                }

                if (auth()->user()->can('manage all projects')) {
                    $actions .= '<form action="' . route('projects.destroy', $project->id) . '" method="POST" class="inline-block">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm(\'Are you sure?\')">Delete</button>
                    </form>';
                }

                $actions .= '</div>';

                return $actions;
            })
            ->rawColumns(['name', 'status', 'priority', 'progress', 'action'])
            ->setRowId('id')
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
