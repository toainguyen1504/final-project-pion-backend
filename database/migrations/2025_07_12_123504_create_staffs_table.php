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
        Schema::create('staffs', function (Blueprint $table) {
            $table->id();

            $table->string('pseudonym')->nullable();       // Tên giả hoặc nickname
            $table->text('bio')->nullable();               // Mô tả ngắn về nhân sự
            $table->string('address')->nullable();         // Địa chỉ liên hệ hoặc làm việc
            $table->unsignedBigInteger('user_id')->unique(); // Gắn với bảng users

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staffs');
    }
};
