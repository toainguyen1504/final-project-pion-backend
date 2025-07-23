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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); //Định danh logic, dùng cho API, route hoặc mapping UI
            $table->string('css_class')->nullable(); // Gắn class vào HTML để tuỳ biến giao diện
            $table->json('config_json')->nullable(); // chứa cấu hình dạng JSON cho giao diện - → phục vụ FE
            $table->string('view_path'); // Dẫn đến Blade hiển thị bài viết → backend render
            $table->boolean('is_active')->default(true); //Bật/tắt template, giúp admin kiểm soát
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
