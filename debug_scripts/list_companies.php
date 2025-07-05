<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== COMPANY ACCESS METHODS ===\n\n";

$companies = \App\Models\SuperAdmin\Company::where('status', 'active')->get();

foreach ($companies as $company) {
    echo "📋 {$company->name}\n";
    echo "   ID: {$company->id}\n";
    echo "   Slug: {$company->slug}\n";
    echo "   Domain: " . ($company->domain ?: 'Not set') . "\n";
    echo "   Status: {$company->status}\n";
    
    echo "   🔑 Admin Access Methods:\n";
    echo "      1. Main Login: http://localhost:8000/login (select '{$company->name}')\n";
    
    if ($company->domain) {
        echo "      2. Direct Domain: http://{$company->domain}/admin/login\n";
    }
    
    echo "      3. Subdomain (local): http://{$company->slug}.localhost:8000/admin/login\n";
    echo "      4. Subdomain (production): http://{$company->slug}.yourdomain.com/admin/login\n";
    
    echo "   🛍️ Store/Ecommerce Access:\n";
    if ($company->domain) {
        echo "      Store: http://{$company->domain}/shop\n";
    }
    echo "      Store (local): http://{$company->slug}.localhost:8000/shop\n";
    echo "      Store (production): http://{$company->slug}.yourdomain.com/shop\n";
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "💡 SETUP NOTES:\n";
echo "1. For local development with subdomains, add to your hosts file:\n";
echo "   127.0.0.1 sample-company-1.localhost\n";
echo "   127.0.0.1 sample-company-2.localhost\n";
echo "   127.0.0.1 sample-company-3.localhost\n\n";

echo "2. For production, point DNS A records:\n";
echo "   sample-company-1.yourdomain.com → Your server IP\n";
echo "   sample-company-2.yourdomain.com → Your server IP\n";
echo "   sample-company-3.yourdomain.com → Your server IP\n\n";

echo "3. Or use wildcard DNS: *.yourdomain.com → Your server IP\n\n";
