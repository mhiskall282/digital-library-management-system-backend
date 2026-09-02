<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Resource;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Resource $resource): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $review = Review::updateOrCreate(
            ['resource_id' => $resource->id, 'user_id' => $user->id],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ? trim($validated['comment']) : null,
            ]
        );

        $resource->recalculateRating();

        ActivityLog::record('REVIEW', $user, $resource, [
            'rating' => $validated['rating'],
        ]);

        return back()->with('success', 'Your review and rating have been posted!');
    }

    public function helpful(Request $request, Review $review): RedirectResponse
    {
        $sessionKey = 'helpful_voted_' . $review->id;
        if (session()->has($sessionKey)) {
            return back()->with('info', 'You have already marked this review as helpful.');
        }

        $review->increment('helpful_count');
        session()->put($sessionKey, true);

        return back()->with('success', 'Marked review as helpful!');
    }

    public function destroy(Request $request, Review $review): RedirectResponse
    {
        $user = $request->user();

        if ($review->user_id !== $user->id && ! $user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $resource = $review->resource;
        $review->delete();
        $resource?->recalculateRating();

        return back()->with('success', 'Review deleted successfully.');
    }
}
