<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flashcard_reviews', function (Blueprint $table) {
            $table->id();

            $table->timestamp('reviewed_at')->nullable(); // thời điểm ôn tập
            $table->float('ease_factor')->default(2.5);   // độ dễ nhớ (SM-2)
            $table->integer('interval')->default(1);      // khoảng cách ngày đến lần ôn tiếp theo
            $table->timestamp('next_review_at')->nullable(); // ngày đề xuất ôn lại
            $table->boolean('is_correct')->default(false);   // người học trả lời đúng hay sai

            $table->foreignId('learner_id')->constrained('learners')->onDelete('cascade');
            $table->foreignId('flashcard_id')->constrained('flashcards')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flashcard_reviews');
    }
};
