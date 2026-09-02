<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Bookmark;
use App\Models\Resource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookmarkController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = $request->input('search');

        $query = Bookmark::with(['resource.category', 'resource.reviews'])
            ->where('user_id', $user->id)
            ->latest();

        if ($search) {
            $query->whereHas('resource', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $bookmarks = $query->paginate(10)->withQueryString();

        return view('bookmarks.index', compact('bookmarks', 'search'));
    }

    public function toggle(Request $request, Resource $resource): RedirectResponse
    {
        $user = $request->user();

        $bookmark = Bookmark::where('user_id', $user->id)
            ->where('resource_id', $resource->id)
            ->first();

        if ($bookmark) {
            $bookmark->delete();
            $message = 'Resource removed from your bookmarks.';
        } else {
            Bookmark::create([
                'user_id' => $user->id,
                'resource_id' => $resource->id,
                'notes' => $request->input('notes'),
            ]);
            ActivityLog::record('BOOKMARK', $user, $resource);
            $message = 'Resource added to your bookmarks!';
        }

        return back()->with('success', $message);
    }

    public function update(Request $request, Bookmark $bookmark): RedirectResponse
    {
        $user = $request->user();

        if ($bookmark->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $bookmark->update([
            'notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Study notes updated!');
    }

    public function destroy(Request $request, Bookmark $bookmark): RedirectResponse
    {
        $user = $request->user();

        if ($bookmark->user_id !== $user->id) {
            abort(403);
        }

        $bookmark->delete();

        return back()->with('success', 'Bookmark removed.');
    }
}
