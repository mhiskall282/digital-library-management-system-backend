<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminBroadcastMail;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class BroadcastController extends Controller
{
    public function create(): View
    {
        $programs = [
            'BSc. Business Information Systems (BIS)',
            'BSc. Banking and Finance',
            'BSc. Accounting',
            'BBA. Marketing',
            'BBA. Human Resource Management',
            'BSc. Procurement and Supply Chain Management',
        ];

        return view('admin.broadcasts.create', compact('programs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'target_type' => ['required', 'in:ALL,LEVEL,PROGRAM'],
            'target_level' => ['nullable', 'required_if:target_type,LEVEL', 'in:L100,L200,L300,L400,MASTERS,PHD'],
            'target_program' => ['nullable', 'required_if:target_type,PROGRAM', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        $query = User::where('role', 'student')->where('is_active', true);

        if ($validated['target_type'] === 'LEVEL') {
            $query->where('level', $validated['target_level']);
        } elseif ($validated['target_type'] === 'PROGRAM') {
            $query->where('program', 'like', "%{$validated['target_program']}%");
        }

        $recipients = $query->get();

        if ($recipients->isEmpty()) {
            return back()->with('error', 'No active students matched the selected target audience.');
        }

        $sendEmail = $request->boolean('send_email');

        foreach ($recipients as $recipient) {
            // 1. In-App Notification
            Notification::create([
                'user_id' => $recipient->id,
                'type' => 'SYSTEM',
                'title' => "📢 {$validated['title']}",
                'message' => $validated['message'],
                'is_read' => false,
            ]);

            // 2. Email Notification
            if ($sendEmail && $recipient->email_notifications) {
                try {
                    Mail::to($recipient->email)->queue(new AdminBroadcastMail(
                        recipient: $recipient,
                        subjectTitle: $validated['title'],
                        content: $validated['message']
                    ));
                } catch (\Throwable $e) {
                    // Ignore mailer error in local sandbox
                }
            }
        }

        ActivityLog::record('BROADCAST_SENT', $request->user(), null, [
            'target_type' => $validated['target_type'],
            'title' => $validated['title'],
            'recipient_count' => $recipients->count(),
            'email_sent' => $sendEmail,
        ]);

        return redirect()->route('admin.dashboard')->with('success', "Broadcast announcement successfully transmitted to {$recipients->count()} scholars!");
    }
}
