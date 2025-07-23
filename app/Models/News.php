<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \App\Models\NewsContent|null $content
 */

class News extends Model
{
    protected $fillable = ['title', 'user_id', 'category_id'];

    public function content()
    {
        return $this->hasOne(NewsContent::class);
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
