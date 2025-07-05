<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with('supplier');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->date_from) {
            $query->whereDate('po_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('po_date', '<=', $request->date_to);
        }

        $purchaseOrders = $query->latest()->paginate(20);
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('admin.purchase-orders.index', compact('purchaseOrders', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        
        return view('admin.purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after:po_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $taxAmount = $request->tax_amount ?? 0;
            $discount = $request->discount ?? 0;
            $total = $subtotal + $taxAmount - $discount;

            // Create purchase order
            $po = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'po_date' => $request->po_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount' => $discount,
                'total_amount' => $total,
                'notes' => $request->notes,
                'terms_conditions' => $request->terms_conditions,
                'created_by' => Auth::id(),
                'status' => 'draft'
            ]);

            // Create purchase order items
            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'description' => $item['description'] ?? null
                ]);
            }

            DB::commit();

            return redirect()->route('admin.purchase-orders.show', $po)
                           ->with('success', 'Purchase order created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error creating purchase order: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product', 'creator', 'approver']);
        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('admin.purchase-orders.show', $purchaseOrder)
                           ->with('error', 'Only draft purchase orders can be edited!');
        }

        $suppliers = Supplier::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        $purchaseOrder->load(['items.product']);
        
        return view('admin.purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('admin.purchase-orders.show', $purchaseOrder)
                           ->with('error', 'Only draft purchase orders can be updated!');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'po_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after:po_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'terms_conditions' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $taxAmount = $request->tax_amount ?? 0;
            $discount = $request->discount ?? 0;
            $total = $subtotal + $taxAmount - $discount;

            // Update purchase order
            $purchaseOrder->update([
                'supplier_id' => $request->supplier_id,
                'po_date' => $request->po_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount' => $discount,
                'total_amount' => $total,
                'notes' => $request->notes,
                'terms_conditions' => $request->terms_conditions
            ]);

            // Delete existing items and create new ones
            $purchaseOrder->items()->delete();
            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'description' => $item['description'] ?? null
                ]);
            }

            DB::commit();

            return redirect()->route('admin.purchase-orders.show', $purchaseOrder)
                           ->with('success', 'Purchase order updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error updating purchase order: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'status' => 'required|in:draft,sent,approved,received,cancelled'
        ]);

        $purchaseOrder->update(['status' => $request->status]);

        if ($request->status === 'approved') {
            $purchaseOrder->update([
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);
        }

        return redirect()->back()
                        ->with('success', 'Purchase order status updated successfully!');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('admin.purchase-orders.index')
                           ->with('error', 'Only draft purchase orders can be deleted!');
        }

        $purchaseOrder->delete();

        return redirect()->route('admin.purchase-orders.index')
                        ->with('success', 'Purchase order deleted successfully!');
    }
}
