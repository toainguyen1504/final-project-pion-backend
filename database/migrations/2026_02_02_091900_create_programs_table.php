<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique(); // định danh duy nhất - lấy theo title
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // người tạo chương trình

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
