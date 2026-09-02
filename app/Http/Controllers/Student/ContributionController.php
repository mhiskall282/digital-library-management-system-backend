<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Resource;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ContributionController extends Controller
{
    public function create(): View
    {
        $categories = Category::orderBy('course_code')->get();
        $user = auth()->user();

        return view('student.contribute', compact('categories', 'user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $maxMb = Setting::get('max_upload_size_mb', 100);
        $maxKb = $maxMb * 1024;

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'type' => ['required', 'in:SLIDE,PAST_QUESTION'],
            'category_id' => ['required', 'exists:categories,id'],
            'level' => ['required', 'in:L100,L200,L300,L400,MASTERS,PHD'],
            'week' => ['nullable', 'integer', 'between:1,15'],
            'academic_year' => ['required', 'string', 'max:20'],
            'tags' => ['nullable', 'string'],
            'file' => ['required', 'file', "max:{$maxKb}", 'mimes:pdf,ppt,pptx,doc,docx,zip,jpg,jpeg,png'],
        ]);

        $storedPath = null;

        try {
            $uploadedFile = $request->file('file');
            $originalName = $uploadedFile->getClientOriginalName();
            $fileSize = $uploadedFile->getSize();
            $mimeType = $uploadedFile->getClientMimeType() ?: 'application/octet-stream';

            // 1. Store on persistent public disk
            $storedPath = $uploadedFile->store('resources', 'public');

            // 2. Prepare binary blob safely for files <= 8MB (avoids DB packet limits & memory issues)
            $fileBlob = null;
            if ($fileSize <= 8 * 1024 * 1024) {
                try {
                    $raw = file_get_contents($uploadedFile->getRealPath());
                    $fileBlob = Resource::prepareBlobForStorage($raw);
                } catch (\Throwable $blobEx) {
                    \Illuminate\Support\Facades\Log::warning('Could not read binary blob for resource: ' . $blobEx->getMessage());
                    $fileBlob = null;
                }
            }

            $tags = $request->filled('tags')
                ? array_map('trim', explode(',', $request->input('tags')))
                : [];

            $resource = Resource::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'],
                'status' => 'PENDING_REVIEW',
                'category_id' => $validated['category_id'],
                'level' => $validated['level'],
                'week' => $validated['week'] ?? null,
                'academic_year' => $validated['academic_year'],
                'file_name' => $originalName,
                'file_path' => $storedPath ?: 'resources/' . $originalName,
                'file_blob' => $fileBlob,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'uploaded_by' => $request->user()->id,
                'tags' => $tags,
            ]);

            ActivityLog::record('STUDENT_SUBMISSION', $request->user(), $resource);

            // Notify Library Staff safely
            try {
                $staffUsers = User::whereIn('role', ['staff', 'admin', 'superadmin'])->get();
                foreach ($staffUsers as $staff) {
                    Notification::create([
                        'user_id' => $staff->id,
                        'type' => 'NEW_RESOURCE',
                        'title' => '📝 New Student Submission for Review',
                        'message' => "Student {$request->user()->name} submitted '{$resource->title}' for {$resource->level}. Awaiting moderation.",
                        'resource_id' => $resource->id,
                        'link' => route('admin.moderation.index'),
                        'is_read' => false,
                    ]);
                }
            } catch (\Throwable $notifEx) {
                \Illuminate\Support\Facades\Log::warning('Staff notification dispatch skipped: ' . $notifEx->getMessage());
            }

            // Notify Student safely
            try {
                Notification::create([
                    'user_id' => $request->user()->id,
                    'type' => 'GENERAL',
                    'title' => 'Document Submitted for Review',
                    'message' => "Your upload '{$resource->title}' is being reviewed by library staff. You will receive +50 points upon approval!",
                    'resource_id' => $resource->id,
                    'is_read' => false,
                ]);
            } catch (\Throwable $notifEx) {
                \Illuminate\Support\Facades\Log::warning('Student confirmation notification skipped: ' . $notifEx->getMessage());
            }

            return redirect()->route('student.hub')->with('success', 'Document submitted successfully! Our library curators will review and publish it shortly.');

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Student contribution upload failed: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => $request->user()?->id,
            ]);

            // Clean up stored file if database record failed
            if ($storedPath && Storage::disk('public')->exists($storedPath)) {
                Storage::disk('public')->delete($storedPath);
            }

            return back()->withInput()->with('error', 'Unable to upload document at this time: ' . $e->getMessage() . '. Please try again or contact the library desk.');
        }
    }
}
