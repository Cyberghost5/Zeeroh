<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    protected $fillable = [
        'commission_percentage', 'service_fee_type', 'service_fee_value', 'is_active',
    ];

    protected $casts = [
        'commission_percentage' => 'decimal:2',
        'service_fee_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public static function active(): self
    {
        return static::where('is_active', true)->firstOrCreate([], [
            'commission_percentage' => 5.00,
            'service_fee_type' => 'fixed',
            'service_fee_value' => 100.00,
            'is_active' => true,
        ]);
    }

    public function calculateServiceFee(float $subtotal): float
    {
        if ($this->service_fee_type === 'percentage') {
            return round($subtotal * ($this->service_fee_value / 100), 2);
        }
        return (float) $this->service_fee_value;
    }

    public function calculateCommission(float $subtotal): float
    {
        return round($subtotal * ($this->commission_percentage / 100), 2);
    }
}
