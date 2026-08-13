@extends('layouts.dashboard')

@section('title', 'Task Management')

@push('styles')
    <style>
        /* Task Board Layout */
        .task-board {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            min-height: calc(100vh - 300px);
        }

        @media (max-width: 1536px) {
            .task-board {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .task-board {
                grid-template-columns: 1fr;
            }
        }

        /* Task Column */
        .task-column {
            background: #f8fafc;
            border-radius: 16px;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            min-height: 500px;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .task-column.drag-over {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            border-color: #6366f1;
            transform: scale(1.02);
        }

        .task-column-header {
            display: flex;
            items-center: justify-between;
            margin-bottom: 1rem;
            padding: 0.75rem;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .column-pending .task-column-header {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }

        .column-in-progress .task-column-header {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }

        .column-on-hold .task-column-header {
            background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
            color: #6b21a8;
        }

        .column-completed .task-column-header {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }

        /* Task Card */
        .task-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.07);
            cursor: grab;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e5e7eb;
            position: relative;
            overflow: hidden;
        }

        .task-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-3px) scale(1.01);
            border-color: #d1d5db;
        }

        .task-card:active {
            cursor: grabbing;
        }

        .task-card.dragging {
            opacity: 0.5;
            transform: rotate(2deg) scale(0.95);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card.active {
            border-color: #6366f1;
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
        }

        /* Filter Pills */
        .filter-pill {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid #e2e8f0;
            background: white;
        }

        .filter-pill:hover {
            transform: translateY(-2px);
            border-color: #6366f1;
        }

        .filter-pill.active {
            border-color: #6366f1;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
        }

        /* Avatar */
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 12px;
        }

        /* Bulk Selection */
        .task-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #6366f1;
        }

        /* Drop Zone */
        .drop-zone {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s;
            min-height: 200px;
            position: relative;
        }

        .drop-zone.drag-over {
            background: rgba(99, 102, 241, 0.1);
            border: 2px dashed #6366f1;
        }

        /* Clickable area for adding tasks */
        .drop-zone::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1;
        }

        .drop-zone:hover::after {
            background: linear-gradient(to top, rgba(99, 102, 241, 0.05) 0%, transparent 100%);
        }

        .drop-zone .add-task-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem;
            margin-top: auto;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.8);
            cursor: pointer;
            transition: all 0.3s ease;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 600;
            z-index: 2;
            opacity: 0.7;
        }

        .drop-zone .add-task-placeholder:hover {
            opacity: 1;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .column-pending .add-task-placeholder {
            border-color: #fcd34d;
            color: #d97706;
            background: rgba(254, 243, 199, 0.6);
        }

        .column-pending .add-task-placeholder:hover {
            border-color: #f59e0b;
            color: #b45309;
            background: rgba(254, 243, 199, 0.9);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
        }

        .column-in-progress .add-task-placeholder {
            border-color: #93c5fd;
            color: #2563eb;
            background: rgba(219, 234, 254, 0.6);
        }

        .column-in-progress .add-task-placeholder:hover {
            border-color: #3b82f6;
            color: #1d4ed8;
            background: rgba(219, 234, 254, 0.9);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        .column-on-hold .add-task-placeholder {
            border-color: #d8b4fe;
            color: #9333ea;
            background: rgba(243, 232, 255, 0.6);
        }

        .column-on-hold .add-task-placeholder:hover {
            border-color: #a855f7;
            color: #7c3aed;
            background: rgba(243, 232, 255, 0.9);
            box-shadow: 0 4px 12px rgba(168, 85, 247, 0.2);
        }

        .column-completed .add-task-placeholder {
            border-color: #86efac;
            color: #16a34a;
            background: rgba(220, 252, 231, 0.6);
        }

        .column-completed .add-task-placeholder:hover {
            border-color: #22c55e;
            color: #15803d;
            background: rgba(220, 252, 231, 0.9);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
        }

        /* Custom SweetAlert Styles */
        .custom-alert-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .custom-alert-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .custom-alert {
            background: white;
            border-radius: 20px;
            padding: 0;
            max-width: 420px;
            width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transform: scale(0.9) translateY(20px);
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            overflow: hidden;
        }

        .custom-alert.show {
            transform: scale(1) translateY(0);
        }

        .alert-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-bottom: 1rem;
            animation: popIn 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .alert-icon.success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            box-shadow: 0 10px 30px rgba(34, 197, 94, 0.3);
        }

        .alert-icon.error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            box-shadow: 0 10px 30px rgba(239, 68, 68, 0.3);
        }

        .alert-icon.warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            box-shadow: 0 10px 30px rgba(234, 179, 8, 0.3);
        }

        .alert-icon.info {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
        }

        @keyframes popIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .alert-title {
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .alert-message {
            font-size: 15px;
            text-align: center;
            color: #6b7280;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .alert-actions {
            display: flex;
            gap: 0.75rem;
            padding: 1.5rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        .alert-btn {
            flex: 1;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            outline: none;
        }

        .alert-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .alert-btn:active {
            transform: translateY(0);
        }

        .alert-btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }

        .alert-btn-primary:hover {
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6);
        }

        .alert-btn-secondary {
            background: white;
            color: #6b7280;
            border: 2px solid #e5e7eb;
        }

        .alert-btn-secondary:hover {
            border-color: #6366f1;
            color: #6366f1;
        }

        .alert-btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
        }

        .alert-btn-danger:hover {
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.6);
        }

        /* Toast Notification */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            background: white;
            border-radius: 16px;
            padding: 16px 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 320px;
            animation: slideInRight 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            border-left: 4px solid;
        }

        .toast.removing {
            animation: slideOutRight 0.3s forwards;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .toast.success {
            border-left-color: #22c55e;
        }

        .toast.error {
            border-left-color: #ef4444;
        }

        .toast.warning {
            border-left-color: #f59e0b;
        }

        .toast.info {
            border-left-color: #3b82f6;
        }

        .toast-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .toast.success .toast-icon {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }

        .toast.error .toast-icon {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }

        .toast.warning .toast-icon {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }

        .toast.info .toast-icon {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            font-size: 14px;
            color: #1f2937;
            margin-bottom: 2px;
        }

        .toast-message {
            font-size: 13px;
            color: #6b7280;
        }

        .toast-close {
            width: 24px;
            height: 24px;
            border-radius: 8px;
            border: none;
            background: #f3f4f6;
            color: #6b7280;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .toast-close:hover {
            background: #e5e7eb;
            color: #1f2937;
        }

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

        /* ===== Dark Mode ===== */
        /* This board is styled with plain CSS classes (not Tailwind utilities),
           so dark mode needs its own override block keyed off the `dark`
           class on <html> rather than Tailwind's `dark:` variant. */
        .dark .task-column {
            background: #111827;
            border-color: #1f2937;
        }

        .dark .task-column.drag-over {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(139, 92, 246, 0.15) 100%);
            border-color: #818cf8;
        }

        .dark .column-pending .task-column-header {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
        }

        .dark .column-in-progress .task-column-header {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
        }

        .dark .column-on-hold .task-column-header {
            background: rgba(168, 85, 247, 0.15);
            color: #c084fc;
        }

        .dark .column-completed .task-column-header {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
        }

        .dark .task-card {
            background: #1f2937;
            border-color: #374151;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .dark .task-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            border-color: #4b5563;
        }

        .dark .task-card.dragging {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        }

        .dark .stat-card {
            background: #1f2937;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .dark .stat-card.active {
            border-color: #818cf8;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.12) 0%, rgba(139, 92, 246, 0.12) 100%);
        }

        .dark .filter-pill {
            border-color: #374151;
            background: #1f2937;
            color: #e5e7eb;
        }

        .dark .filter-pill:hover {
            border-color: #818cf8;
        }

        .dark .drop-zone.drag-over {
            background: rgba(99, 102, 241, 0.15);
            border-color: #818cf8;
        }

        .dark .drop-zone .add-task-placeholder {
            background: rgba(31, 41, 55, 0.8);
            border-color: #4b5563;
            color: #9ca3af;
        }

        .dark .column-pending .add-task-placeholder {
            border-color: rgba(252, 211, 77, 0.4);
            color: #fbbf24;
            background: rgba(254, 243, 199, 0.06);
        }

        .dark .column-pending .add-task-placeholder:hover {
            border-color: #f59e0b;
            color: #fcd34d;
            background: rgba(254, 243, 199, 0.1);
        }

        .dark .column-in-progress .add-task-placeholder {
            border-color: rgba(147, 197, 253, 0.4);
            color: #60a5fa;
            background: rgba(219, 234, 254, 0.06);
        }

        .dark .column-in-progress .add-task-placeholder:hover {
            border-color: #3b82f6;
            color: #93c5fd;
            background: rgba(219, 234, 254, 0.1);
        }

        .dark .column-on-hold .add-task-placeholder {
            border-color: rgba(216, 180, 254, 0.4);
            color: #c084fc;
            background: rgba(243, 232, 255, 0.06);
        }

        .dark .column-on-hold .add-task-placeholder:hover {
            border-color: #a855f7;
            color: #d8b4fe;
            background: rgba(243, 232, 255, 0.1);
        }

        .dark .column-completed .add-task-placeholder {
            border-color: rgba(134, 239, 172, 0.4);
            color: #4ade80;
            background: rgba(220, 252, 231, 0.06);
        }

        .dark .column-completed .add-task-placeholder:hover {
            border-color: #22c55e;
            color: #86efac;
            background: rgba(220, 252, 231, 0.1);
        }

        .dark .custom-alert {
            background: #1f2937;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .dark .alert-title {
            background: linear-gradient(135deg, #f3f4f6 0%, #d1d5db 100%);
            -webkit-background-clip: text;
            background-clip: text;
        }

        .dark .alert-message {
            color: #9ca3af;
        }

        .dark .alert-actions {
            background: #111827;
            border-top-color: #374151;
        }

        .dark .alert-btn-secondary {
            background: #1f2937;
            color: #9ca3af;
            border-color: #374151;
        }

        .dark .alert-btn-secondary:hover {
            border-color: #818cf8;
            color: #818cf8;
        }

        .dark .toast {
            background: #1f2937;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        }

        .dark .toast-title {
            color: #f3f4f6;
        }

        .dark .toast-message {
            color: #9ca3af;
        }

        .dark .toast-close {
            background: #374151;
            color: #9ca3af;
        }

        .dark .toast-close:hover {
            background: #4b5563;
            color: #f3f4f6;
        }

        .dark .toast.success .toast-icon {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
        }

        .dark .toast.error .toast-icon {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
        }

        .dark .toast.warning .toast-icon {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
        }

        .dark .toast.info .toast-icon {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
        }

        .dark .priority-badge-urgent {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.3);
        }

        .dark .priority-badge-high {
            background: rgba(249, 115, 22, 0.1);
            color: #fb923c;
            border-color: rgba(249, 115, 22, 0.3);
        }

        .dark .priority-badge-medium {
            background: rgba(234, 179, 8, 0.1);
            color: #facc15;
            border-color: rgba(234, 179, 8, 0.3);
        }

        .dark .priority-badge-low {
            background: rgba(34, 197, 94, 0.1);
            color: #4ade80;
            border-color: rgba(34, 197, 94, 0.3);
        }

        .dark .status-icon-pending {
            background: rgba(234, 179, 8, 0.15);
            color: #fbbf24;
        }

        .dark .status-icon-in-progress {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
        }

        .dark .status-icon-on-hold {
            background: rgba(168, 85, 247, 0.15);
            color: #c084fc;
        }

        .dark .status-icon-completed {
            background: rgba(34, 197, 94, 0.15);
            color: #4ade80;
        }

        .dark .due-badge-overdue {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.3);
        }

        .dark .due-badge-soon {
            background: rgba(249, 115, 22, 0.1);
            color: #fb923c;
            border-color: rgba(249, 115, 22, 0.3);
        }

        .dark .due-badge-normal {
            background: #1f2937;
            color: #9ca3af;
            border-color: #374151;
        }

        .dark .progress-bar-pending {
            background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        }
    </style>
