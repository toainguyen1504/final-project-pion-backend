<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();

            $table->string('title');  // -> bắt buộc
            $table->string('slug')->unique();
            $table->text('intro')->nullable();     // phần giới thiệu ngắn
            $table->longText('content')->nullable(); // nội dung chính
            $table->unsignedInteger('duration')->default(0); // thời lượng (phút)
            $table->string('video_url')->nullable(); // link video bài học (optional) -> video_provider và video_id  
            $table->string('video_provider')->nullable(); // nhà cung cấp video (YouTube, Vimeo, v.v.)
            $table->string('video_id')->nullable(); // ID video 

            $table->unsignedInteger('order')->default(1); // thứ tự bài học trong chương -> đổi thành sort_order
            $table->boolean('is_preview')->default(false); // có cho xem trước không
            $table->boolean('is_quiz')->default(false);    // có phải bài kiểm tra không
        

            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');

            $table->unique(['course_id', 'order']); // đảm bảo không có 2 bài học nào trong cùng 1 khóa học có cùng thứ tự

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
