<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CompanyRegistrationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\EstimateController;
use App\Http\Controllers\Admin\GrnController;
use App\Http\Controllers\Admin\StockAdjustmentController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SocialMediaController;

/*
|--------------------------------------------------------------------------
| Web Routes - Multi-Tenant SaaS E-commerce Platform
|--------------------------------------------------------------------------
*/

// Root route - Simple domain-based routing
Route::get('/', function () {
    $host = request()->getHost();

    // Debug logging for domain issues
    \Log::info('Root route accessed', [
        'host' => $host,
        'url' => request()->url(),
        'is_localhost' => in_array($host, ['localhost', '127.0.0.1']),
        'has_local' => str_contains($host, '.local')
    ]);

    // Main domain (localhost) - Show SaaS landing page using the original controller
    if ($host === 'localhost' || $host === '127.0.0.1') {
        return app(CompanyRegistrationController::class)->showRegistrationForm();
    }

    // For ANY other domain (including .local domains), check if it's a tenant
    $company = \App\Models\SuperAdmin\Company::where('domain', $host)->first();

    if ($company) {
        // Valid tenant domain - redirect to shop
        \Log::info('Tenant found, redirecting to shop', ['company' => $company->name]);
        return redirect('/shop');
    } else {
        // Unknown domain - log and show SaaS landing as fallback
        \Log::warning('Unknown domain accessed', ['host' => $host]);

        // For .local domains that should be tenants, redirect to shop anyway
        if (str_contains($host, '.local') && !str_contains($host, 'localhost')) {
            return redirect('/shop');
        }

        return app(CompanyRegistrationController::class)->showRegistrationForm();
    }
})->name('home');

// Include super admin routes
require __DIR__ . '/super_admin.php';

// SaaS Landing Page Routes (only on main domain)
Route::middleware(['main.domain'])->group(function () {
    Route::get('/features', [CompanyRegistrationController::class, 'features'])->name('features');
    Route::get('/pricing', [CompanyRegistrationController::class, 'pricing'])->name('pricing');
    Route::get('/contact', [CompanyRegistrationController::class, 'contact'])->name('contact');
    Route::post('/contact', [CompanyRegistrationController::class, 'submitContact'])->name('contact.submit');
    Route::post('/register', [CompanyRegistrationController::class, 'register'])->name('company.register');
    Route::get('/success/{slug}', [CompanyRegistrationController::class, 'registrationSuccess'])->name('registration.success');
    Route::post('/check-slug', [CompanyRegistrationController::class, 'checkSlugAvailability'])->name('check.slug');
});

// Admin Logout Route (outside admin prefix group to handle /admin/logout properly)
Route::match(['get', 'post'], '/admin/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    request()->session()->forget(['selected_company_id', 'selected_company_slug', 'selected_company_name', 'selected_company_domain', 'acting_as_company_admin', 'original_user_company_id']);

    // Return to tenant's admin login page
    return redirect('/admin/login')->with('success', 'Logged out successfully');
})->middleware(['auth', 'company.context'])->name('admin.logout');

// Include tenant authentication routes
require __DIR__ . '/auth.php';

// Tenant E-commerce Frontend Routes (with tenant middleware)
Route::middleware(['tenant'])->group(function () {
    // Frontend Store Routes
    Route::get('/shop', [HomeController::class, 'index'])->name('shop');
    Route::get('/products', [HomeController::class, 'products'])->name('products');
    Route::get('/offer-products', [HomeController::class, 'offerProducts'])->name('offer.products');
    Route::get('/category/{category:slug}', [HomeController::class, 'category'])->name('category');
    Route::get('/product/{product:slug}', [HomeController::class, 'product'])->name('product');
    Route::get('/search', [HomeController::class, 'search'])->name('search');

    // Order Tracking
    Route::match(['get', 'post'], '/track-order', [HomeController::class, 'trackOrder'])->name('track.order');

    // Cart Routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::put('/update', [CartController::class, 'update'])->name('update');
        Route::delete('/remove', [CartController::class, 'remove'])->name('remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
        Route::get('/count', [CartController::class, 'count'])->name('count');
        Route::get('/total-quantity', [CartController::class, 'totalQuantity'])->name('total-quantity');
        Route::get('/summary', [CartController::class, 'summary'])->name('summary');
    });

    // Checkout Routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Order Success
    Route::get('/order/success/{orderNumber}', [CheckoutController::class, 'success'])->name('order.success');

    // API route for social media links (for frontend)
    Route::get('/api/social-media-links', [SocialMediaController::class, 'getActiveLinks'])->name('api.social-media-links');

    // Stock Notification Routes
    Route::prefix('api/stock-notification')->name('stock-notification.')->group(function () {
        Route::post('/subscribe', [\App\Http\Controllers\StockNotificationController::class, 'subscribe'])->name('subscribe');
        Route::get('/form/{product}', [\App\Http\Controllers\StockNotificationController::class, 'quickForm'])->name('form');
        Route::post('/unsubscribe', [\App\Http\Controllers\StockNotificationController::class, 'unsubscribe'])->name('unsubscribe');
        Route::get('/stats', [\App\Http\Controllers\StockNotificationController::class, 'getStats'])->name('stats');
    });

    // Stock Notification Form Page (optional standalone page)
    Route::get('/notify-me/{product}', [\App\Http\Controllers\StockNotificationController::class, 'showForm'])->name('notify-form');

    // Razorpay Payment Routes
    Route::prefix('razorpay')->name('razorpay.')->group(function () {
        Route::post('/create-order', [\App\Http\Controllers\RazorpayController::class, 'createOrder'])->name('create-order');
        Route::post('/verify-payment', [\App\Http\Controllers\RazorpayController::class, 'verifyPayment'])->name('verify-payment');
        Route::post('/webhook', [\App\Http\Controllers\RazorpayController::class, 'webhook'])->name('webhook')->withoutMiddleware(['auth:customer']);
    });
});

