<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@zeeroh.com'],
            [
                'name'               => 'Zeeroh Admin',
                'phone'              => '08000000000',
                'password'           => Hash::make('Admin@123456'),
                'role'               => 'admin',
                'is_active'          => true,
                'email_verified_at'  => now(),
            ]
        );

        $admin->assignRole('admin');
    }
}
