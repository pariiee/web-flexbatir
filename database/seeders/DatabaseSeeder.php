<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun demo utama — langsung bisa login di app
        User::factory()->create([
            'name'                   => 'Demo User',
            'username'               => 'demo',
            'email'                  => 'demo@flexbatir.com',
            'password'               => bcrypt('password'),
            'bio'                    => 'Akun demo FlexBatir 🏃',
            'gender'                 => 'male',
            'weight'                 => 70,
            'height'                 => 170,
            'measurement_preference' => 'metric',
            'is_private'             => false,
        ]);

        // Beberapa akun tambahan untuk test follow/feed
        $users = [
            ['name' => 'Andi Runner',  'username' => 'andirun',  'email' => 'andi@flexbatir.com'],
            ['name' => 'Budi Cyclist', 'username' => 'budicycle', 'email' => 'budi@flexbatir.com'],
            ['name' => 'Cici Walker',  'username' => 'ciciwalk',  'email' => 'cici@flexbatir.com'],
        ];

        foreach ($users as $u) {
            User::factory()->create([
                ...$u,
                'password'               => bcrypt('password'),
                'measurement_preference' => 'metric',
                'is_private'             => false,
            ]);
        }
    }
}
