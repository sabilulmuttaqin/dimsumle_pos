<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'owner',
            'email' => 'owner@gmail.com',
            'password' => Hash::make('owner123'),
            'role' => 'owner'
        ]);

        User::create([
            'name' => 'kasir',
            'email' => 'kasir@gmail.com',
            'password' => Hash::make('akunkasir'),
            'role' => 'kasir'
        ]);
    }
}
