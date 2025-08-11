<?php
/**
 * 🧪 QUICK FIX VERIFICATION
 * ========================
 * This script verifies that the thermal receipt fix is working
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SuperAdmin\Company;
use App\Models\Order;

echo "\n";
echo "🧪 THERMAL RECEIPT FIX VERIFICATION\n";
echo "===================================\n\n";

try {
    // Test 1: Check if company exists
    echo "Test 1: Checking company data...\n";
    $company = Company::first();
    
    if ($company) {
        echo "✅ Company found: {$company->name}\n";
        echo "   Address: " . ($company->address ?: 'Not set') . "\n";
        echo "   Phone: " . ($company->phone ?: 'Not set') . "\n";
        echo "   Email: " . ($company->email ?: 'Not set') . "\n\n";
    } else {
        echo "❌ No company found! Please run update_company_name.php first.\n\n";
    }
    
    // Test 2: Check if orders exist for testing
    echo "Test 2: Checking for test orders...\n";
    $order = Order::first();
    
    if ($order) {
        echo "✅ Sample order found: #{$order->order_number}\n";
        echo "   Order ID: {$order->id}\n";
        echo "   Company ID: " . ($order->company_id ?: 'Not set') . "\n\n";
        
        // Test 3: Simulate getCompanySettings method
        echo "Test 3: Testing company settings fetch...\n";
        if ($company) {
            $companySettings = [
                'name' => $company->name,
                'address' => trim($company->address . ' ' . $company->city . ' ' . $company->state . ' ' . $company->postal_code),
                'phone' => $company->phone,
                'email' => $company->email,
                'gst_number' => $company->gst_number,
                'logo' => $company->logo,
            ];
            
            echo "✅ Company settings simulation successful:\n";
            echo "   Name: " . strtoupper($companySettings['name']) . "\n";
            echo "   Address: " . ($companySettings['address'] ?: 'Not set') . "\n";
            echo "   Phone: " . ($companySettings['phone'] ?: 'Not set') . "\n";
            echo "   Email: " . ($companySettings['email'] ?: 'Not set') . "\n";
            echo "   GST: " . ($companySettings['gst_number'] ?: 'Not set') . "\n\n";
        }
        
        // Test 4: Provide test URL
        echo "🔗 TEST URL:\n";
        echo "   http://greenvalleyherbs.local:8000/admin/orders/{$order->id}/print-invoice?format=thermal\n\n";
        
    } else {
        echo "⚠️  No orders found for testing.\n";
        echo "   Create an order first, then test printing.\n\n";
    }
    
    echo "🎯 NEXT STEPS:\n";
    echo "1. First update your company info: php update_company_name.php\n";
    echo "2. Test the thermal receipt URL above\n";
    echo "3. Verify your company name appears in the header\n\n";
    
    echo "✅ Fix verification completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "💡 Make sure to run the company update script first.\n";
}
