<?php
/**
 * Multi-Tenant Setup Verification Script
 * Run: php verify-multitenant.php
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SuperAdmin\Company;
use App\Models\User;

echo "\n===========================================\n";
echo "Multi-Tenant E-commerce Setup Verification\n";
echo "===========================================\n\n";

// Check Companies
echo "1. Checking Companies:\n";
echo "-----------------------\n";
$companies = Company::all();
if ($companies->count() > 0) {
    foreach ($companies as $company) {
        echo "✓ Company: {$company->name}\n";
        echo "  - Slug: {$company->slug}\n";
        echo "  - Domain: {$company->domain}\n";
        echo "  - Status: {$company->status}\n";
        echo "  - Email: {$company->email}\n\n";
    }
} else {
    echo "✗ No companies found! Run setup-multitenant-data.sql\n\n";
}

// Check Users
echo "2. Checking Admin Users:\n";
echo "------------------------\n";
$admins = User::whereIn('role', ['super_admin', 'admin'])->get();
if ($admins->count() > 0) {
    foreach ($admins as $admin) {
        echo "✓ User: {$admin->name}\n";
        echo "  - Email: {$admin->email}\n";
        echo "  - Role: {$admin->role}\n";
        echo "  - Company: " . ($admin->company_id ? "Company #{$admin->company_id}" : "Super Admin") . "\n\n";
    }
} else {
    echo "✗ No admin users found!\n\n";
}

// Check Routes
echo "3. Checking Route Configuration:\n";
echo "--------------------------------\n";
$routes = [
    'http://localhost:8000' => 'SaaS Landing Page',
    'http://localhost:8000/super-admin/login' => 'Super Admin Login',
    'http://greenvalleyherbs.local:8000' => 'Company 1 Store',
    'http://organicnature.local:8000' => 'Company 2 Store',
];

foreach ($routes as $url => $description) {
    echo "- {$description}: {$url}\n";
}

echo "\n4. Quick Fixes:\n";
echo "---------------\n";
echo "- If companies missing: mysql -u root -p your_db < setup-multitenant-data.sql\n";
echo "- If domains not working: Run setup-local-domains.bat as Administrator\n";
echo "- Clear cache: php artisan cache:clear\n";
echo "- Start server: php artisan serve\n";

echo "\n✓ Verification complete!\n\n";
