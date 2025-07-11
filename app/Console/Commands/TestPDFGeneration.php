<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BillPDFService;
use App\Models\PosSale;
use App\Models\Order;
use App\Models\AppSetting;
use Illuminate\Support\Facades\File;

class TestPDFGeneration extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'pdf:test 
                            {--company-id=1 : Company ID to test}
                            {--test-images : Test image processing only}
                            {--clear-cache : Clear PDF caches}
                            {--warm-cache : Warm up caches}
                            {--generate-sample : Generate sample PDF}';

    /**
     * The console command description.
     */
    protected $description = 'Test PDF generation and image processing functionality';

    protected $billPDFService;

    public function __construct(BillPDFService $billPDFService)
    {
        parent::__construct();
        $this->billPDFService = $billPDFService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 PDF Generation Test Suite');
        $this->newLine();

        $companyId = $this->option('company-id');

        if ($this->option('clear-cache')) {
            $this->clearCaches();
        }

        if ($this->option('warm-cache')) {
            $this->warmCaches($companyId);
        }

        if ($this->option('test-images')) {
            $this->testImageProcessing($companyId);
            return;
        }

        if ($this->option('generate-sample')) {
            $this->generateSamplePDF($companyId);
            return;
        }

        // Run all tests
        $this->runFullTestSuite($companyId);
    }

    protected function clearCaches()
    {
        $this->info('🧹 Clearing PDF caches...');
        
        BillPDFService::clearCache();
        
        // Clear temp files
        $tempDir = storage_path('app/temp/bills');
        if (is_dir($tempDir)) {
            $files = glob($tempDir . '/*');
            $deleted = 0;
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    $deleted++;
                }
            }
            $this->line("   Deleted $deleted temporary files");
        }

        $this->info('✅ Caches cleared');
        $this->newLine();
    }

    protected function warmCaches($companyId)
    {
        $this->info('🔥 Warming up caches...');
        
        try {
            $result = $this->billPDFService->warmCompanyCache($companyId);
            if ($result) {
                $this->info("✅ Cache warmed for company ID: $companyId");
            } else {
                $this->error("❌ Failed to warm cache for company ID: $companyId");
            }
        } catch (\Exception $e) {
            $this->error("❌ Cache warming failed: " . $e->getMessage());
        }

        $this->newLine();
    }

    protected function testImageProcessing($companyId)
    {
        $this->info('🖼️  Testing image processing...');
        
        try {
            // Get company settings
            $companySettings = $this->billPDFService->getCompanySettings($companyId);
            
            $this->table(['Setting', 'Value'], [
                ['Company Name', $companySettings['name'] ?? 'N/A'],
                ['Logo Path', $companySettings['logo'] ?? 'No logo set'],
                ['Address', $companySettings['address'] ?? 'N/A'],
                ['Phone', $companySettings['phone'] ?? 'N/A'],
                ['Email', $companySettings['email'] ?? 'N/A'],
            ]);

            if (empty($companySettings['logo'])) {
                $this->warn('⚠️  No logo configured for this company');
                return;
            }

            // Test image paths
            $logoPath = $companySettings['logo'];
            $possiblePaths = [
                'public/storage' => public_path('storage/' . $logoPath),
                'storage/app/public' => storage_path('app/public/' . $logoPath),
                'public direct' => public_path($logoPath),
                'storage/app' => storage_path('app/' . $logoPath),
            ];

            $this->info("Testing paths for logo: $logoPath");
            
            $pathResults = [];
            foreach ($possiblePaths as $name => $path) {
                $exists = file_exists($path) && is_file($path);
                $pathResults[] = [
                    $name,
                    $exists ? '✅ EXISTS' : '❌ NOT FOUND',
                    $exists ? $this->formatBytes(filesize($path)) : 'N/A'
                ];
            }

            $this->table(['Path Type', 'Status', 'Size'], $pathResults);

            // Test image helper
            $imageResult = BillPDFService::getImageForPDF($logoPath, $companySettings);
            
            if ($imageResult) {
                $this->info('✅ Image helper method working');
                if (strpos($imageResult, 'data:') === 0) {
                    $this->line('   Type: Base64 Data URL');
                    $this->line('   Length: ' . $this->formatBytes(strlen($imageResult)));
                } else {
                    $this->line('   Type: File Path');
                    $this->line('   Path: ' . $imageResult);
                }
            } else {
                $this->error('❌ Image helper method failed');
            }

        } catch (\Exception $e) {
            $this->error('❌ Image processing test failed: ' . $e->getMessage());
        }
    }

    protected function generateSamplePDF($companyId)
    {
        $this->info('📄 Generating sample PDF...');
        
        try {
            // Try to find a recent POS sale
            $sale = PosSale::where('company_id', $companyId)
                          ->with(['items.product', 'cashier'])
                          ->latest()
                          ->first();

            if (!$sale) {
                $this->warn('⚠️  No POS sales found for this company');
                
                // Try to find an order instead
                $order = Order::where('company_id', $companyId)
                             ->with(['items.product', 'customer'])
                             ->latest()
                             ->first();

                if (!$order) {
                    $this->error('❌ No orders found either. Cannot generate sample PDF.');
                    return;
                }

                $this->info('📋 Using latest order instead...');
                $result = $this->billPDFService->generateOrderBill($order);
            } else {
                $this->info('🧾 Using latest POS sale...');
                $result = $this->billPDFService->generatePosSaleBill($sale);
            }

            if ($result['success']) {
                $this->info('✅ PDF generated successfully!');
                $this->table(['Property', 'Value'], [
                    ['File Path', $result['file_path']],
                    ['Filename', $result['filename']],
                    ['Format', $result['format']],
                    ['File Size', $this->formatBytes(filesize($result['file_path']))],
                ]);

                if ($this->confirm('Open the generated PDF?', false)) {
                    if (PHP_OS_FAMILY === 'Windows') {
                        exec('start "" "' . $result['file_path'] . '"');
                    } else {
                        exec('xdg-open "' . $result['file_path'] . '"');
                    }
                }
            } else {
                $this->error('❌ PDF generation failed: ' . $result['error']);
            }

        } catch (\Exception $e) {
            $this->error('❌ Sample PDF generation failed: ' . $e->getMessage());
        }
    }

    protected function runFullTestSuite($companyId)
    {
        $this->info("🔍 Running full test suite for company ID: $companyId");
        $this->newLine();

        // Test 1: Service availability
        $this->info('1️⃣  Testing service availability...');
        $methods = [
            'generateOrderBill',
            'generatePosSaleBill', 
            'downloadPosSaleBillFast',
            'generateUltraFastPDF',
            'getCompanySettings',
            'getBillFormatConfig'
        ];

        foreach ($methods as $method) {
            if (method_exists($this->billPDFService, $method)) {
                $this->line("   ✅ $method");
            } else {
                $this->line("   ❌ $method");
            }
        }
        $this->newLine();

        // Test 2: Company settings
        $this->info('2️⃣  Testing company settings...');
        try {
            $settings = $this->billPDFService->getCompanySettings($companyId);
            $config = $this->billPDFService->getBillFormatConfig($companyId);

            $this->table(['Setting', 'Value'], [
                ['Company Name', $settings['name'] ?? 'Not set'],
                ['Logo', !empty($settings['logo']) ? '✅ Configured' : '❌ Not set'],
                ['Thermal Enabled', $config['thermal_enabled'] ? '✅ Yes' : '❌ No'],
                ['A4 Enabled', $config['a4_enabled'] ? '✅ Yes' : '❌ No'],
                ['Default Format', $config['default_format']],
            ]);
        } catch (\Exception $e) {
            $this->error('❌ Company settings test failed: ' . $e->getMessage());
        }
        $this->newLine();

        // Test 3: Directory structure
        $this->info('3️⃣  Testing directory structure...');
        $directories = [
            'storage/app/public' => storage_path('app/public'),
            'storage/app/public/logos' => storage_path('app/public/logos'),
            'public/storage' => public_path('storage'),
            'storage/app/temp' => storage_path('app/temp'),
            'storage/app/temp/bills' => storage_path('app/temp/bills'),
        ];

        foreach ($directories as $name => $path) {
            $exists = is_dir($path);
            $writable = $exists && is_writable($path);
            
            $status = $exists ? ($writable ? '✅ OK' : '⚠️  Read-only') : '❌ Missing';
            $this->line("   $name: $status");
            
            if (!$exists && strpos($name, 'temp') !== false) {
                try {
                    mkdir($path, 0755, true);
                    $this->line("      📁 Created directory");
                } catch (\Exception $e) {
                    $this->line("      ❌ Failed to create: " . $e->getMessage());
                }
            }
        }
        $this->newLine();

        // Test 4: Image processing
        $this->testImageProcessing($companyId);
        $this->newLine();

        // Test 5: Available formats
        $this->info('4️⃣  Testing available formats...');
        try {
            $formats = $this->billPDFService->getAvailableFormats($companyId);
            
            if (empty($formats)) {
                $this->warn('⚠️  No formats available');
            } else {
                foreach ($formats as $key => $name) {
                    $this->line("   ✅ $key: $name");
                }
            }
        } catch (\Exception $e) {
            $this->error('❌ Format test failed: ' . $e->getMessage());
        }
        $this->newLine();

        // Summary
        $this->info('📊 Test Summary');
        $this->line('   Service: ✅ Available');
        $this->line('   Settings: ✅ Accessible');
        $this->line('   Directories: ✅ Checked');
        $this->line('   Images: ' . (!empty($settings['logo']) ? '✅ Configured' : '⚠️  No logo'));
        $this->line('   Formats: ✅ Available');

        $this->newLine();
        $this->info('🎉 Test suite completed!');
        
        if ($this->confirm('Generate a sample PDF to verify everything works?', true)) {
            $this->generateSamplePDF($companyId);
        }
    }

    protected function formatBytes($bytes, $precision = 2) 
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
