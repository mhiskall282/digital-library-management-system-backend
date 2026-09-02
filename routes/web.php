<?php

use App\Http\Controllers\Admin\BroadcastController as AdminBroadcastController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DownloadApprovalController as AdminDownloadApprovalController;
use App\Http\Controllers\Admin\ModerationController as AdminModerationController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\UserImportController as AdminUserImportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OnboardingController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MaterialRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Student\ContributionController;
use App\Http\Controllers\Student\StudentDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - UEW School of Business Digital Library
|--------------------------------------------------------------------------
*/

// Healthcheck Route for Render, Docker, and Monitoring
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'app' => 'UEW Digital Library Management System',
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('health');

// Public Brand Landing Page & Program Directory
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/programs', [HomeController::class, 'programs'])->name('programs.index');

// Public User Guide & Architecture Documentation
Route::get('/docs', [\App\Http\Controllers\DocsController::class, 'index'])->name('docs.index');
Route::redirect('/doc', '/docs');

// Guest Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// Authenticated Student & Portal Routes
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Scholar Onboarding (Profile Completion)
    Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class, 'update'])->name('onboarding.update');

    // Student Hub (Personalized Dashboard)
    Route::get('/student/hub', [StudentDashboardController::class, 'index'])->name('student.hub');

    // Student Resource Contribution (Upload for Moderation)
    Route::get('/student/contribute', [ContributionController::class, 'create'])->name('student.contribute');
    Route::post('/student/contribute', [ContributionController::class, 'store'])->name('student.contribute.store');

    // General Catalog Explorer
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Resources & Downloads
    Route::get('/resources/{resource}', [ResourceController::class, 'show'])->name('resources.show');
    Route::get('/resources/{resource}/download', [ResourceController::class, 'download'])->name('resources.download');
    Route::post('/resources/{resource}/request-download', [ResourceController::class, 'requestDownload'])->name('resources.request-download');
    Route::get('/resources/{resource}/preview', [ResourceController::class, 'preview'])->name('resources.preview');

    // Resource Reviews & Helpful Voting
    Route::post('/resources/{resource}/reviews', [ReviewController::class, 'store'])->name('resources.reviews.store');
    Route::post('/reviews/{review}/helpful', [ReviewController::class, 'helpful'])->name('reviews.helpful');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Bookmarks & Personal Notes
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/resources/{resource}/bookmark', [BookmarkController::class, 'toggle'])->name('resources.bookmark.toggle');
    Route::put('/bookmarks/{bookmark}', [BookmarkController::class, 'update'])->name('bookmarks.update');
    Route::delete('/bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');

    // Material & Support Requests
    Route::get('/requests', [MaterialRequestController::class, 'index'])->name('requests.index');
    Route::post('/requests', [MaterialRequestController::class, 'store'])->name('requests.store');

    // Notifications Center
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/clear-read', [NotificationController::class, 'clearRead'])->name('notifications.clear-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Student Profile & Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/settings', [ProfileController::class, 'edit'])->name('settings');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Administrative Portal Routes (RBAC Protected: Admin, Staff, Superadmin)
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // Executive Command Center & Analytics
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);
        Route::get('/analytics', [AdminDashboardController::class, 'analytics'])->name('analytics');

        // Student Submissions Moderation Desk
        Route::get('/moderation', [AdminModerationController::class, 'index'])->name('moderation.index');
        Route::post('/moderation/{resource}/approve', [AdminModerationController::class, 'approve'])->name('moderation.approve');
        Route::post('/moderation/{resource}/reject', [AdminModerationController::class, 'reject'])->name('moderation.reject');

        // Material Download Approvals & IP Audit Desk
        Route::get('/downloads', [AdminDownloadApprovalController::class, 'index'])->name('downloads.index');
        Route::post('/downloads/{downloadRequest}/approve', [AdminDownloadApprovalController::class, 'approve'])->name('downloads.approve');
        Route::post('/downloads/{downloadRequest}/reject', [AdminDownloadApprovalController::class, 'reject'])->name('downloads.reject');

        // Material & Support Requests Desk
        Route::get('/material-requests', [MaterialRequestController::class, 'adminIndex'])->name('requests.index');
        Route::put('/material-requests/{materialRequest}', [MaterialRequestController::class, 'updateStatus'])->name('requests.update');

        // Departmental Broadcasts & Announcements
        Route::get('/broadcasts', [AdminBroadcastController::class, 'create'])->name('broadcasts.create');
        Route::post('/broadcasts', [AdminBroadcastController::class, 'store'])->name('broadcasts.store');

        // Email Templates & SMTP Dispatch Studio & Simulation Mailbox
        Route::get('/mail-studio', [\App\Http\Controllers\Admin\MailTestController::class, 'index'])->name('mail.index');
        Route::post('/mail-studio/send', [\App\Http\Controllers\Admin\MailTestController::class, 'sendTest'])->name('mail.send');
        Route::post('/mail-studio/simulate-incoming', [\App\Http\Controllers\Admin\MailTestController::class, 'simulateIncoming'])->name('mail.simulate-incoming');
        Route::get('/mail-studio/logs/{emailLog}', [\App\Http\Controllers\Admin\MailTestController::class, 'showLog'])->name('mail.show-log');
        Route::delete('/mail-studio/logs', [\App\Http\Controllers\Admin\MailTestController::class, 'clearLogs'])->name('mail.clear-logs');

        // System Audit Logs & Compliance Reporting
        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [AdminReportController::class, 'exportCsv'])->name('reports.export');

        // System Settings & Cache Operations
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
        Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
        Route::post('/settings/cache-clear', [AdminSettingsController::class, 'clearCache'])->name('settings.cache-clear');

        // Admin Resource Management
        Route::get('/resources', [AdminResourceController::class, 'index'])->name('resources.index');
        Route::get('/resources/create', [AdminResourceController::class, 'create'])->name('resources.create');
        Route::post('/resources', [AdminResourceController::class, 'store'])->name('resources.store');
        Route::get('/resources/{resource}/edit', [AdminResourceController::class, 'edit'])->name('resources.edit');
        Route::put('/resources/{resource}', [AdminResourceController::class, 'update'])->name('resources.update');
        Route::delete('/resources/{resource}', [AdminResourceController::class, 'destroy'])->name('resources.destroy');

        // Course Categories
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        // User Management & Bulk Ingestion
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/import', [AdminUserImportController::class, 'create'])->name('users.import');
        Route::get('/users/import/sample', [AdminUserImportController::class, 'sampleCsv'])->name('users.import.sample');
        Route::post('/users/import', [AdminUserImportController::class, 'store'])->name('users.import.store');
        Route::post('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::post('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
    });
});
