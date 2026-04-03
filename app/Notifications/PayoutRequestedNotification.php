<?php

namespace App\Notifications;

use App\Models\PayoutRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutRequestedNotification extends Notification
{
    public function __construct(
        public readonly PayoutRequest $payout,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payout Request — ₦' . number_format($this->payout->amount))
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('An organizer has submitted a payout request.')
            ->line('**Organizer:** ' . ($this->payout->organizer->name ?? 'Unknown'))
            ->line('**Amount:** ₦' . number_format($this->payout->amount))
            ->line('**Bank:** ' . $this->payout->bank_name)
            ->line('**Account:** ' . $this->payout->account_number . ' (' . $this->payout->account_name . ')')
            ->action('Review Payout Requests', route('admin.payouts.index'))
            ->line('Please process this request within 2–3 business days.');
    }
}
