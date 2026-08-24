@extends('layouts.dashboard')

@section('title', 'Freelance Profiles')

@push('styles')
<style>
    .s-panel {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
    }

    .dark .s-panel { background-color: #111827; border-color: #1f2937; box-shadow: none; }

    .s-eyebrow {
        font-size: 0.6875rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #6b7280;
    }

    .dark .s-eyebrow { color: #9ca3af; }

    .s-num { font-variant-numeric: tabular-nums; font-feature-settings: 'tnum'; }

    .s-stat-value {
        margin-top: 0.25rem;
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #111827;
        font-variant-numeric: tabular-nums;
    }

    .dark .s-stat-value { color: #f3f4f6; }

    .s-stat-foot { margin-top: 0.125rem; font-size: 0.75rem; color: #9ca3af; }

    .s-grid-4 {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 1rem;
    }

    @media (min-width: 640px) { .s-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (min-width: 1024px) { .s-grid-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); } }

    .s-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.625rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        background-color: #FFF3EC;
        color: #C63508;
    }

    .dark .s-chip { background-color: rgba(255, 90, 31, 0.15); color: #FFA26E; }

    .s-chip.is-new { background-color: #f3f4f6; color: #4b5563; }
    .dark .s-chip.is-new { background-color: #1f2937; color: #9ca3af; }
</style>
@endpush

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

    <!-- Summary Stats -->
    <div class="s-grid-4 mb-8">
        <div class="s-panel p-5">
            <p class="s-eyebrow">Total Profiles</p>
            <p class="s-stat-value">{{ $summary['total_profiles'] }}</p>
            <p class="s-stat-foot">{{ $summary['active_profiles'] }} active</p>
        </div>

        <div class="s-panel p-5">
            <p class="s-eyebrow">{{ \App\Support\GlobalMonth::label() }} Revenue</p>
            <p class="s-stat-value">${{ number_format($summary['month_revenue'], 2) }}</p>
            <p class="s-stat-foot">{{ $summary['month_orders'] }} orders this month</p>
        </div>

        <div class="s-panel p-5">
            <p class="s-eyebrow">Repeat Order Rate</p>
            <p class="s-stat-value">{{ $summary['month_repeat_rate'] }}%</p>
            <p class="s-stat-foot">{{ $summary['month_repeat_orders'] }} of {{ $summary['month_orders'] }} this month were repeat business</p>
        </div>

        <div class="s-panel p-5">
            <p class="s-eyebrow">Top Profile This Month</p>
            @if($summary['top_profile'])
                <p class="text-lg font-extrabold text-gray-900 dark:text-gray-100 mt-2 truncate">{{ $summary['top_profile']->profile_name }}</p>
                <p class="s-stat-foot">{{ '@'.$summary['top_profile']->username }}</p>
            @else
                <p class="text-lg font-extrabold text-gray-400 dark:text-gray-600 mt-2">—</p>
                <p class="s-stat-foot">No orders yet this month</p>
            @endif
        </div>
    </div>

    <!-- Search & Sort -->
    <div class="s-panel p-4 mb-6">
        <form action="{{ route('freelance-profiles.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px] relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="w-full pl-9 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 transition-all text-sm"
                       placeholder="Search by name or username...">
            </div>

            <select name="platform" onchange="this.form.submit()"
                    class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm w-[130px]">
                <option value="">All platforms</option>
                <option value="fiverr" @selected(request('platform') === 'fiverr')>Fiverr</option>
                <option value="upwork" @selected(request('platform') === 'upwork')>Upwork</option>
            </select>

            <select name="status" onchange="this.form.submit()"
                    class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm w-[130px]">
                <option value="">All statuses</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>

            <select name="sort" onchange="this.form.submit()"
                    class="px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
                <option value="revenue" @selected($sort === 'revenue')>Sort: Revenue</option>
                <option value="repeat_rate" @selected($sort === 'repeat_rate')>Sort: Repeat Rate</option>
                <option value="orders" @selected($sort === 'orders')>Sort: Total Orders</option>
            </select>

            <span class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                <span class="font-bold">{{ $profiles->total() }}</span> found
            </span>

            @if(request()->hasAny(['search', 'platform', 'status']))
                <a href="{{ route('freelance-profiles.index') }}"
                   class="px-3 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 font-medium text-sm transition-all">
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
    @if($profiles->isEmpty())
        <div class="s-panel p-12 text-center">
            <i class="fas fa-address-card text-4xl text-gray-300 dark:text-gray-700 mb-3"></i>
            <p class="text-gray-600 dark:text-gray-400 font-medium">No profiles match these filters.</p>
            @if(request()->hasAny(['search', 'platform', 'status']))
                <a href="{{ route('freelance-profiles.index') }}" class="text-purple-600 dark:text-purple-400 text-sm font-semibold mt-2 inline-block">Clear filters</a>
            @endif
        </div>
    @endif
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
        @foreach($profiles as $profile)
            @php($stat = $profileStats[$profile->id])
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
                            <h3 class="text-base font-bold text-gray-900 dark:text-gray-100 truncate">
                                <a href="{{ route('freelance-profiles.show', $profile) }}" class="hover:text-purple-600 dark:hover:text-purple-400">{{ $profile->profile_name }}</a>
                            </h3>
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

                    <!-- Order Analytics -->
                    <div class="grid grid-cols-2 gap-2 mb-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <div>
                            <p class="s-eyebrow">This Month</p>
                            <p class="s-num text-sm font-bold text-gray-900 dark:text-gray-100">${{ number_format($stat['month_revenue'], 0) }}</p>
                        </div>
                        <div class="text-right">
                            <p class="s-eyebrow">Repeat Rate</p>
                            <p class="s-num text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ $stat['repeat_rate'] }}%</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mb-3 text-xs text-gray-500 dark:text-gray-400">
                        <span><i class="fas fa-box mr-1"></i>{{ $stat['total_orders'] }} orders</span>
                        <span><i class="fas fa-users mr-1"></i>{{ $stat['repeat_clients'] }}/{{ $stat['unique_clients'] }} repeat clients</span>
                    </div>

                    <!-- Compact Actions -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('freelance-profiles.show', $profile) }}"
                           class="flex-1 flex items-center justify-center px-3 py-2 bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-500/20 font-medium text-xs transition-all group-hover:bg-purple-100 dark:group-hover:bg-purple-500/20">
                            <i class="fas fa-chart-line mr-1 text-xs"></i>Analytics
                        </a>
                        @can('edit profile')
                            <button onclick="editProfile('{{ $profile->getRouteKey() }}')"
                                    class="flex-1 flex items-center justify-center px-3 py-2 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-500/20 font-medium text-xs transition-all">
                                <i class="fas fa-edit mr-1 text-xs"></i>Edit
                            </button>
                            <button onclick="toggleStatus('{{ $profile->getRouteKey() }}', '{{ $profile->status }}')"
                                    class="flex items-center justify-center px-2 py-2 {{ $profile->status === 'active' ? 'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-500/20' : 'bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-500/20' }} rounded-lg font-medium text-xs transition-all">
                                <i class="fas {{ $profile->status === 'active' ? 'fa-ban' : 'fa-check' }} text-xs"></i>
                            </button>
                        @endcan
                        @can('delete profile')
                            <button onclick="deleteProfile('{{ $profile->getRouteKey() }}')"
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

<script>
const ROUTES = {
    store: @js(route('freelance-profiles.store')),
    update: @js(route('freelance-profiles.update', ['profile' => '__ID__'])),
    edit: @js(route('freelance-profiles.edit', ['profile' => '__ID__'])),
    destroy: @js(route('freelance-profiles.destroy', ['profile' => '__ID__'])),
    toggleStatus: @js(route('freelance-profiles.toggle-status', ['profile' => '__ID__'])),
};

let isEditing = false;
let existingAvatar = null;

// Modal Elements
const profileModal = document.getElementById('profileModal');
const modalBackdrop = document.getElementById('modalBackdrop');

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
        const response = await axios.get(ROUTES.edit.replace('__ID__', id));
        const profile = response.data.profile;

        isEditing = true;
        document.getElementById('modalTitle').textContent = 'Edit Profile';
        document.getElementById('submitBtnText').textContent = 'Update';

        profileIdInput.value = profile.key || profile.id;
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
            confirmButtonColor: '#D67D06'
        });
    }
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
            await axios.post(ROUTES.update.replace('__ID__', profileIdInput.value), formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        } else {
            await axios.post(ROUTES.store, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
        }

        Swal.fire({
            title: 'Success!',
            text: isEditing ? 'Profile updated successfully!' : 'Profile created successfully!',
            icon: 'success',
            confirmButtonColor: '#D67D06'
        }).then(() => {
            window.location.reload();
        });
    } catch (error) {
        Swal.fire({
            title: 'Error!',
            text: error.response?.data?.message || 'Something went wrong.',
            icon: 'error',
            confirmButtonColor: '#D67D06'
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
        confirmButtonColor: '#D67D06',
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Yes, ${action} it!`,
        cancelButtonText: 'Cancel'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.patch(ROUTES.toggleStatus.replace('__ID__', id), {
                    status: newStatus
                });

                Swal.fire({
                    title: 'Success!',
                    text: `Profile ${action}d successfully!`,
                    icon: 'success',
                    confirmButtonColor: '#D67D06'
                }).then(() => {
                    window.location.reload();
                });
            } catch (error) {
                Swal.fire({
                    title: 'Error!',
                    text: error.response?.data?.message || 'Something went wrong.',
                    icon: 'error',
                    confirmButtonColor: '#D67D06'
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
        confirmButtonColor: '#D67D06',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(ROUTES.destroy.replace('__ID__', id))
                .then(response => {
                    Swal.fire({
                        title: 'Deleted!',
                        text: response.data.message,
                        icon: 'success',
                        confirmButtonColor: '#D67D06'
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.response?.data?.message || 'Something went wrong.',
                        icon: 'error',
                        confirmButtonColor: '#D67D06'
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
