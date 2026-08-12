<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('description');
            }

            if (! Schema::hasColumn('orders', 'credentials')) {
                $table->text('credentials')->nullable()->after('notes');
            }

            if (! Schema::hasColumn('orders', 'client_documents')) {
                $table->json('client_documents')->nullable()->after('credentials');
            }

            if (! Schema::hasColumn('orders', 'progress')) {
                $table->unsignedTinyInteger('progress')->default(0)->after('status');
            }
        });

        // Keep existing sheet links by folding them into the new notes field.
        if (Schema::hasColumn('orders', 'order_sheet_url')) {
            DB::table('orders')->whereNotNull('order_sheet_url')->orderBy('id')->each(function ($order) {
                DB::table('orders')->where('id', $order->id)->update([
                    'notes' => trim(($order->notes ?? '')."\n\nOrder sheet: ".$order->order_sheet_url),
                ]);
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'order_sheet_url')) {
                $table->dropColumn('order_sheet_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'order_sheet_url')) {
                $table->string('order_sheet_url')->nullable()->after('freelance_profile_id');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['notes', 'credentials', 'client_documents', 'progress']);
        });
    }
};
