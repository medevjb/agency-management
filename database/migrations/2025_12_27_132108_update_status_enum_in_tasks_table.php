<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status VARCHAR(20) DEFAULT 'pending'");
        }

        DB::statement("UPDATE tasks SET status = 'pending' WHERE status = 'todo'");
        DB::statement("UPDATE tasks SET status = 'on-hold' WHERE status = 'review'");

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending', 'in-progress', 'on-hold', 'completed') DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status VARCHAR(20) DEFAULT 'todo'");
        }

        DB::statement("UPDATE tasks SET status = 'todo' WHERE status = 'pending'");
        DB::statement("UPDATE tasks SET status = 'review' WHERE status = 'on-hold'");

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('todo', 'in-progress', 'review', 'completed') DEFAULT 'todo'");
        }
    }
};
