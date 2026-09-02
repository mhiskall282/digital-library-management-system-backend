<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Resource;
use App\Models\Review;
use App\Models\Setting;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function __construct(
        protected RecommendationService $recommendationService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $activeSemester = Setting::get('active_semester', 'FIRST');
        $academicYear = Setting::get('academic_year', '2023/2024');

        // Enrolled Courses for the student's level and program
        $myCourses = Category::where('level', $user->level)
            ->where(function ($q) use ($user) {
                if ($user->program) {
                    $q->where('program', 'like', "%{$user->program}%")
                      ->orWhere('program', 'General Business');
                }
            })
            ->withCount(['resources' => fn ($q) => $q->approved()])
            ->orderBy('course_code')
            ->get();

        // If no program-specific courses, fallback to general level courses
        if ($myCourses->isEmpty()) {
            $myCourses = Category::where('level', $user->level)
                ->withCount(['resources' => fn ($q) => $q->approved()])
                ->orderBy('course_code')
                ->get();
        }

        // Recent Bookmarks with personal notes
        $recentBookmarks = Bookmark::where('user_id', $user->id)
            ->with(['resource.category'])
            ->latest()
            ->limit(4)
            ->get();

        // Intelligent Recommendations tailored to student's program, level, and rating
        $recommendedResources = $this->recommendationService->getRecommendationsForUser($user, 4);

        // Top student contributors for community motivation
        $topContributors = $this->recommendationService->getTopContributors(5);

        // Student stats
        $bookmarksCount = Bookmark::where('user_id', $user->id)->count();
        $reviewsCount = Review::where('user_id', $user->id)->count();
        $unreadNotificationsCount = Notification::where('user_id', $user->id)->where('is_read', false)->count();

        return view('student.dashboard', compact(
            'user',
            'myCourses',
            'recentBookmarks',
            'recommendedResources',
            'topContributors',
            'bookmarksCount',
            'reviewsCount',
            'unreadNotificationsCount',
            'activeSemester',
            'academicYear'
        ));
    }
}
