<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;
    protected $table = 'courses';

    protected $fillable = [
        'title',
        'slug',
        'language',
        'thumbnail', // thumbnail tạm để để tránh vỡ FE
        'thumbnail_media_id',
        'description',
        'price',
        'discount_price',
        'level',
        'status',
        'duration',
        'participants',
        'total_lessons',
        'benefits',
        'is_free',
        'program_id',
        'category_id',
        'user_id',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'duration' => 'integer',
        'participants' => 'integer',
        'total_lessons' => 'integer',
        'benefits' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function thumbnailMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'thumbnail_media_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }


    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->relationLoaded('thumbnailMedia') && $this->thumbnailMedia) {
            return asset($this->thumbnailMedia->getVariantPath('medium'));
        }

        if ($this->thumbnailMedia) {
            return asset($this->thumbnailMedia->getVariantPath('medium'));
        }

        return $this->thumbnail ?: null;
    }

    public function getThumbnailThumbAttribute(): ?string
    {
        if ($this->relationLoaded('thumbnailMedia') && $this->thumbnailMedia) {
            return asset($this->thumbnailMedia->getVariantPath('thumbnail'));
        }

        if ($this->thumbnailMedia) {
            return asset($this->thumbnailMedia->getVariantPath('thumbnail'));
        }

        return $this->thumbnail ?: null;
    }

    public function getThumbnailOgAttribute(): ?string
    {
        if ($this->relationLoaded('thumbnailMedia') && $this->thumbnailMedia) {
            return asset($this->thumbnailMedia->getVariantPath('og'));
        }

        if ($this->thumbnailMedia) {
            return asset($this->thumbnailMedia->getVariantPath('og'));
        }

        return $this->thumbnail ?: null;
    }
}
