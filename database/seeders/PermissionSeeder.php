<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default permissions if they don't exist
        Permission::createDefaultPermissions();
        
        $this->command->info('Default permissions created successfully!');
    }
}
