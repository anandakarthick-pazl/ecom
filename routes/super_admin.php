<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\CompanyController;
use App\Http\Controllers\SuperAdmin\ThemeController;
use App\Http\Controllers\SuperAdmin\ThemeAssignmentController;
use App\Http\Controllers\SuperAdmin\PackageController;
use App\Http\Controllers\SuperAdmin\SupportTicketController;
use App\Http\Controllers\SuperAdmin\BillingController;
use App\Http\Controllers\SuperAdmin\LandingPageController as SuperAdminLandingPageController;
use App\Http\Controllers\SuperAdmin\SettingsController as SuperAdminSettingsController;
use App\Http\Controllers\SuperAdmin\MenuManagementController;

use App\Http\Controllers\SuperAdminDebugController;

/*
|--------------------------------------------------------------------------
| Super Admin Routes
|--------------------------------------------------------------------------
*/

// Super Admin Domain Routes (Production)
Route::domain('superadmin.rrkcrackers.com')->group(function () {
    // Root redirect to login
    Route::get('/', function () {
        return redirect()->route('super-admin.login');
    });
    
    // All super admin routes under this domain
    Route::prefix('super-admin')->name('super-admin.')->group(function () {
        // Login routes (no middleware)
        Route::get('/login', function () {
            return view('super-admin.auth.login');
        })->name('login')->middleware('guest');
        
        Route::post('/login', function () {
            $credentials = request()->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if (Auth::attempt($credentials, request()->filled('remember'))) {
                $user = Auth::user();
                if (!$user->isSuperAdmin()) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'You do not have access to the Super Admin panel.',
                    ]);
                }
                
                request()->session()->regenerate();
                return redirect()->intended(route('super-admin.dashboard'));
            }

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        })->name('login.post')->middleware('guest');
        
        // All other super admin routes with authentication
        Route::middleware(['auth', 'super-admin'])->group(function () {
            Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
            
            // Company Management
            Route::resource('companies', CompanyController::class);
            Route::patch('/companies/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
            
            // Theme Management
            Route::resource('themes', ThemeController::class);
            Route::post('/themes/{theme}/assign', [ThemeAssignmentController::class, 'assign'])->name('themes.assign');
            
            // Package Management
            Route::resource('packages', PackageController::class);
            
            // Support Tickets
            Route::resource('support-tickets', SupportTicketController::class);
            Route::patch('/support-tickets/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('support-tickets.status');
            
            // Billing Management
            Route::resource('billing', BillingController::class);
            
            // Landing Page Management
            Route::get('/landing-pages', [SuperAdminLandingPageController::class, 'index'])->name('landing-pages.index');
            Route::get('/landing-pages/create', [SuperAdminLandingPageController::class, 'create'])->name('landing-pages.create');
            Route::post('/landing-pages', [SuperAdminLandingPageController::class, 'store'])->name('landing-pages.store');
            Route::get('/landing-pages/{page}/edit', [SuperAdminLandingPageController::class, 'edit'])->name('landing-pages.edit');
            Route::patch('/landing-pages/{page}', [SuperAdminLandingPageController::class, 'update'])->name('landing-pages.update');
            Route::delete('/landing-pages/{page}', [SuperAdminLandingPageController::class, 'destroy'])->name('landing-pages.destroy');
            
            // Settings
            Route::get('/settings', [SuperAdminSettingsController::class, 'index'])->name('settings.index');
            Route::patch('/settings', [SuperAdminSettingsController::class, 'update'])->name('settings.update');
            
            // Menu Management
            Route::get('/menu-management', [MenuManagementController::class, 'index'])->name('menu.index');
            Route::post('/menu-management', [MenuManagementController::class, 'store'])->name('menu.store');
            Route::patch('/menu-management/{menu}', [MenuManagementController::class, 'update'])->name('menu.update');
            Route::delete('/menu-management/{menu}', [MenuManagementController::class, 'destroy'])->name('menu.destroy');
            Route::post('/menu-management/reorder', [MenuManagementController::class, 'reorder'])->name('menu.reorder');
        });
        
        // Logout route
        Route::post('/logout', function () {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('super-admin.login');
        })->name('logout');
    });
});

// Debug Routes (remove in production)
Route::get('/super-admin-debug', [SuperAdminDebugController::class, 'debug']);
Route::post('/super-admin-test-login', [SuperAdminDebugController::class, 'testLogin']);

// Super Admin Routes
Route::prefix('super-admin')->name('super-admin.')->group(function () {
    // Login routes (no middleware)
    Route::get('/login', function () {
        return view('super-admin.auth.login');
    })->name('login')->middleware('guest');
    
    Route::post('/login', function () {
        $credentials = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, request()->filled('remember'))) {
            $user = Auth::user();
            if (!$user->isSuperAdmin()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'You do not have access to the Super Admin panel.',
                ]);
            }
            
            $user->update(['last_login_at' => now()]);
            request()->session()->regenerate();
            return redirect()->intended(route('super-admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    })->name('login.post')->middleware('guest');
    
    // Logout Route
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('home');
    })->name('logout');
});

