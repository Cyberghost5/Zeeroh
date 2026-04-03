<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketTransfer;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketTransferNotification extends Notification
{
    public function __construct(
        public readonly TicketTransfer $transfer,
        public readonly Ticket $ticket,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $acceptUrl = route('tickets.transfer.accept', $this->transfer->token);

        return (new MailMessage)
            ->subject('You have a ticket transfer — ' . $this->ticket->event->title)
            ->greeting('Hi ' . $this->transfer->to_name . '!')
            ->line($this->transfer->fromUser->name . ' is transferring a ticket to you.')
            ->line('**Event:** ' . $this->ticket->event->title)
            ->line('**Ticket type:** ' . $this->ticket->ticketType->name)
            ->line('**Date:** ' . $this->ticket->event->start_date->format('l, F j, Y'))
            ->action('Accept Ticket Transfer', $acceptUrl)
            ->line('This link expires in 48 hours. If you did not expect this, you can ignore this email.');
    }
}
