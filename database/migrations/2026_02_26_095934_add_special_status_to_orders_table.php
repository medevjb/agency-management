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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('special_status', ['selected_for_delivery', 'continue_for_next', 'stuck_order'])
                ->nullable()
                ->after('order_sheet_url')
                ->comment('Special status for order management: Selected for delivery, Continue for next, Stuck order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('special_status');
        });
    }
};
