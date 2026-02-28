@extends('layouts.dashboard')

@section('content')
<div class="pt-4 sm:p-6 lg:p-8 lg:pb-3">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">{{ $bugReport->title }}</h1>
                <p class="text-gray-600 text-lg">Bug Report #{{ $bugReport->id }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('bug-reports.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
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
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Description</h2>
                    <div class="trix-content prose max-w-none text-gray-700">
                        {!! $bugReport->description !!}
                    </div>
                </div>

                @if($bugReport->images->count() > 0)
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Screenshots ({{ $bugReport->images->count() }})</h2>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($bugReport->images as $image)
                            <div class="relative group">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     alt="{{ $image->image_name }}"
                                     class="w-full h-48 object-cover rounded-lg border border-gray-300 cursor-pointer"
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
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Admin Notes</h2>
                    <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded">
                        <p class="text-gray-700">{{ nl2br(e($bugReport->admin_notes)) }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Status</h3>
                    @switch($bugReport->status)
                        @case('pending')
                            <div class="flex items-center p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <i class="fas fa-hourglass-start text-3xl text-yellow-600 mr-4"></i>
                                <div>
                                    <p class="font-semibold text-yellow-800">Pending</p>
                                    <p class="text-sm text-yellow-700">Awaiting review</p>
                                </div>
                            </div>
                            @break
                        @case('in_progress')
                            <div class="flex items-center p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                                <i class="fas fa-spinner text-3xl text-indigo-600 mr-4"></i>
                                <div>
                                    <p class="font-semibold text-indigo-800">In Progress</p>
                                    <p class="text-sm text-indigo-700">Being investigated</p>
                                </div>
                            </div>
                            @break
                        @case('resolved')
                            <div class="flex items-center p-4 bg-green-50 rounded-lg border border-green-200">
                                <i class="fas fa-check-circle text-3xl text-green-600 mr-4"></i>
                                <div>
                                    <p class="font-semibold text-green-800">Resolved</p>
                                    <p class="text-sm text-green-700">Fixed on {{ $bugReport->resolved_at ? $bugReport->resolved_at->format('M d, Y') : 'N/A' }}</p>
                                </div>
                            </div>
                            @break
                        @case('published')
                            <div class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-200">
                                <i class="fas fa-rocket text-3xl text-purple-600 mr-4"></i>
                                <div>
                                    <p class="font-semibold text-purple-800">Published</p>
                                    <p class="text-sm text-purple-700">Released on {{ $bugReport->published_at ? $bugReport->published_at->format('M d, Y') : 'N/A' }}</p>
                                </div>
                            </div>
                            @break
                    @endswitch
                </div>
            </div>

            <!-- Details Card -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Details</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">Priority</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                  style="background-color: {{ $bugReport->priority->color }}20; color: {{ $bugReport->priority->color }}">
                                {{ $bugReport->priority->name }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">Reported By</span>
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-indigo-500 flex items-center justify-center text-white font-semibold mr-2">
                                    {{ substr($bugReport->user->name, 0, 1) }}
                                </div>
                                <span class="text-sm text-gray-900">{{ $bugReport->user->name }}</span>
                            </div>
                        </div>
                        @if(auth()->user()->hasRole('admin') && $bugReport->resolvedBy)
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">Resolved By</span>
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-green-500 flex items-center justify-center text-white font-semibold mr-2">
                                    {{ substr($bugReport->resolvedBy->name, 0, 1) }}
                                </div>
                                <span class="text-sm text-gray-900">{{ $bugReport->resolvedBy->name }}</span>
                            </div>
                        </div>
                        @endif
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm font-medium text-gray-500">Created</span>
                            <span class="text-sm text-gray-900">{{ $bugReport->created_at->format('M d, Y - g:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if(auth()->user()->hasRole('admin') && $bugReport->status !== 'published')
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        @if($bugReport->status === 'pending')
                        <form method="POST" action="{{ route('bug-reports.update', $bugReport) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="in_progress">

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
            <div class="bg-white rounded-lg shadow border-2 border-red-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-red-900 mb-2">Danger Zone</h3>
                    <p class="text-sm text-red-700 mb-4">Once you delete a bug report, there is no going back.</p>
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
