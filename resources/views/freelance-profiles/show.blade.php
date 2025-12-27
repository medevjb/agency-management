@extends('layouts.dashboard')

@section('title', $profile->profile_name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('freelance-profiles.index') }}"
           class="inline-flex items-center text-purple-600 hover:text-purple-700 font-semibold transition-all">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Profiles
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Profile Header Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <!-- Cover & Avatar -->
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 pt-8 pb-20 relative">
                    @if($profile->is_featured)
                        <div class="absolute top-4 right-4">
                            <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-4 py-2 rounded-full shadow-lg">
                                <i class="fas fa-star mr-1"></i>Featured
                            </span>
                        </div>
                    @endif
                </div>

                <div class="px-6 pb-6">
                    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between -mt-16 mb-6">
                        <div class="flex flex-col sm:flex-row sm:items-end space-y-4 sm:space-y-0 sm:space-x-6">
                            @if($profile->avatar)
                                <img src="{{ asset('storage/' . $profile->avatar) }}"
                                     alt="{{ $profile->profile_name }}"
                                     class="w-32 h-32 rounded-2xl border-4 border-white shadow-xl object-cover">
                            @else
                                <div class="w-32 h-32 rounded-2xl border-4 border-white shadow-xl bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                            <div class="pb-2">
                                <div class="flex items-center space-x-3">
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $profile->profile_name }}</h1>
                                    @if($profile->is_verified)
                                        <i class="fas fa-badge-check text-blue-500 text-xl" title="Verified"></i>
                                    @endif
                                </div>
                                <p class="text-purple-600 font-semibold">@{{ $profile->username }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 pb-2">
                            @can('edit profile')
                                <a href="{{ route('freelance-profiles.edit', $profile) }}"
                                   class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 font-semibold text-sm transition-all">
                                    <i class="fas fa-edit mr-2"></i>Edit
                                </a>
                            @endcan
                            @can('delete profile')
                                <button onclick="deleteProfile('{{ $profile->id }}')"
                                        class="px-4 py-2 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 font-semibold text-sm transition-all">
                                    <i class="fas fa-trash mr-2"></i>Delete
                                </button>
                            @endcan
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $profile->status_badge }}">
                            <span class="w-2 h-2 rounded-full bg-current mr-2"></span>
                            {{ ucfirst($profile->status) }}
                        </span>
                    </div>

                    <!-- Bio -->
                    @if($profile->bio)
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-3">
                                <i class="fas fa-align-left mr-2 text-purple-600"></i>
                                About
                            </h3>
                            <p class="text-gray-700 leading-relaxed">{{ $profile->bio }}</p>
                        </div>
                    @endif

                    <!-- Skills -->
                    @if($profile->skills)
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-3">
                                <i class="fas fa-tools mr-2 text-purple-600"></i>
                                Skills
                            </h3>
                            <div class="flex flex-wrap gap-3">
                                @foreach(explode(',', $profile->skills) as $skill)
                                    <span class="px-4 py-2 bg-purple-50 text-purple-700 rounded-xl font-medium hover:bg-purple-100 transition-all">
                                        {{ trim($skill) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-chart-bar mr-2 text-purple-600"></i>
                    Statistics
                </h3>
                <div class="grid grid-cols-3 gap-6">
                    <!-- Rating -->
                    <div class="text-center p-4 bg-yellow-50 rounded-xl">
                        <div class="flex items-center justify-center mb-2">
                            <i class="fas fa-star text-yellow-500 text-2xl mr-2"></i>
                            <span class="text-3xl font-bold text-yellow-600">{{ $profile->rating }}</span>
                        </div>
                        <p class="text-sm text-gray-600 font-semibold">Rating</p>
                    </div>

                    <!-- Total Jobs -->
                    <div class="text-center p-4 bg-green-50 rounded-xl">
                        <div class="flex items-center justify-center mb-2">
                            <i class="fas fa-briefcase text-green-500 text-2xl mr-2"></i>
                            <span class="text-3xl font-bold text-green-600">{{ $profile->total_jobs }}</span>
                        </div>
                        <p class="text-sm text-gray-600 font-semibold">Jobs</p>
                    </div>

                    <!-- Hourly Rate -->
                    @if($profile->hourly_rate)
                        <div class="text-center p-4 bg-blue-50 rounded-xl">
                            <div class="flex items-center justify-center mb-2">
                                <i class="fas fa-dollar-sign text-blue-500 text-2xl mr-2"></i>
                                <span class="text-2xl font-bold text-blue-600">{{ $profile->hourly_rate }}</span>
                            </div>
                            <p class="text-sm text-gray-600 font-semibold">Hourly Rate</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-bolt mr-2 text-purple-600"></i>
                    Quick Actions
                </h3>
                <div class="space-y-3">
                    <!-- Toggle Status -->
                    @can('edit profile')
                        <button onclick="toggleStatus('{{ $profile->id }}')"
                                class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-indigo-50 text-purple-700 rounded-xl hover:from-purple-100 hover:to-indigo-100 transition-all group">
                            <div class="flex items-center">
                                <div class="bg-purple-500 p-3 rounded-lg mr-4">
                                    <i class="fas fa-power-off text-white"></i>
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold">Toggle Status</p>
                                    <p class="text-sm text-purple-600">Switch between active/inactive</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    @endcan

                    <!-- Toggle Verification -->
                    @can('edit profile')
                        <button onclick="toggleVerify('{{ $profile->id }}')"
                                class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-cyan-50 text-blue-700 rounded-xl hover:from-blue-100 hover:to-cyan-100 transition-all group">
                            <div class="flex items-center">
                                <div class="bg-blue-500 p-3 rounded-lg mr-4">
                                    <i class="fas fa-badge-check text-white"></i>
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold">{{ $profile->is_verified ? 'Unverify' : 'Verify' }} Profile</p>
                                    <p class="text-sm text-blue-600">{{ $profile->is_verified ? 'Remove verification badge' : 'Add verification badge' }}</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    @endcan

                    <!-- Toggle Featured -->
                    @can('edit profile')
                        <button onclick="toggleFeatured('{{ $profile->id }}')"
                                class="w-full flex items-center justify-between p-4 bg-gradient-to-r from-yellow-50 to-orange-50 text-yellow-700 rounded-xl hover:from-yellow-100 hover:to-orange-100 transition-all group">
                            <div class="flex items-center">
                                <div class="bg-yellow-500 p-3 rounded-lg mr-4">
                                    <i class="fas fa-star text-white"></i>
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold">{{ $profile->is_featured ? 'Remove from' : 'Add to' }} Featured</p>
                                    <p class="text-sm text-yellow-600">{{ $profile->is_featured ? 'Remove from featured list' : 'Show on featured list' }}</p>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Profile Info Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-info-circle mr-2 text-purple-600"></i>
                    Profile Info
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600">Status</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $profile->status_badge }}">
                            {{ ucfirst($profile->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600">Verified</span>
                        <span class="{{ $profile->is_verified ? 'text-green-600' : 'text-gray-400' }}">
                            <i class="fas {{ $profile->is_verified ? 'fa-check-circle' : 'fa-times-circle' }} text-xl"></i>
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600">Featured</span>
                        <span class="{{ $profile->is_featured ? 'text-yellow-600' : 'text-gray-400' }}">
                            <i class="fas {{ $profile->is_featured ? 'fa-star' : 'fa-star' }} text-xl"></i>
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-gray-600">Member Since</span>
                        <span class="text-gray-900 font-semibold">{{ $profile->created_at->format('M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Author Card -->
            @if($profile->user)
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-user mr-2 text-purple-600"></i>
                        Owner
                    </h3>
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $profile->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $profile->user->email }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Timestamps Card -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-clock mr-2 text-purple-600"></i>
                    Timestamps
                </h3>
                <div class="space-y-4">
                    <div class="py-3 border-b border-gray-100">
                        <p class="text-xs text-gray-500 mb-1">Created</p>
                        <p class="text-sm text-gray-900 font-semibold">{{ $profile->created_at->format('M d, Y - g:i A') }}</p>
                    </div>
                    <div class="py-3">
                        <p class="text-xs text-gray-500 mb-1">Last Updated</p>
                        <p class="text-sm text-gray-900 font-semibold">{{ $profile->updated_at->format('M d, Y - g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteProfile(id) {
    event.preventDefault();
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#9333ea',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/freelance-profiles/${id}`)
                .then(response => {
                    Swal.fire({
                        title: 'Deleted!',
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonColor: '#9333ea'
                    }).then(() => {
                        window.location.href = '{{ route('freelance-profiles.index') }}';
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.response.data.message || 'Something went wrong.',
                        icon: 'error',
                        confirmButtonColor: '#9333ea'
                    });
                });
        }
    });
}

function toggleStatus(id) {
    axios.patch(`/freelance-profiles/${id}/toggle-status`)
        .then(response => {
            Swal.fire({
                title: 'Success!',
                text: response.data.message,
                icon: 'success',
                confirmButtonColor: '#9333ea'
            }).then(() => {
                window.location.reload();
            });
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: error.response.data.message || 'Something went wrong.',
                icon: 'error',
                confirmButtonColor: '#9333ea'
            });
        });
}

function toggleVerify(id) {
    axios.patch(`/freelance-profiles/${id}/toggle-verify`)
        .then(response => {
            Swal.fire({
                title: 'Success!',
                text: response.data.message,
                icon: 'success',
                confirmButtonColor: '#9333ea'
            }).then(() => {
                window.location.reload();
            });
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: error.response.data.message || 'Something went wrong.',
                icon: 'error',
                confirmButtonColor: '#9333ea'
            });
        });
}

function toggleFeatured(id) {
    axios.patch(`/freelance-profiles/${id}/toggle-featured`)
        .then(response => {
            Swal.fire({
                title: 'Success!',
                text: response.data.message,
                icon: 'success',
                confirmButtonColor: '#9333ea'
            }).then(() => {
                window.location.reload();
            });
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: error.response.data.message || 'Something went wrong.',
                icon: 'error',
                confirmButtonColor: '#9333ea'
            });
        });
}
</script>
@endsection
