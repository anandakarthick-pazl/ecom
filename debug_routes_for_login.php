<?php

/**
 * Add this temporary diagnostic route to help identify the login issue
 * Add this to routes/web.php or access via artisan tinker
 */

// Temporary diagnostic route - add this to routes/web.php
Route::get('/debug/login-issue', function() {
    if (!app()->environment(['local', 'development'])) {
        abort(404);
    }
    
    $host = request()->get('domain', 'greenvalleyherbs.local');
    
    $diagnostics = [
        'domain_checked' => $host,
        'company' => null,
        'company_users' => [],
        'admin_users' => [],
        'all_companies' => [],
        'issues' => [],
        'solutions' => []
    ];
    
    try {
        // Check company
        $company = \App\Models\SuperAdmin\Company::where('domain', $host)->first();
        
        if ($company) {
            $diagnostics['company'] = [
                'id' => $company->id,
                'name' => $company->name,
                'domain' => $company->domain,
                'status' => $company->status
            ];
            
            // Check users for this company
            $users = \App\Models\User::where('company_id', $company->id)->get();
            $diagnostics['company_users'] = $users->map(function($user) {
                return [
                    'email' => $user->email,
                    'role' => $user->role,
                    'company_id' => $user->company_id,
                    'is_super_admin' => $user->isSuperAdmin(),
                    'verified' => !is_null($user->email_verified_at)
                ];
            })->toArray();
            
            // Check admin users
            $adminUsers = \App\Models\User::where('company_id', $company->id)
                                        ->whereIn('role', ['admin', 'manager'])
                                        ->get();
            $diagnostics['admin_users'] = $adminUsers->map(function($user) {
                return [
                    'email' => $user->email,
                    'role' => $user->role
                ];
            })->toArray();
            
            // Identify issues
            if ($users->count() === 0) {
                $diagnostics['issues'][] = 'No users found for this company';
                $diagnostics['solutions'][] = 'Create user accounts for company ID: ' . $company->id;
            } elseif ($adminUsers->count() === 0) {
                $diagnostics['issues'][] = 'No admin/manager users found';
                $diagnostics['solutions'][] = 'Update user roles to admin or manager';
            }
            
        } else {
            $diagnostics['issues'][] = 'Company not found for domain: ' . $host;
            $diagnostics['solutions'][] = 'Check if domain exists in companies table';
        }
        
        // Get all companies
        $allCompanies = \App\Models\SuperAdmin\Company::all();
        $diagnostics['all_companies'] = $allCompanies->map(function($comp) {
            return [
                'id' => $comp->id,
                'name' => $comp->name,
                'domain' => $comp->domain,
                'status' => $comp->status
            ];
        })->toArray();
        
    } catch (\Exception $e) {
        $diagnostics['error'] = $e->getMessage();
    }
    
    return response()->json($diagnostics, 200, [], JSON_PRETTY_PRINT);
});

// Also add a simple HTML version
Route::get('/debug/login-issue-html', function() {
    if (!app()->environment(['local', 'development'])) {
        abort(404);
    }
    
    $host = request()->get('domain', 'greenvalleyherbs.local');
    
    $html = '<!DOCTYPE html><html><head><title>Login Issue Diagnostic</title>';
    $html .= '<style>body{font-family:Arial,sans-serif;margin:40px;} .error{color:red;} .success{color:green;} .warning{color:orange;} .section{margin:20px 0; padding:10px; border:1px solid #ddd;}</style>';
    $html .= '</head><body>';
    $html .= '<h1>Login Issue Diagnostic for: ' . $host . '</h1>';
    
    try {
        // Check company
        $company = \App\Models\SuperAdmin\Company::where('domain', $host)->first();
        
        if ($company) {
            $html .= '<div class="section success"><h2>✅ Company Found</h2>';
            $html .= '<p><strong>ID:</strong> ' . $company->id . '</p>';
            $html .= '<p><strong>Name:</strong> ' . $company->name . '</p>';
            $html .= '<p><strong>Domain:</strong> ' . $company->domain . '</p>';
            $html .= '<p><strong>Status:</strong> ' . $company->status . '</p></div>';
            
            // Check users
            $users = \App\Models\User::where('company_id', $company->id)->get();
            
            if ($users->count() > 0) {
                $html .= '<div class="section success"><h2>✅ Users Found (' . $users->count() . ')</h2>';
                foreach ($users as $user) {
                    $html .= '<div style="margin:10px 0; padding:5px; background:#f0f0f0;">';
                    $html .= '<strong>Email:</strong> ' . $user->email . '<br>';
                    $html .= '<strong>Role:</strong> ' . $user->role . '<br>';
                    $html .= '<strong>Company ID:</strong> ' . $user->company_id . '<br>';
                    $html .= '<strong>Super Admin:</strong> ' . ($user->isSuperAdmin() ? 'Yes' : 'No') . '<br>';
                    $html .= '<strong>Verified:</strong> ' . ($user->email_verified_at ? 'Yes' : 'No');
                    $html .= '</div>';
                }
                $html .= '</div>';
                
                // Check admin users
                $adminUsers = \App\Models\User::where('company_id', $company->id)
                                            ->whereIn('role', ['admin', 'manager'])
                                            ->get();
                
                if ($adminUsers->count() > 0) {
                    $html .= '<div class="section success"><h2>✅ Admin/Manager Users Found (' . $adminUsers->count() . ')</h2>';
                    foreach ($adminUsers as $user) {
                        $html .= '<p>• ' . $user->email . ' (Role: ' . $user->role . ')</p>';
                    }
                    $html .= '<div class="success"><h3>✅ Setup appears correct!</h3>';
                    $html .= '<p>The issue might be:</p>';
                    $html .= '<ul><li>Incorrect password</li><li>Session/cache issues</li><li>Browser cookies</li></ul>';
                    $html .= '<p><strong>Try:</strong></p>';
                    $html .= '<ul><li>Clear browser cache and cookies</li><li>Try incognito/private mode</li><li>Check Laravel logs</li></ul></div>';
                    $html .= '</div>';
                } else {
                    $html .= '<div class="section error"><h2>❌ No Admin/Manager Users</h2>';
                    $html .= '<p>Users need "admin" or "manager" role to access tenant admin panel.</p>';
                    $html .= '<p><strong>Solution:</strong> Update user roles to "admin" or "manager"</p></div>';
                }
                
            } else {
                $html .= '<div class="section error"><h2>❌ No Users Found</h2>';
                $html .= '<p>No users are assigned to company ID: ' . $company->id . '</p>';
                $html .= '<p><strong>Solution:</strong> Create user accounts for this company</p></div>';
            }
            
        } else {
            $html .= '<div class="section error"><h2>❌ Company Not Found</h2>';
            $html .= '<p>No company found for domain: ' . $host . '</p>';
            $html .= '<p><strong>Solution:</strong> Check if domain exists in companies table</p>';
            
            // Show all companies
            $allCompanies = \App\Models\SuperAdmin\Company::all();
            $html .= '<h3>All Companies in System:</h3>';
            foreach ($allCompanies as $comp) {
                $html .= '<p>• ' . $comp->name . ' → ' . $comp->domain . ' (ID: ' . $comp->id . ')</p>';
            }
            $html .= '</div>';
        }
        
    } catch (\Exception $e) {
        $html .= '<div class="section error"><h2>❌ Error</h2><p>' . $e->getMessage() . '</p></div>';
    }
    
    $html .= '</body></html>';
    
    return $html;
});
