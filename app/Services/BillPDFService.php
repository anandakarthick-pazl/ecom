<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PosSale;
use App\Models\AppSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

class BillPDFService
{
    const FORMAT_THERMAL = 'thermal';
    const FORMAT_A4_SHEET = 'a4_sheet';
    
    // Cache company settings to avoid repeated database queries
    private static $companySettingsCache = [];
    private static $billConfigCache = [];

    /**
     * Generate PDF for order based on configured format
     */
    public function generateOrderBill(Order $order, $customFormat = null)
    {
        $startTime = microtime(true);
        
        try {
            // Load order with relationships efficiently
            if (!$order->relationLoaded('items')) {
                $order->load(['items.product', 'customer']);
            }
            
            // Get company settings with caching
            $companySettings = $this->getCompanySettingsCache($order->company_id);
            
            // Fix image paths for PDF generation
            $companySettings = $this->processCompanyImagesForPDF($companySettings);
            
            // Determine format to use based on admin configuration
            $format = $customFormat ?? $this->getConfiguredBillFormat($order->company_id);
            
            Log::info('Generating order bill', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'format' => $format,
                'company_id' => $order->company_id,
                'items_count' => $order->items->count()
            ]);
            
            // Generate PDF based on format with timeout protection
            $pdf = $this->generatePDFByFormatSafe($order, $companySettings, $format, 'order');
            
            // Generate filename
            $filename = $this->generateFilename($order, $format);
            $filePath = $this->getTempFilePath($filename);
            
            // Ensure directory exists
            $this->ensureDirectoryExists(dirname($filePath));
            
            // Save PDF with memory optimization
            $pdfOutput = $pdf->output();
            file_put_contents($filePath, $pdfOutput);
            
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            
            Log::info('Order bill generated successfully', [
                'order_id' => $order->id,
                'format' => $format,
                'execution_time' => round($executionTime, 2) . 's',
                'file_size' => $this->formatBytes(filesize($filePath))
            ]);
            
            return [
                'success' => true,
                'file_path' => $filePath,
                'filename' => $filename,
                'format' => $format,
                'message' => 'Order bill PDF generated successfully'
            ];
            
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            
            Log::error('Failed to generate order bill PDF', [
                'order_id' => $order->id ?? null,
                'error' => $e->getMessage(),
                'execution_time' => round($executionTime, 2) . 's',
                'memory_used' => $this->formatBytes(memory_get_peak_usage(true)),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate POS sale bill based on configured format
     */
    public function generatePosSaleBill(PosSale $sale, $customFormat = null)
    {
        $startTime = microtime(true);
        
        try {
            // Load sale with relationships efficiently
            if (!$sale->relationLoaded('items')) {
                $sale->load(['items.product', 'cashier']);
            }
            
            // Get company settings with caching
            $companySettings = $this->getCompanySettingsCache($sale->company_id);
            
            // Fix image paths for PDF generation
            $companySettings = $this->processCompanyImagesForPDF($companySettings);
            
            // Determine format to use based on admin configuration
            $format = $customFormat ?? $this->getConfiguredBillFormat($sale->company_id);
            
            Log::info('Generating POS sale bill', [
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'format' => $format,
                'company_id' => $sale->company_id,
                'items_count' => $sale->items->count()
            ]);
            
            // Generate PDF based on format with timeout protection
            $pdf = $this->generatePDFByFormatSafe($sale, $companySettings, $format, 'pos');
            
            // Generate filename
            $filename = $this->generatePosFilename($sale, $format);
            $filePath = $this->getTempFilePath($filename);
            
            // Ensure directory exists
            $this->ensureDirectoryExists(dirname($filePath));
            
            // Save PDF with memory optimization
            $pdfOutput = $pdf->output();
            file_put_contents($filePath, $pdfOutput);
            
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            
            Log::info('POS bill generated successfully', [
                'sale_id' => $sale->id,
                'format' => $format,
                'execution_time' => round($executionTime, 2) . 's',
                'file_size' => $this->formatBytes(filesize($filePath))
            ]);
            
            return [
                'success' => true,
                'file_path' => $filePath,
                'filename' => $filename,
                'format' => $format,
                'message' => 'POS bill PDF generated successfully'
            ];
            
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            
            Log::error('Failed to generate POS bill PDF', [
                'sale_id' => $sale->id ?? null,
                'error' => $e->getMessage(),
                'execution_time' => round($executionTime, 2) . 's',
                'memory_used' => $this->formatBytes(memory_get_peak_usage(true)),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process company images for PDF generation
     * Converts images to base64 data URLs or absolute paths for better PDF compatibility
     */
    protected function processCompanyImagesForPDF($companySettings)
    {
        if (isset($companySettings['logo']) && !empty($companySettings['logo'])) {
            $logoPath = $companySettings['logo'];
            
            // Try different paths to find the logo
            $possiblePaths = [
                public_path('storage/' . $logoPath),
                storage_path('app/public/' . $logoPath),
                public_path($logoPath),
                storage_path('app/' . $logoPath)
            ];
            
            $foundPath = null;
            foreach ($possiblePaths as $path) {
                if (File::exists($path) && is_file($path)) {
                    $foundPath = $path;
                    break;
                }
            }
            
            if ($foundPath) {
                try {
                    // Get image info
                    $imageInfo = getimagesize($foundPath);
                    if ($imageInfo !== false) {
                        $mimeType = $imageInfo['mime'];
                        
                        // Check if image is supported
                        if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
                            // Convert to base64 data URL for better PDF compatibility
                            $imageData = File::get($foundPath);
                            $base64 = base64_encode($imageData);
                            $companySettings['logo_data_url'] = "data:{$mimeType};base64,{$base64}";
                            $companySettings['logo_absolute_path'] = $foundPath;
                            
                            Log::debug('Logo processed for PDF', [
                                'original_path' => $logoPath,
                                'found_path' => $foundPath,
                                'mime_type' => $mimeType,
                                'size' => $this->formatBytes(strlen($imageData))
                            ]);
                        } else {
                            Log::warning('Unsupported image format for logo', [
                                'path' => $foundPath,
                                'mime_type' => $mimeType
                            ]);
                            $companySettings['logo'] = null;
                        }
                    } else {
                        Log::warning('Invalid image file for logo', ['path' => $foundPath]);
                        $companySettings['logo'] = null;
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to process logo image', [
                        'path' => $foundPath,
                        'error' => $e->getMessage()
                    ]);
                    $companySettings['logo'] = null;
                }
            } else {
                Log::warning('Logo file not found', [
                    'original_path' => $logoPath,
                    'searched_paths' => $possiblePaths
                ]);
                $companySettings['logo'] = null;
            }
        }
        
        return $companySettings;
    }

    /**
     * Get image helper for Blade templates
     * Returns the best available image source for PDF generation
     */
    public static function getImageForPDF($imagePath, $companySettings = null)
    {
        if (empty($imagePath)) {
            return null;
        }
        
        // If this is a company logo and we have processed data
        if ($companySettings && isset($companySettings['logo']) && $companySettings['logo'] === $imagePath) {
            if (isset($companySettings['logo_data_url'])) {
                return $companySettings['logo_data_url'];
            }
            if (isset($companySettings['logo_absolute_path'])) {
                return $companySettings['logo_absolute_path'];
            }
        }
        
        // Try to find the image in various locations
        $possiblePaths = [
            public_path('storage/' . $imagePath),
            storage_path('app/public/' . $imagePath),
            public_path($imagePath),
            storage_path('app/' . $imagePath)
        ];
        
        foreach ($possiblePaths as $path) {
            if (File::exists($path) && is_file($path)) {
                return $path;
            }
        }
        
        return null;
    }

    /**
     * Generate bill PDF and return as response
     */
    public function downloadOrderBill(Order $order, $customFormat = null)
    {
        try {
            $result = $this->generateOrderBill($order, $customFormat);
            
            if (!$result['success']) {
                throw new \Exception($result['error']);
            }
            
            return response()->download($result['file_path'], $result['filename'], [
                'Content-Type' => 'application/pdf'
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            throw new \Exception('Failed to generate order PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate POS sale bill PDF and return as response
     */
    public function downloadPosSaleBill(PosSale $sale, $customFormat = null)
    {
        try {
            $result = $this->generatePosSaleBill($sale, $customFormat);
            
            if (!$result['success']) {
                throw new \Exception($result['error']);
            }
            
            return response()->download($result['file_path'], $result['filename'], [
                'Content-Type' => 'application/pdf'
            ])->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            throw new \Exception('Failed to generate POS bill PDF: ' . $e->getMessage());
        }
    }

    /**
     * Fast POS sale bill generation with optimizations
     */
    public function downloadPosSaleBillFast(PosSale $sale, $customFormat = null)
    {
        $startTime = microtime(true);
        
        try {
            // Pre-check requirements
            if (!$sale->company_id) {
                throw new \Exception('Sale missing company information');
            }
            
            // Load relationships efficiently if not already loaded
            if (!$sale->relationLoaded('items') || !$sale->relationLoaded('cashier')) {
                $sale->load(['items.product', 'cashier']);
            }
            
            // Get settings with aggressive caching
            $companySettings = $this->getCompanySettingsCache($sale->company_id);
            
            // Process images for PDF
            $companySettings = $this->processCompanyImagesForPDF($companySettings);
            
            $format = $customFormat ?? $this->getConfiguredBillFormatCache($sale->company_id);
            
            Log::debug('Fast POS bill generation started', [
                'sale_id' => $sale->id,
                'format' => $format,
                'items_count' => $sale->items->count()
            ]);
            
            // Create view data with minimal processing
            $viewData = [
                'sale' => $sale,
                'globalCompany' => (object) $companySettings
            ];

            // Generate PDF with optimized settings
            $pdf = $this->createOptimizedPDF($viewData, $format, 'pos');
            
            // Generate filename
            $filename = $this->generatePosFilename($sale, $format);
            
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            
            Log::info('Fast POS bill generated', [
                'sale_id' => $sale->id,
                'format' => $format,
                'execution_time' => round($executionTime, 2) . 's',
                'memory_used' => $this->formatBytes(memory_get_peak_usage(true))
            ]);
            
            // Stream directly without saving to disk
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'no-cache'
            ]);
            
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            
            Log::error('Fast POS bill generation failed', [
                'sale_id' => $sale->id ?? null,
                'error' => $e->getMessage(),
                'execution_time' => round($executionTime, 2) . 's',
                'memory_used' => $this->formatBytes(memory_get_peak_usage(true))
            ]);
            
            throw $e;
        }
    }

    /**
     * Create optimized PDF with minimal resource usage
     */
    protected function createOptimizedPDF($viewData, $format, $type = 'pos')
    {
        try {
            // Select appropriate view based on format
            if ($type === 'pos') {
                $viewName = ($format === self::FORMAT_THERMAL) ? 'admin.pos.receipt-pdf' : 'admin.pos.receipt-a4';
            } else {
                $viewName = ($format === self::FORMAT_THERMAL) ? 'admin.orders.bill-thermal' : 'admin.orders.bill-pdf';
            }
            
            // Create PDF with optimized settings
            $pdf = Pdf::loadView($viewName, $viewData);
            
            // Set paper size based on format
            if ($format === self::FORMAT_THERMAL) {
                $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm width
            } else {
                $pdf->setPaper('A4', 'portrait');
            }
            
            // Optimize PDF options for speed
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false, // Disable remote resources for speed
                'defaultFont' => $format === self::FORMAT_THERMAL ? 'monospace' : 'sans-serif',
                'dpi' => 72, // Lower DPI for faster generation
                'defaultPaperSize' => $format === self::FORMAT_THERMAL ? 'custom' : 'A4',
                'isPhpEnabled' => false, // Disable PHP in templates for security/speed
                'isFontSubsettingEnabled' => false, // Disable font subsetting for speed
                'isJavascriptEnabled' => false, // Disable JS for speed
                'debugKeepTemp' => false
            ]);
            
            return $pdf;
            
        } catch (\Exception $e) {
            Log::error('PDF creation failed', [
                'view' => $viewName ?? 'unknown',
                'format' => $format,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get configured bill format for company based on admin settings with caching
     */
    protected function getConfiguredBillFormatCache($companyId)
    {
        $cacheKey = "bill_format_config_{$companyId}";
        
        if (isset(self::$billConfigCache[$cacheKey])) {
            return self::$billConfigCache[$cacheKey];
        }
        
        $format = Cache::remember($cacheKey, 300, function () use ($companyId) { // 5 minute cache
            return $this->getConfiguredBillFormat($companyId);
        });
        
        self::$billConfigCache[$cacheKey] = $format;
        return $format;
    }

    /**
     * Get configured bill format for company based on admin settings
     * NEW LOGIC: Default format setting takes precedence over individual enables
     */
    protected function getConfiguredBillFormat($companyId)
    {
        try {
            // Get format settings from admin configuration
            $thermalEnabled = AppSetting::getForTenant('thermal_printer_enabled', $companyId) ?? false;
            $a4SheetEnabled = AppSetting::getForTenant('a4_sheet_enabled', $companyId) ?? true;
            $defaultFormat = AppSetting::getForTenant('default_bill_format', $companyId) ?? self::FORMAT_A4_SHEET;

            // Convert string boolean values to actual boolean
            if (is_string($thermalEnabled)) {
                $thermalEnabled = filter_var($thermalEnabled, FILTER_VALIDATE_BOOLEAN);
            }
            if (is_string($a4SheetEnabled)) {
                $a4SheetEnabled = filter_var($a4SheetEnabled, FILTER_VALIDATE_BOOLEAN);
            }

            // NEW LOGIC: Default format setting takes precedence
            // If default format is set to A4, both online orders and POS orders use A4
            if ($defaultFormat === self::FORMAT_A4_SHEET) {
                Log::debug('Using A4 format based on default setting', [
                    'company_id' => $companyId,
                    'default_format' => $defaultFormat
                ]);
                return self::FORMAT_A4_SHEET;
            }
            
            // If default format is set to thermal, both online orders and POS orders use thermal
            if ($defaultFormat === self::FORMAT_THERMAL) {
                Log::debug('Using thermal format based on default setting', [
                    'company_id' => $companyId,
                    'default_format' => $defaultFormat
                ]);
                return self::FORMAT_THERMAL;
            }
            
            // Fallback: if default format is invalid, use A4
            Log::warning('Invalid default format, falling back to A4', [
                'company_id' => $companyId,
                'default_format' => $defaultFormat
            ]);
            return self::FORMAT_A4_SHEET;
            
        } catch (\Exception $e) {
            Log::error('Error getting bill format configuration', [
                'company_id' => $companyId,
                'error' => $e->getMessage()
            ]);
            return self::FORMAT_A4_SHEET; // Safe fallback
        }
    }

    /**
     * Generate PDF based on format for both orders and POS sales with safety checks
     */
    protected function generatePDFByFormatSafe($model, $companySettings, $format, $type = 'order')
    {
        try {
            $viewData = [
                'company' => $companySettings
            ];

            if ($type === 'order') {
                $viewData['order'] = $model;
                $viewName = ($format === self::FORMAT_THERMAL) ? 'admin.orders.bill-thermal' : 'admin.orders.invoice-pdf';
            } else {
                // POS sale
                $viewData['sale'] = $model;
                $viewData['globalCompany'] = (object) $companySettings; // For compatibility with existing POS templates
                $viewName = ($format === self::FORMAT_THERMAL) ? 'admin.pos.receipt-pdf' : 'admin.pos.receipt-a4';
            }
            // echo $viewName;exit;
            // Check if view exists
            if (!View::exists($viewName)) {
                throw new \Exception("View template not found: {$viewName}");
            }
            
            // Generate PDF with timeout protection
            $pdf = Pdf::loadView($viewName, $viewData);
            // echo $pdf;exit;
            // Set paper size based on format
            if ($format === self::FORMAT_THERMAL) {
                $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm width thermal paper
            } else {
                $pdf->setPaper('A4', 'portrait');
            }
            
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false, // Disable remote resources to avoid hanging
                'defaultFont' => $format === self::FORMAT_THERMAL ? 'DejaVu Sans' : 'DejaVu Sans', // Unicode support for currency
                'dpi' => 96,
                'isPhpEnabled' => false,
                'isJavascriptEnabled' => false,
                'debugKeepTemp' => false,
                'defaultMediaType' => 'print',
                'isFontSubsettingEnabled' => true,
                'fontHeightRatio' => 1.1
            ]);
            
            return $pdf;
            
        } catch (\Exception $e) {
            Log::error('PDF generation by format failed', [
                'model_type' => $type,
                'model_id' => $model->id ?? null,
                'format' => $format,
                'view_name' => $viewName ?? 'unknown',
                'error' => $e->getMessage(),
                'company_name' => $companySettings['name'] ?? 'Unknown'
            ]);
            throw $e;
        }
    }

    /**
     * Generate filename for the bill
     */
    protected function generateFilename($model, $format = null)
    {
        $prefix = ($format === self::FORMAT_THERMAL) ? 'thermal_bill_' : 'bill_';
        
        if ($model instanceof Order) {
            return $prefix . $model->order_number . '_' . date('Y-m-d_H-i-s') . '.pdf';
        } else {
            return $prefix . $model->invoice_number . '_' . date('Y-m-d_H-i-s') . '.pdf';
        }
    }

    /**
     * Generate POS filename based on format
     */
    protected function generatePosFilename(PosSale $sale, $format)
    {
        $prefix = ($format === self::FORMAT_THERMAL) ? 'thermal_receipt_' : 'receipt_';
        return $prefix . $sale->invoice_number . '_' . date('Y-m-d_H-i-s') . '.pdf';
    }

    /**
     * Check if thermal printer is enabled for company
     */
    public function isThermalEnabled($companyId)
    {
        $value = AppSetting::getForTenant('thermal_printer_enabled', $companyId) ?? false;
        return is_string($value) ? filter_var($value, FILTER_VALIDATE_BOOLEAN) : (bool) $value;
    }

    /**
     * Check if A4 sheet is enabled for company
     */
    public function isA4SheetEnabled($companyId)
    {
        $value = AppSetting::getForTenant('a4_sheet_enabled', $companyId) ?? true;
        return is_string($value) ? filter_var($value, FILTER_VALIDATE_BOOLEAN) : (bool) $value;
    }

    /**
     * Get available formats for a company
     * NEW LOGIC: Only show the format that matches the default setting
     */
    public function getAvailableFormats($companyId)
    {
        $formats = [];
        
        // Get the default format setting
        $defaultFormat = AppSetting::getForTenant('default_bill_format', $companyId) ?? self::FORMAT_A4_SHEET;
        
        // Only show the format that matches the default setting
        if ($defaultFormat === self::FORMAT_THERMAL) {
            $formats[self::FORMAT_THERMAL] = 'Thermal Printer (80mm)';
        } else {
            // Default to A4 for any other value or if A4 is explicitly set
            $formats[self::FORMAT_A4_SHEET] = 'A4 Sheet PDF';
        }
        
        Log::debug('Available formats determined by default setting', [
            'company_id' => $companyId,
            'default_format' => $defaultFormat,
            'available_formats' => array_keys($formats)
        ]);
        
        return $formats;
    }

    /**
     * Get current bill format configuration for company
     */
    public function getBillFormatConfig($companyId)
    {
        return [
            'thermal_enabled' => $this->isThermalEnabled($companyId),
            'a4_enabled' => $this->isA4SheetEnabled($companyId),
            'default_format' => AppSetting::getForTenant('default_bill_format', $companyId) ?? self::FORMAT_A4_SHEET,
            'thermal_width' => AppSetting::getForTenant('thermal_printer_width', $companyId) ?? 80,
            'thermal_auto_cut' => AppSetting::getForTenant('thermal_printer_auto_cut', $companyId) ?? true,
            'a4_orientation' => AppSetting::getForTenant('a4_sheet_orientation', $companyId) ?? 'portrait',
            'logo_enabled' => AppSetting::getForTenant('bill_logo_enabled', $companyId) ?? true,
            'company_info_enabled' => AppSetting::getForTenant('bill_company_info_enabled', $companyId) ?? true
        ];
    }

    /**
     * Get company settings for the bill with caching
     */
    public function getCompanySettings($companyId)
    {
        return $this->getCompanySettingsCache($companyId);
    }

    /**
     * Get company settings with caching to avoid repeated database queries
     */
    protected function getCompanySettingsCache($companyId)
    {
        $cacheKey = "company_settings_{$companyId}";
        
        if (isset(self::$companySettingsCache[$cacheKey])) {
            return self::$companySettingsCache[$cacheKey];
        }
        
        $settings = Cache::remember($cacheKey, 600, function () use ($companyId) { // 10 minute cache
            // Try to get company from database first
            $company = \App\Models\SuperAdmin\Company::find($companyId);
            
            if ($company) {
                // Get data directly from company model
                $companyData = [
                    'id' => $company->id,
                    'name' => $company->name ?? $company->company_name ?? 'Your Company',
                    'address' => $company->address ?? $company->company_address ?? '',
                    'phone' => $company->phone ?? $company->company_phone ?? '',
                    'email' => $company->email ?? $company->company_email ?? '',
                    'website' => $company->website ?? $company->company_website ?? '',
                    'gst_number' => $company->gst_number ?? $company->gst_no ?? '',
                    'logo' => $company->logo ?? $company->company_logo ?? '',
                    'currency' => $company->currency ?? 'RS',
                    'currency_code' => $company->currency_code ?? 'INR',
                    'tax_name' => $company->tax_name ?? 'GST',
                    'tax_rate' => $company->tax_rate ?? 18,
                    'city' => $company->city ?? '',
                    'state' => $company->state ?? '',
                    'postal_code' => $company->postal_code ?? $company->pincode ?? $company->zip ?? '',
                    'country' => $company->country ?? 'India',
                    'primary_color' => $company->primary_color ?? '#2d5016',
                    'secondary_color' => $company->secondary_color ?? '#4a7c28',
                    'status' => $company->status ?? 'active'
                ];
                
                // Build complete address if needed
                if (empty($companyData['address'])) {
                    $addressParts = array_filter([
                        $companyData['city'],
                        $companyData['state'],
                        $companyData['postal_code']
                    ]);
                    $companyData['address'] = implode(', ', $addressParts);
                }
                
                return $companyData;
            }
            
            // Fallback to AppSetting method if company not found
            return [
                'id' => $companyId,
                'name' => AppSetting::getForTenant('company_name', $companyId) ?? 'Your Company',
                'address' => AppSetting::getForTenant('company_address', $companyId) ?? '',
                'phone' => AppSetting::getForTenant('company_phone', $companyId) ?? '',
                'email' => AppSetting::getForTenant('company_email', $companyId) ?? '',
                'website' => AppSetting::getForTenant('company_website', $companyId) ?? '',
                'gst_number' => AppSetting::getForTenant('company_gst_number', $companyId) ?? '',
                'logo' => AppSetting::getForTenant('company_logo', $companyId) ?? '',
                'currency' => AppSetting::getForTenant('currency', $companyId) ?? 'RS',
                'currency_code' => AppSetting::getForTenant('currency_code', $companyId) ?? 'INR',
                'tax_name' => AppSetting::getForTenant('tax_name', $companyId) ?? 'GST',
                'tax_rate' => AppSetting::getForTenant('tax_rate', $companyId) ?? 18,
                'city' => AppSetting::getForTenant('city', $companyId) ?? '',
                'state' => AppSetting::getForTenant('state', $companyId) ?? '',
                'postal_code' => AppSetting::getForTenant('postal_code', $companyId) ?? AppSetting::getForTenant('pincode', $companyId) ?? '',
                'country' => AppSetting::getForTenant('country', $companyId) ?? 'India',
                'primary_color' => AppSetting::getForTenant('primary_color', $companyId) ?? '#2d5016',
                'secondary_color' => AppSetting::getForTenant('secondary_color', $companyId) ?? '#4a7c28',
                'status' => 'active'
            ];
        });
        
        self::$companySettingsCache[$cacheKey] = $settings;
        return $settings;
    }

    /**
     * Get temp file path
     */
    protected function getTempFilePath($filename)
    {
        return storage_path('app/temp/bills/' . $filename);
    }

    /**
     * Ensure directory exists
     */
    protected function ensureDirectoryExists($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * Clean up old temporary files
     */
    public function cleanupTempFiles($olderThanHours = 24)
    {
        $tempDir = storage_path('app/temp/bills');
        
        if (!is_dir($tempDir)) {
            return;
        }
        
        $files = glob($tempDir . '/*');
        $cutoff = time() - ($olderThanHours * 3600);
        $cleaned = 0;
        
        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $cutoff) {
                unlink($file);
                $cleaned++;
            }
        }
        
        Log::info('Cleaned up temp bill files', ['files_removed' => $cleaned]);
    }

    /**
     * Validate bill format configuration
     */
    public function validateBillFormatConfig($companyId)
    {
        $thermalEnabled = $this->isThermalEnabled($companyId);
        $a4Enabled = $this->isA4SheetEnabled($companyId);
        
        if (!$thermalEnabled && !$a4Enabled) {
            return [
                'valid' => false,
                'message' => 'At least one bill format must be enabled.'
            ];
        }
        
        return [
            'valid' => true,
            'message' => 'Bill format configuration is valid.'
        ];
    }

    /**
     * Format bytes for logging
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Ultra-fast PDF generation with minimal database queries and memory usage
     */
    public function generateUltraFastPDF(PosSale $sale, $customFormat = null)
    {
        $startTime = microtime(true);
        
        try {
            // Pre-validate everything needed
            if (!$sale->company_id) {
                throw new \Exception('Sale missing company information');
            }
            
            // Get format with aggressive caching
            $format = $customFormat ?? $this->getConfiguredBillFormatCached($sale->company_id);
            
            // Use in-memory data structures - no additional DB queries
            $companySettings = $this->getCompanySettingsCache($sale->company_id);
            
            // Process images for PDF
            $companySettings = $this->processCompanyImagesForPDF($companySettings);
            
            Log::debug('Ultra-fast PDF generation started', [
                'sale_id' => $sale->id,
                'format' => $format,
                'memory_start' => $this->formatBytes(memory_get_usage(true))
            ]);
            
            // Create minimal view data
            $viewData = [
                'sale' => $sale,
                'globalCompany' => (object) $companySettings,
                'items' => $sale->items, // Use already loaded relationship
                'cashier' => $sale->cashier // Use already loaded relationship
            ];

            // Generate PDF with ultra-optimized settings
            $pdf = $this->createUltraOptimizedPDF($viewData, $format);
            
            // Generate filename
            $filename = $this->generatePosFilename($sale, $format);
            
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            
            Log::info('Ultra-fast PDF generated successfully', [
                'sale_id' => $sale->id,
                'format' => $format,
                'execution_time' => round($executionTime, 2) . 's',
                'memory_peak' => $this->formatBytes(memory_get_peak_usage(true))
            ]);
            
            // Stream directly to browser without file system
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'no-cache',
                'X-Generation-Method' => 'ultra-fast',
                'X-Execution-Time' => round($executionTime, 2) . 's'
            ]);
            
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            
            Log::error('Ultra-fast PDF generation failed', [
                'sale_id' => $sale->id ?? null,
                'error' => $e->getMessage(),
                'execution_time' => round($executionTime, 2) . 's',
                'memory_used' => $this->formatBytes(memory_get_peak_usage(true))
            ]);
            
            throw new \Exception('Ultra-fast PDF generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Create ultra-optimized PDF with minimal resource usage
     */
    protected function createUltraOptimizedPDF($viewData, $format, $type = 'pos')
    {
        try {
            // Select appropriate view based on format
            if ($type === 'pos') {
                $viewName = ($format === self::FORMAT_THERMAL) ? 'admin.pos.receipt-pdf' : 'admin.pos.receipt-a4';
            } else {
                $viewName = ($format === self::FORMAT_THERMAL) ? 'admin.orders.bill-thermal' : 'admin.orders.bill-pdf';
            }
            
            // Create PDF with maximum optimization
            $pdf = Pdf::loadView($viewName, $viewData);
            
            // Set paper size based on format
            if ($format === self::FORMAT_THERMAL) {
                $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm width
            } else {
                $pdf->setPaper('A4', 'portrait');
            }
            
            // Ultra-optimized PDF options for maximum speed
            $pdf->setOptions([
                'isHtml5ParserEnabled' => false, // Disable for speed
                'isRemoteEnabled' => false, // Critical: no external resources
                'defaultFont' => 'Arial', // Simple font
                'dpi' => 72, // Lowest acceptable DPI
                'defaultPaperSize' => $format === self::FORMAT_THERMAL ? 'custom' : 'A4',
                'isPhpEnabled' => false, // Security and speed
                'isFontSubsettingEnabled' => false, // Disable for speed
                'isJavascriptEnabled' => false, // Disable for speed
                'debugKeepTemp' => false,
                'chroot' => false, // Disable sandboxing for speed
                'logOutputFile' => false, // Disable logging
                'tempDir' => sys_get_temp_dir(), // Use system temp
                'fontDir' => storage_path('fonts'), // Local fonts only
                'fontCache' => storage_path('app/dompdf_font_cache'), // Enable font caching
                'isHtml5ParserEnabled' => true,
                'isCssFloatEnabled' => false, // Disable CSS float for speed
                'isImageDragAndDropEnabled' => false
            ]);
            
            return $pdf;
            
        } catch (\Exception $e) {
            Log::error('Ultra-optimized PDF creation failed', [
                'view' => $viewName ?? 'unknown',
                'format' => $format,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Enhanced caching for bill format configuration with longer TTL
     */
    public function getBillFormatConfigCached($companyId)
    {
        $cacheKey = "bill_format_config_v2_{$companyId}";
        
        if (isset(self::$billConfigCache[$cacheKey])) {
            return self::$billConfigCache[$cacheKey];
        }
        
        $config = Cache::remember($cacheKey, 600, function () use ($companyId) { // 10 minute cache
            return [
                'thermal_enabled' => $this->isThermalEnabled($companyId),
                'a4_enabled' => $this->isA4SheetEnabled($companyId),
                'default_format' => AppSetting::getForTenant('default_bill_format', $companyId) ?? self::FORMAT_A4_SHEET,
                'thermal_width' => AppSetting::getForTenant('thermal_printer_width', $companyId) ?? 80,
                'thermal_auto_cut' => AppSetting::getForTenant('thermal_printer_auto_cut', $companyId) ?? true,
                'a4_orientation' => AppSetting::getForTenant('a4_sheet_orientation', $companyId) ?? 'portrait',
                'logo_enabled' => AppSetting::getForTenant('bill_logo_enabled', $companyId) ?? true,
                'company_info_enabled' => AppSetting::getForTenant('bill_company_info_enabled', $companyId) ?? true
            ];
        });
        
        self::$billConfigCache[$cacheKey] = $config;
        return $config;
    }

    /**
     * Enhanced available formats with caching
     * NEW LOGIC: Only show the format that matches the default setting
     */
    public function getAvailableFormatsCached($companyId)
    {
        $cacheKey = "available_formats_v2_{$companyId}";
        
        static $formatCache = [];
        if (isset($formatCache[$cacheKey])) {
            return $formatCache[$cacheKey];
        }
        
        $formats = Cache::remember($cacheKey, 600, function () use ($companyId) {
            $formats = [];
            
            // Get the default format setting
            $defaultFormat = AppSetting::getForTenant('default_bill_format', $companyId) ?? self::FORMAT_A4_SHEET;
            
            // Only show the format that matches the default setting
            if ($defaultFormat === self::FORMAT_THERMAL) {
                $formats[self::FORMAT_THERMAL] = 'Thermal Printer (80mm)';
            } else {
                // Default to A4 for any other value or if A4 is explicitly set
                $formats[self::FORMAT_A4_SHEET] = 'A4 Sheet PDF';
            }
            
            return $formats;
        });
        
        $formatCache[$cacheKey] = $formats;
        return $formats;
    }

    /**
     * Cache warming for frequently accessed company settings
     */
    public function warmCompanyCache($companyId)
    {
        try {
            // Pre-warm all frequently accessed caches
            $this->getCompanySettingsCache($companyId);
            $this->getBillFormatConfigCached($companyId);
            $this->getAvailableFormatsCached($companyId);
            $this->getConfiguredBillFormatCached($companyId);
            
            Log::info('Company cache warmed successfully', ['company_id' => $companyId]);
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to warm company cache', [
                'company_id' => $companyId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Clear caches
     */
    public static function clearCache()
    {
        self::$companySettingsCache = [];
        self::$billConfigCache = [];
        Cache::flush(); // Clear Laravel cache as well
    }

    /**
     * Simple PDF generation method for backward compatibility
     * This method provides a simple interface for generating PDFs
     */
    public function generateSimplePDF($model, $companyData, $viewName, $paperSize = 'A4')
    {
        try {
            // Process images for PDF if company data is provided
            if (is_array($companyData)) {
                $companyData = $this->processCompanyImagesForPDF($companyData);
            }
            
            // Prepare view data
            $viewData = [];
            
            if ($model instanceof PosSale) {
                $viewData['sale'] = $model;
                $viewData['globalCompany'] = is_object($companyData) ? $companyData : (object)$companyData;
            } elseif ($model instanceof Order) {
                $viewData['order'] = $model;
                $viewData['company'] = is_object($companyData) ? $companyData : (object)$companyData;
            } else {
                throw new \Exception('Unsupported model type for PDF generation');
            }
            
            // Check if view exists
            if (!View::exists($viewName)) {
                throw new \Exception("View template not found: {$viewName}");
            }
            
            // Create PDF
            $pdf = Pdf::loadView($viewName, $viewData);
            
            // Set paper size
            if (is_array($paperSize)) {
                // Custom paper size (for thermal)
                $pdf->setPaper($paperSize, 'portrait');
            } else {
                // Standard paper size
                $pdf->setPaper($paperSize, 'portrait');
            }
            
            // Set optimized options
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'Arial',
                'dpi' => 96,
                'isPhpEnabled' => false,
                'isJavascriptEnabled' => false,
                'debugKeepTemp' => false
            ]);
            
            return $pdf;
            
        } catch (\Exception $e) {
            Log::error('Simple PDF generation failed', [
                'view' => $viewName,
                'paper_size' => $paperSize,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