@endpush

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Task Board</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Drag and drop to organize your tasks</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('tasks.archived') }}" class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl font-semibold hover:from-gray-600 hover:to-gray-700 transition-all shadow-lg hover:shadow-xl">
                <i class="fas fa-archive mr-2"></i>View Archived
            </a>
            <button onclick="openCreateModal()" class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                <i class="fas fa-plus mr-2"></i>New Task
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card" onclick="filterByStatus('all')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">All Tasks</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100" id="stat-total">0</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-tasks text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card" onclick="filterByStatus('pending')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400" id="stat-pending">0</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card" onclick="filterByStatus('in-progress')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">In Progress</p>
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400" id="stat-in-progress">0</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-spinner text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="stat-card" onclick="filterByStatus('completed')">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Completed</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400" id="stat-completed">0</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[300px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                    <input type="text" id="searchTasks" placeholder="Search tasks..." class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder:text-gray-400 dark:placeholder:text-gray-500">
                </div>
            </div>

            <div class="flex gap-2 flex-wrap">
                <button class="filter-pill active" data-filter="all" onclick="filterByStatus('all')">All</button>
                <button class="filter-pill" data-filter="pending" onclick="filterByStatus('pending')">Pending</button>
                <button class="filter-pill" data-filter="in-progress" onclick="filterByStatus('in-progress')">In Progress</button>
                <button class="filter-pill" data-filter="on-hold" onclick="filterByStatus('on-hold')">On Hold</button>
                <button class="filter-pill" data-filter="completed" onclick="filterByStatus('completed')">Completed</button>
            </div>

            <select id="filterPriority" onchange="applyFilters()" class="px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors font-semibold bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                <option value="">All Priorities</option>
                <option value="urgent">Urgent</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>

            <select id="filterOrder" onchange="applyFilters()" class="px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-indigo-500 focus:outline-none transition-colors font-semibold bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                <option value="">All Orders</option>
                @foreach($orders as $order)
                    <option value="{{ $order->id }}">{{ $order->name }} (ID: {{ $order->id }})</option>
                @endforeach
            </select>

            <button onclick="toggleBulkMode()" id="bulkModeBtn" class="px-4 py-3 border-2 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                <i class="fas fa-check-square mr-2"></i>Select
            </button>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulkActionBar" class="hidden bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-4 mb-6">
        <div class="flex items-center justify-between text-white">
            <div class="flex items-center gap-4">
                <span class="font-semibold"><span id="selectedCount">0</span> tasks selected</span>
                <select id="bulkAction" class="px-4 py-2 rounded-lg text-gray-800 font-semibold">
                    <option value="">Bulk Actions</option>
                    <option value="mark-pending">Mark as Pending</option>
                    <option value="mark-in-progress">Mark as In Progress</option>
                    <option value="mark-on-hold">Mark as On Hold</option>
                    <option value="mark-completed">Mark as Completed</option>
                    <option value="delete">Delete Selected</option>
                </select>
                <button onclick="applyBulkAction()" class="px-4 py-2 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Apply
                </button>
            </div>
            <button onclick="clearSelection()" class="px-4 py-2 bg-white/20 rounded-lg font-semibold hover:bg-white/30 transition-colors">
                Clear Selection
            </button>
        </div>
    </div>

    <!-- Task Board -->
    <div class="task-board">
        <!-- Pending Column -->
        <div class="task-column column-pending">
            <div class="task-column-header">
                <div class="flex items-center gap-2">
                    <i class="fas fa-clock"></i>
                    <span>Pending</span>
                </div>
                <span class="bg-white/50 px-3 py-1 rounded-full text-sm" id="count-pending">0</span>
            </div>
            <div class="drop-zone" data-status="pending" ondragover="handleDragOver(event)" ondrop="handleDrop(event, 'pending')" ondragleave="handleDragLeave(event)">
                <!-- Tasks will be rendered here by JavaScript -->
            </div>
        </div>

        <!-- In Progress Column -->
        <div class="task-column column-in-progress">
            <div class="task-column-header">
                <div class="flex items-center gap-2">
                    <i class="fas fa-spinner"></i>
                    <span>In Progress</span>
                </div>
                <span class="bg-white/50 px-3 py-1 rounded-full text-sm" id="count-in-progress">0</span>
            </div>
            <div class="drop-zone" data-status="in-progress" ondragover="handleDragOver(event)" ondrop="handleDrop(event, 'in-progress')" ondragleave="handleDragLeave(event)">
                <!-- Tasks will be rendered here by JavaScript -->
            </div>
        </div>

        <!-- On Hold Column -->
        <div class="task-column column-on-hold">
            <div class="task-column-header">
                <div class="flex items-center gap-2">
                    <i class="fas fa-pause-circle"></i>
                    <span>On Hold</span>
                </div>
                <span class="bg-white/50 px-3 py-1 rounded-full text-sm" id="count-on-hold">0</span>
            </div>
            <div class="drop-zone" data-status="on-hold" ondragover="handleDragOver(event)" ondrop="handleDrop(event, 'on-hold')" ondragleave="handleDragLeave(event)">
                <!-- Tasks will be rendered here by JavaScript -->
            </div>
        </div>

        <!-- Completed Column -->
        <div class="task-column column-completed">
            <div class="task-column-header">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Completed</span>
                </div>
                <span class="bg-white/50 px-3 py-1 rounded-full text-sm" id="count-completed">0</span>
            </div>
            <div class="drop-zone" data-status="completed" ondragover="handleDragOver(event)" ondrop="handleDrop(event, 'completed')" ondragleave="handleDragLeave(event)">
                <!-- Tasks will be rendered here by JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Task Modal -->
<div id="taskModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeModal()"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-xl dark:shadow-2xl dark:shadow-black/60 dark:ring-1 dark:ring-white/10 transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100" id="modalTitle">Create New Task</h3>
                        <button onclick="closeModal()" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <form id="taskForm" class="p-6">
                    <input type="hidden" id="taskId">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Title *</label>
                            <input type="text" id="taskTitle" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-indigo-500 focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100" placeholder="Enter task title">
                            <p class="text-red-500 text-xs mt-1 hidden" id="taskTitleError"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea id="taskDescription" rows="3" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-indigo-500 focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100"></textarea>
                            <p class="text-red-500 text-xs mt-1 hidden" id="taskDescriptionError"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                <select id="taskStatus" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-indigo-500 focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                    <option value="pending">Pending</option>
                                    <option value="in-progress">In Progress</option>
                                    <option value="on-hold">On Hold</option>
                                    <option value="completed">Completed</option>
                                </select>
                                <p class="text-red-500 text-xs mt-1 hidden" id="taskStatusError"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                                <select id="taskPriority" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-indigo-500 focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                                <p class="text-red-500 text-xs mt-1 hidden" id="taskPriorityError"></p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Due Date</label>
                                <input type="date" id="taskDueDate" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-indigo-500 focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                <p class="text-red-500 text-xs mt-1 hidden" id="taskDueDateError"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Assigned To</label>
                                <select id="taskAssignedTo" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-indigo-500 focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                    <option value="" selected>Assign to yourself</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-red-500 text-xs mt-1 hidden" id="taskAssignedToError"></p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Order</label>
                            <div class="relative">
                                <input type="hidden" id="taskOrderId">
                                <input type="text"
                                       id="taskOrderSearch"
                                       placeholder="Search orders..."
                                       autocomplete="off"
                                       class="w-full px-4 py-3 pr-10 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-indigo-500 focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                <button type="button" id="clearTaskOrder"
                                        class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                                <div id="taskOrderDropdown"
                                     class="hidden absolute z-[60] w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl shadow-lg max-h-52 overflow-y-auto">
                                </div>
                            </div>
                            <p class="text-red-500 text-xs mt-1 hidden" id="taskOrderError"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Estimated Hours</label>
                                <input type="number" id="taskEstimatedHours" min="0" step="0.5" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-indigo-500 focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                <p class="text-red-500 text-xs mt-1 hidden" id="taskEstimatedHoursError"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Actual Hours</label>
                                <input type="number" id="taskActualHours" min="0" step="0.5" class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-indigo-500 focus:outline-none bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100">
                                <p class="text-red-500 text-xs mt-1 hidden" id="taskActualHoursError"></p>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeModal()" class="px-6 py-3 border-2 border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all">
                            Save Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Task Modal -->
<div id="viewTaskModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" onclick="closeViewModal()"></div>

    <!-- Modal Panel -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div id="viewModalStatusIcon" class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                <!-- Status icon will be injected here -->
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white" id="viewModalTitle">Task Title</h3>
                                <p class="text-sm text-indigo-100" id="viewModalOrder">Order Name</p>
                            </div>
                        </div>
                        <button onclick="closeViewModal()" class="text-white hover:text-gray-200 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-6">
                    <!-- Description -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</h4>
                        <div id="viewModalDescription" class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                            No description provided
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status</h4>
                            <div id="viewModalStatus" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-semibold">
                                <!-- Status badge -->
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Priority</h4>
                            <div id="viewModalPriority" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-bold uppercase">
                                <!-- Priority badge -->
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Due Date</h4>
                            <div id="viewModalDueDate" class="text-sm text-gray-900 dark:text-gray-100 flex items-center gap-2">
                                <!-- Due date -->
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Assigned To</h4>
                            <div id="viewModalAssignedTo" class="flex items-center gap-2">
                                <!-- Assignee -->
                            </div>
                        </div>
                    </div>

                    <!-- Time Tracking -->
                    <div id="viewModalTimeTracking" class="mb-6 hidden">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Time Tracking</h4>
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <i class="fas fa-clock text-gray-500 dark:text-gray-400"></i>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Estimated</p>
                                    <p id="viewModalEstimatedHours" class="text-sm font-semibold text-gray-900 dark:text-gray-100">0h</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-500/10 rounded-lg border border-blue-200 dark:border-blue-900">
                                <i class="fas fa-stopwatch text-blue-600 dark:text-blue-400"></i>
                                <div>
                                    <p class="text-xs text-blue-600 dark:text-blue-400">Actual</p>
                                    <p id="viewModalActualHours" class="text-sm font-semibold text-blue-900 dark:text-blue-300">0h</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 dark:bg-gray-800 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Created: <span id="viewModalCreatedAt" class="font-semibold"></span></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <button id="viewModalArchiveBtn" onclick="archiveTaskFromModal()" class="px-4 py-2 border border-orange-300 dark:border-orange-800 rounded-lg font-semibold text-orange-700 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-500/10 transition-colors hidden">
                            <i class="fas fa-archive mr-2"></i>Archive
                        </button>
                        <button onclick="closeViewModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Close
                        </button>
                        <button id="viewModalEditBtn" class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg font-semibold hover:from-indigo-600 hover:to-purple-700 transition-all">
                            <i class="fas fa-edit mr-2"></i>Edit Task
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Alert Modal -->
<div id="customAlert" class="custom-alert-overlay">
    <div class="custom-alert">
        <div class="p-8 text-center">
            <div class="alert-icon" id="alertIcon">
                <i class="fas fa-check"></i>
            </div>
            <h3 class="alert-title" id="alertTitle">Success!</h3>
            <p class="alert-message" id="alertMessage">Operation completed successfully.</p>
        </div>
        <div class="alert-actions" id="alertActions">
            <!-- Buttons will be injected here -->
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>
@endsection

@push('scripts')
<script>
// Task Management State
let tasksData = @js($tasks);
let selectedTasks = new Set();
let bulkMode = false;
let draggedTask = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    renderTasks();
    updateStats();
});

