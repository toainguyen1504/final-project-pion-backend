<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Lesson extends Model
{
    use HasFactory;
    protected $table = 'lessons';

    protected $fillable = [
        'title',
        'slug',
        'intro',
        'content',
        'duration',
        'video_url',
        'order',
        'is_preview',
        'is_quiz',
        'chapter_id',
    ];

    protected $casts = [
        'duration' => 'integer',
        'order' => 'integer',
        'is_preview' => 'boolean',
        'is_quiz' => 'boolean',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class);
    }

    public function progresses(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LessonNote::class);
    }
}
