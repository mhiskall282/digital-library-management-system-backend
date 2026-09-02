<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Mail\AdminBroadcastMail;
use App\Mail\SecurityAlertMail;
use App\Mail\WelcomeActivationMail;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

$user = User::first();
$to = 'test@johnokyere.xyz';

$configurations = [
    [
        'name' => 'Zoho Pro SSL (465)',
        'host' => 'smtppro.zoho.com',
        'port' => 465,
        'encryption' => 'ssl',
    ],
    [
        'name' => 'Zoho Pro TLS (587)',
        'host' => 'smtppro.zoho.com',
        'port' => 587,
        'encryption' => 'tls',
    ],
    [
        'name' => 'Zoho Standard SSL (465)',
        'host' => 'smtp.zoho.com',
        'port' => 465,
        'encryption' => 'ssl',
    ],
    [
        'name' => 'Zoho Standard TLS (587)',
        'host' => 'smtp.zoho.com',
        'port' => 587,
        'encryption' => 'tls',
    ],
    [
        'name' => 'Zoho EU SSL (465)',
        'host' => 'smtppro.zoho.eu',
        'port' => 465,
        'encryption' => 'ssl',
    ],
    [
        'name' => 'Zoho EU TLS (587)',
        'host' => 'smtppro.zoho.eu',
        'port' => 587,
        'encryption' => 'tls',
    ],
];

$success = false;

foreach ($configurations as $cfg) {
    echo "----------------------------------------\n";
    echo "Testing {$cfg['name']}: {$cfg['host']}:{$cfg['port']} ({$cfg['encryption']})\n";

    Config::set('mail.default', 'smtp');
    Config::set('mail.mailers.smtp.host', $cfg['host']);
    Config::set('mail.mailers.smtp.port', $cfg['port']);
    Config::set('mail.mailers.smtp.encryption', $cfg['encryption']);
    Config::set('mail.mailers.smtp.username', 'test@johnokyere.xyz');
    Config::set('mail.mailers.smtp.password', 'Mhiskall9090@');
    Config::set('mail.from.address', 'test@johnokyere.xyz');
    Config::set('mail.from.name', 'UEW School of Business Digital Library');

    // Purge cached mail manager transport
    app()->forgetInstance('mail.manager');

    try {
        echo "Attempting to send WelcomeActivationMail to {$to}...\n";
        Mail::to($to)->send(new WelcomeActivationMail($user, 'Mhiskall9090@'));
        echo "✅ SUCCESS! Email delivered via {$cfg['name']}!\n";

        echo "Attempting to send SecurityAlertMail to {$to}...\n";
        Mail::to($to)->send(new SecurityAlertMail($user, 'Login Notification', 'Logged in from Winneba Campus Network', '127.0.0.1'));
        echo "✅ SUCCESS! SecurityAlertMail delivered!\n";

        echo "Attempting to send AdminBroadcastMail to {$to}...\n";
        Mail::to($to)->send(new AdminBroadcastMail($user, 'Semester Exam Timetable & Revision Modules', 'Please review the lecture slides for Weeks 1 to 15 in the catalog.'));
        echo "✅ SUCCESS! AdminBroadcastMail delivered!\n";

        $success = true;
        // Update .env with winning configuration
        $envFile = __DIR__ . '/../.env';
        $envContent = file_get_contents($envFile);
        $envContent = preg_replace('/MAIL_HOST=.*/', 'MAIL_HOST=' . $cfg['host'], $envContent);
        $envContent = preg_replace('/MAIL_PORT=.*/', 'MAIL_PORT=' . $cfg['port'], $envContent);
        $envContent = preg_replace('/MAIL_ENCRYPTION=.*/', 'MAIL_ENCRYPTION=' . $cfg['encryption'], $envContent);
        file_put_contents($envFile, $envContent);
        echo "Saved optimal SMTP config ({$cfg['host']}:{$cfg['port']}, {$cfg['encryption']}) to .env\n";
        break;
    } catch (\Throwable $e) {
        echo "❌ FAILED: " . $e->getMessage() . "\n";
    }
}

if (!$success) {
    echo "\n⚠️ Note: If network port 465/587 is blocked on this local Windows machine or if Zoho requires Application-Specific Password / 2FA or account unblocking, test with log driver or check credentials.\n";
}
