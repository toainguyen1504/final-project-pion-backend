<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('momo_order_id')->nullable()->after('order_number');
            $table->string('momo_request_id')->nullable()->after('momo_order_id');
            $table->string('momo_trans_id')->nullable()->after('momo_request_id');

            $table->timestamp('paid_at')->nullable()->after('ordered_at');
            $table->timestamp('expired_at')->nullable()->after('paid_at');

            $table->json('payment_payload')->nullable()->after('expired_at');
            $table->json('payment_result')->nullable()->after('payment_payload');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'momo_order_id',
                'momo_request_id',
                'momo_trans_id',
                'paid_at',
                'expired_at',
                'payment_payload',
                'payment_result',
            ]);
        });
    }
};
