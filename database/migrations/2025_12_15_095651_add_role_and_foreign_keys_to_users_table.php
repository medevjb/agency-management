<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // The default role is now in kebab-case
            $table->string('role')->default('team-member')->after('password');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('tech_stack_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropForeign(['tech_stack_id']);
            $table->dropColumn(['role', 'team_id', 'tech_stack_id']);
        });
    }
};
