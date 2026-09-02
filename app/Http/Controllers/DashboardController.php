<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $search = $request->input('search');
        $level = $request->input('level');
        $week = $request->input('week');
        $semester = $request->input('semester');
        $type = $request->input('type');
        $categoryId = $request->input('category_id');
        $sort = $request->input('sort', 'newest');

        $query = Resource::approved()
            ->with(['category', 'uploader', 'reviews'])
            ->search($search)
            ->filterByType($type)
            ->filterByLevel($level)
            ->filterByCategory($categoryId ? (int) $categoryId : null)
            ->filterByWeek($week ? (int) $week : null);

        if ($semester) {
            $query->whereHas('category', function ($q) use ($semester) {
                $q->where('semester', $semester);
            });
        }

        switch ($sort) {
            case 'popular':
                $query->orderByDesc('downloads');
                break;
            case 'top_rated':
                $query->orderByDesc('average_rating')->orderByDesc('total_reviews');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'newest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $resources = $query->paginate(12)->withQueryString();

        $categories = Category::orderBy('course_code')->get();
        $totalResources = Resource::count();
        $totalSlides = Resource::where('type', 'SLIDE')->count();
        $totalPastQuestions = Resource::where('type', 'PAST_QUESTION')->count();
        $myBookmarksCount = $user ? Bookmark::where('user_id', $user->id)->count() : 0;

        return view('dashboard', compact(
            'resources',
            'categories',
            'totalResources',
            'totalSlides',
            'totalPastQuestions',
            'myBookmarksCount',
            'search',
            'level',
            'week',
            'semester',
            'type',
            'categoryId',
            'sort'
        ));
    }
}