// Tenant Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'company.context'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // E-commerce Management
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::patch('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
    Route::delete('products/{product}/remove-image', [ProductController::class, 'removeImage'])->name('products.remove-image');
    Route::post('products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk-action');

    // Customer Orders
    Route::get('orders/whatsapp-status', [OrderController::class, 'checkWhatsAppStatus'])->name('orders.whatsapp-status');
    Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('orders/recent', [OrderController::class, 'recentOrders'])->name('orders.recent');
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');

    // Enhanced Invoice Routes
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders/{order}/print-invoice', [OrderController::class, 'printInvoice'])->name('orders.print-invoice');
    Route::post('orders/{order}/send-invoice', [OrderController::class, 'sendInvoice'])->name('orders.send-invoice');
    Route::get('orders/{order}/download-invoice', [OrderController::class, 'downloadInvoice'])->name('orders.download-invoice');

    // WhatsApp Bill Routes
    Route::get('orders/{order}/download-bill', [OrderController::class, 'downloadBill'])->name('orders.download-bill');
    Route::get('orders/{order}/bill-formats', [OrderController::class, 'getBillFormats'])->name('orders.bill-formats');
    Route::post('orders/{order}/send-whatsapp-bill', [OrderController::class, 'sendBillWhatsApp'])->name('orders.send-whatsapp-bill');

    // Customer Management
    Route::resource('customers', CustomerController::class)->only(['index', 'show']);
    Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export');

    // Branch Management
    Route::resource('branches', BranchController::class);
    Route::patch('branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])->name('branches.toggle-status');
    Route::post('branches/{branch}/assign-users', [BranchController::class, 'assignUsers'])->name('branches.assign-users');
    Route::delete('branches/{branch}/users/{user}', [BranchController::class, 'removeUser'])->name('branches.remove-user');
    Route::get('branches/{branch}/available-users', [BranchController::class, 'getAvailableUsers'])->name('branches.available-users');
    Route::get('branches/{branch}/stats', [BranchController::class, 'getBranchStats'])->name('branches.stats');

    // Employee Management
    Route::resource('employees', EmployeeController::class);
    Route::patch('employees/{employee}/permissions', [EmployeeController::class, 'updatePermissions'])->name('employees.update-permissions');
    Route::patch('employees/{employee}/role', [EmployeeController::class, 'updateRole'])->name('employees.update-role');
    Route::get('employees/{employee}/permissions', [EmployeeController::class, 'getPermissions'])->name('employees.get-permissions');
    Route::post('employees/bulk-action', [EmployeeController::class, 'bulkAction'])->name('employees.bulk-action');

    // Role Management
    Route::resource('roles', RoleController::class);
    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
    Route::patch('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.update-permissions');
    Route::post('roles/{role}/duplicate', [RoleController::class, 'duplicate'])->name('roles.duplicate');
    Route::post('roles/{role}/assign-users', [RoleController::class, 'assignUsers'])->name('roles.assign-users');
    Route::delete('roles/{role}/users/{user}', [RoleController::class, 'removeUser'])->name('roles.remove-user');
    Route::patch('roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
    Route::get('roles/{role}/users', [RoleController::class, 'getUsers'])->name('roles.get-users');

    // Permission Management
    Route::resource('permissions', PermissionController::class);
    Route::post('permissions/bulk-assign', [PermissionController::class, 'bulkAssign'])->name('permissions.bulk-assign');
    Route::post('permissions/bulk-remove', [PermissionController::class, 'bulkRemove'])->name('permissions.bulk-remove');
    Route::get('permissions/{permission}/roles', [PermissionController::class, 'getRoles'])->name('permissions.get-roles');
    Route::get('permissions/module/{module}', [PermissionController::class, 'modulePermissions'])->name('permissions.module');
    Route::post('permissions/generate', [PermissionController::class, 'generatePermissions'])->name('permissions.generate');
    Route::get('permissions/export', [PermissionController::class, 'export'])->name('permissions.export');

    // Marketing
    Route::resource('banners', BannerController::class);
    Route::patch('banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
    Route::get('banners/upload-logs/list', [BannerController::class, 'uploadLogs'])->name('banners.upload-logs');
    Route::post('banners/use-existing-upload', [BannerController::class, 'useExistingUpload'])->name('banners.use-existing-upload');

    Route::resource('offers', OfferController::class);
    Route::patch('offers/{offer}/toggle-status', [OfferController::class, 'toggleStatus'])->name('offers.toggle-status');

    // Inventory & Procurement Management
    Route::resource('suppliers', SupplierController::class);
    Route::patch('suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle-status');

    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::patch('purchase-orders/{purchase_order}/status', [PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.update-status');

    // Estimates/Quotations
    Route::resource('estimates', EstimateController::class);
    Route::patch('estimates/{estimate}/status', [EstimateController::class, 'updateStatus'])->name('estimates.update-status');
    Route::post('estimates/{estimate}/duplicate', [EstimateController::class, 'duplicate'])->name('estimates.duplicate');

    // Goods Receipt Notes (GRN)
    Route::resource('grns', GrnController::class);
    Route::get('grns/po/{purchase_order}/items', [GrnController::class, 'getPurchaseOrderItems'])->name('grns.po-items');

    // Stock Adjustments
    Route::resource('stock-adjustments', StockAdjustmentController::class);
    Route::patch('stock-adjustments/{stock_adjustment}/approve', [StockAdjustmentController::class, 'approve'])->name('stock-adjustments.approve');
    Route::patch('stock-adjustments/{stock_adjustment}/cancel', [StockAdjustmentController::class, 'cancel'])->name('stock-adjustments.cancel');
    Route::get('products/{product}/stock', [StockAdjustmentController::class, 'getProductStock'])->name('products.stock');

    // Point of Sale (POS)
    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::post('/sale', [PosController::class, 'store'])->name('store');
        Route::get('/sales', [PosController::class, 'sales'])->name('sales');
        Route::get('/sales/{sale}', [PosController::class, 'show'])->name('show');
        Route::get('/receipt/{sale}', [PosController::class, 'receipt'])->name('receipt');
        Route::get('/sales/{sale}/download-bill', [PosController::class, 'downloadBill'])->name('download-bill');
        Route::get('/sales/{sale}/download-enhanced-invoice', [PosController::class, 'downloadEnhancedInvoice'])->name('download-enhanced-invoice');
        Route::get('/sales/{sale}/preview-enhanced-invoice', [PosController::class, 'previewEnhancedInvoice'])->name('preview-enhanced-invoice');
        Route::get('/sales/{sale}/test-enhanced-company-data', [PosController::class, 'testEnhancedCompanyData'])->name('test-enhanced-company-data');
        Route::get('/sales/{sale}/download-bill-debug', [PosController::class, 'downloadBillDebug'])->name('download-bill-debug');
        Route::get('/sales/{sale}/view-bill-debug', [PosController::class, 'viewBillDebug'])->name('view-bill-debug');
        Route::get('/sales/{sale}/debug-logo', [PosController::class, 'debugCompanyLogo'])->name('debug-logo');
        Route::get('/sales/{sale}/test-logo', [PosController::class, 'testLogoDisplay'])->name('test-logo');
        Route::get('/sales/{sale}/bill-formats', [PosController::class, 'getBillFormats'])->name('bill-formats');
        Route::post('/sales/{sale}/refund', [PosController::class, 'refund'])->name('refund');
        Route::get('/products/search', [PosController::class, 'searchProducts'])->name('products.search');
        Route::get('/products/{product}', [PosController::class, 'getProduct'])->name('products.get');
        Route::get('/summary/daily', [PosController::class, 'dailySummary'])->name('summary.daily');

        // Multiple receipts functionality
        Route::post('/download-multiple-receipts', [PosController::class, 'downloadMultipleReceipts'])->name('download-multiple-receipts');
        Route::get('/multi-receipt-sales', [PosController::class, 'getMultiReceiptSales'])->name('multi-receipt-sales');
        Route::post('/download-receipts-by-date', [PosController::class, 'downloadReceiptsByDateRange'])->name('download-receipts-by-date');
    });

    // Inventory Management
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('index');
        Route::get('/low-stock', [\App\Http\Controllers\Admin\InventoryController::class, 'lowStockAlert'])->name('low-stock');
        Route::get('/valuation', [\App\Http\Controllers\Admin\InventoryController::class, 'stockValuation'])->name('valuation');
        Route::get('/product/{product}/movements', [\App\Http\Controllers\Admin\InventoryController::class, 'stockMovements'])->name('movements');
        Route::get('/product/{product}/stock-card', [\App\Http\Controllers\Admin\InventoryController::class, 'stockCard'])->name('stock-card');
        Route::patch('/product/{product}/update-stock', [\App\Http\Controllers\Admin\InventoryController::class, 'updateStock'])->name('update-stock');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/customers', [\App\Http\Controllers\Admin\ReportController::class, 'customerReport'])->name('customers');
        Route::get('/sales', [\App\Http\Controllers\Admin\ReportController::class, 'salesReport'])->name('sales');
        Route::get('/test-excel-export', [\App\Http\Controllers\Admin\ReportController::class, 'testExcelExport'])->name('test-excel-export');
        Route::get('/purchase-orders', [\App\Http\Controllers\Admin\ReportController::class, 'purchaseOrderReport'])->name('purchase-orders');
        Route::get('/purchase-order-items', [\App\Http\Controllers\Admin\ReportController::class, 'purchaseOrderItemReport'])->name('purchase-order-items');
        Route::get('/grn', [\App\Http\Controllers\Admin\ReportController::class, 'grnReport'])->name('grn');
        Route::get('/stock-adjustments', [\App\Http\Controllers\Admin\ReportController::class, 'stockAdjustmentReport'])->name('stock-adjustments');
        Route::get('/income', [\App\Http\Controllers\Admin\ReportController::class, 'incomeReport'])->name('income');
        Route::get('/inventory', [\App\Http\Controllers\Admin\ReportController::class, 'inventoryReport'])->name('inventory');
        Route::get('/products', [\App\Http\Controllers\Admin\ReportController::class, 'productReport'])->name('products');
    });

    // Payment Methods
    Route::resource('payment-methods', \App\Http\Controllers\Admin\PaymentMethodController::class);
    Route::patch('payment-methods/{payment_method}/toggle-status', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'toggleStatus'])->name('payment-methods.toggle-status');
    Route::post('payment-methods/update-sort-order', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'updateSortOrder'])->name('payment-methods.update-sort-order');

    // Stock Notifications Management
    Route::prefix('stock-notifications')->name('stock-notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\StockNotificationController::class, 'listNotifications'])->name('index');
        Route::get('/{product}', [\App\Http\Controllers\StockNotificationController::class, 'listNotifications'])->name('product');
        Route::post('/send/{product}', [\App\Http\Controllers\StockNotificationController::class, 'sendNotifications'])->name('send');
        Route::get('/stats/overview', [\App\Http\Controllers\StockNotificationController::class, 'getStats'])->name('stats');
    });

    // Social Media Management - FIXED VERSION
    Route::resource('social-media', SocialMediaController::class);
    Route::patch('social-media/{socialMediaLink}/toggle-status', [SocialMediaController::class, 'toggleStatus'])
        ->name('social-media.toggle-status');
    Route::post('social-media/update-sort-order', [SocialMediaController::class, 'updateSortOrder'])
        ->name('social-media.update-sort-order');
    Route::post('social-media/quick-add', [SocialMediaController::class, 'quickAdd'])
        ->name('social-media.quick-add');

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/company', [SettingsController::class, 'updateCompany'])->name('company');
        Route::post('/theme', [SettingsController::class, 'updateTheme'])->name('theme');
        Route::post('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications');
        Route::post('/email', [SettingsController::class, 'updateEmail'])->name('email');
        Route::post('/inventory', [SettingsController::class, 'updateInventory'])->name('inventory');
        Route::post('/delivery', [SettingsController::class, 'updateDelivery'])->name('delivery');
        Route::post('/pagination', [SettingsController::class, 'updatePagination'])->name('pagination');
        Route::post('/bill-format', [SettingsController::class, 'updateBillFormat'])->name('bill-format');
        Route::post('/whatsapp-templates', [SettingsController::class, 'updateWhatsAppTemplates'])->name('whatsapp-templates');
        Route::post('/animations', [SettingsController::class, 'updateAnimations'])->name('animations');
        Route::post('/invoice-numbering', [SettingsController::class, 'updateInvoiceNumbering'])->name('invoice-numbering');
        Route::get('/preview-invoice-numbers', [SettingsController::class, 'previewInvoiceNumbers'])->name('preview-invoice-numbers');
        Route::post('/reset-invoice-sequences', [SettingsController::class, 'resetInvoiceSequences'])->name('reset-invoice-sequences');
        Route::post('/test-email', [SettingsController::class, 'testEmail'])->name('test-email');

        // Profile routes
        Route::get('/profile', [SettingsController::class, 'profile'])->name('profile');
        Route::post('/profile', [SettingsController::class, 'updateProfile'])->name('profile.update');
        Route::post('/password', [SettingsController::class, 'updatePassword'])->name('password');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [NotificationController::class, 'getUnread'])->name('unread');
        Route::get('/check-new', [NotificationController::class, 'checkNew'])->name('check-new');
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-read-by-id', [NotificationController::class, 'markAsReadById'])->name('mark-read-by-id');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });

    // File Upload Test (for development/debugging)
    if (config('app.debug')) {
        Route::prefix('test/file-upload')->name('test.file-upload.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\FileUploadTestController::class, 'index'])->name('index');
            Route::post('/test-upload', [\App\Http\Controllers\Admin\FileUploadTestController::class, 'testUpload'])->name('test-upload');
            Route::post('/test-delete', [\App\Http\Controllers\Admin\FileUploadTestController::class, 'testDelete'])->name('test-delete');
            Route::post('/test-url', [\App\Http\Controllers\Admin\FileUploadTestController::class, 'testUrl'])->name('test-url');
            Route::get('/diagnostics', [\App\Http\Controllers\Admin\FileUploadTestController::class, 'diagnostics'])->name('diagnostics');
            Route::post('/fix-storage', [\App\Http\Controllers\Admin\FileUploadTestController::class, 'fixStorage'])->name('fix-storage');
        });
    }
});

