<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('language')->nullable();
            $table->string('thumbnail')->nullable();
            $table->text('description')->nullable();

            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->unsignedTinyInteger('level')->default(0); // từ 0 đến 10, tuy nhiên thực tế chỉ từ 0-6
            $table->enum('status', ['draft', 'pending', 'published', 'inactive', 'archived'])->default('draft');

            $table->integer('duration')->default(0); // tổng thời lượng (phút)
            $table->integer('participants')->default(0); // số lượng học viên
            $table->integer('total_lessons')->default(0); // tổng số bài học
            $table->text('benefits')->nullable(); // mô tả lợi ích, lưu dạng JSON: ["...", "...", "..."]
            $table->boolean('is_free')->default(false);

            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete(); // 1 khóa học thuộc về 1 chương trình học, ví dụ: Tiếng Anh cho Trẻ em, Tiếng Anh Giao tiếp,...
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete(); // 1 khóa học thuộc về 1 danh mục, ví dụ: Tiếng Anh, tiếng Trung..
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // người tạo

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
