<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SuperAdmin\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Main analytics dashboard
     */
    public function index()
    {
        // Get key metrics for the dashboard
        $metrics = $this->getKeyMetrics();
        $chartData = $this->getChartData();
        $recentActivity = $this->getRecentActivity();

        return view('super-admin.analytics.index', compact('metrics', 'chartData', 'recentActivity'));
    }

    /**
     * User analytics
     */
    public function users(Request $request)
    {
        $dateRange = $this->getDateRange($request);
        
        $userStats = [
            'total_users' => User::count(),
            'new_users_period' => User::whereBetween('created_at', $dateRange)->count(),
            'active_users' => User::whereNull('blocked_at')->count(),
            'blocked_users' => User::whereNotNull('blocked_at')->count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'super_admin_users' => User::where('role', 'super_admin')->count(),
        ];

        // User growth over time
        $userGrowth = $this->getUserGrowthData($dateRange);
        
        // User distribution by company
        $usersByCompany = Company::with('users')
            ->withCount('users')
            ->orderBy('users_count', 'desc')
            ->limit(10)
            ->get();

        // User registration trends
        $registrationTrends = $this->getRegistrationTrends($dateRange);

        // User activity metrics
        $activityMetrics = $this->getUserActivityMetrics($dateRange);

        return view('super-admin.analytics.users', compact(
            'userStats', 'userGrowth', 'usersByCompany', 
            'registrationTrends', 'activityMetrics', 'dateRange'
        ));
    }

    /**
     * Sales analytics
     */
    public function sales(Request $request)
    {
        $dateRange = $this->getDateRange($request);

        // Note: This assumes you have orders/sales models
        // Adjust according to your actual models
        $salesStats = [
            'total_revenue' => 0, // Calculate from your orders table
            'total_orders' => 0,  // Count from orders table
            'average_order_value' => 0,
            'conversion_rate' => 0,
            'top_selling_products' => [], // Get from your products/orders
            'revenue_by_company' => [], // Revenue breakdown by company
        ];

        // Mock data for demonstration - replace with actual queries
        $salesStats = [
            'total_revenue' => 125000.50,
            'total_orders' => 1250,
            'average_order_value' => 100.00,
            'conversion_rate' => 3.5,
            'top_selling_products' => [
                ['name' => 'Herbal Tea Blend', 'sales' => 250, 'revenue' => 12500],
                ['name' => 'Organic Turmeric', 'sales' => 180, 'revenue' => 9000],
                ['name' => 'Ginseng Extract', 'sales' => 150, 'revenue' => 15000],
            ],
            'revenue_by_company' => Company::limit(10)->get()->map(function($company) {
                return [
                    'company' => $company->name,
                    'revenue' => rand(5000, 50000),
                    'orders' => rand(50, 500),
                ];
            }),
        ];

        $salesTrends = $this->getSalesTrendsData($dateRange);

        return view('super-admin.analytics.sales', compact('salesStats', 'salesTrends', 'dateRange'));
    }

    /**
     * Growth metrics
     */
    public function growth(Request $request)
    {
        $dateRange = $this->getDateRange($request);

        $growthMetrics = [
            'user_growth_rate' => $this->calculateGrowthRate('users', $dateRange),
            'company_growth_rate' => $this->calculateGrowthRate('companies', $dateRange),
            'revenue_growth_rate' => $this->calculateRevenueGrowthRate($dateRange),
            'retention_rate' => $this->calculateRetentionRate($dateRange),
            'churn_rate' => $this->calculateChurnRate($dateRange),
        ];

        $growthCharts = [
            'monthly_growth' => $this->getMonthlyGrowthData(),
            'cohort_analysis' => $this->getCohortAnalysisData(),
            'user_lifecycle' => $this->getUserLifecycleData(),
        ];

        return view('super-admin.analytics.growth', compact('growthMetrics', 'growthCharts', 'dateRange'));
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'users');
        $format = $request->get('format', 'csv');
        $dateRange = $this->getDateRange($request);

        switch ($type) {
            case 'users':
                return $this->exportUsersData($format, $dateRange);
            case 'companies':
                return $this->exportCompaniesData($format, $dateRange);
            case 'sales':
                return $this->exportSalesData($format, $dateRange);
            default:
                return back()->with('error', 'Invalid export type.');
        }
    }

    /**
     * Custom reports
     */
    public function custom()
    {
        $availableMetrics = $this->getAvailableMetrics();
        $savedReports = $this->getSavedReports();

        return view('super-admin.analytics.custom', compact('availableMetrics', 'savedReports'));
    }

    /**
     * Generate custom report
     */
    public function generateCustom(Request $request)
    {
        $request->validate([
            'report_name' => 'required|string|max:255',
            'metrics' => 'required|array',
            'metrics.*' => 'string',
            'date_range' => 'required|string',
            'filters' => 'nullable|array',
        ]);

        $dateRange = $this->parseDateRange($request->date_range);
        $metrics = $request->metrics;
        $filters = $request->filters ?? [];

        $reportData = $this->buildCustomReport($metrics, $dateRange, $filters);

        // Save the report configuration for future use
        $this->saveReportConfiguration($request->all());

        return view('super-admin.analytics.custom-report', compact('reportData', 'metrics', 'dateRange'));
    }

    // Helper Methods

    private function getKeyMetrics()
    {
        return [
            'total_users' => User::count(),
            'total_companies' => Company::count(),
            'active_companies' => Company::where('status', 'active')->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_this_week' => User::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
            'new_users_this_month' => User::where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            'blocked_users' => User::whereNotNull('blocked_at')->count(),
            'user_growth_rate' => $this->calculateUserGrowthRate(),
        ];
    }

    private function getChartData()
    {
        // User registrations over the last 30 days
        $userRegistrations = User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Company registrations over the last 30 days
        $companyRegistrations = Company::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'user_registrations' => $userRegistrations,
            'company_registrations' => $companyRegistrations,
        ];
    }

    private function getRecentActivity()
    {
        // Get recent user activities - this would need activity log setup
        return [
            ['type' => 'User Registration', 'description' => '5 new users registered today', 'time' => '2 hours ago'],
            ['type' => 'Company Created', 'description' => 'New company "GreenTech Solutions" created', 'time' => '4 hours ago'],
            ['type' => 'User Blocked', 'description' => 'User blocked for policy violation', 'time' => '6 hours ago'],
        ];
    }

    private function getDateRange(Request $request)
    {
        $range = $request->get('range', '30days');
        
        switch ($range) {
            case '7days':
                return [Carbon::now()->subDays(7), Carbon::now()];
            case '30days':
                return [Carbon::now()->subDays(30), Carbon::now()];
            case '90days':
                return [Carbon::now()->subDays(90), Carbon::now()];
            case '1year':
                return [Carbon::now()->subYear(), Carbon::now()];
            case 'custom':
                return [
                    Carbon::parse($request->get('start_date', Carbon::now()->subDays(30))),
                    Carbon::parse($request->get('end_date', Carbon::now()))
                ];
            default:
                return [Carbon::now()->subDays(30), Carbon::now()];
        }
    }

    private function getUserGrowthData($dateRange)
    {
        return User::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', $dateRange)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getRegistrationTrends($dateRange)
    {
        $trends = [];
        $current = Carbon::parse($dateRange[0]);
        $end = Carbon::parse($dateRange[1]);

        while ($current <= $end) {
            $trends[] = [
                'date' => $current->format('Y-m-d'),
                'registrations' => User::whereDate('created_at', $current)->count(),
            ];
            $current->addDay();
        }

        return $trends;
    }

    private function getUserActivityMetrics($dateRange)
    {
        return [
            'daily_active_users' => User::where('last_login_at', '>=', Carbon::now()->subDay())->count(),
            'weekly_active_users' => User::where('last_login_at', '>=', Carbon::now()->subWeek())->count(),
            'monthly_active_users' => User::where('last_login_at', '>=', Carbon::now()->subMonth())->count(),
            'avg_session_duration' => '15:30', // Mock data - implement based on your session tracking
        ];
    }

    private function getSalesTrendsData($dateRange)
    {
        // Mock sales trends data - replace with actual sales queries
        $trends = [];
        $current = Carbon::parse($dateRange[0]);
        $end = Carbon::parse($dateRange[1]);

        while ($current <= $end) {
            $trends[] = [
                'date' => $current->format('Y-m-d'),
                'revenue' => rand(1000, 5000),
                'orders' => rand(10, 50),
            ];
            $current->addDay();
        }

        return $trends;
    }

    private function calculateGrowthRate($table, $dateRange)
    {
        $current = DB::table($table)
            ->whereBetween('created_at', $dateRange)
            ->count();

        $previous = DB::table($table)
            ->whereBetween('created_at', [
                Carbon::parse($dateRange[0])->subDays(Carbon::parse($dateRange[1])->diffInDays(Carbon::parse($dateRange[0]))),
                $dateRange[0]
            ])
            ->count();

        if ($previous == 0) return 100;
        
        return round((($current - $previous) / $previous) * 100, 2);
    }

    private function calculateRevenueGrowthRate($dateRange)
    {
        // Mock implementation - replace with actual revenue calculation
        return rand(-10, 50) + (rand(0, 99) / 100);
    }

    private function calculateRetentionRate($dateRange)
    {
        // Mock implementation - implement based on your user activity tracking
        return rand(70, 95) + (rand(0, 99) / 100);
    }

    private function calculateChurnRate($dateRange)
    {
        // Mock implementation - implement based on your user activity tracking
        return rand(3, 15) + (rand(0, 99) / 100);
    }

    private function calculateUserGrowthRate()
    {
        $thisMonth = User::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $lastMonth = User::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->count();

        if ($lastMonth == 0) return 100;
        
        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2);
    }

    private function getMonthlyGrowthData()
    {
        // Mock implementation
        return [
            ['month' => 'Jan', 'users' => 120, 'companies' => 15],
            ['month' => 'Feb', 'users' => 135, 'companies' => 18],
            ['month' => 'Mar', 'users' => 145, 'companies' => 22],
            ['month' => 'Apr', 'users' => 160, 'companies' => 25],
            ['month' => 'May', 'users' => 180, 'companies' => 28],
            ['month' => 'Jun', 'users' => 200, 'companies' => 32],
        ];
    }

    private function getCohortAnalysisData()
    {
        // Mock implementation - implement proper cohort analysis
        return [];
    }

    private function getUserLifecycleData()
    {
        // Mock implementation
        return [
            'new' => User::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
            'active' => User::where('last_login_at', '>=', Carbon::now()->subDays(30))->count(),
            'inactive' => User::where('last_login_at', '<', Carbon::now()->subDays(30))->count(),
            'churned' => User::whereNotNull('blocked_at')->count(),
        ];
    }

    private function exportUsersData($format, $dateRange)
    {
        $users = User::with('company')
            ->whereBetween('created_at', $dateRange)
            ->get();

        if ($format === 'csv') {
            $filename = 'users_analytics_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($users) {
                $file = fopen('php://output', 'w');
                
                fputcsv($file, ['ID', 'Name', 'Email', 'Company', 'Role', 'Created At', 'Last Login']);

                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->company ? $user->company->name : 'N/A',
                        $user->role,
                        $user->created_at->format('Y-m-d H:i:s'),
                        $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never',
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        // Add JSON export or other formats as needed
        return response()->json($users);
    }

    private function exportCompaniesData($format, $dateRange)
    {
        $companies = Company::withCount('users')
            ->whereBetween('created_at', $dateRange)
            ->get();

        if ($format === 'csv') {
            $filename = 'companies_analytics_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($companies) {
                $file = fopen('php://output', 'w');
                
                fputcsv($file, ['ID', 'Name', 'Domain', 'Status', 'User Count', 'Created At']);

                foreach ($companies as $company) {
                    fputcsv($file, [
                        $company->id,
                        $company->name,
                        $company->domain,
                        $company->status,
                        $company->users_count,
                        $company->created_at->format('Y-m-d H:i:s'),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return response()->json($companies);
    }

    private function exportSalesData($format, $dateRange)
    {
        // Mock implementation - replace with actual sales data export
        $salesData = [
            ['date' => '2024-01-01', 'revenue' => 1500, 'orders' => 15],
            ['date' => '2024-01-02', 'revenue' => 2300, 'orders' => 23],
            // Add more mock data or implement actual sales query
        ];

        if ($format === 'csv') {
            $filename = 'sales_analytics_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($salesData) {
                $file = fopen('php://output', 'w');
                
                fputcsv($file, ['Date', 'Revenue', 'Orders']);

                foreach ($salesData as $data) {
                    fputcsv($file, [$data['date'], $data['revenue'], $data['orders']]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return response()->json($salesData);
    }

    private function getAvailableMetrics()
    {
        return [
            'users' => [
                'total_users' => 'Total Users',
                'new_users' => 'New Users',
                'active_users' => 'Active Users',
                'blocked_users' => 'Blocked Users',
            ],
            'companies' => [
                'total_companies' => 'Total Companies',
                'active_companies' => 'Active Companies',
                'trial_companies' => 'Trial Companies',
            ],
            'sales' => [
                'total_revenue' => 'Total Revenue',
                'total_orders' => 'Total Orders',
                'average_order_value' => 'Average Order Value',
            ],
        ];
    }

    private function getSavedReports()
    {
        // Mock implementation - implement actual saved reports functionality
        return [
            ['id' => 1, 'name' => 'Monthly User Growth', 'created_at' => Carbon::now()->subDays(5)],
            ['id' => 2, 'name' => 'Company Performance', 'created_at' => Carbon::now()->subDays(10)],
        ];
    }

    private function parseDateRange($rangeString)
    {
        // Parse custom date range format
        switch ($rangeString) {
            case '7days':
                return [Carbon::now()->subDays(7), Carbon::now()];
            case '30days':
                return [Carbon::now()->subDays(30), Carbon::now()];
            case '90days':
                return [Carbon::now()->subDays(90), Carbon::now()];
            case '1year':
                return [Carbon::now()->subYear(), Carbon::now()];
            default:
                return [Carbon::now()->subDays(30), Carbon::now()];
        }
    }

    private function buildCustomReport($metrics, $dateRange, $filters)
    {
        $reportData = [];

        foreach ($metrics as $metric) {
            switch ($metric) {
                case 'total_users':
                    $reportData[$metric] = User::count();
                    break;
                case 'new_users':
                    $reportData[$metric] = User::whereBetween('created_at', $dateRange)->count();
                    break;
                case 'active_users':
                    $reportData[$metric] = User::whereNull('blocked_at')->count();
                    break;
                // Add more metrics as needed
            }
        }

        return $reportData;
    }

    private function saveReportConfiguration($config)
    {
        // Implement saving report configuration for future use
        // This could be stored in a database table
    }
}
