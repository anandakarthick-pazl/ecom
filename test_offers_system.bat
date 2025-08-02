@echo off
echo Testing current offer system status...

cd /d "D:\source_code\ecom"

echo.
echo Quick diagnostic test...
php artisan tinker --execute="
try {
    echo '=== OFFER SYSTEM DIAGNOSTIC ===' . PHP_EOL;
    
    // Test 1: Check if Offer model works
    echo '1. Testing Offer model...' . PHP_EOL;
    \$offerCount = \App\Models\Offer::count();
    echo '   Total offers in database: ' . \$offerCount . PHP_EOL;
    
    // Test 2: Check if scopes work
    echo '2. Testing Offer scopes...' . PHP_EOL;
    \$activeCount = \App\Models\Offer::active()->count();
    echo '   Active offers: ' . \$activeCount . PHP_EOL;
    
    \$currentCount = \App\Models\Offer::current()->count();
    echo '   Current offers: ' . \$currentCount . PHP_EOL;
    
    // Test 3: Check category offers
    echo '3. Testing category offers...' . PHP_EOL;
    \$categoryOffers = \App\Models\Offer::where('type', 'category')->count();
    echo '   Category offers: ' . \$categoryOffers . PHP_EOL;
    
    // Test 4: Check sound-crackers category
    echo '4. Testing sound-crackers category...' . PHP_EOL;
    \$category = \App\Models\Category::where('slug', 'sound-crackers')->first();
    if(\$category) {
        echo '   Category found: ' . \$category->name . ' (ID: ' . \$category->id . ')' . PHP_EOL;
        \$productsCount = \$category->products()->count();
        echo '   Products in category: ' . \$productsCount . PHP_EOL;
        
        \$categoryOffersCount = \App\Models\Offer::where('type', 'category')
                                                ->where('category_id', \$category->id)
                                                ->where('is_active', true)
                                                ->count();
        echo '   Active category offers: ' . \$categoryOffersCount . PHP_EOL;
    } else {
        echo '   Category not found!' . PHP_EOL;
    }
    
    // Test 5: Test OfferService
    echo '5. Testing OfferService...' . PHP_EOL;
    \$service = new \App\Services\OfferService();
    echo '   OfferService created successfully!' . PHP_EOL;
    
    echo PHP_EOL . '=== ALL TESTS COMPLETED ===' . PHP_EOL;
    echo 'If you see this message, the scope error has been fixed!' . PHP_EOL;
    
} catch(\Exception \$e) {
    echo 'ERROR: ' . \$e->getMessage() . PHP_EOL;
    echo 'Line: ' . \$e->getLine() . PHP_EOL;
    echo 'File: ' . \$e->getFile() . PHP_EOL;
}
"

echo.
echo ========================================
echo DIAGNOSTIC COMPLETED
echo ========================================
echo.
echo If the diagnostic shows no errors, you can now:
echo 1. Visit: http://greenvalleyherbs.local:8000/offer-products
echo 2. Visit: http://greenvalleyherbs.local:8000/category/sound-crackers
echo.
echo Both pages should work without the scope method error.
echo.

pause
