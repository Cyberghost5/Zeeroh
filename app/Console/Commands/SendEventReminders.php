<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Notifications\EventReminderNotification;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    protected $signature = 'zeeroh:send-event-reminders';
    protected $description = 'Send 24-hour reminder emails to ticket holders';

    public function handle(): void
    {
        $tomorrow = now()->addDay();

        $tickets = Ticket::query()
            ->with(['event', 'user'])
            ->whereHas('event', fn($q) =>
                $q->whereDate('start_date', $tomorrow->toDateString())
                  ->where('status', 'published')
            )
            ->where('status', 'active')
            ->whereNotNull('user_id')
            ->get();

        foreach ($tickets as $ticket) {
            $ticket->user->notify(new EventReminderNotification($ticket->event));
        }

        $this->info("Sent reminders for {$tickets->count()} ticket(s).");
    }
}
