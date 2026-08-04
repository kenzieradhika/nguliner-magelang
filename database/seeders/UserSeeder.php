<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@nguliner.test'],
            [
                'name' => 'Admin NGuliner',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'editor@nguliner.test'],
            [
                'name' => 'Editor NGuliner',
                'password' => Hash::make('password'),
                'role' => 'editor',
            ]
        );
    }
}
