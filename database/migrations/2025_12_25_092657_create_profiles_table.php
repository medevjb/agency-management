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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('profile_name');
            $table->string('username')->unique();
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('skills')->nullable();
            $table->string('hourly_rate')->nullable();
            $table->enum('status', ['active', 'inactive', 'busy', 'away'])->default('active');
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->integer('rating')->default(0);
            $table->integer('total_jobs')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
