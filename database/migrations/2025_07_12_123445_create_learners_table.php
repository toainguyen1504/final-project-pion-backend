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
        Schema::create('learners', function (Blueprint $table) {
            $table->id();

            // Liên kết với bảng users
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Thông tin học tập
            $table->string('first_name');
            $table->string('last_name');
            $table->date('dob');
            $table->string('grade')->nullable();       // Khối lớp
            $table->string('class')->nullable();       // Lớp học
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();

            // Thông tin thanh toán
            $table->decimal('balance', 12, 2)->default(0); // Số dư tài khoản
            $table->string('payment_method')->nullable(); // Mặc định: chuyển khoản, ví, v.v.

            // Thông tin phụ huynh (nếu cần)
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_email')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
