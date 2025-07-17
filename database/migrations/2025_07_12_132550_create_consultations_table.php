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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->text('request_content');         // Nội dung yêu cầu tư vấn
            $table->string('status')->default('pending'); // Trạng thái: pending, approved, rejected
            $table->unsignedBigInteger('user_id');   // Người yêu cầu tư vấn
            $table->unsignedBigInteger('handled_by')->nullable(); // Nhân viên xử lý

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
        Schema::dropIfExists('consultations');
    }
};
