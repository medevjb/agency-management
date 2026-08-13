@extends('layouts.dashboard')

@section('title', 'Freelance Profiles')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Freelance Profiles</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Manage freelance profiles and track their information</p>
            </div>
            @can('create profile')
                <button id="openCreateModal"
                        class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-semibold rounded-xl text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Profile
                </button>
            @endcan
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Profiles -->
        <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">Total Profiles</p>
                    <p class="text-3xl font-bold">{{ $profiles->total() }}</p>
                </div>
                <div class="bg-white/20 p-4 rounded-xl">
                    <i class="fas fa-address-card text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Profiles -->
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Active</p>
                    <p class="text-3xl font-bold">{{ App\Models\Profile::active()->count() }}</p>
                </div>
                <div class="bg-white/20 p-4 rounded-xl">
                    <i class="fas fa-check-circle text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Inactive Profiles -->
        <div class="bg-gradient-to-br from-red-500 to-pink-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium mb-1">Inactive</p>
                    <p class="text-3xl font-bold">{{ App\Models\Profile::where('status', 'inactive')->count() }}</p>
                </div>
                <div class="bg-white/20 p-4 rounded-xl">
                    <i class="fas fa-times-circle text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- With Avatar -->
        <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">With Avatar</p>
                    <p class="text-3xl font-bold">{{ App\Models\Profile::whereNotNull('avatar')->count() }}</p>
                </div>
                <div class="bg-white/20 p-4 rounded-xl">
                    <i class="fas fa-image text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md p-4 mb-6">
        <form action="{{ route('freelance-profiles.index') }}" method="GET" class="flex items-center gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 transition-all text-sm"
                       placeholder="Search by name or username...">
            </div>
            <span class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                <span class="font-bold">{{ $profiles->total() }}</span> found
            </span>
            @if(request('search'))
                <a href="{{ route('freelance-profiles.index') }}"
                   class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 font-medium text-sm transition-all">
                    <i class="fas fa-times mr-1"></i>
                    Clear
                </a>
            @endif
            <button type="submit"
                    class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 font-medium text-sm transition-all shadow-md">
                <i class="fas fa-search mr-1"></i>
                Search
            </button>
        </form>
    </div>

    <!-- Profiles Grid - Compact Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
        @foreach($profiles as $profile)
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
                <!-- Profile Header - Compact -->
                <div class="relative h-24">
                    @if($profile->avatar)
                        <img src="{{ asset('storage/' . $profile->avatar) }}"
                             alt="{{ $profile->profile_name }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center">
                            <i class="fas fa-user text-white text-4xl opacity-50"></i>
                        </div>
                    @endif

                    <!-- Status Badge - Compact -->
                    <div class="absolute top-2 right-2">
                        @if($profile->status === 'active')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-500 text-white shadow-md">
                                <i class="fas fa-check-circle mr-1 text-xs"></i>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-500 text-white shadow-md">
                                <i class="fas fa-times-circle mr-1 text-xs"></i>
                                Inactive
                            </span>
                        @endif
                    </div>

                    <!-- Verified Badge -->
                    @if($profile->is_verified)
                        <div class="absolute bottom-2 left-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-500 text-white shadow-md">
                                <i class="fas fa-check mr-1 text-xs"></i>
                                Verified
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Profile Info - Compact -->
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 truncate">{{ $profile->profile_name }}</h3>
                            <p class="text-purple-600 dark:text-purple-400 font-medium text-sm truncate"><span>@</span>{{$profile->username}}</p>
                        </div>
                    </div>

                    <!-- Rating -->
                    @if($profile->rating > 0)
                        <div class="flex items-center mb-3">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $profile->rating)
                                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                                    @else
                                        <i class="fas fa-star text-gray-300 dark:text-gray-600 text-sm"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">({{ $profile->total_jobs }} jobs)</span>
                        </div>
                    @endif

                    <!-- Compact Actions -->
                    <div class="flex items-center gap-2">
                        <button onclick="viewProfile({{ $profile->id }})"
                                class="flex-1 flex items-center justify-center px-3 py-2 bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-500/20 font-medium text-xs transition-all group-hover:bg-purple-100 dark:group-hover:bg-purple-500/20">
                            <i class="fas fa-eye mr-1 text-xs"></i>View
                        </button>
                        @can('edit profile')
                            <button onclick="editProfile({{ $profile->id }})"
                                    class="flex-1 flex items-center justify-center px-3 py-2 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/20 font-medium text-xs transition-all">
                                <i class="fas fa-edit mr-1 text-xs"></i>Edit
                            </button>
                            <button onclick="toggleStatus({{ $profile->id }}, '{{ $profile->status }}')"
                                    class="flex items-center justify-center px-2 py-2 {{ $profile->status === 'active' ? 'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20' : 'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-500/20' }} rounded-lg font-medium text-xs transition-all">
                                <i class="fas {{ $profile->status === 'active' ? 'fa-ban' : 'fa-check' }} text-xs"></i>
                            </button>
                        @endcan
                        @can('delete profile')
                            <button onclick="deleteProfile({{ $profile->id }})"
                                    class="flex items-center justify-center px-2 py-2 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 font-medium text-xs transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($profiles->hasPages())
        <div class="mt-8">
            {{ $profiles->appends(request()->except('page'))->links() }}
        </div>
    @endif
