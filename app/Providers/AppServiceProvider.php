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
        if ($this->app->environment('production') || env('APP_ENV') === 'production' || request()->header('X-Forwarded-Proto') === 'https' || str_contains(request()->getHttpHost(), 'onrender.com') || str_starts_with(config('app.url', ''), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Dynamic SMTP configuration from Admin Settings
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $dbMailHost = \App\Models\Setting::get('mail_host');
                if ($dbMailHost) {
                    config([
                        'mail.default' => \App\Models\Setting::get('mail_mailer', config('mail.default', 'smtp')),
                        'mail.mailers.smtp.host' => $dbMailHost,
                        'mail.mailers.smtp.port' => (int) \App\Models\Setting::get('mail_port', config('mail.mailers.smtp.port', 587)),
                        'mail.mailers.smtp.encryption' => \App\Models\Setting::get('mail_encryption', config('mail.mailers.smtp.encryption', 'tls')),
                        'mail.mailers.smtp.username' => \App\Models\Setting::get('mail_username', config('mail.mailers.smtp.username')),
                        'mail.from.address' => \App\Models\Setting::get('mail_from_address', config('mail.from.address')),
                        'mail.from.name' => \App\Models\Setting::get('mail_from_name', config('mail.from.name')),
                    ]);

                    $dbMailPassword = \App\Models\Setting::get('mail_password');
                    if (!empty($dbMailPassword)) {
                        config(['mail.mailers.smtp.password' => $dbMailPassword]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fallback gracefully to .env
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
