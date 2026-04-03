<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WaitlistEntry extends Model
{
    protected $fillable = ['ticket_type_id', 'event_id', 'user_id', 'status', 'notified_at'];

    protected $casts = ['notified_at' => 'datetime'];

    public function ticketType() { return $this->belongsTo(TicketType::class); }
    public function event()      { return $this->belongsTo(Event::class); }
    public function user()       { return $this->belongsTo(User::class); }
}
