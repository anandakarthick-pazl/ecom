<?php
// Social Media Diagnostic Script
// Save this as diagnostic.php in your project root and run: php diagnostic.php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 SOCIAL MEDIA FUNCTIONALITY DIAGNOSTIC\n";
echo "=========================================\n\n";

try {
    // 1. Check Environment
    echo "1. ENVIRONMENT CHECK\n";
    echo "-------------------\n";
    echo "Laravel Version: " . app()->version() . "\n";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "Environment: " . config('app.env') . "\n";
    echo "Debug Mode: " . (config('app.debug') ? 'ON' : 'OFF') . "\n";
    echo "Database: " . config('database.default') . "\n\n";

    // 2. Check Routes
    echo "2. ROUTES CHECK\n";
    echo "---------------\n";
    
    $socialMediaRoutes = [
        'admin.social-media.index',
        'admin.social-media.create', 
        'admin.social-media.store',
        'admin.social-media.quick-add',
        'admin.social-media.toggle-status',
        'admin.social-media.update-sort-order'
    ];
    
    foreach ($socialMediaRoutes as $routeName) {
        try {
            $url = route($routeName);
            echo "✅ $routeName: $url\n";
        } catch (Exception $e) {
            echo "❌ $routeName: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";

    // 3. Check Database
    echo "3. DATABASE CHECK\n";
    echo "-----------------\n";
    
    // Check if table exists
    if (Schema::hasTable('social_media_links')) {
        echo "✅ social_media_links table exists\n";
        
        // Check table structure
        $columns = DB::select("DESCRIBE social_media_links");
        echo "📋 Table columns:\n";
        foreach ($columns as $column) {
            echo "   - {$column->Field} ({$column->Type})\n";
        }
        
        // Check data
        $count = DB::table('social_media_links')->count();
        echo "📊 Total records: $count\n";
        
        if ($count > 0) {
            $sample = DB::table('social_media_links')->first();
            echo "📝 Sample record:\n";
            echo "   - ID: {$sample->id}\n";
            echo "   - Name: {$sample->name}\n";
            echo "   - Company ID: {$sample->company_id}\n";
            echo "   - URL: {$sample->url}\n";
            echo "   - Active: " . ($sample->is_active ? 'Yes' : 'No') . "\n";
        }
    } else {
        echo "❌ social_media_links table does not exist\n";
        echo "💡 Solution: Run 'php artisan migrate'\n";
    }
    echo "\n";

    // 4. Check Companies
    echo "4. COMPANIES CHECK\n";
    echo "------------------\n";
    
    if (Schema::hasTable('companies')) {
        $companyCount = DB::table('companies')->count();
        echo "🏢 Total companies: $companyCount\n";
        
        if ($companyCount > 0) {
            $companies = DB::table('companies')->get(['id', 'name', 'domain']);
            foreach ($companies as $company) {
                echo "   - {$company->name} (ID: {$company->id}, Domain: {$company->domain})\n";
                
                // Check social media links for this company
                $linkCount = DB::table('social_media_links')
                    ->where('company_id', $company->id)
                    ->count();
                echo "     📱 Social media links: $linkCount\n";
            }
        }
    } else {
        echo "❌ companies table does not exist\n";
    }
    echo "\n";

    // 5. Check Models
    echo "5. MODELS CHECK\n";
    echo "---------------\n";
    
    if (class_exists('App\\Models\\SocialMediaLink')) {
        echo "✅ SocialMediaLink model exists\n";
        
        try {
            $model = new App\Models\SocialMediaLink();
            echo "✅ Model can be instantiated\n";
            echo "📝 Fillable fields: " . implode(', ', $model->getFillable()) . "\n";
            
            // Test predefined platforms
            $platforms = App\Models\SocialMediaLink::getPredefinedPlatforms();
            echo "🎨 Predefined platforms: " . count($platforms) . "\n";
            foreach (array_keys($platforms) as $platform) {
                echo "   - $platform\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Model error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ SocialMediaLink model not found\n";
    }
    echo "\n";

    // 6. Check Controller
    echo "6. CONTROLLER CHECK\n";
    echo "-------------------\n";
    
    if (class_exists('App\\Http\\Controllers\\Admin\\SocialMediaController')) {
        echo "✅ SocialMediaController exists\n";
        
        try {
            $controller = new App\Http\Controllers\Admin\SocialMediaController();
            echo "✅ Controller can be instantiated\n";
            
            // Check if methods exist
            $methods = ['index', 'create', 'store', 'edit', 'update', 'destroy', 'quickAdd', 'toggleStatus'];
            foreach ($methods as $method) {
                if (method_exists($controller, $method)) {
                    echo "✅ Method $method exists\n";
                } else {
                    echo "❌ Method $method missing\n";
                }
            }
            
        } catch (Exception $e) {
            echo "❌ Controller error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ SocialMediaController not found\n";
    }
    echo "\n";

    // 7. Check Views
    echo "7. VIEWS CHECK\n";
    echo "--------------\n";
    
    $viewPaths = [
        'admin.social-media.index',
        'admin.social-media.create', 
        'admin.social-media.edit'
    ];
    
    foreach ($viewPaths as $viewPath) {
        if (view()->exists($viewPath)) {
            echo "✅ View $viewPath exists\n";
        } else {
            echo "❌ View $viewPath missing\n";
        }
    }
    echo "\n";

    // 8. Check Authentication
    echo "8. AUTHENTICATION CHECK\n";
    echo "------------------------\n";
    
    if (Schema::hasTable('users')) {
        $userCount = DB::table('users')->count();
        echo "👥 Total users: $userCount\n";
        
        if ($userCount > 0) {
            $adminUsers = DB::table('users')
                ->whereIn('role', ['admin', 'manager', 'super_admin'])
                ->get(['email', 'role', 'company_id']);
                
            echo "👨‍💼 Admin users:\n";
            foreach ($adminUsers as $user) {
                echo "   - {$user->email} (Role: {$user->role}, Company: {$user->company_id})\n";
            }
        }
    }
    echo "\n";

    // 9. Suggest Fixes
    echo "9. SUGGESTED FIXES\n";
    echo "------------------\n";
    
    $fixes = [];
    
    // Check common issues
    if (!Schema::hasTable('social_media_links')) {
        $fixes[] = "Run migration: php artisan migrate";
    }
    
    if (DB::table('social_media_links')->count() == 0 && DB::table('companies')->count() > 0) {
        $fixes[] = "Create sample social media data for testing";
    }
    
    $fixes[] = "Clear all caches: php artisan optimize:clear";
    $fixes[] = "Check Laravel logs: tail -f storage/logs/laravel.log";
    $fixes[] = "Check browser console for JavaScript errors";
    $fixes[] = "Verify user is logged in with correct company context";
    
    foreach ($fixes as $i => $fix) {
        echo ($i + 1) . ". $fix\n";
    }
    echo "\n";

    // 10. Test Data Creation
    echo "10. TEST DATA CREATION\n";
    echo "----------------------\n";
    
    if (Schema::hasTable('social_media_links') && Schema::hasTable('companies')) {
        $company = DB::table('companies')->first();
        
        if ($company) {
            echo "Creating test data for company: {$company->name}\n";
            
            try {
                $testData = [
                    'company_id' => $company->id,
                    'name' => 'Facebook Test',
                    'icon_class' => 'fab fa-facebook-f',
                    'url' => 'https://facebook.com/testpage',
                    'color' => '#1877f2',
                    'sort_order' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                DB::table('social_media_links')->updateOrInsert(
                    ['company_id' => $company->id, 'name' => 'Facebook Test'],
                    $testData
                );
                
                echo "✅ Test Facebook link created/updated\n";
                
            } catch (Exception $e) {
                echo "❌ Error creating test data: " . $e->getMessage() . "\n";
            }
        } else {
            echo "❌ No companies found for test data creation\n";
        }
    }
    echo "\n";

    echo "🎉 DIAGNOSTIC COMPLETE!\n";
    echo "========================\n";
    echo "Check the results above and apply the suggested fixes.\n";
    echo "If issues persist, check the Laravel logs for detailed error messages.\n";

} catch (Exception $e) {
    echo "💥 DIAGNOSTIC ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