// Render all tasks
function renderTasks() {
    const statuses = ['pending', 'in-progress', 'on-hold', 'completed'];

    statuses.forEach(status => {
        const dropZone = document.querySelector(`.drop-zone[data-status="${status}"]`);
        const tasks = tasksData.filter(task => task.status === status);

        // Render tasks and add the placeholder
        dropZone.innerHTML = tasks.map(task => renderTaskCard(task)).join('') +
            `<div class="add-task-placeholder" onclick="openCreateModalForStatus('${status}')">
                <i class="fas fa-plus"></i>
                <span>Add Task</span>
            </div>`;
        document.getElementById(`count-${status}`).textContent = tasks.length;
    });
}

// Render individual task card
function renderTaskCard(task) {
    const dueDate = task.due_date ? new Date(task.due_date) : null;
    const now = new Date();
    const isOverdue = dueDate && dueDate < now && task.status !== 'completed';
    const isDueSoon = dueDate && !isOverdue && Math.ceil((dueDate - now) / (1000 * 60 * 60 * 24)) <= 3;
    const daysUntilDue = dueDate ? Math.ceil((dueDate - now) / (1000 * 60 * 60 * 24)) : null;

    const progress = {
        'pending': 0,
        'in-progress': 50,
        'on-hold': 25,
        'completed': 100
    }[task.status] || 0;

    const priorityIcons = {
        urgent: `<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path></svg>`,
        high: `<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>`,
        medium: `<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>`,
        low: `<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>`
    };

    const statusIcons = {
        'completed': '<i class="fas fa-check text-xs"></i>',
        'in-progress': '<i class="fas fa-spinner fa-spin text-xs"></i>',
        'on-hold': '<i class="fas fa-pause text-xs"></i>',
        'pending': '<i class="fas fa-circle text-[8px]"></i>'
    };

    return `
        <div class="task-card group relative cursor-pointer"
             draggable="true"
             ondragstart="handleDragStart(event, ${task.id})"
             ondragend="handleDragEnd(event)"
             onclick="openViewModal(${task.id})"
             data-task-id="${task.id}"
             data-status="${task.status}">

            <!-- Priority Indicator Strip -->
            <div class="absolute left-0 top-0 bottom-0 w-1.5 rounded-l-lg priority-strip-${task.priority}"></div>

            <!-- Status Progress Bar (Top) -->
            <div class="absolute top-0 left-0 right-0 h-0.5 bg-gray-100 dark:bg-gray-700">
                <div class="h-full progress-bar-${task.status}" style="width: ${progress}%"></div>
            </div>

            <!-- Card Content -->
            <div class="pl-4">
                <!-- Checkbox for bulk selection -->
                <div class="flex items-start gap-3">
                    <input type="checkbox"
                           class="task-checkbox mt-1.5"
                           style="display: none;"
                           onchange="toggleTaskSelection(${task.id})"
                           data-task-id="${task.id}">

                    <div class="flex-1 min-w-0">
                        <!-- Title Row -->
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm leading-snug group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors flex-1">
                                ${task.title}
                            </h4>
                            <!-- Status Icon Badge -->
                            <span class="flex-shrink-0 status-icon-${task.status}">
                                ${statusIcons[task.status] || statusIcons['pending']}
                            </span>
                        </div>

                        <!-- Description Preview -->
                        ${task.description ? `
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 line-clamp-2 leading-relaxed">
                                ${task.description.substring(0, 120)}${task.description.length > 120 ? '...' : ''}
                            </p>
                        ` : ''}

                        <!-- Tags and Badges Row -->
                        <div class="flex flex-wrap items-center gap-1.5 mb-3">
                            <!-- Priority Badge -->
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider priority-badge-${task.priority}">
                                ${priorityIcons[task.priority] || priorityIcons['low']}
                                ${task.priority}
                            </span>

                            <!-- Due Date Badge -->
                            ${dueDate ? `
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold due-badge-${isOverdue ? 'overdue' : (isDueSoon ? 'soon' : 'normal')}">
                                    ${isOverdue
                                        ? `<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>${Math.abs(daysUntilDue)}d overdue`
                                        : isDueSoon
                                            ? `<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>${daysUntilDue}d left`
                                            : `<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path></svg>${dueDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}`
                                    }
                                </span>
                            ` : ''}

                            <!-- Order Badge -->
                            ${task.order ? `
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-medium bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-900">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                                    ${task.order.name.length > 20 ? task.order.name.substring(0, 20) + '...' : task.order.name}
                                </span>
                            ` : ''}
                        </div>

                        <!-- Footer Row: Assignee & Time -->
                        <div class="flex items-center justify-between pt-2.5 border-t border-gray-100 dark:border-gray-700">
                            <!-- Left: Assignee -->
                            ${task.assigned_to && task.assignedTo ? `
                                <div class="flex items-center gap-2">
                                    <div class="relative">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-xs shadow-md">
                                            ${task.assignedTo.name.charAt(0)}
                                        </div>
                                        <div class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></div>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-200 leading-tight">${task.assignedTo.name}</span>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500">Assigned</span>
                                    </div>
                                </div>
                            ` : `
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center">
                                        <svg class="w-3 h-3 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                    </div>
                                    <span class="text-xs text-gray-400 dark:text-gray-500">Myself</span>
                                </div>
                            `}

                            <!-- Right: Time Tracking -->
                            ${(task.estimated_hours || task.actual_hours) ? `
                                <div class="flex items-center gap-2.5">
                                    ${task.estimated_hours ? `
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-[10px] font-semibold">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                            ${task.estimated_hours}h
                                        </span>
                                    ` : ''}
                                    ${task.actual_hours ? `
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 text-[10px] font-semibold border border-blue-200 dark:border-blue-900">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                            ${task.actual_hours}h
                                        </span>
                                    ` : ''}
                                </div>
                            ` : `
                                <div class="w-12"></div>
                            `}
                        </div>
                    </div>

                    <!-- Quick Actions (Right Side) -->
                    <div class="flex flex-col gap-1.5 opacity-0 group-hover:opacity-100 transition-all duration-200">
                        <button onclick="event.stopPropagation(); openEditModal(${task.id})"
                                class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-500/20 text-blue-600 dark:text-blue-400 flex items-center justify-center transition-all hover:scale-110 active:scale-95 shadow-sm hover:shadow"
                                title="Edit task">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                        ${task.status === 'completed' ? `
                            <button onclick="event.stopPropagation(); archiveTask(${task.id}, '${task.title.replace(/'/g, "\\'")}')" 
                                    class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-500/10 hover:bg-amber-100 dark:hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 flex items-center justify-center transition-all hover:scale-110 active:scale-95 shadow-sm hover:shadow"
                                    title="Archive task">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                            </button>
                        ` : ''}
                        <button onclick="event.stopPropagation(); deleteTask(${task.id}, '${task.title.replace(/'/g, "\\'")}')"
                                class="w-7 h-7 rounded-lg bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 text-red-600 dark:text-red-400 flex items-center justify-center transition-all hover:scale-110 active:scale-95 shadow-sm hover:shadow"
                                title="Delete task">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Update statistics
function updateStats() {
    const stats = {
        total: tasksData.length,
        pending: tasksData.filter(t => t.status === 'pending').length,
        'in-progress': tasksData.filter(t => t.status === 'in-progress').length,
        'on-hold': tasksData.filter(t => t.status === 'on-hold').length,
        completed: tasksData.filter(t => t.status === 'completed').length
    };

    Object.keys(stats).forEach(key => {
        const el = document.getElementById(`stat-${key}`);
        if (el) el.textContent = stats[key];
    });
}

// Drag and Drop
function handleDragStart(event, taskId) {
    draggedTask = taskId;
    event.target.classList.add('dragging');
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', taskId);
}

function handleDragEnd(event) {
    event.target.classList.remove('dragging');
    document.querySelectorAll('.drop-zone').forEach(zone => {
        zone.classList.remove('drag-over');
    });
    document.querySelectorAll('.task-column').forEach(col => {
        col.classList.remove('drag-over');
    });
}

function handleDragOver(event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
    event.currentTarget.classList.add('drag-over');
    event.currentTarget.closest('.task-column').classList.add('drag-over');
}

function handleDragLeave(event) {
    event.currentTarget.classList.remove('drag-over');
    event.currentTarget.closest('.task-column')?.classList.remove('drag-over');
}

async function handleDrop(event, newStatus) {
    event.preventDefault();
    event.stopPropagation();
    event.currentTarget.classList.remove('drag-over');
    event.currentTarget.closest('.task-column')?.classList.remove('drag-over');

    const taskId = parseInt(event.dataTransfer.getData('text/plain'));
    if (!taskId) return;

    const task = tasksData.find(t => t.id === taskId);
    if (!task || task.status === newStatus) return;

    // Optimistic update
    const oldStatus = task.status;
    task.status = newStatus;

    if (newStatus === 'completed') {
        task.completed_at = new Date().toISOString();
    } else {
        task.completed_at = null;
    }

    renderTasks();
    updateStats();

    // API call
    try {
        const response = await axios.patch(`/tasks/${taskId}/status`, { status: newStatus });

        showToast('Success!', response.data.message || 'Task status updated!', 'success');
    } catch (error) {
        // Rollback on error
        task.status = oldStatus;
        task.completed_at = null;
        renderTasks();
        updateStats();

        let errorMsg = 'Failed to update task status.';
        if (error.response) {
            if (error.response.status === 403) {
                errorMsg = error.response.data.message || 'You do not have permission to change this task status.';
            } else if (error.response.status === 422) {
                const errors = error.response.data.errors;
                errorMsg = errors ? Object.values(errors).flat().join(' ') : (error.response.data.message || errorMsg);
            } else if (error.response.data && error.response.data.message) {
                errorMsg = error.response.data.message;
            }
        }
        showToast('Error!', errorMsg, 'error');
    }

    draggedTask = null;
}

// Custom Alert System
function showAlert(type, title, message, buttons = []) {
    const overlay = document.getElementById('customAlert');
    const icon = document.getElementById('alertIcon');
    const titleEl = document.getElementById('alertTitle');
    const messageEl = document.getElementById('alertMessage');
    const actionsEl = document.getElementById('alertActions');

    // Set icon
    icon.className = `alert-icon ${type}`;
    const icons = {
        success: 'fa-check',
        error: 'fa-times',
        warning: 'fa-exclamation',
        info: 'fa-info'
    };
    icon.innerHTML = `<i class="fas fa-${icons[type]}"></i>`;

    // Set content
    titleEl.textContent = title;
    messageEl.textContent = message;

    // Set buttons
    if (buttons.length === 0) {
        buttons = [{
            text: 'OK',
            class: 'primary',
            action: () => hideAlert()
        }];
    }

    actionsEl.innerHTML = buttons.map(btn => {
        const classMap = {
            primary: 'alert-btn-primary',
            secondary: 'alert-btn-secondary',
            danger: 'alert-btn-danger'
        };
        return `<button class="alert-btn ${classMap[btn.class]}" onclick="${btn.action}">${btn.text}</button>`;
    }).join('');

    // Show alert
    overlay.classList.add('show');
    document.querySelector('.custom-alert').classList.add('show');
}

function hideAlert() {
    const overlay = document.getElementById('customAlert');
    overlay.classList.remove('show');
    document.querySelector('.custom-alert').classList.remove('show');
}

// Toast Notifications
function showToast(title, message, type = 'info') {
    const container = document.getElementById('toastContainer');

    const icons = {
        success: 'fa-check',
        error: 'fa-times',
        warning: 'fa-exclamation',
        info: 'fa-info'
    };

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <div class="toast-icon">
            <i class="fas fa-${icons[type]}"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="removeToast(this.parentElement)">
            <i class="fas fa-times"></i>
        </button>
    `;

    container.appendChild(toast);

    // Auto remove after 4 seconds
    setTimeout(() => removeToast(toast), 4000);
}

