<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('client_key')->nullable()->after('name');
            $table->index(['freelance_profile_id', 'client_key']);
        });

        // Backfill existing rows so repeat-order detection covers order
        // history, not just orders saved after this migration.
        DB::table('orders')->orderBy('id')->chunk(200, function ($orders) {
            foreach ($orders as $order) {
                DB::table('orders')
                    ->where('id', $order->id)
                    ->update(['client_key' => Str::of($order->name)->trim()->lower()->value()]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['freelance_profile_id', 'client_key']);
            $table->dropColumn('client_key');
        });
    }
};
