<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'student_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'level',
        'program',
        'department',
        'role',
        'is_active',
        'is_onboarded',
        'contributor_points',
        'contributor_rank',
        'bio',
        'phone',
        'avatar_path',
        'email_notifications',
        'new_resource_alerts',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_onboarded' => 'boolean',
            'contributor_points' => 'integer',
            'email_notifications' => 'boolean',
            'new_resource_alerts' => 'boolean',
        ];
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->first_name} {$this->last_name}")
        );
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => trim("{$this->first_name} {$this->last_name}")
        );
    }

    // --- Role & Privilege Verification ---

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superadmin'], true);
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['staff', 'admin', 'superadmin'], true);
    }

    public function isLecturer(): bool
    {
        return $this->role === 'lecturer';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function canModerate(): bool
    {
        return in_array($this->role, ['staff', 'admin', 'superadmin'], true);
    }

    public function hasRole(string|array $roles): bool
    {
        $rolesArray = is_array($roles) ? $roles : func_get_args();

        return in_array($this->role, $rolesArray, true);
    }

    // --- Gamification & Points Awarding ---

    public function awardPoints(int $points, ?string $reason = null): void
    {
        $this->contributor_points += $points;
        $this->recalculateRank();
        $this->save();

        if ($reason) {
            Notification::create([
                'user_id' => $this->id,
                'type' => 'GENERAL',
                'title' => "🎖️ Points Earned: +{$points} Contributor Points!",
                'message' => "You earned {$points} points ({$reason}). Your current rank is now {$this->contributor_rank} with {$this->contributor_points} total points.",
                'is_read' => false,
            ]);
        }
    }

    public function recalculateRank(): void
    {
        $pts = $this->contributor_points;

        $this->contributor_rank = match (true) {
            $pts >= 300 => 'Master Scholar',
            $pts >= 150 => 'Top Contributor',
            $pts >= 50 => 'Scholar Contributor',
            default => 'Novice Contributor',
        };
    }

    /**
     * Check if student level allows accessing a given resource level.
     */
    public function canAccessLevel(string $targetLevel): bool
    {
        if ($this->isAdmin() || $this->isStaff() || $this->isLecturer()) {
            return true;
        }

        if (! Setting::get('enforce_level_gating', true)) {
            return true;
        }

        $levels = ['L100' => 1, 'L200' => 2, 'L300' => 3, 'L400' => 4, 'MASTERS' => 5, 'PHD' => 6];
        $userRank = $levels[$this->level] ?? 1;
        $targetRank = $levels[$targetLevel] ?? 1;

        return $userRank >= $targetRank;
    }

    // --- Relationships ---

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function uploadedResources(): HasMany
    {
        return $this->hasMany(Resource::class, 'uploaded_by');
    }

    public function downloadRequests(): HasMany
    {
        return $this->hasMany(DownloadRequest::class);
    }

    public function materialRequests(): HasMany
    {
        return $this->hasMany(MaterialRequest::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
