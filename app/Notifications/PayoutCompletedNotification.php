<?php

namespace App\Notifications;

use App\Models\PayoutRequest;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutCompletedNotification extends Notification
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
            ->subject('Your Payout Has Been Sent — ₦' . number_format($this->payout->amount))
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('Great news! Your payout request has been processed and the funds have been sent.')
            ->line('**Amount:** ₦' . number_format($this->payout->amount))
            ->line('**Bank:** ' . $this->payout->bank_name)
            ->line('**Account:** ' . $this->payout->account_number . ' (' . $this->payout->account_name . ')')
            ->line('Please allow 1–2 business days for the funds to reflect in your account.')
            ->action('View Payout History', route('organizer.payouts.index'))
            ->line('Thank you for using Zeeroh!');
    }
}
