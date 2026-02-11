<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::create(['name' => 'admin', 'description' => 'Administrator']);
        Role::create(['name' => 'teknisi', 'description' => 'Teknisi Service']);
        Role::create(['name' => 'kurir', 'description' => 'Kurir Pengiriman']);
        Role::create(['name' => 'refil', 'description' => 'Staff Refil']);
    }
}