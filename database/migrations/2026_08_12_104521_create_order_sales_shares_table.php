<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_sales_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // The slice of the order amount credited to this sales person.
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['order_id', 'user_id']);
        });

        // Existing sales orders are credited wholly to the person who opened them.
        DB::table('orders')
            ->whereNotNull('sales_user_id')
            ->orderBy('id')
            ->each(function ($order) {
                DB::table('order_sales_shares')->insert([
                    'order_id' => $order->id,
                    'user_id' => $order->sales_user_id,
                    'amount' => $order->value ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_sales_shares');
    }
};
