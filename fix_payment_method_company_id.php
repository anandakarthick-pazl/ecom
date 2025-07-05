<?php
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PaymentMethod;
use App\Models\SuperAdmin\Company;

echo "=== PAYMENT METHOD COMPANY_ID FIX ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

echo "🔍 Step 1: Checking current payment methods...\n";
echo "==============================================\n";

$allPaymentMethods = PaymentMethod::all();
$methodsWithoutCompany = PaymentMethod::whereNull('company_id')->get();
$methodsWithCompany = PaymentMethod::whereNotNull('company_id')->get();

echo "Total payment methods: " . $allPaymentMethods->count() . "\n";
echo "Methods WITHOUT company_id: " . $methodsWithoutCompany->count() . "\n";
echo "Methods WITH company_id: " . $methodsWithCompany->count() . "\n\n";

if ($methodsWithoutCompany->count() > 0) {
    echo "❌ ISSUE: Payment methods missing company_id:\n";
    foreach ($methodsWithoutCompany as $method) {
        echo "   ID: {$method->id}, Type: {$method->type}, Name: {$method->display_name}\n";
    }
    echo "\n";
} else {
    echo "✅ All payment methods have company_id set\n\n";
}

echo "🔍 Step 2: Checking available companies...\n";
echo "==========================================\n";

$companies = Company::where('status', 'active')->get();
echo "Active companies found: " . $companies->count() . "\n";

if ($companies->isEmpty()) {
    echo "❌ No active companies found! Creating default company...\n";
    
    $defaultCompany = Company::create([
        'name' => 'Default Store',
        'company_name' => 'Default Store',
        'domain' => 'localhost:8000',
        'status' => 'active',
        'email' => 'admin@localhost.com',
        'phone' => '9999999999',
        'address' => 'Default Address',
        'city' => 'Default City',
        'state' => 'Default State',
        'pincode' => '000000',
    ]);
    
    echo "✅ Created default company: {$defaultCompany->name} (ID: {$defaultCompany->id})\n";
    $companies = collect([$defaultCompany]);
} else {
    foreach ($companies as $company) {
        echo "   ID: {$company->id}, Name: {$company->name}, Domain: {$company->domain}\n";
    }
}

echo "\n🔧 Step 3: Fixing payment methods without company_id...\n";
echo "======================================================\n";

if ($methodsWithoutCompany->count() > 0) {
    $targetCompany = $companies->first(); // Use first active company
    
    echo "Using company: {$targetCompany->name} (ID: {$targetCompany->id})\n\n";
    
    $fixed = 0;
    foreach ($methodsWithoutCompany as $method) {
        $method->update(['company_id' => $targetCompany->id]);
        echo "✅ Fixed payment method ID {$method->id} ({$method->type})\n";
        $fixed++;
    }
    
    echo "\n🎉 Fixed {$fixed} payment method(s)\n";
} else {
    echo "✅ No payment methods need fixing\n";
}

echo "\n🔍 Step 4: Verification after fix...\n";
echo "====================================\n";

$allMethodsAfterFix = PaymentMethod::all();
$methodsStillMissing = PaymentMethod::whereNull('company_id')->count();

echo "Total payment methods: " . $allMethodsAfterFix->count() . "\n";
echo "Methods still missing company_id: {$methodsStillMissing}\n";

if ($methodsStillMissing == 0) {
    echo "✅ All payment methods now have company_id!\n";
} else {
    echo "❌ Some methods still missing company_id\n";
}

echo "\n📊 Current payment methods by company:\n";
echo "======================================\n";

$companiesWithMethods = Company::with('paymentMethods')->get();
foreach ($companiesWithMethods as $company) {
    $methodCount = $company->paymentMethods->count();
    echo "Company: {$company->name}\n";
    echo "   Payment methods: {$methodCount}\n";
    
    if ($methodCount > 0) {
        foreach ($company->paymentMethods as $method) {
            echo "   - {$method->type}: {$method->display_name} (" . ($method->is_active ? 'Active' : 'Inactive') . ")\n";
        }
    }
    echo "\n";
}

echo "=== COMPANY_ID FIX COMPLETED ===\n";
echo "\nNEXT STEPS:\n";
echo "1. Test creating new payment methods in admin panel\n";
echo "2. Verify company_id is properly set\n";
echo "3. Check payment functionality\n";
