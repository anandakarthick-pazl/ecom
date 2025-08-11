<?php
/**
 * 🧾 THERMAL RECEIPT TEST SCRIPT
 * =============================
 * 
 * This script tests if your thermal receipt will show the correct company information
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SuperAdmin\Company;
use App\Models\AppSetting;
use App\Models\Order;

echo "\n";
echo "🧾 THERMAL RECEIPT TEST\n";
echo "======================\n\n";

try {
    // Get the first company
    $company = Company::first();
    
    if (!$company) {
        echo "❌ No company found in database!\n";
        echo "   Please run the company update script first.\n";
        exit(1);
    }
    
    echo "📋 Testing company data fetch...\n";
    echo "Company ID: {$company->id}\n";
    echo "Company Name: {$company->name}\n";
    echo "Company Email: {$company->email}\n";
    echo "Company Phone: {$company->phone}\n";
    echo "Company Address: {$company->address}\n";
    echo "Company GST: {$company->gst_number}\n";
    echo "Company Logo: {$company->logo}\n\n";
    
    // Test the getCompanySettings equivalent logic
    $companySettings = [
        'name' => $company->name,
        'address' => trim($company->address . ' ' . $company->city . ' ' . $company->state . ' ' . $company->postal_code),
        'phone' => $company->phone,
        'email' => $company->email,
        'gst_number' => $company->gst_number,
        'logo' => $company->logo,
    ];
    
    echo "🔧 Thermal receipt will show:\n";
    echo "   🏢 Company Name: " . strtoupper($companySettings['name']) . "\n";
    echo "   🏠 Address: " . ($companySettings['address'] ?: 'Not set') . "\n";
    echo "   📞 Phone: " . ($companySettings['phone'] ?: 'Not set') . "\n";
    echo "   📧 Email: " . ($companySettings['email'] ?: 'Not set') . "\n";
    echo "   🏛️  GST: " . ($companySettings['gst_number'] ?: 'Not set') . "\n";
    echo "   🖼️  Logo: " . ($companySettings['logo'] ? 'Set' : 'Not set') . "\n\n";
    
    // Test if we have any orders to test with
    $sampleOrder = Order::first();
    if ($sampleOrder) {
        echo "✅ Found sample order #{$sampleOrder->order_number}\n";
        echo "   You can test with: http://greenvalleyherbs.local:8000/admin/orders/{$sampleOrder->id}/print-invoice?format=thermal\n\n";
    } else {
        echo "⚠️  No orders found to test with.\n";
        echo "   Create an order first, then test printing.\n\n";
    }
    
    echo "🎯 NEXT STEPS:\n";
    echo "1. Go to: http://greenvalleyherbs.local:8000/admin/orders\n";
    echo "2. Click 'Print Invoice' on any order\n";
    echo "3. Add '?format=thermal' to the URL\n";
    echo "4. Verify the company name '{$company->name}' appears in the header\n\n";
    
    echo "✅ Company data is ready for thermal receipts!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "💡 Make sure you've run the company update script first.\n";
}
