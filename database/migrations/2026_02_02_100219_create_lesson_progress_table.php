<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();

            $table->timestamp('last_watched_at')->nullable(); // thời điểm xem gần nhất
            $table->unsignedInteger('watched_duration')->default(0); // thời lượng đã xem (phút)
            $table->boolean('is_completed')->default(false); // đã hoàn thành bài học chưa

            $table->foreignId('learner_id')->constrained('learners')->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained('lessons')->onDelete('cascade');
            $table->unique(['learner_id', 'lesson_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
    }
};
