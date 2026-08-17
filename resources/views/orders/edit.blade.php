@extends('layouts.dashboard')

@section('title', 'Edit Order')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-6 max-w-5xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-edit text-white"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Order</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Update order information</p>
                </div>
            </div>
            <a href="{{ route('orders.index') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 text-sm font-medium transition-all">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

    <form action="{{ route('orders.update', $order) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="space-y-6">
            @include('orders.partials.form', ['order' => $order, 'salesPeople' => $salesPeople])

            <!-- Form Actions -->
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-800 px-6 py-4 flex items-center justify-end gap-3">
                <a href="{{ route('orders.show', $order) }}"
                   class="px-5 py-2.5 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium transition-all">
                    View Order
                </a>
                <a href="{{ route('orders.index') }}"
                   class="px-5 py-2.5 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-sm font-medium transition-all">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg shadow-md hover:shadow-lg text-sm font-semibold transition-all">
                    <i class="fas fa-save mr-2"></i>
                    Update Order
                </button>
            </div>
        </div>
    </form>
</div>
@endsection