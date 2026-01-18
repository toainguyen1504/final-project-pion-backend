<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $table = 'teachers';
    protected $fillable = [
        'user_id',
        'subject',
        'nationality',
        'experience',
        'bio',
    ];

    protected $casts = [
        'experience' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
