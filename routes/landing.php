<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyRegistrationController;

/*
|--------------------------------------------------------------------------
| Landing Page Routes  
|--------------------------------------------------------------------------
*/

// Main landing page routes
Route::get('/landing-index', [CompanyRegistrationController::class, 'showRegistrationForm'])->name('landing.index');
Route::get('/landing-pricing', [CompanyRegistrationController::class, 'pricing'])->name('landing.pricing');
Route::get('/landing-features', [CompanyRegistrationController::class, 'features'])->name('landing.features');
Route::get('/landing-contact', [CompanyRegistrationController::class, 'contact'])->name('landing.contact');
Route::post('/landing-contact', [CompanyRegistrationController::class, 'submitContact'])->name('landing.contact.submit');

// Add missing login routes for landing pages
Route::get('/landing-login', function() {
    return redirect()->route('login');
})->name('landing.login');

// Add the missing login.post route for landing context
Route::post('/landing-login', function() {
    // Redirect to main login post route
    $credentials = request()->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    
    return redirect()->route('login')->withInput(request()->only('email', 'remember'));
})->name('login.post');
