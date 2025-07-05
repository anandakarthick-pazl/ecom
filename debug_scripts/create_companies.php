<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CREATING/UPDATING COMPANIES FOR SUBDOMAIN ACCESS ===\n\n";

$companies = [
    [
        'name' => 'Sample Company 1',
        'slug' => 'sample-company-1',
        'email' => 'admin@company1.com',
        'domain' => null
    ],
    [
        'name' => 'Sample Company 2', 
        'slug' => 'sample-company-2',
        'email' => 'admin@company2.com',
        'domain' => null
    ],
    [
        'name' => 'Sample Company 3',
        'slug' => 'sample-company-3', 
        'email' => 'admin@company3.com',
        'domain' => null
    ]
];

foreach ($companies as $companyData) {
    // Create or update company
    $company = \App\Models\SuperAdmin\Company::updateOrCreate(
        ['slug' => $companyData['slug']],
        [
            'name' => $companyData['name'],
            'email' => $companyData['email'],
            'status' => 'active',
            'trial_ends_at' => now()->addDays(30),
            'created_by' => 1
        ]
    );
    
    echo "✅ Company: {$company->name}\n";
    echo "   ID: {$company->id}\n";
    echo "   Slug: '{$company->slug}'\n";
    echo "   Status: {$company->status}\n";
    
    // Create admin user if doesn't exist
    $adminUser = \App\Models\User::updateOrCreate(
        ['email' => $companyData['email']],
        [
            'name' => $companyData['name'] . ' Admin',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'admin',
            'is_super_admin' => false,
            'status' => 'active',
            'company_id' => $company->id
        ]
    );
    
    echo "   Admin: {$adminUser->email}\n";
    echo "   Password: password123\n";
    echo "   URL: http://{$company->slug}.localhost:8000/admin/login\n";
    echo "   Store: http://{$company->slug}.localhost:8000/shop\n\n";
}

echo "🎯 NEXT STEPS:\n";
echo "1. Add to hosts file:\n";
foreach ($companies as $companyData) {
    echo "   127.0.0.1 {$companyData['slug']}.localhost\n";
}

echo "\n2. Test URLs:\n";
foreach ($companies as $companyData) {
    echo "   Admin: http://{$companyData['slug']}.localhost:8000/admin/login\n";
    echo "   Store: http://{$companyData['slug']}.localhost:8000/shop\n";
    echo "   Debug: http://{$companyData['slug']}.localhost:8000/debug-tenant\n\n";
}

echo "3. Test subdomain resolution:\n";
echo "   Visit: http://sample-company-1.localhost:8000/debug-tenant\n";
echo "   Should show company details in JSON\n\n";
