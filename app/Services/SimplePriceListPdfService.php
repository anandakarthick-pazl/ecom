<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;

class SimplePriceListPdfService
{
    /**
     * Generate Simple Price List PDF
     */
    public function generatePriceListPdf()
    {
        // Get company information (simple version)
        $companyInfo = $this->getSimpleCompanyInfo();
        
        // Get all active products grouped by category
        $categoriesWithProducts = $this->getProductsByCategory();
        
        // Generate simple HTML content
        $html = $this->generateSimpleHtmlContent($companyInfo, $categoriesWithProducts);
        
        // Create PDF with minimal options
        return Pdf::loadHtml($html)
            ->setPaper('A4', 'portrait')
            ->setOptions([
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true
            ]);
    }
    
    /**
     * Get company information (simple version)
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
        
        // Try to get from app settings as fallback
        return [
            'name' => \App\Models\AppSetting::get('company_name', 'Your Company'),
            'logo' => \App\Models\AppSetting::get('company_logo') ? asset('storage/' . \App\Models\AppSetting::get('company_logo')) : null,
            'address' => \App\Models\AppSetting::get('company_address', ''),
            'city' => \App\Models\AppSetting::get('company_city', ''),
            'state' => \App\Models\AppSetting::get('company_state', ''),
            'postal_code' => \App\Models\AppSetting::get('company_postal_code', ''),
            'phone' => \App\Models\AppSetting::get('company_phone', ''),
            'email' => \App\Models\AppSetting::get('company_email', ''),
            'gst_number' => \App\Models\AppSetting::get('company_gst_number', '')
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
     * Generate simple HTML content for PDF
     */
    protected function generateSimpleHtmlContent($companyInfo, $categoriesWithProducts)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Price List - ' . htmlspecialchars($companyInfo['name']) . '</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 3px solid #28a745;
            margin-bottom: 20px;
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
        
        .category-header {
            background: #28a745;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            margin: 20px 0 10px 0;
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
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .price { font-weight: bold; }
        .offer-price { color: #dc3545; }
        
        .footer {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>';

        // Header section
        $html .= '<div class="header">';
        
        // Add company logo if available
        if ($companyInfo['logo']) {
            $html .= '<img src="' . $companyInfo['logo'] . '" style="height: 60px; width: auto; object-fit: contain; margin-bottom: 10px;" alt="Company Logo">';
        }
        
        $html .= '<div class="company-name">' . htmlspecialchars($companyInfo['name']) . '</div>
            <div class="company-address">';
                
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
        
        $html .= '</div>
        </div>';
    
        $html .= '<div class="price-list-title">PRICE LIST</div>';
        
        // Products by category
        $overallSerialNumber = 1;
        
        foreach ($categoriesWithProducts as $category) {
            $html .= '<div class="category-header">' . strtoupper(htmlspecialchars($category->name)) . '</div>';
            
            $html .= '<table class="products-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">S.No</th>
                        <th style="width: 40%;">Product</th>
                        <th style="width: 15%;">MRP</th>
                        <th style="width: 12%;">Unit</th>
                        <th style="width: 15%;">Offer Price</th>
                        <th style="width: 10%;">Amount</th>
                    </tr>
                </thead>
                <tbody>';
            
            if ($category->activeProducts->count() > 0) {
                foreach ($category->activeProducts as $product) {
                    $html .= '<tr>
                        <td class="text-center">' . $overallSerialNumber . '</td>
                        <td>' . htmlspecialchars($product->name) . '</td>
                        <td class="text-right price">' . number_format($product->price, 2) . '</td>
                        <td class="text-center">' . htmlspecialchars($product->weight_unit ?? 'pcs') . '</td>
                        <td class="text-right">';
                    
                    // Check for offer price
                    if ($product->discount_price && $product->discount_price < $product->price) {
                        $html .= '<span class="offer-price price">' . number_format($product->discount_price, 2) . '</span>';
                    } else {
                        $html .= '<span class="price">' . number_format($product->price, 2) . '</span>';
                    }
                    
                    $html .= '</td>
                        <td class="text-right">-</td>
                    </tr>';
                    
                    $overallSerialNumber++;
                }
            } else {
                $html .= '<tr>
                    <td colspan="6" class="text-center">No products available in this category</td>
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
