<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all 'completed' status to 'delivered' in orders table
        DB::statement("UPDATE orders SET status = 'delivered' WHERE status = 'completed'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back: change 'delivered' to 'completed'
        DB::statement("UPDATE orders SET status = 'completed' WHERE status = 'delivered'");
    }
};
