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
        ];

        foreach ($defaults as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
