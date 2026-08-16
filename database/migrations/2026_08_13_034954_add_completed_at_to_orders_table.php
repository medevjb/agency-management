<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // When status became 'completed' — drives the value distribution
            // trigger and its monthly grouping, separately from delivered_at.
            $table->timestamp('completed_at')->nullable()->after('delivered_at');
            // When the value distribution for this order last balanced to 100%.
            $table->timestamp('value_distributed_at')->nullable()->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['completed_at', 'value_distributed_at']);
        });
    }
};
