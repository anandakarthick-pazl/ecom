<?php
/**
 * Debug script for POS bill download issue
 * This will help us understand what's happening with the PDF generation
 */

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Debugging POS Bill Download Issue\n";
echo "====================================\n\n";

try {
    // Test 1: Check if we can find the sale
    echo "✅ Test 1: Looking for POS Sale ID 11...\n";
    
    $sale = \App\Models\PosSale::with(['items.product', 'cashier'])->find(11);
    
    if (!$sale) {
        echo "❌ Sale ID 11 not found. Let's find an available sale...\n";
        $sale = \App\Models\PosSale::with(['items.product', 'cashier'])->first();
        if ($sale) {
            echo "✅ Found alternative sale ID: {$sale->id}\n";
        } else {
            echo "❌ No POS sales found in database!\n";
            exit;
        }
    } else {
        echo "✅ Sale found: {$sale->invoice_number}\n";
    }
    
    echo "   📄 Invoice: {$sale->invoice_number}\n";
    echo "   💰 Amount: ₹{$sale->total_amount}\n";
    echo "   📦 Items: {$sale->items->count()}\n";
    echo "   👤 Customer: " . ($sale->customer_name ?: 'Walk-in') . "\n\n";
    
    // Test 2: Check BillPDFService
    echo "✅ Test 2: Testing BillPDFService...\n";
    
    $billService = app(\App\Services\BillPDFService::class);
    echo "   ✓ BillPDFService instantiated\n";
    
    // Test 3: Check if we can get company settings
    echo "✅ Test 3: Getting company settings...\n";
    
    $companySettings = $billService->getCompanySettingsCache($sale->company_id);
    echo "   ✓ Company settings retrieved:\n";
    echo "   📛 Name: {$companySettings['name']}\n";
    echo "   📍 Address: " . ($companySettings['address'] ?: 'Not set') . "\n\n";
    
    // Test 4: Test view rendering
    echo "✅ Test 4: Testing view rendering...\n";
    
    $globalCompany = (object) $companySettings;
    
    try {
        $html = view('admin.pos.receipt-a4', compact('sale', 'globalCompany'))->render();
        echo "   ✓ A4 view renders successfully\n";
        echo "   📏 HTML size: " . strlen($html) . " characters\n";
        
        // Check for common issues in HTML
        if (strpos($html, '{{') !== false || strpos($html, '}}') !== false) {
            echo "   ⚠️  Warning: Unrendered Blade syntax found\n";
        }
        
        if (strpos($html, '$sale') !== false) {
            echo "   ⚠️  Warning: Unprocessed PHP variables found\n";
        }
        
    } catch (\Exception $e) {
        echo "   ❌ View rendering failed: {$e->getMessage()}\n";
        return;
    }
    
    // Test 5: Test PDF generation
    echo "\n✅ Test 5: Testing PDF generation...\n";
    
    try {
        // Test with dompdf directly
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pos.receipt-a4', compact('sale', 'globalCompany'));
        $pdf->setPaper('A4', 'portrait');
        
        echo "   ✓ PDF object created successfully\n";
        
        // Try to generate PDF output
        $pdfOutput = $pdf->output();
        echo "   ✓ PDF output generated\n";
        echo "   📏 PDF size: " . strlen($pdfOutput) . " bytes\n";
        
        // Check if it's actually a PDF
        if (substr($pdfOutput, 0, 4) === '%PDF') {
            echo "   ✅ Valid PDF generated!\n";
        } else {
            echo "   ❌ Generated content is not a valid PDF\n";
            echo "   🔍 First 100 characters: " . substr($pdfOutput, 0, 100) . "\n";
        }
        
    } catch (\Exception $e) {
        echo "   ❌ PDF generation failed: {$e->getMessage()}\n";
        echo "   📍 Error file: {$e->getFile()}:{$e->getLine()}\n";
    }
    
    // Test 6: Test the actual service methods
    echo "\n✅ Test 6: Testing BillPDFService methods...\n";
    
    try {
        echo "   🚀 Testing generateUltraFastPDF...\n";
        $result = $billService->generateUltraFastPDF($sale, 'a4_sheet');
        echo "   ✓ generateUltraFastPDF worked\n";
    } catch (\Exception $e) {
        echo "   ❌ generateUltraFastPDF failed: {$e->getMessage()}\n";
        
        try {
            echo "   🚀 Testing downloadPosSaleBillFast...\n";
            $result = $billService->downloadPosSaleBillFast($sale, 'a4_sheet');
            echo "   ✓ downloadPosSaleBillFast worked\n";
        } catch (\Exception $e2) {
            echo "   ❌ downloadPosSaleBillFast failed: {$e2->getMessage()}\n";
            
            try {
                echo "   🚀 Testing downloadPosSaleBill...\n";
                $result = $billService->downloadPosSaleBill($sale, 'a4_sheet');
                echo "   ✓ downloadPosSaleBill worked\n";
            } catch (\Exception $e3) {
                echo "   ❌ downloadPosSaleBill failed: {$e3->getMessage()}\n";
            }
        }
    }
    
    echo "\n🎯 DIAGNOSIS COMPLETE\n";
    echo "====================\n";
    
    if (isset($result)) {
        echo "✅ PDF generation is working! The issue might be in the browser or web server.\n";
        echo "\n🔧 RECOMMENDED ACTIONS:\n";
        echo "1. Clear browser cache and try again\n";
        echo "2. Try accessing the URL directly in incognito mode\n";
        echo "3. Check Laravel logs in storage/logs/laravel.log\n";
        echo "4. Test with: http://greenvalleyherbs.local:8000/admin/pos/sales/{$sale->id}/download-bill?format=a4_sheet\n";
    } else {
        echo "❌ PDF generation is failing. Check the errors above.\n";
    }

} catch (\Exception $e) {
    echo "❌ Fatal error during diagnosis: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
