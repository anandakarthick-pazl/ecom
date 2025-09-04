<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class SimplePriceListController extends Controller
{
    /**
     * Download CSV price list (simple alternative)
     */
    public function downloadCsv()
    {
        $filename = 'price-list-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, ['S.No', 'Category', 'Product Name', 'MRP', 'Offer Price', 'Unit', 'Qty', 'Amount']);
            
            $sno = 1;
            $categories = Category::active()
                ->with('activeProducts')
                ->whereHas('activeProducts')
                ->orderBy('name')
                ->get();
                
            foreach ($categories as $category) {
                foreach ($category->activeProducts as $product) {
                    $offerPrice = $product->discount_price && $product->discount_price < $product->price 
                        ? $product->discount_price 
                        : $product->price;
                        
                    fputcsv($file, [
                        $sno,
                        $category->name,
                        $product->name,
                        number_format($product->price, 2),
                        number_format($offerPrice, 2),
                        $product->weight_unit ?? 'pcs',
                        '', // Empty qty for customer to fill
                        ''  // Empty amount for customer to fill
                    ]);
                    $sno++;
                }
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Simple text price list
     */
    public function downloadTxt()
    {
        $content = "PRICE LIST\n";
        $content .= "Generated: " . date('d/m/Y H:i:s') . "\n";
        $content .= str_repeat("=", 50) . "\n\n";
        
        $categories = Category::active()
            ->with('activeProducts')
            ->whereHas('activeProducts')
            ->orderBy('name')
            ->get();
            
        $sno = 1;
        foreach ($categories as $category) {
            $content .= strtoupper($category->name) . "\n";
            $content .= str_repeat("-", 30) . "\n";
            
            foreach ($category->activeProducts as $product) {
                $offerPrice = $product->discount_price && $product->discount_price < $product->price 
                    ? $product->discount_price 
                    : $product->price;
                    
                $content .= sprintf(
                    "%d. %s - MRP: %s, Offer: %s\n",
                    $sno,
                    $product->name,
                    number_format($product->price, 2),
                    number_format($offerPrice, 2)
                );
                $sno++;
            }
            $content .= "\n";
        }
        
        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="price-list-' . date('Y-m-d') . '.txt"');
    }
}
