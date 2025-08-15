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
        Schema::create('medias', function (Blueprint $table) {
            $table->id();

            // General type: image | video | audio | document | other
            $table->string('type');

            // MIME type: e.g., image/jpeg, video/mp4
            $table->string('mime_type')->nullable();

            // Local file path (e.g., /uploads/images/file.jpg)
            $table->string('path')->nullable();

            // Source type: local | external
            $table->string('source_type')->default('local');

            // External URL (e.g., YouTube, Vimeo, Google Drive link)
            $table->string('external_url')->nullable();

            // External ID (e.g., YouTube video ID)
            $table->string('external_id')->nullable();

            // Extra metadata in JSON (dimensions, duration, file size, etc.)
            $table->json('meta')->nullable();

            // SEO & descriptive fields
            $table->string('title')->nullable();       // Media title
            $table->string('alt')->nullable();         // Alt text (SEO)
            $table->text('caption')->nullable();       // Caption shown under media
            $table->text('description')->nullable();   // Media description

            // Polymorphic relation to attach media to multiple models (posts, products, etc.)
            // mediaable_id   BIGINT
            // mediaable_type VARCHAR
            $table->nullableMorphs('mediaable');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medias');
    }
};
