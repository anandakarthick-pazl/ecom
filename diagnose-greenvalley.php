<?php
// Quick diagnostic for greenvalleyherbs.local issue
// Run: php diagnose-greenvalley.php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SuperAdmin\Company;

echo "\n================================================\n";
echo "Diagnosing greenvalleyherbs.local Issue\n";
echo "================================================\n\n";

// Check companies in database
echo "1. Checking Companies in Database:\n";
echo "----------------------------------\n";
$companies = Company::all(['id', 'name', 'slug', 'domain', 'status']);

foreach ($companies as $company) {
    $marker = ($company->domain === 'greenvalleyherbs.local') ? ' <-- CHECKING THIS' : '';
    echo "ID: {$company->id} | {$company->name}\n";
    echo "   Slug: {$company->slug}\n";
    echo "   Domain: {$company->domain}{$marker}\n";
    echo "   Status: {$company->status}\n\n";
}

// Specific check for greenvalleyherbs
echo "2. Specific Domain Lookup:\n";
echo "--------------------------\n";
$greenvalley = Company::where('domain', 'greenvalleyherbs.local')->first();
if ($greenvalley) {
    echo "✓ Found: greenvalleyherbs.local -> {$greenvalley->name} (ID: {$greenvalley->id})\n";
} else {
    echo "✗ NOT FOUND: greenvalleyherbs.local\n";
    
    // Check if it exists with different domain
    $altGreenvalley = Company::where('slug', 'greenvalleyherbs')->first();
    if ($altGreenvalley) {
        echo "  But found with slug 'greenvalleyherbs' having domain: {$altGreenvalley->domain}\n";
        echo "  ⚠️ Domain mismatch! Should be 'greenvalleyherbs.local'\n";
    }
}

// Check for similar domains
echo "\n3. Checking for Similar Domains:\n";
echo "--------------------------------\n";
$similarDomains = Company::where('domain', 'LIKE', '%greenvalley%')->get();
foreach ($similarDomains as $sd) {
    echo "Found: {$sd->domain} (ID: {$sd->id})\n";
}

// Test hosts file
echo "\n4. Testing DNS Resolution:\n";
echo "--------------------------\n";
$ip = gethostbyname('greenvalleyherbs.local');
echo "greenvalleyherbs.local resolves to: $ip\n";
echo "Expected: 127.0.0.1\n";

echo "\n5. Quick Fix SQL:\n";
echo "-----------------\n";
echo "UPDATE companies SET domain = 'greenvalleyherbs.local' WHERE slug = 'greenvalleyherbs';\n";

echo "\n✓ Diagnostic complete!\n\n";
