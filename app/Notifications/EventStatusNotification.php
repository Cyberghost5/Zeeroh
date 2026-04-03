<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventStatusNotification extends Notification
{
    public function __construct(
        public readonly Event $event,
        public readonly string $status, // 'approved' | 'rejected'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->status === 'approved') {
            return (new MailMessage)
                ->subject('Your event has been approved — ' . $this->event->title)
                ->greeting('Great news, ' . $notifiable->name . '!')
                ->line('Your event **' . $this->event->title . '** has been approved and is now live on Zeeroh.')
                ->action('View Your Event', route('events.show', $this->event->slug))
                ->line('Start sharing it with your audience!');
        }

        return (new MailMessage)
            ->subject('Event not approved — ' . $this->event->title)
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('Unfortunately, your event **' . $this->event->title . '** was not approved.')
            ->when($this->event->rejection_reason, fn($m) =>
                $m->line('**Reason:** ' . $this->event->rejection_reason)
            )
            ->line('You can edit the event and resubmit for review.')
            ->action('Edit Event', route('organizer.events.edit', $this->event))
            ->line('If you have questions, please contact support.');
    }
}
