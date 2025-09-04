<?php

namespace App\Http\Controllers;

use App\Services\PriceListPdfService;
use App\Services\SimplePriceListPdfService;
use Illuminate\Http\Request;

class PriceListController extends Controller
{
    protected $pdfService;
    protected $simplePdfService;
    
    public function __construct(PriceListPdfService $pdfService, SimplePriceListPdfService $simplePdfService)
    {
        $this->pdfService = $pdfService;
        $this->simplePdfService = $simplePdfService;
    }
    
    /**
     * Download Price List PDF
     */
    public function downloadPdf()
    {
        try {
            $filename = 'price-list-' . date('Y-m-d') . '.pdf';
            return $this->pdfService->downloadPdf($filename);
        } catch (\Exception $e) {
            \Log::error('Price List PDF Generation Error: ' . $e->getMessage());
            
            // Try fallback to simple service
            try {
                $filename = 'price-list-' . date('Y-m-d') . '.pdf';
                return $this->simplePdfService->downloadPdf($filename);
            } catch (\Exception $fallbackError) {
                \Log::error('Simple PDF Generation also failed: ' . $fallbackError->getMessage());
                
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
    
    /**
     * View Price List PDF in browser
     */
    public function viewPdf()
    {
        try {
            $filename = 'price-list-' . date('Y-m-d') . '.pdf';
            return $this->pdfService->viewPdf($filename);
        } catch (\Exception $e) {
            \Log::error('Price List PDF Generation Error: ' . $e->getMessage());
            
            // Try fallback to simple service
            try {
                $filename = 'price-list-' . date('Y-m-d') . '.pdf';
                return $this->simplePdfService->viewPdf($filename);
            } catch (\Exception $fallbackError) {
                \Log::error('Simple PDF Generation also failed: ' . $fallbackError->getMessage());
                
                return redirect()->back()->with('error', 'Failed to generate price list. Please try again.');
            }
        }
    }
}
