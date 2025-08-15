<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property \App\Models\PostContent|null $content
 */
class Post extends Model
{
    protected $fillable = [
        'title',
        'user_id',
        'category_id',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'seo_meta',
        'status',
        'publish_at',
        'featured_media_id'
    ];

    protected static function booted()
    {
        static::creating(function ($post) {
            $baseTitle = $post->seo_title ?: $post->title;
            $slug = Str::slug($baseTitle);

            // Check for duplicate slug
            $count = static::where('slug', 'LIKE', "{$slug}%")->count();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }

            $post->slug = $slug;
        });
    }

    public function content()
    {
        return $this->hasOne(PostContent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
