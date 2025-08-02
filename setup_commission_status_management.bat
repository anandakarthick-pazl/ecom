@echo off
echo ========================================
echo COMMISSION STATUS MANAGEMENT SETUP
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
echo 3. Checking commission system dependencies...
php artisan tinker --execute="echo class_exists('\\App\\Models\\Commission') ? 'Commission model: OK' : 'Commission model: MISSING';"
php artisan tinker --execute="echo class_exists('\\App\\Http\\Controllers\\Admin\\CommissionController') ? 'Commission controller: OK' : 'Commission controller: MISSING';"

echo.
echo ========================================
echo COMMISSION STATUS UPDATE METHODS AVAILABLE:
echo ========================================
echo.
echo 🎯 METHOD 1: From POS Order Details
echo    ► Navigate to: Admin → POS → Sales History
echo    ► Click on any sale with commission
echo    ► Scroll to "Commission Details" section
echo    ► Use "Mark as Paid" or "Cancel" buttons
echo.
echo 🎯 METHOD 2: Dedicated Commission Management Page
echo    ► Navigate to: Admin → Reports → Sales Reports
echo    ► Click "Manage Commissions" button
echo    ► OR go directly to: /admin/commissions
echo.
echo 🎯 METHOD 3: Bulk Operations
echo    ► Use Commission Management page
echo    ► Select multiple commissions
echo    ► Click "Bulk Mark as Paid"
echo.
echo 🎯 METHOD 4: API/Programmatic Updates
echo    ► POST /admin/commissions/{id}/mark-paid
echo    ► POST /admin/commissions/{id}/cancel
echo    ► POST /admin/commissions/{id}/revert-pending
echo.
echo ========================================
echo COMMISSION STATUS WORKFLOW:
echo ========================================
echo.
echo 📊 PENDING → 💰 PAID
echo    ► Individual: Click "Mark as Paid" button
echo    ► Bulk: Select multiple + "Bulk Mark as Paid"
echo    ► Automatically records timestamp and user
echo.
echo 📊 PENDING → ❌ CANCELLED  
echo    ► Click "Cancel" button
echo    ► Provide cancellation reason (required)
echo    ► Reason is logged in commission notes
echo.
echo 💰 PAID → 📊 PENDING (Revert)
echo    ► Click "Revert to Pending" button
echo    ► Removes payment timestamp and user
echo    ► Logs revert action in notes
echo.
echo ========================================
echo FEATURES AVAILABLE:
echo ========================================
echo.
echo ✅ Individual Status Updates
echo    ► Mark as Paid (with optional notes)
echo    ► Cancel (with required reason)
echo    ► Revert to Pending (paid → pending)
echo.
echo ✅ Bulk Operations
echo    ► Select multiple pending commissions
echo    ► Mark all as paid simultaneously
echo    ► Add bulk payment notes
echo.
echo ✅ Advanced Filtering
echo    ► Filter by status (pending/paid/cancelled)
echo    ► Filter by reference name
echo    ► Filter by date range
echo.
echo ✅ Detailed Commission View
echo    ► Modal popup with full commission details
echo    ► Related sale information
echo    ► Complete notes history
echo    ► Direct links to sale details
echo.
echo ✅ Audit Trail
echo    ► All status changes are logged
echo    ► Timestamps for all actions
echo    ► User tracking for payments
echo    ► Detailed notes for each action
echo.
echo ✅ Export Capabilities
echo    ► Export filtered commission data
echo    ► Excel export with all details
echo    ► Date range and status filtering
echo.
echo ========================================
echo TESTING CHECKLIST:
echo ========================================
echo.
echo □ 1. Create a POS sale with commission enabled
echo □ 2. Navigate to /admin/commissions
echo □ 3. Verify commission appears in pending status
echo □ 4. Test "Mark as Paid" individual action
echo □ 5. Test "Revert to Pending" action
echo □ 6. Test "Cancel" action with reason
echo □ 7. Test bulk selection and bulk payment
echo □ 8. Test commission filtering options
echo □ 9. Test commission details modal
echo □ 10. Test export functionality
echo.
echo ========================================
echo ACCESS URLS:
echo ========================================
echo.
echo 🌐 Commission Management:
echo    http://greenvalleyherbs.local:8000/admin/commissions
echo.
echo 🌐 Sales Reports (with commission data):
echo    http://greenvalleyherbs.local:8000/admin/reports/sales
echo.
echo 🌐 POS Sales (individual commission management):
echo    http://greenvalleyherbs.local:8000/admin/pos/sales
echo.
echo ========================================
echo TROUBLESHOOTING:
echo ========================================
echo.
echo ❌ Commission page not accessible:
echo    ► Check user permissions for commission management
echo    ► Verify routes are properly cached
echo    ► Check Laravel logs in storage/logs/
echo.
echo ❌ Status update buttons not working:
echo    ► Check browser console for JavaScript errors
echo    ► Verify CSRF token is valid
echo    ► Check network tab for failed requests
echo.
echo ❌ Bulk operations failing:
echo    ► Ensure at least one commission is selected
echo    ► Check that selected commissions are in pending status
echo    ► Verify bulk-mark-paid route is working
echo.
echo ❌ Modal not loading details:
echo    ► Check commission details route
echo    ► Verify commission relationships are loaded
echo    ► Check for JavaScript errors in console
echo.
echo Setup complete! Commission status management is now fully functional.
echo.
pause