// Super Admin Routes with middleware
Route::prefix('super-admin')->name('super-admin.')->middleware(['auth', 'super.admin'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    
    // Menu Management
    Route::prefix('menu-management')->name('menu-management.')->group(function () {
        Route::get('/', [MenuManagementController::class, 'index'])->name('index');
        Route::post('/update', [MenuManagementController::class, 'update'])->name('update');
        Route::post('/enable-all', [MenuManagementController::class, 'enableAll'])->name('enable-all');
        Route::post('/disable-all', [MenuManagementController::class, 'disableAll'])->name('disable-all');
        Route::post('/reset-recommended', [MenuManagementController::class, 'resetToRecommended'])->name('reset-recommended');
    });
    
    // Companies Management
    Route::resource('companies', CompanyController::class);
    Route::patch('companies/{company}/status', [CompanyController::class, 'updateStatus'])->name('companies.update-status');
    Route::patch('companies/{company}/extend-trial', [CompanyController::class, 'extendTrial'])->name('companies.extend-trial');
    
    // Themes Management
    Route::resource('themes', ThemeController::class);
    Route::patch('themes/{theme}/toggle-status', [ThemeController::class, 'toggleStatus'])->name('themes.toggle-status');
    Route::post('themes/load-samples', [ThemeController::class, 'loadSampleThemes'])->name('themes.load-samples');
    Route::post('themes/upload-package', [ThemeController::class, 'uploadThemePackage'])->name('themes.upload-package');
    
    // Theme Assignment Management
    Route::prefix('theme-assignments')->name('theme-assignments.')->group(function () {
        Route::get('/', [ThemeAssignmentController::class, 'index'])->name('index');
        Route::post('companies/{company}/assign', [ThemeAssignmentController::class, 'assign'])->name('assign');
        Route::delete('companies/{company}/unassign', [ThemeAssignmentController::class, 'unassign'])->name('unassign');
        Route::get('companies/{company}/themes/{theme}/preview', [ThemeAssignmentController::class, 'preview'])->name('preview');
        Route::post('bulk-assign', [ThemeAssignmentController::class, 'bulkAssign'])->name('bulk-assign');
        Route::get('themes/{theme}/companies', [ThemeAssignmentController::class, 'getCompaniesByTheme'])->name('theme-companies');
        Route::get('stats', [ThemeAssignmentController::class, 'getThemeStats'])->name('stats');
        Route::get('report', [ThemeAssignmentController::class, 'generateReport'])->name('report');
        Route::post('companies/{sourceCompany}/clone-to/{targetCompany}', [ThemeAssignmentController::class, 'cloneTheme'])->name('clone-theme');
        Route::get('companies/{company}/customizations', [ThemeAssignmentController::class, 'getCustomizationOptions'])->name('customizations');
        Route::post('companies/{company}/customizations', [ThemeAssignmentController::class, 'saveCustomizations'])->name('save-customizations');
    });
    
    // Storage Management
    Route::prefix('storage')->name('storage.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\StorageController::class, 'index'])->name('index');
        Route::get('/local', [\App\Http\Controllers\SuperAdmin\StorageController::class, 'localStorage'])->name('local');
        Route::get('/s3', [\App\Http\Controllers\SuperAdmin\StorageController::class, 's3Storage'])->name('s3');
        Route::post('/config', [\App\Http\Controllers\SuperAdmin\StorageController::class, 'updateConfig'])->name('config.update');
        Route::post('/upload', [\App\Http\Controllers\SuperAdmin\StorageController::class, 'uploadFile'])->name('upload');
        Route::delete('/delete', [\App\Http\Controllers\SuperAdmin\StorageController::class, 'deleteFile'])->name('delete');
        Route::post('/directory', [\App\Http\Controllers\SuperAdmin\StorageController::class, 'createDirectory'])->name('directory.create');
        Route::post('/test-connection', [\App\Http\Controllers\SuperAdmin\StorageController::class, 'testConnection'])->name('test-connection');
        Route::post('/sync', [\App\Http\Controllers\SuperAdmin\StorageController::class, 'syncFiles'])->name('sync');
        Route::get('/file-url', [\App\Http\Controllers\SuperAdmin\StorageController::class, 'getFileUrl'])->name('file-url');
        Route::post('/backup', [\App\Http\Controllers\SuperAdmin\StorageController::class, 'backupStorage'])->name('backup');
        Route::post('/cleanup', [\App\Http\Controllers\SuperAdmin\StorageController::class, 'cleanupFiles'])->name('cleanup');
    });
    
    // Data Import Management
    Route::prefix('data-import')->name('data-import.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\DataImportController::class, 'index'])->name('index');
        Route::post('preview', [\App\Http\Controllers\SuperAdmin\DataImportController::class, 'preview'])->name('preview');
        Route::post('import', [\App\Http\Controllers\SuperAdmin\DataImportController::class, 'import'])->name('import');
        Route::post('upload-and-import', [\App\Http\Controllers\SuperAdmin\DataImportController::class, 'uploadAndImport'])->name('upload-and-import');
        Route::get('history', [\App\Http\Controllers\SuperAdmin\DataImportController::class, 'history'])->name('history');
    });
    
    // Packages Management
    Route::resource('packages', PackageController::class);
    Route::patch('packages/{package}/toggle-status', [PackageController::class, 'toggleStatus'])->name('packages.toggle-status');
    
    // Support Tickets
    Route::resource('support', SupportTicketController::class);
    Route::post('support/{support}/respond', [SupportTicketController::class, 'respond'])->name('support.respond');
    Route::patch('support/{support}/update-status', [SupportTicketController::class, 'updateStatus'])->name('support.update-status');
    Route::patch('support/{support}/update-priority', [SupportTicketController::class, 'updatePriority'])->name('support.update-priority');
    Route::patch('support/{support}/assign', [SupportTicketController::class, 'assign'])->name('support.assign');
    
    // Billing Management
    Route::resource('billing', BillingController::class);
    Route::patch('billing/{billing}/mark-paid', [BillingController::class, 'markAsPaid'])->name('billing.mark-paid');
    Route::get('billing/{billing}/invoice', [BillingController::class, 'generateInvoice'])->name('billing.invoice');
    Route::get('billing-reports', [BillingController::class, 'reports'])->name('billing.reports');
    
    // Subscription Management
    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'index'])->name('index');
        Route::get('/{company}', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'show'])->name('show');
        Route::patch('/{company}/extend-trial', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'extendTrial'])->name('extend-trial');
        Route::patch('/{company}/update-subscription', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'updateSubscription'])->name('update-subscription');
        Route::patch('/{company}/suspend', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'suspend'])->name('suspend');
        Route::patch('/{company}/activate', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'activate'])->name('activate');
        Route::post('/check-expired', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'checkExpired'])->name('check-expired');
        Route::get('/expiring-soon', [\App\Http\Controllers\SuperAdmin\SubscriptionController::class, 'expiringSoon'])->name('expiring-soon');
    });
    
    // Settings Management
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SuperAdminSettingsController::class, 'index'])->name('index');
        Route::get('/general', [SuperAdminSettingsController::class, 'general'])->name('general');
        Route::post('/general', [SuperAdminSettingsController::class, 'updateGeneral'])->name('general.update');
        Route::get('/email', [SuperAdminSettingsController::class, 'email'])->name('email');
        Route::post('/email', [SuperAdminSettingsController::class, 'updateEmail'])->name('email.update');
        Route::post('/email/test', [SuperAdminSettingsController::class, 'testEmail'])->name('email.test');
        Route::get('/storage', [SuperAdminSettingsController::class, 'storage'])->name('storage');
        Route::post('/storage', [SuperAdminSettingsController::class, 'updateStorage'])->name('storage.update');
        Route::get('/cache', [SuperAdminSettingsController::class, 'cache'])->name('cache');
        Route::post('/cache/clear', [SuperAdminSettingsController::class, 'clearCache'])->name('cache.clear');
        Route::post('/cache/action', [SuperAdminSettingsController::class, 'cacheAction'])->name('cache.action');
        Route::get('/cache/status', [SuperAdminSettingsController::class, 'cacheStatus'])->name('cache.status');
        Route::post('/cache/update', [SuperAdminSettingsController::class, 'updateCacheSettings'])->name('cache.update-settings');
        Route::get('/backup', [SuperAdminSettingsController::class, 'backup'])->name('backup');
        Route::post('/backup/create', [SuperAdminSettingsController::class, 'createBackup'])->name('backup.create');
        Route::get('/backup/{backup}/download', [SuperAdminSettingsController::class, 'downloadBackup'])->name('backup.download');
        Route::post('/backup/{backup}/restore', [SuperAdminSettingsController::class, 'restoreBackup'])->name('backup.restore');
        Route::delete('/backup/{backup}', [SuperAdminSettingsController::class, 'deleteBackup'])->name('backup.delete');
        Route::get('/backup/{backup}/progress', [SuperAdminSettingsController::class, 'backupProgress'])->name('backup.progress');
        Route::post('/backup/{backup}/cancel', [SuperAdminSettingsController::class, 'cancelBackup'])->name('backup.cancel');
        Route::get('/backup/{backup}/details', [SuperAdminSettingsController::class, 'backupDetails'])->name('backup.details');
        Route::post('/backup/cleanup', [SuperAdminSettingsController::class, 'cleanupBackups'])->name('backup.cleanup');
        Route::post('/backup/settings', [SuperAdminSettingsController::class, 'updateBackupSettings'])->name('backup.update-settings');
    });

    // Landing Page Management
    Route::prefix('landing-page')->name('landing-page.')->group(function () {
        Route::get('/', [SuperAdminLandingPageController::class, 'index'])->name('index');
        Route::get('/edit/{section}', [SuperAdminLandingPageController::class, 'edit'])->name('edit');
        Route::put('/update/{section}', [SuperAdminLandingPageController::class, 'update'])->name('update');
        
        // Direct section routes (for easy access)
        Route::get('/hero', [SuperAdminLandingPageController::class, 'hero'])->name('hero');
        Route::put('/hero', [SuperAdminLandingPageController::class, 'updateHero'])->name('hero.update');
        Route::get('/features', [SuperAdminLandingPageController::class, 'features'])->name('features');
        Route::put('/features', [SuperAdminLandingPageController::class, 'updateFeatures'])->name('features.update');
        Route::get('/pricing', [SuperAdminLandingPageController::class, 'pricing'])->name('pricing');
        Route::put('/pricing', [SuperAdminLandingPageController::class, 'updatePricing'])->name('pricing.update');
        Route::get('/contact', [SuperAdminLandingPageController::class, 'contact'])->name('contact');
        Route::put('/contact', [SuperAdminLandingPageController::class, 'updateContact'])->name('contact.update');
        
        // Section routes (alternative paths with sections prefix)
        Route::prefix('sections')->name('sections.')->group(function () {
            Route::get('/hero', [SuperAdminLandingPageController::class, 'hero'])->name('hero');
            Route::put('/hero', [SuperAdminLandingPageController::class, 'updateHero'])->name('hero.update');
            Route::get('/features', [SuperAdminLandingPageController::class, 'features'])->name('features');
            Route::put('/features', [SuperAdminLandingPageController::class, 'updateFeatures'])->name('features.update');
            Route::get('/pricing', [SuperAdminLandingPageController::class, 'pricing'])->name('pricing');
            Route::put('/pricing', [SuperAdminLandingPageController::class, 'updatePricing'])->name('pricing.update');
            Route::get('/contact', [SuperAdminLandingPageController::class, 'contact'])->name('contact');
            Route::put('/contact', [SuperAdminLandingPageController::class, 'updateContact'])->name('contact.update');
        });
    });
    
    // WhatsApp Configuration
    Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\WhatsAppController::class, 'index'])->name('index');
        Route::get('/company/{company}', [\App\Http\Controllers\SuperAdmin\WhatsAppController::class, 'show'])->name('show');
        Route::post('/company/{company}', [\App\Http\Controllers\SuperAdmin\WhatsAppController::class, 'update'])->name('update');
        Route::post('/company/{company}/test', [\App\Http\Controllers\SuperAdmin\WhatsAppController::class, 'test'])->name('test');
        Route::get('/company/{company}/account-info', [\App\Http\Controllers\SuperAdmin\WhatsAppController::class, 'accountInfo'])->name('account-info');
        Route::get('/company/{company}/usage', [\App\Http\Controllers\SuperAdmin\WhatsAppController::class, 'usage'])->name('usage');
        Route::patch('/company/{company}/toggle', [\App\Http\Controllers\SuperAdmin\WhatsAppController::class, 'toggle'])->name('toggle');
        Route::delete('/company/{company}', [\App\Http\Controllers\SuperAdmin\WhatsAppController::class, 'destroy'])->name('destroy');
        Route::get('/company/{company}/export', [\App\Http\Controllers\SuperAdmin\WhatsAppController::class, 'export'])->name('export');
    });
    
    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SuperAdminSettingsController::class, 'index'])->name('index');
        
        // Email Settings
        Route::get('/email', [SuperAdminSettingsController::class, 'email'])->name('email');
        Route::put('/email', [SuperAdminSettingsController::class, 'updateEmail'])->name('email.update');
        Route::post('/test-email', [SuperAdminSettingsController::class, 'testEmail'])->name('test-email');
        
        // General Settings
        Route::get('/general', [SuperAdminSettingsController::class, 'general'])->name('general');
        Route::put('/general', [SuperAdminSettingsController::class, 'updateGeneral'])->name('general.update');
        
        // Cache Management
        Route::get('/cache', [SuperAdminSettingsController::class, 'cache'])->name('cache');
        Route::post('/cache/action', [SuperAdminSettingsController::class, 'cacheAction'])->name('cache.action');
        Route::get('/cache/status', [SuperAdminSettingsController::class, 'cacheStatus'])->name('cache.status');
        Route::put('/cache', [SuperAdminSettingsController::class, 'updateCacheSettings'])->name('cache.update');
        
        // Backup Management
        Route::get('/backup', [SuperAdminSettingsController::class, 'backup'])->name('backup');
        Route::post('/backup/create', [SuperAdminSettingsController::class, 'createBackup'])->name('backup.create');
        Route::get('/backup/{backup}/download', [SuperAdminSettingsController::class, 'downloadBackup'])->name('backup.download');
        Route::post('/backup/{backup}/restore', [SuperAdminSettingsController::class, 'restoreBackup'])->name('backup.restore');
        Route::delete('/backup/{backup}', [SuperAdminSettingsController::class, 'deleteBackup'])->name('backup.delete');
        Route::get('/backup/{backup}/progress', [SuperAdminSettingsController::class, 'backupProgress'])->name('backup.progress');
        Route::post('/backup/{backup}/cancel', [SuperAdminSettingsController::class, 'cancelBackup'])->name('backup.cancel');
        Route::get('/backup/{backup}/details', [SuperAdminSettingsController::class, 'backupDetails'])->name('backup.details');
        Route::post('/backup/cleanup', [SuperAdminSettingsController::class, 'cleanupBackups'])->name('backup.cleanup');
        Route::put('/backup/settings', [SuperAdminSettingsController::class, 'updateBackupSettings'])->name('backup.settings');
    });
    
    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\UserManagementController::class, 'index'])->name('index');
        Route::get('/admins', [\App\Http\Controllers\SuperAdmin\UserManagementController::class, 'admins'])->name('admins');
        Route::get('/blocked', [\App\Http\Controllers\SuperAdmin\UserManagementController::class, 'blocked'])->name('blocked');
        Route::post('/{user}/block', [\App\Http\Controllers\SuperAdmin\UserManagementController::class, 'block'])->name('block');
        Route::post('/{user}/unblock', [\App\Http\Controllers\SuperAdmin\UserManagementController::class, 'unblock'])->name('unblock');
        Route::delete('/{user}', [\App\Http\Controllers\SuperAdmin\UserManagementController::class, 'destroy'])->name('destroy');
    });
    
    // Analytics & Reports
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'index'])->name('index');
        Route::get('/users', [\App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'users'])->name('users');
        Route::get('/sales', [\App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'sales'])->name('sales');
        Route::get('/growth', [\App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'growth'])->name('growth');
        Route::get('/export', [\App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'export'])->name('export');
        Route::get('/custom', [\App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'custom'])->name('custom');
        Route::post('/custom', [\App\Http\Controllers\SuperAdmin\AnalyticsController::class, 'generateCustom'])->name('custom.generate');
    });
    
    // System Health & Monitoring
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/health', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'health'])->name('health');
        Route::get('/performance', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'performance'])->name('performance');
        Route::get('/logs', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'logs'])->name('logs');
        Route::get('/error-logs', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'errorLogs'])->name('error-logs');
        Route::get('/security-logs', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'securityLogs'])->name('security-logs');
        Route::get('/activity-logs', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'activityLogs'])->name('activity-logs');
        Route::get('/queue', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'queue'])->name('queue');
        Route::get('/scheduler', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'scheduler'])->name('scheduler');
        Route::post('/clear-logs', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'clearLogs'])->name('clear-logs');
    });
    
    // API Management
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\ApiController::class, 'index'])->name('index');
        Route::get('/keys', [\App\Http\Controllers\SuperAdmin\ApiController::class, 'keys'])->name('keys');
        Route::post('/keys', [\App\Http\Controllers\SuperAdmin\ApiController::class, 'createKey'])->name('keys.create');
        Route::delete('/keys/{key}', [\App\Http\Controllers\SuperAdmin\ApiController::class, 'deleteKey'])->name('keys.delete');
        Route::get('/documentation', [\App\Http\Controllers\SuperAdmin\ApiController::class, 'documentation'])->name('documentation');
        Route::get('/webhooks', [\App\Http\Controllers\SuperAdmin\ApiController::class, 'webhooks'])->name('webhooks');
        Route::post('/webhooks', [\App\Http\Controllers\SuperAdmin\ApiController::class, 'createWebhook'])->name('webhooks.create');
        Route::delete('/webhooks/{webhook}', [\App\Http\Controllers\SuperAdmin\ApiController::class, 'deleteWebhook'])->name('webhooks.delete');
    });
    
    // Security Management
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\SecurityController::class, 'index'])->name('index');
        Route::get('/access-control', [\App\Http\Controllers\SuperAdmin\SecurityController::class, 'accessControl'])->name('access-control');
        Route::get('/roles', [\App\Http\Controllers\SuperAdmin\SecurityController::class, 'roles'])->name('roles');
        Route::post('/roles', [\App\Http\Controllers\SuperAdmin\SecurityController::class, 'createRole'])->name('roles.create');
        Route::put('/roles/{role}', [\App\Http\Controllers\SuperAdmin\SecurityController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [\App\Http\Controllers\SuperAdmin\SecurityController::class, 'deleteRole'])->name('roles.delete');
    });
    
    // Content Management
    Route::prefix('content')->name('content.')->group(function () {
        Route::get('/blog', [\App\Http\Controllers\SuperAdmin\ContentController::class, 'blog'])->name('blog');
        Route::get('/media', [\App\Http\Controllers\SuperAdmin\ContentController::class, 'media'])->name('media');
        Route::post('/media/upload', [\App\Http\Controllers\SuperAdmin\ContentController::class, 'uploadMedia'])->name('media.upload');
        Route::delete('/media/{media}', [\App\Http\Controllers\SuperAdmin\ContentController::class, 'deleteMedia'])->name('media.delete');
        Route::get('/templates', [\App\Http\Controllers\SuperAdmin\ContentController::class, 'templates'])->name('templates');
        Route::get('/templates/email', [\App\Http\Controllers\SuperAdmin\ContentController::class, 'emailTemplates'])->name('templates.email');
        Route::put('/templates/email/{template}', [\App\Http\Controllers\SuperAdmin\ContentController::class, 'updateEmailTemplate'])->name('templates.email.update');
    });
    
    // Financial Extensions
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/invoices', [\App\Http\Controllers\SuperAdmin\FinanceController::class, 'invoices'])->name('invoices');
        Route::post('/invoices/generate', [\App\Http\Controllers\SuperAdmin\FinanceController::class, 'generateInvoice'])->name('invoices.generate');
        Route::get('/revenue', [\App\Http\Controllers\SuperAdmin\FinanceController::class, 'revenue'])->name('revenue');
        Route::get('/payment-gateway', [\App\Http\Controllers\SuperAdmin\FinanceController::class, 'paymentGateway'])->name('payment-gateway');
        Route::put('/payment-gateway', [\App\Http\Controllers\SuperAdmin\FinanceController::class, 'updatePaymentGateway'])->name('payment-gateway.update');
    });
    
    // Development Tools
    Route::prefix('dev')->name('dev.')->group(function () {
        Route::get('/artisan', [\App\Http\Controllers\SuperAdmin\DevController::class, 'artisan'])->name('artisan');
        Route::post('/artisan/run', [\App\Http\Controllers\SuperAdmin\DevController::class, 'runArtisan'])->name('artisan.run');
        Route::get('/database', [\App\Http\Controllers\SuperAdmin\DevController::class, 'database'])->name('database');
        Route::post('/database/query', [\App\Http\Controllers\SuperAdmin\DevController::class, 'runQuery'])->name('database.query');
        Route::get('/version', [\App\Http\Controllers\SuperAdmin\DevController::class, 'version'])->name('version');
        Route::get('/setup-wizard', [\App\Http\Controllers\SuperAdmin\DevController::class, 'setupWizard'])->name('setup-wizard');
        Route::post('/setup-wizard', [\App\Http\Controllers\SuperAdmin\DevController::class, 'runSetupWizard'])->name('setup-wizard.run');
        Route::get('/deployment', [\App\Http\Controllers\SuperAdmin\DevController::class, 'deployment'])->name('deployment');
    });
    
    // Mobile App Management
    Route::prefix('mobile')->name('mobile.')->group(function () {
        Route::get('/', [\App\Http\Controllers\SuperAdmin\MobileController::class, 'index'])->name('index');
        Route::get('/settings', [\App\Http\Controllers\SuperAdmin\MobileController::class, 'settings'])->name('settings');
        Route::put('/settings', [\App\Http\Controllers\SuperAdmin\MobileController::class, 'updateSettings'])->name('settings.update');
        Route::get('/push-notifications', [\App\Http\Controllers\SuperAdmin\MobileController::class, 'pushNotifications'])->name('push-notifications');
        Route::post('/push-notifications/send', [\App\Http\Controllers\SuperAdmin\MobileController::class, 'sendPushNotification'])->name('push-notifications.send');
    });
    
    // Enhanced Debug Routes
    Route::prefix('debug')->name('debug.')->group(function () {
        Route::get('/', [SuperAdminDebugController::class, 'index'])->name('index');
        Route::get('/console', [SuperAdminDebugController::class, 'console'])->name('console');
        Route::post('/test-login', [SuperAdminDebugController::class, 'testLogin'])->name('test-login');
        Route::get('/system-info', [SuperAdminDebugController::class, 'systemInfo'])->name('system-info');
        Route::get('/config', [SuperAdminDebugController::class, 'config'])->name('config');
        Route::post('/clear-all-caches', [SuperAdminDebugController::class, 'clearAllCaches'])->name('clear-all-caches');
    });
    
    // Configuration Management
    Route::prefix('config')->name('config.')->group(function () {
        Route::get('/app', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'app'])->name('app');
        Route::put('/app', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'updateApp'])->name('app.update');
        Route::get('/environment', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'environment'])->name('environment');
        Route::put('/environment', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'updateEnvironment'])->name('environment.update');
        Route::get('/features', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'features'])->name('features');
        Route::put('/features', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'updateFeatures'])->name('features.update');
        Route::get('/maintenance', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'maintenance'])->name('maintenance');
        Route::post('/maintenance/enable', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'enableMaintenance'])->name('maintenance.enable');
        Route::post('/maintenance/disable', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'disableMaintenance'])->name('maintenance.disable');
        Route::get('/localization', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'localization'])->name('localization');
        Route::put('/localization', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'updateLocalization'])->name('localization.update');
        Route::get('/smtp', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'smtp'])->name('smtp');
        Route::put('/smtp', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'updateSmtp'])->name('smtp.update');
        Route::post('/smtp/test', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'testSmtp'])->name('smtp.test');
        Route::get('/storage', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'storage'])->name('storage');
        Route::put('/storage', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'updateStorage'])->name('storage.update');
        Route::get('/redis', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'redis'])->name('redis');
        Route::put('/redis', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'updateRedis'])->name('redis.update');
        Route::get('/session', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'session'])->name('session');
        Route::put('/session', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'updateSession'])->name('session.update');
        Route::get('/logging', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'logging'])->name('logging');
        Route::put('/logging', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'updateLogging'])->name('logging.update');
        Route::get('/cors', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'cors'])->name('cors');
        Route::put('/cors', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'updateCors'])->name('cors.update');
        Route::get('/rate-limiting', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'rateLimiting'])->name('rate-limiting');
        Route::put('/rate-limiting', [\App\Http\Controllers\SuperAdmin\ConfigController::class, 'updateRateLimiting'])->name('rate-limiting.update');
    });
    
    // Data Management Extensions
    Route::prefix('data')->name('data.')->group(function () {
        Route::get('/migration', [\App\Http\Controllers\SuperAdmin\DataManagementController::class, 'migration'])->name('migration');
        Route::post('/migration/start', [\App\Http\Controllers\SuperAdmin\DataManagementController::class, 'startMigration'])->name('migration.start');
        Route::get('/sync', [\App\Http\Controllers\SuperAdmin\DataManagementController::class, 'sync'])->name('sync');
        Route::post('/sync/execute', [\App\Http\Controllers\SuperAdmin\DataManagementController::class, 'executeSync'])->name('sync.execute');
        Route::get('/bulk', [\App\Http\Controllers\SuperAdmin\DataManagementController::class, 'bulk'])->name('bulk');
        Route::post('/bulk/execute', [\App\Http\Controllers\SuperAdmin\DataManagementController::class, 'executeBulk'])->name('bulk.execute');
        Route::get('/cleanup', [\App\Http\Controllers\SuperAdmin\DataManagementController::class, 'cleanup'])->name('cleanup');
        Route::post('/cleanup/execute', [\App\Http\Controllers\SuperAdmin\DataManagementController::class, 'executeCleanup'])->name('cleanup.execute');
    });
    
    // Communication Extensions
    Route::prefix('communication')->name('communication.')->group(function () {
        Route::get('/sms', [\App\Http\Controllers\SuperAdmin\CommunicationController::class, 'sms'])->name('sms');
        Route::put('/sms', [\App\Http\Controllers\SuperAdmin\CommunicationController::class, 'updateSms'])->name('sms.update');
        Route::post('/sms/test', [\App\Http\Controllers\SuperAdmin\CommunicationController::class, 'testSms'])->name('sms.test');
        Route::get('/push', [\App\Http\Controllers\SuperAdmin\CommunicationController::class, 'push'])->name('push');
        Route::put('/push', [\App\Http\Controllers\SuperAdmin\CommunicationController::class, 'updatePush'])->name('push.update');
        Route::post('/push/send', [\App\Http\Controllers\SuperAdmin\CommunicationController::class, 'sendPush'])->name('push.send');
        Route::get('/social', [\App\Http\Controllers\SuperAdmin\CommunicationController::class, 'social'])->name('social');
        Route::put('/social', [\App\Http\Controllers\SuperAdmin\CommunicationController::class, 'updateSocial'])->name('social.update');
        Route::get('/automation', [\App\Http\Controllers\SuperAdmin\CommunicationController::class, 'automation'])->name('automation');
        Route::put('/automation', [\App\Http\Controllers\SuperAdmin\CommunicationController::class, 'updateAutomation'])->name('automation.update');
    });
    
    // Database & File Management
    Route::prefix('database')->name('database.')->group(function () {
        Route::get('/manager', [\App\Http\Controllers\SuperAdmin\DatabaseController::class, 'manager'])->name('manager');
        Route::get('/queries', [\App\Http\Controllers\SuperAdmin\DatabaseController::class, 'queries'])->name('queries');
        Route::post('/queries/execute', [\App\Http\Controllers\SuperAdmin\DatabaseController::class, 'executeQuery'])->name('queries.execute');
        Route::get('/backup', [\App\Http\Controllers\SuperAdmin\DatabaseController::class, 'backup'])->name('backup');
        Route::post('/backup/create', [\App\Http\Controllers\SuperAdmin\DatabaseController::class, 'createBackup'])->name('backup.create');
        Route::post('/backup/restore', [\App\Http\Controllers\SuperAdmin\DatabaseController::class, 'restoreBackup'])->name('backup.restore');
    });
    
    Route::prefix('files')->name('files.')->group(function () {
        Route::get('/manager', [\App\Http\Controllers\SuperAdmin\FileManagementController::class, 'manager'])->name('manager');
        Route::get('/storage', [\App\Http\Controllers\SuperAdmin\FileManagementController::class, 'storage'])->name('storage');
        Route::put('/storage', [\App\Http\Controllers\SuperAdmin\FileManagementController::class, 'updateStorage'])->name('storage.update');
        Route::get('/cdn', [\App\Http\Controllers\SuperAdmin\FileManagementController::class, 'cdn'])->name('cdn');
        Route::put('/cdn', [\App\Http\Controllers\SuperAdmin\FileManagementController::class, 'updateCdn'])->name('cdn.update');
    });
    
    // Company Extensions
    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/domains', [CompanyController::class, 'domains'])->name('domains');
        Route::post('/domains', [CompanyController::class, 'updateDomains'])->name('domains.update');
        Route::get('/multi-tenant', [CompanyController::class, 'multiTenant'])->name('multi-tenant');
        Route::put('/multi-tenant', [CompanyController::class, 'updateMultiTenant'])->name('multi-tenant.update');
        Route::get('/resources', [CompanyController::class, 'resources'])->name('resources');
        Route::put('/resources', [CompanyController::class, 'updateResources'])->name('resources.update');
    });
    
    // Financial Extensions
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/subscriptions', [\App\Http\Controllers\SuperAdmin\FinanceController::class, 'subscriptions'])->name('subscriptions');
        Route::get('/taxes', [\App\Http\Controllers\SuperAdmin\FinanceController::class, 'taxes'])->name('taxes');
        Route::put('/taxes', [\App\Http\Controllers\SuperAdmin\FinanceController::class, 'updateTaxes'])->name('taxes.update');
        Route::get('/discounts', [\App\Http\Controllers\SuperAdmin\FinanceController::class, 'discounts'])->name('discounts');
        Route::post('/discounts', [\App\Http\Controllers\SuperAdmin\FinanceController::class, 'createDiscount'])->name('discounts.create');
        Route::get('/currency', [\App\Http\Controllers\SuperAdmin\FinanceController::class, 'currency'])->name('currency');
        Route::put('/currency', [\App\Http\Controllers\SuperAdmin\FinanceController::class, 'updateCurrency'])->name('currency.update');
    });
    
    // Integration Extensions
    Route::prefix('integrations')->name('integrations.')->group(function () {
        Route::get('/shipping', [\App\Http\Controllers\SuperAdmin\IntegrationController::class, 'shipping'])->name('shipping');
        Route::put('/shipping', [\App\Http\Controllers\SuperAdmin\IntegrationController::class, 'updateShipping'])->name('shipping.update');
        Route::get('/inventory', [\App\Http\Controllers\SuperAdmin\IntegrationController::class, 'inventory'])->name('inventory');
        Route::put('/inventory', [\App\Http\Controllers\SuperAdmin\IntegrationController::class, 'updateInventory'])->name('inventory.update');
        Route::get('/crm', [\App\Http\Controllers\SuperAdmin\IntegrationController::class, 'crm'])->name('crm');
        Route::put('/crm', [\App\Http\Controllers\SuperAdmin\IntegrationController::class, 'updateCrm'])->name('crm.update');
    });
    
    // Reports Extensions
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/financial', [\App\Http\Controllers\SuperAdmin\ReportsController::class, 'financial'])->name('financial');
        Route::get('/performance', [\App\Http\Controllers\SuperAdmin\ReportsController::class, 'performance'])->name('performance');
        Route::get('/audit', [\App\Http\Controllers\SuperAdmin\ReportsController::class, 'audit'])->name('audit');
        Route::get('/compliance', [\App\Http\Controllers\SuperAdmin\ReportsController::class, 'compliance'])->name('compliance');
    });
    
    // Tenant Management
    Route::prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/isolation', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'isolation'])->name('isolation');
        Route::put('/isolation', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'updateIsolation'])->name('isolation.update');
        Route::get('/migration', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'migration'])->name('migration');
        Route::post('/migration/execute', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'executeMigration'])->name('migration.execute');
    });
    
    // Environment Management
    Route::prefix('environment')->name('environment.')->group(function () {
        Route::get('/staging', [\App\Http\Controllers\SuperAdmin\EnvironmentController::class, 'staging'])->name('staging');
        Route::put('/staging', [\App\Http\Controllers\SuperAdmin\EnvironmentController::class, 'updateStaging'])->name('staging.update');
        Route::get('/production', [\App\Http\Controllers\SuperAdmin\EnvironmentController::class, 'production'])->name('production');
        Route::put('/production', [\App\Http\Controllers\SuperAdmin\EnvironmentController::class, 'updateProduction'])->name('production.update');
        Route::get('/deployment', [\App\Http\Controllers\SuperAdmin\EnvironmentController::class, 'deployment'])->name('deployment');
        Route::post('/deployment/deploy', [\App\Http\Controllers\SuperAdmin\EnvironmentController::class, 'deploy'])->name('deployment.deploy');
    });
    
    // Automation
    Route::prefix('automation')->name('automation.')->group(function () {
        Route::get('/workflows', [\App\Http\Controllers\SuperAdmin\AutomationController::class, 'workflows'])->name('workflows');
        Route::post('/workflows', [\App\Http\Controllers\SuperAdmin\AutomationController::class, 'createWorkflow'])->name('workflows.create');
        Route::get('/cron', [\App\Http\Controllers\SuperAdmin\AutomationController::class, 'cron'])->name('cron');
        Route::post('/cron', [\App\Http\Controllers\SuperAdmin\AutomationController::class, 'createCronJob'])->name('cron.create');
        Route::get('/triggers', [\App\Http\Controllers\SuperAdmin\AutomationController::class, 'triggers'])->name('triggers');
        Route::post('/triggers', [\App\Http\Controllers\SuperAdmin\AutomationController::class, 'createTrigger'])->name('triggers.create');
        Route::get('/rules', [\App\Http\Controllers\SuperAdmin\AutomationController::class, 'rules'])->name('rules');
        Route::post('/rules', [\App\Http\Controllers\SuperAdmin\AutomationController::class, 'createRule'])->name('rules.create');
    });
    
    // Performance
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/optimization', [\App\Http\Controllers\SuperAdmin\PerformanceController::class, 'optimization'])->name('optimization');
        Route::post('/optimization/run', [\App\Http\Controllers\SuperAdmin\PerformanceController::class, 'runOptimization'])->name('optimization.run');
        Route::get('/minification', [\App\Http\Controllers\SuperAdmin\PerformanceController::class, 'minification'])->name('minification');
        Route::post('/minification/run', [\App\Http\Controllers\SuperAdmin\PerformanceController::class, 'runMinification'])->name('minification.run');
        Route::get('/lazy-loading', [\App\Http\Controllers\SuperAdmin\PerformanceController::class, 'lazyLoading'])->name('lazy-loading');
        Route::put('/lazy-loading', [\App\Http\Controllers\SuperAdmin\PerformanceController::class, 'updateLazyLoading'])->name('lazy-loading.update');
        Route::get('/database', [\App\Http\Controllers\SuperAdmin\PerformanceController::class, 'database'])->name('database');
        Route::post('/database/optimize', [\App\Http\Controllers\SuperAdmin\PerformanceController::class, 'optimizeDatabase'])->name('database.optimize');
        Route::get('/cdn', [\App\Http\Controllers\SuperAdmin\PerformanceController::class, 'cdn'])->name('cdn');
        Route::put('/cdn', [\App\Http\Controllers\SuperAdmin\PerformanceController::class, 'updateCdn'])->name('cdn.update');
    });
    
    // Tools
    Route::prefix('tools')->name('tools.')->group(function () {
        Route::get('/code-generator', [\App\Http\Controllers\SuperAdmin\ToolsController::class, 'codeGenerator'])->name('code-generator');
        Route::post('/code-generator/generate', [\App\Http\Controllers\SuperAdmin\ToolsController::class, 'generateCode'])->name('code-generator.generate');
        Route::get('/mass-email', [\App\Http\Controllers\SuperAdmin\ToolsController::class, 'massEmail'])->name('mass-email');
        Route::post('/mass-email/send', [\App\Http\Controllers\SuperAdmin\ToolsController::class, 'sendMassEmail'])->name('mass-email.send');
        Route::get('/backup-restore', [\App\Http\Controllers\SuperAdmin\ToolsController::class, 'backupRestore'])->name('backup-restore');
        Route::post('/backup-restore/backup', [\App\Http\Controllers\SuperAdmin\ToolsController::class, 'createBackup'])->name('backup-restore.backup');
        Route::post('/backup-restore/restore', [\App\Http\Controllers\SuperAdmin\ToolsController::class, 'restoreBackup'])->name('backup-restore.restore');
        Route::get('/maintenance', [\App\Http\Controllers\SuperAdmin\ToolsController::class, 'maintenance'])->name('maintenance');
        Route::post('/maintenance/run', [\App\Http\Controllers\SuperAdmin\ToolsController::class, 'runMaintenance'])->name('maintenance.run');
    });
});
