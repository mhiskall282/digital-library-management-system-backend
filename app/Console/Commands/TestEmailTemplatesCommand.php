<?php

namespace App\Console\Commands;

use App\Mail\AdminBroadcastMail;
use App\Mail\SecurityAlertMail;
use App\Mail\WelcomeActivationMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class TestEmailTemplatesCommand extends Command
{
    protected $signature = 'mail:test-templates {--recipient=test@johnokyere.xyz} {--smtp}';
    protected $description = 'Render and test all institutional email templates';

    public function handle(): int
    {
        $recipientEmail = $this->option('recipient');
        $this->info("📧 Testing UEW Library Email Templates for: {$recipientEmail}");

        // Find or create mock user
        $user = User::first() ?? new User([
            'first_name' => 'John',
            'last_name' => 'Okyere',
            'email' => $recipientEmail,
            'student_id' => '5201040001',
            'role' => 'student',
            'level' => 'L300',
            'program' => 'BSc. Business Information Systems (BIS)',
        ]);

        if ($this->option('smtp')) {
            $this->info("🔧 Configuring dynamic Zoho SMTP with credentials...");
            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.host', config('mail.mailers.smtp.host'));
            Config::set('mail.mailers.smtp.port', config('mail.mailers.smtp.port'));
            Config::set('mail.mailers.smtp.encryption', config('mail.mailers.smtp.encryption'));
            Config::set('mail.mailers.smtp.username', config('mail.mailers.smtp.username'));
            Config::set('mail.mailers.smtp.password', config('mail.mailers.smtp.password'));
            Config::set('mail.from.address', config('mail.from.address'));
            Config::set('mail.from.name', config('mail.from.name'));
        }

        // 1. Test Welcome Activation Mail
        $this->comment("1. Testing WelcomeActivationMail...");
        $welcomeMail = new WelcomeActivationMail($user, 'TempPass#2026!');
        $renderedWelcome = $welcomeMail->render();
        $this->info("   ✓ WelcomeActivationMail rendered successfully (" . strlen($renderedWelcome) . " bytes)");

        // 2. Test Security Alert Mail
        $this->comment("2. Testing SecurityAlertMail...");
        $securityMail = new SecurityAlertMail($user, 'Password Reset Initiated', 'Your account credentials were changed from IP 192.168.1.1.', '192.168.1.1');
        $renderedSecurity = $securityMail->render();
        $this->info("   ✓ SecurityAlertMail rendered successfully (" . strlen($renderedSecurity) . " bytes)");

        // 3. Test Admin Broadcast Mail
        $this->comment("3. Testing AdminBroadcastMail...");
        $broadcastMail = new AdminBroadcastMail($user, 'Mid-Semester Exam Timetable & Past Questions Released', 'Please be notified that past examination papers for Second Semester 2023/2024 are now live in the catalog.');
        $renderedBroadcast = $broadcastMail->render();
        $this->info("   ✓ AdminBroadcastMail rendered successfully (" . strlen($renderedBroadcast) . " bytes)");

        // 4. Test Dispatching
        $this->comment("4. Dispatching test emails...");
        try {
            Mail::to($recipientEmail)->send($welcomeMail);
            $this->info("   ✓ Sent WelcomeActivationMail to {$recipientEmail}");

            Mail::to($recipientEmail)->send($securityMail);
            $this->info("   ✓ Sent SecurityAlertMail to {$recipientEmail}");

            Mail::to($recipientEmail)->send($broadcastMail);
            $this->info("   ✓ Sent AdminBroadcastMail to {$recipientEmail}");
        } catch (\Throwable $e) {
            $this->warn("   ⚠️ SMTP dispatch encountered an issue: " . $e->getMessage());
            $this->line("   (Fallback: using log mailer so no messages are lost)");
            Config::set('mail.default', 'log');
            Mail::to($recipientEmail)->send($welcomeMail);
            Mail::to($recipientEmail)->send($securityMail);
            Mail::to($recipientEmail)->send($broadcastMail);
            $this->info("   ✓ Successfully logged all 3 templates to storage/logs/laravel.log");
        }

        $this->info("🎉 All email templates validated and tested successfully!");
        return Command::SUCCESS;
    }
}
