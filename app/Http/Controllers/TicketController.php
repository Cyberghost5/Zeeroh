<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function show(string $ticketCode)
    {
        $ticket = Ticket::where('ticket_code', $ticketCode)
            ->with('event', 'ticketType', 'order', 'user')
            ->firstOrFail();

        if (auth()->id() !== $ticket->user_id) {
            abort(403);
        }

        return view('tickets.show', compact('ticket'));
    }

    public function download(string $ticketCode)
    {
        $ticket = Ticket::where('ticket_code', $ticketCode)
            ->with('event', 'ticketType', 'order', 'user')
            ->firstOrFail();

        if (auth()->id() !== $ticket->user_id) {
            abort(403);
        }

        // Load QR as base64 for embedding in PDF
        $qrBase64 = null;
        if ($ticket->qr_code_path && Storage::disk('public')->exists($ticket->qr_code_path)) {
            $qrBase64 = 'data:image/svg+xml;base64,' .
                base64_encode(Storage::disk('public')->get($ticket->qr_code_path));
        }

        $pdf = Pdf::loadView('tickets.pdf', compact('ticket', 'qrBase64'))
            ->setPaper([0, 0, 595, 280], 'landscape'); // A5 landscape

        return $pdf->download("ticket-{$ticket->ticket_code}.pdf");
    }
}
