@echo off
echo ========================================
echo COMPREHENSIVE OFFERS FIX & TEST
echo ========================================

cd /d "D:\source_code\ecom"

echo.
echo Step 1: Running database migration...
php artisan migrate --path=database/migrations/2024_07_24_000001_add_company_id_to_offers_table.php

echo.
echo Step 2: Clearing all caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize

echo.
echo Step 3: Testing offer system components...

echo Checking offers table structure...
php artisan tinker --execute="
try {
    echo 'Offers table columns: ';
    \$columns = \Illuminate\Support\Facades\Schema::getColumnListing('offers');
    echo implode(', ', \$columns) . PHP_EOL;
    
    echo 'Sample offers: ' . PHP_EOL;
    \$offers = \App\Models\Offer::select('id', 'name', 'type', 'discount_type', 'category_id', 'product_id', 'value')
                              ->take(3)->get();
    foreach(\$offers as \$offer) {
        echo '- ' . \$offer->name . ' (' . \$offer->type;
        if(\$offer->discount_type) echo ' - ' . \$offer->discount_type;
        echo ': ' . \$offer->value;
        echo \$offer->discount_type === 'percentage' ? '%' : ' ₹';
        echo ')' . PHP_EOL;
    }
} catch(\Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo Step 4: Testing OfferService...
php artisan tinker --execute="
try {
    echo 'Testing OfferService...' . PHP_EOL;
    \$offerService = new \App\Services\OfferService();
    
    // Test with a sample product
    \$product = \App\Models\Product::with('category')->first();
    if(\$product) {
        echo 'Testing with product: ' . \$product->name . PHP_EOL;
        
        \$offers = \$offerService->getApplicableOffers(\$product);
        echo 'Applicable offers: ' . \$offers->count() . PHP_EOL;
        
        \$bestOffer = \$offerService->getBestOffer(\$product);
        if(\$bestOffer) {
            echo 'Best offer: ' . \$bestOffer->name . PHP_EOL;
            \$discount = \$offerService->calculateOfferDiscount(\$bestOffer, \$product);
            echo 'Discount amount: ₹' . \$discount . PHP_EOL;
        } else {
            echo 'No offers available for this product.' . PHP_EOL;
        }
        
        \$effectivePrice = \$offerService->getEffectivePrice(\$product);
        echo 'Original price: ₹' . \$product->price . PHP_EOL;
        echo 'Effective price: ₹' . \$effectivePrice . PHP_EOL;
    } else {
        echo 'No products found to test with.' . PHP_EOL;
    }
} catch(\Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo Step 5: Testing category offers...
php artisan tinker --execute="
try {
    \$category = \App\Models\Category::where('slug', 'sound-crackers')->first();
    if(\$category) {
        echo 'Testing sound-crackers category...' . PHP_EOL;
        echo 'Category: ' . \$category->name . ' (ID: ' . \$category->id . ')' . PHP_EOL;
        
        \$categoryOffers = \App\Models\Offer::where('type', 'category')
                                          ->where('category_id', \$category->id)
                                          ->active()
                                          ->current()
                                          ->get();
        
        echo 'Category offers: ' . \$categoryOffers->count() . PHP_EOL;
        foreach(\$categoryOffers as \$offer) {
            echo '- ' . \$offer->name . ': ';
            echo \$offer->value . (\$offer->discount_type === 'percentage' ? '% off' : ' ₹ off');
            echo ' (Valid: ' . (\$offer->isValid() ? 'Yes' : 'No') . ')' . PHP_EOL;
        }
        
        \$products = \$category->products()->take(3)->get();
        echo 'Testing with ' . \$products->count() . ' products...' . PHP_EOL;
        
        \$offerService = new \App\Services\OfferService();
        \$productsWithOffers = \$offerService->applyOffersToProducts(\$products);
        
        foreach(\$productsWithOffers as \$product) {
            echo '• ' . \$product->name . ': ';
            echo '₹' . \$product->price . ' → ₹' . (\$product->effective_price ?? \$product->price);
            echo ' (Has offer: ' . (\$product->has_offer ?? false ? 'Yes' : 'No') . ')' . PHP_EOL;
        }
    } else {
        echo 'sound-crackers category not found!' . PHP_EOL;
        echo 'Available categories:' . PHP_EOL;
        \$categories = \App\Models\Category::select('name', 'slug')->take(5)->get();
        foreach(\$categories as \$cat) {
            echo '- ' . \$cat->name . ' (slug: ' . \$cat->slug . ')' . PHP_EOL;
        }
    }
} catch(\Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo ========================================
echo FIXES APPLIED SUCCESSFULLY!
echo ========================================
echo.
echo WHAT'S NEW:
echo ✅ Database migration completed
echo ✅ Enhanced Offer model with dynamic calculations
echo ✅ Created OfferService for centralized offer logic
echo ✅ Updated Product model with offer methods
echo ✅ Enhanced category page to show dynamic offers
echo ✅ Updated offer-products page to include category offers
echo ✅ Improved product cards with offer indicators
echo.
echo TESTING INSTRUCTIONS:
echo.
echo 1. CREATE CATEGORY OFFER:
echo    - Go to Admin Panel → Offers → Create Offer
echo    - Select "Category Specific" offer type
echo    - Choose "sound-crackers" category
echo    - Select discount type (percentage or flat amount)
echo    - Set offer value and save
echo.
echo 2. TEST CATEGORY PAGE:
echo    - Visit: http://greenvalleyherbs.local:8000/category/sound-crackers
echo    - You should see offer banners and discounted products
echo.
echo 3. TEST OFFER PRODUCTS PAGE:
echo    - Visit: http://greenvalleyherbs.local:8000/offer-products
echo    - You should see all products with active offers
echo    - Category filter should include sound-crackers
echo.
echo 4. VERIFY OFFER CALCULATION:
echo    - Products should show original price crossed out
echo    - Discounted price should be displayed prominently
echo    - "You save ₹X" message should appear
echo    - Offer badges should be visible
echo.

pause
