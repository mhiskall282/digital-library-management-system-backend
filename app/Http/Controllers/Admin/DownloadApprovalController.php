<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DownloadRequest;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DownloadApprovalController extends Controller
{
    public function index(): View
    {
        $requests = DownloadRequest::with(['user', 'resource.category', 'approver'])
            ->latest()
            ->paginate(20);

        $pendingCount = DownloadRequest::where('status', 'PENDING')->count();

        return view('admin.downloads.index', compact('requests', 'pendingCount'));
    }

    public function approve(Request $request, DownloadRequest $downloadRequest): RedirectResponse
    {
        $downloadRequest->update([
            'status' => 'APPROVED',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        ActivityLog::record('DOWNLOAD_APPROVED', $request->user(), $downloadRequest->resource, [
            'student_id' => $downloadRequest->user_id,
            'ip' => $downloadRequest->ip_address,
        ]);

        Notification::create([
            'user_id' => $downloadRequest->user_id,
            'type' => 'NEW_RESOURCE',
            'title' => 'Download Request Approved!',
            'message' => "Your download request for '{$downloadRequest->resource->title}' has been approved by library staff.",
            'resource_id' => $downloadRequest->resource_id,
            'link' => route('resources.show', $downloadRequest->resource_id),
            'is_read' => false,
        ]);

        return back()->with('success', "Download access granted for student {$downloadRequest->user->name}.");
    }

    public function reject(Request $request, DownloadRequest $downloadRequest): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $downloadRequest->update([
            'status' => 'REJECTED',
            'approved_by' => $request->user()->id,
            'rejection_reason' => $validated['reason'],
        ]);

        ActivityLog::record('DOWNLOAD_REJECTED', $request->user(), $downloadRequest->resource);

        Notification::create([
            'user_id' => $downloadRequest->user_id,
            'type' => 'SYSTEM',
            'title' => 'Download Request Declined',
            'message' => "Your download request for '{$downloadRequest->resource->title}' was not approved. Reason: {$validated['reason']}",
            'resource_id' => $downloadRequest->resource_id,
            'is_read' => false,
        ]);

        return back()->with('info', "Download request declined and feedback logged.");
    }
}
