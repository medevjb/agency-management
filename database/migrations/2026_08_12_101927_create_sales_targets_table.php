<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Always the first day of the month the target applies from.
            $table->date('month');
            $table->decimal('amount', 12, 2);
            $table->foreignId('set_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'month']);
            $table->index('month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_targets');
    }
};