function removeToast(toast) {
    toast.classList.add('removing');
    setTimeout(() => toast.remove(), 300);
}

// Update task status (for quick status change)
async function quickUpdateStatus(taskId, status) {
    const task = tasksData.find(t => t.id === taskId);
    if (!task) return;

    const oldStatus = task.status;
    task.status = status;

    if (status === 'completed') {
        task.completed_at = new Date().toISOString();
    } else {
        task.completed_at = null;
    }

    renderTasks();
    updateStats();

    try {
        await axios.patch(`/tasks/${taskId}/status`, { status });
        showToast('Success!', 'Task status updated successfully!', 'success');
    } catch (error) {
        task.status = oldStatus;
        task.completed_at = null;
        renderTasks();
        updateStats();
        showToast('Error!', 'Failed to update task status.', 'error');
    }
}

// Delete task
function deleteTask(taskId, taskTitle) {
    showAlert(
        'warning',
        'Delete Task?',
        `You are about to delete "${taskTitle}". This action cannot be undone.`,
        [
            {
                text: 'Cancel',
                class: 'secondary',
                action: 'hideAlert()'
            },
            {
                text: 'Delete',
                class: 'danger',
                action: `confirmDeleteTask(${taskId})`
            }
        ]
    );
}

async function confirmDeleteTask(taskId) {
    hideAlert();

    try {
        await axios.delete(`/tasks/${taskId}`);

        tasksData = tasksData.filter(t => t.id !== taskId);
        renderTasks();
        updateStats();

        showToast('Deleted!', 'Task has been deleted.', 'success');
    } catch (error) {
        showToast('Error!', 'Failed to delete task.', 'error');
    }
}

