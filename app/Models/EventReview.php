<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventReview extends Model
{
    protected $fillable = ['event_id', 'user_id', 'ticket_id', 'rating', 'comment', 'is_visible'];

    protected $casts = [
        'rating'     => 'integer',
        'is_visible' => 'boolean',
    ];

    public function event()  { return $this->belongsTo(Event::class); }
    public function user()   { return $this->belongsTo(User::class); }
    public function ticket() { return $this->belongsTo(Ticket::class); }
}
