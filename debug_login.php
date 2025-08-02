<?php
// Debug script for login issues
// Run this with: php artisan tinker

echo "=== LOGIN DEBUG SCRIPT ===\n\n";

try {
    // Check database connection
    echo "1. Checking database connection...\n";
    $connection = DB::connection();
    $pdo = $connection->getPdo();
    echo "✓ Database connected successfully\n\n";
    
    // Check companies table
    echo "2. Checking companies for domain 'greenvalleyherbs.local:8000'...\n";
    $companies = DB::table('companies')->get();
    echo "Total companies in database: " . $companies->count() . "\n";
    
    if ($companies->count() > 0) {
        echo "\nCompanies found:\n";
        foreach ($companies as $company) {
            echo "- ID: {$company->id}, Name: {$company->name}, Domain: {$company->domain}, Status: {$company->status}\n";
        }
    }
    
    // Look for greenvalleyherbs specifically
    $greenValley = DB::table('companies')->where('domain', 'greenvalleyherbs.local:8000')->first();
    if ($greenValley) {
        echo "\n✓ Found Green Valley Herbs company:\n";
        echo "  ID: {$greenValley->id}\n";
        echo "  Name: {$greenValley->name}\n";
        echo "  Domain: {$greenValley->domain}\n";
        echo "  Status: {$greenValley->status}\n";
    } else {
        echo "\n✗ Green Valley Herbs company NOT found with domain 'greenvalleyherbs.local:8000'\n";
        echo "   Checking for similar domains...\n";
        $similarDomains = DB::table('companies')->where('domain', 'like', '%greenvalley%')->get();
        if ($similarDomains->count() > 0) {
            echo "   Found similar domains:\n";
            foreach ($similarDomains as $company) {
                echo "   - {$company->name}: {$company->domain}\n";
            }
        }
    }
    
    echo "\n3. Checking users table...\n";
    $users = DB::table('users')->get();
    echo "Total users in database: " . $users->count() . "\n";
    
    if ($users->count() > 0) {
        echo "\nUsers found:\n";
        foreach ($users as $user) {
            $role = $user->role ?? 'N/A';
            $companyId = $user->company_id ?? 'N/A'; 
            $isSuperAdmin = $user->is_super_admin ? 'Yes' : 'No';
            echo "- ID: {$user->id}, Email: {$user->email}, Role: {$role}, Company ID: {$companyId}, Super Admin: {$isSuperAdmin}\n";
        }
    }
    
    // Check for admin users in Green Valley Herbs company
    if ($greenValley) {
        echo "\n4. Checking admin users for Green Valley Herbs (Company ID: {$greenValley->id})...\n";
        $adminUsers = DB::table('users')
            ->where('company_id', $greenValley->id)
            ->whereIn('role', ['admin', 'manager'])
            ->get();
            
        if ($adminUsers->count() > 0) {
            echo "Admin users found:\n";
            foreach ($adminUsers as $user) {
                echo "- Email: {$user->email}, Role: {$user->role}\n";
            }
        } else {
            echo "✗ No admin users found for Green Valley Herbs company\n";
            echo "   Checking all users for this company...\n";
            $allCompanyUsers = DB::table('users')->where('company_id', $greenValley->id)->get();
            if ($allCompanyUsers->count() > 0) {
                foreach ($allCompanyUsers as $user) {
                    echo "   - Email: {$user->email}, Role: {$user->role}\n";
                }
            } else {
                echo "   No users found for this company\n";
            }
        }
    }
    
    // Check super admin users
    echo "\n5. Checking super admin users...\n";
    $superAdmins = DB::table('users')->where('is_super_admin', true)->get();
    if ($superAdmins->count() > 0) {
        echo "Super admin users found:\n";
        foreach ($superAdmins as $user) {
            echo "- Email: {$user->email}, Company ID: {$user->company_id}\n";
        }
    } else {
        echo "✗ No super admin users found\n";
    }
    
    echo "\n=== RECOMMENDATIONS ===\n";
    if (!$greenValley) {
        echo "1. Create company with domain 'greenvalleyherbs.local:8000'\n";
        echo "2. Or update existing company domain to match\n";
    }
    
    if ($greenValley && DB::table('users')->where('company_id', $greenValley->id)->whereIn('role', ['admin', 'manager'])->count() == 0) {
        echo "3. Create admin user for Green Valley Herbs company\n";
        echo "4. Or update existing user role to 'admin' or 'manager'\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== END DEBUG ===\n";
