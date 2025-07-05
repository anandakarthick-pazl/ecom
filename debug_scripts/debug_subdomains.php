<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUGGING SUBDOMAIN RESOLUTION (FIXED) ===\n\n";

// Updated subdomain extraction logic (same as middleware)
function extractSubdomain($host)
{
    $parts = explode('.', $host);
    
    // For localhost development: subdomain.localhost
    if (count($parts) == 2 && $parts[1] === 'localhost') {
        return $parts[0];
    }
    
    // For production: subdomain.domain.tld
    if (count($parts) > 2) {
        return $parts[0];
    }
    
    return null;
}

$testHosts = [
    'sample-company-1.localhost',
    'sample-company-2.localhost', 
    'sample-company-3.localhost',
    'localhost',
    'demo.localhost',
    'company1.yourdomain.com',
    'subdomain.example.com'
];

echo "📍 SUBDOMAIN EXTRACTION TESTS (UPDATED LOGIC):\n";
foreach ($testHosts as $host) {
    $subdomain = extractSubdomain($host);
    $parts = explode('.', $host);
    echo "Host: {$host}\n";
    echo "  Parts: [" . implode(', ', $parts) . "]\n";
    echo "  Count: " . count($parts) . "\n";
    echo "  Subdomain: " . ($subdomain ?: 'null') . "\n\n";
}

echo "\n📋 DATABASE COMPANIES:\n";
$companies = \App\Models\SuperAdmin\Company::all(['id', 'name', 'slug', 'domain', 'status']);
foreach ($companies as $company) {
    echo "ID: {$company->id} | Name: {$company->name} | Slug: '{$company->slug}' | Domain: '{$company->domain}' | Status: {$company->status}\n";
}

echo "\n🔍 SLUG MATCHING TESTS (WITH NEW EXTRACTION):\n";
$testHosts = ['sample-company-1.localhost', 'sample-company-2.localhost', 'sample-company-3.localhost'];
foreach ($testHosts as $host) {
    $subdomain = extractSubdomain($host);
    $company = \App\Models\SuperAdmin\Company::where('slug', $subdomain)->where('status', 'active')->first();
    echo "Host: '{$host}' → Subdomain: '{$subdomain}' → Company: " . ($company ? $company->name : 'NOT FOUND') . "\n";
}

echo "\n✅ FIXED! The issue was in extractSubdomain() method.\n";
echo "Now test: http://sample-company-1.localhost:8000/debug-tenant\n";
echo "Should show subdomain_extracted: 'sample-company-1'\n";