</div>

<!-- Create/Edit Modal -->
<div id="profileModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div id="modalBackdrop"
             class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
             aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white dark:bg-gray-900 dark:ring-1 dark:ring-gray-700 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-white/20 p-3 rounded-lg mr-4">
                            <i class="fas fa-user text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 id="modalTitle" class="text-xl font-bold text-white">Create Profile</h3>
                            <p class="text-purple-100 text-sm">Fill in the profile information</p>
                        </div>
                    </div>
                    <button onclick="closeModal()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="profileForm" class="p-6 space-y-6">
                @csrf
                <input type="hidden" id="profileId" name="id">

                <!-- Profile Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-id-badge mr-2 text-purple-600 dark:text-purple-400"></i>
                        Profile Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="profileName" name="profile_name" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 transition-all"
                           placeholder="e.g., John Doe">
                </div>

                <!-- Username -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-at mr-2 text-purple-600 dark:text-purple-400"></i>
                        Username <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 font-semibold">@</span>
                        <input type="text" id="username" name="username" required
                               class="w-full pl-8 pr-4 py-3 border border-gray-300 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 transition-all"
                               placeholder="e.g., johndoe">
                    </div>
                </div>

                <!-- Platform -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-globe mr-2 text-purple-600 dark:text-purple-400"></i>
                        Platform <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="platform" value="fiverr" checked class="peer sr-only">
                            <div class="p-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl text-center peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-500/10 transition-all hover:border-green-300 dark:hover:border-green-700">
                                <i class="fas fa-tag text-xl text-green-600 dark:text-green-400 mb-1"></i>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Fiverr</p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">20% fee</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="platform" value="upwork" class="peer sr-only">
                            <div class="p-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-500/10 transition-all hover:border-blue-300 dark:hover:border-blue-700">
                                <i class="fas fa-briefcase text-xl text-blue-600 dark:text-blue-400 mb-1"></i>
                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Upwork</p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400">10% fee</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Avatar with Preview -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-image mr-2 text-purple-600 dark:text-purple-400"></i>
                        Avatar
                    </label>

                    <!-- Image Preview Area -->
                    <div id="avatarPreviewContainer" class="mb-4 hidden">
                        <div class="relative inline-block">
                            <img id="avatarPreview"
                                 alt="Avatar Preview"
                                 class="w-32 h-32 rounded-2xl border-4 border-purple-200 dark:border-purple-900 shadow-lg object-cover">
                            <button type="button" onclick="removeAvatar()"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white w-8 h-8 rounded-full hover:bg-red-600 transition-colors shadow-lg">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Upload Button -->
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-xl hover:border-purple-500 dark:hover:border-purple-600 transition-colors">
                        <div class="space-y-1 text-center">
                            <div class="w-20 h-20 bg-purple-50 dark:bg-purple-500/10 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-cloud-upload-alt text-3xl text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                <label for="avatarInput" class="relative cursor-pointer bg-white dark:bg-gray-900 rounded-md font-medium text-purple-600 dark:text-purple-400 hover:text-purple-500 dark:hover:text-purple-300 focus-within:outline-none">
                                    <span>Upload a file</span>
                                    <input id="avatarInput" name="avatar" type="file" class="sr-only" accept="image/*">
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, GIF up to 2MB</p>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <button type="button" onclick="closeModal()"
                            class="flex-1 px-6 py-3 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 font-semibold transition-all">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 font-semibold transition-all shadow-lg">
                        <i class="fas fa-save mr-2"></i>
                        <span id="submitBtnText">Create</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div id="viewModalBackdrop"
             class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
             aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white dark:bg-gray-900 dark:ring-1 dark:ring-gray-700 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-white/20 p-3 rounded-lg mr-4">
                            <i class="fas fa-user text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Profile Details</h3>
                        </div>
                    </div>
                    <button onclick="closeViewModal()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <div class="text-center mb-6">
                    <div id="viewAvatarContainer" class="mb-4"></div>
                    <h3 id="viewProfileName" class="text-2xl font-bold text-gray-900 dark:text-gray-100"></h3>
                    <p id="viewUsername" class="text-purple-600 dark:text-purple-400 font-semibold">@</p>

                    <!-- Status Badge -->
                    <div id="viewStatusBadge" class="mt-3 inline-flex items-center px-4 py-2 rounded-full text-sm font-bold">
                    </div>
                </div>

                <div class="space-y-4">
                    <div id="viewOwnerContainer" class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 hidden">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">OWNER</p>
                        <p id="viewOwnerName" class="text-gray-900 dark:text-gray-100 font-semibold"></p>
                        <p id="viewOwnerEmail" class="text-sm text-gray-600 dark:text-gray-400"></p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 dark:bg-gray-800 px-6 py-4">
                <button onclick="closeViewModal()"
                        class="w-full px-6 py-3 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 font-semibold transition-all">
                    <i class="fas fa-times mr-2"></i>
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let isEditing = false;
let existingAvatar = null;

