@php
    $dueDate = $task->due_date ? \Carbon\Carbon::parse($task->due_date) : null;
    $isOverdue = $dueDate && $dueDate->isPast() && $task->status !== 'completed';
    $isDueSoon = $dueDate && $dueDate->diffInDays(now()) <= 3 && !$isOverdue;
    $daysUntilDue = $dueDate ? (int)$dueDate->diffInDays(now()) : null;

    // Calculate progress percentage based on status
    $progress = match($task->status) {
        'pending' => 0,
        'in-progress' => 50,
        'on-hold' => 25,
        'completed' => 100,
        default => 0
    };
@endphp

<div class="task-card group relative cursor-pointer"
     draggable="true"
     ondragstart="handleDragStart(event, {{ $task->id }})"
     ondragend="handleDragEnd(event)"
     onclick="openViewModal({{ $task->id }})"
     data-task-id="{{ $task->id }}"
     data-status="{{ $task->status }}">

    <!-- Priority Indicator Strip -->
    <div class="absolute left-0 top-0 bottom-0 w-1.5 rounded-l-lg priority-strip-{{ $task->priority }}"></div>

    <!-- Status Progress Bar (Top) -->
    <div class="absolute top-0 left-0 right-0 h-0.5 bg-gray-100">
        <div class="h-full progress-bar-{{ $task->status }}" style="width: {{ $progress }}%"></div>
    </div>

    <!-- Card Content -->
    <div class="pl-4">
        <!-- Checkbox for bulk selection -->
        <div class="flex items-start gap-3">
            <input type="checkbox"
                   class="task-checkbox mt-1.5"
                   style="display: none;"
                   onchange="toggleTaskSelection({{ $task->id }})"
                   data-task-id="{{ $task->id }}">

            <div class="flex-1 min-w-0">
                <!-- Title Row -->
                <div class="flex items-start justify-between gap-2 mb-2">
                    <h4 class="font-semibold text-gray-900 text-sm leading-snug group-hover:text-indigo-600 transition-colors flex-1">
                        {{ $task->title }}
                    </h4>
                    <!-- Status Icon Badge -->
                    <span class="flex-shrink-0 status-icon-{{ $task->status }}">
                        @if($task->status === 'completed')
                            <i class="fas fa-check text-xs"></i>
                        @elseif($task->status === 'in-progress')
                            <i class="fas fa-spinner fa-spin text-xs"></i>
                        @elseif($task->status === 'on-hold')
                            <i class="fas fa-pause text-xs"></i>
                        @else
                            <i class="fas fa-circle text-[8px]"></i>
                        @endif
                    </span>
                </div>

                <!-- Description Preview -->
                @if($task->description)
                    <p class="text-xs text-gray-500 mb-3 line-clamp-2 leading-relaxed">
                        {{ \Illuminate\Support\Str::limit(strip_tags($task->description), 120) }}
                    </p>
                @endif

                <!-- Tags and Badges Row -->
                <div class="flex flex-wrap items-center gap-1.5 mb-3">
                    <!-- Priority Badge -->
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider priority-badge-{{ $task->priority }}">
                        @if($task->priority === 'urgent')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path></svg>
                        @elseif($task->priority === 'high')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        @elseif($task->priority === 'medium')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                        @else
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        @endif
                        {{ $task->priority }}
                    </span>

                    <!-- Due Date Badge -->
                    @if($dueDate)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold due-badge-{{ $isOverdue ? 'overdue' : ($isDueSoon ? 'soon' : 'normal') }}">
                            @if($isOverdue)
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                {{ $daysUntilDue * -1 }}d overdue
                            @elseif($isDueSoon)
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                {{ $daysUntilDue }}d left
                            @else
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>
                                {{ $dueDate->format('M d') }}
                            @endif
                        </span>
                    @endif

                    <!-- Project Badge -->
                    @if($task->project)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                            {{ \Illuminate\Support\Str::limit($task->project->name, 20) }}
                        </span>
                    @endif
                </div>

                <!-- Footer Row: Assignee & Time -->
                <div class="flex items-center justify-between pt-2.5 border-t border-gray-100">
                    <!-- Left: Assignee -->
                    @if($task->assignedTo)
                        <div class="flex items-center gap-2">
                            <div class="relative">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-xs shadow-md">
                                    {{ $task->assignedTo->name[0] }}
                                </div>
                                <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></div>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold text-gray-800 leading-tight">{{ $task->assignedTo->name }}</span>
                                <span class="text-[10px] text-gray-400">Assigned</span>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center">
                                <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                            </div>
                            <span class="text-xs text-gray-400">Myself</span>
                        </div>
                    @endif

                    <!-- Right: Time Tracking -->
                    @if($task->estimated_hours || $task->actual_hours)
                        <div class="flex items-center gap-2.5">
                            @if($task->estimated_hours)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-[10px] font-semibold">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                    {{ $task->estimated_hours }}h
                                </span>
                            @endif
                            @if($task->actual_hours)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px] font-semibold border border-blue-200">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                    {{ $task->actual_hours }}h
                                </span>
                            @endif
                        </div>
                    @else
                        <div class="w-12"></div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions (Right Side) -->
            <div class="flex flex-col gap-1.5 opacity-0 group-hover:opacity-100 transition-all duration-200">
                <button onclick="event.stopPropagation(); openEditModal({{ $task->id }})"
                        class="w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition-all hover:scale-110 active:scale-95 shadow-sm hover:shadow"
                        title="Edit task">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
                <button onclick="event.stopPropagation(); deleteTask({{ $task->id }}, '{{ $task->title }}')"
                        class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 flex items-center justify-center transition-all hover:scale-110 active:scale-95 shadow-sm hover:shadow"
                        title="Delete task">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Priority strip colors */
    .priority-strip-urgent {
        background: linear-gradient(180deg, #ef4444 0%, #dc2626 100%);
        box-shadow: 0 0 8px rgba(239, 68, 68, 0.3);
    }

    .priority-strip-high {
        background: linear-gradient(180deg, #f97316 0%, #ea580c 100%);
        box-shadow: 0 0 8px rgba(249, 115, 22, 0.3);
    }

    .priority-strip-medium {
        background: linear-gradient(180deg, #eab308 0%, #ca8a04 100%);
        box-shadow: 0 0 8px rgba(234, 179, 8, 0.3);
    }

    .priority-strip-low {
        background: linear-gradient(180deg, #22c55e 0%, #16a34a 100%);
        box-shadow: 0 0 8px rgba(34, 197, 94, 0.3);
    }

    /* Priority badge styles */
    .priority-badge-urgent {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        color: #991b1b;
        border: 1px solid #fecaca;
        box-shadow: 0 1px 2px rgba(239, 68, 68, 0.1);
    }

    .priority-badge-high {
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
        color: #9a3412;
        border: 1px solid #fed7aa;
        box-shadow: 0 1px 2px rgba(249, 115, 22, 0.1);
    }

    .priority-badge-medium {
        background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%);
        color: #854d0e;
        border: 1px solid #fef08a;
        box-shadow: 0 1px 2px rgba(234, 179, 8, 0.1);
    }

    .priority-badge-low {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        color: #166534;
        border: 1px solid #bbf7d0;
        box-shadow: 0 1px 2px rgba(34, 197, 94, 0.1);
    }

    /* Status progress bars */
    .progress-bar-pending {
        background: linear-gradient(90deg, #fef3c7 0%, #fde68a 100%);
        transition: width 0.5s ease;
    }

    .progress-bar-in-progress {
        background: linear-gradient(90deg, #3b82f6 0%, #2563eb 100%);
        transition: width 0.5s ease;
        box-shadow: 0 0 8px rgba(59, 130, 246, 0.4);
    }

    .progress-bar-on-hold {
        background: linear-gradient(90deg, #a855f7 0%, #9333ea 100%);
        transition: width 0.5s ease;
    }

    .progress-bar-completed {
        background: linear-gradient(90deg, #22c55e 0%, #16a34a 100%);
        transition: width 0.5s ease;
        box-shadow: 0 0 8px rgba(34, 197, 94, 0.4);
    }

    /* Status icon badges */
    .status-icon-pending {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 2px rgba(234, 179, 8, 0.2);
    }

    .status-icon-in-progress {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 2px rgba(59, 130, 246, 0.2);
    }

    .status-icon-on-hold {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
        color: #6b21a8;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 2px rgba(168, 85, 247, 0.2);
    }

    .status-icon-completed {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 2px rgba(34, 197, 94, 0.2);
    }

    /* Due date badges */
    .due-badge-overdue {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        color: #991b1b;
        border: 1px solid #fecaca;
        box-shadow: 0 1px 2px rgba(239, 68, 68, 0.1);
    }

    .due-badge-soon {
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
        color: #9a3412;
        border: 1px solid #fed7aa;
        box-shadow: 0 1px 2px rgba(249, 115, 22, 0.1);
    }

    .due-badge-normal {
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        color: #6b7280;
        border: 1px solid #e5e7eb;
    }
</style>
