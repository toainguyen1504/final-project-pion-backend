<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = ['name', 'slug', 'view_path', 'css_class', 'is_active'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function previewImage()
    {
        return $this->morphOne(Image::class, 'imageable')->where('type', 'template_preview');
    }
}
