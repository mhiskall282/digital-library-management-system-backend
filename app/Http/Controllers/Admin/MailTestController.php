<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminBroadcastMail;
use App\Mail\SecurityAlertMail;
use App\Mail\WelcomeActivationMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class MailTestController extends Controller
{
    public function index(Request $request): View
    {
        $template = $request->input('template', 'welcome');
        $user = $request->user();

        $previewHtml = match ($template) {
            'welcome' => (new WelcomeActivationMail($user, 'TempPass#2026!'))->render(),
            'security' => (new SecurityAlertMail($user, 'Account Security Alert', 'A login from a new device was registered.', $request->ip()))->render(),
            'broadcast' => (new AdminBroadcastMail($user, 'Academic Timetable Update', 'Lecture slides and past exam revisions for the current academic session are now active.'))->render(),
            default => 'Select a template to preview.',
        };

        return view('admin.mail.index', compact('template', 'previewHtml'));
    }

    public function sendTest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'template' => ['required', 'in:welcome,security,broadcast'],
            'recipient' => ['required', 'email'],
        ]);

        $recipientEmail = $validated['recipient'];
        $user = $request->user();

        $mailable = match ($validated['template']) {
            'welcome' => new WelcomeActivationMail($user, 'SampleTempPass#2026'),
            'security' => new SecurityAlertMail($user, 'Account Security Test', 'This is a test security alert verification from UEW Library.', $request->ip()),
            'broadcast' => new AdminBroadcastMail($user, 'Test Institutional Broadcast', 'This is a sample administrative broadcast test dispatch.'),
        };

        try {
            Mail::to($recipientEmail)->send($mailable);
            return back()->with('success', "Test email for '{$validated['template']}' template sent successfully to {$recipientEmail}!");
        } catch (\Throwable $e) {
            return back()->with('error', "Failed to dispatch email via SMTP: {$e->getMessage()}");
        }
    }
}
