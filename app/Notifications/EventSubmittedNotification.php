<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventSubmittedNotification extends Notification
{
    public function __construct(
        public readonly Event $event,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Event Pending Review — ' . $this->event->title)
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('A new event has been submitted and is awaiting your approval.')
            ->line('**Event:** ' . $this->event->title)
            ->line('**Organizer:** ' . ($this->event->organizer->name ?? 'Unknown'))
            ->line('**Date:** ' . $this->event->start_date->format('l, F j, Y'))
            ->action('Review Event', route('admin.events.show', $this->event))
            ->line('Please review and approve or reject the event at your earliest convenience.');
    }
}
