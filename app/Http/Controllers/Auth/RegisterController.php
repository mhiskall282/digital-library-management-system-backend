<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'student_id' => ['required', 'string', 'max:30', 'unique:users,student_id'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'level' => ['required', Rule::in(['L100', 'L200', 'L300', 'L400', 'MASTERS', 'PHD'])],
            'program' => ['required', 'string', 'max:150'],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'student_id' => strtoupper(trim($validated['student_id'])),
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'level' => $validated['level'],
            'program' => $validated['program'],
            'role' => 'student',
            'is_active' => true,
            'email_verified_at' => now(),
            'email_notifications' => true,
            'new_resource_alerts' => true,
        ]);

        Auth::login($user);

        // Send welcome notification
        Notification::create([
            'user_id' => $user->id,
            'type' => 'GENERAL',
            'title' => 'Welcome to UEW School of Business Library',
            'message' => 'Your student digital library account is now active. Explore course lecture slides, past examination papers, and start bookmarking revision materials.',
            'link' => '/dashboard',
            'is_read' => false,
        ]);

        ActivityLog::record('REGISTER', $user, null);

        return redirect()->route('dashboard')->with('success', 'Account created successfully! Welcome to the UEW School of Business Digital Library.');
    }
}
