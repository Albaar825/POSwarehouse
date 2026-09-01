<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Warehouse',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('adminhoodie123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
