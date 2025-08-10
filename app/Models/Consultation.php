<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_content',
        'status',
        'user_id',
        'handled_by',
        'guest_name',
        'guest_email',
        'guest_phone',
    ];

    /**
     * The user who submitted the consultation request (if logged in).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     *  staff handle consultation.
     */
    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * check user is guest?
     */
    public function isGuest()
    {
        return is_null($this->user_id);
    }
}
