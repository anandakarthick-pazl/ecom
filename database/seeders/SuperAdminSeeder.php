<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Create the main Super Admin user
        User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@ecomplatform.com',
            'password' => Hash::make('password123'),
            'is_super_admin' => true,
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now()
        ]);

        // Create additional super admin users
        User::create([
            'name' => 'John Doe',
            'email' => 'john@ecomplatform.com',
            'password' => Hash::make('password123'),
            'is_super_admin' => true,
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@ecomplatform.com',
            'password' => Hash::make('password123'),
            'is_super_admin' => true,
            'role' => 'manager',
            'status' => 'active',
            'email_verified_at' => now()
        ]);
    }
}
