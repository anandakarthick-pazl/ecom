@echo off
echo ================================================
echo MIGRATING EXISTING ORDER ITEMS TO NEW PRICING STRUCTURE
echo ================================================

echo.
echo This script will:
echo 1. Update existing order items with MRP and offer pricing data
echo 2. Ensure all historical orders show correct pricing information
echo 3. Preserve existing data while adding new fields
echo.

echo Starting migration process...
echo.

echo Step 1: Checking current order items without pricing data...
php artisan orders:update-pricing --dry-run

echo.
echo Press any key to continue with the actual migration, or Ctrl+C to cancel...
pause

echo.
echo Step 2: Running the actual migration...
php artisan orders:update-pricing

echo.
echo Step 3: Clearing caches to reflect changes...
php artisan cache:clear
php artisan view:clear

echo.
echo ================================================
echo ORDER ITEMS PRICING MIGRATION COMPLETED!
echo ================================================
echo.
echo All existing order items have been updated with:
echo - MRP (Maximum Retail Price) data
echo - Discount amount and percentage
echo - Offer name (where applicable)
echo - Calculated savings
echo.
echo Your invoices will now show complete pricing details
echo for both new and historical orders.
echo.
pause
