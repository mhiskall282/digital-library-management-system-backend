<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $level = $request->input('level');
        $type = $request->input('type');
        $categoryId = $request->input('category_id');

        $query = Resource::with(['category', 'uploader'])->latest();

        if ($search) {
            $query->search($search);
        }
        if ($level) {
            $query->where('level', $level);
        }
        if ($type) {
            $query->where('type', $type);
        }
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $resources = $query->paginate(15)->withQueryString();
        $categories = Category::orderBy('course_code')->get();

        return view('admin.resources.index', compact('resources', 'categories', 'search', 'level', 'type', 'categoryId'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('course_code')->get();

        return view('admin.resources.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['SLIDE', 'PAST_QUESTION'])],
            'category_id' => ['required', 'exists:categories,id'],
            'level' => ['required', Rule::in(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'])],
            'week' => ['nullable', 'integer', 'between:1,15'],
            'academic_year' => ['required', 'string', 'max:20'],
            'file' => ['required', 'file', 'mimes:pdf,ppt,pptx,doc,docx,zip,jpg,jpeg,png', 'max:102400'], // 100MB max
            'tags' => ['nullable', 'string'],
        ]);

        $filePath = null;

        try {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $mimeType = $file->getMimeType() ?: 'application/octet-stream';

            // 1. Store on persistent public disk
            $filePath = $file->store('resources', 'public');

            // 2. Prepare binary blob safely for files <= 8MB (avoids DB packet limits & memory issues)
            $fileBlob = null;
            if ($fileSize <= 8 * 1024 * 1024) {
                try {
                    $raw = file_get_contents($file->getRealPath());
                    $fileBlob = Resource::prepareBlobForStorage($raw);
                } catch (\Throwable $blobEx) {
                    \Illuminate\Support\Facades\Log::warning('Could not read binary blob for admin resource: ' . $blobEx->getMessage());
                    $fileBlob = null;
                }
            }

            // Parse tags
            $tagsArray = [];
            if (! empty($validated['tags'])) {
                $tagsArray = array_map('trim', explode(',', $validated['tags']));
            }

            $resource = Resource::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'type' => $validated['type'],
                'status' => 'APPROVED',
                'category_id' => $validated['category_id'],
                'level' => $validated['level'],
                'week' => $validated['week'] ?? null,
                'academic_year' => $validated['academic_year'],
                'file_name' => $fileName,
                'file_path' => $filePath ?: 'resources/' . $fileName,
                'file_blob' => $fileBlob,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'downloads' => 0,
                'average_rating' => 0.00,
                'total_reviews' => 0,
                'uploaded_by' => $request->user()->id,
                'tags' => $tagsArray,
            ]);

            // Send alerts to students safely
            try {
                $studentsToNotify = User::where('role', 'student')
                    ->where('is_active', true)
                    ->where('new_resource_alerts', true)
                    ->where('level', $validated['level'])
                    ->get();

                foreach ($studentsToNotify as $student) {
                    Notification::create([
                        'user_id' => $student->id,
                        'type' => 'NEW_RESOURCE',
                        'title' => "New {$validated['type']} Uploaded: {$validated['title']}",
                        'message' => "New material is now available in your {$resource->category->course_code} course catalog.",
                        'resource_id' => $resource->id,
                        'link' => route('resources.show', $resource),
                        'is_read' => false,
                    ]);
                }
            } catch (\Throwable $notifEx) {
                \Illuminate\Support\Facades\Log::warning('Student notifications skipped: ' . $notifEx->getMessage());
            }

            ActivityLog::record('RESOURCE_UPLOAD', $request->user(), $resource, [
                'file_name' => $fileName,
                'file_size' => $fileSize,
            ]);

            return redirect()->route('admin.resources.index')->with('success', 'Resource uploaded and published successfully.');

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Admin resource upload failed: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return back()->withInput()->with('error', 'Unable to upload resource: ' . $e->getMessage());
        }
    }

    public function edit(Resource $resource): View
    {
        $categories = Category::orderBy('course_code')->get();

        return view('admin.resources.edit', compact('resource', 'categories'));
    }

    public function update(Request $request, Resource $resource): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['SLIDE', 'PAST_QUESTION'])],
            'category_id' => ['required', 'exists:categories,id'],
            'level' => ['required', Rule::in(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'])],
            'academic_year' => ['required', 'string', 'max:20'],
            'tags' => ['nullable', 'string'],
        ]);

        $tagsArray = [];
        if (! empty($validated['tags'])) {
            $tagsArray = array_map('trim', explode(',', $validated['tags']));
        }

        $resource->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'category_id' => $validated['category_id'],
            'level' => $validated['level'],
            'academic_year' => $validated['academic_year'],
            'tags' => $tagsArray,
        ]);

        ActivityLog::record('RESOURCE_UPDATE', $request->user(), $resource);

        return redirect()->route('admin.resources.index')->with('success', 'Resource metadata updated successfully.');
    }

    public function destroy(Request $request, Resource $resource): RedirectResponse
    {
        if (Storage::disk('public')->exists($resource->file_path)) {
            Storage::disk('public')->delete($resource->file_path);
        }

        ActivityLog::record('RESOURCE_DELETE', $request->user(), null, [
            'title' => $resource->title,
            'file_name' => $resource->file_name,
        ]);

        $resource->delete();

        return back()->with('success', 'Resource and corresponding file removed.');
    }
}
