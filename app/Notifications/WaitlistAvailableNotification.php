<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WaitlistAvailableNotification extends Notification
{
    public function __construct(
        public readonly TicketType $ticketType,
        public readonly Event $event,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tickets available — ' . $this->event->title)
            ->greeting('Good news, ' . $notifiable->name . '!')
            ->line('Tickets for **' . $this->ticketType->name . '** at **' . $this->event->title . '** are now available.')
            ->line('Grab yours before they sell out again!')
            ->action('Get Tickets Now', route('events.show', $this->event->slug))
            ->line('Hurry — availability is limited!');
    }
}
