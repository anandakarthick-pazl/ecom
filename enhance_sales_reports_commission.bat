@echo off
echo ========================================
echo SALES REPORT COMMISSION ENHANCEMENT SCRIPT
echo ========================================
echo.

echo 1. Clearing Laravel caches...
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear

echo.
echo 2. Generating autoloader files...
composer dump-autoload

echo.
echo 3. Checking Commission model import...
php artisan tinker --execute="echo class_exists('\\App\\Models\\Commission') ? 'Commission model exists' : 'Commission model missing';"

echo.
echo ========================================
echo COMMISSION SALES REPORT ENHANCEMENTS APPLIED:
echo ========================================
echo ✓ Added commission statistics to sales report controller
echo ✓ Updated Excel export to include commission data
echo ✓ Added commission summary cards to dashboard
echo ✓ Enhanced POS sales table with commission column
echo ✓ Added Top Commission Performers section
echo ✓ Added commission status filtering
echo ✓ Created dedicated Commission sheet in Excel export
echo.
echo ========================================
echo NEW FEATURES AVAILABLE:
echo ========================================
echo 📊 Commission Summary Cards:
echo    - Total Commission Amount & Count
echo    - Pending Commission Amount & Count  
echo    - Paid Commission Amount & Count
echo    - Commission Rate (percentage of total sales)
echo.
echo 📋 Enhanced POS Sales Table:
echo    - Commission Amount column
echo    - Reference Name display
echo    - Commission Status badges
echo.
echo 🏆 Top Commission Performers:
echo    - Reference names ranked by total commission
echo    - Transaction counts and averages
echo.
echo 📁 Enhanced Excel Export:
echo    - Commission columns in POS Sales sheet
echo    - Dedicated Commissions sheet with detailed data
echo    - Commission summary in Summary sheet
echo.
echo 🔍 Commission Filtering:
echo    - Filter by commission status
echo    - Show only sales with/without commission
echo    - Filter by pending/paid commission status
echo.
echo ========================================
echo TESTING INSTRUCTIONS:
echo ========================================
echo 1. Navigate to: http://greenvalleyherbs.local:8000/admin/reports/sales
echo 2. Check commission summary cards are displayed
echo 3. Verify commission column in POS sales table
echo 4. Test commission status filters
echo 5. Export to Excel and verify all commission data is included
echo 6. Check Top Commission Performers section (if commission data exists)
echo.
echo ========================================
echo TROUBLESHOOTING:
echo ========================================
echo - If commission stats not showing: Ensure commission records exist in database
echo - If Excel export fails: Check logs in storage/logs/laravel.log
echo - If filters not working: Clear browser cache and check form submission
echo - If top performers not visible: Create some commission records first
echo.
pause
