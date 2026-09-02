<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Resource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModerationController extends Controller
{
    public function index(): View
    {
        $pendingResources = Resource::pendingReview()
            ->with(['category', 'uploader'])
            ->latest()
            ->paginate(15);

        $pendingCount = Resource::pendingReview()->count();

        return view('admin.moderation.index', compact('pendingResources', 'pendingCount'));
    }

    public function approve(Request $request, Resource $resource): RedirectResponse
    {
        $resource->update([
            'status' => 'APPROVED',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        // Award +50 points to the student contributor!
        if ($resource->uploader) {
            $resource->uploader->awardPoints(50, "Document Approved: '{$resource->title}'");

            Notification::create([
                'user_id' => $resource->uploader->id,
                'type' => 'NEW_RESOURCE',
                'title' => '🎉 Your Document Submission Was Approved!',
                'message' => "Congratulations! Your upload '{$resource->title}' has been reviewed and published to the catalog. You earned +50 Contributor Points!",
                'resource_id' => $resource->id,
                'link' => route('resources.show', $resource),
                'is_read' => false,
            ]);
        }

        ActivityLog::record('SUBMISSION_APPROVED', $request->user(), $resource);

        return back()->with('success', "Material '{$resource->title}' approved and published. 50 points credited to contributor.");
    }

    public function reject(Request $request, Resource $resource): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $resource->update([
            'status' => 'REJECTED',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => $validated['reason'],
        ]);

        if ($resource->uploader) {
            Notification::create([
                'user_id' => $resource->uploader->id,
                'type' => 'SYSTEM',
                'title' => 'Submission Review Feedback',
                'message' => "Your upload '{$resource->title}' could not be published at this time. Feedback: {$validated['reason']}",
                'resource_id' => $resource->id,
                'is_read' => false,
            ]);
        }

        ActivityLog::record('SUBMISSION_REJECTED', $request->user(), $resource);

        return back()->with('info', "Material '{$resource->title}' rejected and feedback dispatched to student.");
    }
}
