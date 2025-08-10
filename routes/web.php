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
Route::get('/', function() {
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
require __DIR__.'/super_admin.php';

// Include social media routes
require __DIR__.'/social_media_direct.php';

// API route for social media links (fallback if not loaded from social_media_direct.php)
Route::middleware(['web', 'tenant'])
    ->get('/api/social-media-links', [\App\Http\Controllers\Admin\SocialMediaController::class, 'getActiveLinks'])
    ->name('api.social-media-links');

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
require __DIR__.'/auth.php';

// Tenant E-commerce Frontend Routes (with tenant middleware)
Route::middleware(['tenant'])->group(function () {
    // Frontend Store Routes
    Route::get('/shop', [HomeController::class, 'index'])->name('shop');
    Route::get('/products', [HomeController::class, 'products'])->name('products');
    Route::get('/offer-products', [HomeController::class, 'offerProducts'])->name('offer.products');
    Route::get('/flash-offers', [HomeController::class, 'flashOffers'])->name('flash.offers');
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
    });
    
    // Checkout Routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    
    // Order Success
    Route::get('/order/success/{orderNumber}', [CheckoutController::class, 'success'])->name('order.success');
    
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
    
    // Admin Logout Routes - Handle both GET and POST for compatibility
    Route::match(['get', 'post'], '/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        request()->session()->forget(['selected_company_id', 'selected_company_slug', 'selected_company_name', 'selected_company_domain', 'acting_as_company_admin', 'original_user_company_id']);
        
        // Return to tenant's admin login page
        return redirect('/admin/login')->with('success', 'Logged out successfully');
    })->name('logout');
    

    
    // E-commerce Management
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::patch('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
    Route::delete('products/{product}/remove-image', [ProductController::class, 'removeImage'])->name('products.remove-image');
    Route::post('products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk-action');
    
    // Customer Orders
    // IMPORTANT: Specific routes must come BEFORE resource routes with parameters
    Route::get('orders/whatsapp-status', [OrderController::class, 'checkWhatsAppStatus'])->name('orders.whatsapp-status');
    Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
    Route::get('orders/recent', [OrderController::class, 'recentOrders'])->name('orders.recent');
    
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::patch('orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders/{order}/print-invoice', [OrderController::class, 'printInvoice'])->name('orders.print-invoice');
    Route::get('orders/{order}/download-invoice', [OrderController::class, 'downloadInvoice'])->name('orders.download-invoice');
    Route::post('orders/{order}/send-invoice', [OrderController::class, 'sendInvoice'])->name('orders.send-invoice');
    
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
    Route::get('banners/upload-logs', [BannerController::class, 'uploadLogs'])->name('banners.upload-logs');
    Route::post('banners/use-existing-upload', [BannerController::class, 'useExistingUpload'])->name('banners.use-existing-upload');
    Route::delete('banners/upload-logs/{log}', [BannerController::class, 'deleteUploadLog'])->name('banners.delete-upload-log');
    
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
        Route::get('/sales/{sale}/download-bill-debug', [PosController::class, 'downloadBillDebug'])->name('download-bill-debug');
        Route::get('/sales/{sale}/view-bill-debug', [PosController::class, 'viewBillDebug'])->name('view-bill-debug');
        Route::get('/sales/{sale}/bill-formats', [PosController::class, 'getBillFormats'])->name('bill-formats');
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
    
    // Payment Methods
    Route::resource('payment-methods', \App\Http\Controllers\Admin\PaymentMethodController::class);
    Route::patch('payment-methods/{payment_method}/toggle-status', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'toggleStatus'])->name('payment-methods.toggle-status');
    Route::post('payment-methods/update-sort-order', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'updateSortOrder'])->name('payment-methods.update-sort-order');
    
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
        Route::post('/invoice-numbering', [SettingsController::class, 'updateInvoiceNumbering'])->name('invoice-numbering');
        Route::get('/preview-invoice-numbers', [SettingsController::class, 'previewInvoiceNumbers'])->name('preview-invoice-numbers');
        Route::post('/reset-invoice-sequences', [SettingsController::class, 'resetInvoiceSequences'])->name('reset-invoice-sequences');
        Route::get('/invoice-numbering/preview', [SettingsController::class, 'previewInvoiceNumbers'])->name('invoice-numbering.preview');
        Route::post('/invoice-numbering/reset', [SettingsController::class, 'resetInvoiceSequences'])->name('invoice-numbering.reset');
        Route::post('/whatsapp-templates', [SettingsController::class, 'updateWhatsAppTemplates'])->name('whatsapp-templates');
        Route::post('/animations', [SettingsController::class, 'updateAnimations'])->name('animations');
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
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Debug & Verification Routes (Remove in Production)
|--------------------------------------------------------------------------
*/

