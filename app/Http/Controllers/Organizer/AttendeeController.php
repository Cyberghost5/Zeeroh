<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
use App\Notifications\PostCheckInReviewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendeeController extends Controller
{
    /**
     * List attendees for a specific event.
     */
    public function index(Request $request, Event $event)
    {
        abort_if($event->organizer_id !== Auth::id(), 403);

        $tickets = Ticket::where('event_id', $event->id)
            ->with('ticketType', 'order', 'user')
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('holder_name', 'like', "%{$search}%")
                      ->orWhere('holder_email', 'like', "%{$search}%")
                      ->orWhere('ticket_code', 'like', "%{$search}%");
                });
            })
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('organizer.attendees.index', compact('event', 'tickets'));
    }

    /**
     * Check-in a ticket via AJAX (QR scanner).
     */
    public function checkIn(Request $request)
    {
        $request->validate(['ticket_code' => 'required|string']);

        $ticket = Ticket::where('ticket_code', $request->ticket_code)
            ->with('event', 'ticketType', 'user')
            ->first();

        if (!$ticket) {
            return response()->json(['success' => false, 'message' => 'Ticket not found.'], 404);
        }

        if ($ticket->event->organizer_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        if ($ticket->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Ticket is ' . $ticket->status . ' and cannot be checked in.',
                'ticket'  => $this->ticketPayload($ticket),
            ]);
        }

        if ($ticket->checked_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket already checked in at ' . $ticket->checked_in_at->format('M j, Y H:i'),
                'ticket'  => $this->ticketPayload($ticket),
            ]);
        }

        $ticket->update(['checked_in_at' => now(), 'status' => 'used']);

        if ($ticket->user) {
            $ticket->user->notify(
                (new PostCheckInReviewNotification($ticket->load('event')))->delay(now()->addHours(2))
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful!',
            'ticket'  => $this->ticketPayload($ticket),
        ]);
    }

    /**
     * QR scanner page for an event.
     */
    public function scanner(Event $event)
    {
        abort_if($event->organizer_id !== Auth::id(), 403);
        return view('organizer.attendees.scanner', compact('event'));
    }

    private function ticketPayload(Ticket $ticket): array
    {
        return [
            'ticket_code'   => $ticket->ticket_code,
            'holder_name'   => $ticket->holder_name ?? $ticket->user?->name ?? 'N/A',
            'holder_email'  => $ticket->holder_email ?? $ticket->user?->email,
            'ticket_type'   => $ticket->ticketType->name,
            'event'         => $ticket->event->title,
            'status'        => $ticket->status,
            'checked_in_at' => $ticket->checked_in_at?->format('M j, Y H:i'),
        ];
    }
}
