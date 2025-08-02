@echo off
echo ================================================
echo UPDATING INVOICE SYSTEM WITH MRP AND OFFER PRICES
echo ================================================
echo.
echo This update adds comprehensive pricing details to invoices:
echo - MRP (Maximum Retail Price) display
echo - Offer price with discounts
echo - Savings calculations and summaries
echo - Offer name display
echo - Enhanced invoice layouts
echo.
echo Files that will be updated:
echo - Database schema (order_items table)
echo - Order processing logic
echo - All invoice templates (PDF, print, thermal)
echo - Existing order items data
echo.
echo Press any key to continue or Ctrl+C to cancel...
pause
echo.
echo 1. Running database migration to add pricing fields...
php artisan migrate --force

echo.
echo 2. Clearing application cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo.
echo 3. Updating existing order items with pricing details...
php artisan orders:update-pricing

echo.
echo 4. Optimizing application...
php artisan config:cache
php artisan view:cache
php artisan route:cache

echo.
echo ================================================
echo INVOICE PRICING UPDATE COMPLETED SUCCESSFULLY!
echo ================================================
echo.
echo New Features Added:
echo ✓ MRP (Maximum Retail Price) column in invoices
echo ✓ Offer Price column showing discounted price
echo ✓ Discount percentage and savings display
echo ✓ Offer name display when applicable
echo ✓ Total MRP and total savings summary
echo ✓ Enhanced invoice layouts for all formats
echo.
echo All invoice formats updated:
echo ✓ Web invoice view (/admin/orders/{id})
echo ✓ PDF invoice download
echo ✓ Email invoice attachment
echo ✓ WhatsApp invoice sharing
echo ✓ A4 print format
echo ✓ Thermal receipt format
echo.
echo Next Steps:
echo 1. Test invoice generation by viewing any order
echo 2. Check PDF download functionality
echo 3. Verify email invoice sending
echo 4. Test print formats (A4 and thermal)
echo 5. Review INVOICE_PRICING_ENHANCEMENT_GUIDE.md for details
echo.
echo For existing orders migration, run:
echo migrate_existing_order_pricing.bat
echo.
pause
