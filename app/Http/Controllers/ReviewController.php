<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventReview;
use App\Models\Ticket;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Event $event)
    {
        // Must have a checked-in or used ticket for this event
        $ticket = Ticket::where('user_id', auth()->id())
            ->where('event_id', $event->id)
            ->where('status', 'used')
            ->first();

        abort_if(! $ticket, 403, 'You can only review events you attended.');
        abort_if(
            EventReview::where('event_id', $event->id)->where('user_id', auth()->id())->exists(),
            422,
            'You have already reviewed this event.'
        );

        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        EventReview::create([
            'event_id'  => $event->id,
            'user_id'   => auth()->id(),
            'ticket_id' => $ticket->id,
            'rating'    => $validated['rating'],
            'comment'   => $validated['comment'] ?? null,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Thank you for your review!');
    }

    public function destroy(EventReview $review)
    {
        abort_if($review->user_id !== auth()->id() && ! auth()->user()->hasRole('admin'), 403);

        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
