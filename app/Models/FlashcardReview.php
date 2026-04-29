<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashcardReview extends Model
{
    protected $table = 'flashcard_reviews';

    protected $fillable = [
        'reviewed_at',
        'ease_factor',
        'interval',
        'next_review_at',
        'is_correct',
        'learner_id',
        'flashcard_id',
        'course_id',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'next_review_at' => 'datetime',
        'is_correct' => 'boolean',
        'ease_factor' => 'float',
        'interval' => 'integer',
    ];

    public function learner(): BelongsTo
    {
        return $this->belongsTo(Learner::class);
    }

    public function flashcard(): BelongsTo
    {
        return $this->belongsTo(Flashcard::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
