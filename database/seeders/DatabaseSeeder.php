<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // User::factory(10)->create();
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password5656'),
                'role' => 'admin', // Sesuai dengan middleware kamu
                'email_verified_at' => now()
            ]
        );

    }
}
