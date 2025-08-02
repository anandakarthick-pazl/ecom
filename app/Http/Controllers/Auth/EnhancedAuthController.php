<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\SuperAdmin\Company;
use App\Models\User;

class EnhancedAuthController extends Controller
{
    /**
     * Enhanced login processing with better session handling
     */
    public function processLogin(Request $request)
    {
        $host = $request->getHost();
        
        // Validate credentials
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Enhanced login attempt', [
            'email' => $credentials['email'],
            'domain' => $host,
            'session_id' => session()->getId(),
            'user_agent' => $request->userAgent(),
            'is_tenant_domain' => !in_array($host, ['localhost', '127.0.0.1']),
            'raw_host' => $request->getHost(),
            'full_url' => $request->fullUrl()
        ]);

        // First, check if user exists and verify password manually for better debugging
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            Log::warning('Login failed: User not found', [
                'email' => $credentials['email'],
                'domain' => $host
            ]);
            return back()->withErrors(['email' => 'User not found.'])->withInput();
        }

        Log::info('User found for login', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'user_company_id' => $user->company_id,
            'user_is_super_admin' => $user->isSuperAdmin()
        ]);

        if (!password_verify($credentials['password'], $user->password)) {
            Log::warning('Login failed: Invalid password', [
                'email' => $credentials['email'],
                'domain' => $host
            ]);
            return back()->withErrors(['email' => 'Invalid password.'])->withInput();
        }

        Log::info('Password verification successful', ['user_email' => $user->email]);

        // Handle main domain login (localhost)
        if ($host === 'localhost' || $host === '127.0.0.1') {
            if ($user->isSuperAdmin()) {
                return $this->authenticateUser($user, $request, '/super-admin/dashboard', null);
            } else {
                Log::warning('Regular user attempted main domain login', ['email' => $user->email]);
                return back()->withErrors(['email' => 'Please login through your company domain.']);
            }
        }

        // Handle tenant domain login
        $company = Company::where('domain', $host)->first();
        
        Log::info('Tenant domain login - company lookup', [
            'domain' => $host,
            'company_found' => !is_null($company),
            'company_id' => $company ? $company->id : null,
            'company_name' => $company ? $company->name : null,
            'company_status' => $company ? $company->status : null
        ]);
        
        if (!$company) {
            Log::warning('Login failed: No company found for domain', [
                'domain' => $host,
                'user_email' => $user->email
            ]);
            return back()->withErrors(['email' => 'Company not found for this domain.']);
        }

        if ($company->status !== 'active') {
            Log::warning('Login failed: Company not active', [
                'domain' => $host,
                'company_status' => $company->status,
                'user_email' => $user->email
            ]);
            return back()->withErrors(['email' => 'Company account is not active.']);
        }

        // Check user access permissions
        if ($user->isSuperAdmin()) {
            Log::info('Super admin accessing tenant domain', [
                'user_email' => $user->email,
                'company_name' => $company->name
            ]);
            // Super admin can access any tenant
            return $this->authenticateUser($user, $request, '/admin/dashboard', $company, true);
        } else {
            Log::info('Regular user login attempt', [
                'user_email' => $user->email,
                'user_company_id' => $user->company_id,
                'user_company_id_type' => gettype($user->company_id),
                'domain_company_id' => $company->id,
                'domain_company_id_type' => gettype($company->id),
                'company_match' => (int)$user->company_id === (int)$company->id,
                'user_role' => $user->role,
                'role_allowed' => in_array($user->role, ['admin', 'manager'])
            ]);
            
            // Regular user - check company membership and role
            if ((int)$user->company_id !== (int)$company->id) {
                Log::warning('Login failed: User company mismatch', [
                    'user_email' => $user->email,
                    'user_company_id' => $user->company_id,
                    'domain_company_id' => $company->id,
                    'comparison_result' => (int)$user->company_id === (int)$company->id
                ]);
                return back()->withErrors(['email' => 'Access denied to this company.']);
            }

            if (!in_array($user->role, ['admin', 'manager'])) {
                Log::warning('Login failed: Insufficient privileges', [
                    'user_email' => $user->email,
                    'role' => $user->role,
                    'allowed_roles' => ['admin', 'manager']
                ]);
                return back()->withErrors(['email' => 'Insufficient privileges for admin access.']);
            }

            Log::info('Regular user authorization successful', [
                'user_email' => $user->email,
                'company_name' => $company->name
            ]);
            
            return $this->authenticateUser($user, $request, '/admin/dashboard', $company);
        }
    }

    /**
     * Authenticate user with proper session management
     */
    private function authenticateUser(User $user, Request $request, string $redirectPath, ?Company $company = null, bool $actingAsCompanyAdmin = false)
    {
        Log::info('Starting authentication process', [
            'user_email' => $user->email,
            'user_id' => $user->id,
            'company_name' => $company ? $company->name : 'none',
            'company_id' => $company ? $company->id : null,
            'redirect_path' => $redirectPath,
            'acting_as_company_admin' => $actingAsCompanyAdmin,
            'current_session_id' => session()->getId()
        ]);

        // Start fresh session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        Log::info('Session invalidated and token regenerated', [
            'new_session_id' => session()->getId()
        ]);
        
        // Manual authentication
        Auth::login($user, $request->filled('remember'));
        
        Log::info('User logged in via Auth::login', [
            'auth_check' => Auth::check(),
            'auth_user_id' => Auth::check() ? Auth::id() : null
        ]);
        
        // Update last login
        $user->update(['last_login_at' => now()]);

        // Set company context if applicable
        if ($company) {
            $sessionData = [
                'selected_company_id' => $company->id,
                'selected_company_slug' => $company->slug,
                'selected_company_name' => $company->name,
                'selected_company_domain' => $company->domain,
            ];

            if ($actingAsCompanyAdmin) {
                $sessionData['acting_as_company_admin'] = true;
                $sessionData['original_user_company_id'] = $user->company_id;
            }
            
            // Set session data
            session($sessionData);
            
            Log::info('Company context set in session', [
                'session_data' => $sessionData,
                'session_all' => session()->all()
            ]);

            // Set tenant context in app container
            app()->instance('current_tenant', $company);
            
            Log::info('Tenant context set in app container', [
                'tenant_name' => $company->name
            ]);
        }

        // Regenerate session ID for security
        $request->session()->regenerate();
        
        Log::info('Final session regenerated', [
            'final_session_id' => session()->getId(),
            'auth_still_valid' => Auth::check(),
            'session_data_preserved' => session('selected_company_id')
        ]);

        Log::info('Login successful - preparing redirect', [
            'user_email' => $user->email,
            'company_name' => $company ? $company->name : 'none',
            'redirect_to' => $redirectPath,
            'session_id' => session()->getId(),
            'intended_url' => $request->session()->get('url.intended')
        ]);

        // Clear any previous intended URL and redirect
        $intendedUrl = $request->session()->pull('url.intended', $redirectPath);
        
        Log::info('Redirecting user', [
            'final_redirect_url' => $intendedUrl,
            'is_intended' => $intendedUrl !== $redirectPath
        ]);

        return redirect($intendedUrl);
    }

    /**
     * Enhanced logout with proper session cleanup
     */
    public function processLogout(Request $request)
    {
        $host = $request->getHost();
        $user = auth()->user();
        
        Log::info('Logout initiated', [
            'user_email' => $user ? $user->email : 'none',
            'domain' => $host,
            'session_id' => session()->getId()
        ]);

        // Logout user
        Auth::logout();

        // Invalidate session completely
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Clear all session data
        $request->session()->flush();

        // Clear specific session keys (redundant but ensures cleanup)
        $sessionKeys = [
            'selected_company_id', 
            'selected_company_slug', 
            'selected_company_name', 
            'selected_company_domain', 
            'acting_as_company_admin', 
            'original_user_company_id'
        ];
        
        foreach ($sessionKeys as $key) {
            session()->forget($key);
        }

        // Redirect based on domain
        if ($host === 'localhost' || $host === '127.0.0.1') {
            return redirect('/login')->with('success', 'Logged out successfully');
        } else {
            return redirect('/login')->with('success', 'Logged out successfully');
        }
    }

    /**
     * Check authentication status
     */
    public function checkAuth(Request $request)
    {
        return response()->json([
            'authenticated' => auth()->check(),
            'user' => auth()->user() ? [
                'id' => auth()->user()->id,
                'email' => auth()->user()->email,
                'role' => auth()->user()->role,
                'company_id' => auth()->user()->company_id
            ] : null,
            'session_data' => [
                'company_id' => session('selected_company_id'),
                'company_name' => session('selected_company_name'),
                'company_domain' => session('selected_company_domain')
            ],
            'domain' => $request->getHost(),
            'session_id' => session()->getId()
        ]);
    }
}
