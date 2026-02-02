<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();

            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('payment_source')->nullable(); // ví dụ: 'momo', 'bank', (nâng cấp sau thì thêm VN Pay)
            $table->dateTime('enrollment_date')->nullable();

            $table->unsignedTinyInteger('progress')->default(0); // tiến độ học (%)

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // người học: member hoặc learner
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
