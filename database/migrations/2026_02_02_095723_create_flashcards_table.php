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
            $table->string('front_text'); // * từ vựng chính
            $table->string('back_text');  // * nghĩa cơ bản
            $table->string('phonetic')->nullable(); // phiên âm
            $table->string('translation')->nullable(); // nghĩa tiếng Việt
            $table->text('example_sentence')->nullable(); // câu ví dụ
            $table->text('example_translation')->nullable(); // dịch câu ví dụ
            $table->string('image_url')->nullable();
            $table->string('image_prompt')->nullable();
            $table->string('audio')->nullable();
            $table->unsignedTinyInteger('level')->default(0); // cấp độ từ vựng
            $table->integer('order')->default(0); // thứ tự trong lesson

            $table->json('tags')->nullable(); // metadata: unit, chủ đề

            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flashcards');
    }
};