if (app()->environment('local')) {
    Route::get('/debug/routes', function() {
        $host = request()->getHost();
        $routes = [
            'Main Domain (localhost)' => [
                '/' => 'SaaS Landing Page',
                '/super-admin/login' => 'Super Admin Login',
                '/features' => 'Features Page',
                '/pricing' => 'Pricing Page',
                '/contact' => 'Contact Page',
            ],
            'Tenant Domain (*.local)' => [
                '/' => 'Redirect to /shop',
                '/shop' => 'E-commerce Store Homepage',
                '/login' => 'Admin Login',
                '/admin/dashboard' => 'Admin Dashboard',
                '/category/{slug}' => 'Category Page',
                '/product/{slug}' => 'Product Page',
                '/cart' => 'Shopping Cart',
                '/checkout' => 'Checkout Page',
            ]
        ];
        
        return response()->json([
            'current_host' => $host,
            'is_localhost' => ($host === 'localhost' || $host === '127.0.0.1'),
            'routes' => $routes,
            'tenant' => app()->has('current_tenant') ? app('current_tenant')->name : null,
        ], 200, [], JSON_PRETTY_PRINT);
    });
    
    Route::get('/debug/tenant', function() {
        $host = request()->getHost();
        $company = \App\Models\SuperAdmin\Company::where('domain', $host)->first();
        
        return response()->json([
            'host' => $host,
            'company_found' => !is_null($company),
            'company' => $company ? [
                'id' => $company->id,
                'name' => $company->name,
                'domain' => $company->domain,
                'status' => $company->status
            ] : null,
            'current_tenant' => app()->has('current_tenant') ? app('current_tenant')->name : null,
        ], 200, [], JSON_PRETTY_PRINT);
    });
    
    // Login Issue Diagnostic Routes
    Route::get('/debug/login-issue', function() {
        $host = request()->get('domain', request()->getHost());
        
        $diagnostics = [
            'domain_checked' => $host,
            'company' => null,
            'company_users' => [],
            'admin_users' => [],
            'all_companies' => [],
            'issues' => [],
            'solutions' => []
        ];
        
        try {
            // Check company
            $company = \App\Models\SuperAdmin\Company::where('domain', $host)->first();
            
            if ($company) {
                $diagnostics['company'] = [
                    'id' => $company->id,
                    'name' => $company->name,
                    'domain' => $company->domain,
                    'status' => $company->status
                ];
                
                // Check users for this company
                $users = \App\Models\User::where('company_id', $company->id)->get();
                $diagnostics['company_users'] = $users->map(function($user) {
                    return [
                        'email' => $user->email,
                        'role' => $user->role,
                        'company_id' => $user->company_id,
                        'is_super_admin' => $user->isSuperAdmin(),
                        'verified' => !is_null($user->email_verified_at)
                    ];
                })->toArray();
                
                // Check admin users
                $adminUsers = \App\Models\User::where('company_id', $company->id)
                                            ->whereIn('role', ['admin', 'manager'])
                                            ->get();
                $diagnostics['admin_users'] = $adminUsers->map(function($user) {
                    return [
                        'email' => $user->email,
                        'role' => $user->role
                    ];
                })->toArray();
                
                // Identify issues
                if ($users->count() === 0) {
                    $diagnostics['issues'][] = 'No users found for this company';
                    $diagnostics['solutions'][] = 'Create user accounts for company ID: ' . $company->id;
                } elseif ($adminUsers->count() === 0) {
                    $diagnostics['issues'][] = 'No admin/manager users found';
                    $diagnostics['solutions'][] = 'Update user roles to admin or manager';
                }
                
            } else {
                $diagnostics['issues'][] = 'Company not found for domain: ' . $host;
                $diagnostics['solutions'][] = 'Check if domain exists in companies table';
            }
            
            // Get all companies
            $allCompanies = \App\Models\SuperAdmin\Company::all();
            $diagnostics['all_companies'] = $allCompanies->map(function($comp) {
                return [
                    'id' => $comp->id,
                    'name' => $comp->name,
                    'domain' => $comp->domain,
                    'status' => $comp->status
                ];
            })->toArray();
            
        } catch (\Exception $e) {
            $diagnostics['error'] = $e->getMessage();
        }
        
        return response()->json($diagnostics, 200, [], JSON_PRETTY_PRINT);
    });
    
    // HTML version for easier reading
    Route::get('/debug/login-issue-html', function() {
        $host = request()->get('domain', 'greenvalleyherbs.local');
        
        $html = '<!DOCTYPE html><html><head><title>Login Issue Diagnostic</title>';
        $html .= '<style>body{font-family:Arial,sans-serif;margin:40px;background:#f5f5f5;} .error{color:#d32f2f;background:#ffebee;padding:10px;border-left:4px solid #d32f2f;} .success{color:#388e3c;background:#e8f5e8;padding:10px;border-left:4px solid #388e3c;} .warning{color:#f57c00;background:#fff8e1;padding:10px;border-left:4px solid #f57c00;} .section{margin:20px 0; padding:15px; border:1px solid #ddd;background:white;border-radius:5px;} .user-card{margin:10px 0; padding:10px; background:#f9f9f9;border-radius:3px;}</style>';
        $html .= '</head><body>';
        $html .= '<h1>🔍 Login Issue Diagnostic for: ' . $host . '</h1>';
        
        try {
            // Check company
            $company = \App\Models\SuperAdmin\Company::where('domain', $host)->first();
            
            if ($company) {
                $html .= '<div class="section success"><h2>✅ Company Found</h2>';
                $html .= '<p><strong>ID:</strong> ' . $company->id . '</p>';
                $html .= '<p><strong>Name:</strong> ' . $company->name . '</p>';
                $html .= '<p><strong>Domain:</strong> ' . $company->domain . '</p>';
                $html .= '<p><strong>Status:</strong> ' . $company->status . '</p></div>';
                
                // Check users
                $users = \App\Models\User::where('company_id', $company->id)->get();
                
                if ($users->count() > 0) {
                    $html .= '<div class="section success"><h2>✅ Users Found (' . $users->count() . ')</h2>';
                    foreach ($users as $user) {
                        $html .= '<div class="user-card">';
                        $html .= '<strong>📧 Email:</strong> ' . $user->email . '<br>';
                        $html .= '<strong>👤 Role:</strong> ' . $user->role . '<br>';
                        $html .= '<strong>🏢 Company ID:</strong> ' . $user->company_id . '<br>';
                        $html .= '<strong>👑 Super Admin:</strong> ' . ($user->isSuperAdmin() ? 'Yes' : 'No') . '<br>';
                        $html .= '<strong>✅ Verified:</strong> ' . ($user->email_verified_at ? 'Yes' : 'No');
                        $html .= '</div>';
                    }
                    $html .= '</div>';
                    
                    // Check admin users
                    $adminUsers = \App\Models\User::where('company_id', $company->id)
                                                ->whereIn('role', ['admin', 'manager'])
                                                ->get();
                    
                    if ($adminUsers->count() > 0) {
                        $html .= '<div class="section success"><h2>✅ Admin/Manager Users Found (' . $adminUsers->count() . ')</h2>';
                        foreach ($adminUsers as $user) {
                            $html .= '<p>🔑 ' . $user->email . ' (Role: ' . $user->role . ')</p>';
                        }
                        $html .= '<div class="success"><h3>🎉 Setup appears correct!</h3>';
                        $html .= '<p><strong>The issue might be:</strong></p>';
                        $html .= '<ul><li>❌ Incorrect password</li><li>🍪 Session/cache/cookie issues</li><li>🔒 Browser security settings</li><li>📋 Form submission issues</li></ul>';
                        $html .= '<p><strong>🔧 Try these solutions:</strong></p>';
                        $html .= '<ul><li>Clear browser cache and cookies</li><li>Try incognito/private browsing mode</li><li>Check browser console for JavaScript errors</li><li>Verify the password is correct</li><li>Check Laravel logs in storage/logs/</li></ul></div>';
                        $html .= '</div>';
                    } else {
                        $html .= '<div class="section error"><h2>❌ No Admin/Manager Users</h2>';
                        $html .= '<p>Users need "admin" or "manager" role to access tenant admin panel.</p>';
                        $html .= '<p><strong>💡 Solution:</strong> Update user roles to "admin" or "manager" in the database</p>';
                        $html .= '<p><strong>SQL Example:</strong><br><code>UPDATE users SET role = "admin" WHERE email = "your-email@example.com"</code></p></div>';
                    }
                    
                } else {
                    $html .= '<div class="section error"><h2>❌ No Users Found</h2>';
                    $html .= '<p>No users are assigned to company ID: ' . $company->id . '</p>';
                    $html .= '<p><strong>💡 Solution:</strong> Create user accounts for this company or update existing users\' company_id</p></div>';
                }
                
            } else {
                $html .= '<div class="section error"><h2>❌ Company Not Found</h2>';
                $html .= '<p>No company found for domain: <strong>' . $host . '</strong></p>';
                $html .= '<p><strong>💡 Solution:</strong> Check if domain exists in companies table or create company record</p>';
                
                // Show all companies
                $allCompanies = \App\Models\SuperAdmin\Company::all();
                $html .= '<h3>📋 All Companies in System:</h3>';
                if ($allCompanies->count() > 0) {
                    foreach ($allCompanies as $comp) {
                        $html .= '<p>🏢 ' . $comp->name . ' → <code>' . $comp->domain . '</code> (ID: ' . $comp->id . ')</p>';
                    }
                } else {
                    $html .= '<p>❌ No companies found in system!</p>';
                }
                $html .= '</div>';
            }
            
        } catch (\Exception $e) {
            $html .= '<div class="section error"><h2>❌ Diagnostic Error</h2><p>' . $e->getMessage() . '</p></div>';
        }
        
        $html .= '<div class="section"><h3>🔗 Useful Debug URLs:</h3>';
        $html .= '<ul>';
        $html .= '<li><a href="/debug/login-issue?domain=greenvalleyherbs.local" target="_blank">JSON Diagnostic for greenvalleyherbs.local</a></li>';
        $html .= '<li><a href="/debug/tenant" target="_blank">Current Tenant Info</a></li>';
        $html .= '<li><a href="/debug/routes" target="_blank">Available Routes</a></li>';
        $html .= '</ul></div>';
        
        $html .= '</body></html>';
        
        return $html;
    });
}

