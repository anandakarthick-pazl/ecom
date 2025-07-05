<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index()
    {
        // Get pagination settings from admin settings
        $enablePagination = 
            \App\Models\AppSetting::where('key', 'admin_pagination_enabled')
                ->value('value') ?? 'true';
        $recordsPerPage = 
            \App\Models\AppSetting::where('key', 'admin_records_per_page')
                ->value('value') ?? 20;
        
        $enablePagination = ($enablePagination === 'true');
        $recordsPerPage = (int) $recordsPerPage;
        
        $offers = $enablePagination 
                ? Offer::with(['category', 'product'])->latest()->paginate($recordsPerPage)
                : Offer::with(['category', 'product'])->latest()->get();
        
        return view('admin.offers.index', compact('offers', 'enablePagination'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        
        return view('admin.offers.create', compact('categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:offers,code|max:50',
            'type' => 'required|in:percentage,fixed,category,product',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'category_id' => 'required_if:type,category|exists:categories,id',
            'product_id' => 'required_if:type,product|exists:products,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'nullable',
            'usage_limit' => 'nullable|integer|min:1'
        ]);

        $data = $request->all();
        
        // Handle checkbox value
        $data['is_active'] = $request->input('is_active', 0) == '1';

        if ($request->type === 'percentage' && $request->value > 100) {
            return back()->withErrors(['value' => 'Percentage discount cannot be more than 100%']);
        }

        Offer::create($data);

        return redirect()->route('admin.offers.index')
                        ->with('success', 'Offer created successfully!');
    }

    public function show(Offer $offer)
    {
        $offer->load('category', 'product');
        return view('admin.offers.show', compact('offer'));
    }

    public function edit(Offer $offer)
    {
        $categories = Category::active()->orderBy('name')->get();
        $products = Product::active()->orderBy('name')->get();
        
        return view('admin.offers.edit', compact('offer', 'categories', 'products'));
    }

    public function update(Request $request, Offer $offer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:offers,code,' . $offer->id,
            'type' => 'required|in:percentage,fixed,category,product',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'category_id' => 'required_if:type,category|exists:categories,id',
            'product_id' => 'required_if:type,product|exists:products,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'nullable',
            'usage_limit' => 'nullable|integer|min:1'
        ]);

        $data = $request->all();
        
        // Handle checkbox value
        $data['is_active'] = $request->input('is_active', 0) == '1';

        if ($request->type === 'percentage' && $request->value > 100) {
            return back()->withErrors(['value' => 'Percentage discount cannot be more than 100%']);
        }

        $offer->update($data);

        return redirect()->route('admin.offers.index')
                        ->with('success', 'Offer updated successfully!');
    }

    public function destroy(Offer $offer)
    {
        $offer->delete();

        return redirect()->route('admin.offers.index')
                        ->with('success', 'Offer deleted successfully!');
    }

    public function toggleStatus(Offer $offer)
    {
        $offer->update(['is_active' => !$offer->is_active]);
        
        $status = $offer->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Offer {$status} successfully!");
    }
}