// Modal Elements
const profileModal = document.getElementById('profileModal');
const modalBackdrop = document.getElementById('modalBackdrop');
const viewModal = document.getElementById('viewModal');
const viewModalBackdrop = document.getElementById('viewModalBackdrop');

// Form Elements
const profileForm = document.getElementById('profileForm');
const profileIdInput = document.getElementById('profileId');
const profileNameInput = document.getElementById('profileName');
const usernameInput = document.getElementById('username');
const avatarInput = document.getElementById('avatarInput');
const avatarPreviewContainer = document.getElementById('avatarPreviewContainer');
const avatarPreview = document.getElementById('avatarPreview');

// Event Listeners
document.getElementById('openCreateModal').addEventListener('click', openCreateModal);
modalBackdrop.addEventListener('click', closeModal);
viewModalBackdrop.addEventListener('click', closeViewModal);
avatarInput.addEventListener('change', handleFileUpload);
profileForm.addEventListener('submit', submitForm);

function openCreateModal() {
    isEditing = false;
    document.getElementById('modalTitle').textContent = 'Create Profile';
    document.getElementById('submitBtnText').textContent = 'Create';
    resetForm();
    profileModal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    profileModal.classList.add('hidden');
    document.body.style.overflow = '';
    resetForm();
}

async function editProfile(id) {
    try {
        const response = await axios.get(`/freelance-profiles/${id}/edit`);
        const profile = response.data.profile;

        isEditing = true;
        document.getElementById('modalTitle').textContent = 'Edit Profile';
        document.getElementById('submitBtnText').textContent = 'Update';

        profileIdInput.value = profile.id;
        profileNameInput.value = profile.profile_name;
        usernameInput.value = profile.username;

        const platformRadio = profileForm.querySelector(`input[name="platform"][value="${profile.platform || 'fiverr'}"]`);
        if (platformRadio) {
            platformRadio.checked = true;
        }

        existingAvatar = profile.avatar;
        if (profile.avatar) {
            avatarPreview.src = '/storage/' + profile.avatar;
            avatarPreviewContainer.classList.remove('hidden');
        } else {
            avatarPreviewContainer.classList.add('hidden');
        }

        profileModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } catch (error) {
        Swal.fire({
            title: 'Error!',
            text: error.response?.data?.message || 'Failed to load profile',
            icon: 'error',
            confirmButtonColor: '#9333ea'
        });
    }
}

