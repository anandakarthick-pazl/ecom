<?php

use Illuminate\Support\Facades\Route;

// Debug route for POS categories
Route::get('/debug-pos-categories', function () {
    $html = '<!DOCTYPE html><html><head><title>POS Categories Debug</title>';
    $html .= '<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style></head><body>';
    
    $html .= '<h1>POS Categories Debug</h1>';
    
    try {
        // Check categories
        $categories = \App\Models\Category::active()
            ->currentTenant()
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();
            
        $html .= '<h2>Category Model Results:</h2>';
        $html .= '<p class="success">✓ Found ' . $categories->count() . ' active categories</p>';
        
        if ($categories->count() > 0) {
            $html .= '<table border="1" style="border-collapse:collapse; width:100%; margin:10px 0;">';
            $html .= '<tr style="background:#f5f5f5;"><th>ID</th><th>Name</th><th>Sort Order</th><th>Slug</th><th>Active</th><th>Products Count</th></tr>';
            
            foreach ($categories as $category) {
                $productsCount = $category->products()->count();
                $html .= '<tr>';
                $html .= '<td>' . $category->id . '</td>';
                $html .= '<td>' . $category->name . '</td>';
                $html .= '<td>' . $category->sort_order . '</td>';
                $html .= '<td>' . $category->slug . '</td>';
                $html .= '<td>' . ($category->is_active ? 'Yes' : 'No') . '</td>';
                $html .= '<td>' . $productsCount . '</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';
            
            // Show proper dropdown format
            $html .= '<h3>Correct Dropdown Format:</h3>';
            $html .= '<select style="width:300px; padding:5px;">';
            $html .= '<option value="">All Categories</option>';
            foreach ($categories as $category) {
                $html .= '<option value="' . $category->id . '">' . $category->id . ' - ' . $category->name . '</option>';
            }
            $html .= '</select>';
            
        } else {
            $html .= '<p class="error">✗ No categories found</p>';
        }
        
        // Test old method (what was causing the issue)
        $html .= '<h2>Old Method (Broken):</h2>';
        try {
            $oldCategories = \App\Models\Product::active()
                ->currentTenant()
                ->with('category')
                ->get()
                ->pluck('category.name')
                ->filter()
                ->unique()
                ->sort()
                ->values();
                
            $html .= '<p class="error">✗ Old method returns: ' . $oldCategories->count() . ' category names only</p>';
            $html .= '<p>This was causing the JSON object issue because it was getting full category objects instead of just names.</p>';
        } catch (\Exception $e) {
            $html .= '<p class="error">✗ Old method error: ' . $e->getMessage() . '</p>';
        }
        
        // Test category filter
        $html .= '<h2>Category Filter Test:</h2>';
        $firstCategory = $categories->first();
        if ($firstCategory) {
            $products = \App\Models\Product::active()
                ->currentTenant()
                ->where('category_id', $firstCategory->id)
                ->count();
                
            $html .= '<p class="success">✓ Products in "' . $firstCategory->name . '" (ID: ' . $firstCategory->id . '): ' . $products . '</p>';
        }
        
    } catch (\Exception $e) {
        $html .= '<p class="error">Error: ' . $e->getMessage() . '</p>';
        $html .= '<p class="error">Trace: ' . $e->getTraceAsString() . '</p>';
    }
    
    $html .= '<h2>Quick Links:</h2>';
    $html .= '<ul>';
    $html .= '<li><a href="' . route('admin.pos.index') . '">Go to POS</a></li>';
    $html .= '<li><a href="' . route('admin.categories.index') . '">Manage Categories</a></li>';
    $html .= '</ul>';
    
    $html .= '</body></html>';
    return $html;
})->middleware(['web', 'auth', 'company.context'])->name('debug.pos.categories');
