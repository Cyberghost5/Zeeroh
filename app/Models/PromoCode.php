<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'organizer_id', 'event_id', 'code', 'type', 'value',
        'usage_limit', 'used_count', 'min_order_amount',
        'valid_from', 'valid_until', 'is_active',
    ];

    protected $casts = [
        'value'            => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'used_count'       => 'integer',
        'usage_limit'      => 'integer',
        'is_active'        => 'boolean',
        'valid_from'       => 'datetime',
        'valid_until'      => 'datetime',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isValid(float $subtotal = 0): bool
    {
        if (!$this->is_active) return false;
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) return false;
        if ($this->valid_from && now()->lt($this->valid_from)) return false;
        if ($this->valid_until && now()->gt($this->valid_until)) return false;
        if ($this->min_order_amount && $subtotal < $this->min_order_amount) return false;
        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            return round($subtotal * ($this->value / 100), 2);
        }
        return min((float) $this->value, $subtotal);
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }
}
