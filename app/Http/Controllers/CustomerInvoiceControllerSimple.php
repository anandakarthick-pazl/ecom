<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\AppSetting;
use App\Services\BillPDFService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerInvoiceControllerSimple extends Controller
{
    protected $billPDFService;

    public function __construct(BillPDFService $billPDFService)
    {
        $this->billPDFService = $billPDFService;
    }

    /**
     * Download invoice for a specific order (simplified version)
     */
    public function downloadInvoice(Request $request, $orderNumber)
    {
        try {
            // Find the order by order number
            $order = Order::where('order_number', $orderNumber)->firstOrFail();
            
            // Verify customer can access this order (by mobile number for security)
            if ($request->has('mobile')) {
                $inputMobile = $request->input('mobile');
                $normalizedInput = $this->normalizeMobileNumber($inputMobile);
                $normalizedOrder = $this->normalizeMobileNumber($order->customer_mobile);
                
                // Check if mobile numbers match
                $mobileMatches = (
                    $order->customer_mobile === $inputMobile || // Exact match
                    $normalizedOrder === $normalizedInput || // Normalized match
                    substr($order->customer_mobile, -10) === substr($normalizedInput, -10) // Last 10 digits match
                );
                
                if (!$mobileMatches) {
                    Log::warning('Unauthorized invoice access attempt', [
                        'order_number' => $orderNumber,
                        'provided_mobile' => $inputMobile
                    ]);
                    
                    return response()->view('errors.unauthorized-invoice', [
                        'message' => 'Mobile number verification failed.'
                    ], 403);
                }
            }
            
            // Load order relationships
            $order->load(['items.product', 'customer']);
            
            // Get the format from request or use default
            $format = $request->input('format', 'a4_sheet');
            
            Log::info('Customer downloading invoice', [
                'order_number' => $orderNumber,
                'format' => $format
            ]);
            
            // Get company settings
            $companySettings = $this->getCompanySettings($order->company_id);
            
            // Use the enhanced invoice template
            $viewName = 'invoices.enhanced-invoice';
            
            // Check if enhanced view exists, otherwise use fallback
            if (!\View::exists($viewName)) {
                // Try the original service method
                return $this->billPDFService->downloadOrderBill($order, $format);
            }
            
            // Generate PDF with enhanced template - SIMPLIFIED VERSION
            $pdf = Pdf::loadView($viewName, [
                'order' => $order,
                'company' => $companySettings
            ]);
            
            // Set paper size based on format
            if ($format === 'thermal') {
                $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm width
            } else {
                $pdf->setPaper('A4', 'portrait');
            }
            
            // Download the PDF without any additional options
            return $pdf->download('invoice-' . $order->order_number . '.pdf');
            
        } catch (\Exception $e) {
            Log::error('Customer invoice download failed', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to generate invoice',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get company settings for invoices
     */
    private function getCompanySettings($companyId)
    {
        try {
            // Try to get company from companies table if it exists
            if (class_exists('\App\Models\SuperAdmin\Company')) {
                $company = \App\Models\SuperAdmin\Company::find($companyId);
                
                if ($company) {
                    return [
                        'name' => $company->name,
                        'address' => trim(($company->address ?? '') . ' ' . ($company->city ?? '') . ' ' . ($company->state ?? '') . ' ' . ($company->postal_code ?? '')),
                        'phone' => $company->phone ?? '',
                        'email' => $company->email ?? '',
                        'website' => AppSetting::getForTenant('company_website', $companyId) ?? '',
                        'gst_number' => $company->gst_number ?? '',
                        'logo' => $company->logo ? asset('storage/' . $company->logo) : null,
                        'currency' => AppSetting::getForTenant('currency', $companyId) ?? '₹',
                        'tax_name' => AppSetting::getForTenant('tax_name', $companyId) ?? 'GST',
                        'tax_rate' => AppSetting::getForTenant('tax_rate', $companyId) ?? 18,
                        'bank_name' => AppSetting::getForTenant('bank_name', $companyId) ?? '',
                        'account_number' => AppSetting::getForTenant('account_number', $companyId) ?? '',
                        'ifsc_code' => AppSetting::getForTenant('ifsc_code', $companyId) ?? '',
                        'primary_color' => AppSetting::getForTenant('primary_color', $companyId) ?? '#2d5016'
                    ];
                }
            }
            
            // Fallback to app settings
            return [
                'name' => AppSetting::getForTenant('company_name', $companyId) ?? 'Your Company',
                'address' => AppSetting::getForTenant('company_address', $companyId) ?? '',
                'phone' => AppSetting::getForTenant('company_phone', $companyId) ?? '',
                'email' => AppSetting::getForTenant('company_email', $companyId) ?? '',
                'website' => AppSetting::getForTenant('company_website', $companyId) ?? '',
                'gst_number' => AppSetting::getForTenant('company_gst_number', $companyId) ?? '',
                'logo' => AppSetting::getForTenant('company_logo', $companyId) ?? '',
                'currency' => AppSetting::getForTenant('currency', $companyId) ?? '₹',
                'tax_name' => AppSetting::getForTenant('tax_name', $companyId) ?? 'GST',
                'tax_rate' => AppSetting::getForTenant('tax_rate', $companyId) ?? 18,
                'bank_name' => AppSetting::getForTenant('bank_name', $companyId) ?? '',
                'account_number' => AppSetting::getForTenant('account_number', $companyId) ?? '',
                'ifsc_code' => AppSetting::getForTenant('ifsc_code', $companyId) ?? '',
                'primary_color' => AppSetting::getForTenant('primary_color', $companyId) ?? '#2d5016'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get company settings', [
                'company_id' => $companyId,
                'error' => $e->getMessage()
            ]);
            
            // Return safe defaults
            return [
                'name' => 'Your Company',
                'address' => '',
                'phone' => '',
                'email' => '',
                'website' => '',
                'gst_number' => '',
                'logo' => '',
                'currency' => '₹',
                'tax_name' => 'GST',
                'tax_rate' => 18,
                'bank_name' => '',
                'account_number' => '',
                'ifsc_code' => '',
                'primary_color' => '#2d5016'
            ];
        }
    }
    
    /**
     * Normalize mobile number for comparison
     */
    private function normalizeMobileNumber($mobile)
    {
        if (empty($mobile)) {
            return '';
        }
        
        // Remove all non-digit characters
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        
        // Remove country code if present (91 for India)
        if (strlen($mobile) > 10) {
            // If starts with 91 and total length is 12, remove 91
            if (substr($mobile, 0, 2) === '91' && strlen($mobile) === 12) {
                $mobile = substr($mobile, 2);
            }
            // If starts with 0 and total length is 11, remove 0
            elseif (substr($mobile, 0, 1) === '0' && strlen($mobile) === 11) {
                $mobile = substr($mobile, 1);
            }
        }
        
        // Ensure we have exactly 10 digits
        if (strlen($mobile) > 10) {
            $mobile = substr($mobile, -10); // Take last 10 digits
        }
        
        return $mobile;
    }
}
