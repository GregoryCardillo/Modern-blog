<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'), // change before production
                'role' => 'admin',
            ]
        );
    }
}
