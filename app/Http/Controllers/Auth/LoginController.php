<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'student_id';

        $user = User::where($loginField, $credentials['login'])->first();

        if (! $user || ! Auth::attempt([$loginField => $credentials['login'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()->withInput($request->only('login', 'remember'))->withErrors([
                'login' => 'Invalid student ID/email or password credentials.',
            ]);
        }

        if (! $user->is_active) {
            Auth::logout();

            return back()->withErrors([
                'login' => 'Your account has been deactivated. Please contact library administrators.',
            ]);
        }

        $request->session()->regenerate();

        ActivityLog::record('LOGIN', $user, null, ['ip' => $request->ip()]);

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended(route('student.hub'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            ActivityLog::record('LOGOUT', $user, null);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'You have been safely signed out.');
    }
}
