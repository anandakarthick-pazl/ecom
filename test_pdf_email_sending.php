<?php

/**
 * Test script for PDF email sending functionality
 * 
 * This script tests the OrderInvoiceMail functionality to ensure
 * PDF emails are being sent correctly.
 * 
 * Usage: php test_pdf_email_sending.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Order;
use App\Mail\OrderInvoiceMail;
use App\Jobs\SendOrderInvoiceEmail;
use App\Services\BillPDFService;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

// Initialize Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

echo "=== PDF Email Sending Test ===\n\n";

function testEmailConfiguration()
{
    echo "1. Testing Email Configuration...\n";
    
    // Check mail configuration
    $mailDriver = config('mail.default');
    $mailFrom = config('mail.from');
    
    echo "   Mail Driver: {$mailDriver}\n";
    echo "   Mail From: {$mailFrom['address']} ({$mailFrom['name']})\n";
    
    // Check if email notifications are enabled
    $emailNotifications = AppSetting::get('email_notifications', true);
    echo "   Email Notifications Enabled: " . ($emailNotifications ? 'Yes' : 'No') . "\n";
    
    // Check if queue is enabled
    $useQueue = AppSetting::get('use_email_queue', true);
    echo "   Use Email Queue: " . ($useQueue ? 'Yes' : 'No') . "\n";
    
    echo "   ✓ Email configuration checked\n\n";
}

function testPDFGeneration()
{
    echo "2. Testing PDF Generation...\n";
    
    try {
        // Get a test order
        $order = Order::with(['items.product', 'customer'])
            ->whereNotNull('customer_email')
            ->first();
            
        if (!$order) {
            echo "   ⚠ No orders with customer email found, creating test data...\n";
            // You might want to create test order data here
            return false;
        }
        
        echo "   Testing with Order ID: {$order->id}, Order Number: {$order->order_number}\n";
        
        // Test BillPDFService
        $billService = new BillPDFService();
        $result = $billService->generateOrderBill($order);
        
        if ($result['success']) {
            echo "   ✓ PDF generated successfully: {$result['file_path']}\n";
            echo "   File size: " . formatBytes(filesize($result['file_path'])) . "\n";
            
            // Cleanup test file
            if (file_exists($result['file_path'])) {
                unlink($result['file_path']);
                echo "   ✓ Test PDF file cleaned up\n";
            }
            
            return $order;
        } else {
            echo "   ✗ PDF generation failed: {$result['error']}\n";
            return false;
        }
        
    } catch (Exception $e) {
        echo "   ✗ PDF generation test failed: " . $e->getMessage() . "\n";
        return false;
    }
    
    echo "\n";
}

function testEmailSending($order)
{
    echo "3. Testing Email Sending...\n";
    
    if (!$order) {
        echo "   ⚠ No order available for testing\n\n";
        return;
    }
    
    try {
        $testEmail = $order->customer_email ?: 'test@example.com';
        echo "   Testing email to: {$testEmail}\n";
        
        // Test OrderInvoiceMail creation
        $mail = new OrderInvoiceMail($order, null, true);
        echo "   ✓ OrderInvoiceMail instance created\n";
        
        // Get envelope
        $envelope = $mail->envelope();
        echo "   Email Subject: {$envelope->subject}\n";
        
        // Get content
        $content = $mail->content();
        echo "   Email View: {$content->view}\n";
        
        // Get attachments
        $attachments = $mail->attachments();
        echo "   Attachments: " . count($attachments) . "\n";
        
        if (count($attachments) > 0) {
            foreach ($attachments as $attachment) {
                echo "   - Attachment: {$attachment->as}\n";
            }
        }
        
        echo "   ✓ Email components tested successfully\n";
        
    } catch (Exception $e) {
        echo "   ✗ Email testing failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

function testQueueJob($order)
{
    echo "4. Testing Queue Job...\n";
    
    if (!$order) {
        echo "   ⚠ No order available for testing\n\n";
        return;
    }
    
    try {
        $testEmail = $order->customer_email ?: 'test@example.com';
        echo "   Creating SendOrderInvoiceEmail job for: {$testEmail}\n";
        
        // Create job instance
        $job = new SendOrderInvoiceEmail($order, $testEmail, null, true);
        echo "   ✓ Queue job instance created\n";
        
        // Test job properties
        echo "   Job tries: {$job->tries}\n";
        echo "   Job timeout: {$job->timeout} seconds\n";
        
        echo "   ✓ Queue job tested successfully\n";
        
    } catch (Exception $e) {
        echo "   ✗ Queue job testing failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

function testViewTemplates()
{
    echo "5. Testing View Templates...\n";
    
    try {
        // Check if required view templates exist
        $templates = [
            'emails.order-invoice',
            'admin.orders.invoice-pdf'
        ];
        
        foreach ($templates as $template) {
            if (view()->exists($template)) {
                echo "   ✓ Template exists: {$template}\n";
            } else {
                echo "   ✗ Template missing: {$template}\n";
            }
        }
        
    } catch (Exception $e) {
        echo "   ✗ Template testing failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

function testDatabaseConnections()
{
    echo "6. Testing Database Connections...\n";
    
    try {
        // Test database connection
        $ordersCount = DB::table('orders')->count();
        echo "   ✓ Database connected, {$ordersCount} orders found\n";
        
        // Test app settings
        $emailNotifications = AppSetting::get('email_notifications', 'default');
        echo "   ✓ App settings accessible\n";
        
    } catch (Exception $e) {
        echo "   ✗ Database test failed: " . $e->getMessage() . "\n";
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

function runDiagnostics()
{
    echo "7. Running System Diagnostics...\n";
    
    // Check PHP extensions
    $requiredExtensions = ['gd', 'zip', 'fileinfo', 'mbstring'];
    foreach ($requiredExtensions as $ext) {
        if (extension_loaded($ext)) {
            echo "   ✓ PHP Extension: {$ext}\n";
        } else {
            echo "   ✗ Missing PHP Extension: {$ext}\n";
        }
    }
    
    // Check memory limit
    $memoryLimit = ini_get('memory_limit');
    echo "   Memory Limit: {$memoryLimit}\n";
    
    // Check max execution time
    $maxExecutionTime = ini_get('max_execution_time');
    echo "   Max Execution Time: {$maxExecutionTime}s\n";
    
    // Check storage directories
    $directories = [
        storage_path('app/temp'),
        storage_path('app/public'),
        storage_path('logs')
    ];
    
    foreach ($directories as $dir) {
        if (is_dir($dir) && is_writable($dir)) {
            echo "   ✓ Directory writable: {$dir}\n";
        } else {
            echo "   ✗ Directory not writable: {$dir}\n";
        }
    }
    
    echo "\n";
}

// Run all tests
try {
    testEmailConfiguration();
    $order = testPDFGeneration();
    testEmailSending($order);
    testQueueJob($order);
    testViewTemplates();
    testDatabaseConnections();
    runDiagnostics();
    
    echo "=== Test Summary ===\n";
    echo "All tests completed. Check the output above for any issues.\n";
    echo "If you see ✗ symbols, those indicate problems that need to be fixed.\n\n";
    
    echo "Next Steps:\n";
    echo "1. Fix any issues shown above\n";
    echo "2. Configure your email driver (SMTP, etc.) in .env file\n";
    echo "3. Test with a real order to verify PDF email sending\n";
    echo "4. Monitor the logs for any email sending issues\n\n";
    
} catch (Exception $e) {
    echo "Test script failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
