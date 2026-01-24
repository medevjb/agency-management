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
            // Add missing columns if they don't exist
            if (! Schema::hasColumn('orders', 'order_id')) {
                $table->string('order_id')->nullable()->after('id');
            }
            if (! Schema::hasColumn('orders', 'priority')) {
                $table->string('priority')->default('medium')->after('status');
            }
            if (! Schema::hasColumn('orders', 'value')) {
                $table->decimal('value', 15, 2)->nullable()->after('priority');
            }
            if (! Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('actual_revenue');
            }
            if (! Schema::hasColumn('orders', 'freelance_profile_id')) {
                $table->foreignId('freelance_profile_id')->nullable()->constrained('profiles')->nullOnDelete()->after('tech_stack_id');
            }
            if (! Schema::hasColumn('orders', 'order_sheet_url')) {
                $table->string('order_sheet_url')->nullable()->after('freelance_profile_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['freelance_profile_id']);
            $table->dropColumn(['order_id', 'priority', 'value', 'delivered_at', 'freelance_profile_id', 'order_sheet_url']);
        });
    }
};
