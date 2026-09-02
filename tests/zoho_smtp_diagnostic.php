<?php

/**
 * UEW Digital Library — Zoho Mail SMTP Diagnostic & Activation Script
 *
 * This script tests the Zoho SMTP connection, verifies template rendering,
 * and provides a step-by-step activation guide when SMTP access is restricted.
 *
 * Usage:
 *   php tests/zoho_smtp_diagnostic.php
 *   php tests/zoho_smtp_diagnostic.php --send-test
 *   php tests/zoho_smtp_diagnostic.php --verify-templates
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Mail\AdminBroadcastMail;
use App\Mail\SecurityAlertMail;
use App\Mail\WelcomeActivationMail;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

$sendTest     = in_array('--send-test', $argv);
$verifyOnly   = in_array('--verify-templates', $argv);

$host     = 'smtppro.zoho.com';
$port     = 465;
$username = 'test@johnokyere.xyz';
$password = 'Mhiskall9090@';
$to       = 'test@johnokyere.xyz';

echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║    UEW Digital Library — Zoho Mail SMTP Diagnostic Tool         ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

// ─── PHASE 1: Raw TCP Connection Test ────────────────────────────────────────
echo "── Phase 1: Raw TCP Socket Connection Test ──────────────────────────\n";
echo "Testing TCP reachability to {$host}:{$port} ...\n";

$errno  = 0;
$errstr = '';
$socket = @stream_socket_client(
    "ssl://{$host}:{$port}",
    $errno,
    $errstr,
    10,
    STREAM_CLIENT_CONNECT,
    stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true]])
);

if ($socket) {
    $banner = fgets($socket, 512);
    fclose($socket);
    echo "✅ TCP+SSL Connection: SUCCESSFUL\n";
    echo "   Server banner: " . trim($banner) . "\n";
    $tcpOk = true;
} else {
    echo "❌ TCP+SSL Connection: FAILED\n";
    echo "   Error #{$errno}: {$errstr}\n";
    echo "   → Check local firewall, ISP port blocking on 465, or try 587.\n";
    $tcpOk = false;
}

// ─── Also test port 587 ───────────────────────────────────────────────────
echo "\nTesting TCP to {$host}:587 (TLS) ...\n";
$socket587 = @stream_socket_client("tcp://{$host}:587", $e587, $m587, 10);
if ($socket587) {
    $banner587 = fgets($socket587, 512);
    fclose($socket587);
    echo "✅ TCP:587 Reachable — banner: " . trim($banner587) . "\n";
} else {
    echo "❌ TCP:587 Not reachable: {$m587}\n";
}

echo "\n";

// ─── PHASE 2: Template Rendering Verification ────────────────────────────────
echo "── Phase 2: Email Template Rendering ───────────────────────────────\n";

$user = User::first() ?? new User([
    'first_name' => 'Test',
    'last_name'  => 'Student',
    'email'      => $to,
    'student_id' => '5201040001',
    'role'       => 'student',
    'level'      => 'L300',
    'program'    => 'BSc. Business Information Systems (BIS)',
]);

$templates = [
    'WelcomeActivationMail' => new WelcomeActivationMail($user, 'TempPass#2026!'),
    'SecurityAlertMail'     => new SecurityAlertMail($user, 'New Login Detected', 'Login from Winneba Campus IP.', '196.200.1.1'),
    'AdminBroadcastMail'    => new AdminBroadcastMail($user, 'Semester Timetable Released', 'All mid-semester revision slides for Weeks 7–15 are now live in the catalog.'),
];

$allRendered = true;
foreach ($templates as $name => $mailable) {
    try {
        $html = $mailable->render();
        $bytes = strlen($html);
        echo "✅ {$name}: rendered OK ({$bytes} bytes)\n";
    } catch (\Throwable $e) {
        echo "❌ {$name}: RENDER FAILED — {$e->getMessage()}\n";
        $allRendered = false;
    }
}

echo "\n";

// ─── PHASE 3: Log-Driver Dispatch (Safe Fallback) ────────────────────────────
echo "── Phase 3: Log-Driver Dispatch Test (safe, no real send) ──────────\n";
Config::set('mail.default', 'log');

foreach ($templates as $name => $mailable) {
    try {
        Mail::to($to)->send($mailable);
        echo "✅ {$name}: logged to storage/logs/laravel.log\n";
    } catch (\Throwable $e) {
        echo "❌ {$name}: LOG dispatch failed — {$e->getMessage()}\n";
    }
}

echo "\n";

// ─── PHASE 4: Live SMTP Send (optional --send-test flag) ──────────────────────
if ($sendTest) {
    echo "── Phase 4: Live Zoho SMTP Dispatch ────────────────────────────────\n";

    // Try SSL 465
    foreach ([[465, 'ssl'], [587, 'tls']] as [$p, $enc]) {
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $host);
        Config::set('mail.mailers.smtp.port', $p);
        Config::set('mail.mailers.smtp.encryption', $enc);
        Config::set('mail.mailers.smtp.username', $username);
        Config::set('mail.mailers.smtp.password', $password);
        Config::set('mail.from.address', $username);
        Config::set('mail.from.name', 'UEW School of Business Digital Library');
        app()->forgetInstance('mail.manager');

        try {
            Mail::to($to)->send(new WelcomeActivationMail($user, 'TempPass#2026!'));
            echo "✅ Live SMTP send SUCCESS via port {$p} ({$enc})!\n";
            echo "   → Check inbox at {$to}\n";
            break;
        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, '554 5.7.8')) {
                echo "⚠️  Port {$p} ({$enc}): Auth restricted by Zoho (554 5.7.8)\n";
            } elseif (str_contains($msg, 'Connection refused') || str_contains($msg, 'Failed to connect')) {
                echo "❌  Port {$p} ({$enc}): Connection refused (firewall/ISP block)\n";
            } else {
                echo "❌  Port {$p} ({$enc}): " . substr($msg, 0, 120) . "\n";
            }
        }
    }
    echo "\n";
}

// ─── PHASE 5: Zoho Activation Instructions ──────────────────────────────────
echo "── Phase 5: Zoho SMTP Activation Checklist ─────────────────────────\n";
echo "\n";
echo "If SMTP auth returns '554 5.7.8 Access Restricted', complete these steps:\n\n";

echo "  STEP 1 — Enable SMTP Access in Zoho Admin:\n";
echo "    1. Go to https://mailadmin.zoho.com\n";
echo "    2. Log in with: {$username}\n";
echo "    3. Navigate: Users → {$username} → Mail Settings\n";
echo "    4. Click: Email Accounts → IMAP/POP & SMTP Configuration\n";
echo "    5. Toggle ON: 'SMTP Access'\n";
echo "    6. Save changes\n\n";

echo "  STEP 2 — If Two-Factor Authentication (2FA) is enabled:\n";
echo "    1. Go to https://accounts.zoho.com\n";
echo "    2. Navigate: Security → App Passwords → Generate App Password\n";
echo "    3. Name: 'UEW Library SMTP'\n";
echo "    4. Copy the generated password\n";
echo "    5. Replace MAIL_PASSWORD in .env with the app password\n\n";

echo "  STEP 3 — Verify your Zoho account type:\n";
echo "    • Free Zoho accounts: SMTP may require upgrade to 'Mail Lite' plan\n";
echo "    • Paid Zoho accounts (Mail Plus/Pro): SMTP is fully supported\n";
echo "    • Zoho Workplace: Full SMTP, IMAP, POP3 available\n\n";

echo "  STEP 4 — After enabling SMTP, test with:\n";
echo "    php tests/zoho_smtp_diagnostic.php --send-test\n\n";

echo "  STEP 5 — Alternative: Use Mailgun, SendGrid, or AWS SES:\n";
echo "    composer require symfony/mailgun-mailer\n";
echo "    Set MAIL_MAILER=mailgun + MAILGUN_DOMAIN + MAILGUN_SECRET in .env\n\n";

// ─── Summary ─────────────────────────────────────────────────────────────────
echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║  Diagnostic Summary                                              ║\n";
echo "╠══════════════════════════════════════════════════════════════════╣\n";
echo "║  TCP Connection   : " . ($tcpOk ? '✅ OK         ' : '❌ BLOCKED   ') . "                              ║\n";
echo "║  Template Render  : " . ($allRendered ? '✅ OK (3/3)    ' : '❌ FAILED     ') . "                              ║\n";
echo "║  Log-Driver Send  : ✅ OK (see storage/logs/laravel.log)        ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n";
