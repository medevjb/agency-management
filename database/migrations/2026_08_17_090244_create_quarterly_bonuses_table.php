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
        Schema::create('quarterly_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salesperson_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('quarter');
            $table->decimal('quarter_sales', 14, 2);
            $table->decimal('quarter_target', 14, 2);
            $table->decimal('qualified_extra_sales', 14, 2);
            $table->decimal('bonus_percentage_applied', 8, 6);
            $table->string('bonus_source_currency', 3)->default('USD');
            $table->decimal('bonus_source_amount', 14, 4);
            $table->decimal('exchange_rate_applied', 12, 4);
            $table->string('payment_currency', 3)->default('BDT');
            $table->decimal('bonus_payment_amount', 16, 2);
            $table->string('status')->default('ACCRUING');
            $table->timestamp('calculated_at')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['salesperson_id', 'year', 'quarter']);
            $table->index(['status', 'year', 'quarter']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quarterly_bonuses');
    }
};
