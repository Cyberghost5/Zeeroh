<?php

namespace Database\Seeders;

use App\Models\CommissionSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommissionSettingsSeeder extends Seeder
{
    public function run(): void
    {
        CommissionSetting::firstOrCreate(
            ['is_active' => true],
            [
                'commission_percentage' => 5.00,
                'service_fee_type'      => 'fixed',
                'service_fee_value'     => 100.00,
                'is_active'             => true,
            ]
        );
    }
}
