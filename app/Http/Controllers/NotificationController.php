<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $filter = $request->input('filter', 'all');

        $query = Notification::where('user_id', $user->id)->latest();

        if ($filter === 'unread') {
            $query->unread();
        }

        $notifications = $query->paginate(15)->withQueryString();
        $unreadCount = Notification::where('user_id', $user->id)->unread()->count();

        return view('notifications.index', compact('notifications', 'unreadCount', 'filter'));
    }

    public function markRead(Request $request, Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403);
        }

        $notification->update(['is_read' => true]);

        if ($notification->link) {
            return redirect($notification->link);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        Notification::where('user_id', $request->user()->id)
            ->unread()
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function clearRead(Request $request): RedirectResponse
    {
        Notification::where('user_id', $request->user()->id)
            ->read()
            ->delete();

        return back()->with('success', 'Read notifications cleared.');
    }

    public function destroy(Request $request, Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403);
        }

        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }
}
