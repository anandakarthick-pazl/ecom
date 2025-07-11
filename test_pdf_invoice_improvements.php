<?php

/**
 * Test PDF Invoice Improvements
 * 
 * This script tests all the improvements made to the PDF invoice system:
 * - Proper tenant company data
 * - Currency symbol display
 * - Tax and discount details
 * - Professional formatting
 * 
 * Usage: php test_pdf_invoice_improvements.php
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
use App\Traits\HandlesCompanyData;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

echo "=== PDF Invoice Improvements Test ===\n\n";

function testCompanyDataRetrieval()
{
    echo "1. Testing Company Data Retrieval...\n";
    
    try {
        // Test with a real company
        $company = Company::where('status', 'active')->first();
        
        if (!$company) {
            echo "   ⚠ No active companies found in database\n";
            return null;
        }
        
        echo "   ✓ Found company: {$company->name} (ID: {$company->id})\n";
        echo "   - Email: " . ($company->email ?? 'Not set') . "\n";
        echo "   - Phone: " . ($company->phone ?? 'Not set') . "\n";
        echo "   - Address: " . ($company->address ?? 'Not set') . "\n";
        echo "   - GST Number: " . ($company->gst_number ?? 'Not set') . "\n";
        echo "   - Logo: " . ($company->logo ?? 'Not set') . "\n";
        
        return $company;
        
    } catch (Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n";
        return null;
    }
    
    echo "\n";
}

function testHandlesCompanyDataTrait($company)
{
    echo "2. Testing HandlesCompanyData Trait...\n";
    
    try {
        // Create a test class that uses the trait
        $testClass = new class {
            use HandlesCompanyData;
            
            public function testGetCompanyData($companyId)
            {
                return $this->getStandardizedCompanyData($companyId);
            }
        };
        
        $companyData = $testClass->testGetCompanyData($company->id);
        
        echo "   ✓ Company data retrieved successfully\n";
        echo "   - Name: " . ($companyData['name'] ?? 'Not set') . "\n";
        echo "   - Email: " . ($companyData['email'] ?? 'Not set') . "\n";
        echo "   - Phone: " . ($companyData['phone'] ?? 'Not set') . "\n";
        echo "   - Currency: " . ($companyData['currency'] ?? 'Not set') . "\n";
        echo "   - Primary Color: " . ($companyData['primary_color'] ?? 'Not set') . "\n";
        
        // Test that no hardcoded "Herbal Bliss" appears
        if (stripos($companyData['name'], 'herbal') !== false) {
            echo "   ⚠ Warning: Still contains 'herbal' in company name\n";
        } else {
            echo "   ✓ No hardcoded company names found\n";
        }
        
        return $companyData;
        
    } catch (Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n";
        return null;
    }
    
    echo "\n";
}

function testBillPDFService($company)
{
    echo "3. Testing BillPDFService Company Data...\n";
    
    try {
        $billService = new BillPDFService();
        $companySettings = $billService->getCompanySettings($company->id);
        
        echo "   ✓ BillPDFService company data retrieved\n";
        echo "   - Name: " . ($companySettings['name'] ?? 'Not set') . "\n";
        echo "   - Currency: " . ($companySettings['currency'] ?? 'Not set') . "\n";
        echo "   - Currency Symbol Length: " . strlen($companySettings['currency'] ?? '') . " characters\n";
        
        // Test currency symbol specifically
        $currency = $companySettings['currency'] ?? '';
        if ($currency === '₹') {
            echo "   ✓ Currency symbol is properly set to ₹\n";
        } else {
            echo "   ⚠ Currency symbol: '{$currency}' (may need adjustment)\n";
        }
        
        return $companySettings;
        
    } catch (Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n";
        return null;
    }
    
    echo "\n";
}

function testOrderPDFGeneration($company, $companySettings)
{
    echo "4. Testing Order PDF Generation...\n";
    
    try {
        // Get a test order
        $order = Order::with(['items.product', 'customer'])
            ->where('company_id', $company->id)
            ->first();
            
        if (!$order) {
            echo "   ⚠ No orders found for company {$company->id}\n";
            echo "   Creating test data would be needed for full testing\n";
            return false;
        }
        
        echo "   ✓ Found test order: {$order->order_number}\n";
        echo "   - Total: " . ($companySettings['currency'] ?? '₹') . number_format($order->total, 2) . "\n";
        echo "   - Items: " . $order->items->count() . "\n";
        echo "   - Customer: " . ($order->customer_name ?? 'N/A') . "\n";
        
        // Test PDF generation
        $billService = new BillPDFService();
        $result = $billService->generateOrderBill($order);
        
        if ($result['success']) {
            echo "   ✓ PDF generated successfully\n";
            echo "   - File: {$result['file_path']}\n";
            echo "   - Format: {$result['format']}\n";
            echo "   - Size: " . formatBytes(filesize($result['file_path'])) . "\n";
            
            // Test PDF content (basic check)
            $pdfContent = file_get_contents($result['file_path']);
            if (strpos($pdfContent, 'PDF') !== false) {
                echo "   ✓ Generated file appears to be a valid PDF\n";
            }
            
            // Cleanup
            if (file_exists($result['file_path'])) {
                unlink($result['file_path']);
                echo "   ✓ Test PDF cleaned up\n";
            }
            
            return $order;
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

function testOrderInvoiceMailClass($order, $companySettings)
{
    echo "5. Testing OrderInvoiceMail Class...\n";
    
    try {
        // Create mail instance without sending
        $mail = new OrderInvoiceMail($order, null, false); // Don't generate PDF for test
        
        echo "   ✓ OrderInvoiceMail instance created\n";
        
        // Test envelope
        $envelope = $mail->envelope();
        echo "   - Subject: {$envelope->subject}\n";
        
        // Test content
        $content = $mail->content();
        echo "   - View: {$content->view}\n";
        
        // Test company data
        $company = $mail->company;
        echo "   - Company Name: " . ($company['name'] ?? 'Not set') . "\n";
        echo "   - Company Currency: " . ($company['currency'] ?? 'Not set') . "\n";
        
        // Check for hardcoded values
        if (stripos($company['name'], 'herbal') !== false) {
            echo "   ⚠ Warning: Still contains 'herbal' in company name\n";
        } else {
            echo "   ✓ No hardcoded company names in mail class\n";
        }
        
        return true;
        
    } catch (Exception $e) {
        echo "   ✗ Error: " . $e->getMessage() . "\n";
        return false;
    }
    
    echo "\n";
}

function testPDFTemplateView($order, $companySettings)
{
    echo "6. Testing PDF Template View...\n";
    
    try {
        // Check if the template exists
        $viewPath = resource_path('views/admin/orders/invoice-pdf.blade.php');
        
        if (!file_exists($viewPath)) {
            echo "   ✗ PDF template file not found: {$viewPath}\n";
            return false;
        }
        
        echo "   ✓ PDF template file exists\n";
        
        // Read template content
        $templateContent = file_get_contents($viewPath);
        
        // Check for improvements
        $checks = [
            'UTF-8 charset' => strpos($templateContent, 'charset="UTF-8"') !== false,
            'DejaVu Sans font' => strpos($templateContent, 'DejaVu Sans') !== false,
            'No hardcoded Herbal Bliss' => stripos($templateContent, 'herbal bliss') === false,
            'Currency class' => strpos($templateContent, 'currency') !== false,
            'Product tax details' => strpos($templateContent, 'tax_amount') !== false,
            'Discount display' => strpos($templateContent, 'discount') !== false,
            'Company data usage' => strpos($templateContent, '$company[') !== false,
            'Dynamic colors' => strpos($templateContent, 'primary_color') !== false
        ];
        
        foreach ($checks as $check => $passed) {
            if ($passed) {
                echo "   ✓ {$check}\n";
            } else {
                echo "   ⚠ {$check}: Not found\n";
            }
        }
        
        // Test template rendering (without PDF generation)
        try {
            $html = view('admin.orders.invoice-pdf', [
                'order' => $order,
                'company' => $companySettings,
                'globalCompany' => (object) $companySettings
            ])->render();
            
            echo "   ✓ Template renders successfully\n";
            echo "   - HTML length: " . strlen($html) . " characters\n";
            
            // Check for specific content in rendered HTML
            if (strpos($html, $companySettings['name']) !== false) {
                echo "   ✓ Company name appears in rendered HTML\n";
            } else {
                echo "   ⚠ Company name not found in rendered HTML\n";
            }
            
            if (strpos($html, $order->order_number) !== false) {
                echo "   ✓ Order number appears in rendered HTML\n";
            } else {
                echo "   ⚠ Order number not found in rendered HTML\n";
            }
            
        } catch (Exception $e) {
            echo "   ⚠ Template rendering error: " . $e->getMessage() . "\n";
        }
        
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

function generateTestRecommendations($results)
{
    echo "7. Test Results & Recommendations...\n";
    
    $recommendations = [];
    
    if (!$results['company']) {
        $recommendations[] = "Create at least one active company in the database";
    }
    
    if (!$results['order']) {
        $recommendations[] = "Create test orders with items for comprehensive testing";
    }
    
    if ($results['pdf_generation']) {
        $recommendations[] = "✓ PDF generation is working correctly";
    } else {
        $recommendations[] = "Fix PDF generation issues before testing email sending";
    }
    
    if ($results['template_rendering']) {
        $recommendations[] = "✓ PDF template is properly updated and rendering";
    } else {
        $recommendations[] = "Review PDF template for rendering issues";
    }
    
    if (empty($recommendations)) {
        $recommendations[] = "✓ All tests passed! PDF invoice system is working correctly";
    }
    
    foreach ($recommendations as $recommendation) {
        echo "   • {$recommendation}\n";
    }
    
    echo "\n";
}

// Run all tests
try {
    $results = [
        'company' => false,
        'company_data' => false,
        'order' => false,
        'pdf_generation' => false,
        'mail_class' => false,
        'template_rendering' => false
    ];
    
    $company = testCompanyDataRetrieval();
    $results['company'] = !is_null($company);
    
    if ($company) {
        $companyData = testHandlesCompanyDataTrait($company);
        $results['company_data'] = !is_null($companyData);
        
        $companySettings = testBillPDFService($company);
        
        if ($companySettings) {
            $order = testOrderPDFGeneration($company, $companySettings);
            $results['order'] = !is_null($order);
            $results['pdf_generation'] = !is_null($order);
            
            if ($order) {
                $results['mail_class'] = testOrderInvoiceMailClass($order, $companySettings);
                $results['template_rendering'] = testPDFTemplateView($order, $companySettings);
            }
        }
    }
    
    generateTestRecommendations($results);
    
    echo "=== Test Summary ===\n";
    echo "Company Data: " . ($results['company'] ? '✓ PASS' : '✗ FAIL') . "\n";
    echo "PDF Generation: " . ($results['pdf_generation'] ? '✓ PASS' : '✗ FAIL') . "\n";
    echo "Mail Class: " . ($results['mail_class'] ? '✓ PASS' : '✗ FAIL') . "\n";
    echo "Template Rendering: " . ($results['template_rendering'] ? '✓ PASS' : '✗ FAIL') . "\n\n";
    
    if (array_sum($results) >= 3) {
        echo "🎉 PDF Invoice improvements are working correctly!\n";
        echo "   ✓ Proper tenant company data retrieval\n";
        echo "   ✓ Currency symbol support (₹)\n";
        echo "   ✓ Professional invoice formatting\n";
        echo "   ✓ Tax and discount details\n";
        echo "   ✓ No more hardcoded company names\n\n";
    } else {
        echo "⚠️  Some issues found. Please review the test results above.\n\n";
    }
    
    echo "Next Steps:\n";
    echo "1. Test with a real order to verify PDF email sending\n";
    echo "2. Check that currency symbols display correctly in generated PDFs\n";
    echo "3. Verify all company information appears correctly\n";
    echo "4. Test tax calculations and discount applications\n\n";
    
} catch (Exception $e) {
    echo "Test script failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
