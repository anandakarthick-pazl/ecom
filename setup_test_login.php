<?php
// Quick database check script
// Run: php artisan tinker, then copy and paste this code

echo "=== QUICK LOGIN DEBUG ===\n\n";

try {
    // Check companies
    echo "1. Checking for greenvalleyherbs domain...\n";
    $company = \App\Models\SuperAdmin\Company::where('domain', 'greenvalleyherbs.local:8000')->first();
    
    if (!$company) {
        echo "❌ No company found with domain 'greenvalleyherbs.local:8000'\n";
        echo "Looking for all companies:\n";
        $companies = \App\Models\SuperAdmin\Company::all();
        foreach ($companies as $comp) {
            echo "  - {$comp->name}: {$comp->domain} (Status: {$comp->status})\n";
        }
        
        // Try to create or update company
        echo "\n2. Creating/updating company...\n";
        $company = \App\Models\SuperAdmin\Company::updateOrCreate(
            ['domain' => 'greenvalleyherbs.local:8000'],
            [
                'name' => 'Green Valley Herbs',
                'slug' => 'green-valley-herbs',
                'status' => 'active',
                'package_id' => 1, // Assuming package 1 exists
                'trial_ends_at' => now()->addMonths(1),
                'subscription_status' => 'trial'
            ]
        );
        echo "✅ Company created/updated: {$company->name} (ID: {$company->id})\n";
    } else {
        echo "✅ Company found: {$company->name} (ID: {$company->id}, Status: {$company->status})\n";
    }
    
    // Check users for this company
    echo "\n3. Checking users for company ID {$company->id}...\n";
    $companyUsers = \App\Models\User::where('company_id', $company->id)->get();
    
    if ($companyUsers->count() === 0) {
        echo "❌ No users found for this company\n";
        echo "Creating admin user...\n";
        
        $adminUser = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@greenvalleyherbs.com',
            'password' => bcrypt('password123'),
            'company_id' => $company->id,
            'role' => 'admin',
            'status' => 'active'
        ]);
        echo "✅ Admin user created: {$adminUser->email} / password123\n";
    } else {
        echo "✅ Found " . $companyUsers->count() . " users:\n";
        foreach ($companyUsers as $user) {
            echo "  - {$user->email} (Role: {$user->role}, Status: {$user->status})\n";
        }
    }
    
    // Check super admin users
    echo "\n4. Checking super admin users...\n";
    $superAdmins = \App\Models\User::where('is_super_admin', true)->get();
    
    if ($superAdmins->count() === 0) {
        echo "❌ No super admin users found\n";
        echo "Creating super admin...\n";
        
        $superAdmin = \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@ecom.com',
            'password' => bcrypt('superadmin123'),
            'company_id' => null,
            'role' => 'super_admin',
            'is_super_admin' => true,
            'status' => 'active'
        ]);
        echo "✅ Super admin created: {$superAdmin->email} / superadmin123\n";
    } else {
        echo "✅ Found " . $superAdmins->count() . " super admin users:\n";
        foreach ($superAdmins as $user) {
            echo "  - {$user->email} (Company ID: {$user->company_id})\n";
        }
    }
    
    echo "\n=== LOGIN CREDENTIALS ===\n";
    echo "URL: http://greenvalleyherbs.local:8000/login\n\n";
    
    $testUser = \App\Models\User::where('company_id', $company->id)
                               ->whereIn('role', ['admin', 'manager'])
                               ->first();
    
    if ($testUser) {
        echo "Company Admin Login:\n";
        echo "Email: {$testUser->email}\n";
        echo "Password: [Check database or reset password]\n";
        echo "Role: {$testUser->role}\n\n";
    }
    
    $testSuperAdmin = \App\Models\User::where('is_super_admin', true)->first();
    if ($testSuperAdmin) {
        echo "Super Admin Login (works on any domain):\n";
        echo "Email: {$testSuperAdmin->email}\n";
        echo "Password: [Check database or reset password]\n\n";
    }
    
    echo "Test user with known password:\n";
    echo "Email: admin@greenvalleyherbs.com\n";
    echo "Password: password123\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== END DEBUG ===\n";
