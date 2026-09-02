<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Support\Collection;

class RecommendationService
{
    /**
     * Get personalized study recommendations for a given student.
     */
    public function getRecommendationsForUser(User $user, int $limit = 6): Collection
    {
        // 1. Resources from courses in the student's program and level
        $programResources = Resource::approved()
            ->where('level', $user->level)
            ->whereHas('category', function ($q) use ($user) {
                if ($user->program) {
                    $q->where('program', 'like', "%{$user->program}%")
                      ->orWhere('program', 'General Business');
                }
            })
            ->with(['category', 'uploader'])
            ->orderByDesc('average_rating')
            ->orderByDesc('downloads')
            ->limit($limit)
            ->get();

        if ($programResources->count() >= $limit) {
            return $programResources;
        }

        // 2. Fallback: Any top-rated materials for their level
        $remaining = $limit - $programResources->count();
        $fallback = Resource::approved()
            ->where('level', $user->level)
            ->whereNotIn('id', $programResources->pluck('id'))
            ->with(['category', 'uploader'])
            ->orderByDesc('downloads')
            ->limit($remaining)
            ->get();

        return $programResources->merge($fallback);
    }

    /**
     * Get trending materials across the entire repository.
     */
    public function getTrendingResources(int $limit = 6): Collection
    {
        return Resource::approved()
            ->with(['category', 'uploader'])
            ->orderByDesc('downloads')
            ->orderByDesc('average_rating')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top student contributors for gamification leaderboard.
     */
    public function getTopContributors(int $limit = 5): Collection
    {
        return User::where('role', 'student')
            ->where('is_active', true)
            ->orderByDesc('contributor_points')
            ->limit($limit)
            ->get();
    }

    /**
     * Get the full catalog hierarchically grouped by Program and Level.
     * e.g. Banking and Finance -> L100, L200, L300, L400, MASTERS, PHD
     */
    public function getProgramsCatalog(): array
    {
        $categories = Category::with(['resources' => function ($q) {
            $q->approved()->with('uploader');
        }])->get();

        $grouped = [];

        foreach ($categories as $cat) {
            $program = $cat->program ?: 'General Business';
            $level = $cat->level;

            if (! isset($grouped[$program])) {
                $grouped[$program] = [
                    'program' => $program,
                    'levels' => [
                        'L100' => [],
                        'L200' => [],
                        'L300' => [],
                        'L400' => [],
                        'MASTERS' => [],
                        'PHD' => [],
                    ],
                    'total_resources' => 0,
                ];
            }

            $grouped[$program]['levels'][$level][] = $cat;
            $grouped[$program]['total_resources'] += $cat->resources->count();
        }

        return $grouped;
    }
}