async function viewProfile(id) {
    try {
        const response = await axios.get(`/freelance-profiles/${id}`);
        const profile = response.data.profile;

        // Avatar
        const avatarContainer = document.getElementById('viewAvatarContainer');
        if (profile.avatar) {
            avatarContainer.innerHTML = `<img src="/storage/${profile.avatar}" alt="${profile.profile_name}" class="w-32 h-32 rounded-2xl border-4 border-white shadow-xl object-cover mx-auto">`;
        } else {
            avatarContainer.innerHTML = `<div class="w-32 h-32 rounded-2xl border-4 border-gray-200 dark:border-gray-700 shadow-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center mx-auto"><i class="fas fa-user text-white text-4xl"></i></div>`;
        }

        document.getElementById('viewProfileName').textContent = profile.profile_name;
        document.getElementById('viewUsername').textContent = '@' + profile.username;

        // Status Badge
        const statusBadge = document.getElementById('viewStatusBadge');
        if (profile.status === 'active') {
            statusBadge.className = 'mt-3 inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-green-500 text-white';
            statusBadge.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Active';
        } else {
            statusBadge.className = 'mt-3 inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-red-500 text-white';
            statusBadge.innerHTML = '<i class="fas fa-times-circle mr-1"></i>Inactive';
        }

        // Owner
        const ownerContainer = document.getElementById('viewOwnerContainer');
        if (profile.user) {
            ownerContainer.classList.remove('hidden');
            document.getElementById('viewOwnerName').textContent = profile.user.name;
            document.getElementById('viewOwnerEmail').textContent = profile.user.email;
        } else {
            ownerContainer.classList.add('hidden');
        }

        viewModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } catch (error) {
        Swal.fire({
            title: 'Error!',
            text: error.response?.data?.message || 'Failed to load profile',
            icon: 'error',
            confirmButtonColor: '#9333ea'
        });
    }
}

function closeViewModal() {
    viewModal.classList.add('hidden');
    document.body.style.overflow = '';
}

function handleFileUpload(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.src = e.target.result;
            avatarPreviewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
}

function removeAvatar() {
    existingAvatar = null;
    avatarPreview.src = '';
    avatarPreviewContainer.classList.add('hidden');
    avatarInput.value = '';
}

async function submitForm(event) {
    event.preventDefault();

    const formData = new FormData();
    formData.append('profile_name', profileNameInput.value);
    formData.append('username', usernameInput.value);
    formData.append('platform', profileForm.querySelector('input[name="platform"]:checked')?.value || 'fiverr');
    formData.append('bio', '');

    if (avatarInput.files[0]) {
        formData.append('avatar', avatarInput.files[0]);
    } else if (existingAvatar === null && isEditing) {
        formData.append('avatar', '');
    }

    try {
        if (isEditing) {
            formData.append('_method', 'PATCH');
            await axios.post(`/freelance-profiles/${profileIdInput.value}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        } else {
            await axios.post('/freelance-profiles', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        }

        Swal.fire({
            title: 'Success!',
            text: isEditing ? 'Profile updated successfully!' : 'Profile created successfully!',
            icon: 'success',
            confirmButtonColor: '#9333ea'
        }).then(() => {
            window.location.reload();
        });
    } catch (error) {
        Swal.fire({
            title: 'Error!',
            text: error.response?.data?.message || 'Something went wrong.',
            icon: 'error',
            confirmButtonColor: '#9333ea'
        });
    }
}

async function toggleStatus(id, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const action = currentStatus === 'active' ? 'disable' : 'enable';

    Swal.fire({
        title: `Are you sure?`,
        text: `You want to ${action} this profile?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#9333ea',
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Yes, ${action} it!`,
        cancelButtonText: 'Cancel'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.patch(`/freelance-profiles/${id}/toggle-status`, {
                    status: newStatus
                });

                Swal.fire({
                    title: 'Success!',
                    text: `Profile ${action}d successfully!`,
                    icon: 'success',
                    confirmButtonColor: '#9333ea'
                }).then(() => {
                    window.location.reload();
                });
            } catch (error) {
                Swal.fire({
                    title: 'Error!',
                    text: error.response?.data?.message || 'Something went wrong.',
                    icon: 'error',
                    confirmButtonColor: '#9333ea'
                });
            }
        }
    });
}

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
                        window.location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.response?.data?.message || 'Something went wrong.',
                        icon: 'error',
                        confirmButtonColor: '#9333ea'
                    });
                });
        }
    });
}

function resetForm() {
    profileForm.reset();
    profileIdInput.value = '';
    avatarPreview.src = '';
    avatarPreviewContainer.classList.add('hidden');
    existingAvatar = null;
    isEditing = false;
}

// Auto-open the create modal when arriving via the "Add Profile" link (?create=1)
if (new URLSearchParams(window.location.search).has('create')) {
    openCreateModal();
    window.history.replaceState({}, '', '{{ route('freelance-profiles.index') }}');
}
</script>
@endsection
