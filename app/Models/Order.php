<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'event_id', 'promo_code_id', 'order_number', 'subtotal',
        'discount_amount', 'service_fee', 'commission', 'total_amount',
        'payment_gateway', 'payment_reference', 'payment_status', 'paid_at',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'service_fee'     => 'decimal:2',
        'commission'      => 'decimal:2',
        'total_amount'    => 'decimal:2',
        'paid_at'         => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    public static function generateOrderNumber(): string
    {
        return 'ZR-' . strtoupper(substr(uniqid(), -8));
    }
}
