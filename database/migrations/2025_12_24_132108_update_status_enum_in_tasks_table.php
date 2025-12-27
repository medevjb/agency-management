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
        // Step 1: Convert enum to varchar first (to allow any value)
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status VARCHAR(20) DEFAULT 'pending'");

        // Step 2: Update existing data to use new enum values
        DB::statement("UPDATE tasks SET status = 'pending' WHERE status = 'todo'");
        DB::statement("UPDATE tasks SET status = 'on-hold' WHERE status = 'review'");

        // Step 3: Convert back to enum with new values
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending', 'in-progress', 'on-hold', 'completed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Convert to varchar first
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status VARCHAR(20) DEFAULT 'todo'");

        // Step 2: Update data back to old values
        DB::statement("UPDATE tasks SET status = 'todo' WHERE status = 'pending'");
        DB::statement("UPDATE tasks SET status = 'review' WHERE status = 'on-hold'");

        // Step 3: Convert back to old enum
        DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('todo', 'in-progress', 'review', 'completed') DEFAULT 'todo'");
    }
};
