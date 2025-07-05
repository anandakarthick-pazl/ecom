<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $query = StockAdjustment::with(['creator', 'approver']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('adjustment_number', 'like', '%' . $request->search . '%')
                  ->orWhere('reason', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->date_from) {
            $query->whereDate('adjustment_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('adjustment_date', '<=', $request->date_to);
        }

        $adjustments = $query->latest()->paginate(20);

        return view('admin.stock-adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $products = Product::active()->orderBy('name')->get();
        return view('admin.stock-adjustments.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'adjustment_date' => 'required|date',
            'type' => 'required|in:increase,decrease,recount',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.current_stock' => 'required|integer|min:0',
            'items.*.adjusted_quantity' => 'required|integer',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
            'items.*.reason' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Create stock adjustment
            $adjustment = StockAdjustment::create([
                'adjustment_date' => $request->adjustment_date,
                'type' => $request->type,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'status' => 'draft'
            ]);

            // Create adjustment items
            foreach ($request->items as $item) {
                $newStock = $item['current_stock'] + $item['adjusted_quantity'];
                
                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $item['product_id'],
                    'current_stock' => $item['current_stock'],
                    'adjusted_quantity' => $item['adjusted_quantity'],
                    'new_stock' => $newStock,
                    'unit_cost' => $item['unit_cost'] ?? 0,
                    'total_cost_impact' => abs($item['adjusted_quantity']) * ($item['unit_cost'] ?? 0),
                    'reason' => $item['reason'] ?? null
                ]);
            }

            DB::commit();

            return redirect()->route('admin.stock-adjustments.show', $adjustment)
                           ->with('success', 'Stock adjustment created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error creating stock adjustment: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['items.product', 'creator', 'approver']);
        return view('admin.stock-adjustments.show', compact('stockAdjustment'));
    }

    public function edit(StockAdjustment $stockAdjustment)
    {
        if ($stockAdjustment->status !== 'draft') {
            return redirect()->route('admin.stock-adjustments.show', $stockAdjustment)
                           ->with('error', 'Only draft stock adjustments can be edited!');
        }

        $products = Product::active()->orderBy('name')->get();
        $stockAdjustment->load(['items.product']);
        
        return view('admin.stock-adjustments.edit', compact('stockAdjustment', 'products'));
    }

    public function update(Request $request, StockAdjustment $stockAdjustment)
    {
        if ($stockAdjustment->status !== 'draft') {
            return redirect()->route('admin.stock-adjustments.show', $stockAdjustment)
                           ->with('error', 'Only draft stock adjustments can be updated!');
        }

        $request->validate([
            'adjustment_date' => 'required|date',
            'type' => 'required|in:increase,decrease,recount',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.current_stock' => 'required|integer|min:0',
            'items.*.adjusted_quantity' => 'required|integer',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
            'items.*.reason' => 'nullable|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            // Update stock adjustment
            $stockAdjustment->update([
                'adjustment_date' => $request->adjustment_date,
                'type' => $request->type,
                'reason' => $request->reason,
                'notes' => $request->notes
            ]);

            // Delete existing items and create new ones
            $stockAdjustment->items()->delete();
            foreach ($request->items as $item) {
                $newStock = $item['current_stock'] + $item['adjusted_quantity'];
                
                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $stockAdjustment->id,
                    'product_id' => $item['product_id'],
                    'current_stock' => $item['current_stock'],
                    'adjusted_quantity' => $item['adjusted_quantity'],
                    'new_stock' => $newStock,
                    'unit_cost' => $item['unit_cost'] ?? 0,
                    'total_cost_impact' => abs($item['adjusted_quantity']) * ($item['unit_cost'] ?? 0),
                    'reason' => $item['reason'] ?? null
                ]);
            }

            DB::commit();

            return redirect()->route('admin.stock-adjustments.show', $stockAdjustment)
                           ->with('success', 'Stock adjustment updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error updating stock adjustment: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function approve(StockAdjustment $stockAdjustment)
    {
        if ($stockAdjustment->status !== 'draft') {
            return redirect()->route('admin.stock-adjustments.show', $stockAdjustment)
                           ->with('error', 'Only draft adjustments can be approved!');
        }

        try {
            DB::beginTransaction();

            $stockAdjustment->approve(Auth::id());

            DB::commit();

            return redirect()->route('admin.stock-adjustments.show', $stockAdjustment)
                           ->with('success', 'Stock adjustment approved and stock updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error approving stock adjustment: ' . $e->getMessage());
        }
    }

    public function cancel(StockAdjustment $stockAdjustment)
    {
        if (!in_array($stockAdjustment->status, ['draft'])) {
            return redirect()->route('admin.stock-adjustments.show', $stockAdjustment)
                           ->with('error', 'Only draft adjustments can be cancelled!');
        }

        $stockAdjustment->update(['status' => 'cancelled']);

        return redirect()->route('admin.stock-adjustments.index')
                        ->with('success', 'Stock adjustment cancelled successfully!');
    }

    public function destroy(StockAdjustment $stockAdjustment)
    {
        if ($stockAdjustment->status !== 'draft') {
            return redirect()->route('admin.stock-adjustments.index')
                           ->with('error', 'Only draft stock adjustments can be deleted!');
        }

        $stockAdjustment->delete();

        return redirect()->route('admin.stock-adjustments.index')
                        ->with('success', 'Stock adjustment deleted successfully!');
    }

    public function getProductStock(Product $product)
    {
        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'current_stock' => $product->stock
        ]);
    }
}
