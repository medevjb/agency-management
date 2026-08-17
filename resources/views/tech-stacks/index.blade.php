@extends('layouts.dashboard')

@section('content')
<div class="pt-4 sm:p-6 lg:p-8 lg:pb-3">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="mb-4">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-2">Tech Stacks</h1>
            <p class="text-gray-600 dark:text-gray-400 text-lg">Manage and track all your technology stacks</p>
        </div>
    </div>

    <!-- Filters Section -->
    <form method="GET" action="{{ route('tech-stacks.index') }}" class="mb-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-search mr-1 text-indigo-500 dark:text-indigo-400"></i>Search
                    </label>
                    <input type="text"
                           id="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search tech stacks..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
                </div>

                <!-- Leader Filter -->
                <div>
                    <label for="leader" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        <i class="fas fa-user-tie mr-1 text-indigo-500 dark:text-indigo-400"></i>Stack Leader
                    </label>
                    <select id="leader" name="leader"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm">
                        <option value="">All Leaders</option>
                        @foreach($leaders ?? [] as $leader)
                            <option value="{{ $leader->id }}" {{ request('leader') == $leader->id ? 'selected' : '' }}>
                                {{ $leader->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end space-x-2">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-all duration-200 text-sm">
                        <i class="fas fa-filter mr-1"></i>
                        Filter
                    </button>
                    <a href="{{ route('tech-stacks.index') }}"
                       class="inline-flex items-center justify-center px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-all duration-200 text-sm">
                        <i class="fas fa-times mr-1"></i>
                        Clear
                    </a>
                </div>
            </div>
        </div>

        <!-- Create Button -->
        @can('create', \App\Models\TechStack::class)
            <div class="flex justify-end mb-4">
                <a href="{{ route('tech-stacks.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-medium rounded-lg shadow hover:shadow-lg transform transition-all duration-200 hover:scale-105">
                    <i class="fas fa-plus mr-2"></i>
                    New Tech Stack
                </a>
            </div>
        @endcan
    </form>

    <!-- Tech Stacks Table -->
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Tech Stack
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Stack Leader
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Members
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Projects
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse($techStacks as $techStack)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-md">
                                            <i class="fas fa-layer-group text-white text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $techStack->name }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-xs">
                                            {{ $techStack->description ?? 'No description' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($techStack->stackLeader)
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center shadow-md">
                                                <i class="fas fa-user-tie text-white text-xs"></i>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $techStack->stackLeader->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $techStack->stackLeader->employee_id ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-center text-gray-400 dark:text-gray-500">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            <div class="h-8 w-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                <i class="fas fa-user-slash text-gray-500 dark:text-gray-400 text-xs"></i>
                                            </div>
                                        </div>
                                        <div class="ml-2 text-sm">Not Assigned</div>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400 shadow-sm">
                                    <i class="fas fa-users mr-1"></i>
                                    {{ $techStack->users->count() ?? 0 }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 dark:bg-orange-500/10 text-orange-800 dark:text-orange-400 shadow-sm">
                                    <i class="fas fa-folder-open mr-1"></i>
                                    {{ $techStack->orders->count() }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('tech-stacks.show', $techStack) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-500/10 hover:bg-indigo-200 dark:hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-all duration-200 shadow-sm"
                                       title="View Tech Stack">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    @can('update', $techStack)
                                        <a href="{{ route('tech-stacks.edit', $techStack) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-500/10 hover:bg-blue-200 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-all duration-200 shadow-sm"
                                           title="Edit Tech Stack">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                    @endcan
                                    @can('delete', $techStack)
                                        <button onclick="deleteTechStack('{{ $techStack->getRouteKey() }}')"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 dark:bg-red-500/10 hover:bg-red-200 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-all duration-200 shadow-sm"
                                                title="Delete Tech Stack">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                                        <i class="fas fa-layer-group text-gray-400 dark:text-gray-500 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-1">No Tech Stacks Found</h3>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Get started by creating your first tech stack.</p>
                                    @can('create', \App\Models\TechStack::class)
                                        <a href="{{ route('tech-stacks.create') }}"
                                           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors">
                                            <i class="fas fa-plus mr-2"></i>
                                            Create Tech Stack
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($techStacks->hasPages())
            <div class="bg-white dark:bg-gray-900 px-6 py-4 border-t border-gray-200 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing
                        <span class="font-medium">{{ $techStacks->firstItem() }}</span>
                        to
                        <span class="font-medium">{{ $techStacks->lastItem() }}</span>
                        of
                        <span class="font-medium">{{ $techStacks->total() }}</span>
                        results
                    </div>

                    <div class="flex items-center space-x-2">
                        @if($techStacks->onFirstPage())
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 dark:text-gray-600 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg cursor-not-allowed">
                                <i class="fas fa-chevron-left mr-1"></i>
                                Previous
                            </span>
                        @else
                            <a href="{{ $techStacks->previousPageUrl() }}"
                               class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-chevron-left mr-1"></i>
                                Previous
                            </a>
                        @endif

                        @foreach($techStacks->getUrlRange(1, $techStacks->lastPage()) as $page => $url)
                            @if($page == $techStacks->currentPage())
                                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 border border-indigo-600 rounded-lg">
                                    {{ $page }}
                                </span>
                            @elseif($page == 1 || $page == $techStacks->lastPage() || abs($page - $techStacks->currentPage()) <= 1)
                                <a href="{{ $url }}"
                                   class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    {{ $page }}
                                </a>
                            @elseif(abs($page - $techStacks->currentPage()) == 2)
                                <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                                    ...
                                </span>
                            @endif
                        @endforeach

                        @if($techStacks->hasMorePages())
                            <a href="{{ $techStacks->nextPageUrl() }}"
                               class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                Next
                                <i class="fas fa-chevron-right ml-1"></i>
                            </a>
                        @else
                            <span class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 dark:text-gray-600 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg cursor-not-allowed">
                                Next
                                <i class="fas fa-chevron-right ml-1"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        const ROUTES = {
            destroy: @js(route('tech-stacks.destroy', ['id' => '__ID__'])),
        };

        function deleteTechStack(techStackId) {
            Swal.fire({
                title: 'Delete Tech Stack?',
                text: 'Are you sure you want to delete this tech stack? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(ROUTES.destroy.replace('__ID__', techStackId), {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (response.data.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.data.message || 'Tech stack has been deleted successfully.',
                                icon: 'success',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = '{{ route('tech-stacks.index') }}';
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.data.message || 'Failed to delete tech stack',
                                icon: 'error',
                                confirmButtonColor: '#3B82F6'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Delete error:', error);
                        const errorMessage = error.response?.data?.message ||
                                             error.response?.data?.error ||
                                             error.message ||
                                             'An error occurred while deleting the tech stack';
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonColor: '#3B82F6'
                        });
                    });
                }
            });
        }
    </script>
@endpush
@endsection
