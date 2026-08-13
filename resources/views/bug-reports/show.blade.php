@extends('layouts.dashboard')

@section('content')
<div class="pt-4 sm:p-6 lg:p-8 lg:pb-3">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $bugReport->title }}</h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg">Bug Report #{{ $bugReport->id }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('bug-reports.index') }}" class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Bugs
                </a>
                @can('update', $bugReport)
                    <a href="{{ route('bug-reports.edit', $bugReport) }}" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Bug Details -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Description</h2>
                    <div class="prose max-w-none text-gray-700 dark:text-gray-300">
                        {{ nl2br(e($bugReport->description)) }}
                    </div>
                </div>

                @if($bugReport->images->count() > 0)
                <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Screenshots ({{ $bugReport->images->count() }})</h2>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($bugReport->images as $image)
                            <div class="relative group">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     alt="{{ $image->image_name }}"
                                     class="w-full h-48 object-cover rounded-lg border border-gray-300 dark:border-gray-700 cursor-pointer"
                                     onclick="window.open('{{ asset('storage/' . $image->image_path) }}', '_blank')">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition rounded-lg"></div>
                                @can('update', $bugReport)
                                <form method="POST" action="{{ route('bug-reports.images.delete', [$bugReport, $image]) }}"
                                      class="absolute top-2 right-2"
                                      onsubmit="return confirm('Delete this image?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition hover:bg-red-600">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(auth()->user()->hasRole('admin') && $bugReport->admin_notes)
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Admin Notes</h2>
                    <div class="bg-indigo-50 dark:bg-indigo-500/10 border-l-4 border-indigo-500 p-4 rounded">
                        <p class="text-gray-700 dark:text-gray-300">{{ nl2br(e($bugReport->admin_notes)) }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Status</h3>
                    @switch($bugReport->status)
                        @case('pending')
                            <div class="flex items-center p-4 bg-yellow-50 dark:bg-yellow-500/10 rounded-lg border border-yellow-200 dark:border-yellow-900">
                                <i class="fas fa-hourglass-start text-3xl text-yellow-600 dark:text-yellow-400 mr-4"></i>
                                <div>
                                    <p class="font-semibold text-yellow-800 dark:text-yellow-400">Pending</p>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-500">Awaiting review</p>
                                </div>
                            </div>
                            @break
                        @case('in_progress')
                            <div class="flex items-center p-4 bg-indigo-50 dark:bg-indigo-500/10 rounded-lg border border-indigo-200 dark:border-indigo-900">
                                <i class="fas fa-spinner text-3xl text-indigo-600 dark:text-indigo-400 mr-4"></i>
                                <div>
                                    <p class="font-semibold text-indigo-800 dark:text-indigo-400">In Progress</p>
                                    <p class="text-sm text-indigo-700 dark:text-indigo-500">Being investigated</p>
                                </div>
                            </div>
                            @break
                        @case('resolved')
                            <div class="flex items-center p-4 bg-green-50 dark:bg-green-500/10 rounded-lg border border-green-200 dark:border-green-900">
                                <i class="fas fa-check-circle text-3xl text-green-600 dark:text-green-400 mr-4"></i>
                                <div>
                                    <p class="font-semibold text-green-800 dark:text-green-400">Resolved</p>
                                    <p class="text-sm text-green-700 dark:text-green-500">Fixed on {{ $bugReport->resolved_at ? $bugReport->resolved_at->format('M d, Y') : 'N/A' }}</p>
                                </div>
                            </div>
                            @break
                        @case('published')
                            <div class="flex items-center p-4 bg-purple-50 dark:bg-purple-500/10 rounded-lg border border-purple-200 dark:border-purple-900">
                                <i class="fas fa-rocket text-3xl text-purple-600 dark:text-purple-400 mr-4"></i>
                                <div>
                                    <p class="font-semibold text-purple-800 dark:text-purple-400">Published</p>
                                    <p class="text-sm text-purple-700 dark:text-purple-500">Released on {{ $bugReport->published_at ? $bugReport->published_at->format('M d, Y') : 'N/A' }}</p>
                                </div>
                            </div>
                            @break
                    @endswitch
                </div>
            </div>

            <!-- Details Card -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Details</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</span>
                            @if($bugReport->category)
                                <span class="text-sm text-gray-900 dark:text-gray-100">{{ $bugReport->category->name }}</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                    Pending triage
                                </span>
                            @endif
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Priority</span>
                            @if($bugReport->priority)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      style="background-color: {{ $bugReport->priority->color }}20; color: {{ $bugReport->priority->color }}">
                                    {{ $bugReport->priority->name }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                    Pending triage
                                </span>
                            @endif
                        </div>
                        @if($bugReport->ai_triaged_at)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">AI Triage</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bugReport->ai_triage_confidence < 60 ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' : 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300' }}"
                                  title="{{ $bugReport->ai_triage_reasoning }}">
                                {{ $bugReport->ai_triage_confidence < 60 ? 'Needs review' : 'AI categorized' }} ({{ $bugReport->ai_triage_confidence }}% confidence)
                            </span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Reported By</span>
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white font-semibold mr-2">
                                    {{ substr($bugReport->user->name, 0, 1) }}
                                </div>
                                <span class="text-sm text-gray-900 dark:text-gray-100">{{ $bugReport->user->name }}</span>
                            </div>
                        </div>
                        @if(auth()->user()->hasRole('admin') && $bugReport->resolvedBy)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Resolved By</span>
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold mr-2">
                                    {{ substr($bugReport->resolvedBy->name, 0, 1) }}
                                </div>
                                <span class="text-sm text-gray-900 dark:text-gray-100">{{ $bugReport->resolvedBy->name }}</span>
                            </div>
                        </div>
                        @endif
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</span>
                            <span class="text-sm text-gray-900 dark:text-gray-100">{{ $bugReport->created_at->format('M d, Y - g:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if(auth()->user()->hasRole('admin') && $bugReport->status !== 'published' && ! $bugReport->bug_category_id)
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
                <div class="p-6 text-sm text-gray-500 dark:text-gray-400">
                    <i class="fas fa-robot mr-2"></i>Waiting on AI triage before quick actions become available.
                </div>
            </div>
            @endif

            @if(auth()->user()->hasRole('admin') && $bugReport->status !== 'published' && $bugReport->bug_category_id)
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        @if($bugReport->status === 'pending')
                        <form method="POST" action="{{ route('bug-reports.update', $bugReport) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="in_progress">
                            <input type="hidden" name="bug_category_id" value="{{ $bugReport->bug_category_id }}">
                            <input type="hidden" name="bug_priority_id" value="{{ $bugReport->bug_priority_id }}">
                            <input type="hidden" name="title" value="{{ $bugReport->title }}">
                            <input type="hidden" name="description" value="{{ $bugReport->description }}">
                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                <i class="fas fa-play mr-2"></i>Start Working
                            </button>
                        </form>
                        @endif

                        @if($bugReport->status === 'in_progress')
                        <form method="POST" action="{{ route('bug-reports.update', $bugReport) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="resolved">
                            <input type="hidden" name="bug_category_id" value="{{ $bugReport->bug_category_id }}">
                            <input type="hidden" name="bug_priority_id" value="{{ $bugReport->bug_priority_id }}">
                            <input type="hidden" name="title" value="{{ $bugReport->title }}">
                            <input type="hidden" name="description" value="{{ $bugReport->description }}">
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-check mr-2"></i>Mark as Resolved
                            </button>
                        </form>
                        @endif

                        @if($bugReport->status === 'resolved')
                        <form method="POST" action="{{ route('bug-reports.update', $bugReport) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="published">
                            <input type="hidden" name="bug_category_id" value="{{ $bugReport->bug_category_id }}">
                            <input type="hidden" name="bug_priority_id" value="{{ $bugReport->bug_priority_id }}">
                            <input type="hidden" name="title" value="{{ $bugReport->title }}">
                            <input type="hidden" name="description" value="{{ $bugReport->description }}">
                            <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                <i class="fas fa-rocket mr-2"></i>Publish
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @can('delete', $bugReport)
            <!-- Danger Zone -->
            <div class="bg-white dark:bg-gray-900 rounded-lg shadow border-2 border-red-200 dark:border-red-900">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-red-900 dark:text-red-400 mb-2">Danger Zone</h3>
                    <p class="text-sm text-red-700 dark:text-red-400/80 mb-4">Once you delete a bug report, there is no going back.</p>
                    <form method="POST" action="{{ route('bug-reports.destroy', $bugReport) }}" onsubmit="return confirm('Are you sure you want to delete this bug report? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash mr-2"></i>Delete Bug Report
                        </button>
                    </form>
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>
@endsection
