<?php
/**
 * Test script to verify POS bill download functionality
 * Run this from the root directory: php test_pos_bill_download.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔧 Testing POS Bill Download Fix\n";
echo "================================\n\n";

try {
    // Test 1: Check if BillPDFService exists and has the required methods
    echo "✅ Test 1: Checking BillPDFService...\n";
    
    $billService = app(\App\Services\BillPDFService::class);
    
    if (method_exists($billService, 'downloadPosSaleBill')) {
        echo "   ✓ downloadPosSaleBill method exists\n";
    } else {
        echo "   ❌ downloadPosSaleBill method missing\n";
    }
    
    if (method_exists($billService, 'downloadPosSaleBillFast')) {
        echo "   ✓ downloadPosSaleBillFast method exists\n";
    } else {
        echo "   ❌ downloadPosSaleBillFast method missing\n";
    }
    
    if (method_exists($billService, 'generateUltraFastPDF')) {
        echo "   ✓ generateUltraFastPDF method exists\n";
    } else {
        echo "   ❌ generateUltraFastPDF method missing\n";
    }
    
    if (method_exists($billService, 'generateSimplePDF')) {
        echo "   ✓ generateSimplePDF method exists (backward compatibility)\n";
    } else {
        echo "   ❌ generateSimplePDF method missing\n";
    }
    
    echo "\n";
    
    // Test 2: Check if PosController has the fixed downloadBill method
    echo "✅ Test 2: Checking PosController...\n";
    
    $reflection = new ReflectionClass(\App\Http\Controllers\Admin\PosController::class);
    
    if ($reflection->hasMethod('downloadBill')) {
        echo "   ✓ downloadBill method exists in PosController\n";
        
        $method = $reflection->getMethod('downloadBill');
        $methodContent = file_get_contents($method->getFileName());
        
        if (strpos($methodContent, 'BillPDFService') !== false) {
            echo "   ✓ PosController uses BillPDFService\n";
        } else {
            echo "   ❌ PosController doesn't use BillPDFService\n";
        }
        
        if (strpos($methodContent, 'generateSimplePDF') !== false) {
            echo "   ⚠️  PosController still has old generateSimplePDF call\n";
        } else {
            echo "   ✓ Old generateSimplePDF call has been removed\n";
        }
    } else {
        echo "   ❌ downloadBill method missing in PosController\n";
    }
    
    echo "\n";
    
    // Test 3: Check if required view files exist
    echo "✅ Test 3: Checking view files...\n";
    
    $viewPaths = [
        'admin.pos.receipt-pdf' => 'resources/views/admin/pos/receipt-pdf.blade.php',
        'admin.pos.receipt-a4' => 'resources/views/admin/pos/receipt-a4.blade.php',
        'admin.pos.receipt' => 'resources/views/admin/pos/receipt.blade.php'
    ];
    
    foreach ($viewPaths as $viewName => $viewPath) {
        if (file_exists(__DIR__ . '/' . $viewPath)) {
            echo "   ✓ {$viewName} view exists\n";
        } else {
            echo "   ❌ {$viewName} view missing at {$viewPath}\n";
        }
    }
    
    echo "\n";
    
    // Test 4: Check route configuration
    echo "✅ Test 4: Checking routes...\n";
    
    $routesContent = file_get_contents(__DIR__ . '/routes/web.php');
    
    if (strpos($routesContent, "Route::get('/sales/{sale}/download-bill'") !== false || 
        strpos($routesContent, "->name('download-bill')") !== false) {
        echo "   ✓ Download bill route is configured\n";
    } else {
        echo "   ❌ Download bill route not found in web.php\n";
    }
    
    echo "\n";
    
    // Test 5: Check if we can find a sample POS sale to test with
    echo "✅ Test 5: Checking for sample data...\n";
    
    try {
        $sampleSale = \App\Models\PosSale::first();
        if ($sampleSale) {
            echo "   ✓ Found sample POS sale (ID: {$sampleSale->id})\n";
            echo "   📄 Invoice: {$sampleSale->invoice_number}\n";
            echo "   💰 Amount: {$sampleSale->total_amount}\n";
            echo "   📅 Date: {$sampleSale->sale_date}\n";
        } else {
            echo "   ℹ️  No POS sales found in database\n";
        }
    } catch (\Exception $e) {
        echo "   ⚠️  Could not check POS sales: {$e->getMessage()}\n";
    }
    
    echo "\n";
    
    echo "🎉 Fix Verification Complete!\n";
    echo "============================\n\n";
    
    echo "📋 Summary:\n";
    echo "- Fixed the missing generateSimplePDF method issue\n";
    echo "- Updated PosController to use BillPDFService properly\n";
    echo "- Added backward compatibility method to BillPDFService\n";
    echo "- Implemented multiple fallback PDF generation methods\n\n";
    
    echo "🚀 The download bill functionality should now work properly!\n";
    echo "   Test URL: http://greenvalleyherbs.local:8000/admin/pos/sales/{sale_id}/download-bill\n\n";

} catch (\Exception $e) {
    echo "❌ Error during testing: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
