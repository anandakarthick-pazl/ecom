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

/*
|--------------------------------------------------------------------------
| Web Routes - Multi-Tenant SaaS E-commerce Platform
|--------------------------------------------------------------------------
*/

// Root route with simplified logic
Route::get('/', function() {
    $host = request()->getHost();
    
    // Only localhost shows SaaS landing, everything else goes to shop
    if ($host === 'localhost' || $host === '127.0.0.1') {
        return app(CompanyRegistrationController::class)->showRegistrationForm();
    }
    
    // All other domains (including all .local domains) redirect to shop
    return redirect('/shop');
})->name('home');

// Include super admin routes
require __DIR__.'/super_admin.php';

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

// Include tenant authentication routes
require __DIR__.'/auth.php';

// Tenant E-commerce Frontend Routes (with tenant middleware)
Route::middleware(['tenant'])->group(function () {
    // Frontend Store Routes
    Route::get('/shop', [HomeController::class, 'index'])->name('shop');
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
    });
    
    // Checkout Routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    
    // Order Success
    Route::get('/order/success/{orderNumber}', [CheckoutController::class, 'success'])->name('order.success');
});

// Tenant Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'company.context'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Logout Route
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        request()->session()->forget(['selected_company_id', 'selected_company_slug', 'selected_company_name', 'selected_company_domain', 'acting_as_company_admin', 'original_user_company_id']);
        
        // Return to tenant's shop page
        return redirect()->route('shop');
    })->name('logout');
    
    // E-commerce Management
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::patch('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
    
    // Customer Orders
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('orders/{order}/send-invoice', [OrderController::class, 'sendInvoice'])->name('orders.send-invoice');
    Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('orders/recent', [OrderController::class, 'recentOrders'])->name('orders.recent');
    
    // Customer Management
    Route::resource('customers', CustomerController::class)->only(['index', 'show']);
    Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export');
    
    // Marketing
    Route::resource('banners', BannerController::class);
    Route::patch('banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggle-status');
    
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
        Route::post('/sales/{sale}/refund', [PosController::class, 'refund'])->name('refund');
        Route::get('/products/search', [PosController::class, 'searchProducts'])->name('products.search');
        Route::get('/products/{product}', [PosController::class, 'getProduct'])->name('products.get');
        Route::get('/summary/daily', [PosController::class, 'dailySummary'])->name('summary.daily');
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
        Route::get('/purchase-orders', [\App\Http\Controllers\Admin\ReportController::class, 'purchaseOrderReport'])->name('purchase-orders');
        Route::get('/purchase-order-items', [\App\Http\Controllers\Admin\ReportController::class, 'purchaseOrderItemReport'])->name('purchase-order-items');
        Route::get('/grn', [\App\Http\Controllers\Admin\ReportController::class, 'grnReport'])->name('grn');
        Route::get('/stock-adjustments', [\App\Http\Controllers\Admin\ReportController::class, 'stockAdjustmentReport'])->name('stock-adjustments');
        Route::get('/income', [\App\Http\Controllers\Admin\ReportController::class, 'incomeReport'])->name('income');
        Route::get('/inventory', [\App\Http\Controllers\Admin\ReportController::class, 'inventoryReport'])->name('inventory');
        Route::get('/products', [\App\Http\Controllers\Admin\ReportController::class, 'productReport'])->name('products');
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/company', [SettingsController::class, 'updateCompany'])->name('company');
        Route::post('/theme', [SettingsController::class, 'updateTheme'])->name('theme');
        Route::post('/notifications', [SettingsController::class, 'updateNotifications'])->name('notifications');
        Route::post('/email', [SettingsController::class, 'updateEmail'])->name('email');
        Route::post('/inventory', [SettingsController::class, 'updateInventory'])->name('inventory');
        Route::post('/test-email', [SettingsController::class, 'testEmail'])->name('test-email');
        
        // Profile routes
        Route::get('/profile', [SettingsController::class, 'profile'])->name('profile');
        Route::post('/profile', [SettingsController::class, 'updateProfile'])->name('profile');
        Route::post('/password', [SettingsController::class, 'updatePassword'])->name('password');
    });
    
    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [NotificationController::class, 'getUnread'])->name('unread');
        Route::get('/check-new', [NotificationController::class, 'checkNew'])->name('check-new');
        Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Debug Routes (Remove in Production)
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    Route::get('/debug/domain', function() {
        $host = request()->getHost();
        return response()->json([
            'current_host' => $host,
            'is_localhost' => in_array($host, ['localhost', '127.0.0.1']),
            'should_show' => in_array($host, ['localhost', '127.0.0.1']) ? 'SaaS Landing' : 'Redirect to /shop',
            'timestamp' => now()->toISOString()
        ], 200, [], JSON_PRETTY_PRINT);
    });
}
