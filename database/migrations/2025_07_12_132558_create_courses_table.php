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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('title');                  // Tên khoá học
            $table->text('description');              // Mô tả chi tiết
            $table->decimal('price', 10, 2);          // Giá học phí

            $table->string('type')->nullable();       // Loại khoá: online, offline, hybrid...
            $table->string('level')->nullable();      // Trình độ: cơ bản, nâng cao, chuyên sâu...
            $table->string('duration')->nullable();   // Thời lượng: 8 tuần, 60 giờ...
            $table->integer('max_enrollments')->nullable(); // Số lượng học viên tối đa

            $table->unsignedBigInteger('user_id');    // Người tạo khoá học (admin, teacher...)

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
        Schema::dropIfExists('courses');
    }
};
