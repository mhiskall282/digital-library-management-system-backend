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

        ActivityLog::record('SETTINGS_UPDATE', $request->user(), null, [
            'updated_by' => $request->user()->name,
        ]);

        return back()->with('success', 'Library operational parameters successfully updated!');
    }

    public function clearCache(Request $request): RedirectResponse
    {
        Artisan::call('optimize:clear');

        ActivityLog::record('SYSTEM_CACHE_CLEARED', $request->user());

        return back()->with('success', 'Application configuration, route, and template caches have been flushed.');
    }
}