// Include debug routes in development
if (config('app.debug')) {
    require __DIR__ . '/debug.php';
    
    // Temporary test route for storage
    Route::get('/super-admin/storage-test', [\App\Http\Controllers\SuperAdmin\StorageTestController::class, 'index'])
        ->middleware(['auth', 'super.admin'])
        ->name('super-admin.storage.test');
        
    // Banner debug routes
    Route::get('/debug/banners', function() {
        $banners = \App\Models\Banner::all();
        
        $debug = [
            'total_banners' => $banners->count(),
            'storage_paths' => [
                'storage/app/public exists' => is_dir(storage_path('app/public')),
                'public/storage exists' => is_dir(public_path('storage')),
                'banner files location' => public_path('storage/public/banner/banners/'),
                'banner files exist' => is_dir(public_path('storage/public/banner/banners/')),
            ],
            'banners' => $banners->map(function($banner) {
                $filename = basename($banner->image ?? '');
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'image_stored' => $banner->image,
                    'filename' => $filename,
                    'is_active' => $banner->is_active,
                    'position' => $banner->position,
                    'start_date' => $banner->start_date,
                    'end_date' => $banner->end_date,
                    'generated_url' => $banner->image_url,
                    'file_exists_public' => $filename ? file_exists(public_path('storage/public/banner/banners/' . $filename)) : false,
                    'file_exists_storage' => $filename ? file_exists(storage_path('app/public/public/banner/banners/' . $filename)) : false,
                ];
            })->toArray()
        ];
        
        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
    });
    
    Route::get('/debug/storage-test', function() {
        $testPath = public_path('storage/public/banner/banners/');
        $files = [];
        
        if (is_dir($testPath)) {
            $files = array_diff(scandir($testPath), ['.', '..']);
        }
        
        return response()->json([
            'test_path' => $testPath,
            'path_exists' => is_dir($testPath),
            'files_found' => array_values($files),
            'file_urls' => array_map(function($file) {
                return asset('storage/public/banner/banners/' . $file);
            }, $files)
        ], 200, [], JSON_PRETTY_PRINT);
    });
}
