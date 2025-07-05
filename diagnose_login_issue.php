<?php

/**
 * Login Diagnostic Script
 * Helps identify why tenant login is failing
 */

// Check if we're in the right environment
if (!function_exists('app') || !app()->environment(['local', 'development'])) {
    die('This script can only be run in local/development environment');
}

echo "=== TENANT LOGIN DIAGNOSTIC ===\n\n";

try {
    // 1. Check the company for greenvalleyherbs.local domain
    echo "🏢 CHECKING COMPANY FOR DOMAIN: greenvalleyherbs.local\n";
    echo str_repeat("-", 50) . "\n";
    
    $company = \App\Models\SuperAdmin\Company::where('domain', 'greenvalleyherbs.local')->first();
    
    if ($company) {
        echo "✅ Company Found:\n";
        echo "   - ID: {$company->id}\n";
        echo "   - Name: {$company->name}\n";
        echo "   - Domain: {$company->domain}\n";
        echo "   - Status: {$company->status}\n";
        echo "   - Created: {$company->created_at}\n\n";
    } else {
        echo "❌ NO COMPANY FOUND for domain 'greenvalleyherbs.local'\n";
        echo "   This means the domain is not registered in the system.\n\n";
        
        // Show all companies
        echo "📋 ALL COMPANIES IN SYSTEM:\n";
        $allCompanies = \App\Models\SuperAdmin\Company::all();
        foreach ($allCompanies as $comp) {
            echo "   - {$comp->name} → {$comp->domain} (ID: {$comp->id})\n";
        }
        echo "\n";
    }
    
    // 2. Check users for this company (if company exists)
    if ($company) {
        echo "👥 CHECKING USERS FOR COMPANY: {$company->name}\n";
        echo str_repeat("-", 50) . "\n";
        
        $users = \App\Models\User::where('company_id', $company->id)->get();
        
        if ($users->count() > 0) {
            echo "✅ Found {$users->count()} user(s) for this company:\n";
            foreach ($users as $user) {
                echo "   - Email: {$user->email}\n";
                echo "     Role: {$user->role}\n";
                echo "     Company ID: {$user->company_id}\n";
                echo "     Status: " . ($user->email_verified_at ? 'Verified' : 'Not Verified') . "\n";
                echo "     Last Login: " . ($user->last_login_at ?? 'Never') . "\n";
                echo "     Created: {$user->created_at}\n\n";
            }
        } else {
            echo "❌ NO USERS FOUND for company ID: {$company->id}\n";
            echo "   This means no users are assigned to this company.\n\n";
        }
        
        // 3. Check admin/manager users specifically
        echo "🔐 CHECKING ADMIN/MANAGER USERS FOR COMPANY:\n";
        echo str_repeat("-", 50) . "\n";
        
        $adminUsers = \App\Models\User::where('company_id', $company->id)
                                    ->whereIn('role', ['admin', 'manager'])
                                    ->get();
        
        if ($adminUsers->count() > 0) {
            echo "✅ Found {$adminUsers->count()} admin/manager user(s):\n";
            foreach ($adminUsers as $user) {
                echo "   - {$user->email} (Role: {$user->role})\n";
            }
            echo "\n";
        } else {
            echo "❌ NO ADMIN/MANAGER USERS found for this company\n";
            echo "   Users need 'admin' or 'manager' role to login to tenant admin\n\n";
        }
    }
    
    // 4. Check all users regardless of company
    echo "👤 ALL USERS IN SYSTEM:\n";
    echo str_repeat("-", 50) . "\n";
    
    $allUsers = \App\Models\User::all();
    foreach ($allUsers as $user) {
        $companyName = 'Unknown';
        if ($user->company_id) {
            $userCompany = \App\Models\SuperAdmin\Company::find($user->company_id);
            $companyName = $userCompany ? $userCompany->name : "Company ID {$user->company_id} (Not Found)";
        }
        
        echo "   - {$user->email}\n";
        echo "     Role: {$user->role}\n";
        echo "     Company: {$companyName} (ID: {$user->company_id})\n";
        echo "     Super Admin: " . ($user->isSuperAdmin() ? 'Yes' : 'No') . "\n\n";
    }
    
    // 5. Provide troubleshooting steps
    echo "🔧 TROUBLESHOOTING STEPS:\n";
    echo str_repeat("-", 50) . "\n";
    
    if (!$company) {
        echo "❌ ISSUE: Company not found for domain 'greenvalleyherbs.local'\n";
        echo "   SOLUTION: Create or update company record with correct domain\n\n";
    } elseif ($users->count() === 0) {
        echo "❌ ISSUE: No users found for company '{$company->name}'\n";
        echo "   SOLUTION: Create user accounts and assign them to company ID {$company->id}\n\n";
    } elseif ($adminUsers->count() === 0) {
        echo "❌ ISSUE: No admin/manager users found for company '{$company->name}'\n";
        echo "   SOLUTION: Update user roles to 'admin' or 'manager' for company users\n\n";
    } else {
        echo "✅ SETUP LOOKS CORRECT\n";
        echo "   Company exists, admin users exist\n";
        echo "   Issue might be:\n";
        echo "   - Incorrect password\n";
        echo "   - Session/cache issues\n";
        echo "   - Middleware conflicts\n\n";
    }
    
    echo "📝 QUICK FIXES:\n";
    echo "1. If company missing: Check domain spelling in database\n";
    echo "2. If users missing: Create user with correct company_id\n";
    echo "3. If role wrong: Update user role to 'admin' or 'manager'\n";
    echo "4. Clear cache: php artisan cache:clear\n";
    echo "5. Check logs: Check Laravel logs for detailed error messages\n\n";
    
    echo "🎯 NEXT STEPS:\n";
    echo "1. Review the output above to identify the issue\n";
    echo "2. Fix the identified problem\n";
    echo "3. Try logging in again\n";
    echo "4. Check Laravel logs for more details if still failing\n\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR RUNNING DIAGNOSTIC:\n";
    echo "   {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}:{$e->getLine()}\n\n";
}

echo "=== DIAGNOSTIC COMPLETE ===\n";
