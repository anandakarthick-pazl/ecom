<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdmin\Billing;
use App\Models\SuperAdmin\Company;
use App\Models\SuperAdmin\Package;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index()
    {
        $billings = Billing::with(['company', 'package'])
                          ->latest()
                          ->paginate(15);

        return view('super-admin.billing.index', compact('billings'));
    }

    public function create()
    {
        $companies = Company::active()->get();
        $packages = Package::active()->get();

        return view('super-admin.billing.create', compact('companies', 'packages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'package_id' => 'required|exists:packages,id',
            'amount' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,yearly,lifetime',
            'billing_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:billing_date',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad(Billing::count() + 1, 5, '0', STR_PAD_LEFT);

        Billing::create([
            'company_id' => $request->company_id,
            'package_id' => $request->package_id,
            'amount' => $request->amount,
            'billing_cycle' => $request->billing_cycle,
            'billing_date' => $request->billing_date,
            'due_date' => $request->due_date,
            'payment_method' => $request->payment_method,
            'invoice_number' => $invoiceNumber,
            'status' => 'pending',
            'notes' => $request->notes
        ]);

        return redirect()->route('super-admin.billing.index')
                        ->with('success', 'Billing record created successfully!');
    }

    public function show(Billing $billing)
    {
        $billing->load(['company', 'package']);
        return view('super-admin.billing.show', compact('billing'));
    }

    public function edit(Billing $billing)
    {
        $companies = Company::active()->get();
        $packages = Package::active()->get();

        return view('super-admin.billing.edit', compact('billing', 'companies', 'packages'));
    }

    public function update(Request $request, Billing $billing)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'billing_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:billing_date',
            'payment_method' => 'nullable|string',
            'status' => 'required|in:pending,paid,overdue,cancelled,refunded',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $data = $request->only([
            'amount', 'billing_date', 'due_date', 'payment_method', 
            'status', 'transaction_id', 'notes'
        ]);

        if ($request->status === 'paid' && $billing->status !== 'paid') {
            $data['paid_at'] = now();
        }

        $billing->update($data);

        return redirect()->route('super-admin.billing.show', $billing)
                        ->with('success', 'Billing record updated successfully!');
    }

    public function destroy(Billing $billing)
    {
        if ($billing->status === 'paid') {
            return redirect()->route('super-admin.billing.index')
                            ->with('error', 'Cannot delete paid billing record.');
        }

        $billing->delete();

        return redirect()->route('super-admin.billing.index')
                        ->with('success', 'Billing record deleted successfully!');
    }

    public function markAsPaid(Billing $billing)
    {
        $billing->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        return response()->json(['success' => true]);
    }

    public function generateInvoice(Billing $billing)
    {
        $billing->load(['company', 'package']);
        
        return view('super-admin.billing.invoice', compact('billing'));
    }

    public function reports()
    {
        $totalRevenue = Billing::where('status', 'paid')->sum('amount');
        $monthlyRevenue = Billing::where('status', 'paid')
                                ->whereMonth('paid_at', now()->month)
                                ->sum('amount');
        $pendingAmount = Billing::where('status', 'pending')->sum('amount');
        $overdueAmount = Billing::where('status', 'overdue')->sum('amount');

        $monthlyData = Billing::selectRaw('MONTH(paid_at) as month, SUM(amount) as total')
                             ->where('status', 'paid')
                             ->whereYear('paid_at', now()->year)
                             ->groupBy('month')
                             ->orderBy('month')
                             ->get();

        return view('super-admin.billing.reports', compact(
            'totalRevenue', 'monthlyRevenue', 'pendingAmount', 'overdueAmount', 'monthlyData'
        ));
    }
}
