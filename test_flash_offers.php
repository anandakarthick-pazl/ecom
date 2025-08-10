<?php
/**
 * Flash Offer Testing Script
 * Run this to test flash offer functionality
 * Usage: php test_flash_offers.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Offer;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

echo "🔥 Flash Offer System Test\n\n";

// Test 1: Check existing offers
echo "1. Checking existing offers...\n";
$allOffers = Offer::all();
echo "   Total offers in database: " . $allOffers->count() . "\n";

if ($allOffers->count() > 0) {
    foreach ($allOffers as $offer) {
        echo "   - Offer #{$offer->id}: {$offer->name} (Type: {$offer->type}, Flash: " . ($offer->is_flash_offer ? 'Yes' : 'No') . ")\n";
    }
} else {
    echo "   ❌ No offers found in database\n";
}

// Test 2: Check flash offers specifically
echo "\n2. Checking flash offers...\n";
$flashOffers = Offer::where('is_flash_offer', true)->get();
echo "   Flash offers found: " . $flashOffers->count() . "\n";

if ($flashOffers->count() > 0) {
    foreach ($flashOffers as $offer) {
        $isActive = $offer->isFlashOfferActive();
        echo "   - Flash Offer #{$offer->id}: {$offer->name} (Active: " . ($isActive ? 'Yes' : 'No') . ")\n";
        echo "     Start: " . ($offer->start_date ? $offer->start_date->format('Y-m-d') : 'None') . "\n";
        echo "     End: " . ($offer->end_date ? $offer->end_date->format('Y-m-d') : 'None') . "\n";
        echo "     Show Popup: " . ($offer->show_popup ? 'Yes' : 'No') . "\n";
        if ($offer->banner_image) {
            echo "     Banner: " . $offer->banner_image . "\n";
        }
    }
} else {
    echo "   ❌ No flash offers found\n";
}

// Test 3: Check active flash offers
echo "\n3. Checking active flash offers...\n";
$activeFlashOffers = Offer::activeFlashOffers()->get();
echo "   Active flash offers: " . $activeFlashOffers->count() . "\n";

// Test 4: Create a test flash offer if none exist
if ($flashOffers->count() === 0) {
    echo "\n4. Creating a test flash offer...\n";
    
    try {
        $testOffer = Offer::create([
            'name' => 'Test Flash Sale',
            'code' => 'FLASH' . time(),
            'type' => 'percentage',
            'discount_type' => 'percentage',
            'value' => 25,
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'is_active' => true,
            'is_flash_offer' => true,
            'show_popup' => true,
            'popup_delay' => 3000,
            'show_countdown' => true,
            'banner_title' => 'Flash Sale - 25% Off!',
            'banner_description' => 'Limited time offer! Get 25% off on all products.',
            'banner_button_text' => 'Shop Now',
            'countdown_text' => 'Hurry! Limited time offer'
        ]);
        
        echo "   ✅ Test flash offer created successfully!\n";
        echo "   - ID: {$testOffer->id}\n";
        echo "   - Name: {$testOffer->name}\n";
        echo "   - Discount: {$testOffer->value}%\n";
        echo "   - Valid until: " . $testOffer->end_date->format('Y-m-d H:i:s') . "\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Failed to create test flash offer: " . $e->getMessage() . "\n";
    }
}

// Test 5: Check frontend routes
echo "\n5. Checking routes...\n";
$routes = [
    'shop' => 'Home page',
    'flash.offers' => 'Flash offers page',
    'offer.products' => 'Offer products page'
];

foreach ($routes as $routeName => $description) {
    try {
        $url = route($routeName);
        echo "   ✅ {$description}: {$url}\n";
    } catch (\Exception $e) {
        echo "   ❌ {$description}: Route not found\n";
    }
}

// Test 6: Check views exist
echo "\n6. Checking views...\n";
$views = [
    'flash-offers' => 'Flash offers listing page',
    'components.flash-offer-popup' => 'Flash offer popup component'
];

foreach ($views as $viewName => $description) {
    $viewPath = resource_path('views/' . str_replace('.', '/', $viewName) . '.blade.php');
    if (file_exists($viewPath)) {
        echo "   ✅ {$description}: Found\n";
    } else {
        echo "   ❌ {$description}: Not found at {$viewPath}\n";
    }
}

// Test 7: Check storage directories
echo "\n7. Checking storage setup...\n";
$storagePaths = [
    'storage/app/public/offers/banners' => 'Banner storage directory',
    'public/storage' => 'Storage link'
];

foreach ($storagePaths as $path => $description) {
    $fullPath = base_path($path);
    if (file_exists($fullPath)) {
        echo "   ✅ {$description}: Found at {$fullPath}\n";
    } else {
        echo "   ❌ {$description}: Missing at {$fullPath}\n";
    }
}

// Test 8: Check models and relationships
echo "\n8. Testing model functionality...\n";
try {
    // Test Offer model scopes
    $activeOffers = Offer::active()->count();
    echo "   ✅ Active offers scope: {$activeOffers} offers\n";
    
    $currentOffers = Offer::current()->count();
    echo "   ✅ Current offers scope: {$currentOffers} offers\n";
    
    $flashOfferScope = Offer::flashOffers()->count();
    echo "   ✅ Flash offers scope: {$flashOfferScope} offers\n";
    
    $activeFlashScope = Offer::activeFlashOffers()->count();
    echo "   ✅ Active flash offers scope: {$activeFlashScope} offers\n";
    
} catch (\Exception $e) {
    echo "   ❌ Model testing failed: " . $e->getMessage() . "\n";
}

// Test 9: Check flash offer methods
echo "\n9. Testing flash offer methods...\n";
$testOffer = Offer::where('is_flash_offer', true)->first();

if ($testOffer) {
    try {
        $isActive = $testOffer->isFlashOfferActive();
        echo "   ✅ isFlashOfferActive(): " . ($isActive ? 'true' : 'false') . "\n";
        
        $timeRemaining = $testOffer->getTimeRemaining();
        if (isset($timeRemaining['expired'])) {
            echo "   ✅ getTimeRemaining(): " . ($timeRemaining['expired'] ? 'Expired' : 'Active') . "\n";
            if (!$timeRemaining['expired']) {
                echo "     Days: {$timeRemaining['days']}, Hours: {$timeRemaining['hours']}, Minutes: {$timeRemaining['minutes']}\n";
            }
        }
        
        $discountDisplay = $testOffer->discount_value_display;
        echo "   ✅ discount_value_display: {$discountDisplay}\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Flash offer method testing failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ⚠️  No flash offers found to test methods\n";
}

echo "\n🎉 Flash Offer System Test Complete!\n\n";

echo "Next Steps:\n";
echo "1. Visit http://greenvalleyherbs.local:8000/admin/offers to create flash offers\n";
echo "2. Visit http://greenvalleyherbs.local:8000/flash-offers to see flash offers page\n";
echo "3. Visit http://greenvalleyherbs.local:8000/shop to see flash offer popup\n";
echo "4. Check the navigation for the new ⚡ Flash Offers link\n\n";

// Show admin URL for creating offers
echo "Admin Panel URLs:\n";
echo "- Create Offer: http://greenvalleyherbs.local:8000/admin/offers/create\n";
echo "- Manage Offers: http://greenvalleyherbs.local:8000/admin/offers\n\n";

// Show instructions for creating flash offers
echo "To create a Flash Offer in Admin:\n";
echo "1. Go to Admin > Offers > Create\n";
echo "2. Set Type to 'Flash' or check 'Flash Offer'\n";
echo "3. Fill in discount details\n";
echo "4. Upload a banner image (optional)\n";
echo "5. Enable 'Show Popup' for popup display\n";
echo "6. Enable 'Show Countdown' for timer\n";
echo "7. Set start and end dates\n";
echo "8. Save the offer\n";

echo "\nDone! 🚀\n";
?>
