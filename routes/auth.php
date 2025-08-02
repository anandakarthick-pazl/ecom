<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Auth\AdminAuthController;

/*
|--------------------------------------------------------------------------
| Authentication Routes - Multi-Tenant Support
|--------------------------------------------------------------------------
*/

// Universal login route that works for both main domain and tenant domains
Route::get('/login', function () {
    $host = request()->getHost();
    
    Log::info('Login page accessed', [
        'domain' => $host,
        'url' => request()->url(),
        'is_localhost' => in_array($host, ['localhost', '127.0.0.1'])
    ]);
    
    // Main domain (localhost) - redirect to super admin or show main login
    if ($host === 'localhost' || $host === '127.0.0.1') {
        // For super admin routes, redirect to super admin login
        if (request()->has('admin_type') && request()->get('admin_type') === 'super') {
            return redirect()->route('super-admin.login');
        }
        
        // Show main domain login page
        return view('auth.main-login', [
            'pageTitle' => 'Admin Login',
            'showCompanySelector' => true
        ]);
    }
    
    // Tenant domain - show tenant-specific login
    try {
        $company = \App\Models\SuperAdmin\Company::where('domain', $host)->first();
        
        if ($company) {
            // Set tenant context
            app()->singleton('current_tenant', function () use ($company) {
                return $company;
            });
            
            return view('auth.tenant-login-modern', ['company' => $company]);
        } else {
            Log::warning('Unknown tenant domain accessed', ['domain' => $host]);
            
            // Redirect to main domain with error
            return redirect()->to('http://localhost:8000/login')
                           ->with('error', 'Company not found for domain: ' . $host);
        }
    } catch (\Exception $e) {
        Log::error('Error loading tenant login', [
            'domain' => $host,
            'error' => $e->getMessage()
        ]);
        
        return redirect()->to('http://localhost:8000/login')
                       ->with('error', 'Unable to load login page');
    }
})->name('login')->middleware('guest');

// Enhanced login POST route with better error handling
Route::post('/login', [App\Http\Controllers\Auth\EnhancedAuthController::class, 'processLogin'])
    ->name('login.post')
    ->middleware('guest');

// Universal login POST route (LEGACY - for backward compatibility)
Route::post('/login-legacy', function () {
    $host = request()->getHost();
    
    // Validate credentials
    $credentials = request()->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    Log::info('Login attempt', [
        'email' => $credentials['email'],
        'domain' => $host,
        'is_localhost' => in_array($host, ['localhost', '127.0.0.1'])
    ]);

    // Attempt authentication
    if (Auth::attempt($credentials, request()->filled('remember'))) {
        $user = Auth::user();
        
        // Handle main domain login
        if ($host === 'localhost' || $host === '127.0.0.1') {
            // Super admin can access from main domain
            if ($user->isSuperAdmin()) {
                $user->update(['last_login_at' => now()]);
                request()->session()->regenerate();
                
                Log::info('Super admin login successful', ['user' => $user->email]);
                return redirect()->intended('/super-admin/dashboard');
            } else {
                // Regular users should use tenant domains
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Please login through your company domain.',
                ]);
            }
        }
        
        // Handle tenant domain login
        try {
            $company = \App\Models\SuperAdmin\Company::where('domain', $host)->first();
            
            if (!$company) {
                Auth::logout();
                Log::warning('Login failed: No company found for domain', ['domain' => $host]);
                return back()->withErrors([
                    'email' => 'Company not found for this domain.',
                ]);
            }
            
            // Set tenant context
            app()->singleton('current_tenant', function () use ($company) {
                return $company;
            });
            
            // Super admin can access any tenant
            if ($user->isSuperAdmin()) {
                session([
                    'selected_company_id' => $company->id,
                    'selected_company_slug' => $company->slug,
                    'selected_company_name' => $company->name,
                    'selected_company_domain' => $company->domain,
                    'acting_as_company_admin' => true,
                    'original_user_company_id' => $user->company_id
                ]);
            } else {
                // Check if user belongs to this company
                if ((int)$user->company_id !== (int)$company->id) {
                    Auth::logout();
                    Log::warning('Login failed: User company mismatch', [
                        'user' => $user->email,
                        'user_company_id' => $user->company_id,
                        'domain_company_id' => $company->id
                    ]);
                    return back()->withErrors([
                        'email' => 'Access denied to this company.',
                    ]);
                }
                
                // Check if user has admin role
                if (!in_array($user->role, ['admin', 'manager'])) {
                    Auth::logout();
                    Log::warning('Login failed: Insufficient privileges', [
                        'user' => $user->email,
                        'role' => $user->role
                    ]);
                    return back()->withErrors([
                        'email' => 'Insufficient privileges for admin access.',
                    ]);
                }
                
                // Set company context
                session([
                    'selected_company_id' => $company->id,
                    'selected_company_slug' => $company->slug,
                    'selected_company_name' => $company->name,
                    'selected_company_domain' => $company->domain,
                ]);
            }
            
            // Login successful
            $user->update(['last_login_at' => now()]);
            request()->session()->regenerate();
            
            Log::info('Tenant login successful', [
                'user' => $user->email,
                'company' => $company->name,
                'role' => $user->role
            ]);
            
            return redirect()->intended('/admin/dashboard');
            
        } catch (\Exception $e) {
            Auth::logout();
            Log::error('Login error', [
                'error' => $e->getMessage(),
                'domain' => $host
            ]);
            
            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again.',
            ]);
        }
    }

    Log::warning('Login failed: Invalid credentials', [
        'email' => $credentials['email'],
        'domain' => $host
    ]);

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->withInput();
})->name('login.post')->middleware('guest');

