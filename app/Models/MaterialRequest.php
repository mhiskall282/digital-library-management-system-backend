<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_code',
        'course_name',
        'program',
        'level',
        'topic',
        'type',
        'urgency',
        'status',
        'admin_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['OPEN', 'IN_PROGRESS']);
    }
}
