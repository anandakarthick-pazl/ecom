<?php
// Test script to verify landing page setup
// Run: php test-landing-page.php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n================================================\n";
echo "Original SaaS Landing Page Verification\n";
echo "================================================\n\n";

// Test 1: Check view files
echo "1. Checking Landing Page Views:\n";
echo "-------------------------------\n";

$viewPath = resource_path('views/landing/index.blade.php');
if (file_exists($viewPath)) {
    $content = file_get_contents($viewPath);
    if (strpos($content, 'Herbal Ecom - Complete E-commerce Solution') !== false) {
        echo "✓ Original landing page found at: landing.index\n";
        echo "  Title: 'Herbal Ecom - Complete E-commerce Solution'\n";
    } else {
        echo "✗ View exists but might not be the original\n";
    }
} else {
    echo "✗ Landing page view not found!\n";
}

// Check alternate landing-page directory
$altViewPath = resource_path('views/landing-page/index.blade.php');
if (file_exists($altViewPath)) {
    echo "✓ Alternative landing page found at: landing-page.index\n";
}

// Test 2: Check controller
echo "\n2. Checking CompanyRegistrationController:\n";
echo "------------------------------------------\n";
if (class_exists('App\Http\Controllers\CompanyRegistrationController')) {
    echo "✓ CompanyRegistrationController exists\n";
    $controller = new App\Http\Controllers\CompanyRegistrationController();
    echo "  Method showRegistrationForm: " . (method_exists($controller, 'showRegistrationForm') ? '✓' : '✗') . "\n";
} else {
    echo "✗ CompanyRegistrationController not found\n";
}

// Test 3: Check packages and themes
echo "\n3. Checking Data:\n";
echo "-----------------\n";
$packages = \App\Models\SuperAdmin\Package::where('status', 'active')->count();
$themes = \App\Models\SuperAdmin\Theme::where('status', 'active')->count();
$companies = \App\Models\SuperAdmin\Company::count();

echo "Active Packages: $packages\n";
echo "Active Themes: $themes\n";
echo "Total Companies: $companies\n";

// Test 4: Route configuration
echo "\n4. Route Configuration:\n";
echo "-----------------------\n";
echo "Main route (/) logic:\n";
echo "- localhost → CompanyRegistrationController@showRegistrationForm\n";
echo "- *.local → redirect to /shop\n";

echo "\n5. URLs to Test:\n";
echo "----------------\n";
echo "Original SaaS Landing: http://localhost:8000\n";
echo "Company 1 Store: http://greenvalleyherbs.local:8000\n";
echo "Company 2 Store: http://organicnature.local:8000\n";

echo "\n✓ Verification complete!\n";
echo "\nIf the landing page still shows wrong content:\n";
echo "1. Run: php artisan view:clear\n";
echo "2. Check browser cache (use incognito mode)\n";
echo "3. Restart the Laravel server\n\n";
