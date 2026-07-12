@extends('layouts.dashboard')

@section('title', 'Edit Profile - ' . $profile->profile_name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-4">
            <a href="{{ route('freelance-profiles.show', $profile) }}"
               class="p-3 bg-purple-100 rounded-xl hover:bg-purple-200 transition-all">
                <i class="fas fa-arrow-left text-purple-700"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Profile</h1>
                <p class="mt-2 text-sm text-gray-600">Update the profile information for {{ $profile->profile_name }}</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('freelance-profiles.update', $profile) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PATCH')

            <!-- Basic Information -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <!-- Section Header -->
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                    <div class="flex items-center">
                        <div class="bg-white/20 p-3 rounded-lg mr-4">
                            <i class="fas fa-user text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Basic Information</h2>
                            <p class="text-purple-100 text-sm">Profile name and username details</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Profile Name -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-id-badge mr-2 text-purple-600"></i>
                            Profile Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="profile_name" value="{{ old('profile_name', $profile->profile_name) }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                               placeholder="e.g., John Doe">
                        @error('profile_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-at mr-2 text-purple-600"></i>
                            Username <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-semibold">@</span>
                            <input type="text" name="username" value="{{ old('username', $profile->username) }}" required
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                                   placeholder="e.g., johndoe">
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Only letters, numbers, and underscores allowed</p>
                        @error('username')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Avatar & Upload -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-image mr-2 text-purple-600"></i>
                            Profile Avatar
                        </label>

                        <!-- Current Avatar -->
                        @if($profile->avatar)
                            <div class="mb-4 flex items-center p-4 bg-gray-50 rounded-xl">
                                <img src="{{ asset('storage/' . $profile->avatar) }}"
                                     alt="{{ $profile->profile_name }}"
                                     class="w-16 h-16 rounded-xl object-cover mr-4">
                                <div>
                                    <p class="font-semibold text-gray-900">Current Avatar</p>
                                    <p class="text-sm text-gray-600">Upload a new one to replace</p>
                                </div>
                            </div>
                        @endif

                        <!-- Upload New -->
                        <div class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-purple-500 transition-colors">
                            <div class="space-y-2 text-center">
                                <div class="w-20 h-20 bg-purple-50 rounded-full flex items-center justify-center mx-auto">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-purple-600"></i>
                                </div>
                                <div class="flex text-sm text-gray-600">
                                    <label for="avatar" class="relative cursor-pointer bg-white rounded-md font-medium text-purple-600 hover:text-purple-500 focus-within:outline-none">
                                        <span>Upload a file</span>
                                        <input id="avatar" name="avatar" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                        @error('avatar')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <!-- Section Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-6 py-4">
                    <div class="flex items-center">
                        <div class="bg-white/20 p-3 rounded-lg mr-4">
                            <i class="fas fa-info-circle text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Profile Details</h2>
                            <p class="text-indigo-100 text-sm">Additional information about the profile</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Bio -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-align-left mr-2 text-indigo-600"></i>
                            Bio
                        </label>
                        <textarea name="bio" rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all resize-none"
                                  placeholder="Write a short bio...">{{ old('bio', $profile->bio) }}</textarea>
                        <p class="mt-2 text-xs text-gray-500">Maximum 1000 characters</p>
                        @error('bio')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Skills -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-tools mr-2 text-indigo-600"></i>
                            Skills
                        </label>
                        <input type="text" name="skills" value="{{ old('skills', $profile->skills) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                               placeholder="e.g., PHP, Laravel, React, Node.js">
                        <p class="mt-2 text-xs text-gray-500">Separate skills with commas</p>
                        @error('skills')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hourly Rate -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-dollar-sign mr-2 text-indigo-600"></i>
                            Hourly Rate
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-semibold">$</span>
                            <input type="text" name="hourly_rate" value="{{ old('hourly_rate', $profile->hourly_rate) }}"
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                                   placeholder="e.g., 25-50 or 50">
                        </div>
                        @error('hourly_rate')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Platform -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-globe mr-2 text-indigo-600"></i>
                            Platform <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="platform" value="fiverr" {{ old('platform', $profile->platform) === 'fiverr' ? 'checked' : '' }} class="peer sr-only">
                                <div class="p-4 border-2 border-gray-200 rounded-xl text-center peer-checked:border-green-500 peer-checked:bg-green-50 transition-all hover:border-green-300">
                                    <i class="fas fa-tag text-2xl text-green-600 mb-2"></i>
                                    <p class="font-semibold text-gray-900">Fiverr</p>
                                    <p class="text-xs text-gray-500">20% platform fee</p>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="platform" value="upwork" {{ old('platform', $profile->platform) === 'upwork' ? 'checked' : '' }} class="peer sr-only">
                                <div class="p-4 border-2 border-gray-200 rounded-xl text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all hover:border-blue-300">
                                    <i class="fas fa-briefcase text-2xl text-blue-600 mb-2"></i>
                                    <p class="font-semibold text-gray-900">Upwork</p>
                                    <p class="text-xs text-gray-500">10% platform fee</p>
                                </div>
                            </label>
                        </div>
                        @error('platform')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Profile Settings -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <!-- Section Header -->
                <div class="bg-gradient-to-r from-blue-600 to-cyan-600 px-6 py-4">
                    <div class="flex items-center">
                        <div class="bg-white/20 p-3 rounded-lg mr-4">
                            <i class="fas fa-cog text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Profile Settings</h2>
                            <p class="text-blue-100 text-sm">Status, verification, and display settings</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-signal mr-2 text-blue-600"></i>
                            Status
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="status" value="active" {{ old('status', $profile->status) == 'active' ? 'checked' : '' }} class="peer sr-only">
                                <div class="p-4 border-2 border-gray-200 rounded-xl text-center peer-checked:border-green-500 peer-checked:bg-green-50 transition-all hover:border-green-300">
                                    <i class="fas fa-check-circle text-2xl text-green-600 mb-2"></i>
                                    <p class="font-semibold text-gray-900">Active</p>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="status" value="inactive" {{ old('status', $profile->status) == 'inactive' ? 'checked' : '' }} class="peer sr-only">
                                <div class="p-4 border-2 border-gray-200 rounded-xl text-center peer-checked:border-gray-500 peer-checked:bg-gray-50 transition-all hover:border-gray-300">
                                    <i class="fas fa-minus-circle text-2xl text-gray-600 mb-2"></i>
                                    <p class="font-semibold text-gray-900">Inactive</p>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="status" value="busy" {{ old('status', $profile->status) == 'busy' ? 'checked' : '' }} class="peer sr-only">
                                <div class="p-4 border-2 border-gray-200 rounded-xl text-center peer-checked:border-red-500 peer-checked:bg-red-50 transition-all hover:border-red-300">
                                    <i class="fas fa-exclamation-circle text-2xl text-red-600 mb-2"></i>
                                    <p class="font-semibold text-gray-900">Busy</p>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="status" value="away" {{ old('status', $profile->status) == 'away' ? 'checked' : '' }} class="peer sr-only">
                                <div class="p-4 border-2 border-gray-200 rounded-xl text-center peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition-all hover:border-yellow-300">
                                    <i class="fas fa-clock text-2xl text-yellow-600 mb-2"></i>
                                    <p class="font-semibold text-gray-900">Away</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Rating -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-star mr-2 text-blue-600"></i>
                            Rating
                        </label>
                        <div class="flex items-center space-x-4">
                            <input type="range" name="rating" min="0" max="5" value="{{ old('rating', $profile->rating) }}" id="ratingRange"
                                   class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
                                   oninput="updateRating(this.value)">
                            <span id="ratingValue" class="text-2xl font-bold text-blue-600">{{ old('rating', $profile->rating) }}</span>
                            <span class="text-gray-500">/ 5</span>
                        </div>
                    </div>

                    <!-- Total Jobs -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-briefcase mr-2 text-blue-600"></i>
                            Total Jobs Completed
                        </label>
                        <input type="number" name="total_jobs" value="{{ old('total_jobs', $profile->total_jobs) }}" min="0"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                               placeholder="e.g., 25">
                        @error('total_jobs')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Verification & Featured Toggles -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Verified -->
                        <label class="relative cursor-pointer">
                            <input type="checkbox" name="is_verified" value="1" {{ old('is_verified', $profile->is_verified) ? 'checked' : '' }} class="peer sr-only">
                            <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                                        <i class="fas fa-badge-check text-2xl text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">Verified Profile</p>
                                        <p class="text-sm text-gray-600">Mark as verified</p>
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- Featured -->
                        <label class="relative cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $profile->is_featured) ? 'checked' : '' }} class="peer sr-only">
                            <div class="p-4 border-2 border-gray-200 rounded-xl peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition-all">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                                        <i class="fas fa-star text-2xl text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">Featured Profile</p>
                                        <p class="text-sm text-gray-600">Show on featured list</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="{{ route('freelance-profiles.show', $profile) }}"
                       class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-semibold transition-all text-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <div class="flex gap-3 w-full sm:w-auto">
                        <a href="{{ route('freelance-profiles.index') }}"
                           class="w-full sm:w-auto px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-semibold transition-all text-center">
                            <i class="fas fa-list mr-2"></i>
                            Back to List
                        </a>
                        <button type="submit"
                                class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 font-semibold transition-all shadow-lg hover:shadow-xl">
                            <i class="fas fa-save mr-2"></i>
                            Update Profile
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function updateRating(value) {
    document.getElementById('ratingValue').textContent = value;
}
</script>
@endsection
