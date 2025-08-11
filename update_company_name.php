<?php
/**
 * 🏢 COMPANY NAME UPDATE TOOL
 * ===========================
 * 
 * INSTRUCTIONS:
 * 1. Edit the company details below (lines 15-19)
 * 2. Save this file
 * 3. Run: php update_company_name.php
 * 4. Test by printing a new invoice
 */

// 👇 EDIT THESE VALUES WITH YOUR ACTUAL COMPANY INFORMATION 👇
$YOUR_COMPANY_NAME = 'Green Valley Herbs';                    // 🏢 Your company name
$YOUR_COMPANY_EMAIL = 'info@greenvalleyherbs.com';           // 📧 Your email
$YOUR_COMPANY_PHONE = '+91 9876543210';                      // 📞 Your phone
$YOUR_COMPANY_ADDRESS = '123 Green Valley Street, Chennai';   // 🏠 Your address
$YOUR_GST_NUMBER = '';                                        // 🏛️  Your GST (optional)

// ============================================================================
// 🚫 DO NOT EDIT BELOW THIS LINE UNLESS YOU KNOW WHAT YOU'RE DOING
// ============================================================================

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SuperAdmin\Company;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;

echo "\n";
echo "🏢 COMPANY NAME UPDATE TOOL\n";
echo "═══════════════════════════\n";

// Validate input
if (empty($YOUR_COMPANY_NAME)) {
    echo "❌ ERROR: Please set your company name in the script!\n";
    echo "   Edit line 15: \$YOUR_COMPANY_NAME = 'Your Actual Company Name';\n";
    exit(1);
}

echo "📋 New company details to be saved:\n";
echo "   🏢 Name: $YOUR_COMPANY_NAME\n";
echo "   📧 Email: $YOUR_COMPANY_EMAIL\n";
echo "   📞 Phone: $YOUR_COMPANY_PHONE\n";
echo "   🏠 Address: $YOUR_COMPANY_ADDRESS\n";
if ($YOUR_GST_NUMBER) echo "   🏛️  GST: $YOUR_GST_NUMBER\n";
echo "\n";

echo "🔍 Checking database...\n";

try {
    // Get the first company
    $company = Company::first();
    
    if (!$company) {
        echo "⚠️  No company found in companies table. Creating new company...\n";
        $company = Company::create([
            'name' => $YOUR_COMPANY_NAME,
            'email' => $YOUR_COMPANY_EMAIL,
            'phone' => $YOUR_COMPANY_PHONE,
            'address' => $YOUR_COMPANY_ADDRESS,
            'gst_number' => $YOUR_GST_NUMBER,
            'status' => 'active'
        ]);
        echo "✅ Created new company (ID: {$company->id})\n";
    } else {
        echo "✅ Found existing company: {$company->name} (ID: {$company->id})\n";
        
        // Update company table
        $updateData = [
            'name' => $YOUR_COMPANY_NAME,
            'email' => $YOUR_COMPANY_EMAIL,
            'phone' => $YOUR_COMPANY_PHONE,
            'address' => $YOUR_COMPANY_ADDRESS
        ];
        
        if ($YOUR_GST_NUMBER) {
            $updateData['gst_number'] = $YOUR_GST_NUMBER;
        }
        
        $company->update($updateData);
        echo "✅ Updated companies table\n";
    }
    
    echo "💾 Updating app settings...\n";
    
    // Update app settings for this company
    $settings = [
        'company_name' => $YOUR_COMPANY_NAME,
        'company_email' => $YOUR_COMPANY_EMAIL,
        'company_phone' => $YOUR_COMPANY_PHONE,
        'company_address' => $YOUR_COMPANY_ADDRESS
    ];
    
    if ($YOUR_GST_NUMBER) {
        $settings['company_gst_number'] = $YOUR_GST_NUMBER;
    }
    
    foreach ($settings as $key => $value) {
        if (!empty($value)) {
            // Update company-specific setting
            AppSetting::setForTenant($key, $value, $company->id);
            
            // Also update global setting for backward compatibility
            AppSetting::set($key, $value);
            
            echo "   ✅ $key\n";
        }
    }
    
    // Clear all caches
    Cache::flush();
    AppSetting::clearCache();
    echo "✅ Cleared application cache\n";
    
    echo "\n";
    echo "🎉 SUCCESS! COMPANY UPDATE COMPLETED!\n";
    echo "════════════════════════════════════\n";
    echo "✨ Your company name '$YOUR_COMPANY_NAME' will now appear on:\n";
    echo "   • 🧾 Thermal receipts (80mm)\n";
    echo "   • 📄 PDF invoices (A4)\n";
    echo "   • 📧 Email receipts\n";
    echo "   • ⚙️  Admin settings pages\n";
    echo "   • 🛒 POS receipts\n";
    echo "\n";
    echo "🔄 NEXT STEPS:\n";
    echo "1. Refresh your browser (Ctrl+F5)\n";
    echo "2. Go to: http://greenvalleyherbs.local:8000/admin/orders\n";
    echo "3. Click 'Print Invoice' on any order\n";
    echo "4. Verify '$YOUR_COMPANY_NAME' appears in the header\n";
    echo "\n";
    echo "🏆 DONE! Your invoices are now updated.\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR OCCURRED:\n";
    echo "   " . $e->getMessage() . "\n";
    echo "\n💡 TROUBLESHOOTING:\n";
    echo "1. Make sure you're in the correct directory: D:\\source_code\\ecom\n";
    echo "2. Check database connection in .env file\n";
    echo "3. Run: php artisan migrate\n";
    echo "4. Try again: php update_company_name.php\n";
    exit(1);
}
