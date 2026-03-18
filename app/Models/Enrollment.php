<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $table = 'enrollments';

    protected $fillable = [
        'payment_status',
        'payment_source',
        'enrollment_date',
        'progress',
        // 'user_id',
        'learner_id',
        'course_id',
    ];

    protected $casts = [
        'enrollment_date' => 'datetime',
        'progress' => 'integer',
    ];

    // public function user(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }

    public function learner(): BelongsTo
    {
        return $this->belongsTo(Learner::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
