@echo off
echo Applying offers fix for category-wise offers...

cd /d "D:\source_code\ecom"

echo.
echo Step 1: Running migration to add offer fields...
php artisan migrate --path=database/migrations/2024_07_24_000001_add_company_id_to_offers_table.php

echo.
echo Step 2: Clearing all caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo Step 3: Optimizing application...
php artisan optimize

echo.
echo ========================================
echo OFFERS FIX COMPLETED!
echo ========================================
echo.
echo Your category-wise offers should now work properly.
echo.
echo TEST INSTRUCTIONS:
echo 1. Go to Admin Panel -> Offers -> Create Offer
echo 2. Select "Category Specific" offer type
echo 3. Choose your "sound-crackers" category
echo 4. Select discount type (percentage or flat amount)
echo 5. Set the offer value and dates
echo 6. Save the offer
echo 7. Visit: http://greenvalleyherbs.local:8000/category/sound-crackers
echo 8. You should see the offer applied to products!
echo.

pause
