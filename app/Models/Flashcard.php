<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flashcard extends Model
{
    use HasFactory;

    protected $table = 'flashcards';

    protected $fillable = [
        'vocabulary',
        'phonetic',
        'translation',
        'example_sentence',
        'example_translation',
        'image_url',
        'image_prompt',
        'audio',
        'level',
        'order',
        'tags',
        'lesson_id',
    ];

    protected $casts = [
        'front_text' => 'string',
        'back_text' => 'string',
        'phonetic' => 'string',
        'translation' => 'string',
        'example_sentence' => 'string',
        'example_translation' => 'string',
        'image_url' => 'string',
        'image_prompt' => 'string',
        'audio' => 'string',
        'level' => 'integer',
        'order' => 'integer',
        'tags' => 'array', // JSON -> array
    ];

    /**
     * Quan hệ: Flashcard thuộc về một Lesson
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Quan hệ: Flashcard có nhiều Review
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(FlashcardReview::class);
    }
}
