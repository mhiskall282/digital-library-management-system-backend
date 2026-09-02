<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_id',
        'user_id',
        'rating',
        'comment',
        'helpful_count',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'helpful_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (Review $review) {
            $review->resource?->recalculateRating();
        });

        static::deleted(function (Review $review) {
            $review->resource?->recalculateRating();
        });
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
