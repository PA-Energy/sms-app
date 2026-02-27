<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'email' => 'admin@smsapp.com',
                'password' => Hash::make('admin123'),
            ]
        );

        $this->command->info('Admin user created successfully!');
        $this->command->info('Username: admin');
        $this->command->info('Password: admin123');
    }
}
