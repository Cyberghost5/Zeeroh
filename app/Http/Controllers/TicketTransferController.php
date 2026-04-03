<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketTransfer;
use App\Notifications\TicketTransferNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class TicketTransferController extends Controller
{
    /**
     * Show transfer form for a ticket the user owns.
     */
    public function create(string $ticketCode)
    {
        $ticket = Ticket::where('ticket_code', $ticketCode)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->with('event', 'ticketType')
            ->firstOrFail();

        abort_if($ticket->event->start_date->isPast(), 403, 'Cannot transfer a ticket for a past event.');

        $pendingTransfer = $ticket->transfers()->where('status', 'pending')->where('expires_at', '>', now())->first();

        return view('tickets.transfer', compact('ticket', 'pendingTransfer'));
    }

    /**
     * Initiate a transfer — create a token and notify the recipient.
     */
    public function store(Request $request, string $ticketCode)
    {
        $ticket = Ticket::where('ticket_code', $ticketCode)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->with('event', 'ticketType')
            ->firstOrFail();

        abort_if($ticket->event->start_date->isPast(), 403, 'Cannot transfer a ticket for a past event.');

        $validated = $request->validate([
            'to_email' => ['required', 'email', 'max:255'],
            'to_name'  => ['required', 'string', 'max:120'],
        ]);

        // Don't allow transferring to yourself
        if (strtolower($validated['to_email']) === strtolower(auth()->user()->email)) {
            return back()->withErrors(['to_email' => 'You cannot transfer a ticket to yourself.']);
        }

        // Cancel any existing pending transfer
        $ticket->transfers()->where('status', 'pending')->update(['status' => 'cancelled']);

        $transfer = TicketTransfer::create([
            'ticket_id'    => $ticket->id,
            'from_user_id' => auth()->id(),
            'to_email'     => $validated['to_email'],
            'to_name'      => $validated['to_name'],
            'token'        => TicketTransfer::generateToken(),
            'status'       => 'pending',
            'expires_at'   => now()->addHours(48),
        ]);

        // Notify recipient via email
        Notification::route('mail', $validated['to_email'])
            ->notify(new TicketTransferNotification($transfer, $ticket));

        return redirect()->route('tickets.transfer.show', $ticketCode)
            ->with('success', "Transfer link sent to {$validated['to_email']}. Valid for 48 hours.");
    }

    /**
     * Cancel a pending transfer.
     */
    public function cancel(string $ticketCode)
    {
        $ticket = Ticket::where('ticket_code', $ticketCode)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $ticket->transfers()->where('status', 'pending')->update(['status' => 'cancelled']);

        return back()->with('success', 'Transfer cancelled.');
    }

    /**
     * Recipient clicks the accept link — confirm the transfer.
     */
    public function accept(string $token)
    {
        $transfer = TicketTransfer::where('token', $token)
            ->where('status', 'pending')
            ->with('ticket.event', 'ticket.ticketType')
            ->firstOrFail();

        if ($transfer->isExpired()) {
            $transfer->update(['status' => 'expired']);
            abort(410, 'This transfer link has expired.');
        }

        return view('tickets.transfer-accept', compact('transfer'));
    }

    /**
     * Confirm acceptance — update ticket holder.
     */
    public function confirm(Request $request, string $token)
    {
        $transfer = TicketTransfer::where('token', $token)
            ->where('status', 'pending')
            ->with('ticket')
            ->firstOrFail();

        if ($transfer->isExpired()) {
            $transfer->update(['status' => 'expired']);
            abort(410, 'This transfer link has expired.');
        }

        $ticket = $transfer->ticket;
        $ticket->update([
            'holder_name'  => $transfer->to_name,
            'holder_email' => $transfer->to_email,
        ]);

        $transfer->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()->route('home')
            ->with('success', "Ticket {$ticket->ticket_code} successfully transferred to {$transfer->to_name}.");
    }
}
