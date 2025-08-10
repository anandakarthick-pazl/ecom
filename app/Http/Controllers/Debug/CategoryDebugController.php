<?php

namespace App\Http\Controllers\Debug;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryDebugController extends Controller
{
    /**
     * Debug category product counts
     */
    public function debugCategoryCounts()
    {
        $debugData = [];
        
        // Get all categories
        $categories = Category::active()
            ->parent()
            ->orderBy('sort_order')
            ->get();
            
        foreach ($categories as $category) {
            // Get different counts to identify the issue
            $totalProducts = Product::where('category_id', $category->id)->count();
            $activeProducts = Product::where('category_id', $category->id)->active()->count();
            $inStockProducts = Product::where('category_id', $category->id)->active()->where('stock', '>', 0)->count();
            
            // Sample products for this category
            $sampleProducts = Product::where('category_id', $category->id)
                ->select('id', 'name', 'is_active', 'stock', 'company_id')
                ->limit(3)
                ->get();
            
            $debugData[] = [
                'category_id' => $category->id,
                'category_name' => $category->name,
                'category_company_id' => $category->company_id ?? 'null',
                'total_products' => $totalProducts,
                'active_products' => $activeProducts,
                'in_stock_products' => $inStockProducts,
                'sample_products' => $sampleProducts->map(function($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'is_active' => $p->is_active,
                        'stock' => $p->stock,
                        'company_id' => $p->company_id ?? 'null'
                    ];
                })->toArray()
            ];
        }
        
        // Also get general stats
        $generalStats = [
            'total_categories' => Category::count(),
            'active_categories' => Category::active()->count(),
            'parent_categories' => Category::active()->parent()->count(),
            'total_products' => Product::count(),
            'active_products' => Product::active()->count(),
            'in_stock_products' => Product::active()->where('stock', '>', 0)->count(),
            'current_company_id' => session('current_company_id') ?? auth()->user()->company_id ?? 'not_set'
        ];
        
        return response()->json([
            'general_stats' => $generalStats,
            'category_debug' => $debugData,
            'note' => 'Check if company_id filtering is affecting the counts'
        ], 200, [], JSON_PRETTY_PRINT);
    }
    
    /**
     * Quick fix for category counts
     */
    public function quickFixCategoryCounts()
    {
        $results = [];
        
        $categories = Category::active()
            ->parent()
            ->orderBy('sort_order')
            ->get();
            
        foreach ($categories as $category) {
            // Get count without any potential tenant filtering
            $count = Product::where('category_id', $category->id)
                ->where('is_active', true)
                ->count();
                
            $results[] = [
                'category' => $category->name,
                'old_count' => $category->products_count ?? 'not_set',
                'new_count' => $count
            ];
        }
        
        return response()->json([
            'message' => 'Category count analysis complete',
            'results' => $results
        ], 200, [], JSON_PRETTY_PRINT);
    }
}
