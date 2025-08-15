<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostContent extends Model
{
    protected $fillable = [
        'post_id',
        'content_html',
        'content_json'
    ];

    /**
     * When PostContent is updated, automatically update the parent Post's updated_at.
     */
    protected $touches = ['post'];

    /**
     * Relationship: Each PostContent belongs to a single Post.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
