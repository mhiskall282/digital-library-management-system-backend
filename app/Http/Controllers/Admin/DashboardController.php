<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Resource;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalResources = Resource::count();
        $totalDownloads = (int) Resource::sum('downloads');
        $totalStudents = User::where('role', 'student')->count();
        $totalCategories = Category::count();
        $totalReviews = Review::count();
        $averageRating = (float) (Review::avg('rating') ?: 0.0);

        // Recent Uploads
        $recentResources = Resource::with(['category', 'uploader'])
            ->latest()
            ->limit(6)
            ->get();

        // Top Downloaded Materials
        $topDownloaded = Resource::with('category')
            ->orderByDesc('downloads')
            ->limit(5)
            ->get();

        // Recent System Activity
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(8)
            ->get();

        // Active Session Info
        $academicYear = Setting::get('academic_year', '2023/2024');
        $activeSemester = Setting::get('active_semester', 'FIRST');

        return view('admin.dashboard', compact(
            'totalResources',
            'totalDownloads',
            'totalStudents',
            'totalCategories',
            'totalReviews',
            'averageRating',
            'recentResources',
            'topDownloaded',
            'recentActivities',
            'academicYear',
            'activeSemester'
        ));
    }

    public function analytics(): View
    {
        $totalResources = Resource::count();
        $totalDownloads = (int) Resource::sum('downloads');
        $totalUsers = User::where('role', 'student')->count();
        $totalCategories = Category::count();
        $totalReviews = Review::count();
        $averageRating = (float) (Review::avg('rating') ?: 0.0);

        $levelDistribution = Resource::selectRaw('level, count(*) as count, sum(downloads) as total_downloads')
            ->groupBy('level')
            ->orderBy('level')
            ->get();

        $typeDistribution = Resource::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get();

        $topDownloaded = Resource::with('category')
            ->orderByDesc('downloads')
            ->limit(5)
            ->get();

        $topRated = Resource::with('category')
            ->where('total_reviews', '>', 0)
            ->orderByDesc('average_rating')
            ->limit(5)
            ->get();

        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.analytics', compact(
            'totalResources',
            'totalDownloads',
            'totalUsers',
            'totalCategories',
            'totalReviews',
            'averageRating',
            'levelDistribution',
            'typeDistribution',
            'topDownloaded',
            'topRated',
            'recentActivities'
        ));
    }
}
