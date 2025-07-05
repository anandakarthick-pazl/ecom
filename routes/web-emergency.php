<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyRegistrationController;

/*
|--------------------------------------------------------------------------
| ESSENTIAL Routes Only - Emergency Backup
|--------------------------------------------------------------------------
*/

// HOME ROUTE - Essential for application
Route::get('/', function() {
    return "Laravel Application is Working! Home route is now defined.";
})->name('home');

// LOGIN ROUTE - Essential for authentication  
Route::get('/login', function() {
    return "Login page placeholder - home route should now work.";
})->name('login');

// Basic routes for testing
Route::get('/test-routes', function() {
    $routes = [
        'home' => route('home'),
        'login' => route('login'),
    ];
    
    return response()->json([
        'message' => 'Route test successful',
        'routes' => $routes,
        'domain' => request()->getHost(),
        'timestamp' => now()
    ]);
});

// Landing page routes
Route::get('/features', function() { return 'Features page'; })->name('features');
Route::get('/pricing', function() { return 'Pricing page'; })->name('pricing');
Route::get('/contact', function() { return 'Contact page'; })->name('contact');
