<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Dept Head',
                'email' => 'dept_head@example.com',
                'role' => 0,
            ],
            [
                'name' => 'Supervisor',
                'email' => 'supervisor@example.com',
                'role' => 1,
            ],
            [
                'name' => 'Foreman',
                'email' => 'foreman@example.com',
                'role' => 2,
            ],
            [
                'name' => 'Analis Kimia',
                'email' => 'analis_kimia@example.com',
                'role' => 3,
            ],
            [
                'name' => 'Analis Mikro',
                'email' => 'analis_mikro@example.com',
                'role' => 4,
            ],
            [
                'name' => 'Analis RM',
                'email' => 'analis_rm@example.com',
                'role' => 5,
            ],
            [
                'name' => 'Analis Field',
                'email' => 'analis_field@example.com',
                'role' => 6,
            ],
            [
                'name' => 'Operator',
                'email' => 'operator@example.com',
                'role' => 7,
            ],
            [
                'name' => 'Helper',
                'email' => 'helper@example.com',
                'role' => 8,
            ],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'password' => Hash::make('password'), // default password
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
                'created_at' => now(),  
                'updated_at' => now(),
            ]);
        }
    }
}
