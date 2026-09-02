<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Bookmark;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Resource;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $student = User::where('email', 'student@st.uew.edu.gh')->first();
        $student2 = User::where('email', 'ama.osei@st.uew.edu.gh')->first();

        // Ensure storage directory exists and generate a sample mock PDF
        $mockPdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f \n0000000010 00000 n \n0000000053 00000 n \n0000000102 00000 n \ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n185\n%%EOF";

        Storage::disk('public')->put('resources/bba111_lecture_week1.pdf', $mockPdfContent);
        Storage::disk('public')->put('resources/act211_financial_accounting_past_exam_2023.pdf', $mockPdfContent);
        Storage::disk('public')->put('resources/fin311_capital_budgeting_slides.pdf', $mockPdfContent);
        Storage::disk('public')->put('resources/bba411_strategic_management_case_study.pdf', $mockPdfContent);
        Storage::disk('public')->put('resources/mkt211_marketing_mix_overview.pdf', $mockPdfContent);

        $bba111 = Category::where('course_code', 'BBA 111')->first();
        $act211 = Category::where('course_code', 'ACT 211')->first();
        $fin311 = Category::where('course_code', 'FIN 311')->first();
        $bba411 = Category::where('course_code', 'BBA 411')->first();
        $mkt211 = Category::where('course_code', 'MKT 211')->first();

        $sampleResources = [
            [
                'title' => 'Introduction to Management Theories & Classical School',
                'description' => 'Comprehensive slides covering Taylor scientific management, Fayol administrative theory, and modern organizational systems.',
                'type' => 'SLIDE',
                'status' => 'APPROVED',
                'category_id' => $bba111?->id,
                'level' => 'L100',
                'week' => 1,
                'academic_year' => '2023/2024',
                'file_name' => 'bba111_lecture_week1.pdf',
                'file_path' => 'resources/bba111_lecture_week1.pdf',
                'file_blob' => $mockPdfContent,
                'file_size' => 2450000,
                'mime_type' => 'application/pdf',
                'downloads' => 142,
                'uploaded_by' => $admin?->id,
                'tags' => ['Management', 'Fayol', 'Taylor', 'L100', 'Week 1'],
            ],
            [
                'title' => 'Financial Accounting I End of Semester Examination 2023',
                'description' => 'Official UEW School of Business past examination paper with solution guidelines and ledger formats.',
                'type' => 'PAST_QUESTION',
                'status' => 'APPROVED',
                'category_id' => $act211?->id,
                'level' => 'L200',
                'week' => 15,
                'academic_year' => '2022/2023',
                'file_name' => 'act211_financial_accounting_past_exam_2023.pdf',
                'file_path' => 'resources/act211_financial_accounting_past_exam_2023.pdf',
                'file_blob' => $mockPdfContent,
                'file_size' => 1850000,
                'mime_type' => 'application/pdf',
                'downloads' => 285,
                'uploaded_by' => $admin?->id,
                'tags' => ['Past Exam', 'Accounting', 'Trial Balance', 'L200', 'Week 15'],
            ],
            [
                'title' => 'Corporate Capital Budgeting & Investment Appraisal',
                'description' => 'In-depth lecture slides on NPV, IRR, Payback Period, and Capital Rationing under conditions of inflation and risk.',
                'type' => 'SLIDE',
                'status' => 'APPROVED',
                'category_id' => $fin311?->id,
                'level' => 'L300',
                'week' => 4,
                'academic_year' => '2023/2024',
                'file_name' => 'fin311_capital_budgeting_slides.pdf',
                'file_path' => 'resources/fin311_capital_budgeting_slides.pdf',
                'file_blob' => $mockPdfContent,
                'file_size' => 4120000,
                'mime_type' => 'application/pdf',
                'downloads' => 97,
                'uploaded_by' => $admin?->id,
                'tags' => ['Finance', 'NPV', 'IRR', 'Capital Budgeting', 'L300', 'Week 4'],
            ],
            [
                'title' => 'Strategic Management & Industry Case Studies',
                'description' => 'Detailed analysis of Porter Five Forces, Blue Ocean strategy, and multinational corporate maneuvers in African emerging markets.',
                'type' => 'SLIDE',
                'status' => 'APPROVED',
                'category_id' => $bba411?->id,
                'level' => 'L400',
                'week' => 8,
                'academic_year' => '2023/2024',
                'file_name' => 'bba411_strategic_management_case_study.pdf',
                'file_path' => 'resources/bba411_strategic_management_case_study.pdf',
                'file_blob' => $mockPdfContent,
                'file_size' => 3890000,
                'mime_type' => 'application/pdf',
                'downloads' => 164,
                'uploaded_by' => $admin?->id,
                'tags' => ['Strategy', 'Porter', 'Case Studies', 'L400', 'Week 8'],
            ],
            [
                'title' => 'Principles of Marketing: The 4Ps and Buyer Journey',
                'description' => 'Lecture slides exploring product lifecycle, pricing tactics, promotional channels, and retail distribution strategies in Ghana.',
                'type' => 'SLIDE',
                'status' => 'APPROVED',
                'category_id' => $mkt211?->id,
                'level' => 'L200',
                'week' => 2,
                'academic_year' => '2023/2024',
                'file_name' => 'mkt211_marketing_mix_overview.pdf',
                'file_path' => 'resources/mkt211_marketing_mix_overview.pdf',
                'file_blob' => $mockPdfContent,
                'file_size' => 1950000,
                'mime_type' => 'application/pdf',
                'downloads' => 210,
                'uploaded_by' => $admin?->id,
                'tags' => ['Marketing', '4Ps', 'Consumer Behavior', 'L200', 'Week 2'],
            ],
        ];

        foreach ($sampleResources as $data) {
            if (! $data['category_id']) {
                continue;
            }

            $res = Resource::updateOrCreate(
                ['title' => $data['title']],
                $data
            );

            // Add reviews for the resource
            if ($student) {
                Review::updateOrCreate(
                    ['resource_id' => $res->id, 'user_id' => $student->id],
                    [
                        'rating' => 5,
                        'comment' => 'Exceptionally structured lecture material. Crucial for revision!',
                        'helpful_count' => 12,
                    ]
                );
            }

            if ($student2) {
                Review::updateOrCreate(
                    ['resource_id' => $res->id, 'user_id' => $student2->id],
                    [
                        'rating' => 4,
                        'comment' => 'Very helpful overview. Would love more practical worked examples next semester.',
                        'helpful_count' => 4,
                    ]
                );
            }

            $res->recalculateRating();
        }

        // Add sample bookmark for Kwame Mensah
        $firstResource = Resource::first();
        if ($firstResource && $student) {
            Bookmark::updateOrCreate(
                ['user_id' => $student->id, 'resource_id' => $firstResource->id],
                ['notes' => 'Review slides 14 to 28 before midterm exam. Important formulas highlighted.']
            );
        }

        // Add sample notifications for students
        if ($student && $firstResource) {
            Notification::updateOrCreate(
                ['user_id' => $student->id, 'title' => 'New Semester Resource Uploaded'],
                [
                    'type' => 'NEW_RESOURCE',
                    'message' => 'New slides for ' . $firstResource->title . ' have been uploaded to your course catalog.',
                    'resource_id' => $firstResource->id,
                    'link' => '/resources/' . $firstResource->id,
                    'is_read' => false,
                ]
            );

            Notification::updateOrCreate(
                ['user_id' => $student->id, 'title' => 'Exam Preparations 2024'],
                [
                    'type' => 'GENERAL',
                    'message' => 'School of Business Digital Library past questions archive is now fully updated.',
                    'resource_id' => null,
                    'link' => '/dashboard',
                    'is_read' => true,
                ]
            );
        }

        // Add sample activity logs
        ActivityLog::record('SYSTEM_SEED', $admin, null, ['status' => 'initialized']);
    }
}