// Archive task
function archiveTask(taskId, taskTitle) {
    showAlert(
        'info',
        'Archive Task?',
        `You are about to archive "${taskTitle}". You can unarchive it later from the archived tasks page.`,
        [
            {
                text: 'Cancel',
                class: 'secondary',
                action: 'hideAlert()'
            },
            {
                text: 'Archive',
                class: 'primary',
                action: `confirmArchiveTask(${taskId})`
            }
        ]
    );
}

async function confirmArchiveTask(taskId) {
    hideAlert();

    try {
        await axios.post(`/tasks/${taskId}/archive`);

        tasksData = tasksData.filter(t => t.id !== taskId);
        renderTasks();
        updateStats();

        showToast('Archived!', 'Task has been archived successfully.', 'success');
    } catch (error) {
        showToast('Error!', 'Failed to archive task.', 'error');
    }
}

// Modal functions
function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Create New Task';
    document.getElementById('taskForm').reset();
    document.getElementById('taskId').value = '';
    resetOrderPicker();
    document.getElementById('taskModal').classList.remove('hidden');
}

function openCreateModalForStatus(status) {
    document.getElementById('modalTitle').textContent = 'Create New Task';
    document.getElementById('taskForm').reset();
    document.getElementById('taskId').value = '';
    document.getElementById('taskStatus').value = status;
    resetOrderPicker();
    document.getElementById('taskModal').classList.remove('hidden');
}