/*
|--------------------------------------------------------------------------
| Dedicated Admin Authentication Routes
|--------------------------------------------------------------------------
*/

// Dedicated Admin Login Routes - NEW IMPLEMENTATION
Route::get('/admin/login', [\App\Http\Controllers\Auth\AdminAuthController::class, 'showAdminLoginForm'])
    ->name('admin.login.form')
    ->middleware('guest');

Route::post('/admin/login', [\App\Http\Controllers\Auth\AdminAuthController::class, 'adminLogin'])
    ->name('admin.login.submit')
    ->middleware('guest');

/*
|--------------------------------------------------------------------------
| Tenant-Specific Admin Authentication Routes (Legacy)
|--------------------------------------------------------------------------
*/

// Admin Authentication Routes (for tenant companies)
Route::middleware(['tenant'])->group(function () {
    
    // Legacy tenant login routes (for backward compatibility)
    Route::get('/tenant-login', function () {
        return redirect('/login');
    })->name('tenant.login')->middleware('guest');
    
    // Alternative admin login route
    Route::get('/admin/auth', function () {
        return redirect('/login');
    })->name('admin.tenant.login')->middleware('guest');
});

/*
|--------------------------------------------------------------------------
| Password Reset Routes
|--------------------------------------------------------------------------
*/

// Forgot Password Routes
Route::get('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showForgotPasswordForm'])
    ->name('password.request')
    ->middleware('guest');

Route::post('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email')
    ->middleware('guest');

// Reset Password Routes
Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetPasswordForm'])
    ->name('password.reset')
    ->middleware('guest');

Route::post('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'resetPassword'])
    ->name('password.update')
    ->middleware('guest');

// Admin Forgot Password Routes
Route::get('/admin/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showAdminForgotPasswordForm'])
    ->name('admin.password.request')
    ->middleware('guest');

Route::post('/admin/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendAdminResetLinkEmail'])
    ->name('admin.password.email')
    ->middleware('guest');

/*
|--------------------------------------------------------------------------
| Session Management Routes
|--------------------------------------------------------------------------
*/

// Primary logout route - handles both POST and GET for compatibility
Route::match(['get', 'post'], '/logout', function () {
    $host = request()->getHost();
    
    Log::info('Logout initiated', [
        'user' => auth()->user()?->email,
        'domain' => $host,
        'method' => request()->method()
    ]);
    
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    // Clear all session data
    request()->session()->forget([
        'selected_company_id', 
        'selected_company_slug', 
        'selected_company_name', 
        'selected_company_domain', 
        'acting_as_company_admin', 
        'original_user_company_id'
    ]);
    
    // Redirect based on domain
    if ($host === 'localhost' || $host === '127.0.0.1') {
        return redirect('/login')->with('success', 'Logged out successfully');
    } else {
        // For tenant domains, redirect to admin login
        return redirect('/admin/login')->with('success', 'Logged out successfully');
    }
})->name('logout');

