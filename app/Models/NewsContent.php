<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsContent extends Model
{
    protected $fillable = ['news_id', 'content_html', 'content_json'];

    // Khi cập nhật NewsContent, tự động cập nhật updated_at của News
    protected $touches = ['news'];

    public function news()
    {
        return $this->belongsTo(News::class);
    }
}
