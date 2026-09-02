<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Academic Settings
            [
                'key' => 'academic_year',
                'value' => '2023/2024',
                'type' => 'string',
                'group' => 'academic',
                'description' => 'Current active university academic year session.',
            ],
            [
                'key' => 'active_semester',
                'value' => 'FIRST',
                'type' => 'string',
                'group' => 'academic',
                'description' => 'Current academic semester (FIRST or SECOND).',
            ],
            [
                'key' => 'institution_name',
                'value' => 'University of Education, Winneba — School of Business',
                'type' => 'string',
                'group' => 'academic',
                'description' => 'Official institutional repository branding name.',
            ],

            // Storage & Upload Limits
            [
                'key' => 'max_upload_size_mb',
                'value' => '100',
                'type' => 'integer',
                'group' => 'storage',
                'description' => 'Maximum allowed file size in megabytes per upload.',
            ],
            [
                'key' => 'allowed_file_extensions',
                'value' => 'pdf, ppt, pptx, doc, docx, zip',
                'type' => 'string',
                'group' => 'storage',
                'description' => 'Permitted document extensions for lecture slides and question archives.',
            ],

            // Security & Gating
            [
                'key' => 'allow_student_registration',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Allow self-service registration for enrolled students.',
            ],
            [
                'key' => 'enforce_level_gating',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Enforce academic level prerequisites on material downloads.',
            ],
            [
                'key' => 'portal_maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'security',
                'description' => 'Temporarily restrict student access for scheduled catalog maintenance.',
            ],

            // Notifications
            [
                'key' => 'enable_email_alerts',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Dispatch SMTP email notifications for new lecture materials.',
            ],
            [
                'key' => 'auto_notify_new_resources',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Instantly alert enrolled students when relevant slides are uploaded.',
            ],
            [
                'key' => 'contact_email',
                'value' => 'library@uew.edu.gh',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'Administrative contact email for library support requests.',
            ],

            // SMTP Gateway & Email Server Settings
            [
                'key' => 'mail_mailer',
                'value' => 'smtp',
                'type' => 'string',
                'group' => 'email_smtp',
                'description' => 'Active mail driver (smtp or log).',
            ],
            [
                'key' => 'mail_host',
                'value' => 'smtp.gmail.com',
                'type' => 'string',
                'group' => 'email_smtp',
                'description' => 'SMTP relay hostname (e.g. smtp.gmail.com or smtppro.zoho.com).',
            ],
            [
                'key' => 'mail_port',
                'value' => '587',
                'type' => 'integer',
                'group' => 'email_smtp',
                'description' => 'SMTP port (587 for TLS, 465 for SSL).',
            ],
            [
                'key' => 'mail_encryption',
                'value' => 'tls',
                'type' => 'string',
                'group' => 'email_smtp',
                'description' => 'Transport layer encryption (tls or ssl).',
            ],
            [
                'key' => 'mail_username',
                'value' => env('MAIL_USERNAME', 'johnotchere282@gmail.com'),
                'type' => 'string',
                'group' => 'email_smtp',
                'description' => 'SMTP authenticating username or email address.',
            ],
            [
                'key' => 'mail_password',
                'value' => env('MAIL_PASSWORD', ''),
                'type' => 'string',
                'group' => 'email_smtp',
                'description' => 'SMTP authenticating password or App Password.',
            ],
            [
                'key' => 'mail_from_address',
                'value' => env('MAIL_FROM_ADDRESS', 'johnotchere282@gmail.com'),
                'type' => 'string',
                'group' => 'email_smtp',
                'description' => 'Default sender email address shown to recipients.',
            ],
            [
                'key' => 'mail_from_name',
                'value' => env('MAIL_FROM_NAME', 'UEW School of Business Digital Library'),
                'type' => 'string',
                'group' => 'email_smtp',
                'description' => 'Default sender display name.',
            ],
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
