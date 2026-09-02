<?php
/**
 * Export all UEW Library email templates to HTML files for browser preview.
 * Run: php tests/export_email_previews.php
 * Then open the generated files in your browser.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Mail\AdminBroadcastMail;
use App\Mail\SecurityAlertMail;
use App\Mail\WelcomeActivationMail;
use App\Models\User;

$outDir = __DIR__ . '/../storage/app/email-previews';
if (!is_dir($outDir)) {
    mkdir($outDir, 0755, true);
}

// Use first real user, or build a mock
$user = User::first() ?? new User([
    'first_name' => 'Kwame',
    'last_name'  => 'Mensah',
    'email'      => 'test@johnokyere.xyz',
    'student_id' => '5201040001',
    'role'       => 'student',
    'level'      => 'L300',
    'program'    => 'BSc. Business Information Systems (BIS)',
    'points'     => 150,
]);

$templates = [
    '1_welcome_activation' => new WelcomeActivationMail($user, 'Temp@Pass#2026'),
    '2_security_alert'     => new SecurityAlertMail(
        $user,
        'New Login Detected from Unfamiliar Device',
        'We noticed a sign-in to your UEW Library account from a new device. If this was you, no action is needed.',
        '196.200.55.102'
    ),
    '3_admin_broadcast'    => new AdminBroadcastMail(
        $user,
        'Mid-Semester Exam Timetable & Revision Slides Released',
        "Dear {$user->first_name},\n\nWe are pleased to inform you that Mid-Semester revision presentations (Weeks 7–8) and the official 2023/2024 examination timetable are now live in the UEW Digital Library catalog.\n\nLog in to access your course materials and start revising early!\n\nBest regards,\nUEW School of Business Library Team"
    ),
];

echo "Exporting email templates...\n\n";

foreach ($templates as $filename => $mailable) {
    $html = $mailable->render();
    $outPath = "{$outDir}/{$filename}.html";
    file_put_contents($outPath, $html);
    echo "✅ {$filename}.html — " . strlen($html) . " bytes\n";
    echo "   Open: file:///" . str_replace('\\', '/', $outPath) . "\n\n";
}

echo "Done! Open the above files in your browser to preview all email designs.\n";
