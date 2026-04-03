<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    protected $fillable = [
        'event_id', 'name', 'description', 'price', 'quantity',
        'quantity_sold', 'max_per_order', 'sale_start', 'sale_end', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_start' => 'datetime',
        'sale_end' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function event()      { return $this->belongsTo(Event::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
    public function waitlist()   { return $this->hasMany(WaitlistEntry::class); }

    public function getAvailableQuantityAttribute()
    {
        return $this->quantity - $this->quantity_sold;
    }

    public function isAvailable(): bool
    {
        return $this->is_active && $this->available_quantity > 0;
    }
}
