<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with('orders');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('mobile_number', 'LIKE', "%{$request->search}%")
                  ->orWhere('email', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->city) {
            $query->where('city', 'LIKE', "%{$request->city}%");
        }

        $customers = $query->latest()->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $customer->load(['orders' => function($query) {
            $query->latest();
        }]);
        
        return view('admin.customers.show', compact('customer'));
    }

    public function export(Request $request)
    {
        $query = Customer::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('mobile_number', 'LIKE', "%{$request->search}%")
                  ->orWhere('email', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->city) {
            $query->where('city', 'LIKE', "%{$request->city}%");
        }

        $customers = $query->get();

        $filename = 'customers_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Name', 'Mobile', 'Email', 'City', 'Total Orders', 'Total Spent', 'Last Order'
            ]);

            // Data rows
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->name,
                    $customer->mobile_number,
                    $customer->email,
                    $customer->city,
                    $customer->total_orders,
                    $customer->total_spent,
                    $customer->last_order_at ? $customer->last_order_at->format('Y-m-d H:i:s') : 'Never'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
