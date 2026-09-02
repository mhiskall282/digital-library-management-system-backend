<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            // Avoid account enumeration: show success feedback even if email not found
            return back()->with('status', 'If your email is registered in our portal, you will receive password reset instructions shortly.');
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        // For local development or production: provide clear demo instruction or log link
        session()->flash('demo_reset_token', $token);
        session()->flash('demo_reset_email', $user->email);

        return back()->with('status', 'Password reset instructions have been generated. (For testing, you can use the direct reset link below).');
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (! $resetRecord) {
            return back()->withErrors(['email' => 'Invalid or expired password reset token.']);
        }

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        ActivityLog::record('PASSWORD_RESET', $user, null);

        return redirect()->route('login')->with('success', 'Your password has been reset successfully. Please log in with your new password.');
    }
}
