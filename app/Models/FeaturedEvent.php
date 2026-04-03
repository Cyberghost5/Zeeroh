<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedEvent extends Model
{
    protected $fillable = [
        'event_id', 'position', 'amount_paid', 'starts_at', 'ends_at', 'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'amount_paid' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
