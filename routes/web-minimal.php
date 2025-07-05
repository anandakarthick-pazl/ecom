<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| MINIMAL ROUTES FOR TESTING
|--------------------------------------------------------------------------
*/

// Test if basic routes work
Route::get('/test-route', function () {
    return 'Routes are working!';
})->name('test');

// Basic cart routes (minimal)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/test', function() { return 'Cart route working'; })->name('cart.test');

// Basic shop route
Route::get('/shop', [HomeController::class, 'index'])->name('shop');

// Basic checkout route
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');

// Home route
Route::get('/', function () {
    return view('welcome');
})->name('home');
