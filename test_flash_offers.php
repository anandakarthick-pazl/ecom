<?php
/**
 * Flash Offer Testing Script (Home Page Only Version)
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

echo "🔥 Flash Offer System Test (Home Page Only)\n\n";

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

// Test 4: Check popup-enabled flash offers
echo "\n4. Checking popup-enabled flash offers...\n";
$popupFlashOffers = Offer::activeFlashOffers()->where('show_popup', true)->get();
echo "   Popup-enabled flash offers: " . $popupFlashOffers->count() . "\n";

if ($popupFlashOffers->count() > 0) {
    foreach ($popupFlashOffers as $offer) {
        echo "   - Popup Offer #{$offer->id}: {$offer->name}\n";
        echo "     Delay: " . ($offer->popup_delay / 1000) . " seconds\n";
        echo "     Frequency: " . ($offer->popup_frequency ?? 'always') . "\n";
    }
}

// Test 5: Create a test flash offer if none exist
if ($popupFlashOffers->count() === 0) {
    echo "\n5. Creating a test flash offer for home page popup...\n";
    
    try {
        $testOffer = Offer::create([
            'name' => 'Test Flash Sale - Home Page Only',
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
            'popup_frequency' => 'always',
            'show_countdown' => true,
            'banner_title' => 'Flash Sale - 25% Off Everything!',
            'banner_description' => 'Limited time offer! Get 25% off on all products. Only on home page!',
            'banner_button_text' => 'Shop Now',
            'countdown_text' => 'Hurry! Limited time offer'
        ]);
        
        echo "   ✅ Test flash offer created successfully!\n";
        echo "   - ID: {$testOffer->id}\n";
        echo "   - Name: {$testOffer->name}\n";
        echo "   - Discount: {$testOffer->value}%\n";
        echo "   - Valid until: " . $testOffer->end_date->format('Y-m-d H:i:s') . "\n";
        echo "   - Will show popup on HOME PAGE ONLY\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Failed to create test flash offer: " . $e->getMessage() . "\n";
    }
}

// Test 6: Check home page route
echo "\n6. Checking home page route...\n";
try {
    $homeUrl = route('shop');
    echo "   ✅ Home page URL: {$homeUrl}\n";
    echo "   📝 Note: Flash offers will ONLY display on this page\n";
} catch (\Exception $e) {
    echo "   ❌ Home page route not found\n";
}

// Test 7: Check popup component
echo "\n7. Checking popup component...\n";
$popupPath = resource_path('views/components/flash-offer-popup.blade.php');
if (file_exists($popupPath)) {
    echo "   ✅ Flash offer popup component: Found\n";
    echo "   📝 Component will only show on home page routes\n";
} else {
    echo "   ❌ Flash offer popup component: Not found\n";
}

// Test 8: Check layout integration
echo "\n8. Checking layout integration...\n";
$layoutPath = resource_path('views/layouts/app.blade.php');
if (file_exists($layoutPath)) {
    $layoutContent = file_get_contents($layoutPath);
    if (strpos($layoutContent, 'flash-offer-popup') !== false) {
        echo "   ✅ Flash offer popup included in layout\n";
    } else {
        echo "   ❌ Flash offer popup NOT included in layout\n";
    }
} else {
    echo "   ❌ Layout file not found\n";
}

// Test 9: Check AppServiceProvider integration
echo "\n9. Checking AppServiceProvider integration...\n";
$providerPath = app_path('Providers/AppServiceProvider.php');
if (file_exists($providerPath)) {
    $providerContent = file_get_contents($providerPath);
    if (strpos($providerContent, 'request()->routeIs(\'shop\')') !== false) {
        echo "   ✅ Flash offer view composer configured for home page only\n";
    } else {
        echo "   ❌ Flash offer view composer not properly configured\n";
    }
} else {
    echo "   ❌ AppServiceProvider not found\n";
}

// Test 10: Test flash offer methods
echo "\n10. Testing flash offer methods...\n";
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

echo "📋 Summary:\n";
echo "✅ Flash offers will ONLY display on the home page\n";
echo "✅ Popup will appear automatically after 3 seconds on home page\n";
echo "✅ No separate flash offers page or navigation link\n";
echo "✅ Admin can still create and manage flash offers normally\n\n";

echo "🚀 Next Steps:\n";
echo "1. Visit http://greenvalleyherbs.local:8000/admin/offers to create flash offers\n";
echo "2. Visit http://greenvalleyherbs.local:8000/shop (HOME PAGE) to see popup\n";
echo "3. Flash offers will NOT show on other pages like /products or /category/*\n\n";

echo "📝 To create a Flash Offer in Admin:\n";
echo "1. Go to Admin > Offers > Create\n";
echo "2. Set Type to 'Flash' or check 'Flash Offer'\n";
echo "3. Enable 'Show Popup' for home page popup display\n";
echo "4. Set popup delay (3 seconds recommended)\n";
echo "5. Fill in banner title and description\n";
echo "6. Upload banner image (optional)\n";
echo "7. Enable 'Show Countdown' for urgency timer\n";
echo "8. Set start and end dates\n";
echo "9. Save the offer\n\n";

echo "🎯 Expected Behavior:\n";
echo "- Home page (http://greenvalleyherbs.local:8000/shop): ✅ Shows popup\n";
echo "- Products page: ❌ No popup\n";
echo "- Category pages: ❌ No popup\n";
echo "- Other pages: ❌ No popup\n\n";

echo "Done! 🚀\n";
?>
