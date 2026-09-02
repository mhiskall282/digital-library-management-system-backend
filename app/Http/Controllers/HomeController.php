<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Resource;
use App\Models\Setting;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        protected RecommendationService $recommendationService
    ) {}

    public function index(): View
    {
        $totalResources = Resource::approved()->count();
        $totalDownloads = (int) Resource::approved()->sum('downloads');
        $totalStudents = User::where('role', 'student')->count();
        $totalCourses = Category::count();

        $trendingResources = $this->recommendationService->getTrendingResources(6);
        $topContributors = $this->recommendationService->getTopContributors(5);

        $academicYear = Setting::get('academic_year', '2023/2024');
        $activeSemester = Setting::get('active_semester', 'FIRST');

        $featuredPrograms = [
            [
                'name' => 'BSc. Business Information Systems (BIS)',
                'code' => 'BIS',
                'description' => 'Enterprise systems, database architecture, software development, and digital commerce technologies.',
                'icon' => '💻',
            ],
            [
                'name' => 'BSc. Banking and Finance',
                'code' => 'BNF',
                'description' => 'Commercial banking, financial markets, asset pricing, portfolio theory, and microfinance.',
                'icon' => '🏦',
            ],
            [
                'name' => 'BSc. Accounting',
                'code' => 'ACT',
                'description' => 'Financial reporting under IFRS, cost management, forensic auditing, and taxation practice.',
                'icon' => '📊',
            ],
            [
                'name' => 'BBA. Marketing',
                'code' => 'MKT',
                'description' => 'Digital customer acquisition, brand valuation, market intelligence, and consumer psychology.',
                'icon' => '🎯',
            ],
            [
                'name' => 'BBA. Human Resource Management',
                'code' => 'HRM',
                'description' => 'Talent acquisition, executive compensation, Ghanaian labor law, and organizational change.',
                'icon' => '👥',
            ],
            [
                'name' => 'BSc. Procurement & Supply Chain',
                'code' => 'PSC',
                'description' => 'Public procurement act, global strategic sourcing, logistics inventory, and vendor audit.',
                'icon' => '📦',
            ],
        ];

        return view('welcome', compact(
            'totalResources',
            'totalDownloads',
            'totalStudents',
            'totalCourses',
            'trendingResources',
            'topContributors',
            'featuredPrograms',
            'academicYear',
            'activeSemester'
        ));
    }

    public function programs(): View
    {
        $programsCatalog = $this->recommendationService->getProgramsCatalog();
        $totalCourses = Category::count();
        $totalResources = Resource::approved()->count();

        return view('programs.index', compact('programsCatalog', 'totalCourses', 'totalResources'));
    }
}
