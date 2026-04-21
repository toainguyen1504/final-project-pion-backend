<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('thumbnail_media_id')
                ->nullable()
                ->after('thumbnail') // đặt sau column cũ cho dễ nhìn
                ->constrained('medias')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['thumbnail_media_id']);
            $table->dropColumn('thumbnail_media_id');
        });
    }
};
