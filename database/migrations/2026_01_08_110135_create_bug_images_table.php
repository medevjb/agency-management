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
        Schema::create('bug_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bug_report_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->string('image_name');
            $table->unsignedInteger('size')->nullable();
            $table->string('mime_type')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bug_images');
    }
};
