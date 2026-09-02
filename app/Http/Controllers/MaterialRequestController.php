<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\MaterialRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaterialRequestController extends Controller
{
    public function index(Request $request): View
    {
        $myRequests = MaterialRequest::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return view('requests.index', compact('myRequests'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'course_code' => ['required', 'string', 'max:20'],
            'course_name' => ['required', 'string', 'max:255'],
            'program' => ['required', 'string', 'max:255'],
            'level' => ['required', 'in:L100,L200,L300,L400,MASTERS,PHD'],
            'topic' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:SLIDE,PAST_QUESTION'],
            'urgency' => ['required', 'in:LOW,MEDIUM,HIGH'],
        ]);

        $materialRequest = MaterialRequest::create([
            'user_id' => $request->user()->id,
            'course_code' => strtoupper($validated['course_code']),
            'course_name' => $validated['course_name'],
            'program' => $validated['program'],
            'level' => $validated['level'],
            'topic' => $validated['topic'],
            'type' => $validated['type'],
            'urgency' => $validated['urgency'],
            'status' => 'OPEN',
        ]);

        ActivityLog::record('MATERIAL_REQUESTED', $request->user(), null, [
            'course' => $materialRequest->course_code,
            'topic' => $materialRequest->topic,
        ]);

        // Notify Library Staff
        $staffUsers = User::whereIn('role', ['staff', 'admin', 'superadmin'])->get();
        foreach ($staffUsers as $staff) {
            Notification::create([
                'user_id' => $staff->id,
                'type' => 'SYSTEM',
                'title' => '📢 New Material Request Submitted',
                'message' => "Student requested {$materialRequest->type} for {$materialRequest->course_code} ({$materialRequest->topic}).",
                'link' => route('admin.requests.index'),
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'Your material request has been logged! Library curators will review and source it.');
    }

    // Admin & Staff Support Desk
    public function adminIndex(): View
    {
        $requests = MaterialRequest::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.requests.index', compact('requests'));
    }

    public function updateStatus(Request $request, MaterialRequest $materialRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:OPEN,IN_PROGRESS,FULFILLED,CLOSED'],
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        $materialRequest->update($validated);

        Notification::create([
            'user_id' => $materialRequest->user_id,
            'type' => 'SYSTEM',
            'title' => "Material Request Update: {$materialRequest->course_code}",
            'message' => "Status changed to {$materialRequest->status}." . ($validated['admin_notes'] ? " Note: {$validated['admin_notes']}" : ''),
            'link' => route('requests.index'),
            'is_read' => false,
        ]);

        return back()->with('success', "Request for {$materialRequest->course_code} updated to {$materialRequest->status}.");
    }
}
