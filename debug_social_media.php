<?php
// Social Media Routes Verification Script
// Run this with: php artisan tinker

echo "=== SOCIAL MEDIA ROUTES DEBUG ===\n\n";

try {
    // 1. Check if routes are registered
    echo "1. Checking if social media routes are registered...\n";
    
    try {
        $indexRoute = route('admin.social-media.index');
        echo "✅ admin.social-media.index route exists: {$indexRoute}\n";
    } catch (Exception $e) {
        echo "❌ admin.social-media.index route missing: " . $e->getMessage() . "\n";
    }
    
    try {
        $createRoute = route('admin.social-media.create');
        echo "✅ admin.social-media.create route exists: {$createRoute}\n";
    } catch (Exception $e) {
        echo "❌ admin.social-media.create route missing: " . $e->getMessage() . "\n";
    }
    
    // 2. Check database table
    echo "\n2. Checking database table...\n";
    
    if (Schema::hasTable('social_media_links')) {
        echo "✅ social_media_links table exists\n";
        
        $count = DB::table('social_media_links')->count();
        echo "📊 Total social media links: {$count}\n";
        
        if ($count > 0) {
            $links = DB::table('social_media_links')->get();
            foreach ($links as $link) {
                echo "  - {$link->name}: {$link->url} (Company: {$link->company_id})\n";
            }
        }
        
    } else {
        echo "❌ social_media_links table does not exist\n";
        echo "💡 Run: php artisan migrate\n";
    }
    
    // 3. Check model
    echo "\n3. Checking model...\n";
    
    if (class_exists('App\\Models\\SocialMediaLink')) {
        echo "✅ SocialMediaLink model exists\n";
        
        try {
            $model = new \App\Models\SocialMediaLink();
            echo "✅ Model can be instantiated\n";
            
            $fillable = $model->getFillable();
            echo "📝 Fillable fields: " . implode(', ', $fillable) . "\n";
            
        } catch (Exception $e) {
            echo "❌ Model error: " . $e->getMessage() . "\n";
        }
        
    } else {
        echo "❌ SocialMediaLink model not found\n";
    }
    
    // 4. Check controller
    echo "\n4. Checking controller...\n";
    
    if (class_exists('App\\Http\\Controllers\\Admin\\SocialMediaController')) {
        echo "✅ SocialMediaController exists\n";
        
        $controller = new \App\Http\Controllers\Admin\SocialMediaController();
        echo "✅ Controller can be instantiated\n";
        
    } else {
        echo "❌ SocialMediaController not found\n";
    }
    
    // 5. Check views
    echo "\n5. Checking views...\n";
    
    $viewPaths = [
        'admin.social-media.index',
        'admin.social-media.create', 
        'admin.social-media.edit'
    ];
    
    foreach ($viewPaths as $viewPath) {
        if (view()->exists($viewPath)) {
            echo "✅ View {$viewPath} exists\n";
        } else {
            echo "❌ View {$viewPath} missing\n";
        }
    }
    
    // 6. Quick fix suggestions
    echo "\n=== QUICK FIXES ===\n";
    
    if (!Schema::hasTable('social_media_links')) {
        echo "1. Run migration: php artisan migrate\n";
    }
    
    echo "2. Clear route cache: php artisan route:clear\n";
    echo "3. Clear config cache: php artisan config:clear\n";
    echo "4. Clear view cache: php artisan view:clear\n";
    
    // 7. Test creating a sample social media link
    echo "\n6. Testing model functionality...\n";
    
    if (Schema::hasTable('social_media_links')) {
        try {
            // Find a company to test with
            $company = \App\Models\SuperAdmin\Company::first();
            
            if ($company) {
                echo "✅ Found test company: {$company->name} (ID: {$company->id})\n";
                
                // Try to create a test social media link
                $testLink = \App\Models\SocialMediaLink::updateOrCreate([
                    'company_id' => $company->id,
                    'name' => 'Facebook'
                ], [
                    'icon_class' => 'fab fa-facebook-f',
                    'url' => 'https://facebook.com/testpage',
                    'color' => '#1877f2',
                    'sort_order' => 1,
                    'is_active' => true
                ]);
                
                echo "✅ Test social media link created/updated: {$testLink->name}\n";
                
            } else {
                echo "❌ No companies found for testing\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Model test failed: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== END DEBUG ===\n";
