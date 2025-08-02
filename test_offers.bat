@echo off
echo Testing Category-wise Offers...

cd /d "D:\source_code\ecom"

echo.
echo Checking if offers table has required columns...
php artisan tinker --execute="
try {
    \$offers = \App\Models\Offer::select('id', 'name', 'type', 'discount_type', 'category_id', 'value')->take(5)->get();
    echo 'Offers table structure OK' . PHP_EOL;
    foreach(\$offers as \$offer) {
        echo 'Offer: ' . \$offer->name . ' (Type: ' . \$offer->type . ', Discount Type: ' . (\$offer->discount_type ?? 'N/A') . ')' . PHP_EOL;
    }
} catch(\Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo Checking sound-crackers category...
php artisan tinker --execute="
try {
    \$category = \App\Models\Category::where('slug', 'sound-crackers')->first();
    if(\$category) {
        echo 'Category found: ' . \$category->name . ' (ID: ' . \$category->id . ')' . PHP_EOL;
        \$products = \$category->products()->count();
        echo 'Products in category: ' . \$products . PHP_EOL;
        
        \$offers = \App\Models\Offer::where('type', 'category')->where('category_id', \$category->id)->get();
        echo 'Category offers: ' . \$offers->count() . PHP_EOL;
        foreach(\$offers as \$offer) {
            echo '- ' . \$offer->name . ' (' . \$offer->value . (\$offer->discount_type === 'percentage' ? '%' : ' ₹') . ' off)' . PHP_EOL;
        }
    } else {
        echo 'Category not found! Please check the slug.' . PHP_EOL;
    }
} catch(\Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo Testing OfferService...
php artisan tinker --execute="
try {
    \$offerService = new \App\Services\OfferService();
    \$category = \App\Models\Category::where('slug', 'sound-crackers')->first();
    if(\$category) {
        \$products = \$category->products()->take(3)->get();
        \$productsWithOffers = \$offerService->applyOffersToProducts(\$products);
        
        foreach(\$productsWithOffers as \$product) {
            echo 'Product: ' . \$product->name . PHP_EOL;
            echo '  Original Price: ₹' . \$product->price . PHP_EOL;
            echo '  Effective Price: ₹' . (\$product->effective_price ?? \$product->price) . PHP_EOL;
            echo '  Has Offer: ' . (\$product->has_offer ?? false ? 'Yes' : 'No') . PHP_EOL;
            echo '  Discount: ' . (\$product->discount_percentage ?? 0) . '%' . PHP_EOL;
            echo '---' . PHP_EOL;
        }
    }
} catch(\Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo ========================================
echo TEST COMPLETED!
echo ========================================
echo.
echo If you see any errors above, please run:
echo 1. apply_offers_fix.bat
echo 2. Create a category offer in admin panel
echo 3. Test again
echo.

pause
