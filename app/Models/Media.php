<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'medias';
    protected $casts = [
        'meta' => 'array',
    ];

    protected $fillable = [
        'path',
        'type', // image, video, document...
        'mime_type',
        'title',
        'alt',
        'caption',
        'description',
        'source_type', // local/external
        'external_url',
        'external_id',
        'meta',
    ];

    public function mediaable()
    {
        return $this->morphTo();
    }
}
