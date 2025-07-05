<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUGGING LOGIN ISSUE ===\n\n";

// Check the user
$user = \App\Models\User::where('email', 'admin@company3.com')->first();
if ($user) {
    echo "✅ User found:\n";
    echo "   ID: {$user->id}\n";
    echo "   Name: {$user->name}\n";
    echo "   Email: {$user->email}\n";
    echo "   Company ID: {$user->company_id} (type: " . gettype($user->company_id) . ")\n";
    echo "   Role: {$user->role}\n";
    echo "   Is Super Admin: " . ($user->is_super_admin ? 'Yes' : 'No') . "\n";
    echo "   Status: {$user->status}\n\n";
} else {
    echo "❌ User not found!\n\n";
}

// Check the company
$company = \App\Models\SuperAdmin\Company::where('slug', 'sample-company-3')->first();
if ($company) {
    echo "✅ Company found:\n";
    echo "   ID: {$company->id} (type: " . gettype($company->id) . ")\n";
    echo "   Name: {$company->name}\n";
    echo "   Slug: {$company->slug}\n";
    echo "   Status: {$company->status}\n\n";
} else {
    echo "❌ Company not found!\n\n";
}

// Check comparison
if ($user && $company) {
    echo "=== COMPARISON TESTS ===\n";
    echo "User Company ID: {$user->company_id} (type: " . gettype($user->company_id) . ")\n";
    echo "Selected Company ID: {$company->id} (type: " . gettype($company->id) . ")\n";
    echo "Strict comparison (===): " . ($user->company_id === $company->id ? 'TRUE' : 'FALSE') . "\n";
    echo "Loose comparison (==): " . ($user->company_id == $company->id ? 'TRUE' : 'FALSE') . "\n";
    echo "Int comparison: " . ((int)$user->company_id === (int)$company->id ? 'TRUE' : 'FALSE') . "\n";
    echo "String comparison: " . ((string)$user->company_id === (string)$company->id ? 'TRUE' : 'FALSE') . "\n\n";
}

// List all companies
echo "=== ALL COMPANIES ===\n";
$companies = \App\Models\SuperAdmin\Company::all(['id', 'name', 'slug', 'status']);
foreach ($companies as $comp) {
    echo "ID: {$comp->id}, Name: {$comp->name}, Slug: {$comp->slug}, Status: {$comp->status}\n";
}

echo "\n=== ALL USERS ===\n";
$users = \App\Models\User::all(['id', 'name', 'email', 'company_id', 'role', 'is_super_admin']);
foreach ($users as $u) {
    echo "ID: {$u->id}, Name: {$u->name}, Email: {$u->email}, Company ID: {$u->company_id}, Role: {$u->role}, Super Admin: " . ($u->is_super_admin ? 'Yes' : 'No') . "\n";
}
