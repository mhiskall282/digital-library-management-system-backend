<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->is_onboarded) {
            return redirect()->route('student.hub');
        }

        $programs = [
            'BSc. Business Information Systems (BIS)',
            'BSc. Banking and Finance',
            'BSc. Accounting',
            'BBA. Marketing',
            'BBA. Human Resource Management',
            'BSc. Procurement and Supply Chain Management',
            'MBA. Accounting / Finance / Marketing',
            'MSc. Development Finance',
            'MPhil. Business Administration',
            'PhD. Business Administration',
        ];

        return view('auth.onboarding', compact('user', 'programs'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'program' => ['required', 'string', 'max:255'],
            'level' => ['required', 'in:L100,L200,L300,L400,MASTERS,PHD'],
            'phone' => ['nullable', 'string', 'max:20'],
            'bio' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->program = $validated['program'];
        $user->level = $validated['level'];
        $user->phone = $validated['phone'] ?? null;
        $user->bio = $validated['bio'] ?? null;
        $user->is_onboarded = true;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Award welcome bonus points!
        $user->awardPoints(25, 'Profile Onboarding Completed');

        ActivityLog::record('ONBOARDING_COMPLETED', $user, null, [
            'program' => $user->program,
            'level' => $user->level,
        ]);

        return redirect()->route('student.hub')->with('success', 'Profile onboarding complete! You earned 25 Contributor Points.');
    }
}
