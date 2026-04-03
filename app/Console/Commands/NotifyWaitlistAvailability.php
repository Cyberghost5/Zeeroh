<?php

namespace App\Console\Commands;

use App\Models\TicketType;
use App\Notifications\WaitlistAvailableNotification;
use Illuminate\Console\Command;

class NotifyWaitlistAvailability extends Command
{
    protected $signature = 'zeeroh:notify-waitlist';
    protected $description = 'Notify waitlisted users when a ticket type has availability';

    public function handle(): void
    {
        $types = TicketType::query()
            ->whereHas('waitlist', fn($q) => $q->where('status', 'waiting'))
            ->with(['event', 'waitlist.user'])
            ->get()
            ->filter(fn($type) => $type->quantity > $type->tickets()->where('status', 'active')->count());

        $notified = 0;

        foreach ($types as $type) {
            $available = $type->quantity - $type->tickets()->where('status', 'active')->count();

            $entries = $type->waitlist
                ->where('status', 'waiting')
                ->take($available);

            foreach ($entries as $entry) {
                $entry->user->notify(new WaitlistAvailableNotification($type, $type->event));
                $entry->update(['status' => 'notified', 'notified_at' => now()]);
                $notified++;
            }
        }

        $this->info("Notified {$notified} waitlisted user(s).");
    }
}
