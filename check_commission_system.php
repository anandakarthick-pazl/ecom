<?php
/**
 * Commission System Status Checker
 * Run this to verify your commission system is working properly
 */

echo "==================================================\n";
echo "COMMISSION SYSTEM STATUS CHECKER\n";
echo "==================================================\n\n";

// Check if we're in Laravel environment
if (!function_exists('app')) {
    echo "❌ ERROR: Not in Laravel environment. Run this with: php artisan tinker\n";
    exit(1);
}

try {
    echo "1. Checking Commission Model...\n";
    if (class_exists('\\App\\Models\\Commission')) {
        echo "   ✅ Commission Model: EXISTS\n";
        
        // Count commissions
        $total = \App\Models\Commission::count();
        $pending = \App\Models\Commission::where('status', 'pending')->count();
        $paid = \App\Models\Commission::where('status', 'paid')->count();
        
        echo "   📊 Total Commissions: {$total}\n";
        echo "   📊 Pending: {$pending}\n";
        echo "   📊 Paid: {$paid}\n";
    } else {
        echo "   ❌ Commission Model: MISSING\n";
    }
    
    echo "\n2. Checking Commission Controller...\n";
    if (class_exists('\\App\\Http\\Controllers\\Admin\\CommissionController')) {
        echo "   ✅ Commission Controller: EXISTS\n";
    } else {
        echo "   ❌ Commission Controller: MISSING\n";
    }
    
    echo "\n3. Checking Commission Routes...\n";
    $routes = collect(\Illuminate\Support\Facades\Route::getRoutes());
    $commissionRoutes = $routes->filter(function($route) {
        return str_contains($route->uri(), 'commission');
    });
    
    if ($commissionRoutes->count() > 0) {
        echo "   ✅ Commission Routes: {$commissionRoutes->count()} routes found\n";
        foreach ($commissionRoutes as $route) {
            echo "   📝 {$route->methods()[0]} /{$route->uri()}\n";
        }
    } else {
        echo "   ❌ Commission Routes: NOT FOUND\n";
    }
    
    echo "\n4. Checking Commission Views...\n";
    $viewPath = resource_path('views/admin/commissions');
    if (is_dir($viewPath)) {
        echo "   ✅ Commission Views: EXISTS\n";
        $files = scandir($viewPath);
        $viewFiles = array_filter($files, function($file) {
            return str_ends_with($file, '.blade.php');
        });
        echo "   📝 View files: " . implode(', ', $viewFiles) . "\n";
    } else {
        echo "   ❌ Commission Views: MISSING\n";
    }
    
    echo "\n5. Checking Database Tables...\n";
    if (\Illuminate\Support\Facades\Schema::hasTable('commissions')) {
        echo "   ✅ Commissions Table: EXISTS\n";
        
        // Check columns
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('commissions');
        $requiredColumns = ['id', 'reference_type', 'reference_id', 'reference_name', 'commission_percentage', 'base_amount', 'commission_amount', 'status', 'notes', 'paid_at', 'paid_by'];
        
        $missingColumns = array_diff($requiredColumns, $columns);
        if (empty($missingColumns)) {
            echo "   ✅ All required columns present\n";
        } else {
            echo "   ⚠️ Missing columns: " . implode(', ', $missingColumns) . "\n";
        }
    } else {
        echo "   ❌ Commissions Table: MISSING\n";
    }
    
    echo "\n==================================================\n";
    echo "COMMISSION SYSTEM ACCESS URLS:\n";
    echo "==================================================\n";
    echo "📊 Commission Management: /admin/commissions\n";
    echo "📊 POS Sales: /admin/pos/sales\n";
    echo "📊 Sales Reports: /admin/reports/sales\n";
    
    echo "\n==================================================\n";
    echo "COMMISSION SYSTEM STATUS: ";
    
    $systemOK = class_exists('\\App\\Models\\Commission') && 
                class_exists('\\App\\Http\\Controllers\\Admin\\CommissionController') &&
                \Illuminate\Support\Facades\Schema::hasTable('commissions');
                
    if ($systemOK) {
        echo "✅ FULLY FUNCTIONAL\n";
        echo "==================================================\n";
        echo "\n🎉 Your commission system is ready to use!\n";
        echo "\n📝 Next Steps:\n";
        echo "1. Access: http://greenvalleyherbs.local:8000/admin/commissions\n";
        echo "2. Create a test POS sale with commission\n";
        echo "3. Verify commission appears in pending status\n";
        echo "4. Test status update buttons\n";
    } else {
        echo "❌ NEEDS ATTENTION\n";
        echo "==================================================\n";
        echo "\n⚠️ Some components are missing. Please check the issues above.\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: {$e->getMessage()}\n";
}

echo "\n";
?>