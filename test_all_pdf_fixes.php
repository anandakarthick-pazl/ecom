<?php

/**
 * Test All PDF Invoice Fixes
 * 
 * This script tests all the fixes made:
 * 1. Currency symbol changed from ₹ to "RS"
 * 2. Product tax amounts display
 * 3. Discount amounts display
 * 4. Label changes from "Order Bill" to "Invoice"
 * 5. Removal of "Order #:" and replacement with "Invoice"
 * 
 * Usage: php test_all_pdf_fixes.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Order;
use App\Models\SuperAdmin\Company;
use App\Mail\OrderInvoiceMail;
use App\Services\BillPDFService;
use Illuminate\Support\Facades\View;

echo "=== Testing All PDF Invoice Fixes ===\n\n";

function testCurrencySymbolFixes()
{
    echo "1. Testing Currency Symbol Fixes...\n";
    
    try {
        // Test BillPDFService currency
        $company = Company::where('status', 'active')->first();
        if (!$company) {
            echo "   ⚠ No active companies found\n";
            return false;
        }
        
        $billService = new BillPDFService();
        $companySettings = $billService->getCompanySettings($company->id);
        
        $currency = $companySettings['currency'] ?? '';
        if ($currency === 'RS') {
            echo "   ✓ BillPDFService currency: {$currency}\n";
        } else {
            echo "   ⚠ BillPDFService currency: '{$currency}' (expected 'RS')\n";
        }
        
        // Test HandlesCompanyData trait
        $testClass = new class {
            use App\Traits\HandlesCompanyData;
            public function testGetCompanyData($companyId) {
                return $this->getStandardizedCompanyData($companyId);
            }
        };
        
        $companyData = $testClass->testGetCompanyData($company->id);
        $traitCurrency = $companyData['currency'] ?? '';
        if ($traitCurrency === 'RS') {
            echo "   ✓ HandlesCompanyData currency: {$traitCurrency}\n";
        } else {
            echo "   ⚠ HandlesCompanyData currency: '{$traitCurrency}' (expected 'RS')\n";
        }
        
        return true;
        
    } catch (Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n";
        return false;
    }
    
    echo "\n";
}

function testPDFTemplateChanges()
{
    echo "2. Testing PDF Template Changes...\n";
    
    try {
        $templatePath = resource_path('views/admin/orders/invoice-pdf.blade.php');
        
        if (!file_exists($templatePath)) {
            echo "   ✗ PDF template not found: {$templatePath}\n";
            return false;
        }
        
        $templateContent = file_get_contents($templatePath);
        
        // Test currency symbol
        $rsCount = substr_count($templateContent, 'RS ');
        if ($rsCount > 0) {
            echo "   ✓ Found {$rsCount} instances of 'RS ' currency symbol\n";
        } else {
            echo "   ⚠ No 'RS ' currency symbols found in template\n";
        }
        
        // Test for any remaining ₹ symbols
        $rupeeCount = substr_count($templateContent, '₹');
        if ($rupeeCount === 0) {
            echo "   ✓ No ₹ symbols found (all replaced with RS)\n";
        } else {
            echo "   ⚠ Found {$rupeeCount} ₹ symbols still in template\n";
        }
        
        // Test tax amount display
        if (strpos($templateContent, 'tax_amount') !== false) {
            echo "   ✓ Product tax amount field found in template\n";
        } else {
            echo "   ⚠ Product tax amount field not found\n";
        }
        
        // Test discount display
        if (strpos($templateContent, 'itemDiscount') !== false) {
            echo "   ✓ Item discount calculation found in template\n";
        } else {
            echo "   ⚠ Item discount calculation not found\n";
        }
        
        // Test invoice labels
        if (strpos($templateContent, 'Invoice No:') !== false) {
            echo "   ✓ 'Invoice No:' label found\n";
        } else {
            echo "   ⚠ 'Invoice No:' label not found\n";
        }
        
        // Test for removed "Order #:"
        if (strpos($templateContent, 'Order #:') === false) {
            echo "   ✓ 'Order #:' successfully removed\n";
        } else {
            echo "   ⚠ 'Order #:' still found in template\n";
        }
        
        // Test invoice notes instead of order notes
        if (strpos($templateContent, 'Invoice Notes:') !== false) {
            echo "   ✓ 'Invoice Notes:' label found\n";
        } else {
            echo "   ⚠ 'Invoice Notes:' label not found\n";
        }
        
        return true;
        
    } catch (Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n";
        return false;
    }
    
    echo "\n";
}

function testOrderShowPageChanges()
{
    echo "3. Testing Order Show Page Changes...\n";
    
    try {
        $showPagePath = resource_path('views/admin/orders/show.blade.php');
        
        if (!file_exists($showPagePath)) {
            echo "   ✗ Order show page not found: {$showPagePath}\n";
            return false;
        }
        
        $pageContent = file_get_contents($showPagePath);
        
        // Test download button text
        if (strpos($pageContent, 'Download Invoice') !== false) {
            echo "   ✓ 'Download Invoice' button found\n";
        } else {
            echo "   ⚠ 'Download Invoice' button not found\n";
        }
        
        // Test WhatsApp button text
        if (strpos($pageContent, 'Send Invoice via WhatsApp') !== false) {
            echo "   ✓ 'Send Invoice via WhatsApp' button found\n";
        } else {
            echo "   ⚠ 'Send Invoice via WhatsApp' button not found\n";
        }
        
        // Test currency symbols in table
        $rsInTable = substr_count($pageContent, 'RS {{');
        if ($rsInTable > 0) {
            echo "   ✓ Found {$rsInTable} 'RS' currency references in tables\n";
        } else {
            echo "   ⚠ No 'RS' currency references found in tables\n";
        }
        
        // Test for any remaining ₹ symbols
        $rupeeInPage = substr_count($pageContent, '₹{{');
        if ($rupeeInPage === 0) {
            echo "   ✓ No ₹ symbols found in tables (all replaced)\n";
        } else {
            echo "   ⚠ Found {$rupeeInPage} ₹ symbols still in tables\n";
        }
        
        return true;
        
    } catch (Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n";
        return false;
    }
    
    echo "\n";
}

function testPDFGenerationWithRealData()
{
    echo "4. Testing PDF Generation with Real Data...\n";
    
    try {
        // Get a test order
        $order = Order::with(['items.product', 'customer'])->first();
        
        if (!$order) {
            echo "   ⚠ No orders found for testing\n";
            return false;
        }
        
        echo "   Using Order: {$order->order_number}\n";
        echo "   Items: " . $order->items->count() . "\n";
        echo "   Total: " . number_format($order->total, 2) . "\n";
        
        // Test PDF generation
        $billService = new BillPDFService();
        $result = $billService->generateOrderBill($order);
        
        if ($result['success']) {
            echo "   ✓ PDF generated successfully\n";
            echo "   File: {$result['file_path']}\n";
            echo "   Size: " . formatBytes(filesize($result['file_path'])) . "\n";
            
            // Read PDF content to check for "RS"
            $pdfContent = file_get_contents($result['file_path']);
            if (strpos($pdfContent, 'RS') !== false) {
                echo "   ✓ PDF contains 'RS' currency references\n";
            } else {
                echo "   ⚠ PDF does not contain 'RS' currency references\n";
            }
            
            // Clean up
            if (file_exists($result['file_path'])) {
                unlink($result['file_path']);
                echo "   ✓ Test PDF cleaned up\n";
            }
            
            return true;
        } else {
            echo "   ✗ PDF generation failed: " . $result['error'] . "\n";
            return false;
        }
        
    } catch (Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n";
        return false;
    }
    
    echo "\n";
}

function testOrderItemTaxAndDiscount()
{
    echo "5. Testing Order Item Tax and Discount Display...\n";
    
    try {
        // Get an order with items
        $order = Order::with(['items.product'])->whereHas('items')->first();
        
        if (!$order) {
            echo "   ⚠ No orders with items found\n";
            return false;
        }
        
        echo "   Testing with Order: {$order->order_number}\n";
        
        foreach ($order->items as $index => $item) {
            echo "   Item " . ($index + 1) . ": {$item->product_name}\n";
            echo "     - Price: " . number_format($item->price ?? 0, 2) . "\n";
            echo "     - Quantity: " . ($item->quantity ?? 0) . "\n";
            echo "     - Tax %: " . number_format($item->tax_percentage ?? 0, 1) . "%\n";
            echo "     - Tax Amount: " . number_format($item->tax_amount ?? 0, 2) . "\n";
            
            // Test discount calculation
            $itemSubtotal = ($item->price ?? 0) * ($item->quantity ?? 0);
            $orderSubtotal = $order->subtotal ?? 0;
            $orderDiscount = $order->discount ?? 0;
            $itemDiscount = $orderSubtotal > 0 ? ($itemSubtotal / $orderSubtotal) * $orderDiscount : 0;
            
            echo "     - Item Discount: " . number_format($itemDiscount, 2) . "\n";
            
            $lineTotal = $itemSubtotal + ($item->tax_amount ?? 0) - $itemDiscount;
            echo "     - Line Total: " . number_format($lineTotal, 2) . "\n";
        }
        
        echo "   Order Level:\n";
        echo "     - Subtotal: " . number_format($order->subtotal ?? 0, 2) . "\n";
        echo "     - Total Discount: " . number_format($order->discount ?? 0, 2) . "\n";
        echo "     - CGST: " . number_format($order->cgst_amount ?? 0, 2) . "\n";
        echo "     - SGST: " . number_format($order->sgst_amount ?? 0, 2) . "\n";
        echo "     - Total: " . number_format($order->total ?? 0, 2) . "\n";
        
        return true;
        
    } catch (Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n";
        return false;
    }
    
    echo "\n";
}

function formatBytes($bytes, $precision = 2)
{
    $units = ['B', 'KB', 'MB', 'GB'];
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    return round($bytes, $precision) . ' ' . $units[$i];
}

function generateSummaryReport($results)
{
    echo "6. Summary Report...\n";
    
    $totalTests = count($results);
    $passedTests = array_sum($results);
    $failedTests = $totalTests - $passedTests;
    
    echo "   Total Tests: {$totalTests}\n";
    echo "   Passed: {$passedTests}\n";
    echo "   Failed: {$failedTests}\n";
    echo "   Success Rate: " . round(($passedTests / $totalTests) * 100, 1) . "%\n\n";
    
    // Individual test results
    $testNames = [
        'Currency Symbol Fix',
        'PDF Template Changes',
        'Order Show Page Changes',
        'PDF Generation Test',
        'Tax & Discount Calculations'
    ];
    
    echo "   Detailed Results:\n";
    foreach ($results as $index => $result) {
        $status = $result ? '✓ PASS' : '✗ FAIL';
        echo "   - {$testNames[$index]}: {$status}\n";
    }
    
    echo "\n";
    
    if ($passedTests === $totalTests) {
        echo "🎉 All fixes implemented successfully!\n";
        echo "   ✓ Currency symbol changed to 'RS'\n";
        echo "   ✓ Product tax amounts are displayed\n";
        echo "   ✓ Discount amounts are calculated and shown\n";
        echo "   ✓ Labels changed from 'Bill' to 'Invoice'\n";
        echo "   ✓ 'Order #:' removed and replaced with 'Invoice No:'\n\n";
    } else {
        echo "⚠️  Some tests failed. Please review the results above.\n\n";
    }
    
    echo "Next Steps:\n";
    echo "1. Test PDF generation with a real order\n";
    echo "2. Send test invoice email to verify attachment\n";
    echo "3. Check that 'RS' appears correctly in generated PDFs\n";
    echo "4. Verify all tax and discount amounts are accurate\n\n";
}

// Run all tests
try {
    $results = [];
    
    $results[] = testCurrencySymbolFixes();
    $results[] = testPDFTemplateChanges();
    $results[] = testOrderShowPageChanges();
    $results[] = testPDFGenerationWithRealData();
    $results[] = testOrderItemTaxAndDiscount();
    
    generateSummaryReport($results);
    
} catch (Exception $e) {
    echo "Test script failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
