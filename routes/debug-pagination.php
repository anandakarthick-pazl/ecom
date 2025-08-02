<?php

use Illuminate\Support\Facades\Route;

// Debug route for pagination testing
Route::get('/debug-pagination', function () {
    $html = '<!DOCTYPE html><html><head><title>Pagination Debug</title>';
    $html .= '<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style></head><body>';
    
    $html .= '<h1>Admin Pagination Debug</h1>';
    
    try {
        // Test pagination settings
        $html .= '<h2>Pagination Settings:</h2>';
        
        $adminPaginationEnabled = \App\Models\AppSetting::get('admin_pagination_enabled', true);
        $adminRecordsPerPage = \App\Models\AppSetting::get('admin_records_per_page', '20');
        
        $html .= '<p class="' . ($adminPaginationEnabled ? 'success' : 'error') . '">';
        $html .= 'Admin Pagination Enabled: ' . ($adminPaginationEnabled ? 'Yes' : 'No') . '</p>';
        $html .= '<p class="info">Records per page: ' . $adminRecordsPerPage . '</p>';
        
        // Test categories pagination
        $html .= '<h2>Categories Pagination Test:</h2>';
        
        $categoriesQuery = \App\Models\Category::orderBy('sort_order');
        $totalCategories = $categoriesQuery->count();
        
        $html .= '<p class="info">Total categories in database: ' . $totalCategories . '</p>';
        
        // Test with HasPagination trait
        $controller = new \App\Http\Controllers\Admin\CategoryController();
        $request = request();
        
        // Simulate the controller logic
        $query = \App\Models\Category::with('parent')->orderBy('sort_order');
        
        // Apply tenant scope (if applicable)
        if (method_exists($controller, 'applyTenantScope')) {
            $query = $controller->applyTenantScope($query);
        }
        
        try {
            // Use reflection to call protected method
            $reflection = new ReflectionClass($controller);
            $method = $reflection->getMethod('applyAdminPagination');
            $method->setAccessible(true);
            $result = $method->invoke($controller, $query, $request, '20');
            
            $html .= '<p class="success">✓ applyAdminPagination method worked</p>';
            $html .= '<p class="info">Result type: ' . get_class($result) . '</p>';
            $html .= '<p class="info">Has links method: ' . (method_exists($result, 'links') ? 'Yes' : 'No') . '</p>';
            
            if (method_exists($result, 'total')) {
                $html .= '<p class="info">Total items: ' . $result->total() . '</p>';
                $html .= '<p class="info">Per page: ' . $result->perPage() . '</p>';
                $html .= '<p class="info">Current page: ' . $result->currentPage() . '</p>';
            } else {
                $html .= '<p class="error">✗ No pagination info (Collection returned)</p>';
                $html .= '<p class="info">Items count: ' . $result->count() . '</p>';
            }
            
            // Test ensurePaginator method
            if (method_exists($controller, 'ensurePaginator')) {
                $ensureMethod = $reflection->getMethod('ensurePaginator');
                $ensureMethod->setAccessible(true);
                $paginator = $ensureMethod->invoke($controller, $result, $request, 20);
                
                $html .= '<p class="success">✓ ensurePaginator method worked</p>';
                $html .= '<p class="info">Ensured result type: ' . get_class($paginator) . '</p>';
                $html .= '<p class="info">Has links method: ' . (method_exists($paginator, 'links') ? 'Yes' : 'No') . '</p>';
            }
            
        } catch (\Exception $e) {
            $html .= '<p class="error">✗ Pagination test failed: ' . $e->getMessage() . '</p>';
        }
        
        // Test simple pagination fallback
        $html .= '<h3>Simple Pagination Fallback:</h3>';
        $simplePagination = \App\Models\Category::orderBy('sort_order')->paginate(20);
        $html .= '<p class="success">✓ Simple pagination works</p>';
        $html .= '<p class="info">Type: ' . get_class($simplePagination) . '</p>';
        $html .= '<p class="info">Has links: ' . (method_exists($simplePagination, 'links') ? 'Yes' : 'No') . '</p>';
        
    } catch (\Exception $e) {
        $html .= '<p class="error">General Error: ' . $e->getMessage() . '</p>';
        $html .= '<p class="error">Trace: ' . $e->getTraceAsString() . '</p>';
    }
    
    $html .= '<h2>Quick Links:</h2>';
    $html .= '<ul>';
    $html .= '<li><a href="' . route('admin.categories.index') . '">Test Categories Page</a></li>';
    $html .= '<li><a href="' . route('admin.dashboard') . '">Admin Dashboard</a></li>';
    $html .= '</ul>';
    
    $html .= '</body></html>';
    return $html;
})->middleware(['web', 'auth', 'company.context'])->name('debug.pagination');
