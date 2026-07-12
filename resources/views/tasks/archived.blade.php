@extends('layouts.dashboard')

@section('title', 'Archived Tasks')

@push('styles')
    <style>
        .archive-hero {
            background: linear-gradient(135deg, #fef3c7 0%, #e0f2fe 50%, #ecfccb 100%);
            border: 1px solid #e2e8f0;
        }

        .archive-card {
            border: 1px solid #e2e8f0;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .archive-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.12);
            border-color: #cbd5f5;
        }

        .archive-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border: 1px solid transparent;
        }

        .archive-action {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .archive-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.08);
        }

        .archive-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .archive-meta {
            color: #64748b;
        }
    </style>
@endpush

@section('content')
@php
    $archivedCount = $tasks->count();
    $recentArchived = $tasks->filter(function ($task) {
        return $task->archived_at && $task->archived_at->gt(now()->subDays(7));
    })->count();
    $avgDaysToArchive = (int) round($tasks->filter(function ($task) {
        return $task->archived_at && $task->created_at;
    })->map(function ($task) {
        return $task->archived_at->diffInDays($task->created_at);
    })->avg() ?? 0);
    $lastArchived = $tasks->first();
@endphp

<div class="p-6 space-y-6">
    <div class="archive-hero rounded-3xl p-6 md:p-8">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-500 uppercase tracking-[0.2em]">Archive</p>
                <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-2">Archived Tasks</h1>
                <p class="text-slate-600 mt-2 max-w-2xl">
                    Keep completed and paused work tidy. Restore tasks when they need attention again, or permanently remove items you no longer need.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('tasks.index') }}" class="archive-action px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Tasks
                </a>
                <div class="archive-action px-4 py-2 rounded-xl bg-slate-900 text-white font-semibold">
                    <i class="fas fa-archive mr-2"></i><span id="archiveCount">{{ $archivedCount }}</span> archived
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500 font-semibold">Archived in last 7 days</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $recentArchived }}</p>
            <p class="text-xs text-slate-400 mt-3">Keep tabs on recent cleanups.</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500 font-semibold">Average time to archive</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $avgDaysToArchive }} days</p>
            <p class="text-xs text-slate-400 mt-3">From creation to archive.</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <p class="text-sm text-slate-500 font-semibold">Last archived</p>
            <p class="text-lg font-bold text-slate-900 mt-2">
                {{ $lastArchived && $lastArchived->archived_at ? $lastArchived->archived_at->format('M d, Y') : 'No recent activity' }}
            </p>
            <p class="text-xs text-slate-400 mt-3">Latest archived task date.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-4 md:p-5 space-y-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="relative flex-1 min-w-[240px]">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input id="archiveSearch" type="text" placeholder="Search archived tasks by title or description"
                       class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl focus:border-slate-400 focus:outline-none">
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <select id="archivePriority" class="px-4 py-3 border border-slate-200 rounded-xl font-semibold text-slate-600">
                    <option value="">All priorities</option>
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
                <select id="archiveProject" class="px-4 py-3 border border-slate-200 rounded-xl font-semibold text-slate-600">
                    <option value="">All projects</option>
                    @foreach($orders as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
                <select id="archiveRange" class="px-4 py-3 border border-slate-200 rounded-xl font-semibold text-slate-600">
                    <option value="">All time</option>
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
                <select id="archiveSort" class="px-4 py-3 border border-slate-200 rounded-xl font-semibold text-slate-600">
                    <option value="newest">Sort: Newest first</option>
                    <option value="oldest">Sort: Oldest first</option>
                    <option value="title">Sort: Title A-Z</option>
                </select>
            </div>
        </div>
    </div>

    @if($tasks->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                <i class="fas fa-archive text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mt-4">No archived tasks yet</h3>
            <p class="text-slate-500 mt-2">When you archive a task, it will show up here for quick review or restoration.</p>
            <a href="{{ route('tasks.index') }}" class="inline-flex items-center mt-5 px-5 py-2.5 rounded-xl bg-slate-900 text-white font-semibold">
                Browse active tasks
            </a>
        </div>
    @else
        <div id="archiveGrid" class="archive-grid">
            @foreach($tasks as $task)
                @php
                    $priorityClasses = match($task->priority) {
                        'urgent' => 'bg-red-50 text-red-700 border-red-200',
                        'high' => 'bg-orange-50 text-orange-700 border-orange-200',
                        'medium' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
                        default => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    };
                    $archiveDescription = trim(strip_tags($task->description ?? '')) ?: null;
                    $archiveProject = $task->order?->name;
                    $archiveAssignee = $task->assignedTo?->name;
                    $archiveArchivedAt = $task->archived_at?->format('M d, Y');
                    $archiveDueDate = $task->due_date?->format('M d, Y');
                    $archiveCompletedAt = $task->completed_at?->format('M d, Y');
                @endphp
                <div class="archive-card bg-white rounded-2xl p-5 flex flex-col gap-4"
                     data-archive-card
                     data-archive-card-id="{{ $task->id }}"
                     data-archive-title="{{ e($task->title) }}"
                     data-archive-description="{{ e($archiveDescription ?? '') }}"
                     data-archive-project="{{ e($archiveProject ?? '') }}"
                     data-archive-assignee="{{ e($archiveAssignee ?? '') }}"
                     data-archive-priority="{{ e($task->priority) }}"
                     data-archive-priority-class="{{ e($priorityClasses) }}"
                     data-archive-archived-at="{{ e($archiveArchivedAt ?? '') }}"
                     data-archive-due-date="{{ e($archiveDueDate ?? '') }}"
                     data-archive-completed-at="{{ e($archiveCompletedAt ?? '') }}"
                     data-title="{{ strtolower($task->title) }}"
                     data-description="{{ strtolower(strip_tags($task->description ?? '')) }}"
                     data-priority="{{ $task->priority }}"
                     data-project="{{ $task->order_id }}"
                     data-archived-ts="{{ optional($task->archived_at)->timestamp ?? 0 }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 leading-tight">{{ $task->title }}</h3>
                            <p class="text-xs archive-meta mt-1">
                                Archived {{ $task->archived_at ? $task->archived_at->format('M d, Y') : 'date unavailable' }}
                            </p>
                        </div>
                        <span class="archive-pill {{ $priorityClasses }}">{{ $task->priority }}</span>
                    </div>

                    <p class="text-sm text-slate-600 leading-relaxed">
                        {{ $task->description ? \Illuminate\Support\Str::limit(strip_tags($task->description), 140) : 'No description provided.' }}
                    </p>

                    <div class="grid grid-cols-2 gap-3 text-xs text-slate-600">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-folder text-slate-400"></i>
                            <span>{{ $task->order?->name ?? 'No project' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user text-slate-400"></i>
                            <span>{{ $task->assignedTo?->name ?? 'Myself' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar text-slate-400"></i>
                            <span>
                                {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-flag-checkered text-slate-400"></i>
                            <span>
                                {{ $task->completed_at ? $task->completed_at->format('M d, Y') : 'Not completed' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button onclick="openArchiveView({{ $task->id }})"
                                class="archive-action px-4 py-2 rounded-xl border border-slate-200 text-slate-700 font-semibold">
                            View
                        </button>
                        <button onclick="openRestoreModal({{ $task->id }})"
                                class="archive-action px-4 py-2 rounded-xl bg-emerald-600 text-white font-semibold">
                            Restore
                        </button>
                        <button onclick="deleteTask({{ $task->id }})"
                                class="archive-action px-4 py-2 rounded-xl border border-red-200 text-red-600 font-semibold">
                            Delete
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        <div id="archiveNoMatch" class="hidden bg-white rounded-2xl border border-dashed border-slate-300 p-8 text-center">
            <p class="text-slate-600 font-semibold">No archived tasks match these filters.</p>
            <p class="text-sm text-slate-400 mt-2">Try adjusting your search or filters.</p>
        </div>
    @endif
</div>

<div id="archiveViewModal" class="fixed inset-0 z-50 hidden" aria-labelledby="archive-modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60" onclick="closeArchiveView()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-2xl rounded-2xl bg-white shadow-xl">
                <div class="border-b border-slate-200 p-6 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-[0.2em]">Archived Task</p>
                        <h3 id="archiveModalTitle" class="text-2xl font-bold text-slate-900 mt-2">Task title</h3>
                        <p id="archiveModalArchivedAt" class="text-sm text-slate-500 mt-1">Archived date</p>
                    </div>
                    <button onclick="closeArchiveView()" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <p class="text-sm font-semibold text-slate-700 mb-2">Description</p>
                        <div id="archiveModalDescription" class="text-sm text-slate-600 bg-slate-50 rounded-xl p-4">
                            No description provided.
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-slate-600">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-folder text-slate-400"></i>
                            <span id="archiveModalProject">No project</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user text-slate-400"></i>
                            <span id="archiveModalAssignee">Myself</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar text-slate-400"></i>
                            <span id="archiveModalDueDate">No due date</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-flag-checkered text-slate-400"></i>
                            <span id="archiveModalCompletedAt">Not completed</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <span id="archiveModalPriority" class="archive-pill bg-slate-100 text-slate-600 border-slate-200">priority</span>
                        <span class="archive-pill bg-slate-900 text-white border-slate-900">Archived</span>
                    </div>
                </div>
                <div class="border-t border-slate-200 px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Restore as</label>
                        <select id="archiveRestoreStatus" class="px-3 py-2 border border-slate-200 rounded-lg text-sm font-semibold text-slate-600">
                            <option value="pending">Pending</option>
                            <option value="in-progress">In Progress</option>
                            <option value="on-hold">On Hold</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="confirmRestoreFromModal()"
                                class="archive-action px-4 py-2 rounded-xl bg-emerald-600 text-white font-semibold">
                            Restore
                        </button>
                        <button onclick="confirmDeleteFromModal()"
                                class="archive-action px-4 py-2 rounded-xl border border-red-200 text-red-600 font-semibold">
                            Delete
                        </button>
                        <button onclick="closeArchiveView()"
                                class="archive-action px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const archiveSearch = document.getElementById('archiveSearch');
    const archivePriority = document.getElementById('archivePriority');
    const archiveProject = document.getElementById('archiveProject');
    const archiveRange = document.getElementById('archiveRange');
    const archiveSort = document.getElementById('archiveSort');
    const archiveGrid = document.getElementById('archiveGrid');
    const archiveCount = document.getElementById('archiveCount');
    const archiveNoMatch = document.getElementById('archiveNoMatch');

    const archiveCards = archiveGrid ? Array.from(archiveGrid.querySelectorAll('[data-archive-card]')) : [];

    function sortArchiveCards(mode) {
        const sorted = archiveCards.slice().sort((a, b) => {
            if (mode === 'title') {
                return a.dataset.title.localeCompare(b.dataset.title);
            }
            const aTime = Number(a.dataset.archivedTs || 0);
            const bTime = Number(b.dataset.archivedTs || 0);
            return mode === 'oldest' ? aTime - bTime : bTime - aTime;
        });

        sorted.forEach(card => archiveGrid.appendChild(card));
    }

    function applyArchiveFilters() {
        if (!archiveGrid) {
            return;
        }

        const query = archiveSearch.value.trim().toLowerCase();
        const priority = archivePriority.value;
        const project = archiveProject.value;
        const range = Number(archiveRange.value || 0);
        const now = Date.now();

        let visibleCount = 0;

        sortArchiveCards(archiveSort.value);

        archiveCards.forEach(card => {
            const matchesQuery =
                card.dataset.title.includes(query) || card.dataset.description.includes(query);
            const matchesPriority = !priority || card.dataset.priority === priority;
            const matchesProject = !project || card.dataset.project === project;

            let matchesRange = true;
            if (range) {
                const archivedAt = Number(card.dataset.archivedTs || 0) * 1000;
                matchesRange = archivedAt ? (now - archivedAt) / (1000 * 60 * 60 * 24) <= range : false;
            }

            const isVisible = matchesQuery && matchesPriority && matchesProject && matchesRange;
            card.classList.toggle('hidden', !isVisible);
            if (isVisible) {
                visibleCount += 1;
            }
        });

        if (archiveCount) {
            archiveCount.textContent = visibleCount;
        }

        if (archiveNoMatch) {
            archiveNoMatch.classList.toggle('hidden', visibleCount !== 0);
        }
    }

    if (archiveSearch) {
        ['input', 'change'].forEach(eventName => {
            archiveSearch.addEventListener(eventName, applyArchiveFilters);
        });
    }

    [archivePriority, archiveProject, archiveRange, archiveSort].forEach(element => {
        if (!element) {
            return;
        }
        element.addEventListener('change', applyArchiveFilters);
    });

    let currentArchiveTask = null;

    function getArchiveTask(taskId) {
        const card = archiveCards.find(item => Number(item.dataset.archiveCardId) === taskId);
        if (!card) {
            return null;
        }
        return {
            id: taskId,
            title: card.dataset.archiveTitle || '',
            description: card.dataset.archiveDescription || '',
            project: card.dataset.archiveProject || '',
            assignee: card.dataset.archiveAssignee || '',
            priority: card.dataset.archivePriority || 'medium',
            priority_class: card.dataset.archivePriorityClass || 'bg-slate-100 text-slate-600 border-slate-200',
            archived_at: card.dataset.archiveArchivedAt || '',
            due_date: card.dataset.archiveDueDate || '',
            completed_at: card.dataset.archiveCompletedAt || '',
        };
    }

    function fillArchiveModal(taskData) {
        currentArchiveTask = taskData;
        document.getElementById('archiveModalTitle').textContent = taskData.title;
        document.getElementById('archiveModalArchivedAt').textContent = `Archived ${taskData.archived_at || 'date unavailable'}`;
        document.getElementById('archiveModalDescription').textContent = taskData.description || 'No description provided.';
        document.getElementById('archiveModalProject').textContent = taskData.project || 'No project';
        document.getElementById('archiveModalAssignee').textContent = taskData.assignee || 'Myself';
        document.getElementById('archiveModalDueDate').textContent = taskData.due_date || 'No due date';
        document.getElementById('archiveModalCompletedAt').textContent = taskData.completed_at || 'Not completed';
        document.getElementById('archiveRestoreStatus').value = 'pending';

        const priorityBadge = document.getElementById('archiveModalPriority');
        priorityBadge.textContent = taskData.priority;
        priorityBadge.className = `archive-pill ${taskData.priority_class}`;
    }

    function openArchiveView(taskId) {
        const taskData = getArchiveTask(taskId);

        if (!taskData) {
            return;
        }

        fillArchiveModal(taskData);

        document.getElementById('archiveViewModal').classList.remove('hidden');
    }

    function closeArchiveView() {
        document.getElementById('archiveViewModal').classList.add('hidden');
        currentArchiveTask = null;
    }

    function openRestoreModal(taskId) {
        const taskData = getArchiveTask(taskId);
        if (!taskData) {
            return;
        }
        fillArchiveModal(taskData);
        document.getElementById('archiveViewModal').classList.remove('hidden');
    }

    function confirmRestoreFromModal() {
        if (!currentArchiveTask) {
            return;
        }
        restoreTask(currentArchiveTask.id, document.getElementById('archiveRestoreStatus').value);
    }

    function confirmDeleteFromModal() {
        if (!currentArchiveTask) {
            return;
        }
        deleteTask(currentArchiveTask.id);
    }

    function restoreTask(taskId, status) {
        const taskData = getArchiveTask(taskId);
        if (!taskData) {
            return;
        }
        Swal.fire({
            title: 'Restore task?',
            text: `Restore "${taskData.title}" back to active tasks.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, restore it',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (!result.isConfirmed) {
                return;
            }
            axios.post(`/tasks/${taskId}/unarchive`)
                .then(() => {
                    if (status && status !== 'pending') {
                        return axios.patch(`/tasks/${taskId}/status`, { status: status });
                    }
                })
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Restored!',
                        text: 'Task has been moved back to active tasks.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to restore task.'
                    });
                });
        });
    }

    function deleteTask(taskId) {
        const taskData = getArchiveTask(taskId);
        if (!taskData) {
            return;
        }
        Swal.fire({
            title: 'Delete permanently?',
            text: `This will permanently remove "${taskData.title}".`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (!result.isConfirmed) {
                return;
            }
            axios.delete(`/tasks/${taskId}`)
                .then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Task has been removed.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to delete task.'
                    });
                });
        });
    }

    if (archiveGrid) {
        applyArchiveFilters();
    }
</script>
@endpush