function openEditModal(taskId) {
    axios.get(`/tasks/${taskId}/edit`)
        .then(response => {
            const task = response.data.task;
            document.getElementById('modalTitle').textContent = 'Edit Task';
            document.getElementById('taskId').value = task.id;
            document.getElementById('taskTitle').value = task.title;
            document.getElementById('taskDescription').value = task.description || '';
            document.getElementById('taskStatus').value = task.status;
            document.getElementById('taskPriority').value = task.priority;
            document.getElementById('taskDueDate').value = task.due_date || '';
            document.getElementById('taskAssignedTo').value = task.assigned_to || '';
            document.getElementById('taskEstimatedHours').value = task.estimated_hours || '';
            document.getElementById('taskActualHours').value = task.actual_hours || '';

            if (task.order) {
                setOrderPicker(task.order.id, task.order.name, task.order.status);
            } else {
                resetOrderPicker();
            }

            document.getElementById('taskModal').classList.remove('hidden');
        })
        .catch(error => {
            showToast('Error!', 'Failed to load task data.', 'error');
        });
}

function closeModal() {
    document.getElementById('taskModal').classList.add('hidden');
}

// View Modal Functions
function openViewModal(taskId) {
    if (event.target.closest('button')) {
        return;
    }

    axios.get(`/tasks/${taskId}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(response => {
            const task = response.data.task;

            document.getElementById('viewModalTitle').textContent = task.title;
            document.getElementById('viewModalOrder').textContent = task.order ? `${task.order.name} (ID: ${task.order.id})` : 'No Order';
            document.getElementById('viewModalDescription').innerHTML = task.description || 'No description provided';

            const statusConfig = {
                'pending': { icon: 'fa-circle', color: 'bg-yellow-100 dark:bg-yellow-500/10 text-yellow-800 dark:text-yellow-400' },
                'in-progress': { icon: 'fa-spinner fa-spin', color: 'bg-blue-100 dark:bg-blue-500/10 text-blue-800 dark:text-blue-400' },
                'on-hold': { icon: 'fa-pause', color: 'bg-purple-100 dark:bg-purple-500/10 text-purple-800 dark:text-purple-400' },
                'completed': { icon: 'fa-check', color: 'bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400' }
            };
            const status = statusConfig[task.status];
            document.getElementById('viewModalStatus').innerHTML = `<i class="fas ${status.icon} mr-1"></i>${task.status.replace('-', ' ')}`;
            document.getElementById('viewModalStatus').className = `inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-semibold ${status.color}`;
            document.getElementById('viewModalStatusIcon').innerHTML = `<i class="fas ${status.icon} text-white"></i>`;

            const priorityConfig = {
                'urgent': { color: 'bg-red-100 dark:bg-red-500/10 text-red-800 dark:text-red-400', border: 'border-red-200 dark:border-red-900' },
                'high': { color: 'bg-orange-100 dark:bg-orange-500/10 text-orange-800 dark:text-orange-400', border: 'border-orange-200 dark:border-orange-900' },
                'medium': { color: 'bg-yellow-100 dark:bg-yellow-500/10 text-yellow-800 dark:text-yellow-400', border: 'border-yellow-200 dark:border-yellow-900' },
                'low': { color: 'bg-green-100 dark:bg-green-500/10 text-green-800 dark:text-green-400', border: 'border-green-200 dark:border-green-900' }
            };
            const priority = priorityConfig[task.priority];
            document.getElementById('viewModalPriority').textContent = task.priority;
            document.getElementById('viewModalPriority').className = `inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-bold uppercase border ${priority.color} ${priority.border}`;

            if (task.due_date) {
                const dueDate = new Date(task.due_date);
                document.getElementById('viewModalDueDate').innerHTML = `<i class="far fa-calendar-alt text-gray-400 dark:text-gray-500"></i>${dueDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}`;
            } else {
                document.getElementById('viewModalDueDate').innerHTML = '<span class="text-gray-400 dark:text-gray-500">No due date</span>';
            }

            if (task.assigned_to) {
                document.getElementById('viewModalAssignedTo').innerHTML = `
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                        ${task.assigned_to.name[0]}
                    </div>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">${task.assigned_to.name}</span>
                `;
            } else {
                document.getElementById('viewModalAssignedTo').innerHTML = '<span class="text-sm text-gray-400 dark:text-gray-500">Myself</span>';
            }

            if (task.estimated_hours || task.actual_hours) {
                document.getElementById('viewModalTimeTracking').classList.remove('hidden');
                document.getElementById('viewModalEstimatedHours').textContent = `${task.estimated_hours || 0}h`;
                document.getElementById('viewModalActualHours').textContent = `${task.actual_hours || 0}h`;
            } else {
                document.getElementById('viewModalTimeTracking').classList.add('hidden');
            }

            if (task.created_at) {
                const createdDate = new Date(task.created_at);
                document.getElementById('viewModalCreatedAt').textContent = createdDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            }

            const archiveBtn = document.getElementById('viewModalArchiveBtn');
            if (task.status === 'completed') {
                archiveBtn.classList.remove('hidden');
            } else {
                archiveBtn.classList.add('hidden');
            }

            window.currentViewTaskId = taskId;

            document.getElementById('viewModalEditBtn').onclick = function() {
                closeViewModal();
                openEditModal(taskId);
            };

            document.getElementById('viewTaskModal').classList.remove('hidden');
        })
        .catch(error => {
            showToast('Error!', 'Failed to load task details.', 'error');
        });
}

function closeViewModal() {
    document.getElementById('viewTaskModal').classList.add('hidden');
}

async function archiveTaskFromModal() {
    if (!window.currentViewTaskId) return;

    hideAlert();

    try {
        await axios.post(`/tasks/${window.currentViewTaskId}/archive`);

        tasksData = tasksData.filter(t => t.id !== window.currentViewTaskId);
        renderTasks();
        updateStats();
        closeViewModal();

        showToast('Archived!', 'Task has been archived successfully.', 'success');
    } catch (error) {
        showToast('Error!', 'Failed to archive task.', 'error');
    }
}

// Form submission
document.getElementById('taskForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    // Clear all previous errors
    clearValidationErrors();

    const taskId = document.getElementById('taskId').value;
    const assignedToValue = document.getElementById('taskAssignedTo').value;

    const data = {
        title: document.getElementById('taskTitle').value,
        description: document.getElementById('taskDescription').value,
        status: document.getElementById('taskStatus').value,
        priority: document.getElementById('taskPriority').value,
        due_date: document.getElementById('taskDueDate').value,
        order_id: document.getElementById('taskOrderId').value,
        estimated_hours: document.getElementById('taskEstimatedHours').value,
        actual_hours: document.getElementById('taskActualHours').value,
        _method: taskId ? 'PATCH' : 'POST'
    };

    // Only include assigned_to if a value is selected
    // Otherwise, let the backend default to the current user
    if (assignedToValue && assignedToValue !== '') {
        data.assigned_to = assignedToValue;
    }

    const url = taskId ? `/tasks/${taskId}` : '/tasks';

    try {
        const response = await axios.post(url, data);

        if (taskId) {
            // Update existing task
            const index = tasksData.findIndex(t => t.id === parseInt(taskId));
            if (index !== -1) {
                tasksData[index] = response.data.task;
            }
        } else {
            // Add new task
            tasksData.push(response.data.task);
        }

        renderTasks();
        updateStats();
        closeModal();

        showToast(
            'Success!',
            taskId ? 'Task updated successfully!' : 'Task created successfully!',
            'success'
        );
    } catch (error) {
        if (error.response && error.response.status === 422) {
            // Validation errors
            const errors = error.response.data.errors;
            showValidationErrors(errors);
        } else {
            showToast('Error!', 'Failed to save task.', 'error');
        }
    }
});

function clearValidationErrors() {
    const errorFields = ['taskTitleError', 'taskDescriptionError', 'taskStatusError', 'taskPriorityError', 'taskDueDateError', 'taskEstimatedHoursError', 'taskActualHoursError', 'taskOrderError', 'taskAssignedToError'];
    errorFields.forEach(fieldId => {
        const errorEl = document.getElementById(fieldId);
        if (errorEl) {
            errorEl.textContent = '';
            errorEl.classList.add('hidden');
        }
    });

    // Remove border-red-500 from all inputs
    const inputs = document.querySelectorAll('#taskForm input, #taskForm select, #taskForm textarea');
    inputs.forEach(input => {
        input.classList.remove('border-red-500');
        input.classList.add('border-gray-200');
    });
}

function showValidationErrors(errors) {
    const fieldMapping = {
        'title': { input: 'taskTitle', error: 'taskTitleError' },
        'description': { input: 'taskDescription', error: 'taskDescriptionError' },
        'status': { input: 'taskStatus', error: 'taskStatusError' },
        'priority': { input: 'taskPriority', error: 'taskPriorityError' },
        'due_date': { input: 'taskDueDate', error: 'taskDueDateError' },
        'estimated_hours': { input: 'taskEstimatedHours', error: 'taskEstimatedHoursError' },
        'actual_hours': { input: 'taskActualHours', error: 'taskActualHoursError' },
        'order_id': { input: 'taskOrderSearch', error: 'taskOrderError' },
        'assigned_to': { input: 'taskAssignedTo', error: 'taskAssignedToError' }
    };

    for (const field in errors) {
        const mapping = fieldMapping[field];
        if (!mapping) continue;

        const input = document.getElementById(mapping.input);
        const errorEl = document.getElementById(mapping.error);

        if (input && errorEl) {
            input.classList.remove('border-gray-200');
            input.classList.add('border-red-500');
            errorEl.textContent = errors[field][0];
            errorEl.classList.remove('hidden');
        }
    }

    showToast('Validation Error', 'Please fix the errors below.', 'error');
}

// Filter functions
function filterByStatus(status) {
    document.querySelectorAll('.filter-pill').forEach(pill => {
        pill.classList.remove('active');
        if (pill.dataset.filter === status) {
            pill.classList.add('active');
        }
    });

    // Filter visual only - we keep all data
    const allTasks = document.querySelectorAll('.task-card');
    allTasks.forEach(card => {
        if (status === 'all' || card.dataset.status === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function applyFilters() {
    const priority = document.getElementById('filterPriority').value;
    const order = document.getElementById('filterOrder').value;
    const search = document.getElementById('searchTasks').value.toLowerCase();

    tasksData.forEach(task => {
        const card = document.querySelector(`.task-card[data-task-id="${task.id}"]`);
        if (!card) return;

        let visible = true;

        if (priority && task.priority !== priority) visible = false;
        if (order && task.order_id != order) visible = false;
        if (search && !task.title.toLowerCase().includes(search)) visible = false;

        card.style.display = visible ? 'block' : 'none';
    });
}

// Search with debounce
let searchTimeout;
document.getElementById('searchTasks').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 300);
});

// Bulk actions
function toggleBulkMode() {
    bulkMode = !bulkMode;
    const btn = document.getElementById('bulkModeBtn');

    if (bulkMode) {
        btn.classList.add('bg-indigo-100', 'border-indigo-500');
        document.querySelectorAll('.task-checkbox').forEach(cb => {
            cb.style.display = 'block';
        });
    } else {
        btn.classList.remove('bg-indigo-100', 'border-indigo-500');
        document.querySelectorAll('.task-checkbox').forEach(cb => {
            cb.style.display = 'none';
            cb.checked = false;
        });
        clearSelection();
    }
}

function toggleTaskSelection(taskId) {
    if (selectedTasks.has(taskId)) {
        selectedTasks.delete(taskId);
    } else {
        selectedTasks.add(taskId);
    }
    updateBulkActionBar();
}

function updateBulkActionBar() {
    const bulkBar = document.getElementById('bulkActionBar');
    const selectedCount = document.getElementById('selectedCount');

    if (selectedTasks.size > 0) {
        bulkBar.classList.remove('hidden');
        selectedCount.textContent = selectedTasks.size;
    } else {
        bulkBar.classList.add('hidden');
    }
}

function clearSelection() {
    selectedTasks.clear();
    document.querySelectorAll('.task-checkbox').forEach(cb => cb.checked = false);
    updateBulkActionBar();
}

async function applyBulkAction() {
    const action = document.getElementById('bulkAction').value;

    if (!action || selectedTasks.size === 0) {
        return;
    }

    const statusMap = {
        'mark-pending': 'pending',
        'mark-in-progress': 'in-progress',
        'mark-completed': 'completed',
        'mark-on-hold': 'on-hold'
    };

    if (action === 'delete') {
        showAlert(
            'warning',
            'Delete Tasks?',
            `You are about to delete ${selectedTasks.size} task(s). This action cannot be undone.`,
            [
                {
                    text: 'Cancel',
                    class: 'secondary',
                    action: 'hideAlert()'
                },
                {
                    text: 'Delete',
                    class: 'danger',
                    action: 'confirmBulkAction()'
                }
            ]
        );
    } else {
        executeBulkAction(action, statusMap[action]);
    }
}

async function executeBulkAction(action, status) {
    const data = { task_ids: Array.from(selectedTasks) };
    if (status) {
        data.status = status;
    }

    try {
        await axios.post('/tasks/bulk-update', data);

        // Update local state
        if (action === 'delete') {
            tasksData = tasksData.filter(t => !selectedTasks.has(t.id));
        } else {
            tasksData.forEach(task => {
                if (selectedTasks.has(task.id)) {
                    task.status = status;
                    if (status === 'completed') {
                        task.completed_at = new Date().toISOString();
                    }
                }
            });
        }

        renderTasks();
        updateStats();
        clearSelection();

        showToast('Success!', 'Bulk action completed successfully!', 'success');
    } catch (error) {
        showToast('Error!', 'Failed to apply bulk action.', 'error');
    }
}

async function confirmBulkAction() {
    hideAlert();
    await executeBulkAction('delete');
}

// ── Modal Order AJAX Picker ──────────────────────────────────────────────────
(function () {
    const searchInput  = document.getElementById('taskOrderSearch');
    const hiddenInput  = document.getElementById('taskOrderId');
    const dropdown     = document.getElementById('taskOrderDropdown');
    const clearBtn     = document.getElementById('clearTaskOrder');
    const searchUrl    = '{{ route('tasks.orders-search') }}';

    let debounceTimer  = null;
    let cachedLatest   = null;

    const statusClass = {
        'nra':         'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300',
        'pending':     'bg-yellow-100 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400',
        'in-progress': 'bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400',
        'delivered':   'bg-green-100 dark:bg-green-500/10 text-green-700 dark:text-green-400',
        'on-hold':     'bg-orange-100 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400',
        'cancelled':   'bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400',
    };

    function getStatusClass(status) {
        return statusClass[status] || 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300';
    }

    function renderOptions(orders) {
        if (!orders.length) {
            dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-400 dark:text-gray-500">No orders found</div>';
        } else {
            dropdown.innerHTML = orders.map(order => `
                <button type="button"
                        data-id="${order.id}"
                        data-name="${order.name.replace(/"/g, '&quot;')}"
                        data-status="${order.status || ''}"
                        class="order-picker-option w-full text-left px-4 py-2.5 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 text-gray-900 dark:text-gray-100 text-sm border-b border-gray-100 dark:border-gray-700 last:border-0 flex items-center justify-between gap-2 transition-colors">
                    <span class="truncate">${order.name}</span>
                    <span class="flex-shrink-0 text-xs font-medium px-1.5 py-0.5 rounded ${getStatusClass(order.status)}">${order.status || 'N/A'}</span>
                </button>
            `).join('');

            dropdown.querySelectorAll('.order-picker-option').forEach(btn => {
                btn.addEventListener('click', function () {
                    setOrderPicker(this.dataset.id, this.dataset.name, this.dataset.status);
                });
            });
        }
        dropdown.classList.remove('hidden');
    }

    function fetchAndRender(q) {
        const url = q ? `${searchUrl}?q=${encodeURIComponent(q)}` : searchUrl;
        fetch(url).then(r => r.json()).then(renderOptions);
    }

    searchInput.addEventListener('focus', function () {
        if (cachedLatest) {
            renderOptions(cachedLatest);
        } else {
            fetch(searchUrl)
                .then(r => r.json())
                .then(data => { cachedLatest = data; renderOptions(data); });
        }
    });

    searchInput.addEventListener('input', function () {
        const q = this.value.trim();
        clearTimeout(debounceTimer);
        if (!q) {
            renderOptions(cachedLatest || []);
            return;
        }
        debounceTimer = setTimeout(() => fetchAndRender(q), 300);
    });

    clearBtn.addEventListener('click', () => resetOrderPicker());

    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target) && !clearBtn.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
})();

window.setOrderPicker = function (id, name, status) {
    document.getElementById('taskOrderId').value = id;
    document.getElementById('taskOrderSearch').value = name;
    document.getElementById('taskOrderSearch').classList.add('border-indigo-400', 'bg-indigo-50');
    document.getElementById('clearTaskOrder').classList.remove('hidden');
    document.getElementById('taskOrderDropdown').classList.add('hidden');
};

window.resetOrderPicker = function () {
    document.getElementById('taskOrderId').value = '';
    document.getElementById('taskOrderSearch').value = '';
    document.getElementById('taskOrderSearch').classList.remove('border-indigo-400', 'bg-indigo-50');
    document.getElementById('clearTaskOrder').classList.add('hidden');
    document.getElementById('taskOrderDropdown').classList.add('hidden');
};
</script>
@endpush
