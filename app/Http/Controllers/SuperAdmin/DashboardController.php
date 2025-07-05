<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\Company;
use App\Models\SuperAdmin\Theme;
use App\Models\SuperAdmin\Package;
use App\Models\SuperAdmin\SupportTicket;
use App\Models\SuperAdmin\Billing;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $stats = [
                'total_companies' => Company::count(),
                'active_companies' => Company::where('status', 'active')->count(),
                'trial_companies' => Company::whereNotNull('trial_ends_at')
                                          ->where('trial_ends_at', '>', now())->count(),
                'expired_companies' => Company::where('subscription_ends_at', '<', now())->count(),
                'total_revenue' => Billing::where('status', 'paid')->sum('amount') ?? 0,
                'monthly_revenue' => Billing::where('status', 'paid')
                                           ->whereMonth('paid_at', now()->month)
                                           ->whereYear('paid_at', now()->year)
                                           ->sum('amount') ?? 0,
                'open_tickets' => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
                'total_users' => User::where('is_super_admin', false)->count()
            ];

            $recentCompanies = Company::with(['theme', 'package'])
                                     ->latest()
                                     ->take(5)
                                     ->get();

            $expiringTrials = Company::where('trial_ends_at', '<=', now()->addDays(7))
                                    ->where('trial_ends_at', '>', now())
                                    ->with(['theme', 'package'])
                                    ->get();

            $recentTickets = SupportTicket::with(['company', 'user'])
                                         ->latest()
                                         ->take(5)
                                         ->get();

            $monthlyRevenue = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthlyRevenue[$i] = Billing::where('status', 'paid')
                                            ->whereMonth('paid_at', $i)
                                            ->whereYear('paid_at', now()->year)
                                            ->sum('amount') ?? 0;
            }

            return view('super-admin.dashboard', compact(
                'stats', 
                'recentCompanies', 
                'expiringTrials', 
                'recentTickets',
                'monthlyRevenue'
            ));
        } catch (\Exception $e) {
            \Log::error('Super Admin Dashboard Error: ' . $e->getMessage());
            
            // Fallback data if database queries fail
            $stats = [
                'total_companies' => 0,
                'active_companies' => 0,
                'trial_companies' => 0,
                'expired_companies' => 0,
                'total_revenue' => 0,
                'monthly_revenue' => 0,
                'open_tickets' => 0,
                'total_users' => 0
            ];
            
            $recentCompanies = collect();
            $expiringTrials = collect();
            $recentTickets = collect();
            $monthlyRevenue = array_fill(1, 12, 0);
            
            return view('super-admin.dashboard', compact(
                'stats', 
                'recentCompanies', 
                'expiringTrials', 
                'recentTickets',
                'monthlyRevenue'
            ))->with('error', 'Some dashboard data could not be loaded. Please check the database.');
        }
    }
}
