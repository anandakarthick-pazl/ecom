<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\SuperAdmin\Company;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login form
     */
    public function showAdminLoginForm()
    {
        $host = request()->getHost();
        
        Log::info('Admin login page accessed', [
            'domain' => $host,
            'url' => request()->url(),
            'is_localhost' => in_array($host, ['localhost', '127.0.0.1'])
        ]);
        
        // Main domain (localhost) - show main login
        if ($host === 'localhost' || $host === '127.0.0.1') {
            // Show main domain admin login page
            return view('auth.admin-login', [
                'pageTitle' => 'Admin Login',
                'showCompanySelector' => true,
                'loginType' => 'admin'
            ]);
        }
        
        // Tenant domain - show tenant-specific admin login
        try {
            $company = Company::where('domain', $host)->first();
            
            if ($company) {
                // Set tenant context
                app()->singleton('current_tenant', function () use ($company) {
                    return $company;
                });
                
                Log::info('Showing admin login for tenant', [
                    'company_name' => $company->name,
                    'company_domain' => $company->domain
                ]);
                
                return view('auth.tenant-admin-login', [
                    'company' => $company,
                    'pageTitle' => 'Admin Login - ' . $company->name,
                    'loginType' => 'admin',
                    'isAdminLogin' => true
                ]);
            } else {
                Log::warning('Unknown tenant domain accessed for admin login', ['domain' => $host]);
                
                // For unknown tenant domains, show a login form with error
                return view('auth.tenant-admin-login', [
                    'company' => null,
                    'pageTitle' => 'Admin Login',
                    'loginType' => 'admin',
                    'isAdminLogin' => true,
                    'error' => 'Company not found for domain: ' . $host
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error loading tenant admin login', [
                'domain' => $host,
                'error' => $e->getMessage()
            ]);
            
            return view('auth.tenant-admin-login', [
                'company' => null,
                'pageTitle' => 'Admin Login',
                'loginType' => 'admin',
                'isAdminLogin' => true,
                'error' => 'Unable to load admin login page'
            ]);
        }
    }
    
    /**
     * Handle admin login request
     */
    public function adminLogin(Request $request)
    {
        // Use the enhanced authentication controller for consistent login handling
        $enhancedAuthController = new \App\Http\Controllers\Auth\EnhancedAuthController();
        
        Log::info('Admin login request received', [
            'email' => $request->get('email'),
            'domain' => $request->getHost(),
            'url' => $request->url()
        ]);
        
        return $enhancedAuthController->processLogin($request);
    }
}
