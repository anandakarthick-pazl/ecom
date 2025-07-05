<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Cart API endpoints for AJAX requests
    Route::prefix('cart')->group(function () {
        Route::post('/add', [CartController::class, 'add']);
        Route::put('/update', [CartController::class, 'update']);
        Route::delete('/remove', [CartController::class, 'remove']);
        Route::delete('/clear', [CartController::class, 'clear']);
        Route::get('/count', [CartController::class, 'count']);
    });
});
