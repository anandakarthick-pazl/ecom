<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\AppSetting;
use App\Services\BillPDFService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf; // Explicitly use DomPDF, not Snappy

class CustomerInvoiceController extends Controller
{
    protected $billPDFService;

    public function __construct(BillPDFService $billPDFService)
    {
        $this->billPDFService = $billPDFService;
    }

    /**
     * Download invoice for a specific order
     * Customer can choose between thermal and PDF format
     */
    public function downloadInvoice(Request $request, $orderNumber)
    {
        try {
            set_time_limit(300); // prevent timeout for large invoices

            // Find the order by order number
            $order = Order::where('order_number', $orderNumber)->firstOrFail();

            // Verify customer can access this order (by mobile number for security)
            if ($request->has('mobile')) {
                $inputMobile = $request->input('mobile');
                $normalizedInput = $this->normalizeMobileNumber($inputMobile);
                $normalizedOrder = $this->normalizeMobileNumber($order->customer_mobile);

                $mobileMatches = (
                    $order->customer_mobile === $inputMobile ||
                    $normalizedOrder === $normalizedInput ||
                    substr($order->customer_mobile, -10) === substr($normalizedInput, -10)
                );

                if (!$mobileMatches) {
                    Log::warning('Unauthorized invoice access attempt', [
                        'order_number' => $orderNumber,
                        'provided_mobile' => $inputMobile,
                        'normalized_provided' => $normalizedInput,
                        'order_mobile' => $order->customer_mobile,
                        'normalized_order' => $normalizedOrder
                    ]);

                    return response()->view('errors.unauthorized-invoice', [
                        'message' => 'Mobile number verification failed. Please ensure you are using the same mobile number used for placing the order.'
                    ], 403);
                }
            }

            // Load relationships efficiently
            $order->load(['items.product', 'customer']);

            // Get invoice format
            $format = $request->input('format') ??
                AppSetting::getForTenant('default_bill_format', $order->company_id) ??
                'a4_sheet';

            // Get company settings
            $companySettings = $this->getCompanySettings($order->company_id);

            // Select template
            $viewName = 'invoices.enhanced-invoice';
            if (!\View::exists($viewName)) {
                return $this->billPDFService->downloadOrderBill($order, $format);
            }

            // Ensure logo/images use local path for DomPDF
            if (!empty($companySettings['logo'])) {
                $companySettings['logo_path'] = public_path('uploads/logos/' . $companySettings['logo']);
            }

            // Generate PDF with optimized options
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'sans-serif',
            ])->loadView($viewName, [
                'order' => $order,
                'company' => $companySettings
            ]);

            // Paper format
            if ($format === 'thermal') {
                $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait'); // 80mm width
            } else {
                $pdf->setPaper('A4', 'portrait');
            }

            // Stream to avoid memory overload
            return $pdf->stream('invoice-' . $order->order_number . '.pdf');
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
     * Download invoice from track order page
     * Requires order number and mobile verification
     */
    public function downloadTrackedInvoice(Request $request)
    {
        try {
            $request->validate([
                'order_number' => 'required|string',
                'mobile_number' => 'required|string',
                'format' => 'nullable|in:thermal,a4_sheet'
            ]);

            // Normalize mobile numbers for comparison
            $inputMobile = $this->normalizeMobileNumber($request->mobile_number);

            // Log the attempt for debugging
            Log::info('Tracked invoice download attempt', [
                'order_number' => $request->order_number,
                'input_mobile' => $request->mobile_number,
                'normalized_mobile' => $inputMobile,
                'format' => $request->format
            ]);

            // Find the order with flexible mobile number matching
            $order = Order::where('order_number', $request->order_number)
                ->where(function ($query) use ($inputMobile, $request) {
                    // Try exact match first
                    $query->where('customer_mobile', $request->mobile_number)
                        // Try normalized match
                        ->orWhere('customer_mobile', $inputMobile)
                        // Try with +91 prefix
                        ->orWhere('customer_mobile', '+91' . $inputMobile)
                        // Try without +91 prefix if input has it
                        ->orWhere('customer_mobile', ltrim($request->mobile_number, '+91'))
                        // Try last 10 digits match
                        ->orWhereRaw('RIGHT(customer_mobile, 10) = ?', [substr($inputMobile, -10)]);
                })
                ->first();

            if (!$order) {
                Log::warning('Order not found for tracked invoice download', [
                    'order_number' => $request->order_number,
                    'mobile_number' => $request->mobile_number
                ]);

                // Try to find if order exists but mobile doesn't match
                $orderExists = Order::where('order_number', $request->order_number)->exists();

                if ($orderExists) {
                    $actualOrder = Order::where('order_number', $request->order_number)->first();
                    Log::info('Order exists but mobile mismatch', [
                        'order_mobile' => $actualOrder->customer_mobile,
                        'input_mobile' => $request->mobile_number
                    ]);
                    return back()->with('error', 'Mobile number does not match with the order. Please check and try again.');
                } else {
                    return back()->with('error', 'Order not found. Please check the order number.');
                }
            }

            // Load order relationships
            $order->load(['items.product', 'customer']);

            // Get the format from request or use default
            $format = $request->input('format', null);

            // If no format specified, get default from company settings
            if (!$format) {
                $companyId = $order->company_id;
                $format = AppSetting::getForTenant('default_bill_format', $companyId) ?? 'a4_sheet';
            }

            Log::info('Customer downloading tracked order invoice', [
                'order_number' => $request->order_number,
                'format' => $format,
                'customer_mobile' => $order->customer_mobile
            ]);

            // Get company settings
            $companySettings = $this->getCompanySettings($order->company_id);

            // Use the enhanced invoice template
            $viewName = 'invoices.enhanced-invoice';

            // Check if enhanced view exists, otherwise use service
            if (!\View::exists($viewName)) {
                return $this->billPDFService->downloadOrderBill($order, $format);
            }

            // Generate PDF with enhanced template using DomPDF explicitly
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Order not found exception', [
                'order_number' => $request->order_number ?? 'N/A',
                'mobile' => $request->mobile_number ?? 'N/A'
            ]);
            return back()->with('error', 'Order not found or mobile number does not match.');
        } catch (\Exception $e) {
            Log::error('Tracked order invoice download failed', [
                'order_number' => $request->order_number ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to generate invoice. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Get available invoice formats for customer
     */
    public function getAvailableFormats(Request $request)
    {
        try {
            $order = Order::where('order_number', $request->order_number)->first();

            if (!$order) {
                return response()->json(['formats' => ['a4_sheet' => 'PDF Invoice']]);
            }

            $companyId = $order->company_id;
            $formats = $this->billPDFService->getAvailableFormats($companyId);

            return response()->json(['formats' => $formats]);
        } catch (\Exception $e) {
            return response()->json(['formats' => ['a4_sheet' => 'PDF Invoice']]);
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

    /**
     * Normalize mobile number for comparison
     * Remove country code, spaces, and special characters
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
