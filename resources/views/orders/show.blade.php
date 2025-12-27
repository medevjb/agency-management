@extends('layouts.dashboard')

@section('title', 'Order Details')

@push('styles')
<style>
    .detail-card {
        background-color: white;
        border-radius: 1rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .detail-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .detail-card-header {
        padding: 2rem;
        border-bottom: 1px solid #f3f4f6;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .detail-card-header h2 {
        color: white;
        margin: 0;
    }

    .detail-card-header i {
        background: rgba(255, 255, 255, 0.2);
    }

    .detail-card-body {
        padding: 2rem;
    }

    .detail-section {
        margin-bottom: 2rem;
    }

    .detail-section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f3f4f6;
    }

    .detail-section-title i {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 2rem;
    }

    @media (min-width: 768px) {
        .info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1280px) {
        .info-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        padding: 1rem;
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .info-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border-color: #d1d5db;
    }

    .info-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
        display: flex;
        align-items: center;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .info-label i {
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.5rem;
        font-size: 0.75rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .info-value {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.75rem 1.5rem;
        border-radius: 9999px;
        font-size: 1rem;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .user-card {
        display: flex;
        align-items: center;
        padding: 1.25rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 1rem;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .user-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 12px rgba(102, 126, 234, 0.2);
    }

    .user-card .w-12 {
        background: rgba(255, 255, 255, 0.3);
        border: 2px solid rgba(255, 255, 255, 0.5);
    }

    .user-card p {
        color: white !important;
    }

    .history-timeline {
        position: relative;
    }

    .history-item {
        position: relative;
        padding-left: 3rem;
        padding-bottom: 2rem;
        border-left: 3px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .history-item:hover {
        border-left-color: #667eea;
    }

    .history-item:last-child {
        border-left: 0;
        padding-bottom: 0;
    }

    .history-dot {
        position: absolute;
        left: -0.375rem;
        top: 0;
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 9999px;
        border: 3px solid white;
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.5), 0 2px 8px rgba(0, 0, 0, 0.15);
        z-index: 10;
    }

    .history-content {
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
        border-radius: 1rem;
        padding: 1.25rem;
        border: 1px solid #e5e7eb;
        margin-left: 1rem;
        transition: all 0.3s ease;
    }

    .history-item:hover .history-content {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #667eea;
    }

    .timeline-actions {
        margin-top: 2rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .stat-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
        padding: 1.5rem;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -4px rgba(0, 0, 0, 0.15);
    }

    .action-btn {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .action-btn:hover::before {
        left: 100%;
    }

    .status-badge {
        position: relative;
        overflow: hidden;
    }

    .status-badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }

    .status-badge:hover::before {
        left: 100%;
    }

    .gradient-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .empty-state {
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
        border: 2px dashed #e5e7eb;
        transition: all 0.3s ease;
    }

    .empty-state:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
    }
</style>
@endpush

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-file-invoice text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $order->name }}</h1>
                        <p class="mt-1 text-sm text-gray-600">Order #{{ $order->id }} • Created on {{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('orders.edit', $order) }}" class="action-btn inline-flex items-center px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-semibold transition-all shadow-sm">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Order
                </a>
                <a href="{{ route('orders.index') }}" class="action-btn inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all shadow-lg">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Orders
                </a>
            </div>
        </div>
    </div>

    <!-- Status & Priority Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2 font-semibold uppercase tracking-wide">Status</p>
                    @php
                        $statusConfig = [
                            'nra' => ['icon' => '🤷‍♀️', 'color' => 'bg-gray-100 text-gray-800', 'label' => 'NRA'],
                            'pending' => ['icon' => '⏳', 'color' => 'bg-yellow-100 text-yellow-800', 'label' => 'Pending'],
                            'in-progress' => ['icon' => '🔄', 'color' => 'bg-blue-100 text-blue-800', 'label' => 'In Progress'],
                            'completed' => ['icon' => '✅', 'color' => 'bg-green-100 text-green-800', 'label' => 'Completed'],
                            'on-hold' => ['icon' => '⏸️', 'color' => 'bg-orange-100 text-orange-800', 'label' => 'On Hold'],
                            'cancelled' => ['icon' => '❌', 'color' => 'bg-red-100 text-red-800', 'label' => 'Cancelled'],
                        ];
                        $status = $statusConfig[$order->status] ?? $statusConfig['nra'];
                    @endphp
                    <span class="status-badge {{ $status['color'] }}">
                        {{ $status['icon'] }} {{ $status['label'] }}
                    </span>
                </div>
                <div class="bg-gradient-to-br from-purple-100 to-indigo-100 p-3 rounded-xl">
                    <i class="fas fa-tasks text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2 font-semibold uppercase tracking-wide">Priority</p>
                    @php
                        $priorityConfig = [
                            'low' => ['icon' => '🟢', 'color' => 'bg-green-100 text-green-800', 'label' => 'Low'],
                            'medium' => ['icon' => '🟡', 'color' => 'bg-yellow-100 text-yellow-800', 'label' => 'Medium'],
                            'high' => ['icon' => '🔴', 'color' => 'bg-red-100 text-red-800', 'label' => 'High'],
                        ];
                        $priority = $priorityConfig[$order->priority] ?? $priorityConfig['medium'];
                    @endphp
                    <span class="status-badge {{ $priority['color'] }}">
                        {{ $priority['icon'] }} {{ $priority['label'] }}
                    </span>
                </div>
                <div class="bg-gradient-to-br from-orange-100 to-red-100 p-3 rounded-xl">
                    <i class="fas fa-flag text-2xl text-orange-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2 font-semibold uppercase tracking-wide">Value</p>
                    <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">${{ number_format($order->value ?: ($order->actual_revenue / 0.80), 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Before 20% split</p>
                </div>
                <div class="bg-gradient-to-br from-blue-100 to-indigo-100 p-3 rounded-xl">
                    <i class="fas fa-coins text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2 font-semibold uppercase tracking-wide">Amount</p>
                    <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">${{ number_format($order->actual_revenue, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">After 20% split</p>
                </div>
                <div class="bg-gradient-to-br from-green-100 to-emerald-100 p-3 rounded-xl">
                    <i class="fas fa-dollar-sign text-2xl text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details -->
    <div class="detail-card mb-6">
        <div class="detail-card-header">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-info-circle bg-gradient-to-br from-purple-500 to-indigo-600 text-white"></i>
                Order Information
            </h2>
        </div>
        <div class="detail-card-body">
            <div class="info-grid">
                <div class="info-item">
                    <p class="info-label">
                        <i class="fas fa-hashtag bg-purple-100 text-purple-600"></i>
                        Custom Order ID
                    </p>
                    <p class="info-value">{{ $order->order_id ?: 'Not set' }}</p>
                </div>

                <div class="info-item">
                    <p class="info-label">
                        <i class="fas fa-database bg-indigo-100 text-indigo-600"></i>
                        System ID
                    </p>
                    <p class="info-value">#{{ $order->id }}</p>
                </div>

                <div class="info-item">
                    <p class="info-label">
                        <i class="fas fa-file-alt bg-blue-100 text-blue-600"></i>
                        Name
                    </p>
                    <p class="info-value">{{ $order->name }}</p>
                </div>

                <div class="info-item">
                    <p class="info-label">
                        <i class="fas fa-calendar bg-green-100 text-green-600"></i>
                        Created At
                    </p>
                    <p class="info-value">{{ $order->created_at->format('M d, Y g:i A') }}</p>
                </div>

                <div class="info-item">
                    <p class="info-label">
                        <i class="fas fa-play-circle bg-green-100 text-green-600"></i>
                        Start Date
                    </p>
                    <p class="info-value">{{ $order->start_date ? $order->start_date->format('M d, Y') : 'Not set' }}</p>
                </div>

                <div class="info-item">
                    <p class="info-label">
                        <i class="fas fa-flag-checkered bg-red-100 text-red-600"></i>
                        Deadline
                    </p>
                    <p class="info-value">{{ $order->end_date ? $order->end_date->format('M d, Y') : 'Not set' }}</p>
                </div>

                <div class="info-item">
                    <p class="info-label">
                        <i class="fas fa-building bg-indigo-100 text-indigo-600"></i>
                        Team
                    </p>
                    <p class="info-value">{{ $order->team ? $order->team->name : 'No team assigned' }}</p>
                </div>

                <div class="info-item">
                    <p class="info-label">
                        <i class="fas fa-layer-group bg-cyan-100 text-cyan-600"></i>
                        Tech Stack
                    </p>
                    <p class="info-value">{{ $order->techStack ? $order->techStack->name : 'No tech stack assigned' }}</p>
                </div>
            </div>

            @if($order->description)
            <div class="mt-6">
                <p class="info-label mb-3">
                    <i class="fas fa-align-left bg-purple-100 text-purple-600"></i>
                    Description
                </p>
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-5 border-2 border-gray-200 hover:border-purple-300 transition-all">
                    <p class="text-gray-700 leading-relaxed">{{ $order->description }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Assigned Users -->
    @if($order->users->count() > 0)
    <div class="detail-card mb-6">
        <div class="detail-card-header">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-users bg-gradient-to-br from-indigo-500 to-purple-600 text-white"></i>
                Assigned Team Members ({{ $order->users->count() }})
            </h2>
        </div>
        <div class="detail-card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($order->users as $user)
                    <div class="user-card">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="empty-state rounded-xl p-8 mb-6 text-center">
        <i class="fas fa-users-slash text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500 font-semibold">No team members assigned</p>
    </div>
    @endif

    <!-- Status History -->
    @if($order->statusHistory->count() > 0)
    <div class="detail-card mb-6">
        <div class="detail-card-header">
            <h2 class="text-xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-history bg-gradient-to-br from-blue-500 to-cyan-600 text-white"></i>
                Status History
            </h2>
        </div>
        <div class="detail-card-body">
            <div class="history-timeline">
                @foreach($order->statusHistory as $index => $history)
                    <div class="history-item">
                        @php
                            $historyStatusConfig = [
                                'nra' => ['icon' => '🤷‍♀️', 'color' => 'bg-gray-100 text-gray-800'],
                                'pending' => ['icon' => '⏳', 'color' => 'bg-yellow-100 text-yellow-800'],
                                'in-progress' => ['icon' => '🔄', 'color' => 'bg-blue-100 text-blue-800'],
                                'completed' => ['icon' => '✅', 'color' => 'bg-green-100 text-green-800'],
                                'on-hold' => ['icon' => '⏸️', 'color' => 'bg-orange-100 text-orange-800'],
                                'cancelled' => ['icon' => '❌', 'color' => 'bg-red-100 text-red-800'],
                            ];
                            $hStatus = $historyStatusConfig[$history->status] ?? $historyStatusConfig['nra'];
                        @endphp
                        <div class="history-dot {{ $hStatus['color'] }}"></div>
                        <div class="history-content">
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $hStatus['color'] }}">
                                    {{ $hStatus['icon'] }} {{ ucfirst(str_replace('-', ' ', $history->status)) }}
                                </span>
                                <span class="text-sm text-gray-500">{{ $history->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                            @if($history->changedByUser)
                            <p class="text-sm text-gray-600 mb-1">
                                <i class="fas fa-user mr-1 text-purple-600"></i>
                                Changed by: <span class="font-semibold text-gray-900">{{ $history->changedByUser->name }}</span>
                            </p>
                            @endif
                            @if($history->actual_revenue)
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-dollar-sign mr-1 text-green-600"></i>
                                Revenue: <span class="font-bold text-green-600">${{ number_format($history->actual_revenue, 2) }}</span>
                            </p>
                            @endif
                            @if($history->notes)
                            <div class="mt-2 p-2 bg-white rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-sticky-note mr-1 text-yellow-600"></i>
                                    <span class="italic">{{ $history->notes }}</span>
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Timeline Actions -->
    <div class="timeline-actions">
        <a href="{{ route('orders.edit', $order) }}" class="action-btn inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold shadow-lg">
            <i class="fas fa-edit mr-2"></i>
            Edit Order
        </a>
        <button onclick="deleteOrder({{ $order->id }})" class="action-btn inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-xl font-semibold shadow-lg">
            <i class="fas fa-trash mr-2"></i>
            Delete Order
        </button>
        <a href="{{ route('orders.index') }}" class="action-btn inline-flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-semibold shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Orders
        </a>
    </div>
</div>

@push('scripts')
<script>
function deleteOrder(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();

            axios.delete('/orders/' + id)
            .then(response => {
                Swal.fire({
                    title: 'Deleted!',
                    text: response.data.message || 'Order has been deleted.',
                    icon: 'success',
                    confirmButtonColor: '#9333ea'
                }).then(() => {
                    window.location.href = '{{ route('orders.index') }}';
                });
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: error.response?.data?.message || 'Something went wrong.',
                    icon: 'error',
                    confirmButtonColor: '#dc2626'
                });
            });
        }
    });
}
</script>
@endpush
@endsection
