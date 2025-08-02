<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;
use App\Models\SuperAdmin\Company;

class ResetPasswordController extends Controller
{
    /**
     * Display the password reset form
     */
    public function showResetPasswordForm($token)
    {
        $host = request()->getHost();
        $company = null;
        $isAdmin = request()->has('admin');
        
        Log::info('Password reset form accessed', [
            'token' => substr($token, 0, 10) . '...',
            'domain' => $host,
            'is_admin' => $isAdmin
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
        
        return view('auth.reset-password', [
            'token' => $token,
            'company' => $company,
            'pageTitle' => 'Reset Password',
            'isAdmin' => $isAdmin
        ]);
    }

    /**
     * Reset the given user's password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $host = request()->getHost();
        $email = $request->email;
        $token = $request->token;
        $isAdmin = $request->has('admin');
        
        Log::info('Password reset attempted', [
            'email' => $email,
            'domain' => $host,
            'is_admin' => $isAdmin,
            'token' => substr($token, 0, 10) . '...'
        ]);

        try {
            // Check if token exists and is valid
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->where('token', hash('sha256', $token))
                ->first();

            if (!$resetRecord) {
                Log::warning('Password reset failed: Invalid token', [
                    'email' => $email,
                    'token' => substr($token, 0, 10) . '...'
                ]);
                return back()->withErrors(['email' => 'This password reset token is invalid.']);
            }

            // Check if token is not expired (24 hours)
            $tokenAge = Carbon::parse($resetRecord->created_at)->diffInHours(Carbon::now());
            if ($tokenAge > 24) {
                Log::warning('Password reset failed: Token expired', [
                    'email' => $email,
                    'token_age_hours' => $tokenAge
                ]);
                
                // Delete expired token
                DB::table('password_reset_tokens')->where('email', $email)->delete();
                
                return back()->withErrors(['email' => 'This password reset token has expired.']);
            }

            // Find the user
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                Log::warning('Password reset failed: User not found', ['email' => $email]);
                return back()->withErrors(['email' => 'User not found.']);
            }
            
            // For admin reset, verify user has admin privileges
            if ($isAdmin) {
                if (!$user->isSuperAdmin() && !in_array($user->role, ['admin', 'manager'])) {
                    Log::warning('Admin password reset failed: User not admin', [
                        'email' => $email,
                        'role' => $user->role
                    ]);
                    return back()->withErrors(['email' => 'Access denied. Admin privileges required.']);
                }
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

            // Update the user's password
            $user->password = Hash::make($request->password);
            $user->save();

            // Delete the reset token
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            Log::info('Password reset successful', [
                'email' => $email,
                'domain' => $host,
                'is_admin' => $isAdmin
            ]);

            // Redirect to appropriate login page based on context
            if ($isAdmin || $host !== 'localhost') {
                return redirect()->route('admin.login.form')
                    ->with('status', 'Your password has been reset! You can now log in.');
            } else {
                return redirect()->route('login')
                    ->with('status', 'Your password has been reset! You can now log in.');
            }

        } catch (\Exception $e) {
            Log::error('Password reset process failed', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['email' => 'An error occurred while resetting your password. Please try again.']);
        }
    }
}
