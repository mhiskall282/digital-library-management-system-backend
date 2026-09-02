<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'course_code',
        'course_name',
        'level',
        'semester',
        'description',
    ];

    protected function displayTitle(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->course_code} — {$this->course_name}"
        );
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function scopeLevel(Builder $query, ?string $level): Builder
    {
        return $level ? $query->where('level', $level) : $query;
    }

    public function scopeSemester(Builder $query, ?string $semester): Builder
    {
        return $semester ? $query->where('semester', $semester) : $query;
    }
}
