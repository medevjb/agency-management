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
        Schema::create('github_repositories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('github_account_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('name');
            $table->string('html_url')->nullable();
            $table->string('default_branch')->nullable();
            $table->timestamps();

            $table->unique(['github_account_id', 'full_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('github_repositories');
    }
};
