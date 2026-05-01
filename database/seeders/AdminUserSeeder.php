<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@culinary.local')],
            [
                'name' => 'Admin',
                'password' => env('ADMIN_PASSWORD', 'admin123'),
                'is_admin' => true,
            ]
        );
    }
}