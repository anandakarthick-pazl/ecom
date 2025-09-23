<?php

namespace App\Http\Controllers;

use App\Services\PriceListPdfService;
use App\Services\SimplePriceListPdfService;
use App\Services\TamilPriceListPdfService;
use Illuminate\Http\Request;

class PriceListController extends Controller
{
    protected $pdfService;
    protected $simplePdfService;
    protected $tamilPdfService;
    
    public function __construct(
        PriceListPdfService $pdfService, 
        SimplePriceListPdfService $simplePdfService,
        TamilPriceListPdfService $tamilPdfService
    ) {
        $this->pdfService = $pdfService;
        $this->simplePdfService = $simplePdfService;
        $this->tamilPdfService = $tamilPdfService;
    }
    
    /**
     * Download Price List PDF
     */
    public function downloadPdf()
    {
        try {
            $filename = 'price-list-' . date('Y-m-d') . '.pdf';
            return $this->tamilPdfService->downloadPdf($filename);
        } catch (\Exception $e) {
            \Log::error('Tamil PDF Generation Error: ' . $e->getMessage());
            
            // Try fallback to simple service
            try {
                $filename = 'price-list-' . date('Y-m-d') . '.pdf';
                return $this->simplePdfService->downloadPdf($filename);
            } catch (\Exception $fallbackError) {
                \Log::error('Simple PDF Generation also failed: ' . $fallbackError->getMessage());
                
                // Final fallback to main service
                try {
                    return $this->pdfService->downloadPdf($filename);
                } catch (\Exception $finalError) {
                    \Log::error('All PDF services failed: ' . $finalError->getMessage());
                    
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to generate price list. Please try again or contact support.'
                        ], 500);
                    }
                    
                    return redirect()->back()->with('error', 'Failed to generate price list. Please try again.');
                }
            }
        }
    }
    
    /**
     * View Price List PDF in browser
     */
    public function viewPdf()
    {
        try {
            $filename = 'price-list-' . date('Y-m-d') . '.pdf';
            return $this->tamilPdfService->viewPdf($filename);
        } catch (\Exception $e) {
            \Log::error('Tamil PDF Generation Error: ' . $e->getMessage());
            
            // Try fallback to simple service
            try {
                $filename = 'price-list-' . date('Y-m-d') . '.pdf';
                return $this->simplePdfService->viewPdf($filename);
            } catch (\Exception $fallbackError) {
                \Log::error('Simple PDF Generation also failed: ' . $fallbackError->getMessage());
                
                // Final fallback to main service
                try {
                    return $this->pdfService->viewPdf($filename);
                } catch (\Exception $finalError) {
                    \Log::error('All PDF services failed: ' . $finalError->getMessage());
                    
                    return redirect()->back()->with('error', 'Failed to generate price list. Please try again.');
                }
            }
        }
    }
}
