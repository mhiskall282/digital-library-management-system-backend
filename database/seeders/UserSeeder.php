<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. School of Business Chief Librarian (Admin)
        User::updateOrCreate(
            ['email' => 'admin@uew.edu.gh'],
            [
                'student_id' => 'ADMIN-001',
                'first_name' => 'Chief',
                'last_name' => 'Librarian',
                'password' => Hash::make('admin1234'),
                'level' => 'PHD',
                'program' => 'School of Business Administration',
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
                'email_notifications' => true,
                'new_resource_alerts' => true,
            ]
        );

        // 2. Demo Student: Kwame Mensah (L300 Accounting)
        User::updateOrCreate(
            ['email' => 'student@st.uew.edu.gh'],
            [
                'student_id' => '5201040001',
                'first_name' => 'Kwame',
                'last_name' => 'Mensah',
                'password' => Hash::make('student1234'),
                'level' => 'L300',
                'program' => 'BSc. Accounting',
                'role' => 'student',
                'is_active' => true,
                'email_verified_at' => now(),
                'email_notifications' => true,
                'new_resource_alerts' => true,
            ]
        );

        // 3. Demo Student: Ama Osei (L200 Marketing)
        User::updateOrCreate(
            ['email' => 'ama.osei@st.uew.edu.gh'],
            [
                'student_id' => '5201040002',
                'first_name' => 'Ama',
                'last_name' => 'Osei',
                'password' => Hash::make('student1234'),
                'level' => 'L200',
                'program' => 'BBA. Marketing',
                'role' => 'student',
                'is_active' => true,
                'email_verified_at' => now(),
                'email_notifications' => true,
                'new_resource_alerts' => true,
            ]
        );
    }
}
