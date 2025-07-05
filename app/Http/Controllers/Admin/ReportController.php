<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\PosSale;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\GoodsReceiptNote;
use App\Models\StockAdjustment;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Temporarily comment out Excel imports until package is installed
// use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\CustomerReportExport;
// use App\Exports\SalesReportExport;
// use App\Exports\PurchaseOrderReportExport;
// use App\Exports\PurchaseOrderItemReportExport;
// use App\Exports\GrnReportExport;
// use App\Exports\StockAdjustmentReportExport;
// use App\Exports\IncomeReportExport;
// use App\Exports\InventoryReportExport;

class ReportController extends Controller
{
    public function index()
    {
        $summary = [
            'total_customers' => Customer::count(),
            'total_sales' => PosSale::where('status', 'completed')->sum('total_amount') + 
                           Order::where('status', 'delivered')->sum('total'),
            'total_purchases' => PurchaseOrder::sum('total_amount'),
            'total_products' => Product::count(),
            'monthly_sales' => $this->getMonthlySales(),
            'top_products' => $this->getTopProducts(),
        ];

        return view('admin.reports.index', compact('summary'));
    }

    public function customerReport(Request $request)
    {
        $query = Customer::withCount(['orders', 'posSales'])
            ->withSum('orders as total_online_spent', 'total')
            ->withSum('posSales as total_pos_spent', 'total_amount');

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $customers = $query->latest()->paginate(20);

        if ($request->export) {
            // Temporary CSV export until Excel package is installed
            return $this->exportToCSV($customers->items(), 'customer-report.csv', [
                'Name', 'Email', 'Phone', 'Orders', 'POS Sales', 'Online Spent', 'POS Spent', 'Total Spent', 'Joined Date'
            ], function($customer) {
                $totalSpent = ($customer->total_online_spent ?? 0) + ($customer->total_pos_spent ?? 0);
                return [
                    $customer->name,
                    $customer->email ?? 'N/A',
                    $customer->mobile_number ?? 'N/A',
                    $customer->orders_count ?? 0,
                    $customer->pos_sales_count ?? 0,
                    $customer->total_online_spent ?? 0,
                    $customer->total_pos_spent ?? 0,
                    $totalSpent,
                    $customer->created_at->format('d/m/Y')
                ];
            });
        }

        return view('admin.reports.customers', compact('customers'));
    }

    public function salesReport(Request $request)
    {
        $posQuery = PosSale::with(['cashier', 'items.product'])
            ->where('status', 'completed');
        
        $onlineQuery = Order::with(['customer', 'items.product'])
            ->where('status', 'delivered');

        if ($request->date_from) {
            $posQuery->whereDate('sale_date', '>=', $request->date_from);
            $onlineQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $posQuery->whereDate('sale_date', '<=', $request->date_to);
            $onlineQuery->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->payment_method) {
            $posQuery->where('payment_method', $request->payment_method);
        }

        $posSales = $posQuery->latest()->get();
        $onlineOrders = $onlineQuery->latest()->get();

        $summary = [
            'pos_sales_count' => $posSales->count(),
            'pos_sales_total' => $posSales->sum('total_amount'),
            'online_sales_count' => $onlineOrders->count(),
            'online_sales_total' => $onlineOrders->sum('total'),
            'total_sales' => $posSales->sum('total_amount') + $onlineOrders->sum('total'),
            'cash_sales' => $posSales->where('payment_method', 'cash')->sum('total_amount'),
            'digital_sales' => $posSales->whereIn('payment_method', ['card', 'upi', 'gpay', 'paytm', 'phonepe'])->sum('total_amount'),
        ];

        if ($request->export) {
            // Temporary message until Excel package is installed
            return redirect()->back()->with('info', 'Excel export feature requires Laravel Excel package. Please install it using: composer require maatwebsite/excel');
        }

        return view('admin.reports.sales', compact('posSales', 'onlineOrders', 'summary'));
    }

