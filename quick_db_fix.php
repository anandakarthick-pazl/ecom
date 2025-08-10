<?php
// Quick database fix for banner paths
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Banner;

echo "Fixing banner database records...\n";

$banners = Banner::all();
foreach ($banners as $banner) {
    if ($banner->image) {
        $originalImage = $banner->image;
        $filename = basename($banner->image);
        
        if ($originalImage !== $filename) {
            $banner->update(['image' => $filename]);
            echo "Updated banner ID {$banner->id}: {$originalImage} -> {$filename}\n";
        } else {
            echo "Banner ID {$banner->id} already correct: {$filename}\n";
        }
    }
}

echo "Database fix completed!\n";
?>
