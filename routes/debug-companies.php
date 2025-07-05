<?php

use Illuminate\Support\Facades\Route;

// Debug route to show all company access methods
Route::get('/companies-access', function () {
    $companies = \App\Models\SuperAdmin\Company::where('status', 'active')->get();
    
    return view('debug.companies-access', compact('companies'));
})->name('companies.access');
