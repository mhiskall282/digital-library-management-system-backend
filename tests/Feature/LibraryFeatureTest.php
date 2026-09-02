<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DownloadRequest;
use App\Models\MaterialRequest;
use App\Models\Resource;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ResourceSeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LibraryFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SettingSeeder::class);
        $this->seed(UserSeeder::class);
        $this->seed(CategorySeeder::class);
        $this->seed(ResourceSeeder::class);
    }

    public function test_health_check_endpoint(): void
    {
        $response = $this->get('/health');
        $response->assertStatus(200)
                 ->assertJson(['status' => 'healthy']);
    }

    public function test_public_landing_page_loads_for_guests(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200)
                 ->assertSee('UEW')
                 ->assertSee('School of Business')
                 ->assertSee('Academic Programs');
    }

    public function test_programs_directory_loads(): void
    {
        $response = $this->get('/programs');
        $response->assertStatus(200)
                 ->assertSee('Academic Programs & Level Directory')
                 ->assertSee('L100');
    }

    public function test_login_screen_has_no_exposed_credentials(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200)
                 ->assertDontSee('Demo Credentials for Testing')
                 ->assertDontSee('Pass: admin1234')
                 ->assertDontSee('Pass: student1234');
    }

    public function test_student_login_flow(): void
    {
        $response = $this->post('/login', [
            'login' => 'student@st.uew.edu.gh',
            'password' => 'student1234',
        ]);

        $response->assertRedirect('/student/hub');
        $this->assertAuthenticated();
    }

    public function test_student_registration_flow_and_welcome_email_logging(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Abena',
            'last_name' => 'Osei',
            'student_id' => '5201049999',
            'email' => 'abena.osei@st.uew.edu.gh',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'level' => 'L200',
            'program' => 'BSc. Business Information Systems (BIS)',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'abena.osei@st.uew.edu.gh',
            'student_id' => '5201049999',
            'program' => 'BSc. Business Information Systems (BIS)',
        ]);
        $this->assertDatabaseHas('email_logs', [
            'direction' => 'outgoing',
            'recipient' => 'abena.osei@st.uew.edu.gh',
            'template' => 'welcome',
        ]);
    }

    public function test_student_can_view_student_hub(): void
    {
        $student = User::where('email', 'student@st.uew.edu.gh')->first();

        $response = $this->actingAs($student)->get('/student/hub');
        $response->assertStatus(200)
                 ->assertSee('Welcome back, Kwame!')
                 ->assertSee('L300')
                 ->assertSee('Study Desk');
    }

    public function test_student_can_view_catalog(): void
    {
        $student = User::where('email', 'student@st.uew.edu.gh')->first();

        $response = $this->actingAs($student)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_student_can_contribute_material_for_moderation(): void
    {
        Storage::fake('public');
        $student = User::where('email', 'student@st.uew.edu.gh')->first();
        $category = Category::first();

        $file = UploadedFile::fake()->create('sample_notes.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($student)->post('/student/contribute', [
            'title' => 'Student Compiled Examination Prep Notes',
            'type' => 'SLIDE',
            'category_id' => $category->id,
            'level' => 'L300',
            'academic_year' => '2023/2024',
            'file' => $file,
        ]);

        $response->assertRedirect('/student/hub');

        $this->assertDatabaseHas('resources', [
            'title' => 'Student Compiled Examination Prep Notes',
            'status' => 'PENDING_REVIEW',
            'uploaded_by' => $student->id,
        ]);
    }

    public function test_admin_can_approve_submission_and_points_are_awarded(): void
    {
        $student = User::where('email', 'student@st.uew.edu.gh')->first();
        $initialPoints = $student->contributor_points;

        $resource = Resource::create([
            'title' => 'Sample Pending Exam Questions',
            'type' => 'PAST_QUESTION',
            'status' => 'PENDING_REVIEW',
            'category_id' => Category::first()->id,
            'level' => 'L300',
            'academic_year' => '2023/2024',
            'file_name' => 'exam.pdf',
            'file_path' => 'resources/exam.pdf',
            'file_size' => 1024,
            'uploaded_by' => $student->id,
        ]);

        $admin = User::where('email', 'admin@uew.edu.gh')->first();

        $response = $this->actingAs($admin)->post("/admin/moderation/{$resource->id}/approve");
        $response->assertStatus(302);

        $resource->refresh();
        $this->assertEquals('APPROVED', $resource->status);

        $student->refresh();
        $this->assertEquals($initialPoints + 50, $student->contributor_points);
    }

    public function test_download_approval_request_flow(): void
    {
        Setting::set('require_download_approval', true);
        $student = User::where('email', 'student@st.uew.edu.gh')->first();
        $resource = Resource::approved()->first();

        $response = $this->actingAs($student)->post("/resources/{$resource->id}/request-download", [
            'reason' => 'Mid-semester revision for exam prep.',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('download_requests', [
            'user_id' => $student->id,
            'resource_id' => $resource->id,
            'status' => 'PENDING',
        ]);
    }

    public function test_material_request_desk_flow(): void
    {
        $student = User::where('email', 'student@st.uew.edu.gh')->first();

        $response = $this->actingAs($student)->post('/requests', [
            'course_code' => 'BNF 211',
            'course_name' => 'Banking Operations',
            'program' => 'BSc. Banking and Finance',
            'level' => 'L200',
            'topic' => 'Need Week 3 Credit Analysis Slides',
            'type' => 'SLIDE',
            'urgency' => 'HIGH',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('material_requests', [
            'course_code' => 'BNF 211',
            'user_id' => $student->id,
            'status' => 'OPEN',
        ]);
    }

    public function test_admin_can_broadcast_announcement(): void
    {
        $admin = User::where('email', 'admin@uew.edu.gh')->first();

        $response = $this->actingAs($admin)->post('/admin/broadcasts', [
            'target_type' => 'ALL',
            'title' => 'Mid-Semester Exam Archives Uploaded',
            'message' => 'All students may now review past question papers.',
        ]);

        $response->assertRedirect('/admin');
    }

    public function test_admin_bulk_import_sample_csv_download(): void
    {
        $admin = User::where('email', 'admin@uew.edu.gh')->first();

        $response = $this->actingAs($admin)->get('/admin/users/import/sample');
        $response->assertStatus(200)
                 ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_access_admin_portal(): void
    {
        $admin = User::where('email', 'admin@uew.edu.gh')->first();

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200)
                 ->assertSee('Command Center');
    }

    public function test_student_cannot_access_admin_portal(): void
    {
        $student = User::where('email', 'student@st.uew.edu.gh')->first();

        $response = $this->actingAs($student)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_resources_filtered_by_week(): void
    {
        $student = User::where('email', 'student@st.uew.edu.gh')->first();
        $category = Category::first();

        Resource::create([
            'title' => 'Week 5 Specialized Accounting Slides',
            'type' => 'SLIDE',
            'status' => 'APPROVED',
            'category_id' => $category->id,
            'level' => 'L200',
            'week' => 5,
            'academic_year' => '2023/2024',
            'file_name' => 'week5.pdf',
            'file_path' => 'resources/week5.pdf',
            'file_size' => 1024,
            'uploaded_by' => $student->id,
        ]);

        $response = $this->actingAs($student)->get('/dashboard?week=5');
        $response->assertStatus(200)
                 ->assertSee('Week 5 Specialized Accounting Slides');
    }

    public function test_binary_blob_streaming_download(): void
    {
        $student = User::where('email', 'student@st.uew.edu.gh')->first();
        $category = Category::first();
        $blobContent = "MOCK_BINARY_BLOB_DOCUMENT_STREAM";

        $resource = Resource::create([
            'title' => 'Blob Stored Lecture Material',
            'type' => 'SLIDE',
            'status' => 'APPROVED',
            'category_id' => $category->id,
            'level' => 'L200',
            'week' => 3,
            'academic_year' => '2023/2024',
            'file_name' => 'blob_doc.pdf',
            'file_path' => 'resources/blob_doc.pdf',
            'file_blob' => $blobContent,
            'file_size' => strlen($blobContent),
            'mime_type' => 'application/pdf',
            'uploaded_by' => $student->id,
        ]);

        $response = $this->actingAs($student)->get("/resources/{$resource->id}/download");
        $response->assertStatus(200)
                 ->assertHeader('Content-Disposition', 'attachment; filename="blob_doc.pdf"')
                 ->assertSee('MOCK_BINARY_BLOB_DOCUMENT_STREAM');
    }

    public function test_admin_can_access_audit_reports_and_export_csv(): void
    {
        $admin = User::where('email', 'admin@uew.edu.gh')->first();

        // 1. Audit logs page
        $response = $this->actingAs($admin)->get('/admin/reports');
        $response->assertStatus(200)
                 ->assertSee('Institutional Audit Logs');

        // 2. CSV report export stream
        $csvResponse = $this->actingAs($admin)->get('/admin/reports/export');
        $csvResponse->assertStatus(200)
                    ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_docs_page_loads_for_guests_and_students(): void
    {
        $response = $this->get('/docs');
        $response->assertStatus(200)
                 ->assertSee('How to Use the UEW Business Digital Library')
                 ->assertSee('Syllabus Weeks (Weeks 1 to 15)');
    }

    public function test_doc_alias_redirects_to_docs(): void
    {
        $response = $this->get('/doc');
        $response->assertRedirect('/docs');
    }

    public function test_login_page_renders_with_student_and_staff_switchers(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200)
                 ->assertSee('Sign In to Your Account')
                 ->assertSee('Student (Index No.)')
                 ->assertSee('Faculty &amp; Staff', false);
    }

    public function test_mail_studio_loads_for_admin(): void
    {
        $admin = User::where('email', 'admin@uew.edu.gh')->first();

        $response = $this->actingAs($admin)->get('/admin/mail-studio');
        $response->assertStatus(200)
                 ->assertSee('Email Templates &amp; Dispatch Studio', false);
    }

    public function test_admin_can_simulate_email_dispatch(): void
    {
        $admin = User::where('email', 'admin@uew.edu.gh')->first();

        $response = $this->actingAs($admin)->post('/admin/mail-studio/send', [
            'template' => 'welcome',
            'recipient' => 'test@johnokyere.xyz',
            'mode' => 'simulate',
        ]);

        $response->assertRedirect('/admin/mail-studio?template=welcome&tab=mailbox');
        $this->assertDatabaseHas('email_logs', [
            'direction' => 'outgoing',
            'mailer' => 'simulated',
            'template' => 'welcome',
            'recipient' => 'test@johnokyere.xyz',
        ]);
    }

    public function test_admin_can_simulate_incoming_email(): void
    {
        $admin = User::where('email', 'admin@uew.edu.gh')->first();

        $response = $this->actingAs($admin)->post('/admin/mail-studio/simulate-incoming', [
            'sender' => 'student.test@uew.edu.gh',
            'subject' => 'Inquiry Regarding Course Material',
            'message' => 'Please confirm if my upload was approved.',
        ]);

        $response->assertRedirect('/admin/mail-studio?tab=mailbox');
        $this->assertDatabaseHas('email_logs', [
            'direction' => 'incoming',
            'sender' => 'student.test@uew.edu.gh',
            'subject' => 'Inquiry Regarding Course Material',
        ]);
    }

    public function test_admin_can_update_email_smtp_settings(): void
    {
        $admin = User::where('email', 'admin@uew.edu.gh')->first();

        $response = $this->actingAs($admin)->put('/admin/settings', [
            'academic_year' => '2023/2024',
            'active_semester' => 'FIRST',
            'institution_name' => 'UEW School of Business',
            'max_upload_size_mb' => 100,
            'allowed_file_extensions' => 'pdf, docx, pptx',
            'contact_email' => 'library@uew.edu.gh',
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => 587,
            'mail_encryption' => 'tls',
            'mail_username' => 'test-admin@uew.edu.gh',
            'mail_password' => 'mockappkey123456',
            'mail_from_address' => 'test-admin@uew.edu.gh',
            'mail_from_name' => 'UEW Business Library',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('smtp.gmail.com', \App\Models\Setting::get('mail_host'));
        $this->assertEquals(587, \App\Models\Setting::get('mail_port'));
        $this->assertEquals('tls', \App\Models\Setting::get('mail_encryption'));
        $this->assertEquals('test-admin@uew.edu.gh', \App\Models\Setting::get('mail_username'));
        $this->assertEquals('mockappkey123456', \App\Models\Setting::get('mail_password'));
    }

    public function test_admin_can_ping_smtp_connection_from_settings(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $admin = User::where('email', 'admin@uew.edu.gh')->first();

        $response = $this->actingAs($admin)->post('/admin/settings/test-smtp', [
            'test_recipient' => 'admin@uew.edu.gh',
        ]);

        $response->assertSessionHas('success');
        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\SecurityAlertMail::class, function ($mail) {
            return $mail->hasTo('admin@uew.edu.gh');
        });
    }
}
