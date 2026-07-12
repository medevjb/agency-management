<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Profile;
use Illuminate\Http\Request;
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
        $query = Profile::query()->with(['user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('profile_name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min_rate')) {
            $query->whereNotNull('hourly_rate');
        }

        $profiles = $query->latest()->paginate(12);

        return view('freelance-profiles.index', compact('profiles'));
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

        $profile->load(['user']);

        if (request()->expectsJson()) {
            return response()->json([
                'profile' => $profile,
            ]);
        }

        return view('freelance-profiles.show', compact('profile'));
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
