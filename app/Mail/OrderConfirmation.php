<?php

namespace App\Mail;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Zeeroh Tickets – ' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-confirmation',
            with: ['order' => $this->order],
        );
    }

    public function attachments(): array
    {
        $this->order->loadMissing(['tickets.event', 'tickets.ticketType', 'tickets.user']);

        $attachments = [];

        foreach ($this->order->tickets as $ticket) {
            $qrBase64 = null;
            if ($ticket->qr_code_path && Storage::disk('public')->exists($ticket->qr_code_path)) {
                $qrBase64 = 'data:image/svg+xml;base64,' .
                    base64_encode(Storage::disk('public')->get($ticket->qr_code_path));
            }

            $pdfContent = Pdf::loadView('tickets.pdf', compact('ticket', 'qrBase64'))
                ->setPaper([0, 0, 595, 280], 'landscape')
                ->output();

            $attachments[] = Attachment::fromData(
                fn() => $pdfContent,
                "ticket-{$ticket->ticket_code}.pdf"
            )->withMime('application/pdf');
        }

        return $attachments;
    }
}
