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
            $table->string('slug')->unique(); // Logical identifier, used for API, routing, or UI mapping
            $table->string('css_class')->nullable(); // Attach a CSS class to HTML for UI customization
            $table->json('config_json')->nullable(); // Store JSON configuration for the interface → used by frontend
            $table->string('view_path'); // Path to the Blade template used to render the post → backend rendering
            $table->boolean('is_active')->default(true); // Enable/disable the template, allowing admin control
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
