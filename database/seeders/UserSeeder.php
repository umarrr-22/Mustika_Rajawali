<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Vivi Admin',
                'email' => 'vivi@mustikarajawali.com',
                'password' => Hash::make('password'),
                'role_id' => 1,
                'email_verified_at' => now()
            ],
            [
                'name' => 'Umar Teknisi',
                'email' => 'umar@mustikarajawali.com',
                'password' => Hash::make('password'),
                'role_id' => 2,
                'email_verified_at' => now()
            ],
            [
                'name' => 'Hisyam Teknisi',
                'email' => 'hisyam@mustikarajawali.com',
                'password' => Hash::make('password'),
                'role_id' => 2,
                'email_verified_at' => now()
            ],
            [
                'name' => 'Siswanto Teknisi',
                'email' => 'siswanto@mustikarajawali.com',
                'password' => Hash::make('password'),
                'role_id' => 2,
                'email_verified_at' => now()
            ],
            [
                'name' => 'Ragil Kurir',
                'email' => 'ragilm@mustikarajawali.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
                'email_verified_at' => now()
            ],
            [
                'name' => 'Bagas Kurir',
                'email' => 'bagas@mustikarajawali.com',
                'password' => Hash::make('password'),
                'role_id' => 3,
                'email_verified_at' => now()
            ],
            [
                'name' => 'Wawan Refile',
                'email' => 'wawan@mustikarajawali.com',
                'password' => Hash::make('password'),
                'role_id' => 4,
                'email_verified_at' => now()
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}