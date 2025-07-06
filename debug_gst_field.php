<?php

// GST Field Debug Script
// Place this at: D:\source_code\ecom\debug_gst_field.php
// Run with: php debug_gst_field.php

require_once 'vendor/autoload.php';

use App\Models\SuperAdmin\Company;
use Illuminate\Support\Facades\Schema;

// Start Laravel bootstrap
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

echo "<h2>GST Field Debug Information</h2>\n";

echo "<h3>1. Check Current Company Data</h3>\n";
$companyId = session('selected_company_id') ?? (auth()->check() ? auth()->user()->company_id : null) ?? 1;
echo "Company ID: " . $companyId . "\n";

$company = Company::find($companyId);
if ($company) {
    echo "✅ Company found\n";
    echo "Company Name: " . $company->name . "\n";
    echo "GST Number: " . ($company->gst_number ?: 'Not set') . "\n";
    echo "Company Data: " . print_r($company->toArray(), true) . "\n";
} else {
    echo "❌ Company not found\n";
    echo "Available companies: \n";
    $companies = Company::all();
    foreach ($companies as $comp) {
        echo "- ID: {$comp->id}, Name: {$comp->name}\n";
    }
}

echo "\n<h3>2. Check Database Migration</h3>\n";
try {
    $hasGstColumn = Schema::hasColumn('companies', 'gst_number');
    echo $hasGstColumn ? "✅ GST column exists\n" : "❌ GST column missing\n";
} catch (Exception $e) {
    echo "❌ Error checking column: " . $e->getMessage() . "\n";
}

echo "\n<h3>3. Test Company Creation/Update</h3>\n";
if ($company) {
    $originalGst = $company->gst_number;
    $company->gst_number = '22AAAAA0000A1Z5';
    $result = $company->save();
    echo $result ? "✅ Company update test successful\n" : "❌ Company update failed\n";
    echo "New GST value: " . $company->fresh()->gst_number . "\n";
    
    // Restore original value
    $company->gst_number = $originalGst;
    $company->save();
} else {
    echo "❌ Cannot test - no company found\n";
}

echo "\n<h3>4. Solution Steps</h3>\n";
echo "1. Run: php artisan migrate\n";
echo "2. Check that you have a company record in the database\n";
echo "3. Clear caches: php artisan cache:clear\n";
echo "4. Access admin settings and the GST field should appear\n";
echo "5. Settings URL: /admin/settings\n";
