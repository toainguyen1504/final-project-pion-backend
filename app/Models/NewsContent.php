<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsContent extends Model
{
    protected $fillable = ['news_id', 'content_html'];

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
