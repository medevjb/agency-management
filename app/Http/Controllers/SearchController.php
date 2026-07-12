<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Team;
use App\Models\TechStack;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function teams(Request $request): JsonResponse
    {
        $query = Team::query()->select(['id', 'name']);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        } else {
            $query->latest();
        }

        return response()->json($query->take(10)->get());
    }

    public function techStacks(Request $request): JsonResponse
    {
        $query = TechStack::query()->select(['id', 'name']);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        } else {
            $query->latest();
        }

        return response()->json($query->take(10)->get());
    }

    public function profiles(Request $request): JsonResponse
    {
        $query = Profile::query()->select(['id', 'username', 'platform']);

        if ($request->filled('q')) {
            $query->where('username', 'like', '%'.$request->q.'%')
                ->orWhere('platform', 'like', '%'.$request->q.'%');
        } else {
            $query->latest();
        }

        return response()->json(
            $query->take(10)->get()->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->username.' ('.ucfirst($p->platform).')',
            ])
        );
    }
}
