<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DownloadRequest;
use App\Models\Notification;
use App\Models\Resource;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResourceController extends Controller
{
    public function show(Request $request, Resource $resource): View|RedirectResponse
    {
        $user = $request->user();

        // Check level gating
        $hasAccess = $user ? $user->canAccessLevel($resource->level) : false;

        $resource->load(['category', 'uploader', 'reviews.user']);

        // Related resources from same category or level
        $relatedResources = Resource::approved()
            ->where('id', '!=', $resource->id)
            ->where(function ($q) use ($resource) {
                $q->where('category_id', $resource->category_id)
                  ->orWhere('level', $resource->level);
            })
            ->limit(4)
            ->get();

        $userReview = $user ? $resource->reviews->where('user_id', $user->id)->first() : null;
        $isBookmarked = $resource->isBookmarkedBy($user);

        // Download Approval Status Check
        $requiresApproval = (bool) Setting::get('require_download_approval', false);
        $userDownloadRequest = ($user && $requiresApproval)
            ? DownloadRequest::where('user_id', $user->id)->where('resource_id', $resource->id)->latest()->first()
            : null;

        return view('resources.show', compact(
            'resource',
            'relatedResources',
            'userReview',
            'isBookmarked',
            'hasAccess',
            'requiresApproval',
            'userDownloadRequest'
        ));
    }

    public function download(Request $request, Resource $resource): StreamedResponse|BinaryFileResponse|RedirectResponse|\Illuminate\Http\Response
    {
        $user = $request->user();

        if ($user && ! $user->canAccessLevel($resource->level)) {
            return back()->with('error', "Your academic level ({$user->level}) does not grant download access for {$resource->level} materials.");
        }

        // Check download approval requirement
        if ($user && $user->isStudent() && Setting::get('require_download_approval', false)) {
            $approvedRequest = DownloadRequest::where('user_id', $user->id)
                ->where('resource_id', $resource->id)
                ->where('status', 'APPROVED')
                ->first();

            if (! $approvedRequest) {
                return back()->with('error', 'Download approval required. Please submit an academic request with your study purpose below.');
            }
        }

        // Increment verified downloads
        $resource->increment('downloads');

        // IP Audit Logging for Intellectual Property Compliance
        ActivityLog::record('DOWNLOAD', $user, $resource, [
            'file_name' => $resource->file_name,
            'level' => $resource->level,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // 1. Direct High-Performance Blob Stream (Memory Efficient)
        $blob = $resource->getRawBlob();
        if (! empty($blob)) {
            return response($blob, 200, [
                'Content-Type' => $resource->mime_type ?: 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $resource->file_name . '"',
                'Content-Length' => strlen($blob),
                'Cache-Control' => 'private, max-age=3600',
            ]);
        }

        if (Storage::disk('public')->exists($resource->file_path)) {
            return Storage::disk('public')->download($resource->file_path, $resource->file_name);
        }

        // Fallback: If mock file doesn't exist on disk, stream generated content
        return response()->streamDownload(function () {
            echo "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f \n0000000010 00000 n \n0000000053 00000 n \n0000000102 00000 n \ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n185\n%%EOF";
        }, $resource->file_name, [
            'Content-Type' => $resource->mime_type ?: 'application/pdf',
        ]);
    }

    public function requestDownload(Request $request, Resource $resource): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $downloadReq = DownloadRequest::updateOrCreate(
            ['user_id' => $request->user()->id, 'resource_id' => $resource->id],
            [
                'reason' => $validated['reason'],
                'status' => 'PENDING',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'rejection_reason' => null,
            ]
        );

        ActivityLog::record('DOWNLOAD_REQUESTED', $request->user(), $resource, [
            'ip' => $request->ip(),
        ]);

        // Notify Library Staff
        $staffUsers = User::whereIn('role', ['staff', 'admin', 'superadmin'])->get();
        foreach ($staffUsers as $staff) {
            Notification::create([
                'user_id' => $staff->id,
                'type' => 'SYSTEM',
                'title' => '📥 Download Access Request',
                'message' => "Student {$request->user()->name} requested access to download '{$resource->title}'.",
                'link' => route('admin.downloads.index'),
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'Download request submitted. Library curators have been alerted and will review your request.');
    }

    public function preview(Request $request, Resource $resource): StreamedResponse|BinaryFileResponse|RedirectResponse|\Illuminate\Http\Response
    {
        $user = $request->user();

        if ($user && ! $user->canAccessLevel($resource->level)) {
            return back()->with('error', 'Access restricted for your academic level.');
        }

        // Direct High-Performance Blob Stream
        $blob = $resource->getRawBlob();
        if (! empty($blob)) {
            return response($blob, 200, [
                'Content-Type' => $resource->mime_type ?: 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $resource->file_name . '"',
                'Content-Length' => strlen($blob),
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        if (Storage::disk('public')->exists($resource->file_path)) {
            return Storage::disk('public')->response($resource->file_path);
        }

        return response()->stream(function () {
            echo "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Count 1/Kids[3 0 R]>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f \n0000000010 00000 n \n0000000053 00000 n \n0000000102 00000 n \ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n185\n%%EOF";
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $resource->file_name . '"',
        ]);
    }
}
