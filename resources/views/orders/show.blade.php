@extends('layouts.dashboard')

@section('title', 'Order Details')

@push('styles')
<style>
    .detail-card {
        @apply bg-white rounded-2xl shadow-lg overflow-hidden;
    }

    .detail-card-header {
        @apply px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white;
    }

    .detail-card-body {
        @apply p-8;
    }

    .detail-section {
        @apply mb-8;
    }

    .detail-section-title {
        @apply text-lg font-bold text-gray-900 mb-4 flex items-center;
    }

    .detail-section-title i {
        @apply w-10 h-10 rounded-xl flex items-center justify-center mr-3 shadow-sm;
    }

    .info-grid {
        @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6;
    }

    .info-item {
        @apply space-y-1;
    }

    .info-label {
        @apply text-sm font-semibold text-gray-500 flex items-center;
    }

    .info-label i {
        @apply w-5 h-5 rounded-lg flex items-center justify-center mr-2 text-xs;
    }

    .info-value {
        @apply text-base font-medium text-gray-900;
    }

    .status-badge {
        @apply inline-flex items-center px-4 py-2 rounded-full text-sm font-bold;
    }

    .user-card {
        @apply flex items-center p-4 bg-gray-50 rounded-xl border border-gray-200;
    }

    .history-timeline {
        @apply relative;
    }

    .history-item {
        @apply relative pl-8 pb-8 border-l-2 border-gray-200;
    }

    .history-item:last-child {
        @apply border-l-0 pb-0;
    }

    .history-dot {
        @apply absolute left-0 top-0 w-4 h-4 rounded-full border-2 border-white shadow;
    }

    .timeline-actions {
        @apply mt-6 flex flex-wrap gap-3;
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
                <a href="{{ route('orders.edit', $order) }}" class="inline-flex items-center px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-semibold transition-all">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Order
                </a>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all shadow-lg">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Orders
                </a>
            </div>
        </div>
    </div>

    <!-- Status & Priority Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Status</p>
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
                <div class="bg-gray-100 p-3 rounded-xl">
                    <i class="fas fa-tasks text-2xl text-gray-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Priority</p>
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
                <div class="bg-gray-100 p-3 rounded-xl">
                    <i class="fas fa-flag text-2xl text-gray-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Revenue</p>
                    <p class="text-3xl font-bold text-green-600">${{ number_format($order->actual_revenue, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">After 20% split</p>
                </div>
                <div class="bg-green-100 p-3 rounded-xl">
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
                        Order ID
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
                <p class="info-label mb-2">
                    <i class="fas fa-align-left bg-purple-100 text-purple-600"></i>
                    Description
                </p>
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <p class="text-gray-700">{{ $order->description }}</p>
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
    <div class="bg-white rounded-xl shadow-md border border-gray-100 p-8 mb-6 text-center">
        <i class="fas fa-users-slash text-4xl text-gray-300 mb-3"></i>
        <p class="text-gray-500">No team members assigned</p>
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
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 ml-2">
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $hStatus['color'] }}">
                                    {{ $hStatus['icon'] }} {{ ucfirst(str_replace('-', ' ', $history->status)) }}
                                </span>
                                <span class="text-sm text-gray-500">{{ $history->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                            @if($history->changedByUser)
                            <p class="text-sm text-gray-600 mb-1">
                                <i class="fas fa-user mr-1"></i>
                                Changed by: <span class="font-medium">{{ $history->changedByUser->name }}</span>
                            </p>
                            @endif
                            @if($history->actual_revenue)
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-dollar-sign mr-1"></i>
                                Revenue: <span class="font-semibold text-green-600">${{ number_format($history->actual_revenue, 2) }}</span>
                            </p>
                            @endif
                            @if($history->notes)
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-sticky-note mr-1"></i>
                                <span class="italic">{{ $history->notes }}</span>
                            </p>
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
        <a href="{{ route('orders.edit', $order) }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all shadow-lg">
            <i class="fas fa-edit mr-2"></i>
            Edit Order
        </a>
        <button onclick="deleteOrder({{ $order->id }})" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition-all shadow-lg">
            <i class="fas fa-trash mr-2"></i>
            Delete Order
        </button>
        <a href="{{ route('orders.index') }}" class="inline-flex items-center px-6 py-3 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-semibold transition-all">
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

            axios.delete('/orders/' + id, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
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
