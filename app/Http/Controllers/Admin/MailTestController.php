<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminBroadcastMail;
use App\Mail\SecurityAlertMail;
use App\Mail\WelcomeActivationMail;
use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class MailTestController extends Controller
{
    public function index(Request $request): View
    {
        $template = $request->input('template', 'welcome');
        $tab = $request->input('tab', 'preview'); // preview, mailbox, incoming
        $user = $request->user();

        // Generate template preview
        $previewHtml = match ($template) {
            'welcome' => (new WelcomeActivationMail($user, 'TempPass#2026!'))->render(),
            'security' => (new SecurityAlertMail($user, 'Account Security Alert', 'A login from a new device was registered.', $request->ip()))->render(),
            'broadcast' => (new AdminBroadcastMail($user, 'Academic Timetable Update', 'Lecture slides and past exam revisions for the current academic session are now active.'))->render(),
            default => 'Select a template to preview.',
        };

        // Fetch recorded/simulated email logs
        $logs = EmailLog::latest()->take(25)->get();
        $outgoingCount = EmailLog::outgoing()->count();
        $incomingCount = EmailLog::incoming()->count();

        return view('admin.mail.index', compact(
            'template',
            'tab',
            'previewHtml',
            'logs',
            'outgoingCount',
            'incomingCount'
        ));
    }

    public function sendTest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'template' => ['required', 'in:welcome,security,broadcast'],
            'recipient' => ['required', 'email'],
            'mode' => ['required', 'in:smtp,simulate'],
        ]);

        $recipientEmail = $validated['recipient'];
        $mode = $validated['mode'];
        $user = $request->user();

        $mailable = match ($validated['template']) {
            'welcome' => new WelcomeActivationMail($user, 'SampleTempPass#2026'),
            'security' => new SecurityAlertMail($user, 'Account Security Test', 'This is a test security alert verification from UEW Library.', $request->ip()),
            'broadcast' => new AdminBroadcastMail($user, 'Test Institutional Broadcast', 'This is a sample administrative broadcast test dispatch.'),
        };

        $subject = match ($validated['template']) {
            'welcome' => 'Welcome to the UEW School of Business Digital Library',
            'security' => 'Security Alert: Account Activity Notice',
            'broadcast' => 'UEW Library Announcement: Test Broadcast',
        };

        $bodyHtml = $mailable->render();
        $sender = config('mail.from.address') ?? 'test@johnokyere.xyz';

        if ($mode === 'simulate') {
            // Instant simulation mode
            EmailLog::create([
                'direction' => 'outgoing',
                'mailer' => 'simulated',
                'template' => $validated['template'],
                'recipient' => $recipientEmail,
                'sender' => $sender,
                'subject' => $subject,
                'body_html' => $bodyHtml,
                'status' => 'simulated',
                'metadata' => [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'dispatched_by' => $user->id,
                ],
            ]);

            return redirect()->route('admin.mail.index', ['template' => $validated['template'], 'tab' => 'mailbox'])
                ->with('success', "Simulated email [{$subject}] successfully dispatched to {$recipientEmail}! Check your In-App Mailbox below.");
        }

        // Live SMTP attempt
        try {
            Mail::to($recipientEmail)->send($mailable);

            EmailLog::create([
                'direction' => 'outgoing',
                'mailer' => 'smtp',
                'template' => $validated['template'],
                'recipient' => $recipientEmail,
                'sender' => $sender,
                'subject' => $subject,
                'body_html' => $bodyHtml,
                'status' => 'delivered',
                'metadata' => [
                    'ip' => $request->ip(),
                    'smtp_host' => config('mail.mailers.smtp.host'),
                ],
            ]);

            return redirect()->route('admin.mail.index', ['template' => $validated['template'], 'tab' => 'mailbox'])
                ->with('success', "Test email for '{$validated['template']}' template sent via live SMTP to {$recipientEmail}!");
        } catch (\Throwable $e) {
            // Auto-archive as fallback with diagnostic explanation
            EmailLog::create([
                'direction' => 'outgoing',
                'mailer' => 'smtp',
                'template' => $validated['template'],
                'recipient' => $recipientEmail,
                'sender' => $sender,
                'subject' => $subject,
                'body_html' => $bodyHtml,
                'status' => 'simulated',
                'error_message' => $e->getMessage(),
                'metadata' => [
                    'ip' => $request->ip(),
                    'failed_mode' => 'smtp',
                ],
            ]);

            $friendlyMsg = str_contains($e->getMessage(), '554 5.7.8')
                ? "Live Zoho SMTP returned '554 5.7.8 (Access Restricted)'. The message has been archived in the In-App Mailbox below so you can inspect and verify it!"
                : "SMTP attempt failed: " . substr($e->getMessage(), 0, 100) . ". Saved to In-App Mailbox.";

            return redirect()->route('admin.mail.index', ['template' => $validated['template'], 'tab' => 'mailbox'])
                ->with('warning', $friendlyMsg);
        }
    }

    public function simulateIncoming(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sender' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $bodyHtml = nl2br(e($validated['message']));
        $libraryEmail = config('mail.from.address') ?? 'library@uew.edu.gh';

        EmailLog::create([
            'direction' => 'incoming',
            'mailer' => 'simulated',
            'template' => 'incoming_inquiry',
            'recipient' => $libraryEmail,
            'sender' => $validated['sender'],
            'subject' => $validated['subject'],
            'body_html' => $bodyHtml,
            'status' => 'received',
            'metadata' => [
                'ip' => $request->ip(),
                'simulated_at' => now()->toIso8601String(),
            ],
        ]);

        return redirect()->route('admin.mail.index', ['tab' => 'mailbox'])
            ->with('success', "Incoming email simulated from {$validated['sender']}! Displayed in In-App Mailbox.");
    }

    public function showLog(EmailLog $emailLog): JsonResponse
    {
        return response()->json([
            'id' => $emailLog->id,
            'direction' => $emailLog->direction,
            'status' => $emailLog->status,
            'recipient' => $emailLog->recipient,
            'sender' => $emailLog->sender,
            'subject' => $emailLog->subject,
            'created_at' => $emailLog->created_at->format('M d, Y h:i A'),
            'body_html' => $emailLog->body_html,
            'error_message' => $emailLog->error_message,
        ]);
    }

    public function clearLogs(): RedirectResponse
    {
        EmailLog::truncate();
        return redirect()->route('admin.mail.index', ['tab' => 'mailbox'])->with('success', 'Email logs cleared successfully.');
    }
}
