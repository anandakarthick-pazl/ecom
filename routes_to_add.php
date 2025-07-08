<?php

/*
|--------------------------------------------------------------------------
| Super Admin Menu Management Routes
|--------------------------------------------------------------------------
| Add these routes to your existing routes/web.php file or create a separate
| routes/super-admin.php file and include it in your RouteServiceProvider
*/

use App\Http\Controllers\SuperAdmin\MenuManagementController;

// Super Admin Menu Management Routes
Route::prefix('super-admin')->name('super-admin.')->middleware(['auth', 'super-admin'])->group(function () {
    
    // Menu Management Routes
    Route::prefix('menu-management')->name('menu-management.')->group(function () {
        Route::get('/', [MenuManagementController::class, 'index'])->name('index');
        Route::post('/update', [MenuManagementController::class, 'update'])->name('update');
        Route::post('/enable-all', [MenuManagementController::class, 'enableAll'])->name('enable-all');
        Route::post('/disable-all', [MenuManagementController::class, 'disableAll'])->name('disable-all');
        Route::post('/reset-recommended', [MenuManagementController::class, 'resetToRecommended'])->name('reset-recommended');
    });
    
    // Add your other existing super admin routes here...
    /*
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('companies', CompanyController::class);
    Route::resource('users', UserManagementController::class);
    // ... other routes
    */
});
