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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();

            $table->string('subject')->nullable(); // nhiều môn thì tách bảng phụ, hiện chị là 1 giáo viên 1 môn
            $table->string('nationality')->nullable();
            $table->smallInteger('experience_years')->default(0); // số năm kinh nghiệm
            $table->text('certificate')->nullable(); // eg. TESOL, IELTS ...
            $table->text('bio')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('user_id')->index(); // index thay vì unique
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
