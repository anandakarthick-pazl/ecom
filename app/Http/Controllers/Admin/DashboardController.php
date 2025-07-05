<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use App\Models\PurchaseOrder;
use App\Models\PosSale;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get the current company from the middleware
        $currentCompanyId = session('selected_company_id');
        $currentCompany = $request->current_company;
        
        // Today's stats
        $today = today();
        $todayOrders = Order::where('company_id', $currentCompanyId)
                          ->whereDate('created_at', $today)->count();
        $todayRevenue = Order::where('company_id', $currentCompanyId)
                           ->whereDate('created_at', $today)
                           ->where('status', '!=', 'cancelled')
                           ->sum('total');
        $todayPosRevenue = PosSale::where('company_id', $currentCompanyId)
                                 ->whereDate('created_at', $today)->sum('total_amount');
        $todayTotalRevenue = $todayRevenue + $todayPosRevenue;
        
        // Calculate today's profit/loss
        $todayProfit = $this->calculateProfitForPeriod($today, $today, $currentCompanyId);
        
        // This month's stats
        $currentMonth = now()->startOfMonth();
        $monthlyOrders = Order::where('company_id', $currentCompanyId)
                             ->whereBetween('created_at', [$currentMonth, now()])->count();
        $monthlyRevenue = Order::where('company_id', $currentCompanyId)
                             ->whereBetween('created_at', [$currentMonth, now()])
                             ->where('status', '!=', 'cancelled')
                             ->sum('total');
        $monthlyPosRevenue = PosSale::where('company_id', $currentCompanyId)
                                   ->whereBetween('created_at', [$currentMonth, now()])->sum('total_amount');
        $monthlyTotalRevenue = $monthlyRevenue + $monthlyPosRevenue;
        
        // Calculate monthly profit/loss
        $monthlyProfit = $this->calculateProfitForPeriod($currentMonth, now(), $currentCompanyId);
        
        // Overall stats
        $totalOrders = Order::where('company_id', $currentCompanyId)->count();
        $totalRevenue = Order::where('company_id', $currentCompanyId)
                            ->where('status', '!=', 'cancelled')->sum('total');
        $totalPosRevenue = PosSale::where('company_id', $currentCompanyId)->sum('total_amount');
        $grandTotalRevenue = $totalRevenue + $totalPosRevenue;
        $totalCustomers = Customer::where('company_id', $currentCompanyId)->count();
        $totalProducts = Product::where('company_id', $currentCompanyId)->count();
        
        // New customers today and this month
        $newCustomersToday = Customer::where('company_id', $currentCompanyId)
                                   ->whereDate('created_at', $today)->count();
        $newCustomersThisMonth = Customer::where('company_id', $currentCompanyId)
                                        ->whereBetween('created_at', [$currentMonth, now()])->count();
        
        // Top customers by total spent
        $topCustomers = Customer::where('company_id', $currentCompanyId)
                              ->orderBy('total_spent', 'desc')
                              ->limit(5)
                              ->get();
        
        // Recent customers
        $recentCustomers = Customer::where('company_id', $currentCompanyId)
                                 ->latest()
                                 ->limit(10)
                                 ->get();
        
        // Low stock products
        $lowStockProducts = Product::where('company_id', $currentCompanyId)
                                 ->where('stock', '<=', DB::raw('COALESCE(low_stock_threshold, 5)'))
                                 ->where('is_active', true)
                                 ->orderBy('stock')
                                 ->limit(10)
                                 ->get();
        
        // Recent orders
        $recentOrders = Order::where('company_id', $currentCompanyId)
                           ->with(['customer', 'items'])
                           ->latest()
                           ->limit(15)
                           ->get();
        
        // Order status stats
        $orderStatusStats = Order::where('company_id', $currentCompanyId)
                                ->selectRaw('status, count(*) as count')
                                ->groupBy('status')
                                ->pluck('count', 'status');
        
        // Monthly sales chart data (last 12 months)
        $monthlySales = [];
        $monthlyProfits = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
            
            $sales = Order::where('company_id', $currentCompanyId)
                         ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                         ->where('status', '!=', 'cancelled')
                         ->sum('total');
            $posSales = PosSale::where('company_id', $currentCompanyId)
                              ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                              ->sum('total_amount');
            $totalSales = $sales + $posSales;
            
            $profit = $this->calculateProfitForPeriod($startOfMonth, $endOfMonth, $currentCompanyId);
            
            $monthlySales[] = [
                'month' => $date->format('M Y'),
                'sales' => $totalSales,
                'profit' => $profit
            ];
            
            $monthlyProfits[] = [
                'month' => $date->format('M Y'),
                'profit' => $profit
            ];
        }
        
        // Best selling products
        $bestSellingProducts = Product::where('company_id', $currentCompanyId)
            ->withCount([
                'orderItems as total_quantity_sold' => function($query) use ($currentCompanyId) {
                    $query->select(DB::raw('SUM(quantity)'))
                          ->whereHas('order', function($q) use ($currentCompanyId) {
                              $q->where('company_id', $currentCompanyId);
                          });
                }
            ])
            ->having('total_quantity_sold', '>', 0)
            ->orderBy('total_quantity_sold', 'desc')
            ->limit(10)
            ->get();
        
        // Pending orders that need attention
        $pendingOrders = Order::where('company_id', $currentCompanyId)
                             ->where('status', 'pending')
                             ->whereDate('created_at', '<=', now()->subDays(1))
                             ->count();
        
        // Low stock alert count
        $lowStockCount = Product::where('company_id', $currentCompanyId)
                               ->where('stock', '<=', DB::raw('COALESCE(low_stock_threshold, 5)'))
                               ->where('is_active', true)
                               ->count();
        
        // Inventory value
        $inventoryValue = Product::where('company_id', $currentCompanyId)
                                ->where('is_active', true)
                                ->selectRaw('SUM(stock * COALESCE(cost_price, price)) as total_value')
                                ->value('total_value') ?? 0;
        
        // Revenue by category (this month)
        $categoryRevenue = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.company_id', $currentCompanyId)
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('orders.created_at', [$currentMonth, now()])
            ->select('categories.name', DB::raw('SUM(order_items.total) as revenue'))
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'currentCompany',
            'todayOrders', 'todayRevenue', 'todayTotalRevenue', 'todayProfit',
            'monthlyOrders', 'monthlyRevenue', 'monthlyTotalRevenue', 'monthlyProfit',
            'totalOrders', 'totalRevenue', 'grandTotalRevenue', 'totalCustomers', 'totalProducts',
            'newCustomersToday', 'newCustomersThisMonth', 'topCustomers', 'recentCustomers',
            'lowStockProducts', 'recentOrders', 'orderStatusStats', 'monthlySales',
            'bestSellingProducts', 'pendingOrders', 'lowStockCount', 'inventoryValue',
            'categoryRevenue', 'monthlyProfits'
        ));
    }
    
    private function calculateProfitForPeriod($startDate, $endDate, $companyId)
    {
        // Calculate revenue from orders
        $orderRevenue = Order::where('company_id', $companyId)
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->where('status', '!=', 'cancelled')
                            ->sum('total');
        
        // Calculate revenue from POS sales
        $posRevenue = PosSale::where('company_id', $companyId)
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->sum('total_amount');
        
        $totalRevenue = $orderRevenue + $posRevenue;
        
        // Calculate cost of goods sold (COGS) from orders
        $orderCogs = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.company_id', $companyId)
            ->where('orders.status', '!=', 'cancelled')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->selectRaw('SUM(order_items.quantity * COALESCE(products.cost_price, products.price * 0.6)) as total_cogs')
            ->value('total_cogs') ?? 0;
        
        // Calculate COGS from POS sales (if POS tables exist)
        $posCogs = 0;
        if (DB::getSchemaBuilder()->hasTable('pos_sale_items')) {
            $posCogs = DB::table('pos_sale_items')
                ->join('pos_sales', 'pos_sale_items.pos_sale_id', '=', 'pos_sales.id')
                ->join('products', 'pos_sale_items.product_id', '=', 'products.id')
                ->where('pos_sales.company_id', $companyId)
                ->whereBetween('pos_sales.created_at', [$startDate, $endDate])
                ->selectRaw('SUM(pos_sale_items.quantity * COALESCE(products.cost_price, products.price * 0.6)) as total_cogs')
                ->value('total_cogs') ?? 0;
        }
        
        $totalCogs = $orderCogs + $posCogs;
        
        // Calculate operating expenses (simplified - you can add more expense categories)
        $operatingExpenses = 0;
        if (DB::getSchemaBuilder()->hasTable('purchase_orders')) {
            $operatingExpenses = PurchaseOrder::where('company_id', $companyId)
                                             ->whereBetween('created_at', [$startDate, $endDate])
                                             ->where('status', 'completed')
                                             ->sum('total_amount') ?? 0;
        }
        
        // Gross Profit = Revenue - COGS
        $grossProfit = $totalRevenue - $totalCogs;
        
        // Net Profit = Gross Profit - Operating Expenses
        $netProfit = $grossProfit - $operatingExpenses;
        
        return [
            'revenue' => $totalRevenue,
            'cogs' => $totalCogs,
            'gross_profit' => $grossProfit,
            'operating_expenses' => $operatingExpenses,
            'net_profit' => $netProfit,
            'gross_margin' => $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0,
            'net_margin' => $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0
        ];
    }
}
