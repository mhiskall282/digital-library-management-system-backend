<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->input('role');
        $level = $request->input('level');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = User::withCount(['bookmarks', 'reviews'])->latest();

        if ($role) {
            $query->where('role', $role);
        }
        if ($level) {
            $query->where('level', $level);
        }
        if ($status !== null && $status !== '') {
            $query->where('is_active', $status === 'active');
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users', 'role', 'level', 'status', 'search'));
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot deactivate your own administrative account.');
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        ActivityLog::record(
            $user->is_active ? 'USER_ACTIVATED' : 'USER_DEACTIVATED',
            $request->user(),
            $user
        );

        $statusText = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Account for {$user->name} has been {$statusText}.");
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot alter your own administrative role.');
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(['student', 'admin'])],
        ]);

        $user->role = $validated['role'];
        $user->save();

        ActivityLog::record('USER_ROLE_CHANGED', $request->user(), $user, [
            'new_role' => $user->role,
        ]);

        return back()->with('success', "Role for {$user->name} updated to {$user->role}.");
    }
}
