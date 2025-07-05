<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SuperAdmin\Theme;
use App\Models\SuperAdmin\Package;
use App\Models\SuperAdmin\Company;
use App\Models\SuperAdmin\SupportTicket;
use App\Models\SuperAdmin\Billing;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuperAdminDebugController extends Controller
{
    public function debug()
    {
        try {
            $results = [];
            
            // Check database tables
            $tables = ['users', 'themes', 'packages', 'companies', 'support_tickets', 'billings', 'landing_page_settings'];
            foreach ($tables as $table) {
                try {
                    $count = DB::table($table)->count();
                    $results["table_{$table}"] = "EXISTS ({$count} records)";
                } catch (\Exception $e) {
                    $results["table_{$table}"] = "ERROR: " . $e->getMessage();
                }
            }
            
            // Check if super admin user exists
            $superAdmin = User::where('email', 'superadmin@ecomplatform.com')->first();
            $results['super_admin_user'] = $superAdmin ? 'Found' : 'Not Found';
            
            if ($superAdmin) {
                $results['is_super_admin'] = $superAdmin->is_super_admin ? 'Yes' : 'No';
                $results['password_check'] = Hash::check('password123', $superAdmin->password) ? 'Valid' : 'Invalid';
                $results['user_status'] = $superAdmin->status ?? 'No status';
                $results['user_role'] = $superAdmin->role ?? 'No role';
            }
            
            // Check model counts
            try {
                $results['themes_count'] = Theme::count();
                $results['packages_count'] = Package::count();
                $results['companies_count'] = Company::count();
                $results['tickets_count'] = SupportTicket::count();
                $results['billings_count'] = Billing::count();
            } catch (\Exception $e) {
                $results['model_error'] = $e->getMessage();
            }
            
            // Check routes
            try {
                $results['login_route'] = route('super-admin.login');
                $results['dashboard_route'] = route('super-admin.dashboard');
            } catch (\Exception $e) {
                $results['route_error'] = $e->getMessage();
            }
            
            // Check current auth status
            $results['current_user'] = Auth::check() ? Auth::user()->email : 'Not logged in';
            $results['is_current_super_admin'] = Auth::check() && Auth::user()->isSuperAdmin() ? 'Yes' : 'No';
            
            // Check dashboard controller
            try {
                $controller = new \App\Http\Controllers\SuperAdmin\DashboardController();
                $results['dashboard_controller'] = 'EXISTS';
            } catch (\Exception $e) {
                $results['dashboard_controller'] = 'ERROR: ' . $e->getMessage();
            }
            
            // Test dashboard data
            try {
                $stats = [
                    'total_companies' => Company::count(),
                    'active_companies' => Company::where('status', 'active')->count(),
                    'total_revenue' => Billing::where('status', 'paid')->sum('amount') ?? 0,
                ];
                $results['dashboard_data'] = 'OK - ' . json_encode($stats);
            } catch (\Exception $e) {
                $results['dashboard_data'] = 'ERROR: ' . $e->getMessage();
            }
            
            return response()->json($results, 200, [], JSON_PRETTY_PRINT);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Debug failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500, [], JSON_PRETTY_PRINT);
        }
    }
    
    public function testLogin(Request $request)
    {
        $credentials = [
            'email' => 'superadmin@ecomplatform.com',
            'password' => 'password123'
        ];
        
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            $response = [
                'login_success' => true,
                'user_email' => $user->email,
                'is_super_admin' => $user->isSuperAdmin(),
                'redirect_url' => route('super-admin.dashboard')
            ];
            
            return response()->json($response);
        }
        
        return response()->json([
            'login_success' => false,
            'error' => 'Authentication failed'
        ]);
    }
}
