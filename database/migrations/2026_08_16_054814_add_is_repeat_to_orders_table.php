<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_repeat')->default(false)->after('status');
        });

        $this->backfillWithTheOldAutomaticRule();
    }

    /**
     * Fresh/Repeat used to be computed on the fly: an order was a repeat when
     * an earlier order (created_at, tie-broken by id) existed for the same
     * profile and client_key. One-time backfill keeps that history before the
     * flag becomes a purely manual choice made at order entry.
     */
    private function backfillWithTheOldAutomaticRule(): void
    {
        $seen = [];

        DB::table('orders')
            ->select(['id', 'freelance_profile_id', 'client_key', 'created_at'])
            ->whereNull('deleted_at')
            ->orderBy('freelance_profile_id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->lazy()
            ->each(function (object $order) use (&$seen) {
                $key = $order->freelance_profile_id.'|'.$order->client_key;
                $isRepeat = filled($order->client_key) && isset($seen[$key]);

                if (filled($order->client_key)) {
                    $seen[$key] = true;
                }

                if ($isRepeat) {
                    DB::table('orders')->where('id', $order->id)->update(['is_repeat' => true]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_repeat');
        });
    }
};
