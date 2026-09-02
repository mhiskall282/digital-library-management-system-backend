<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $bookmarksCount = $user->bookmarks()->count();
        $reviewsCount = $user->reviews()->count();

        return view('profile.edit', compact('user', 'bookmarksCount', 'reviewsCount'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'level' => ['required', Rule::in(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'])],
            'program' => ['required', 'string', 'max:150'],
            'email_notifications' => ['nullable', 'boolean'],
            'new_resource_alerts' => ['nullable', 'boolean'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_path = $avatarPath;
        }

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->level = $validated['level'];
        $user->program = $validated['program'];
        $user->email_notifications = $request->boolean('email_notifications');
        $user->new_resource_alerts = $request->boolean('new_resource_alerts');
        $user->save();

        ActivityLog::record('PROFILE_UPDATE', $user);

        return back()->with('success', 'Profile and preferences updated successfully!');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLog::record('PASSWORD_CHANGE', $user);

        return back()->with('success', 'Your password has been changed securely.');
    }
}
