<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Learner extends Model
{
    protected $table = 'learners';
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'dob',
        'grade',
        'class',
        'is_active',
        'last_login_at',
        'balance',
        'payment_method',
        'guardian_name',
        'guardian_phone',
        'guardian_email',
    ];

    protected $casts = [
        'dob' => 'date',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
        'balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
