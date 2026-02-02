<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flashcards', function (Blueprint $table) {
            $table->id();

            $table->text('front_text'); // mặt trước
            $table->text('back_text');  // mặt sau
            $table->string('image_url')->nullable(); // ảnh minh họa (upload hoặc render)
            $table->text('image_prompt')->nullable(); // mô tả ảnh nếu render từ AI
            $table->string('audio')->nullable();     // âm thanh nếu có

            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flashcards');
    }
};
