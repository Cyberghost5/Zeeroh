<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TicketTransfer extends Model
{
    protected $fillable = [
        'ticket_id', 'from_user_id', 'to_email', 'to_name',
        'token', 'status', 'expires_at', 'completed_at',
    ];

    protected $casts = [
        'expires_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function ticket()   { return $this->belongsTo(Ticket::class); }
    public function fromUser() { return $this->belongsTo(User::class, 'from_user_id'); }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }
}
