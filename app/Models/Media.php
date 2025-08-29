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

    public function getVariantPath($type = 'og')
    {
        $path = $this->meta['variants'][$type]['path'] ?? $this->path;
        return 'storage/' . ltrim($path, '/');
    }

    public static function findByPath($src)
    {
        return self::whereJsonContains('meta->variants->original->path', $src)
            ->orWhereJsonContains('meta->variants->og->path', $src)
            ->orWhereJsonContains('meta->variants->medium->path', $src)
            ->orWhereJsonContains('meta->variants->thumbnail->path', $src)
            ->first();
    }

    public function mediaable()
    {
        return $this->morphTo();
    }
}
