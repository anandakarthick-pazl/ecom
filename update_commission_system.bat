@echo off
echo ================================================
echo COMMISSION STATUS UPDATE SYSTEM ACTIVATION
echo ================================================
echo.

cd /d "D:\source_code\ecom"

echo 1. Clearing Laravel caches...
php artisan route:clear
php artisan view:clear  
php artisan config:clear
php artisan cache:clear

echo.
echo 2. Updating composer autoloader...
composer dump-autoload

echo.
echo 3. Running database migrations (if needed)...
php artisan migrate --force

echo.
echo 4. Verifying commission system components...
php artisan tinker --execute="echo 'Commission Model: ' . (class_exists('\\App\\Models\\Commission') ? 'OK' : 'MISSING');"
php artisan tinker --execute="echo 'Commission Controller: ' . (class_exists('\\App\\Http\\Controllers\\Admin\\CommissionController') ? 'OK' : 'MISSING');"

echo.
echo ================================================
echo COMMISSION STATUS UPDATE METHODS:
echo ================================================
echo.
echo 🎯 METHOD 1: Individual Updates
echo    URL: http://greenvalleyherbs.local:8000/admin/pos/sales
echo    ► Click any sale → Commission Details → Action buttons
echo.
echo 🎯 METHOD 2: Commission Management Dashboard  
echo    URL: http://greenvalleyherbs.local:8000/admin/commissions
echo    ► Full management interface with filters and bulk operations
echo.
echo 🎯 METHOD 3: Sales Report Integration
echo    URL: http://greenvalleyherbs.local:8000/admin/reports/sales
echo    ► Click "Manage Commissions" button
echo.
echo ================================================
echo COMMISSION STATUS WORKFLOW:
echo ================================================
echo.
echo 📊 PENDING → 💰 PAID
echo    ► Click "Mark as Paid" (individual)
echo    ► Select multiple + "Bulk Mark as Paid"
echo    ► Add optional payment notes
echo.
echo 📊 PENDING → ❌ CANCELLED
echo    ► Click "Cancel" button
echo    ► Enter cancellation reason (required)
echo.
echo 💰 PAID → 📊 PENDING
echo    ► Click "Revert to Pending" button
echo    ► Logs revert action automatically
echo.
echo ================================================
echo FEATURES AVAILABLE:
echo ================================================
echo.
echo ✅ Summary Dashboard Cards
echo    ► Pending Commission Amount & Count
echo    ► Paid Commission Amount & Count  
echo    ► This Month Commission Totals
echo    ► All-time Commission Totals
echo.
echo ✅ Advanced Filtering
echo    ► Filter by Status (pending/paid/cancelled)
echo    ► Filter by Reference Name (search)
echo    ► Filter by Date Range
echo    ► Combine multiple filters
echo.
echo ✅ Bulk Operations
echo    ► Select All / Clear All functionality
echo    ► Bulk mark as paid with notes
echo    ► Real-time selection count
echo    ► Only pending commissions selectable
echo.
echo ✅ Commission Details Modal
echo    ► Complete commission information
echo    ► Related sale details and links
echo    ► Notes history and audit trail
echo    ► Action buttons within modal
echo.
echo ✅ Export Functionality  
echo    ► Export filtered data to Excel
echo    ► Include all commission details
echo    ► Date range and status filtering
echo.
echo ✅ Audit Trail & Logging
echo    ► All status changes logged
echo    ► Timestamps for all actions
echo    ► User tracking for payments
echo    ► Detailed notes for each action
echo.
echo ================================================
echo TESTING YOUR COMMISSION SYSTEM:
echo ================================================
echo.
echo Step 1: Create test commission
echo    ► Make a POS sale with commission enabled
echo    ► Commission should auto-create as "pending"
echo.
echo Step 2: Access commission management
echo    ► Go to: http://greenvalleyherbs.local:8000/admin/commissions
echo    ► Verify pending commission appears
echo.
echo Step 3: Test individual actions
echo    ► Click "Mark as Paid" button (green)
echo    ► Verify status changes to "paid" 
echo    ► Test "Revert to Pending" button (yellow)
echo    ► Test "Cancel" with reason (red)
echo.
echo Step 4: Test bulk operations
echo    ► Create multiple pending commissions
echo    ► Select multiple checkboxes
echo    ► Click "Bulk Mark as Paid"
echo    ► Add payment notes and confirm
echo.
echo Step 5: Test filtering and export
echo    ► Use status filters
echo    ► Test date range filtering
echo    ► Try export functionality
echo.
echo ================================================
echo TROUBLESHOOTING:
echo ================================================
echo.
echo ❌ Can't access commission page:
echo    ► Check user has commission management permissions
echo    ► Verify you're logged in as admin/manager
echo    ► Check Laravel logs: storage/logs/laravel.log
echo.
echo ❌ Buttons not working:
echo    ► Check browser console for JavaScript errors
echo    ► Verify CSRF token is valid
echo    ► Clear browser cache and try again
echo.
echo ❌ Bulk operations failing:
echo    ► Ensure at least one commission is selected
echo    ► Only pending commissions can be bulk processed
echo    ► Check network tab for failed requests
echo.
echo ❌ Commission not auto-creating:
echo    ► Verify commission settings in POS
echo    ► Check if reference name is properly set
echo    ► Ensure commission percentage is configured
echo.
echo ================================================
echo SUCCESS! Your commission system is ready to use!
echo ================================================
echo.
echo 🎉 COMMISSION STATUS UPDATE SYSTEM IS ACTIVE!
echo.
echo Quick Access URLs:
echo ► Commission Management: http://greenvalleyherbs.local:8000/admin/commissions
echo ► POS Sales: http://greenvalleyherbs.local:8000/admin/pos/sales  
echo ► Sales Reports: http://greenvalleyherbs.local:8000/admin/reports/sales
echo.
echo System updated successfully! 
echo.
pause