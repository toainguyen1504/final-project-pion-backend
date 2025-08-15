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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('title');                  // Course name
            $table->text('description');              // Detailed course description
            $table->decimal('price', 10, 2);          // Tuition fee

            $table->string('type')->nullable();       // Course type: online, offline, hybrid...
            $table->string('level')->nullable();      // Level: beginner, advanced, intensive...
            $table->string('duration')->nullable();   // Duration: 8 weeks, 60 hours...
            $table->integer('max_enrollments')->nullable(); // Maximum number of students

            $table->unsignedBigInteger('user_id');    // Course creator (admin, teacher...)


            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
