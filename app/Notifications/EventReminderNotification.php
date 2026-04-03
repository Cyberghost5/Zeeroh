<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification
{
    public function __construct(public readonly Event $event) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reminder: ' . $this->event->title . ' is tomorrow!')
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('Just a reminder that **' . $this->event->title . '** is happening tomorrow.')
            ->line('📅 ' . $this->event->start_date->format('l, F j, Y') . ' at ' . $this->event->start_time)
            ->line($this->event->is_virtual
                ? '🌐 Online / Virtual'
                : '📍 ' . $this->event->venue_name . ', ' . $this->event->city)
            ->action('View Your Tickets', route('dashboard'))
            ->line('See you there!');
