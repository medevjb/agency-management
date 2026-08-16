<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indexes for the hot query paths at scale. Every column here is filtered
     * or sorted by the dashboards, the order board, the sales pages, or the
     * hourly maintenance commands — and none of them had an index, forcing a
     * full table scan on every request. The `(status, completed_at)` index on
     * orders already exists (see 2026_08_13_115723); these extend the same
     * idea to the remaining hot columns and to the share/pivot tables whose
     * unique keys only cover the "other" direction of lookup.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'completed_at', 'value_distributed_at'], 'orders_status_completed_at_value_dist_index');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('delivered_at');
            $table->index('priority');
            $table->index('created_at');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index(['status', 'completed_at']);
            $table->index('due_date');
            $table->index('priority');
        });

        Schema::table('bug_reports', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('portfolios', function (Blueprint $table) {
            $table->index('status');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['status']);
        });

        Schema::table('profiles', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('bug_reports', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['priority']);
            $table->dropIndex(['due_date']);
            $table->dropIndex(['status', 'completed_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_completed_at_value_dist_index');
            $table->dropIndex(['start_date']);
            $table->dropIndex(['end_date']);
            $table->dropIndex(['delivered_at']);
            $table->dropIndex(['priority']);
            $table->dropIndex(['created_at']);
        });
    }
};
