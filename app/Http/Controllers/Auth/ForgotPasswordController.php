<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\SuperAdmin\Company;
use App\Mail\ResetPasswordMail;

class ForgotPasswordController extends Controller
{
    /**
     * Display the forgot password form
     */
    public function showForgotPasswordForm()
    {
        $host = request()->getHost();
        $company = null;
        
        Log::info('Forgot password page accessed', [
            'domain' => $host,
            'is_localhost' => in_array($host, ['localhost', '127.0.0.1'])
        ]);
        
        // Check if it's a tenant domain
        if ($host !== 'localhost' && $host !== '127.0.0.1') {
            $company = Company::where('domain', $host)->first();
            
            if ($company) {
                app()->singleton('current_tenant', function () use ($company) {
                    return $company;
                });
            }
        }
        
        return view('auth.forgot-password', [
            'company' => $company,
            'pageTitle' => 'Forgot Password'
        ]);
    }
    
    /**
     * Display the admin forgot password form
     */
    public function showAdminForgotPasswordForm()
    {
        $host = request()->getHost();
        $company = null;
        
        Log::info('Admin forgot password page accessed', [
            'domain' => $host,
            'is_localhost' => in_array($host, ['localhost', '127.0.0.1'])
        ]);
        
        // Check if it's a tenant domain
        if ($host !== 'localhost' && $host !== '127.0.0.1') {
            $company = Company::where('domain', $host)->first();
            
            if ($company) {
                app()->singleton('current_tenant', function () use ($company) {
                    return $company;
                });
            }
        }
        
        return view('auth.admin-forgot-password', [
            'company' => $company,
            'pageTitle' => 'Admin - Forgot Password',
            'isAdminLogin' => true
        ]);
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $host = request()->getHost();
        $email = $request->email;
        
        Log::info('Password reset requested', [
            'email' => $email,
            'domain' => $host
        ]);

        try {
            // Find user by email
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                Log::warning('Password reset failed: User not found', ['email' => $email]);
                return back()->withErrors(['email' => 'We can\'t find a user with that email address.']);
            }
            
            // For tenant domains, verify user belongs to the company
            if ($host !== 'localhost' && $host !== '127.0.0.1') {
                $company = Company::where('domain', $host)->first();
                
                if (!$company) {
                    Log::warning('Password reset failed: Company not found for domain', ['domain' => $host]);
                    return back()->withErrors(['email' => 'Invalid domain.']);
                }
                
                // Check if user belongs to this company (unless they're super admin)
                if (!$user->isSuperAdmin() && $user->company_id != $company->id) {
                    Log::warning('Password reset failed: User company mismatch', [
                        'email' => $email,
                        'user_company_id' => $user->company_id,
                        'domain_company_id' => $company->id
                    ]);
                    return back()->withErrors(['email' => 'Access denied for this domain.']);
                }
            }
            
            // Generate reset token
            $token = Str::random(64);
            
            // Store reset token in database
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'email' => $email,
                    'token' => hash('sha256', $token),
                    'created_at' => Carbon::now()
                ]
            );
            
            // Generate reset URL
            $resetUrl = url('/reset-password/' . $token);
            
            // Send email
            try {
                Mail::to($user->email)->send(new ResetPasswordMail($user, $token, $resetUrl));
                
                Log::info('Password reset email sent successfully', [
                    'email' => $email,
                    'domain' => $host
                ]);
                
                return back()->with('status', 'We have emailed your password reset link!');
                
            } catch (\Exception $e) {
                Log::error('Failed to send password reset email', [
                    'email' => $email,
                    'error' => $e->getMessage()
                ]);
                
                return back()->withErrors(['email' => 'Unable to send reset email. Please try again later.']);
            }
            
        } catch (\Exception $e) {
            Log::error('Password reset process failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['email' => 'An error occurred. Please try again later.']);
        }
    }
    
    /**
     * Send a reset link for admin users.
     */
    public function sendAdminResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $host = request()->getHost();
        $email = $request->email;
        
        Log::info('Admin password reset requested', [
            'email' => $email,
            'domain' => $host
        ]);

        try {
            // Find user by email
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                Log::warning('Admin password reset failed: User not found', ['email' => $email]);
                return back()->withErrors(['email' => 'We can\'t find a user with that email address.']);
            }
            
            // Verify user has admin privileges
            if (!$user->isSuperAdmin() && !in_array($user->role, ['admin', 'manager'])) {
                Log::warning('Password reset failed: User not admin', [
                    'email' => $email,
                    'role' => $user->role
                ]);
                return back()->withErrors(['email' => 'Access denied. Admin privileges required.']);
            }
            
            // For tenant domains, verify user belongs to the company
            if ($host !== 'localhost' && $host !== '127.0.0.1') {
                $company = Company::where('domain', $host)->first();
                
                if (!$company) {
                    Log::warning('Admin password reset failed: Company not found for domain', ['domain' => $host]);
                    return back()->withErrors(['email' => 'Invalid domain.']);
                }
                
                // Check if user belongs to this company (unless they're super admin)
                if (!$user->isSuperAdmin() && $user->company_id != $company->id) {
                    Log::warning('Admin password reset failed: User company mismatch', [
                        'email' => $email,
                        'user_company_id' => $user->company_id,
                        'domain_company_id' => $company->id
                    ]);
                    return back()->withErrors(['email' => 'Access denied for this domain.']);
                }
            }
            
            // Generate reset token
            $token = Str::random(64);
            
            // Store reset token in database
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'email' => $email,
                    'token' => hash('sha256', $token),
                    'created_at' => Carbon::now()
                ]
            );
            
            // Generate reset URL (admin specific)
            $resetUrl = url('/reset-password/' . $token . '?admin=1');
            
            // Send email
            try {
                Mail::to($user->email)->send(new ResetPasswordMail($user, $token, $resetUrl, true));
                
                Log::info('Admin password reset email sent successfully', [
                    'email' => $email,
                    'domain' => $host
                ]);
                
                return back()->with('status', 'We have emailed your password reset link!');
                
            } catch (\Exception $e) {
                Log::error('Failed to send admin password reset email', [
                    'email' => $email,
                    'error' => $e->getMessage()
                ]);
                
                return back()->withErrors(['email' => 'Unable to send reset email. Please try again later.']);
            }
            
        } catch (\Exception $e) {
            Log::error('Admin password reset process failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['email' => 'An error occurred. Please try again later.']);
        }
    }
}
