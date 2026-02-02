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
        'front_text',
        'back_text',
        'image_url',
        'image_prompt',
        'audio',
        'lesson_id',
    ];

    protected $casts = [
        'front_text' => 'string',
        'back_text' => 'string',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(FlashcardReview::class);
    }
}
