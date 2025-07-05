<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SimpleSetup extends Command
{
    protected $signature = 'superadmin:simple-setup';
    protected $description = 'Simple Super Admin setup without migrations';

    public function handle()
    {
        $this->info('🚀 Simple Super Admin Setup...');

        try {
            // Ensure required columns exist
            $this->info('Adding required columns to users table...');
            
            $columns = [
                'is_super_admin' => 'BOOLEAN DEFAULT FALSE',
                'role' => "VARCHAR(255) DEFAULT 'admin'",
                'status' => "VARCHAR(255) DEFAULT 'active'",
                'company_id' => 'BIGINT UNSIGNED NULL'
            ];

            foreach ($columns as $column => $definition) {
                try {
                    DB::statement("ALTER TABLE users ADD COLUMN {$column} {$definition}");
                    $this->info("✓ Added {$column} column");
                } catch (\Exception $e) {
                    $this->warn("Column {$column} might already exist");
                }
            }

            // Create super admin user
            $this->info('Creating super admin user...');
            DB::table('users')->updateOrInsert(
                ['email' => 'superadmin@ecomplatform.com'],
                [
                    'name' => 'Super Administrator',
                    'email' => 'superadmin@ecomplatform.com',
                    'password' => Hash::make('password123'),
                    'is_super_admin' => 1,
                    'role' => 'admin',
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            $this->info('✓ Super admin user created');

            // Create basic tables if they don't exist
            $this->createBasicTables();

            // Insert sample data
            $this->insertSampleData();

            $this->newLine();
            $this->info('🎉 Simple Setup Complete!');
            $this->newLine();

            $this->table(['Access Point', 'URL', 'Credentials'], [
                ['Super Admin', 'http://localhost:8000/super-admin/login', 'superadmin@ecomplatform.com / password123'],
                ['Debug Info', 'http://localhost:8000/super-admin-debug', 'Check system status']
            ]);

        } catch (\Exception $e) {
            $this->error('Setup failed: ' . $e->getMessage());
            $this->line('Please check your database connection and try again.');
            return 1;
        }

        return 0;
    }

    private function createBasicTables()
    {
        $this->info('Creating basic tables...');

        // Themes table
        try {
            DB::statement("
                CREATE TABLE IF NOT EXISTS themes (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    slug VARCHAR(255) UNIQUE NOT NULL,
                    description TEXT,
                    category VARCHAR(255),
                    price DECIMAL(10,2) DEFAULT 0,
                    is_free BOOLEAN DEFAULT TRUE,
                    features JSON,
                    status ENUM('active', 'inactive') DEFAULT 'active',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ");
            $this->info('✓ Themes table created');
        } catch (\Exception $e) {
            $this->warn('Themes table might already exist');
        }

        // Packages table
        try {
            DB::statement("
                CREATE TABLE IF NOT EXISTS packages (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    slug VARCHAR(255) UNIQUE NOT NULL,
                    description TEXT,
                    price DECIMAL(10,2) DEFAULT 0,
                    billing_cycle ENUM('monthly', 'yearly', 'lifetime') DEFAULT 'monthly',
                    trial_days INT DEFAULT 15,
                    features JSON,
                    limits JSON,
                    is_popular BOOLEAN DEFAULT FALSE,
                    sort_order INT DEFAULT 0,
                    status ENUM('active', 'inactive') DEFAULT 'active',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ");
            $this->info('✓ Packages table created');
        } catch (\Exception $e) {
            $this->warn('Packages table might already exist');
        }

        // Companies table
        try {
            DB::statement("
                CREATE TABLE IF NOT EXISTS companies (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    slug VARCHAR(255) UNIQUE NOT NULL,
                    domain VARCHAR(255) UNIQUE NOT NULL,
                    email VARCHAR(255),
                    phone VARCHAR(255),
                    address TEXT,
                    city VARCHAR(255),
                    state VARCHAR(255),
                    country VARCHAR(255),
                    postal_code VARCHAR(255),
                    theme_id BIGINT UNSIGNED,
                    package_id BIGINT UNSIGNED,
                    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
                    trial_ends_at TIMESTAMP NULL,
                    subscription_ends_at TIMESTAMP NULL,
                    created_by BIGINT UNSIGNED,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )
            ");
            $this->info('✓ Companies table created');
        } catch (\Exception $e) {
            $this->warn('Companies table might already exist');
        }
    }

    private function insertSampleData()
    {
        $this->info('Inserting sample data...');

        // Sample themes
        $themes = [
            ['name' => 'Fashion Store', 'slug' => 'fashion-store', 'description' => 'Modern fashion theme', 'category' => 'clothing', 'price' => 0, 'is_free' => 1],
            ['name' => 'Electronics Hub', 'slug' => 'electronics-hub', 'description' => 'Tech theme for electronics', 'category' => 'electronics', 'price' => 49.99, 'is_free' => 0],
            ['name' => 'Universal Store', 'slug' => 'universal-store', 'description' => 'Versatile general theme', 'category' => 'general', 'price' => 0, 'is_free' => 1]
        ];

        foreach ($themes as $theme) {
            DB::table('themes')->updateOrInsert(
                ['slug' => $theme['slug']], 
                array_merge($theme, ['created_at' => now(), 'updated_at' => now()])
            );
        }
        $this->info('✓ Sample themes inserted');

        // Sample packages
        $packages = [
            ['name' => 'Starter', 'slug' => 'starter', 'description' => 'Perfect for small businesses', 'price' => 29.99, 'billing_cycle' => 'monthly', 'trial_days' => 15, 'sort_order' => 1],
            ['name' => 'Professional', 'slug' => 'professional', 'description' => 'Ideal for growing businesses', 'price' => 59.99, 'billing_cycle' => 'monthly', 'trial_days' => 15, 'is_popular' => 1, 'sort_order' => 2],
            ['name' => 'Enterprise', 'slug' => 'enterprise', 'description' => 'For large businesses', 'price' => 149.99, 'billing_cycle' => 'monthly', 'trial_days' => 30, 'sort_order' => 3]
        ];

        foreach ($packages as $package) {
            DB::table('packages')->updateOrInsert(
                ['slug' => $package['slug']], 
                array_merge($package, ['created_at' => now(), 'updated_at' => now()])
            );
        }
        $this->info('✓ Sample packages inserted');

        $this->info('✓ Sample data insertion complete');
    }
}
