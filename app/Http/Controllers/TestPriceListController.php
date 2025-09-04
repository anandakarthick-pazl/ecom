<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class TestPriceListController extends Controller
{
    /**
     * Test if routes work - just return simple response
     */
    public function test()
    {
        return response()->json([
            'success' => true,
            'message' => 'Price List routes are working!',
            'products_count' => Product::active()->count(),
            'categories_count' => Category::active()->count(),
            'timestamp' => now()->toDateTimeString()
        ]);
    }
    
    /**
     * Simple HTML price list without PDF
     */
    public function simpleHtml()
    {
        $categories = Category::active()
            ->with(['activeProducts' => function($query) {
                $query->orderBy('name');
            }])
            ->whereHas('activeProducts')
            ->orderBy('name')
            ->get();
            
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Price List - Simple HTML</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #333; text-align: center; }
                h2 { color: #666; border-bottom: 2px solid #ddd; padding-bottom: 5px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .price { font-weight: bold; color: #2d5016; }
            </style>
        </head>
        <body>
            <h1>PRICE LIST</h1>
            <p><strong>Generated:</strong> ' . date('d/m/Y H:i:s') . '</p>';
            
        if ($categories->count() > 0) {
            foreach ($categories as $category) {
                $html .= '<h2>' . htmlspecialchars($category->name) . '</h2>';
                
                if ($category->activeProducts->count() > 0) {
                    $html .= '<table>
                        <tr>
                            <th>S.No</th>
                            <th>Product Name</th>
                            <th>MRP</th>
                            <th>Offer Price</th>
                        </tr>';
                    
                    $sno = 1;
                    foreach ($category->activeProducts as $product) {
                        $offerPrice = $product->discount_price && $product->discount_price < $product->price 
                            ? '₹' . number_format($product->discount_price, 2)
                            : '₹' . number_format($product->price, 2);
                            
                        $html .= '<tr>
                            <td>' . $sno . '</td>
                            <td>' . htmlspecialchars($product->name) . '</td>
                            <td class="price">₹' . number_format($product->price, 2) . '</td>
                            <td class="price">' . $offerPrice . '</td>
                        </tr>';
                        $sno++;
                    }
                    
                    $html .= '</table>';
                } else {
                    $html .= '<p>No products in this category.</p>';
                }
            }
        } else {
            $html .= '<p>No categories or products found.</p>';
        }
        
        $html .= '</body></html>';
        
        return response($html);
    }
}
