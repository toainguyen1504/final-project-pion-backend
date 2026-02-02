<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(1); // thứ tự chương trong khóa học
            $table->boolean('is_preview')->default(false); // có cho xem trước không

            $table->unsignedInteger('total_duration')->default(0); // tổng thời lượng chương (phút)
            $table->unsignedInteger('total_lessons')->default(0);  // tổng số bài học trong chương

            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
