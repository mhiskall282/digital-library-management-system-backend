<?php

namespace Database\Seeders;

use App\Mail\AdminBroadcastMail;
use App\Mail\SecurityAlertMail;
use App\Mail\WelcomeActivationMail;
use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class EmailLogSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('role', 'student')->first() ?? User::first();
        if (!$user) return;

        // 1. Welcome Activation
        $welcome = new WelcomeActivationMail($user, 'TempPass#2026!');
        EmailLog::create([
            'direction' => 'outgoing',
            'mailer' => 'simulated',
            'template' => 'welcome',
            'recipient' => $user->email,
            'sender' => 'test@johnokyere.xyz',
            'subject' => 'Welcome to the UEW School of Business Digital Library',
            'body_html' => $welcome->render(),
            'status' => 'simulated',
            'created_at' => now()->subHours(2),
        ]);

        // 2. Security Alert
        $alert = new SecurityAlertMail($user, 'New Login Location Detected', 'Sign-in from Winneba Campus Network', '196.200.1.1');
        EmailLog::create([
            'direction' => 'outgoing',
            'mailer' => 'smtp',
            'template' => 'security',
            'recipient' => $user->email,
            'sender' => 'test@johnokyere.xyz',
            'subject' => 'Security Alert: Account Activity Notice',
            'body_html' => $alert->render(),
            'status' => 'simulated',
            'error_message' => '554 5.7.8 Access Restricted (auto-archived to In-App Mailbox)',
            'created_at' => now()->subHour(),
        ]);

        // 3. Admin Broadcast
        $broadcast = new AdminBroadcastMail($user, 'Mid-Semester Exam Timetable & Revision Presentations', 'All revision slide archives for Weeks 7-15 are now live in the catalog.');
        EmailLog::create([
            'direction' => 'outgoing',
            'mailer' => 'simulated',
            'template' => 'broadcast',
            'recipient' => 'all-students@uew.edu.gh',
            'sender' => 'test@johnokyere.xyz',
            'subject' => 'UEW Library Announcement: Mid-Semester Timetable',
            'body_html' => $broadcast->render(),
            'status' => 'simulated',
            'created_at' => now()->subMinutes(30),
        ]);

        // 4. Inbound Student Inquiry
        EmailLog::create([
            'direction' => 'incoming',
            'mailer' => 'simulated',
            'template' => 'inquiry',
            'recipient' => 'test@johnokyere.xyz',
            'sender' => 'student.mensah@uew.edu.gh',
            'subject' => 'Question regarding BNF 311 Week 8 Slide Approval',
            'body_html' => '<p>Good day,<br>I submitted the Week 8 financial modeling slide deck yesterday for review. Could you please let me know when it will be published to the catalog?<br><br>Thanks,<br>Kwame Mensah</p>',
            'status' => 'received',
            'created_at' => now()->subMinutes(10),
        ]);
    }
}
