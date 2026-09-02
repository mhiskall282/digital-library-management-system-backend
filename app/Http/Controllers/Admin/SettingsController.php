<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(): View
    {
        $settings = Setting::all()->keyBy('key');

        // Server Environment Telemetry
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_os' => PHP_OS,
            'db_connection' => config('database.default'),
            'db_name' => config('database.connections.' . config('database.default') . '.database'),
            'storage_driver' => config('filesystems.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
        ];

        return view('admin.settings', compact('settings', 'systemInfo'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year' => ['required', 'string', 'max:20'],
            'active_semester' => ['required', 'in:FIRST,SECOND'],
            'institution_name' => ['required', 'string', 'max:255'],
            'max_upload_size_mb' => ['required', 'integer', 'min:5', 'max:500'],
            'allowed_file_extensions' => ['required', 'string'],
            'allow_student_registration' => ['nullable', 'boolean'],
            'enforce_level_gating' => ['nullable', 'boolean'],
            'portal_maintenance_mode' => ['nullable', 'boolean'],
            'enable_email_alerts' => ['nullable', 'boolean'],
            'auto_notify_new_resources' => ['nullable', 'boolean'],
            'contact_email' => ['required', 'email'],

            // SMTP Server & Email Logins
            'mail_mailer' => ['nullable', 'in:smtp,log'],
            'mail_host' => ['nullable', 'string', 'max:255'],
            'mail_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'mail_encryption' => ['nullable', 'in:tls,ssl,none'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],
        ]);

        Setting::set('academic_year', $validated['academic_year'], 'string', 'academic');
        Setting::set('active_semester', $validated['active_semester'], 'string', 'academic');
        Setting::set('institution_name', $validated['institution_name'], 'string', 'academic');

        Setting::set('max_upload_size_mb', (int) $validated['max_upload_size_mb'], 'integer', 'storage');
        Setting::set('allowed_file_extensions', $validated['allowed_file_extensions'], 'string', 'storage');

        Setting::set('allow_student_registration', $request->boolean('allow_student_registration'), 'boolean', 'security');
        Setting::set('enforce_level_gating', $request->boolean('enforce_level_gating'), 'boolean', 'security');
        Setting::set('portal_maintenance_mode', $request->boolean('portal_maintenance_mode'), 'boolean', 'security');

        Setting::set('enable_email_alerts', $request->boolean('enable_email_alerts'), 'boolean', 'notifications');
        Setting::set('auto_notify_new_resources', $request->boolean('auto_notify_new_resources'), 'boolean', 'notifications');
        Setting::set('contact_email', $validated['contact_email'], 'string', 'notifications');

        // Save SMTP Configuration
        if ($request->filled('mail_mailer')) {
            Setting::set('mail_mailer', $validated['mail_mailer'], 'string', 'email_smtp', 'Active mail driver');
        }
        if ($request->filled('mail_host')) {
            Setting::set('mail_host', $validated['mail_host'], 'string', 'email_smtp', 'SMTP relay hostname');
        }
        if ($request->filled('mail_port')) {
            Setting::set('mail_port', (int) $validated['mail_port'], 'integer', 'email_smtp', 'SMTP port');
        }
        if ($request->filled('mail_encryption')) {
            Setting::set('mail_encryption', $validated['mail_encryption'], 'string', 'email_smtp', 'Transport layer encryption');
        }
        if ($request->filled('mail_username')) {
            Setting::set('mail_username', $validated['mail_username'], 'string', 'email_smtp', 'SMTP authenticating username');
        }
        // Only update password if user explicitly entered a new one
        if (!empty($validated['mail_password'])) {
            Setting::set('mail_password', $validated['mail_password'], 'string', 'email_smtp', 'SMTP authentication password');
        }
        if ($request->filled('mail_from_address')) {
            Setting::set('mail_from_address', $validated['mail_from_address'], 'string', 'email_smtp', 'Sender email address');
        }
        if ($request->filled('mail_from_name')) {
            Setting::set('mail_from_name', $validated['mail_from_name'], 'string', 'email_smtp', 'Sender display name');
        }

        ActivityLog::record('SETTINGS_UPDATE', $request->user(), null, [
            'updated_by' => $request->user()->name,
            'smtp_updated' => !empty($validated['mail_username']),
        ]);

        return back()->with('success', 'Library operational parameters and SMTP credentials successfully updated!');
    }

    public function testSmtp(Request $request): RedirectResponse
    {
        $request->validate([
            'test_recipient' => ['required', 'email'],
        ]);

        $recipient = $request->test_recipient;

        try {
            // Ensure runtime mail config uses saved DB settings
            $smtpHost = Setting::get('mail_host') ?? config('mail.mailers.smtp.host');
            $smtpPort = (int) (Setting::get('mail_port') ?? config('mail.mailers.smtp.port', 587));
            $smtpEncryption = Setting::get('mail_encryption') ?? config('mail.mailers.smtp.encryption', 'tls');
            $smtpUser = Setting::get('mail_username') ?? config('mail.mailers.smtp.username');
            $smtpPass = Setting::get('mail_password') ?? config('mail.mailers.smtp.password');
            $fromAddress = Setting::get('mail_from_address') ?? config('mail.from.address');
            $fromName = Setting::get('mail_from_name') ?? config('mail.from.name');

            config([
                'mail.default' => Setting::get('mail_mailer', 'smtp'),
                'mail.mailers.smtp.host' => $smtpHost,
                'mail.mailers.smtp.port' => $smtpPort,
                'mail.mailers.smtp.encryption' => $smtpEncryption,
                'mail.mailers.smtp.username' => $smtpUser,
                'mail.mailers.smtp.password' => $smtpPass,
                'mail.from.address' => $fromAddress,
                'mail.from.name' => $fromName,
            ]);

            app()->forgetInstance('mail.manager');

            $user = $request->user();
            \Illuminate\Support\Facades\Mail::to($recipient)
                ->send(new \App\Mail\SecurityAlertMail(
                    $user,
                    'SMTP Server Gateway Test',
                    'Your email server connection has been tested and verified from the UEW Digital Library Admin Settings panel.',
                    $request->ip()
                ));

            ActivityLog::record('SMTP_TEST_SUCCESS', $request->user(), null, ['recipient' => $recipient]);

            return back()->with('success', "Live SMTP test successfully dispatched to {$recipient}! Connection verified.");
        } catch (\Throwable $e) {
            ActivityLog::record('SMTP_TEST_FAILED', $request->user(), null, ['error' => $e->getMessage()]);

            return back()->with('error', "SMTP Connection Failed: " . $e->getMessage());
        }
    }

    public function clearCache(Request $request): RedirectResponse
    {
        Artisan::call('optimize:clear');

        ActivityLog::record('SYSTEM_CACHE_CLEARED', $request->user());

        return back()->with('success', 'Application configuration, route, and template caches have been flushed.');
    }
}
