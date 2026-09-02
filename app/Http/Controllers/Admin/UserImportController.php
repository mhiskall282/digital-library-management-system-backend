<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeActivationMail;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserImportController extends Controller
{
    public function create(): View
    {
        return view('admin.users.import');
    }

    public function sampleCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="uew_student_import_template.csv"',
        ];

        $columns = ['student_id', 'first_name', 'last_name', 'email', 'level', 'program', 'department'];

        $sampleData = [
            ['5201040010', 'Kofi', 'Antwi', 'kofi.antwi@st.uew.edu.gh', 'L200', 'BSc. Banking and Finance', 'Banking and Finance'],
            ['5201040011', 'Abena', 'Mensah', 'abena.mensah@st.uew.edu.gh', 'L100', 'BSc. Business Information Systems (BIS)', 'Information Systems'],
            ['5201040012', 'Yaw', 'Boateng', 'yaw.boateng@st.uew.edu.gh', 'L300', 'BSc. Accounting', 'Accounting'],
            ['5201040013', 'Akua', 'Donkor', 'akua.donkor@st.uew.edu.gh', 'L400', 'BBA. Marketing', 'Marketing'],
        ];

        return response()->streamDownload(function () use ($columns, $sampleData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        }, 'uew_student_import_template.csv', $headers);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'], // 5MB limit
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if (! $handle) {
            return back()->with('error', 'Could not open uploaded CSV file.');
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);
            return back()->with('error', 'CSV file appears to be empty.');
        }

        // Normalize headers
        $header = array_map(fn ($col) => strtolower(trim($col)), $header);
        $expected = ['student_id', 'first_name', 'last_name', 'email', 'level', 'program'];

        foreach ($expected as $reqCol) {
            if (! in_array($reqCol, $header, true)) {
                fclose($handle);
                return back()->with('error', "CSV is missing required column header: '{$reqCol}'. Please download and use the official template.");
            }
        }

        $idIndex = array_search('student_id', $header, true);
        $fnIndex = array_search('first_name', $header, true);
        $lnIndex = array_search('last_name', $header, true);
        $emailIndex = array_search('email', $header, true);
        $levelIndex = array_search('level', $header, true);
        $progIndex = array_search('program', $header, true);
        $deptIndex = array_search('department', $header, true);

        $importedCount = 0;
        $skippedCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }

            $email = trim($row[$emailIndex] ?? '');
            $studentId = trim($row[$idIndex] ?? '');

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skippedCount++;
                continue;
            }

            // Check if user already registered
            if (User::where('email', $email)->orWhere('student_id', $studentId)->exists()) {
                $skippedCount++;
                continue;
            }

            $level = strtoupper(trim($row[$levelIndex] ?? 'L100'));
            if (! in_array($level, ['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'], true)) {
                $level = 'L100';
            }

            $tempPassword = 'UEW-' . Str::random(8);

            $user = User::create([
                'student_id' => $studentId,
                'first_name' => trim($row[$fnIndex] ?? 'Student'),
                'last_name' => trim($row[$lnIndex] ?? 'Scholar'),
                'email' => $email,
                'password' => Hash::make($tempPassword),
                'level' => $level,
                'program' => trim($row[$progIndex] ?? 'BSc. Administration'),
                'department' => $deptIndex !== false ? trim($row[$deptIndex] ?? '') : null,
                'role' => 'student',
                'is_active' => true,
                'is_onboarded' => false, // Requires first-time onboarding!
            ]);

            // Dispatch Welcome & Activation Email
            try {
                Mail::to($user->email)->queue(new WelcomeActivationMail($user, $tempPassword));
            } catch (\Throwable $e) {
                // Ignore mail failure in local sandbox
            }

            $importedCount++;
        }

        fclose($handle);

        ActivityLog::record('BULK_STUDENTS_IMPORTED', $request->user(), null, [
            'imported' => $importedCount,
            'skipped' => $skippedCount,
        ]);

        return redirect()->route('admin.users.index')->with('success', "Batch import completed: {$importedCount} student accounts created with activation notices queued. ({$skippedCount} skipped / duplicates).");
    }
}
