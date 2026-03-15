<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number')->unique();

            $table->enum('status', ['pending', 'paid', 'failed'])
                ->default('pending');

            $table->enum('payment_method', ['bank', 'vnpay', 'momo'])
                ->default('bank');

            $table->enum('payment_status', ['pending', 'paid', 'failed'])
                ->default('pending');

            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_amount', 10, 2)->default(0);

            $table->timestamp('ordered_at')->nullable();

            $table->foreignId('payer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