// Stock Notification Routes (Public)
Route::group(['prefix' => 'stock-notifications'], function () {
    Route::post('/subscribe', [App\Http\Controllers\StockNotificationController::class, 'subscribe'])->name('stock-notification.subscribe');
    Route::get('/form/{product}', [App\Http\Controllers\StockNotificationController::class, 'showForm'])->name('stock-notification.form');
    Route::get('/quick-form/{product}', [App\Http\Controllers\StockNotificationController::class, 'quickForm'])->name('stock-notification.quick-form');
    Route::post('/unsubscribe', [App\Http\Controllers\StockNotificationController::class, 'unsubscribe'])->name('stock-notification.unsubscribe.post');
    Route::get('/unsubscribe', [App\Http\Controllers\StockNotificationController::class, 'signedUnsubscribe'])
        ->name('stock-notification.unsubscribe')
        ->middleware('signed');
});

// Admin Stock Notification Routes
Route::group(['middleware' => ['auth', 'admin'], 'prefix' => 'admin/stock-notifications'], function () {
    Route::get('/stats', [App\Http\Controllers\StockNotificationController::class, 'getStats'])->name('admin.stock-notifications.stats');
    Route::get('/pending-summary', [App\Http\Controllers\StockNotificationController::class, 'getPendingSummary'])->name('admin.stock-notifications.pending-summary');
    Route::post('/trigger-check', [App\Http\Controllers\StockNotificationController::class, 'triggerStockCheck'])->name('admin.stock-notifications.trigger-check');
    Route::post('/send/{product}', [App\Http\Controllers\StockNotificationController::class, 'sendNotifications'])->name('admin.stock-notifications.send');
    Route::get('/list/{product}', [App\Http\Controllers\StockNotificationController::class, 'listNotifications'])->name('admin.stock-notifications.list');
    Route::post('/cleanup', [App\Http\Controllers\StockNotificationController::class, 'cleanupOldNotifications'])->name('admin.stock-notifications.cleanup');
});

// Include debug routes in development
if (config('app.debug')) {
    require __DIR__ . '/debug.php';
    require __DIR__ . '/emergency_stock_notifications.php';
}