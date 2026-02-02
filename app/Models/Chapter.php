<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    protected $table = 'chapters';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'order',
        'is_preview',
        'total_duration',
        'total_lessons',
        'course_id',
    ];

    protected $casts = [
        'is_preview' => 'boolean',
        'order' => 'integer',
        'total_duration' => 'integer',
        'total_lessons' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(LessonNote::class);
    }
}
