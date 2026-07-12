<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending', 'in-progress', 'on-hold', 'completed', 'archived')");
            }

            $table->timestamp('completed_at')->nullable()->after('due_date');
            $table->timestamp('archived_at')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['completed_at', 'archived_at']);

            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE tasks MODIFY COLUMN status ENUM('pending', 'in-progress', 'on-hold', 'completed')");
            }
        });
    }
};
