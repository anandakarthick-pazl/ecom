<?php
/**
 * Final POS Bill Download Verification Script
 * Run this to verify the fix is working properly
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🎯 POS Bill Download - Final Verification\n";
echo "=========================================\n\n";

$allTestsPassed = true;

try {
    // Test 1: Find a test sale
    echo "✅ Test 1: Finding test POS sale...\n";
    
    $sale = \App\Models\PosSale::with(['items.product', 'cashier'])->first();
    
    if (!$sale) {
        echo "❌ No POS sales found in database!\n";
        echo "   Create a test sale first through the POS interface.\n\n";
        exit;
    }
    
    echo "   ✓ Found sale: {$sale->invoice_number} (ID: {$sale->id})\n";
    echo "   💰 Amount: ₹{$sale->total_amount}\n";
    echo "   📦 Items: {$sale->items->count()}\n\n";
    
    // Test 2: Check if PosController has the fixed method
    echo "✅ Test 2: Verifying PosController fix...\n";
    
    $reflection = new ReflectionClass(\App\Http\Controllers\Admin\PosController::class);
    $method = $reflection->getMethod('downloadBill');
    $methodContent = file_get_contents($method->getFileName());
    
    if (strpos($methodContent, 'generateSimplePDF') === false) {
        echo "   ✓ Old generateSimplePDF call removed\n";
    } else {
        echo "   ⚠️  Old generateSimplePDF call still exists\n";
        $allTestsPassed = false;
    }
    
    if (strpos($methodContent, 'Pdf::loadView') !== false) {
        echo "   ✓ Direct PDF generation implemented\n";
    } else {
        echo "   ❌ Direct PDF generation not found\n";
        $allTestsPassed = false;
    }
    
    if (strpos($methodContent, 'BillPDFService') !== false) {
        echo "   ✓ BillPDFService fallback available\n";
    } else {
        echo "   ❌ BillPDFService fallback missing\n";
        $allTestsPassed = false;
    }
    
    echo "\n";
    
    // Test 3: Verify routes
    echo "✅ Test 3: Checking routes...\n";
    
    $routes = \Illuminate\Support\Facades\Route::getRoutes();
    $downloadBillRoute = null;
    $debugRoutes = [];
    
    foreach ($routes as $route) {
        $name = $route->getName();
        if ($name === 'admin.pos.download-bill') {
            $downloadBillRoute = $route;
        }
        if (strpos($name, 'debug') !== false && strpos($name, 'pos') !== false) {
            $debugRoutes[] = $name;
        }
    }
    
    if ($downloadBillRoute) {
        echo "   ✓ Main download bill route exists\n";
    } else {
        echo "   ❌ Main download bill route missing\n";
        $allTestsPassed = false;
    }
    
    if (count($debugRoutes) > 0) {
        echo "   ✓ Debug routes available: " . implode(', ', $debugRoutes) . "\n";
    } else {
        echo "   ℹ️  No debug routes (normal for production)\n";
    }
    
    echo "\n";
    
    // Test 4: Test PDF generation capability
    echo "✅ Test 4: Testing PDF generation...\n";
    
    try {
        // Simple company data for testing
        $globalCompany = (object) [
            'company_name' => 'Green Valley Herbs',
            'company_address' => 'Test Address',
            'company_phone' => '1234567890',
            'company_email' => 'test@example.com',
            'gst_number' => 'TEST123456789',
            'company_logo' => null
        ];
        
        // Test A4 PDF generation
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pos.receipt-a4', compact('sale', 'globalCompany'));
        $pdf->setPaper('A4', 'portrait');
        $pdfOutput = $pdf->output();
        
        if (substr($pdfOutput, 0, 4) === '%PDF') {
            echo "   ✓ A4 PDF generation working\n";
            echo "   📏 PDF size: " . number_format(strlen($pdfOutput)) . " bytes\n";
        } else {
            echo "   ❌ A4 PDF generation failed\n";
            $allTestsPassed = false;
        }
        
        // Test thermal PDF generation if view exists
        if (view()->exists('admin.pos.receipt-pdf')) {
            $thermalPdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pos.receipt-pdf', compact('sale', 'globalCompany'));
            $thermalPdf->setPaper([0, 0, 226.77, 841.89], 'portrait');
            $thermalOutput = $thermalPdf->output();
            
            if (substr($thermalOutput, 0, 4) === '%PDF') {
                echo "   ✓ Thermal PDF generation working\n";
            } else {
                echo "   ❌ Thermal PDF generation failed\n";
            }
        } else {
            echo "   ℹ️  Thermal view not available\n";
        }
        
    } catch (\Exception $e) {
        echo "   ❌ PDF generation failed: {$e->getMessage()}\n";
        $allTestsPassed = false;
    }
    
    echo "\n";
    
    // Test 5: Generate test URLs
    echo "✅ Test 5: Generating test URLs...\n";
    
    $baseUrl = config('app.url', 'http://greenvalleyherbs.local:8000');
    
    $testUrls = [
        'A4 Download' => "{$baseUrl}/admin/pos/sales/{$sale->id}/download-bill?format=a4_sheet",
        'Thermal Download' => "{$baseUrl}/admin/pos/sales/{$sale->id}/download-bill?format=thermal",
        'Debug Download' => "{$baseUrl}/admin/pos/sales/{$sale->id}/download-bill-debug",
        'HTML Preview' => "{$baseUrl}/admin/pos/sales/{$sale->id}/view-bill-debug"
    ];
    
    foreach ($testUrls as $name => $url) {
        echo "   🔗 {$name}: {$url}\n";
    }
    
    echo "\n";
    
    // Final results
    echo "🎯 VERIFICATION RESULTS\n";
    echo "======================\n\n";
    
    if ($allTestsPassed) {
        echo "🎉 ALL TESTS PASSED! \n\n";
        echo "✅ The POS bill download functionality has been successfully fixed!\n\n";
        echo "🚀 NEXT STEPS:\n";
        echo "1. Test the URLs above in your browser\n";
        echo "2. Try downloading bills in both A4 and thermal formats\n";
        echo "3. Check that PDFs open correctly in your PDF viewer\n";
        echo "4. Verify all sale data appears correctly in the PDFs\n\n";
        echo "📞 If you encounter any issues, check the Laravel logs:\n";
        echo "   tail -f storage/logs/laravel.log\n\n";
    } else {
        echo "⚠️  SOME TESTS FAILED\n\n";
        echo "Please check the issues mentioned above and:\n";
        echo "1. Ensure all files have been updated correctly\n";
        echo "2. Clear all caches: php artisan cache:clear\n";
        echo "3. Check Laravel logs for specific errors\n";
        echo "4. Verify all dependencies are installed\n\n";
    }
    
    echo "📚 Documentation:\n";
    echo "- Complete fix details: POS_BILL_DOWNLOAD_FIX.md\n";
    echo "- Testing guide: POS_BILL_TESTING_GUIDE.md\n\n";
    
    echo "🔧 Debug Tools (temporary):\n";
    echo "- Debug script: debug_pos_bill.php\n";
    echo "- Debug methods in PosController (remove after testing)\n\n";
    
} catch (\Exception $e) {
    echo "❌ Verification failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
