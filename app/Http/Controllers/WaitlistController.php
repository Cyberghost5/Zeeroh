<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TicketType;
use App\Models\WaitlistEntry;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    public function join(Request $request)
    {
        $validated = $request->validate([
            'ticket_type_id' => ['required', 'integer', 'exists:ticket_types,id'],
        ]);

        $ticketType = TicketType::with('event')->findOrFail($validated['ticket_type_id']);

        // Only allow joining waitlist for sold-out types
        abort_if($ticketType->isAvailable(), 422, 'Tickets are still available — no need to join the waitlist.');
        abort_if($ticketType->event->start_date->isPast(), 422, 'This event has already passed.');

        WaitlistEntry::firstOrCreate([
            'ticket_type_id' => $ticketType->id,
            'event_id'       => $ticketType->event_id,
            'user_id'        => auth()->id(),
        ], ['status' => 'waiting']);

        if ($request->expectsJson()) {
            return response()->json(['joined' => true]);
        }

        return back()->with('success', "You're on the waitlist for \"{$ticketType->name}\". We'll email you if tickets become available.");
    }

    public function leave(Request $request)
    {
        $validated = $request->validate([
            'ticket_type_id' => ['required', 'integer', 'exists:ticket_types,id'],
        ]);

        WaitlistEntry::where('ticket_type_id', $validated['ticket_type_id'])
            ->where('user_id', auth()->id())
            ->delete();

        if ($request->expectsJson()) {
            return response()->json(['joined' => false]);
        }

        return back()->with('success', 'Removed from waitlist.');
    }
}
