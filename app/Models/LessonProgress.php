<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    protected $fillable = [
        'last_watched_at',
        'watched_duration',
        'is_completed',
        'learner_id',
        'lesson_id',
    ];

    protected $casts = [
        'last_watched_at' => 'datetime',
        'watched_duration' => 'integer',
        'is_completed' => 'boolean',
    ];

    public function learner(): BelongsTo
    {
        return $this->belongsTo(Learner::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
