<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\AppSetting;
use App\Services\BillPDFService;
use App\Services\TwilioWhatsAppService;
use App\Mail\OrderInvoiceMail;
use App\Models\SuperAdmin\WhatsAppConfig;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestInvoiceFunctionality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:test 
                           {order_id : The ID of the order to test}
                           {--format=auto : Invoice format (thermal, a4_sheet, or auto)}
                           {--test-email : Test email functionality}
                           {--test-whatsapp : Test WhatsApp functionality}
                           {--test-pdf : Test PDF generation only}
                           {--email= : Override email address for testing}
                           {--phone= : Override phone number for testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test invoice functionality including PDF generation, email sending, and WhatsApp integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧾 Starting Invoice Functionality Test');
        $this->info('=====================================');

        $orderId = $this->argument('order_id');
        $format = $this->option('format');
        
        // Load order
        $order = Order::with(['items.product', 'customer'])->find($orderId);
        
        if (!$order) {
            $this->error("❌ Order with ID {$orderId} not found!");
            return Command::FAILURE;
        }

        $this->info("📋 Testing Order: {$order->order_number}");
        $this->info("👤 Customer: {$order->customer_name}");
        $this->info("📧 Email: " . ($order->customer_email ?: 'Not provided'));
        $this->info("📱 Mobile: " . ($order->customer_mobile ?: 'Not provided'));
        $this->newLine();

        // Test configuration
        $this->testConfiguration($order->company_id);

        // Test PDF generation
        if ($this->option('test-pdf') || !$this->hasSpecificTest()) {
            $this->testPdfGeneration($order, $format);
        }

        // Test email functionality
        if ($this->option('test-email') || !$this->hasSpecificTest()) {
            $this->testEmailFunctionality($order, $format);
        }

        // Test WhatsApp functionality
        if ($this->option('test-whatsapp') || !$this->hasSpecificTest()) {
            $this->testWhatsAppFunctionality($order, $format);
        }

        $this->newLine();
        $this->info('✅ Invoice functionality test completed!');
        
        return Command::SUCCESS;
    }

    /**
     * Check if any specific test is requested
     */
    private function hasSpecificTest()
    {
        return $this->option('test-email') || 
               $this->option('test-whatsapp') || 
               $this->option('test-pdf');
    }

    /**
     * Test configuration settings
     */
    private function testConfiguration($companyId)
    {
        $this->info('⚙️  Testing Configuration');
        $this->info('------------------------');

        try {
            // Test bill format configuration
            $thermalEnabled = AppSetting::getForTenant('thermal_printer_enabled', $companyId) ?? false;
            $a4Enabled = AppSetting::getForTenant('a4_sheet_enabled', $companyId) ?? true;
            $defaultFormat = AppSetting::getForTenant('default_bill_format', $companyId) ?? 'a4_sheet';

            $this->table(['Setting', 'Value'], [
                ['Thermal Enabled', $thermalEnabled ? '✅ Yes' : '❌ No'],
                ['A4 Enabled', $a4Enabled ? '✅ Yes' : '❌ No'],
                ['Default Format', $defaultFormat],
            ]);

            if (!$thermalEnabled && !$a4Enabled) {
                $this->error('❌ No bill formats are enabled! At least one format must be enabled.');
            } else {
                $this->info('✅ Bill format configuration is valid');
            }

            // Test email configuration
            $emailEnabled = AppSetting::getForTenant('email_notifications', $companyId) ?? true;
            $smtpHost = AppSetting::getForTenant('smtp_host', $companyId);
            
            if ($emailEnabled && $smtpHost) {
                $this->info('✅ Email configuration appears to be set up');
            } else {
                $this->warn('⚠️  Email configuration may be incomplete');
            }

            // Test WhatsApp configuration
            $whatsappConfig = WhatsAppConfig::where('company_id', $companyId)->first();
            if ($whatsappConfig && $whatsappConfig->isConfigured() && $whatsappConfig->is_enabled) {
                $this->info('✅ WhatsApp configuration is active');
            } else {
                $this->warn('⚠️  WhatsApp is not configured or disabled');
            }

        } catch (\Exception $e) {
            $this->error('❌ Configuration test failed: ' . $e->getMessage());
        }

        $this->newLine();
    }

    /**
     * Test PDF generation
     */
    private function testPdfGeneration($order, $format)
    {
        $this->info('📄 Testing PDF Generation');
        $this->info('-------------------------');

        try {
            $billService = new BillPDFService();
            
            // Determine formats to test
            $formatsToTest = [];
            if ($format === 'auto') {
                $defaultFormat = AppSetting::getForTenant('default_bill_format', $order->company_id) ?? 'a4_sheet';
                $formatsToTest[] = $defaultFormat;
            } else {
                $formatsToTest[] = $format;
            }

            foreach ($formatsToTest as $testFormat) {
                $this->info("🔄 Generating {$testFormat} PDF...");
                $startTime = microtime(true);
                
                $result = $billService->generateOrderBill($order, $testFormat);
                
                $endTime = microtime(true);
                $executionTime = round(($endTime - $startTime) * 1000, 2);

                if ($result['success']) {
                    $fileSize = file_exists($result['file_path']) ? filesize($result['file_path']) : 0;
                    $this->info("✅ {$testFormat} PDF generated successfully");
                    $this->line("   📁 File: {$result['file_path']}");
                    $this->line("   📏 Size: " . $this->formatBytes($fileSize));
                    $this->line("   ⏱️  Time: {$executionTime}ms");
                    
                    // Verify PDF content
                    if ($fileSize > 0) {
                        $this->info("✅ PDF file is valid (non-empty)");
                        
                        // Clean up test file
                        unlink($result['file_path']);
                        $this->line("   🧹 Temporary file cleaned up");
                    } else {
                        $this->error("❌ PDF file is empty");
                    }
                } else {
                    $this->error("❌ {$testFormat} PDF generation failed: " . $result['error']);
                }
            }

        } catch (\Exception $e) {
            $this->error('❌ PDF generation test failed: ' . $e->getMessage());
            Log::error('Invoice test PDF generation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        $this->newLine();
    }

    /**
     * Test email functionality
     */
    private function testEmailFunctionality($order, $format)
    {
        $this->info('📧 Testing Email Functionality');
        $this->info('-----------------------------');

        try {
            $testEmail = $this->option('email') ?: $order->customer_email;
            
            if (!$testEmail) {
                $this->warn('⚠️  No email address available for testing');
                $this->info('   Use --email=your@email.com to test with a specific email');
                return;
            }

            $this->info("🔄 Sending test invoice email to: {$testEmail}");
            $startTime = microtime(true);

            // Create test mail
            $mail = new OrderInvoiceMail($order);
            
            // Send email
            Mail::to($testEmail)->send($mail);
            
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2);

            $this->info("✅ Email sent successfully");
            $this->line("   📬 To: {$testEmail}");
            $this->line("   ⏱️  Time: {$executionTime}ms");

        } catch (\Exception $e) {
            $this->error('❌ Email test failed: ' . $e->getMessage());
            Log::error('Invoice test email failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        $this->newLine();
    }

    /**
     * Test WhatsApp functionality
     */
    private function testWhatsAppFunctionality($order, $format)
    {
        $this->info('📱 Testing WhatsApp Functionality');
        $this->info('--------------------------------');

        try {
            $testPhone = $this->option('phone') ?: $order->customer_mobile;
            
            if (!$testPhone) {
                $this->warn('⚠️  No phone number available for testing');
                $this->info('   Use --phone=+1234567890 to test with a specific phone number');
                return;
            }

            // Get WhatsApp configuration
            $whatsappConfig = WhatsAppConfig::where('company_id', $order->company_id)->first();
            
            if (!$whatsappConfig || !$whatsappConfig->isConfigured()) {
                $this->warn('⚠️  WhatsApp is not configured for this company');
                return;
            }

            if (!$whatsappConfig->is_enabled) {
                $this->warn('⚠️  WhatsApp is disabled for this company');
                return;
            }

            $this->info("🔄 Testing WhatsApp message to: {$testPhone}");
            $startTime = microtime(true);

            // Generate PDF for WhatsApp
            $billService = new BillPDFService();
            $testFormat = $format === 'auto' 
                ? AppSetting::getForTenant('default_bill_format', $order->company_id) ?? 'a4_sheet'
                : $format;
                
            $pdfResult = $billService->generateOrderBill($order, $testFormat);
            
            if (!$pdfResult['success']) {
                $this->error('❌ Failed to generate PDF for WhatsApp: ' . $pdfResult['error']);
                return;
            }

            // Test WhatsApp sending
            $whatsappService = new TwilioWhatsAppService($whatsappConfig);
            $result = $whatsappService->sendBillPDF($order, $pdfResult['file_path'], 'Test invoice from console command');

            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2);

            // Clean up PDF
            if (file_exists($pdfResult['file_path'])) {
                unlink($pdfResult['file_path']);
            }

            if ($result['success']) {
                $this->info("✅ WhatsApp message sent successfully");
                $this->line("   📱 To: {$result['sent_to']}");
                $this->line("   📄 Format: {$testFormat}");
                $this->line("   🆔 Message SID: {$result['message_sid']}");
                $this->line("   ⏱️  Time: {$executionTime}ms");
            } else {
                $this->error("❌ WhatsApp sending failed: " . $result['error']);
            }

        } catch (\Exception $e) {
            $this->error('❌ WhatsApp test failed: ' . $e->getMessage());
            Log::error('Invoice test WhatsApp failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        $this->newLine();
    }

    /**
     * Format bytes for display
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
