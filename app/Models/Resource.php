<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'category_id',
        'level',
        'week',
        'academic_year',
        'file_name',
        'file_path',
        'file_blob',
        'file_size',
        'mime_type',
        'downloads',
        'average_rating',
        'total_reviews',
        'uploaded_by',
        'tags',
    ];

    protected $hidden = [
        'file_blob',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'average_rating' => 'decimal:2',
            'downloads' => 'integer',
            'total_reviews' => 'integer',
            'file_size' => 'integer',
            'week' => 'integer',
            'reviewed_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function downloadRequests(): HasMany
    {
        return $this->hasMany(DownloadRequest::class);
    }

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function isPending(): bool
    {
        return $this->status === 'PENDING_REVIEW';
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    public function scopePendingReview(Builder $query): Builder
    {
        return $query->where('status', 'PENDING_REVIEW');
    }

    public function isBookmarkedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }

    public function recalculateRating(): void
    {
        $this->average_rating = (float) ($this->reviews()->avg('rating') ?: 0.0);
        $this->total_reviews = $this->reviews()->count();
        $this->save();
    }

    protected function formattedSize(): Attribute
    {
        return Attribute::make(
            get: function () {
                $bytes = $this->file_size;
                if ($bytes >= 1048576) {
                    return number_format($bytes / 1048576, 1) . ' MB';
                }
                if ($bytes >= 1024) {
                    return number_format($bytes / 1024, 1) . ' KB';
                }
                return $bytes . ' B';
            }
        );
    }

    protected function extension(): Attribute
    {
        return Attribute::make(
            get: fn () => strtoupper(pathinfo($this->file_name, PATHINFO_EXTENSION) ?: 'FILE')
        );
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($sub) use ($search) {
            $sub->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhereHas('category', function ($cat) use ($search) {
                    $cat->where('course_code', 'like', "%{$search}%")
                        ->orWhere('course_name', 'like', "%{$search}%");
                });
        });
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($cat) use ($search) {
                            $cat->where('course_code', 'like', "%{$search}%")
                                ->orWhere('course_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['level'] ?? null, fn ($q, $lvl) => $q->where('level', $lvl))
            ->when($filters['type'] ?? null, fn ($q, $type) => $q->where('type', $type))
            ->when($filters['category_id'] ?? null, fn ($q, $catId) => $q->where('category_id', $catId))
            ->when($filters['semester'] ?? null, function ($q, $sem) {
                $q->whereHas('category', fn ($cat) => $cat->where('semester', $sem));
            });
    }

    public function scopeFilterByType(Builder $query, ?string $type): Builder
    {
        return $type ? $query->where('type', $type) : $query;
    }

    public function scopeFilterByLevel(Builder $query, ?string $level): Builder
    {
        return $level ? $query->where('level', $level) : $query;
    }

    public function scopeFilterByCategory(Builder $query, ?int $categoryId): Builder
    {
        return $categoryId ? $query->where('category_id', $categoryId) : $query;
    }

    public function scopeFilterByWeek(Builder $query, ?int $week): Builder
    {
        return $week ? $query->where('week', $week) : $query;
    }

    protected function formattedWeek(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->week ? "Week {$this->week}" : 'General / Exam'
        );
    }
}
