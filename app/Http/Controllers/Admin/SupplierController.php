<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('company_name', 'LIKE', "%{$request->search}%")
                  ->orWhere('phone', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->status !== null) {
            $query->where('is_active', $request->status);
        }

        $suppliers = $query->latest()->paginate(20);

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'pincode' => 'required|string|size:6',
            'gst_number' => 'nullable|string|max:15',
            'pan_number' => 'nullable|string|max:10',
            'credit_limit' => 'nullable|numeric|min:0',
            'credit_days' => 'nullable|integer|min:0|max:365',
            'opening_balance' => 'nullable|numeric',
            'is_active' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        Supplier::create($data);

        return redirect()->route('admin.suppliers.index')
                        ->with('success', 'Supplier created successfully!');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['purchaseOrders' => function($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('admin.suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'pincode' => 'required|string|size:6',
            'gst_number' => 'nullable|string|max:15',
            'pan_number' => 'nullable|string|max:10',
            'credit_limit' => 'nullable|numeric|min:0',
            'credit_days' => 'nullable|integer|min:0|max:365',
            'opening_balance' => 'nullable|numeric',
            'is_active' => 'boolean',
            'notes' => 'nullable|string'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $supplier->update($data);

        return redirect()->route('admin.suppliers.index')
                        ->with('success', 'Supplier updated successfully!');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchaseOrders()->count() > 0) {
            return redirect()->route('admin.suppliers.index')
                           ->with('error', 'Cannot delete supplier with purchase orders!');
        }

        $supplier->delete();

        return redirect()->route('admin.suppliers.index')
                        ->with('success', 'Supplier deleted successfully!');
    }

    public function toggleStatus(Supplier $supplier)
    {
        $supplier->update(['is_active' => !$supplier->is_active]);
        
        $status = $supplier->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Supplier {$status} successfully!");
    }
}
