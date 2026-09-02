<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'metadata',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class, 'subject_id');
    }

    protected function details(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->metadata ?: []
        );
    }

    public static function record(string $action, ?User $user = null, mixed $subject = null, ?array $metadata = null): self
    {
        return static::create([
            'user_id' => $user?->id ?? auth()->id(),
            'action' => strtoupper($action),
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'metadata' => $metadata,
            'ip_address' => Request::ip(),
        ]);
    }
}
