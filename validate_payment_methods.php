<?php
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PaymentMethod;
use App\Models\SuperAdmin\Company;

echo "=== PAYMENT METHOD VALIDATION REPORT ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

try {
    echo "📊 DATABASE OVERVIEW\n";
    echo "==================\n";
    
    $totalMethods = PaymentMethod::count();
    $methodsWithCompany = PaymentMethod::whereNotNull('company_id')->where('company_id', '>', 0)->count();
    $methodsWithoutCompany = PaymentMethod::whereNull('company_id')->orWhere('company_id', 0)->count();
    $totalCompanies = Company::count();
    $activeCompanies = Company::where('status', 'active')->count();
    
    echo "Payment Methods:\n";
    echo "  Total: {$totalMethods}\n";
    echo "  With company_id: {$methodsWithCompany}\n";
    echo "  Without company_id: {$methodsWithoutCompany}\n";
    echo "\n";
    echo "Companies:\n";
    echo "  Total: {$totalCompanies}\n";
    echo "  Active: {$activeCompanies}\n";
    echo "\n";
    
    // Status indicator
    if ($methodsWithoutCompany > 0) {
        echo "❌ STATUS: Issues detected - {$methodsWithoutCompany} payment methods missing company_id\n";
    } else {
        echo "✅ STATUS: All payment methods have company_id assigned\n";
    }
    echo "\n";
    
    echo "📋 DETAILED ANALYSIS\n";
    echo "===================\n";
    
    // List all companies and their payment methods
    $companies = Company::with('paymentMethods')->get();
    
    foreach ($companies as $company) {
        $methodCount = $company->paymentMethods->count();
        $activeMethodCount = $company->paymentMethods->where('is_active', true)->count();
        
        echo "Company: {$company->name} (ID: {$company->id})\n";
        echo "  Status: {$company->status}\n";
        echo "  Domain: {$company->domain}\n";
        echo "  Payment methods: {$methodCount} (Active: {$activeMethodCount})\n";
        
        if ($methodCount > 0) {
            foreach ($company->paymentMethods as $method) {
                $status = $method->is_active ? 'Active' : 'Inactive';
                echo "    - {$method->type}: {$method->display_name} ({$status})\n";
            }
        } else {
            echo "    ⚠️  No payment methods found\n";
        }
        echo "\n";
    }
    
    // Check for orphaned payment methods
    $orphanedMethods = PaymentMethod::whereNotIn('company_id', $companies->pluck('id'))->get();
    
    if ($orphanedMethods->isNotEmpty()) {
        echo "❌ ORPHANED PAYMENT METHODS\n";
        echo "===========================\n";
        echo "Found {$orphanedMethods->count()} payment methods with invalid company_id:\n";
        
        foreach ($orphanedMethods as $method) {
            echo "  ID: {$method->id}, Type: {$method->type}, Company_ID: {$method->company_id}\n";
        }
        echo "\n";
    }
    
    // Check for payment methods without company_id
    $methodsWithoutCompany = PaymentMethod::whereNull('company_id')->orWhere('company_id', 0)->get();
    
    if ($methodsWithoutCompany->isNotEmpty()) {
        echo "❌ PAYMENT METHODS WITHOUT COMPANY_ID\n";
        echo "=====================================\n";
        echo "Found {$methodsWithoutCompany->count()} payment methods without valid company_id:\n";
        
        foreach ($methodsWithoutCompany as $method) {
            echo "  ID: {$method->id}, Type: {$method->type}, Name: {$method->display_name}\n";
        }
        echo "\n";
    }
    
    echo "🔧 SYSTEM CONFIGURATION CHECK\n";
    echo "=============================\n";
    
    // Check session configuration
    echo "Session Configuration:\n";
    echo "  Driver: " . config('session.driver', 'not_set') . "\n";
    echo "  Domain: " . config('session.domain', 'not_set') . "\n";
    echo "  Secure: " . (config('session.secure', false) ? 'true' : 'false') . "\n";
    echo "\n";
    
    // Check authentication configuration
    echo "Authentication:\n";
    if (auth()->check()) {
        $user = auth()->user();
        echo "  Logged in: Yes\n";
        echo "  User ID: {$user->id}\n";
        echo "  User company_id: " . ($user->company_id ?? 'not_set') . "\n";
    } else {
        echo "  Logged in: No\n";
    }
    echo "\n";
    
    // Check middleware and tenant configuration
    echo "Tenant Configuration:\n";
    echo "  Has current_tenant: " . (app()->has('current_tenant') ? 'Yes' : 'No') . "\n";
    if (app()->has('current_tenant')) {
        echo "  Current tenant ID: " . app('current_tenant')->id . "\n";
    }
    echo "  Session selected_company_id: " . (session('selected_company_id') ?? 'not_set') . "\n";
    echo "  Session current_company_id: " . (session('current_company_id') ?? 'not_set') . "\n";
    echo "\n";
    
    echo "📋 RECOMMENDATIONS\n";
    echo "==================\n";
    
    if ($methodsWithoutCompany > 0) {
        echo "1. ❌ Run the enhanced fix: php enhanced_payment_method_company_id_fix.php\n";
    } else {
        echo "1. ✅ Payment method company_id assignment is working correctly\n";
    }
    
    if ($activeCompanies == 0) {
        echo "2. ❌ Create at least one active company\n";
    } else {
        echo "2. ✅ Active companies are configured\n";
    }
    
    $companiesWithoutMethods = $companies->filter(function($company) {
        return $company->paymentMethods->isEmpty() && $company->status === 'active';
    });
    
    if ($companiesWithoutMethods->isNotEmpty()) {
        echo "3. ⚠️  Some active companies don't have payment methods:\n";
        foreach ($companiesWithoutMethods as $company) {
            echo "     - {$company->name} (ID: {$company->id})\n";
        }
    } else {
        echo "3. ✅ All active companies have payment methods\n";
    }
    
    echo "\n=== VALIDATION COMPLETE ===\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR during validation: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
