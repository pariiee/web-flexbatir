<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@flexbatir.com'],
            [
                'name'                   => 'Admin FlexBatir',
                'username'               => 'admin',
                'password'               => Hash::make('admin123!'),
                'is_admin'               => true,
                'is_banned'              => false,
                'measurement_preference' => 'metric',
                'is_private'             => false,
            ]
        );

        $this->command->info('Admin seeded: admin@flexbatir.com / admin123!');
    }
}
