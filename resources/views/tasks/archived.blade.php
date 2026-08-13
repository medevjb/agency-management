@extends('layouts.dashboard')

@section('title', 'Archived Tasks')

@push('styles')
    <style>
        .archive-card {
            border-left: 4px solid transparent;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .archive-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.10);
        }

        .archive-card[data-priority="urgent"] { border-left-color: #ef4444; }
        .archive-card[data-priority="high"]   { border-left-color: #f97316; }
        .archive-card[data-priority="medium"] { border-left-color: #eab308; }
        .archive-card[data-priority="low"]    { border-left-color: #22c55e; }

        .archive-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 9px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid transparent;
        }

        .archive-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.25rem;
        }

        .filter-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 30px;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .action-btn:hover {
            transform: translateY(-1px);
        }

        .stat-icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
    </style>
@endpush

@section('content')
@php
    $archivedCount = $tasks->count();
    $recentArchived = $tasks->filter(fn($t) => $t->archived_at?->gt(now()->subDays(7)))->count();
    $avgDaysToArchive = (int) round($tasks->filter(fn($t) => $t->archived_at && $t->created_at)
        ->map(fn($t) => $t->archived_at->diffInDays($t->created_at))
        ->avg() ?? 0);
    $lastArchived = $tasks->first();
@endphp

<div class="p-6 space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-4">
            <div class="stat-icon-wrap bg-slate-100 dark:bg-slate-800">
                <i class="fas fa-archive text-slate-500 dark:text-slate-400 text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Archive</p>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white leading-tight">Archived Tasks</h1>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('tasks.index') }}"
               class="action-btn border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                <i class="fas fa-arrow-left text-xs"></i> Back to Tasks
            </a>
            <div class="action-btn bg-slate-900 dark:bg-slate-700 text-white cursor-default">
                <i class="fas fa-box-archive text-xs"></i>
                <span id="archiveCount">{{ $archivedCount }}</span> archived
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 flex items-center gap-4">
            <div class="stat-icon-wrap bg-violet-50 dark:bg-violet-900/30">
                <i class="fas fa-clock-rotate-left text-violet-500 text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Last 7 days</p>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $recentArchived }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">recently archived</p>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 flex items-center gap-4">
            <div class="stat-icon-wrap bg-blue-50 dark:bg-blue-900/30">
                <i class="fas fa-gauge-high text-blue-500 text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Avg. time to archive</p>
                <p class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $avgDaysToArchive }}<span class="text-sm font-semibold text-slate-400 ml-1">days</span></p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">from creation</p>
            </div>
        </div>
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 flex items-center gap-4">
            <div class="stat-icon-wrap bg-emerald-50 dark:bg-emerald-900/30">
                <i class="fas fa-calendar-check text-emerald-500 text-lg"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">Last archived</p>
                <p class="text-base font-bold text-slate-900 dark:text-white mt-0.5">
                    {{ $lastArchived?->archived_at?->format('M d, Y') ?? '—' }}
                </p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">most recent entry</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input id="archiveSearch" type="text" placeholder="Search by title or description…"
                       class="w-full pl-10 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-300 focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100 dark:focus:ring-indigo-900 placeholder:text-slate-400">
            </div>
            <div class="flex flex-wrap gap-2">
                <select id="archivePriority"
                        class="filter-select text-sm px-3 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 font-semibold focus:outline-none focus:border-indigo-400">
                    <option value="">All priorities</option>
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
                <select id="archiveProject"
                        class="filter-select text-sm px-3 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 font-semibold focus:outline-none focus:border-indigo-400">
                    <option value="">All projects</option>
                    @foreach($orders as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
                <select id="archiveRange"
                        class="filter-select text-sm px-3 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 font-semibold focus:outline-none focus:border-indigo-400">
                    <option value="">All time</option>
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
                <select id="archiveSort"
                        class="filter-select text-sm px-3 py-2.5 border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 font-semibold focus:outline-none focus:border-indigo-400">
                    <option value="newest">Newest first</option>
                    <option value="oldest">Oldest first</option>
                    <option value="title">Title A–Z</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Empty state --}}
    @if($tasks->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-300 dark:border-slate-600 p-12 text-center">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-400">
                <i class="fas fa-archive text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mt-4">No archived tasks yet</h3>
            <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm max-w-sm mx-auto">When you archive a task it will appear here for review or restoration.</p>
            <a href="{{ route('tasks.index') }}"
               class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-slate-700 text-white font-semibold text-sm">
                <i class="fas fa-list-check text-xs"></i> Browse active tasks
            </a>
        </div>
    @else
        <div id="archiveGrid" class="archive-grid">
            @foreach($tasks as $task)
                @php
                    $priorityClasses = match($task->priority) {
                        'urgent' => 'bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 border-red-200 dark:border-red-900',
                        'high'   => 'bg-orange-50 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400 border-orange-200 dark:border-orange-900',
                        'medium' => 'bg-yellow-50 dark:bg-yellow-500/10 text-yellow-800 dark:text-yellow-400 border-yellow-200 dark:border-yellow-900',
                        default  => 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-900',
                    };
                    $archiveDescription = trim(strip_tags($task->description ?? '')) ?: null;
                    $archiveProject     = $task->order?->name;
                    $archiveAssignee    = $task->assignedTo?->name;
                    $archiveArchivedAt  = $task->archived_at?->format('M d, Y');
                    $archiveDueDate     = $task->due_date?->format('M d, Y');
                    $archiveCompletedAt = $task->completed_at?->format('M d, Y');
                @endphp

                <div class="archive-card bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 flex flex-col gap-3"
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

                    {{-- Title + priority --}}
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white leading-snug line-clamp-2">{{ $task->title }}</h3>
                        <span class="archive-pill {{ $priorityClasses }} shrink-0">{{ $task->priority }}</span>
                    </div>

                    {{-- Description --}}
                    @if($archiveDescription)
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-2">
                            {{ \Illuminate\Support\Str::limit($archiveDescription, 120) }}
                        </p>
                    @endif

                    {{-- Meta grid --}}
                    <div class="grid grid-cols-2 gap-x-3 gap-y-1.5 text-xs text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-1.5 min-w-0">
                            <i class="fas fa-folder w-3 text-slate-400 shrink-0"></i>
                            <span class="truncate">{{ $archiveProject ?? 'No project' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 min-w-0">
                            <i class="fas fa-user w-3 text-slate-400 shrink-0"></i>
                            <span class="truncate">{{ $archiveAssignee ?? 'Myself' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 min-w-0">
                            <i class="fas fa-calendar-day w-3 text-slate-400 shrink-0"></i>
                            <span class="truncate">{{ $archiveDueDate ?? 'No due date' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 min-w-0">
                            <i class="fas fa-check-circle w-3 text-slate-400 shrink-0"></i>
                            <span class="truncate">{{ $archiveCompletedAt ?? 'Not completed' }}</span>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-between pt-1 border-t border-slate-100 dark:border-slate-700 mt-auto">
                        <span class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                            <i class="fas fa-box-archive text-[10px]"></i>
                            {{ $archiveArchivedAt ?? 'Date unavailable' }}
                        </span>
                        <div class="flex items-center gap-1.5">
                            <button onclick="openArchiveView({{ $task->id }})" title="View"
                                    class="action-btn border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs px-3 py-1.5">
                                <i class="fas fa-eye text-[11px]"></i> View
                            </button>
                            <button onclick="openRestoreModal({{ $task->id }})" title="Restore"
                                    class="action-btn bg-emerald-500 hover:bg-emerald-600 text-white text-xs px-3 py-1.5">
                                <i class="fas fa-rotate-left text-[11px]"></i> Restore
                            </button>
                            <button onclick="deleteTask({{ $task->id }})" title="Delete"
                                    class="action-btn border border-red-200 dark:border-red-800 text-red-500 dark:text-red-400 bg-white dark:bg-slate-700 text-xs px-3 py-1.5">
                                <i class="fas fa-trash text-[11px]"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="archiveNoMatch" class="hidden bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-300 dark:border-slate-600 p-10 text-center">
            <i class="fas fa-filter text-slate-300 dark:text-slate-600 text-3xl"></i>
            <p class="text-slate-600 dark:text-slate-400 font-semibold mt-3">No tasks match your filters</p>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">Try adjusting your search or filter criteria.</p>
        </div>
    @endif
</div>

{{-- View / Restore Modal --}}
<div id="archiveViewModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="archive-modal-title">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeArchiveView()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-slate-800 shadow-2xl border border-slate-200 dark:border-slate-700">

                {{-- Modal header --}}
                <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex items-start gap-3">
                    <div class="stat-icon-wrap bg-slate-100 dark:bg-slate-700 shrink-0">
                        <i class="fas fa-archive text-slate-500 dark:text-slate-400"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Archived Task</p>
                        <h3 id="archiveModalTitle" class="text-lg font-bold text-slate-900 dark:text-white mt-0.5 leading-snug">Task title</h3>
                        <p id="archiveModalArchivedAt" class="text-xs text-slate-400 dark:text-slate-500 mt-0.5"></p>
                    </div>
                    <button onclick="closeArchiveView()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 mt-0.5 shrink-0">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Modal body --}}
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Description</p>
                        <div id="archiveModalDescription"
                             class="text-sm text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-900 rounded-xl p-3.5 min-h-[56px] leading-relaxed">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm text-slate-600 dark:text-slate-400">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-folder text-slate-400 w-4"></i>
                            <span id="archiveModalProject" class="truncate">No project</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-user text-slate-400 w-4"></i>
                            <span id="archiveModalAssignee" class="truncate">Myself</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-day text-slate-400 w-4"></i>
                            <span id="archiveModalDueDate">No due date</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-slate-400 w-4"></i>
                            <span id="archiveModalCompletedAt">Not completed</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <span id="archiveModalPriority" class="archive-pill"></span>
                        <span class="archive-pill bg-slate-900 dark:bg-slate-700 text-white border-transparent">
                            <i class="fas fa-archive text-[9px]"></i> Archived
                        </span>
                    </div>
                </div>

                {{-- Modal footer --}}
                <div class="p-5 border-t border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <label class="text-xs font-semibold text-slate-500 dark:text-slate-400 whitespace-nowrap">Restore as</label>
                        <select id="archiveRestoreStatus"
                                class="filter-select text-sm px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 font-semibold focus:outline-none">
                            <option value="pending">Pending</option>
                            <option value="in-progress">In Progress</option>
                            <option value="on-hold">On Hold</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="confirmRestoreFromModal()"
                                class="action-btn bg-emerald-500 hover:bg-emerald-600 text-white text-sm">
                            <i class="fas fa-rotate-left text-xs"></i> Restore
                        </button>
                        <button onclick="confirmDeleteFromModal()"
                                class="action-btn border border-red-200 dark:border-red-800 text-red-500 dark:text-red-400 text-sm">
                            <i class="fas fa-trash text-xs"></i> Delete
                        </button>
                        <button onclick="closeArchiveView()"
                                class="action-btn border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-400 text-sm">
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
    const archiveSearch   = document.getElementById('archiveSearch');
    const archivePriority = document.getElementById('archivePriority');
    const archiveProject  = document.getElementById('archiveProject');
    const archiveRange    = document.getElementById('archiveRange');
    const archiveSort     = document.getElementById('archiveSort');
    const archiveGrid     = document.getElementById('archiveGrid');
    const archiveCountEl  = document.getElementById('archiveCount');
    const archiveNoMatch  = document.getElementById('archiveNoMatch');

    const archiveCards = archiveGrid ? Array.from(archiveGrid.querySelectorAll('[data-archive-card]')) : [];

    function sortArchiveCards(mode) {
        archiveCards.slice().sort((a, b) => {
            if (mode === 'title') {
                return a.dataset.title.localeCompare(b.dataset.title);
            }
            const aTs = Number(a.dataset.archivedTs || 0);
            const bTs = Number(b.dataset.archivedTs || 0);
            return mode === 'oldest' ? aTs - bTs : bTs - aTs;
        }).forEach(card => archiveGrid.appendChild(card));
    }

    function applyArchiveFilters() {
        if (!archiveGrid) { return; }

        const query   = archiveSearch.value.trim().toLowerCase();
        const priority = archivePriority.value;
        const project  = archiveProject.value;
        const range    = Number(archiveRange.value || 0);
        const now      = Date.now();
        let visible    = 0;

        sortArchiveCards(archiveSort.value);

        archiveCards.forEach(card => {
            const matchQuery    = card.dataset.title.includes(query) || card.dataset.description.includes(query);
            const matchPriority = !priority || card.dataset.priority === priority;
            const matchProject  = !project  || card.dataset.project  === project;
            let   matchRange    = true;
            if (range) {
                const ts = Number(card.dataset.archivedTs || 0) * 1000;
                matchRange = ts ? (now - ts) / 86400000 <= range : false;
            }
            const show = matchQuery && matchPriority && matchProject && matchRange;
            card.classList.toggle('hidden', !show);
            if (show) { visible++; }
        });

        if (archiveCountEl) { archiveCountEl.textContent = visible; }
        if (archiveNoMatch) { archiveNoMatch.classList.toggle('hidden', visible > 0); }
    }

    if (archiveSearch) {
        archiveSearch.addEventListener('input', applyArchiveFilters);
    }
    [archivePriority, archiveProject, archiveRange, archiveSort].forEach(el => {
        el?.addEventListener('change', applyArchiveFilters);
    });

    let currentArchiveTask = null;

    function getArchiveTask(taskId) {
        const card = archiveCards.find(c => Number(c.dataset.archiveCardId) === taskId);
        if (!card) { return null; }
        return {
            id:            taskId,
            title:         card.dataset.archiveTitle         || '',
            description:   card.dataset.archiveDescription   || '',
            project:       card.dataset.archiveProject       || '',
            assignee:      card.dataset.archiveAssignee      || '',
            priority:      card.dataset.archivePriority      || 'medium',
            priority_class: card.dataset.archivePriorityClass || 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-600',
            archived_at:   card.dataset.archiveArchivedAt    || '',
            due_date:      card.dataset.archiveDueDate       || '',
            completed_at:  card.dataset.archiveCompletedAt   || '',
        };
    }

    function fillArchiveModal(task) {
        currentArchiveTask = task;
        document.getElementById('archiveModalTitle').textContent       = task.title;
        document.getElementById('archiveModalArchivedAt').textContent  = task.archived_at ? `Archived ${task.archived_at}` : 'Archive date unavailable';
        document.getElementById('archiveModalDescription').textContent = task.description || 'No description provided.';
        document.getElementById('archiveModalProject').textContent     = task.project     || 'No project';
        document.getElementById('archiveModalAssignee').textContent    = task.assignee    || 'Myself';
        document.getElementById('archiveModalDueDate').textContent     = task.due_date    || 'No due date';
        document.getElementById('archiveModalCompletedAt').textContent = task.completed_at || 'Not completed';
        document.getElementById('archiveRestoreStatus').value          = 'pending';

        const badge = document.getElementById('archiveModalPriority');
        badge.textContent = task.priority;
        badge.className   = `archive-pill ${task.priority_class}`;
    }

    function openArchiveView(taskId) {
        const task = getArchiveTask(taskId);
        if (!task) { return; }
        fillArchiveModal(task);
        document.getElementById('archiveViewModal').classList.remove('hidden');
    }

    function closeArchiveView() {
        document.getElementById('archiveViewModal').classList.add('hidden');
        currentArchiveTask = null;
    }

    function openRestoreModal(taskId) {
        const task = getArchiveTask(taskId);
        if (!task) { return; }
        fillArchiveModal(task);
        document.getElementById('archiveViewModal').classList.remove('hidden');
    }

    function confirmRestoreFromModal() {
        if (!currentArchiveTask) { return; }
        restoreTask(currentArchiveTask.id, document.getElementById('archiveRestoreStatus').value);
    }

    function confirmDeleteFromModal() {
        if (!currentArchiveTask) { return; }
        deleteTask(currentArchiveTask.id);
    }

    function restoreTask(taskId, status) {
        const task = getArchiveTask(taskId);
        if (!task) { return; }
        Swal.fire({
            title: 'Restore task?',
            text: `"${task.title}" will be moved back to active tasks.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, restore',
            cancelButtonText: 'Cancel',
        }).then(result => {
            if (!result.isConfirmed) { return; }
            axios.post(`/tasks/${taskId}/unarchive`)
                .then(() => status && status !== 'pending' ? axios.patch(`/tasks/${taskId}/status`, { status }) : null)
                .then(() => Swal.fire({ icon: 'success', title: 'Restored!', text: 'Task moved back to active tasks.', timer: 1500, showConfirmButton: false })
                    .then(() => location.reload()))
                .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to restore task.' }));
        });
    }

    function deleteTask(taskId) {
        const task = getArchiveTask(taskId);
        if (!task) { return; }
        Swal.fire({
            title: 'Delete permanently?',
            text: `"${task.title}" will be removed and cannot be recovered.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
        }).then(result => {
            if (!result.isConfirmed) { return; }
            axios.delete(`/tasks/${taskId}`)
                .then(() => Swal.fire({ icon: 'success', title: 'Deleted!', text: 'Task has been removed.', timer: 1500, showConfirmButton: false })
                    .then(() => location.reload()))
                .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete task.' }));
        });
    }

    if (archiveGrid) { applyArchiveFilters(); }
</script>
@endpush
