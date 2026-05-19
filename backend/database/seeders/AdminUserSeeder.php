<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Namine',
            'email' => 'naminebadane@gmail.com',
            'password' => Hash::make('namine44'),
            'is_admin' => true,
        ]);
    }
}