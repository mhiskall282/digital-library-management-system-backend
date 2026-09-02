<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production') || env('APP_ENV') === 'production' || request()->header('X-Forwarded-Proto') === 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Gate::define('manage-settings', function (User $user) {
            return $user->isSuperAdmin() || $user->isAdmin();
        });

        Gate::define('manage-users', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-categories', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('upload-resources', function (User $user) {
            return $user->isAdmin() || $user->isLecturer();
        });

        Gate::define('view-analytics', function (User $user) {
            return $user->isAdmin();
        });
    }
}
