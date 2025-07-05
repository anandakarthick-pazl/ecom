<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Debug Authentication Routes
|--------------------------------------------------------------------------
*/

// Debug login page
Route::get('/debug-login', function () {
    $companies = \App\Models\SuperAdmin\Company::where('status', 'active')->get(['name', 'slug']);
    $users = \App\Models\User::all(['id', 'name', 'email', 'role', 'is_super_admin', 'company_id']);
    
    return view('debug-login', compact('companies', 'users'));
})->name('debug.login');

// Debug login processing
Route::post('/debug-login', function () {
    $email = request('email');
    $password = request('password');
    $loginType = request('login_type');
    $companySlug = request('company_slug');
    
    $log = [];
    $log[] = "=== DEBUG LOGIN ATTEMPT ===";
    $log[] = "Email: " . $email;
    $log[] = "Password: " . $password;
    $log[] = "Login Type: " . $loginType;
    $log[] = "Company Slug: " . $companySlug;
    
    // Step 1: Find user
    $user = \App\Models\User::where('email', $email)->first();
    if (!$user) {
        $log[] = "❌ USER NOT FOUND";
        return response()->json(['log' => $log, 'success' => false]);
    }
    $log[] = "✅ User found: " . $user->name;
    
    // Step 2: Check password
    $passwordCheck = Hash::check($password, $user->password);
    if (!$passwordCheck) {
        $log[] = "❌ PASSWORD INCORRECT";
        return response()->json(['log' => $log, 'success' => false]);
    }
    $log[] = "✅ Password correct";
    
    // Step 3: Check user status
    if ($user->status !== 'active') {
        $log[] = "❌ USER NOT ACTIVE: " . $user->status;
        return response()->json(['log' => $log, 'success' => false]);
    }
    $log[] = "✅ User is active";
    
    // Step 4: Validate login type
    if ($loginType === 'super_admin') {
        if (!$user->isSuperAdmin()) {
            $log[] = "❌ USER IS NOT SUPER ADMIN";
            return response()->json(['log' => $log, 'success' => false]);
        }
        $log[] = "✅ User is Super Admin";
        
        // Step 5: Attempt login
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $log[] = "✅ AUTH::ATTEMPT SUCCESS";
            $log[] = "✅ Should redirect to: " . route('super-admin.dashboard');
            Auth::logout(); // Don't actually log in during debug
            return response()->json(['log' => $log, 'success' => true, 'redirect' => route('super-admin.dashboard')]);
        } else {
            $log[] = "❌ AUTH::ATTEMPT FAILED";
            return response()->json(['log' => $log, 'success' => false]);
        }
        
    } elseif ($loginType === 'admin') {
        if (empty($companySlug)) {
            $log[] = "❌ NO COMPANY SELECTED";
            return response()->json(['log' => $log, 'success' => false]);
        }
        
        // Find company
        $company = \App\Models\SuperAdmin\Company::where('slug', $companySlug)->first();
        if (!$company) {
            $log[] = "❌ COMPANY NOT FOUND: " . $companySlug;
            return response()->json(['log' => $log, 'success' => false]);
        }
        $log[] = "✅ Company found: " . $company->name;
        
        // Check user access to company
        if (!$user->isSuperAdmin() && (int)$user->company_id !== (int)$company->id) {
            $log[] = "❌ USER DOES NOT BELONG TO COMPANY";
            $log[] = "   User Company ID: " . $user->company_id . " (type: " . gettype($user->company_id) . ")";
            $log[] = "   Selected Company ID: " . $company->id . " (type: " . gettype($company->id) . ")";
            $log[] = "   Int comparison: " . (int)$user->company_id . " !== " . (int)$company->id;
            return response()->json(['log' => $log, 'success' => false]);
        }
        $log[] = "✅ User has access to company";
        
        // Step 5: Attempt login
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $log[] = "✅ AUTH::ATTEMPT SUCCESS";
            $log[] = "✅ Should redirect to: " . route('admin.dashboard');
            Auth::logout(); // Don't actually log in during debug
            return response()->json(['log' => $log, 'success' => true, 'redirect' => route('admin.dashboard')]);
        } else {
            $log[] = "❌ AUTH::ATTEMPT FAILED";
            return response()->json(['log' => $log, 'success' => false]);
        }
    } else {
        $log[] = "❌ INVALID LOGIN TYPE: " . $loginType;
        return response()->json(['log' => $log, 'success' => false]);
    }
})->name('debug.login.post');

// Quick user creation
Route::get('/quick-create-users', function () {
    try {
        // Create Super Admin
        $superAdmin = \App\Models\User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_super_admin' => true,
                'status' => 'active',
                'company_id' => null
            ]
        );

        // Create Demo Company
        $company = \App\Models\SuperAdmin\Company::updateOrCreate(
            ['slug' => 'demo'],
            [
                'name' => 'Demo Company',
                'slug' => 'demo',
                'domain' => 'demo.yourdomain.com',
                'email' => 'demo@demo.com',
                'status' => 'active',
                'trial_ends_at' => now()->addDays(30),
                'created_by' => 1
            ]
        );

        // Create Demo Admin
        $demoAdmin = \App\Models\User::updateOrCreate(
            ['email' => 'demo@demo.com'],
            [
                'name' => 'Demo Admin',
                'email' => 'demo@demo.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_super_admin' => false,
                'status' => 'active',
                'company_id' => $company->id
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Users created successfully',
            'users' => [
                'super_admin' => [
                    'email' => 'admin@admin.com',
                    'password' => 'password'
                ],
                'demo_admin' => [
                    'email' => 'demo@demo.com',
                    'password' => 'password',
                    'company' => 'Demo Company'
                ]
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});
