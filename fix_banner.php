<?php
/**
 * Banner Fix Script - Run this once to fix the banner issue
 * Usage: php fix_banner.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

echo "🔧 Starting banner fix...\n\n";

// Step 1: Update banner records in database
echo "Step 1: Updating banner records in database...\n";

$banners = Banner::all();
$updatedCount = 0;

foreach ($banners as $banner) {
    if ($banner->image) {
        $filename = basename($banner->image);
        
        // Update to store just the filename
        $banner->update(['image' => $filename]);
        
        echo "  ✓ Updated banner ID {$banner->id}: {$filename}\n";
        $updatedCount++;
    }
}

echo "Updated {$updatedCount} banner records.\n\n";

// Step 2: Verify file exists in correct location
echo "Step 2: Verifying files in correct location...\n";

foreach ($banners as $banner) {
    if ($banner->image) {
        $filename = basename($banner->image);
        $correctPath = storage_path('app/public/banner/banners/' . $filename);
        
        if (file_exists($correctPath)) {
            echo "  ✓ File exists: {$filename}\n";
        } else {
            echo "  ✗ File missing: {$filename}\n";
        }
    }
}

// Step 3: Create storage link if it doesn't exist
echo "\nStep 3: Checking storage link...\n";

$storageLink = public_path('storage');
if (!file_exists($storageLink)) {
    echo "  Creating storage link...\n";
    // Create the link
    $target = storage_path('app/public');
    
    if (PHP_OS_FAMILY === 'Windows') {
        $command = 'mklink /D "' . $storageLink . '" "' . $target . '"';
        shell_exec($command);
    } else {
        symlink($target, $storageLink);
    }
    
    if (file_exists($storageLink)) {
        echo "  ✓ Storage link created successfully\n";
    } else {
        echo "  ✗ Failed to create storage link. Please run: php artisan storage:link\n";
    }
} else {
    echo "  ✓ Storage link already exists\n";
}

// Step 4: Test banner URL generation
echo "\nStep 4: Testing banner URL generation...\n";

foreach ($banners as $banner) {
    if ($banner->image) {
        $url = $banner->image_url;
        echo "  Banner '{$banner->title}': {$url}\n";
        break; // Just test the first one
    }
}

echo "\n🎉 Banner fix completed!\n";
echo "\nNext steps:\n";
echo "1. Visit your admin panel: http://greenvalleyherbs.local:8000/admin/banners\n";
echo "2. Check if banner images are now displaying\n";
echo "3. Try uploading a new banner to test the fixed upload functionality\n";

if (!file_exists($storageLink)) {
    echo "\n⚠️  If images still don't show, run this command:\n";
    echo "php artisan storage:link\n";
}

echo "\nAll done! 🚀\n";
?>
