<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staffs';
    protected $fillable = [
        'user_id',
        'pseudonym',
        'bio',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
