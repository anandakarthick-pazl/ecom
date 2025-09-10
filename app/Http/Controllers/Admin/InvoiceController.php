<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\AppSetting;
use App\Services\BillPDFService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    protected $billPDFService;

    public function __construct(BillPDFService $billPDFService)
    {
        $this->billPDFService = $billPDFService;
    }

    /**
     * Print invoice for admin panel
     */
    public function printInvoice(Request $request, Order $order)
    {
        try {
            // Load order relationships
            $order->load(['items.product', 'customer']);
            
            // Get the format from request or use default
            $format = $request->input('format', 'a4_sheet');
            
            // Get company settings
            $companySettings = $this->getCompanySettings($order->company_id);
            
            Log::info('Admin printing invoice', [
                'order_number' => $order->order_number,
                'format' => $format
            ]);
            
            // Use the enhanced invoice template
            $viewName = 'invoices.enhanced-invoice';
            
            // Check if enhanced view exists, otherwise fallback
            if (!\View::exists($viewName)) {
                $viewName = 'admin.orders.invoice-pdf';
            }
            
            // Generate PDF
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
            
            // Stream the PDF (open in browser)
            return $pdf->stream('invoice-' . $order->order_number . '.pdf');
            
        } catch (\Exception $e) {
            Log::error('Failed to print invoice', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }

    /**
     * Download invoice for admin panel
     */
    public function downloadInvoice(Request $request, Order $order)
    {
        try {
            // Load order relationships
            $order->load(['items.product', 'customer']);
            
            // Get the format from request or use default
            $format = $request->input('format', 'a4_sheet');
            
            // Get company settings
            $companySettings = $this->getCompanySettings($order->company_id);
            
            Log::info('Admin downloading invoice', [
                'order_number' => $order->order_number,
                'format' => $format
            ]);
            
            // Use the enhanced invoice template
            $viewName = 'invoices.enhanced-invoice';
            
            // Check if enhanced view exists, otherwise fallback
            if (!\View::exists($viewName)) {
                $viewName = 'admin.orders.invoice-pdf';
            }
            
            // Generate PDF
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
            
            // Download the PDF
            return $pdf->download('invoice-' . $order->order_number . '.pdf');
            
        } catch (\Exception $e) {
            Log::error('Failed to download invoice', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
    }

    /**
     * Get company settings for invoices
     */
    private function getCompanySettings($companyId)
    {
        try {
            // Get company from companies table
            $company = \App\Models\SuperAdmin\Company::find($companyId);
            
            if ($company) {
                return [
                    'name' => $company->name,
                    'address' => trim($company->address . ' ' . $company->city . ' ' . $company->state . ' ' . $company->postal_code),
                    'phone' => $company->phone,
                    'email' => $company->email,
                    'website' => AppSetting::getForTenant('company_website', $companyId) ?? '',
                    'gst_number' => $company->gst_number,
                    'logo' => $company->logo ? asset('storage/' . $company->logo) : null,
                    'currency' => AppSetting::getForTenant('currency', $companyId) ?? '₹',
                    'tax_name' => AppSetting::getForTenant('tax_name', $companyId) ?? 'GST',
                    'tax_rate' => AppSetting::getForTenant('tax_rate', $companyId) ?? 18,
                    'bank_name' => AppSetting::getForTenant('bank_name', $companyId) ?? '',
                    'account_number' => AppSetting::getForTenant('account_number', $companyId) ?? '',
                    'ifsc_code' => AppSetting::getForTenant('ifsc_code', $companyId) ?? '',
                    'primary_color' => AppSetting::getForTenant('primary_color', $companyId) ?? '#2d5016'
                ];
            } else {
                // Fallback to app settings if company not found
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
            }
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
}
