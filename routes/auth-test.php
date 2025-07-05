<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Test Routes
|--------------------------------------------------------------------------
*/

// Test authentication system
Route::get('/test-auth', function () {
    try {
        // Check database connection
        $usersCount = \DB::table('users')->count();
        $companiesCount = \DB::table('companies')->count();
        
        // Get all users
        $users = \App\Models\User::all(['id', 'name', 'email', 'role', 'is_super_admin', 'company_id']);
        $companies = \App\Models\SuperAdmin\Company::where('status', 'active')->get(['id', 'name', 'slug']);
        
        return view('test-auth', compact('users', 'companies', 'usersCount', 'companiesCount'));
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Database error: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->name('test.auth');

// Test login functionality
Route::post('/test-login', function () {
    $credentials = request()->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    
    try {
        $user = \App\Models\User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        $passwordCheck = \Hash::check($credentials['password'], $user->password);
        
        return response()->json([
            'user_found' => true,
            'password_correct' => $passwordCheck,
            'user_data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_super_admin' => $user->is_super_admin,
                'company_id' => $user->company_id,
                'status' => $user->status
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Authentication test error: ' . $e->getMessage()
        ], 500);
    }
})->name('test.login');

// Create test users
Route::get('/create-test-users', function () {
    try {
        // Create Super Admin
        $superAdmin = \App\Models\User::updateOrCreate(
            ['email' => 'superadmin@herbalecom.com'],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@herbalecom.com',
                'password' => \Hash::make('password123'),
                'company_id' => null,
                'role' => 'admin',
                'is_super_admin' => true,
                'status' => 'active'
            ]
        );

        // Create Demo Company
        $demoCompany = \App\Models\SuperAdmin\Company::updateOrCreate(
            ['slug' => 'demo'],
            [
                'name' => 'Demo Company',
                'slug' => 'demo',
                'domain' => 'demo.yourdomain.com',
                'email' => 'demo@herbalecom.com',
                'status' => 'active',
                'trial_ends_at' => now()->addDays(14),
                'created_by' => 1
            ]
        );

        // Create Demo Admin
        $demoAdmin = \App\Models\User::updateOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Demo Admin',
                'email' => 'admin@demo.com',
                'password' => \Hash::make('password123'),
                'company_id' => $demoCompany->id,
                'role' => 'admin',
                'is_super_admin' => false,
                'status' => 'active'
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Test users created successfully',
            'users' => [
                'super_admin' => [
                    'email' => 'superadmin@herbalecom.com',
                    'password' => 'password123',
                    'type' => 'Super Admin'
                ],
                'demo_admin' => [
                    'email' => 'admin@demo.com',
                    'password' => 'password123',
                    'type' => 'Admin (Demo Company)'
                ]
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error creating test users: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