// Legacy logout route for backward compatibility
Route::post('/logout-legacy', function () {
    return redirect('/logout');
})->name('logout.legacy');

/*
|--------------------------------------------------------------------------
| Development/Debug Routes
|--------------------------------------------------------------------------
*/

// Debug route to check domain and company setup
Route::get('/debug/domain-check', function() {
    if (!app()->environment(['local', 'development'])) {
        abort(404);
    }
    
    $host = request()->getHost();
    $company = null;
    
    try {
        $company = \App\Models\SuperAdmin\Company::where('domain', $host)->first();
    } catch (\Exception $e) {
        // Handle error
    }
    
    return response()->json([
        'current_domain' => $host,
        'company_found' => !is_null($company),
        'company_details' => $company ? [
            'id' => $company->id,
            'name' => $company->name,
            'domain' => $company->domain,
            'status' => $company->status
        ] : null,
        'all_companies' => \App\Models\SuperAdmin\Company::all()->map(function($c) {
            return [
                'id' => $c->id,
                'name' => $c->name, 
                'domain' => $c->domain,
                'status' => $c->status
            ];
        }),
        'admin_users_for_company' => $company ? \App\Models\User::where('company_id', $company->id)
                                                   ->whereIn('role', ['admin', 'manager'])
                                                   ->get(['id', 'email', 'role', 'status'])
                                                   ->toArray() : [],
        'super_admins' => \App\Models\User::where('is_super_admin', true)
                                          ->get(['id', 'email', 'role', 'status'])
                                          ->toArray(),
        'environment' => app()->environment(),
        'session_id' => session()->getId()
    ]);
})->middleware(['web']);

/*
|--------------------------------------------------------------------------
| Development/Debug Routes (Original)
|--------------------------------------------------------------------------
*/

// Alternative login route for development/testing
Route::get('/admin-login-direct', function() {
    $host = request()->getHost();
    $company = null;
    
    if ($host !== 'localhost' && $host !== '127.0.0.1') {
        $company = \App\Models\SuperAdmin\Company::where('domain', $host)->first();
    }
    
    return response()->json([
        'domain' => $host,
        'company' => $company ? [
            'name' => $company->name,
            'domain' => $company->domain,
            'status' => $company->status
        ] : null,
        'login_url' => request()->getSchemeAndHttpHost() . '/login',
        'tenant_context' => app()->has('current_tenant') ? app('current_tenant')->name : 'none',
        'auth_status' => auth()->check() ? 'authenticated' : 'guest'
    ]);
})->middleware(['web']);

// Session info route for debugging
Route::get('/debug/session-info', function() {
    if (!app()->environment(['local', 'development'])) {
        abort(404);
    }
    
    return response()->json([
        'session_data' => session()->all(),
        'auth_user' => auth()->user() ? [
            'id' => auth()->user()->id,
            'email' => auth()->user()->email,
            'role' => auth()->user()->role,
            'company_id' => auth()->user()->company_id
        ] : null,
        'tenant_context' => app()->has('current_tenant') ? app('current_tenant')->name : 'none',
        'domain' => request()->getHost()
    ]);
})->middleware(['web']);

// Enhanced authentication check route
Route::get('/auth/check', [App\Http\Controllers\Auth\EnhancedAuthController::class, 'checkAuth'])
    ->name('auth.check')
    ->middleware(['web']);

// Legacy auth check route
Route::get('/auth/check-legacy', function() {
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => auth()->user() ? [
            'id' => auth()->user()->id,
            'email' => auth()->user()->email,
            'role' => auth()->user()->role
        ] : null,
        'expires_in' => config('session.lifetime') * 60 // Convert minutes to seconds
    ]);
})->middleware(['web']);
