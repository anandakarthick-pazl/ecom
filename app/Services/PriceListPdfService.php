<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use App\Models\AppSetting;
use Barryvdh\DomPDF\Facade\Pdf;

class PriceListPdfService
{

    
    /**
     * Generate Price List PDF
     */
    public function generatePriceListPdf()
    {
        // Get company information
        $companyInfo = $this->getCompanyInfo();
        
        // Get all active products grouped by category
        $categoriesWithProducts = $this->getProductsByCategory();
        
        // Generate HTML content
        $html = $this->generateHtmlContent($companyInfo, $categoriesWithProducts);
        
        // Create PDF using Laravel DomPDF
        return Pdf::loadHtml($html)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'dpi' => 96,
                'fontDir' => storage_path('fonts/'),
                'fontCache' => storage_path('fonts/cache/'),
                'tempDir' => storage_path('app/temp/'),
                'chroot' => false,
                'debugKeepTemp' => false,
                'logOutputFile' => false
            ]);
    }
    
    /**
     * Get company information
     */
    protected function getCompanyInfo()
    {
        // Try to get company info from globalCompany (set by view composer)
        $globalCompany = null;
        
        // Try to get from current tenant
        if (app()->has('current_tenant')) {
            $company = app('current_tenant');
            return [
                'name' => $company->name ?? 'Your Company',
                'logo' => $company->logo ? asset('storage/' . $company->logo) : null,
                'address' => $company->address ?? '',
                'city' => $company->city ?? '',
                'state' => $company->state ?? '',
                'postal_code' => $company->postal_code ?? '',
                'phone' => $company->phone ?? '',
                'email' => $company->email ?? '',
                'gst_number' => $company->gst_number ?? ''
            ];
        }
        
        // Try to get from session or authentication
        $companyId = null;
        if (session('selected_company_id')) {
            $companyId = session('selected_company_id');
        } elseif (auth()->check() && auth()->user()->company_id) {
            $companyId = auth()->user()->company_id;
        }
        
        if ($companyId) {
            $company = \App\Models\SuperAdmin\Company::find($companyId);
            if ($company) {
                return [
                    'name' => $company->name ?? 'Your Company',
                    'logo' => $company->logo ? asset('storage/' . $company->logo) : null,
                    'address' => $company->address ?? '',
                    'city' => $company->city ?? '',
                    'state' => $company->state ?? '',
                    'postal_code' => $company->postal_code ?? '',
                    'phone' => $company->phone ?? '',
                    'email' => $company->email ?? '',
                    'gst_number' => $company->gst_number ?? ''
                ];
            }
        }
        
        // Try to get from domain
        $host = request()->getHost();
        if ($host && $host !== 'localhost' && $host !== '127.0.0.1') {
            $company = \App\Models\SuperAdmin\Company::where('domain', $host)->first();
            if ($company) {
                return [
                    'name' => $company->name ?? 'Your Company',
                    'logo' => $company->logo ? asset('storage/' . $company->logo) : null,
                    'address' => $company->address ?? '',
                    'city' => $company->city ?? '',
                    'state' => $company->state ?? '',
                    'postal_code' => $company->postal_code ?? '',
                    'phone' => $company->phone ?? '',
                    'email' => $company->email ?? '',
                    'gst_number' => $company->gst_number ?? ''
                ];
            }
        }
        
        // Fallback to app settings
        return [
            'name' => AppSetting::get('company_name', 'Your Company'),
            'logo' => AppSetting::get('company_logo') ? asset('storage/' . AppSetting::get('company_logo')) : null,
            'address' => AppSetting::get('company_address', ''),
            'city' => AppSetting::get('company_city', ''),
            'state' => AppSetting::get('company_state', ''),
            'postal_code' => AppSetting::get('company_postal_code', ''),
            'phone' => AppSetting::get('company_phone', ''),
            'email' => AppSetting::get('company_email', ''),
            'gst_number' => AppSetting::get('company_gst_number', '')
        ];
    }
    
    /**
     * Get all active products grouped by category
     */
    protected function getProductsByCategory()
    {
        return Category::active()
            ->with(['activeProducts' => function($query) {
                $query->orderBy('name');
            }])
            ->whereHas('activeProducts')
            ->orderBy('name')
            ->get()
            ->filter(function($category) {
                return $category->activeProducts->count() > 0;
            });
    }
    
    /**
     * Generate HTML content for PDF
     */
    protected function generateHtmlContent($companyInfo, $categoriesWithProducts)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Price List - ' . $companyInfo['name'] . '</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 3px solid #28a745;
            margin-bottom: 20px;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-right: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 10px;
        }
        
        .company-address {
            font-size: 11px;
            color: #666;
            line-height: 1.5;
        }
        
        .price-list-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            color: #333;
        }
        
        .category-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .category-header {
            background: #28a745;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .products-table th,
        .products-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        .products-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
        }
        
        .products-table td {
            font-size: 11px;
            text-align: center;
        }
        
        .sno-col { width: 6%; text-align: center; }
        .product-col { width: 35%; }
        .mrp-col { width: 12%; text-align: right; }
        .unit-col { width: 10%; text-align: center; }
        .offer-col { width: 12%; text-align: right; }
        .qty-col { width: 10%; text-align: center; }
        .amount-col { width: 15%; text-align: right; }
        
        .price {
            font-weight: bold;
        }
        
        .offer-price {
            color: #dc3545;
        }
        
        .original-price {
            text-decoration: line-through;
            color: #666;
        }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        
        @page {
            margin: 20mm;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .no-products {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>
<body>';

        // Header section
        $html .= '<div class="header">
            <div class="header-content">';
            
        if ($companyInfo['logo']) {
            $html .= '<img src="' . $companyInfo['logo'] . '" class="company-logo" alt="Company Logo">';
        }
        
        $html .= '<div class="company-info">
                <div class="company-name">' . htmlspecialchars($companyInfo['name']) . '</div>
                <div class="company-address">';
                
        $addressParts = array_filter([
            $companyInfo['address'],
            $companyInfo['city'],
            $companyInfo['state'] . ' ' . $companyInfo['postal_code']
        ]);
        
        if (!empty($addressParts)) {
            $html .= implode('<br>', array_map('htmlspecialchars', $addressParts)) . '<br>';
        }
        
        if ($companyInfo['phone']) {
            $html .= 'Phone: ' . htmlspecialchars($companyInfo['phone']) . '<br>';
        }
        
        if ($companyInfo['email']) {
            $html .= 'Email: ' . htmlspecialchars($companyInfo['email']) . '<br>';
        }
        
        if ($companyInfo['gst_number']) {
            $html .= 'GST No: ' . htmlspecialchars($companyInfo['gst_number']);
        }
        
        $html .= '</div>
            </div>
        </div>
    </div>';
    
        $html .= '<div class="price-list-title">PRICE LIST</div>';
        
        // Products by category
        $overallSerialNumber = 1;
        
        foreach ($categoriesWithProducts as $category) {
            $html .= '<div class="category-section">
                <div class="category-header">' . strtoupper(htmlspecialchars($category->name)) . '</div>
                
                <table class="products-table">
                    <thead>
                        <tr>
                            <th class="sno-col">S.No</th>
                            <th class="product-col">Product</th>
                            <th class="mrp-col">MRP</th>
                            <th class="unit-col">Unit</th>
                            <th class="offer-col">Offer Price</th>
                            <th class="qty-col">Qty</th>
                            <th class="amount-col">Amount</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            if ($category->activeProducts->count() > 0) {
                foreach ($category->activeProducts as $product) {
                    $html .= '<tr>
                        <td class="sno-col">' . $overallSerialNumber . '</td>
                        <td class="product-col">' . htmlspecialchars($product->name) . '</td>
                        <td class="mrp-col price">' . number_format($product->price, 2) . '</td>
                        <td class="unit-col">' . htmlspecialchars($product->weight_unit ?? 'pcs') . '</td>
                        <td class="offer-col">';
                    
                    // Check for offer price
                    $offerDetails = $product->getOfferDetails();
                    if ($offerDetails && $offerDetails['discounted_price'] < $product->price) {
                        $html .= '<span class="offer-price price">' . number_format($offerDetails['discounted_price'], 2) . '</span>';
                    } elseif ($product->discount_price && $product->discount_price < $product->price) {
                        $html .= '<span class="offer-price price">' . number_format($product->discount_price, 2) . '</span>';
                    } else {
                        $html .= '<span class="price">' . number_format($product->price, 2) . '</span>';
                    }
                    
                    $html .= '</td>
                        <td class="qty-col">-</td>
                        <td class="amount-col">-</td>
                    </tr>';
                    
                    $overallSerialNumber++;
                }
            } else {
                $html .= '<tr>
                    <td colspan="7" class="no-products">No products available in this category</td>
                </tr>';
            }
            
            $html .= '</tbody>
                </table>
            </div>';
        }
        
        // Footer
        $html .= '<div class="footer">
            Generated on: ' . date('d/m/Y H:i:s') . ' | ' . htmlspecialchars($companyInfo['name']) . ' - Price List
        </div>';
        
        $html .= '</body>
</html>';
        
        return $html;
    }
    
    /**
     * Download PDF
     */
    public function downloadPdf($filename = null)
    {
        $pdf = $this->generatePriceListPdf();
        
        if (!$filename) {
            $filename = 'price-list-' . date('Y-m-d') . '.pdf';
        }
        
        return $pdf->download($filename);
    }
    
    /**
     * Display PDF in browser
     */
    public function viewPdf($filename = null)
    {
        $pdf = $this->generatePriceListPdf();
        
        if (!$filename) {
            $filename = 'price-list-' . date('Y-m-d') . '.pdf';
        }
        
        return $pdf->stream($filename);
    }
}
