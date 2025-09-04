<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;

class TamilPriceListPdfService
{
    /**
     * Generate Tamil-compatible Price List PDF
     */
    public function generateTamilPriceListPdf()
    {
        // Get company information
        $companyInfo = $this->getSimpleCompanyInfo();
        
        // Get all active products grouped by category
        $categoriesWithProducts = $this->getProductsByCategory();
        
        // Generate Tamil-compatible HTML content
        $html = $this->generateTamilHtmlContent($companyInfo, $categoriesWithProducts);
        
        // Create PDF with Tamil font support
        return Pdf::loadHtml($html)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'isUnicode' => true,
                'isFontSubsettingEnabled' => true,
                'fontDir' => storage_path('fonts/'),
                'fontCache' => storage_path('fonts/cache/'),
                'enable_font_subsetting' => true,
                'dpi' => 96
            ]);
    }
    
    /**
     * Get company information
     */
    protected function getSimpleCompanyInfo()
    {
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
                    'logo' => $company->logo ? asset('storage/logos/' . $company->logo) : null,
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
        
        // Fallback
        return [
            'name' => 'Your Company',
            'logo' => null,
            'address' => '',
            'city' => '',
            'state' => '',
            'postal_code' => '',
            'phone' => '',
            'email' => '',
            'gst_number' => ''
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
     * Generate Tamil-compatible HTML content for PDF
     */
    protected function generateTamilHtmlContent($companyInfo, $categoriesWithProducts)
    {
        $html = '<!DOCTYPE html>
<html lang="ta">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Price List - ' . htmlspecialchars($companyInfo['name']) . '</title>
    <style>
        @font-face {
            font-family: "DejaVu Sans";
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: "DejaVu Sans", "Noto Sans Tamil", "Tamil Sangam MN", "Latha", Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 15px;
        }
        
        .header {
            text-align: center;
            background: #f8f9fa;
            padding: 20px;
            border: 2px solid #28a745;
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 8px;
        }
        
        .company-address {
            font-size: 10px;
            color: #666;
            line-height: 1.4;
            margin-bottom: 10px;
        }
        
        .price-list-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0;
            color: #333;
            border-bottom: 2px solid #28a745;
            padding-bottom: 5px;
        }
        
        .category-header {
            background: #28a745;
            color: white;
            padding: 8px 10px;
            font-size: 13px;
            font-weight: bold;
            margin: 15px 0 8px 0;
            text-align: center;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        
        .products-table th,
        .products-table td {
            border: 1px solid #333;
            padding: 6px 4px;
            text-align: left;
            vertical-align: top;
        }
        
        .products-table th {
            background-color: #e9ecef;
            font-weight: bold;
            font-size: 9px;
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
            font-family: "DejaVu Sans", Arial, sans-serif;
        }
        .offer-price { color: #dc3545; }
        .tamil-text {
            font-family: "DejaVu Sans", "Noto Sans Tamil", "Tamil Sangam MN", "Latha", sans-serif;
        }
        
        .footer {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        @page {
            margin: 15mm;
            size: A4;
        }
        
        /* Ensure Tamil text displays correctly */
        .tamil-text, 
        .category-header,
        .products-table td {
            unicode-bidi: embed;
            direction: ltr;
        }
    </style>
</head>
<body>';

        // Header section
        $html .= '<div class="header">';
        
        // Add company logo if available
        if ($companyInfo['logo']) {
            $html .= '<div style="margin-bottom: 10px;">
                <img src="' . $companyInfo['logo'] . '" style="height: 50px; width: auto; object-fit: contain;" alt="Company Logo">
            </div>';
        }
        
        $html .= '<div class="company-name tamil-text">' . htmlspecialchars($companyInfo['name']) . '</div>';
        
        if ($companyInfo['address'] || $companyInfo['city'] || $companyInfo['phone'] || $companyInfo['email']) {
            $html .= '<div class="company-address tamil-text">';
            
            $addressParts = array_filter([
                $companyInfo['address'],
                $companyInfo['city'] . ' ' . $companyInfo['state'] . ' ' . $companyInfo['postal_code']
            ]);
            
            if (!empty($addressParts)) {
                $html .= implode('<br>', array_map('htmlspecialchars', $addressParts)) . '<br>';
            }
            
            if ($companyInfo['phone']) {
                $html .= 'Phone: ' . htmlspecialchars($companyInfo['phone']) . '<br>';
            }
            
            if ($companyInfo['email']) {
                $html .= 'Email: ' . htmlspecialchars($companyInfo['email']);
            }
            
            $html .= '</div>';
        }
        
        $html .= '</div>';
    
        $html .= '<div class="price-list-title">PRICE LIST</div>';
        
        // Products by category
        $overallSerialNumber = 1;
        
        foreach ($categoriesWithProducts as $category) {
            $html .= '<div class="category-header tamil-text">' . strtoupper(htmlspecialchars($category->name)) . '</div>';
            
            $html .= '<table class="products-table">
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
                        <td class="product-col tamil-text">' . htmlspecialchars($product->name) . '</td>
                        <td class="mrp-col price">' . number_format($product->price, 2) . '</td>
                        <td class="unit-col">' . htmlspecialchars($product->weight_unit ?? 'pcs') . '</td>
                        <td class="offer-col">';
                    
                    // Check for offer price
                    if ($product->discount_price && $product->discount_price < $product->price) {
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
                    <td colspan="7" style="text-align: center; font-style: italic;">No products available</td>
                </tr>';
            }
            
            $html .= '</tbody>
            </table>';
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
        $pdf = $this->generateTamilPriceListPdf();
        
        if (!$filename) {
            $filename = 'price-list-tamil-' . date('Y-m-d') . '.pdf';
        }
        
        return $pdf->download($filename);
    }
    
    /**
     * Display PDF in browser
     */
    public function viewPdf($filename = null)
    {
        $pdf = $this->generateTamilPriceListPdf();
        
        if (!$filename) {
            $filename = 'price-list-tamil-' . date('Y-m-d') . '.pdf';
        }
        
        return $pdf->stream($filename);
    }
}
