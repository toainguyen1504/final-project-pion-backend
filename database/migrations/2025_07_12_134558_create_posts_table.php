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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            $table->string('title');                  // Post title shown to users
            $table->string('slug')->unique();         // SEO-friendly URL - slug follow seo_title
            $table->unsignedBigInteger('user_id');    // Author
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('template_id')->nullable();

            // SEO fields (Rank Math style)
            $table->string('seo_title')->nullable();        // SEO title
            $table->text('seo_description')->nullable();    // SEO meta description
            $table->string('seo_keywords')->nullable();     // Focus keywords (comma-separated)
            $table->json('seo_meta')->nullable();           // Extended SEO (Open Graph, Twitter Card...)

            // Status & scheduling
            $table->enum('status', ['draft', 'pending', 'published', 'archived'])->default('draft');
            $table->timestamp('publish_at')->nullable();    // For scheduling

            // Main featured image (relation to medias table)
            $table->unsignedBigInteger('featured_media_id')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('template_id')->references('id')->on('templates')->onDelete('set null');
            $table->foreign('featured_media_id')->references('id')->on('medias')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
