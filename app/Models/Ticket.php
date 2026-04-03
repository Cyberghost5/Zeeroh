<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $fillable = [
        'order_id', 'order_item_id', 'ticket_type_id', 'event_id', 'user_id',
        'ticket_code', 'qr_code_path', 'holder_name', 'holder_email',
        'holder_phone', 'status', 'checked_in_at',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transfers()
    {
        return $this->hasMany(TicketTransfer::class);
    }

    public function review()
    {
        return $this->hasOne(EventReview::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($ticket) {
            $ticket->ticket_code = 'TK-' . strtoupper(Str::random(10));
        });
    }
}
