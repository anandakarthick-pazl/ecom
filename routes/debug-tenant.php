<?php

use Illuminate\Support\Facades\Route;

// Debug route to test tenant resolution (custom domains only)
Route::get('/debug-tenant', function () {
    $host = request()->getHost();
    
    // Find company by custom domain only
    $company = \App\Models\SuperAdmin\Company::where('domain', $host)
                     ->where('status', 'active')
                     ->first();
    
    $allCompanies = \App\Models\SuperAdmin\Company::all(['id', 'name', 'slug', 'domain', 'status']);
    
    return response()->json([
        'request_info' => [
            'host' => $host,
            'url' => request()->url(),
            'path' => request()->path(),
            'lookup_method' => 'custom_domain_only'
        ],
        'company_found' => [
            'exists' => !is_null($company),
            'name' => $company ? $company->name : null,
            'slug' => $company ? $company->slug : null,
            'domain' => $company ? $company->domain : null
        ],
        'all_companies' => $allCompanies->toArray(),
        'tenant_context' => [
            'current_tenant' => app()->bound('current_tenant') ? app('current_tenant')->name ?? 'bound but null' : 'not bound'
        ],
        'note' => 'This system now uses CUSTOM DOMAINS only (no subdomains)'
    ], 200, [], JSON_PRETTY_PRINT);
})->name('debug.tenant');

// Test route for shop page
Route::get('/debug-shop', function () {
    return response()->json([
        'message' => 'Shop route working',
        'host' => request()->getHost(),
        'current_tenant' => app()->bound('current_tenant') ? app('current_tenant')->name ?? 'bound but null' : 'not bound'
    ]);
})->middleware(['tenant']);
