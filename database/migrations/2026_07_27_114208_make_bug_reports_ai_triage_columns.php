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
        Schema::table('bug_reports', function (Blueprint $table) {
            $table->dropForeign(['bug_category_id']);
            $table->dropForeign(['bug_priority_id']);
        });

        Schema::table('bug_reports', function (Blueprint $table) {
            $table->foreignId('bug_category_id')->nullable()->change();
            $table->foreignId('bug_priority_id')->nullable()->change();
        });

        Schema::table('bug_reports', function (Blueprint $table) {
            $table->foreign('bug_category_id')->references('id')->on('bug_categories')->onDelete('cascade');
            $table->foreign('bug_priority_id')->references('id')->on('bug_priorities')->onDelete('cascade');
            $table->text('ai_triage_reasoning')->nullable()->after('bug_priority_id');
            $table->unsignedTinyInteger('ai_triage_confidence')->nullable()->after('ai_triage_reasoning');
            $table->timestamp('ai_triaged_at')->nullable()->after('ai_triage_confidence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bug_reports', function (Blueprint $table) {
            $table->dropColumn(['ai_triage_reasoning', 'ai_triage_confidence', 'ai_triaged_at']);
            $table->dropForeign(['bug_category_id']);
            $table->dropForeign(['bug_priority_id']);
        });

        Schema::table('bug_reports', function (Blueprint $table) {
            $table->foreignId('bug_category_id')->nullable(false)->change();
            $table->foreignId('bug_priority_id')->nullable(false)->change();
        });

        Schema::table('bug_reports', function (Blueprint $table) {
            $table->foreign('bug_category_id')->references('id')->on('bug_categories')->onDelete('cascade');
            $table->foreign('bug_priority_id')->references('id')->on('bug_priorities')->onDelete('cascade');
        });
    }
};
