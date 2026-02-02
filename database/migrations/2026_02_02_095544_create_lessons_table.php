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

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('intro')->nullable();     // phần giới thiệu ngắn
            $table->longText('content')->nullable(); // nội dung chính
            $table->unsignedInteger('duration')->default(0); // thời lượng (phút)
            $table->string('video_url')->nullable(); // link video bài học

            $table->unsignedInteger('order')->default(1); // thứ tự bài học trong chương
            $table->boolean('is_preview')->default(false); // có cho xem trước không
            $table->boolean('is_quiz')->default(false);    // có phải bài kiểm tra không

            $table->foreignId('chapter_id')->constrained('chapters')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
