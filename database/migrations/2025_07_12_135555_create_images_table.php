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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('path');              // Đường dẫn file ảnh
            $table->string('type')->nullable();  // thumbnail, gallery, avatar, banner...
            $table->string('caption')->nullable(); // mô tả ảnh
            $table->morphs('imageable');         
            // Gắn với bất kỳ model nào , morph: news, teachers, course, templates, users, v.v.
            // imageable_id, imageable_type ---	Liên kết đa hình ----	Laravel sẽ tự tạo từ ->morphs('imageable')
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
