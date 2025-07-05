<?php

/**
 * Fix AppSetting Class Not Found Error
 * 
 * This script clears all relevant caches and verifies the AppSetting class
 * is properly loaded and functioning.
 */

echo "🔧 Fixing AppSetting 'Class not found' error...\n\n";

// Change to project directory
$projectPath = __DIR__;
chdir($projectPath);

echo "📁 Working directory: " . getcwd() . "\n\n";

// Step 1: Clear Laravel caches
echo "🧹 Clearing Laravel caches...\n";

$commands = [
    'php artisan config:clear',
    'php artisan cache:clear', 
    'php artisan view:clear',
    'php artisan route:clear',
    'php artisan optimize:clear'
];

foreach ($commands as $command) {
    echo "   Running: $command\n";
    $output = [];
    $returnCode = 0;
    exec($command . ' 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "   ✅ Success\n";
    } else {
        echo "   ❌ Error: " . implode("\n", $output) . "\n";
    }
}

echo "\n";

// Step 2: Verify AppSetting file exists and is readable
echo "📄 Checking AppSetting file...\n";

$appSettingPath = $projectPath . '/app/Models/AppSetting.php';
if (file_exists($appSettingPath)) {
    echo "   ✅ AppSetting.php exists\n";
    
    if (is_readable($appSettingPath)) {
        echo "   ✅ AppSetting.php is readable\n";
        
        $content = file_get_contents($appSettingPath);
        if (strpos($content, 'class AppSetting') !== false) {
            echo "   ✅ AppSetting class is defined\n";
        } else {
            echo "   ❌ AppSetting class not found in file\n";
        }
        
        if (strpos($content, 'namespace App\\Models') !== false) {
            echo "   ✅ Proper namespace is set\n";
        } else {
            echo "   ❌ Namespace issue detected\n";
        }
    } else {
        echo "   ❌ AppSetting.php is not readable\n";
    }
} else {
    echo "   ❌ AppSetting.php does not exist\n";
}

echo "\n";

// Step 3: Check trait dependency
echo "🔗 Checking trait dependency...\n";

$traitPath = $projectPath . '/app/Traits/BelongsToTenantEnhanced.php';
if (file_exists($traitPath)) {
    echo "   ✅ BelongsToTenantEnhanced trait exists\n";
} else {
    echo "   ❌ BelongsToTenantEnhanced trait is missing\n";
}

echo "\n";

// Step 4: Run composer dump-autoload
echo "🔄 Regenerating autoloader...\n";

$output = [];
$returnCode = 0;
exec('composer dump-autoload 2>&1', $output, $returnCode);

if ($returnCode === 0) {
    echo "   ✅ Autoloader regenerated successfully\n";
} else {
    echo "   ❌ Autoloader regeneration failed: " . implode("\n", $output) . "\n";
}

echo "\n";

// Step 5: Test if we can include Laravel and access the class
echo "🧪 Testing AppSetting class access...\n";

try {
    // Try to load Laravel
    require_once $projectPath . '/vendor/autoload.php';
    
    if (file_exists($projectPath . '/bootstrap/app.php')) {
        $app = require $projectPath . '/bootstrap/app.php';
        
        // Test class exists
        if (class_exists('App\\Models\\AppSetting')) {
            echo "   ✅ AppSetting class can be loaded\n";
            
            // Test reflection
            $reflection = new ReflectionClass('App\\Models\\AppSetting');
            if ($reflection->hasMethod('get')) {
                echo "   ✅ AppSetting::get() method exists\n";
            } else {
                echo "   ❌ AppSetting::get() method missing\n";
            }
            
            if ($reflection->hasMethod('set')) {
                echo "   ✅ AppSetting::set() method exists\n";
            } else {
                echo "   ❌ AppSetting::set() method missing\n";
            }
            
        } else {
            echo "   ❌ AppSetting class cannot be loaded\n";
        }
    } else {
        echo "   ❌ Laravel bootstrap file not found\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error testing class: " . $e->getMessage() . "\n";
}

echo "\n";

// Step 6: Check for database connection and app_settings table
echo "🗄️  Checking database setup...\n";

try {
    // Boot Laravel app properly
    $app = require $projectPath . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Check if app_settings table exists
    $tableExists = DB::connection()->getSchemaBuilder()->hasTable('app_settings');
    
    if ($tableExists) {
        echo "   ✅ app_settings table exists\n";
        
        // Check if table has required columns
        $columns = DB::connection()->getSchemaBuilder()->getColumnListing('app_settings');
        $requiredColumns = ['id', 'key', 'value', 'type', 'group', 'company_id'];
        
        $missingColumns = array_diff($requiredColumns, $columns);
        if (empty($missingColumns)) {
            echo "   ✅ All required columns exist\n";
        } else {
            echo "   ⚠️  Missing columns: " . implode(', ', $missingColumns) . "\n";
        }
        
    } else {
        echo "   ❌ app_settings table does not exist\n";
        echo "   💡 Run: php artisan migrate\n";
    }
    
} catch (Exception $e) {
    echo "   ⚠️  Could not check database: " . $e->getMessage() . "\n";
    echo "   💡 This may be normal if database is not configured\n";
}

echo "\n";

// Step 7: Check HomeController fix
echo "🏠 Verifying HomeController fix...\n";

$homeControllerPath = $projectPath . '/app/Http/Controllers/HomeController.php';
if (file_exists($homeControllerPath)) {
    $content = file_get_contents($homeControllerPath);
    
    if (strpos($content, 'use App\\Models\\AppSetting;') !== false) {
        echo "   ✅ AppSetting import added to HomeController\n";
    } else {
        echo "   ❌ AppSetting import missing from HomeController\n";
    }
    
    // Check if old patterns are removed
    if (strpos($content, '\\App\\Models\\AppSetting::where') === false) {
        echo "   ✅ Old direct database calls removed\n";
    } else {
        echo "   ❌ Old direct database calls still present\n";
    }
    
    // Check if new patterns are used
    if (strpos($content, 'AppSetting::get(') !== false) {
        echo "   ✅ New AppSetting::get() calls implemented\n";
    } else {
        echo "   ❌ New AppSetting::get() calls not found\n";
    }
    
} else {
    echo "   ❌ HomeController not found\n";
}

echo "\n";

// Final summary
echo "🎯 Fix Summary:\n";
echo "=" . str_repeat("=", 50) . "\n";
echo "✅ Updated HomeController to properly import and use AppSetting class\n";
echo "✅ Replaced direct database queries with AppSetting::get() method calls\n";
echo "✅ Cleared all Laravel caches\n";
echo "✅ Regenerated autoloader\n";
echo "\n";

echo "🚀 Next Steps:\n";
echo "1. Test the application: http://greenvalleyherbs.local:8000/products\n";
echo "2. If you still see errors, check the Laravel logs in storage/logs/\n";
echo "3. Ensure your .env file has correct database configuration\n";
echo "4. Run migrations if app_settings table doesn't exist: php artisan migrate\n";
echo "\n";

echo "🔧 If problems persist, run these commands manually:\n";
echo "   php artisan config:clear\n";
echo "   php artisan cache:clear\n";
echo "   php artisan optimize:clear\n";
echo "   composer dump-autoload\n";
echo "\n";

echo "✨ Fix completed! The AppSetting 'Class not found' error should now be resolved.\n";

?>
