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
        Schema::create('post_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id')->unique();

            // HTML content from CKEditor / Quill
            $table->longText('content_html')->nullable();

            // Structured JSON format (e.g., Gutenberg blocks)
            $table->json('content_json')->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_contents');
    }
};
