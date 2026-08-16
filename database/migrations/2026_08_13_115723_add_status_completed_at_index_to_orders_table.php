<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Every month-scoped "completed orders" query in the app — Value
     * Distribution most of all, but also the dashboards — filters on
     * status plus a completed_at range and sorts by completed_at. Neither
     * column had an index, so every one of those queries was a full table
     * scan; pagination bounds what gets rendered, not what the database
     * has to read to find it. This index lets the database seek straight
     * to the matching rows instead, no matter how many orders pile up.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index(['status', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status', 'completed_at']);
        });
    }
};
