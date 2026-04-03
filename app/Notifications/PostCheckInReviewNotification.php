<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostCheckInReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Ticket $ticket) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $event = $this->ticket->event;

        return (new MailMessage)
            ->subject('Thanks for attending ' . $event->title . ' — Share your experience!')
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('Thank you for attending **' . $event->title . '** — we hope you had a great time!')
            ->line('Your feedback means a lot to the organizer and helps other attendees discover great events.')
            ->action('Leave a Review', route('events.show', $event->slug) . '#reviews')
            ->line('It only takes a minute. We appreciate you being part of the Zeeroh community!');
    }
}
