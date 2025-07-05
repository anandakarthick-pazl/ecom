<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceiptNote;
use App\Models\GrnItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class GrnController extends Controller
{
    public function index(Request $request)
    {
        $query = GoodsReceiptNote::with(['purchaseOrder', 'supplier', 'receiver']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('grn_number', 'like', '%' . $request->search . '%')
                  ->orWhere('invoice_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('purchaseOrder', function($subQ) use ($request) {
                      $subQ->where('po_number', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->date_from) {
            $query->whereDate('received_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('received_date', '<=', $request->date_to);
        }

        $grns = $query->latest()->paginate(20);
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('admin.grns.index', compact('grns', 'suppliers'));
    }

    public function create(Request $request)
    {
        $purchaseOrders = PurchaseOrder::where('status', 'approved')
                                     ->with('supplier')
                                     ->orderBy('po_date', 'desc')
                                     ->get();
        
        $selectedPo = null;
        if ($request->po_id) {
            $selectedPo = PurchaseOrder::with(['items.product', 'supplier'])
                                     ->findOrFail($request->po_id);
        }

        return view('admin.grns.create', compact('purchaseOrders', 'selectedPo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'received_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:255',
            'invoice_date' => 'nullable|date',
            'invoice_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.ordered_quantity' => 'required|integer|min:1',
            'items.*.received_quantity' => 'required|integer|min:0',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrder::findOrFail($request->purchase_order_id);

            // Create GRN
            $grn = GoodsReceiptNote::create([
                'purchase_order_id' => $request->purchase_order_id,
                'supplier_id' => $purchaseOrder->supplier_id,
                'received_date' => $request->received_date,
                'invoice_number' => $request->invoice_number,
                'invoice_date' => $request->invoice_date,
                'invoice_amount' => $request->invoice_amount,
                'notes' => $request->notes,
                'received_by' => Auth::id(),
                'status' => 'pending'
            ]);

            $totalReceived = 0;
            $totalOrdered = 0;

            // Create GRN items and update stock
            foreach ($request->items as $item) {
                GrnItem::create([
                    'grn_id' => $grn->id,
                    'product_id' => $item['product_id'],
                    'ordered_quantity' => $item['ordered_quantity'],
                    'received_quantity' => $item['received_quantity'],
                    'unit_cost' => $item['unit_cost'] ?? 0,
                    'total_cost' => ($item['unit_cost'] ?? 0) * $item['received_quantity']
                ]);

                // Update product stock
                if ($item['received_quantity'] > 0) {
                    $product = Product::findOrFail($item['product_id']);
                    $product->increment('stock', $item['received_quantity']);
                }

                $totalReceived += $item['received_quantity'];
                $totalOrdered += $item['ordered_quantity'];
            }

            // Update GRN status based on quantities
            if ($totalReceived == 0) {
                $grn->update(['status' => 'pending']);
            } elseif ($totalReceived >= $totalOrdered) {
                $grn->update(['status' => 'completed']);
                // Update purchase order status
                $purchaseOrder->update(['status' => 'received']);
            } else {
                $grn->update(['status' => 'partial']);
            }

            DB::commit();

            return redirect()->route('admin.grns.show', $grn)
                           ->with('success', 'GRN created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error creating GRN: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function show(GoodsReceiptNote $grn)
    {
        $grn->load(['purchaseOrder', 'supplier', 'items.product', 'receiver']);
        return view('admin.grns.show', compact('grn'));
    }

    public function edit(GoodsReceiptNote $grn)
    {
        if ($grn->status === 'completed') {
            return redirect()->route('admin.grns.show', $grn)
                           ->with('error', 'Completed GRNs cannot be edited!');
        }

        $grn->load(['purchaseOrder.items.product', 'supplier', 'items.product']);
        
        return view('admin.grns.edit', compact('grn'));
    }

    public function update(Request $request, GoodsReceiptNote $grn)
    {
        if ($grn->status === 'completed') {
            return redirect()->route('admin.grns.show', $grn)
                           ->with('error', 'Completed GRNs cannot be updated!');
        }

        $request->validate([
            'received_date' => 'required|date',
            'invoice_number' => 'nullable|string|max:255',
            'invoice_date' => 'nullable|date',
            'invoice_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.ordered_quantity' => 'required|integer|min:1',
            'items.*.received_quantity' => 'required|integer|min:0',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Revert previous stock changes
            foreach ($grn->items as $oldItem) {
                if ($oldItem->received_quantity > 0) {
                    $product = Product::findOrFail($oldItem->product_id);
                    $product->decrement('stock', $oldItem->received_quantity);
                }
            }

            // Update GRN
            $grn->update([
                'received_date' => $request->received_date,
                'invoice_number' => $request->invoice_number,
                'invoice_date' => $request->invoice_date,
                'invoice_amount' => $request->invoice_amount,
                'notes' => $request->notes
            ]);

            // Delete existing items and create new ones
            $grn->items()->delete();

            $totalReceived = 0;
            $totalOrdered = 0;

            foreach ($request->items as $item) {
                GrnItem::create([
                    'grn_id' => $grn->id,
                    'product_id' => $item['product_id'],
                    'ordered_quantity' => $item['ordered_quantity'],
                    'received_quantity' => $item['received_quantity'],
                    'unit_cost' => $item['unit_cost'] ?? 0,
                    'total_cost' => ($item['unit_cost'] ?? 0) * $item['received_quantity']
                ]);

                // Update product stock with new quantities
                if ($item['received_quantity'] > 0) {
                    $product = Product::findOrFail($item['product_id']);
                    $product->increment('stock', $item['received_quantity']);
                }

                $totalReceived += $item['received_quantity'];
                $totalOrdered += $item['ordered_quantity'];
            }

            // Update GRN status
            if ($totalReceived == 0) {
                $grn->update(['status' => 'pending']);
            } elseif ($totalReceived >= $totalOrdered) {
                $grn->update(['status' => 'completed']);
                $grn->purchaseOrder->update(['status' => 'received']);
            } else {
                $grn->update(['status' => 'partial']);
            }

            DB::commit();

            return redirect()->route('admin.grns.show', $grn)
                           ->with('success', 'GRN updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error updating GRN: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function destroy(GoodsReceiptNote $grn)
    {
        if ($grn->status === 'completed') {
            return redirect()->route('admin.grns.index')
                           ->with('error', 'Completed GRNs cannot be deleted!');
        }

        try {
            DB::beginTransaction();

            // Revert stock changes
            foreach ($grn->items as $item) {
                if ($item->received_quantity > 0) {
                    $product = Product::findOrFail($item->product_id);
                    $product->decrement('stock', $item->received_quantity);
                }
            }

            $grn->delete();

            DB::commit();

            return redirect()->route('admin.grns.index')
                           ->with('success', 'GRN deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error deleting GRN: ' . $e->getMessage());
        }
    }

    public function getPurchaseOrderItems(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['items.product']);
        return response()->json($purchaseOrder);
    }
}
