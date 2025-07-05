<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\PurchaseOrderItem;
use App\Models\PosSaleItem;
use App\Models\OrderItem;
use App\Models\StockAdjustmentItem;
use App\Models\GrnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category']);

        // Filters
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

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->orderBy('name')->paginate(20);
        $categories = Category::orderBy('name')->get();

        // Stock summary
        $stockSummary = [
            'total_products' => Product::count(),
            'in_stock' => Product::where('stock', '>', 0)->count(),
            'low_stock' => Product::where('stock', '<=', DB::raw('low_stock_threshold'))->count(),
            'out_of_stock' => Product::where('stock', '<=', 0)->count(),
            'total_value' => Product::selectRaw('SUM(stock * cost_price) as total')->value('total') ?? 0,
        ];

        return view('admin.inventory.index', compact('products', 'categories', 'stockSummary'));
    }

    public function stockMovements(Request $request, Product $product)
    {
        $movements = collect();

        // Purchase Orders
        $purchases = PurchaseOrderItem::with(['purchaseOrder.supplier', 'product'])
            ->where('product_id', $product->id)
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->purchaseOrder->po_date,
                    'type' => 'Purchase Order',
                    'reference' => $item->purchaseOrder->po_number,
                    'party' => $item->purchaseOrder->supplier->display_name ?? 'N/A',
                    'quantity_in' => $item->quantity,
                    'quantity_out' => 0,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total_price,
                ];
            });

        // GRN (Goods Receipt)
        $grns = GrnItem::with(['grn.supplier', 'product'])
            ->where('product_id', $product->id)
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->grn->received_date,
                    'type' => 'Goods Receipt',
                    'reference' => $item->grn->grn_number,
                    'party' => $item->grn->supplier->display_name ?? 'N/A',
                    'quantity_in' => $item->received_quantity,
                    'quantity_out' => 0,
                    'unit_price' => $item->unit_cost,
                    'total' => $item->total_cost,
                ];
            });

        // Sales (POS)
        $posSales = PosSaleItem::with(['posSale', 'product'])
            ->where('product_id', $product->id)
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->posSale->created_at,
                    'type' => 'POS Sale',
                    'reference' => $item->posSale->invoice_number,
                    'party' => $item->posSale->customer_name ?? 'Walk-in Customer',
                    'quantity_in' => 0,
                    'quantity_out' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total_price,
                ];
            });

        // Online Orders
        $onlineOrders = OrderItem::with(['order.customer', 'product'])
            ->where('product_id', $product->id)
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->order->created_at,
                    'type' => 'Online Order',
                    'reference' => $item->order->order_number,
                    'party' => $item->order->customer->name ?? 'Guest',
                    'quantity_in' => 0,
                    'quantity_out' => $item->quantity,
                    'unit_price' => $item->price,
                    'total' => $item->total,
                ];
            });

        // Stock Adjustments
        $adjustments = StockAdjustmentItem::with(['stockAdjustment.creator', 'product'])
            ->where('product_id', $product->id)
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->stockAdjustment->adjustment_date,
                    'type' => 'Stock Adjustment',
                    'reference' => $item->stockAdjustment->adjustment_number,
                    'party' => $item->stockAdjustment->creator->name ?? 'System',
                    'quantity_in' => $item->adjusted_quantity > 0 ? $item->adjusted_quantity : 0,
                    'quantity_out' => $item->adjusted_quantity < 0 ? abs($item->adjusted_quantity) : 0,
                    'unit_price' => $item->unit_cost,
                    'total' => $item->total_cost_impact,
                ];
            });

        // Merge and sort all movements
        $movements = $purchases
            ->concat($grns)
            ->concat($posSales)
            ->concat($onlineOrders)
            ->concat($adjustments)
            ->sortByDesc('date');

        return view('admin.inventory.movements', compact('product', 'movements'));
    }

    public function stockCard(Request $request, Product $product)
    {
        $movements = $this->stockMovements($request, $product)->getData()['movements'];
        
        // Calculate running balance
        $runningBalance = 0;
        $movementsWithBalance = $movements->map(function ($movement) use (&$runningBalance) {
            $runningBalance += $movement['quantity_in'] - $movement['quantity_out'];
            $movement['running_balance'] = $runningBalance;
            return $movement;
        });

        return view('admin.inventory.stock-card', compact('product', 'movementsWithBalance'));
    }

    public function lowStockAlert()
    {
        $lowStockProducts = Product::with('category')
            ->where('stock', '<=', DB::raw('low_stock_threshold'))
            ->where('is_active', true)
            ->orderBy('stock', 'asc')
            ->get();

        return view('admin.inventory.low-stock', compact('lowStockProducts'));
    }

    public function stockValuation()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->get()
            ->map(function ($product) {
                $costValue = $product->stock * $product->cost_price;
                $sellingValue = $product->stock * $product->price;
                
                return [
                    'product' => $product,
                    'cost_value' => $costValue,
                    'selling_value' => $sellingValue,
                    'potential_profit' => $sellingValue - $costValue,
                ];
            });

        $totalCostValue = $products->sum('cost_value');
        $totalSellingValue = $products->sum('selling_value');
        $totalPotentialProfit = $products->sum('potential_profit');

        return view('admin.inventory.valuation', compact(
            'products', 
            'totalCostValue', 
            'totalSellingValue', 
            'totalPotentialProfit'
        ));
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'new_stock' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
        ]);

        $oldStock = $product->stock;
        $newStock = $request->new_stock;
        $adjustment = $newStock - $oldStock;

        $product->update(['stock' => $newStock]);

        // Create stock adjustment record
        $stockAdjustment = \App\Models\StockAdjustment::create([
            'adjustment_date' => now()->toDateString(),
            'type' => $adjustment > 0 ? 'increase' : 'decrease',
            'reason' => $request->reason,
            'status' => 'approved',
            'created_by' => auth()->id(),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        \App\Models\StockAdjustmentItem::create([
            'stock_adjustment_id' => $stockAdjustment->id,
            'product_id' => $product->id,
            'current_stock' => $oldStock,
            'adjusted_quantity' => $adjustment,
            'new_stock' => $newStock,
            'unit_cost' => $product->cost_price,
            'total_cost_impact' => abs($adjustment) * $product->cost_price,
            'reason' => $request->reason,
        ]);

        return redirect()->back()->with('success', 'Stock updated successfully!');
    }
}
