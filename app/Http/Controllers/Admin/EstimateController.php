<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EstimateController extends Controller
{
    public function index(Request $request)
    {
        $query = Estimate::with('creator');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('estimate_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->date_from) {
            $query->whereDate('estimate_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('estimate_date', '<=', $request->date_to);
        }

        $estimates = $query->latest()->paginate(20);

        return view('admin.estimates.index', compact('estimates'));
    }

    public function create()
    {
        $products = Product::active()->orderBy('name')->get();
        return view('admin.estimates.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'estimate_date' => 'required|date',
            'valid_until' => 'required|date|after:estimate_date',
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

            // Create estimate
            $estimate = Estimate::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'estimate_date' => $request->estimate_date,
                'valid_until' => $request->valid_until,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount' => $discount,
                'total_amount' => $total,
                'notes' => $request->notes,
                'terms_conditions' => $request->terms_conditions,
                'created_by' => Auth::id(),
                'status' => 'draft'
            ]);

            // Create estimate items
            foreach ($request->items as $item) {
                EstimateItem::create([
                    'estimate_id' => $estimate->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'description' => $item['description'] ?? null
                ]);
            }

            DB::commit();

            return redirect()->route('admin.estimates.show', $estimate)
                           ->with('success', 'Estimate created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error creating estimate: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function show(Estimate $estimate)
    {
        $estimate->load(['items.product', 'creator']);
        return view('admin.estimates.show', compact('estimate'));
    }

    public function edit(Estimate $estimate)
    {
        if ($estimate->status !== 'draft') {
            return redirect()->route('admin.estimates.show', $estimate)
                           ->with('error', 'Only draft estimates can be edited!');
        }

        $products = Product::active()->orderBy('name')->get();
        $estimate->load(['items.product']);
        
        return view('admin.estimates.edit', compact('estimate', 'products'));
    }

    public function update(Request $request, Estimate $estimate)
    {
        if ($estimate->status !== 'draft') {
            return redirect()->route('admin.estimates.show', $estimate)
                           ->with('error', 'Only draft estimates can be updated!');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'estimate_date' => 'required|date',
            'valid_until' => 'required|date|after:estimate_date',
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

            // Update estimate
            $estimate->update([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'estimate_date' => $request->estimate_date,
                'valid_until' => $request->valid_until,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount' => $discount,
                'total_amount' => $total,
                'notes' => $request->notes,
                'terms_conditions' => $request->terms_conditions
            ]);

            // Delete existing items and create new ones
            $estimate->items()->delete();
            foreach ($request->items as $item) {
                EstimateItem::create([
                    'estimate_id' => $estimate->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'description' => $item['description'] ?? null
                ]);
            }

            DB::commit();

            return redirect()->route('admin.estimates.show', $estimate)
                           ->with('success', 'Estimate updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error updating estimate: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function updateStatus(Request $request, Estimate $estimate)
    {
        $request->validate([
            'status' => 'required|in:draft,sent,accepted,rejected,expired'
        ]);

        $estimate->update(['status' => $request->status]);

        if ($request->status === 'sent') {
            $estimate->update(['sent_at' => now()]);
        }

        if ($request->status === 'accepted') {
            $estimate->update(['accepted_at' => now()]);
        }

        return redirect()->back()
                        ->with('success', 'Estimate status updated successfully!');
    }

    public function destroy(Estimate $estimate)
    {
        if ($estimate->status !== 'draft') {
            return redirect()->route('admin.estimates.index')
                           ->with('error', 'Only draft estimates can be deleted!');
        }

        $estimate->delete();

        return redirect()->route('admin.estimates.index')
                        ->with('success', 'Estimate deleted successfully!');
    }

    public function duplicate(Estimate $estimate)
    {
        try {
            DB::beginTransaction();

            $newEstimate = $estimate->replicate();
            $newEstimate->estimate_number = null; // Will be auto-generated
            $newEstimate->status = 'draft';
            $newEstimate->sent_at = null;
            $newEstimate->accepted_at = null;
            $newEstimate->created_by = Auth::id();
            $newEstimate->save();

            // Duplicate items
            foreach ($estimate->items as $item) {
                $newItem = $item->replicate();
                $newItem->estimate_id = $newEstimate->id;
                $newItem->save();
            }

            DB::commit();

            return redirect()->route('admin.estimates.edit', $newEstimate)
                           ->with('success', 'Estimate duplicated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->with('error', 'Error duplicating estimate: ' . $e->getMessage());
        }
    }
}