    public function purchaseOrderReport(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'items.product']);

        if ($request->date_from) {
            $query->whereDate('po_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('po_date', '<=', $request->date_to);
        }

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $purchaseOrders = $query->latest()->paginate(20);
        $suppliers = Supplier::orderBy('name')->get();

        $summary = [
            'total_orders' => $purchaseOrders->total(),
            'total_amount' => PurchaseOrder::when($request->date_from, function($q) use ($request) {
                return $q->whereDate('po_date', '>=', $request->date_from);
            })->when($request->date_to, function($q) use ($request) {
                return $q->whereDate('po_date', '<=', $request->date_to);
            })->sum('total_amount'),
            'pending_orders' => PurchaseOrder::where('status', 'draft')->count(),
            'completed_orders' => PurchaseOrder::where('status', 'received')->count(),
        ];

        if ($request->export) {
            return redirect()->back()->with('info', 'Excel export feature requires Laravel Excel package. Please install it using: composer require maatwebsite/excel');
        }

        return view('admin.reports.purchase-orders', compact('purchaseOrders', 'suppliers', 'summary'));
    }

    public function purchaseOrderItemReport(Request $request)
    {
        $query = PurchaseOrderItem::with(['purchaseOrder.supplier', 'product.category', 'grnItems']);

        if ($request->date_from) {
            $query->whereHas('purchaseOrder', function($q) use ($request) {
                $q->whereDate('po_date', '>=', $request->date_from);
            });
        }

        if ($request->date_to) {
            $query->whereHas('purchaseOrder', function($q) use ($request) {
                $q->whereDate('po_date', '<=', $request->date_to);
            });
        }

        if ($request->supplier_id) {
            $query->whereHas('purchaseOrder', function($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        $items = $query->latest()->paginate(20);
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        $summary = [
            'total_items' => $items->total(),
            'total_value' => PurchaseOrderItem::when($request->date_from, function($q) use ($request) {
                return $q->whereHas('purchaseOrder', function($q2) use ($request) {
                    $q2->whereDate('po_date', '>=', $request->date_from);
                });
            })->when($request->date_to, function($q) use ($request) {
                return $q->whereHas('purchaseOrder', function($q2) use ($request) {
                    $q2->whereDate('po_date', '<=', $request->date_to);
                });
            })->sum('total_price'),
            'total_quantity' => PurchaseOrderItem::sum('quantity'),
            'received_quantity' => DB::table('grn_items')->sum('received_quantity'),
        ];

        if ($request->export) {
            return redirect()->back()->with('info', 'Excel export feature requires Laravel Excel package. Please install it using: composer require maatwebsite/excel');
        }

        return view('admin.reports.purchase-order-items', compact('items', 'suppliers', 'products', 'summary'));
    }

    public function grnReport(Request $request)
    {
        $query = GoodsReceiptNote::with(['supplier', 'purchaseOrder', 'items.product']);

        if ($request->date_from) {
            $query->whereDate('received_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('received_date', '<=', $request->date_to);
        }

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $grns = $query->latest()->paginate(20);
        $suppliers = Supplier::orderBy('name')->get();

        $summary = [
            'total_grns' => $grns->total(),
            'total_received_value' => GoodsReceiptNote::when($request->date_from, function($q) use ($request) {
                return $q->whereDate('received_date', '>=', $request->date_from);
            })->when($request->date_to, function($q) use ($request) {
                return $q->whereDate('received_date', '<=', $request->date_to);
            })->sum('invoice_amount'),
            'pending_grns' => GoodsReceiptNote::where('status', 'pending')->count(),
            'completed_grns' => GoodsReceiptNote::where('status', 'completed')->count(),
        ];

        if ($request->export) {
            return redirect()->back()->with('info', 'Excel export feature requires Laravel Excel package. Please install it using: composer require maatwebsite/excel');
        }

        return view('admin.reports.grns', compact('grns', 'suppliers', 'summary'));
    }

    public function stockAdjustmentReport(Request $request)
    {
        $query = StockAdjustment::with(['creator', 'approver', 'items.product']);

        if ($request->date_from) {
            $query->whereDate('adjustment_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('adjustment_date', '<=', $request->date_to);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        $adjustments = $query->latest()->paginate(20);

        $summary = [
            'total_adjustments' => $adjustments->total(),
            'total_value_impact' => StockAdjustment::when($request->date_from, function($q) use ($request) {
                return $q->whereDate('adjustment_date', '>=', $request->date_from);
            })->when($request->date_to, function($q) use ($request) {
                return $q->whereDate('adjustment_date', '<=', $request->date_to);
            })->get()->sum('total_adjustment_value'),
            'increase_adjustments' => StockAdjustment::where('type', 'increase')->count(),
            'decrease_adjustments' => StockAdjustment::where('type', 'decrease')->count(),
        ];

        if ($request->export) {
            return redirect()->back()->with('info', 'Excel export feature requires Laravel Excel package. Please install it using: composer require maatwebsite/excel');
        }

        return view('admin.reports.stock-adjustments', compact('adjustments', 'summary'));
    }

    public function incomeReport(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        // Income Sources
        $posIncome = PosSale::whereBetween('sale_date', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->sum('total_amount');

        $onlineIncome = Order::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'delivered')
            ->sum('total');

        // Expenses
        $purchaseExpenses = PurchaseOrder::whereBetween('po_date', [$dateFrom, $dateTo])
            ->sum('total_amount');

        $stockAdjustmentLoss = StockAdjustment::whereBetween('adjustment_date', [$dateFrom, $dateTo])
            ->where('type', 'decrease')
            ->get()
            ->sum('total_adjustment_value');

        $totalIncome = $posIncome + $onlineIncome;
        $totalExpenses = $purchaseExpenses + $stockAdjustmentLoss;
        $netProfit = $totalIncome - $totalExpenses;

        $data = [
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'income' => [
                'pos_sales' => $posIncome,
                'online_sales' => $onlineIncome,
                'total' => $totalIncome,
            ],
            'expenses' => [
                'purchases' => $purchaseExpenses,
                'stock_loss' => $stockAdjustmentLoss,
                'total' => $totalExpenses,
            ],
            'net_profit' => $netProfit,
            'profit_margin' => $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0,
        ];

        if ($request->export) {
            return redirect()->back()->with('info', 'Excel export feature requires Laravel Excel package. Please install it using: composer require maatwebsite/excel');
        }

        return view('admin.reports.income', compact('data'));
    }

    public function inventoryReport(Request $request)
    {
        $query = Product::with(['category']);

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->stock_status) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('stock', '>', 0);
                    break;
                case 'low_stock':
                    $query->where('stock', '<=', DB::raw('low_stock_threshold'));
                    break;
                case 'out_of_stock':
                    $query->where('stock', '<=', 0);
                    break;
            }
        }

        $products = $query->orderBy('name')->get();

        $summary = [
            'total_products' => $products->count(),
            'in_stock' => $products->where('stock', '>', 0)->count(),
            'low_stock' => $products->filter(function($p) { return $p->stock <= $p->low_stock_threshold; })->count(),
            'out_of_stock' => $products->where('stock', '<=', 0)->count(),
            'total_stock_value' => $products->sum(function($p) { return $p->stock * $p->cost_price; }),
            'total_selling_value' => $products->sum(function($p) { return $p->stock * $p->price; }),
        ];

        if ($request->export) {
            return redirect()->back()->with('info', 'Excel export feature requires Laravel Excel package. Please install it using: composer require maatwebsite/excel');
        }

        return view('admin.reports.inventory', compact('products', 'summary'));
    }

    public function productReport(Request $request)
    {
        $query = Product::with(['category'])
            ->withSum('orderItems as total_online_sold', 'quantity')
            ->withSum('posSaleItems as total_pos_sold', 'quantity');

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->date_from && $request->date_to) {
            // Add date filtering for sales within the period
            $query->withSum(['orderItems as period_online_sold' => function($q) use ($request) {
                $q->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }], 'quantity')
            ->withSum(['posSaleItems as period_pos_sold' => function($q) use ($request) {
                $q->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }], 'quantity');
        }

        $products = $query->get()->map(function($product) {
            $totalSold = ($product->total_online_sold ?? 0) + ($product->total_pos_sold ?? 0);
            $stockValue = $product->stock * $product->cost_price;
            
            return [
                'product' => $product,
                'total_sold' => $totalSold,
                'stock_value' => $stockValue,
                'potential_revenue' => $product->stock * $product->price,
            ];
        })->sortByDesc('total_sold');

        return view('admin.reports.products', compact('products'));
    }

    private function getMonthlySales()
    {
        $monthlySales = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $posAmount = PosSale::whereYear('sale_date', $month->year)
                ->whereMonth('sale_date', $month->month)
                ->where('status', 'completed')
                ->sum('total_amount');
            
            $onlineAmount = Order::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('status', 'delivered')
                ->sum('total');

            $monthlySales[] = [
                'month' => $month->format('M Y'),
                'amount' => $posAmount + $onlineAmount,
            ];
        }
        return $monthlySales;
    }

    private function getTopProducts($limit = 10)
    {
        return Product::select('products.*')
            ->selectRaw('(COALESCE(SUM(order_items.quantity), 0) + COALESCE(SUM(pos_sale_items.quantity), 0)) as total_sold')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('pos_sale_items', 'products.id', '=', 'pos_sale_items.product_id')
            ->groupBy('products.id')
            ->orderBy('total_sold', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Temporary CSV export helper until Excel package is installed
     */
    private function exportToCSV($data, $filename, $headers, $mapFunction)
    {
        $output = fopen('php://temp', 'r+');
        
        // Add headers
        fputcsv($output, $headers);
        
        // Add data
        foreach ($data as $item) {
            fputcsv($output, $mapFunction($item));
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
