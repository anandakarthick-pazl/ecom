<?php

namespace App\Http\Controllers\Traits;

use App\Services\BillPDFService;
use App\Models\Order;
use App\Models\PosSale;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * PDF Generation Trait for Controllers
 * 
 * This trait provides easy-to-use methods for generating PDFs in your controllers.
 * Simply use this trait in your controller and call the methods as needed.
 * 
 * Usage:
 * use App\Http\Controllers\Traits\PDFGenerationTrait;
 * 
 * class YourController extends Controller {
 *     use PDFGenerationTrait;
 *     
 *     public function downloadReceipt($saleId) {
 *         return $this->downloadPosBill($saleId);
 *     }
 * }
 */
trait PDFGenerationTrait
{
    protected $billPDFService;

    /**
     * Get or create the BillPDFService instance
     */
    protected function getBillPDFService()
    {
        if (!$this->billPDFService) {
            $this->billPDFService = app(BillPDFService::class);
        }
        return $this->billPDFService;
    }

    /**
     * Download POS sale bill as PDF
     * 
     * @param int|PosSale $sale Sale ID or PosSale model
     * @param string|null $format Format: 'thermal' or 'a4_sheet' or null for auto
     * @param bool $fast Use fast generation method (streams directly)
     * @return Response
     */
    protected function downloadPosBill($sale, $format = null, $fast = true)
    {
        try {
            // Load sale if ID provided
            if (is_numeric($sale)) {
                $sale = PosSale::with(['items.product', 'cashier'])->findOrFail($sale);
            }

            // Verify user has access to this sale
            $this->authorizePosSale($sale);

            $service = $this->getBillPDFService();

            if ($fast) {
                return $service->downloadPosSaleBillFast($sale, $format);
            } else {
                return $service->downloadPosSaleBill($sale, $format);
            }

        } catch (\Exception $e) {
            Log::error('Failed to download POS bill', [
                'sale_id' => is_object($sale) ? $sale->id : $sale,
                'format' => $format,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return $this->handlePDFError($e, 'POS bill');
        }
    }

    /**
     * Download order bill as PDF
     * 
     * @param int|Order $order Order ID or Order model
     * @param string|null $format Format: 'thermal' or 'a4_sheet' or null for auto
     * @return Response
     */
    protected function downloadOrderBill($order, $format = null)
    {
        try {
            // Load order if ID provided
            if (is_numeric($order)) {
                $order = Order::with(['items.product', 'customer'])->findOrFail($order);
            }

            // Verify user has access to this order
            $this->authorizeOrder($order);

            $service = $this->getBillPDFService();
            return $service->downloadOrderBill($order, $format);

        } catch (\Exception $e) {
            Log::error('Failed to download order bill', [
                'order_id' => is_object($order) ? $order->id : $order,
                'format' => $format,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return $this->handlePDFError($e, 'order bill');
        }
    }

    /**
     * Generate POS sale bill and return file info (doesn't download)
     * 
     * @param int|PosSale $sale Sale ID or PosSale model
     * @param string|null $format Format: 'thermal' or 'a4_sheet' or null for auto
     * @return array Result array with success status and file info
     */
    protected function generatePosBill($sale, $format = null)
    {
        try {
            // Load sale if ID provided
            if (is_numeric($sale)) {
                $sale = PosSale::with(['items.product', 'cashier'])->findOrFail($sale);
            }

            // Verify user has access to this sale
            $this->authorizePosSale($sale);

            $service = $this->getBillPDFService();
            return $service->generatePosSaleBill($sale, $format);

        } catch (\Exception $e) {
            Log::error('Failed to generate POS bill', [
                'sale_id' => is_object($sale) ? $sale->id : $sale,
                'format' => $format,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate order bill and return file info (doesn't download)
     * 
     * @param int|Order $order Order ID or Order model
     * @param string|null $format Format: 'thermal' or 'a4_sheet' or null for auto
     * @return array Result array with success status and file info
     */
    protected function generateOrderBill($order, $format = null)
    {
        try {
            // Load order if ID provided
            if (is_numeric($order)) {
                $order = Order::with(['items.product', 'customer'])->findOrFail($order);
            }

            // Verify user has access to this order
            $this->authorizeOrder($order);

            $service = $this->getBillPDFService();
            return $service->generateOrderBill($order, $format);

        } catch (\Exception $e) {
            Log::error('Failed to generate order bill', [
                'order_id' => is_object($order) ? $order->id : $order,
                'format' => $format,
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get available PDF formats for a company
     * 
     * @param int|null $companyId Company ID (uses current user's company if null)
     * @return array Available formats
     */
    protected function getAvailablePDFFormats($companyId = null)
    {
        if (!$companyId) {
            $companyId = $this->getCurrentCompanyId();
        }

        $service = $this->getBillPDFService();
        return $service->getAvailableFormats($companyId);
    }

    /**
     * Get PDF format configuration for a company
     * 
     * @param int|null $companyId Company ID (uses current user's company if null)
     * @return array Format configuration
     */
    protected function getPDFFormatConfig($companyId = null)
    {
        if (!$companyId) {
            $companyId = $this->getCurrentCompanyId();
        }

        $service = $this->getBillPDFService();
        return $service->getBillFormatConfig($companyId);
    }

    /**
     * Warm up PDF caches for better performance
     * 
     * @param int|null $companyId Company ID (uses current user's company if null)
     * @return bool Success status
     */
    protected function warmPDFCaches($companyId = null)
    {
        if (!$companyId) {
            $companyId = $this->getCurrentCompanyId();
        }

        $service = $this->getBillPDFService();
        return $service->warmCompanyCache($companyId);
    }

    /**
     * Clear PDF caches
     * 
     * @return void
     */
    protected function clearPDFCaches()
    {
        BillPDFService::clearCache();
    }

    /**
     * Validate PDF format
     * 
     * @param string $format Format to validate
     * @param int|null $companyId Company ID
     * @return bool
     */
    protected function isValidPDFFormat($format, $companyId = null)
    {
        if (!$companyId) {
            $companyId = $this->getCurrentCompanyId();
        }

        $availableFormats = $this->getAvailablePDFFormats($companyId);
        return isset($availableFormats[$format]);
    }

    /**
     * Get the optimal PDF format for a company
     * 
     * @param int|null $companyId Company ID
     * @param string $type Type hint: 'pos' or 'order'
     * @return string Optimal format
     */
    protected function getOptimalPDFFormat($companyId = null, $type = 'pos')
    {
        if (!$companyId) {
            $companyId = $this->getCurrentCompanyId();
        }

        $config = $this->getPDFFormatConfig($companyId);
        
        // For POS, prefer thermal if available
        if ($type === 'pos' && $config['thermal_enabled']) {
            return 'thermal';
        }
        
        // For orders, prefer A4
        if ($config['a4_enabled']) {
            return 'a4_sheet';
        }
        
        // Fallback to thermal if only that's available
        if ($config['thermal_enabled']) {
            return 'thermal';
        }
        
        // Final fallback
        return 'a4_sheet';
    }

    /**
     * Handle PDF generation errors
     * 
     * @param \Exception $e The exception
     * @param string $type Type of PDF (for error message)
     * @return Response
     */
    protected function handlePDFError(\Exception $e, $type = 'PDF')
    {
        $message = "Failed to generate {$type}. Please try again.";
        
        // Different responses based on request type
        if (request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }

        if (request()->ajax()) {
            return response($message, 500);
        }

        // For regular requests, redirect back with error
        return redirect()->back()->with('error', $message);
    }

    /**
     * Authorize user access to POS sale
     * Override this method in your controller to implement custom authorization
     * 
     * @param PosSale $sale
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizePosSale(PosSale $sale)
    {
        // Check if user belongs to the same company
        $userCompanyId = $this->getCurrentCompanyId();
        
        if ($sale->company_id !== $userCompanyId) {
            abort(403, 'You do not have permission to access this sale.');
        }

        // Additional authorization can be added here
        // Example: Check if user has 'view-pos-sales' permission
        // $this->authorize('view', $sale);
    }

    /**
     * Authorize user access to order
     * Override this method in your controller to implement custom authorization
     * 
     * @param Order $order
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function authorizeOrder(Order $order)
    {
        // Check if user belongs to the same company
        $userCompanyId = $this->getCurrentCompanyId();
        
        if ($order->company_id !== $userCompanyId) {
            abort(403, 'You do not have permission to access this order.');
        }

        // Additional authorization can be added here
        // Example: Check if user has 'view-orders' permission
        // $this->authorize('view', $order);
    }

    /**
     * Get current user's company ID
     * Override this method if you have a different way to get company ID
     * 
     * @return int
     */
    protected function getCurrentCompanyId()
    {
        // Implement your logic to get current company ID
        // This is just a common pattern - adjust as needed
        
        if (auth()->check()) {
            return auth()->user()->company_id ?? 1;
        }
        
        return 1; // Default fallback
    }

    /**
     * Batch generate PDFs for multiple sales/orders
     * 
     * @param array $items Array of sale/order IDs or models
     * @param string $type Type: 'pos' or 'order'
     * @param string|null $format PDF format
     * @return array Results array
     */
    protected function batchGeneratePDFs(array $items, $type = 'pos', $format = null)
    {
        $results = [];
        $service = $this->getBillPDFService();

        foreach ($items as $item) {
            try {
                if ($type === 'pos') {
                    $result = $this->generatePosBill($item, $format);
                } else {
                    $result = $this->generateOrderBill($item, $format);
                }
                
                $results[] = $result;
                
            } catch (\Exception $e) {
                $results[] = [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'item_id' => is_object($item) ? $item->id : $item
                ];
            }
        }

        return $results;
    }

    /**
     * Get PDF generation statistics
     * 
     * @param int|null $companyId Company ID
     * @return array Statistics
     */
    protected function getPDFStatistics($companyId = null)
    {
        if (!$companyId) {
            $companyId = $this->getCurrentCompanyId();
        }

        $cacheKey = "pdf_stats_{$companyId}";
        
        return Cache::remember($cacheKey, 300, function () use ($companyId) {
            $tempDir = storage_path('app/temp/bills');
            $tempFiles = is_dir($tempDir) ? count(glob($tempDir . '/*')) : 0;
            
            return [
                'company_id' => $companyId,
                'temp_files' => $tempFiles,
                'cache_warmed' => Cache::has("company_settings_{$companyId}"),
                'available_formats' => count($this->getAvailablePDFFormats($companyId)),
                'service_loaded' => class_exists(BillPDFService::class),
            ];
        });
    }
}
