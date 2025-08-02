<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'type' => 'required|in:percentage,fixed,category,product,flash',
            'discount_type' => 'required_if:type,category,product,flash|in:percentage,flat',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'category_id' => 'required_if:type,category|nullable|exists:categories,id',
            'product_id' => 'required_if:type,product|nullable|exists:products,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'nullable',
            'usage_limit' => 'nullable|integer|min:1',
            // Flash offer fields
            'is_flash_offer' => 'nullable',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_title' => 'nullable|string|max:255',
            'banner_description' => 'nullable|string',
            'banner_button_text' => 'nullable|string|max:100',
            'banner_button_url' => 'nullable|url',
            'show_popup' => 'nullable',
            'popup_delay' => 'nullable|integer|min:0|max:60',
            'show_countdown' => 'nullable',
            'countdown_text' => 'nullable|string|max:255'
        ], [
            'category_id.required_if' => 'Please select a category for category-specific offers.',
            'product_id.required_if' => 'Please select a product for product-specific offers.',
            'discount_type.required_if' => 'Please select discount type (percentage or flat amount).',
            'banner_image.image' => 'Banner image must be a valid image file.',
            'banner_image.max' => 'Banner image must not be larger than 2MB.',
        ]);

        $data = $request->all();
        
        // Handle checkbox values
        $data['is_active'] = $request->input('is_active', 0) == '1';
        $data['is_flash_offer'] = $request->input('is_flash_offer', 0) == '1';
        $data['show_popup'] = $request->input('show_popup', 0) == '1';
        $data['show_countdown'] = $request->input('show_countdown', 0) == '1';

        // Set default discount_type for percentage and fixed types
        if ($request->type === 'percentage') {
            $data['discount_type'] = 'percentage';
        } elseif ($request->type === 'fixed') {
            $data['discount_type'] = 'flat';
        } elseif ($request->type === 'flash') {
            // For flash offers, auto-enable flash offer
            $data['is_flash_offer'] = true;
            if (!$request->has('discount_type')) {
                $data['discount_type'] = 'percentage';
            }
        }
        
        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            $image = $request->file('banner_image');
            $filename = time() . '_flash_offer_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('offers/banners', $filename, 'public');
            $data['banner_image'] = $path;
        }
        
        // Set default values for flash offer fields
        if ($data['is_flash_offer']) {
            $data['popup_delay'] = $request->input('popup_delay', 3) * 1000; // Convert to milliseconds
            $data['banner_button_text'] = $request->input('banner_button_text', 'Shop Now');
            $data['countdown_text'] = $request->input('countdown_text', 'Hurry! Limited time offer');
        }

        // Validate percentage values
        if (($request->type === 'percentage' || $request->discount_type === 'percentage') && $request->value > 100) {
            return back()->withErrors(['value' => 'Percentage discount cannot be more than 100%'])->withInput();
        }

        // Clear unused IDs based on type
        if ($request->type !== 'category') {
            $data['category_id'] = null;
        }
        if ($request->type !== 'product') {
            $data['product_id'] = null;
        }

        try {
            Offer::create($data);
            return redirect()->route('admin.offers.index')
                            ->with('success', 'Offer created successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create offer: ' . $e->getMessage()])->withInput();
        }
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
            'type' => 'required|in:percentage,fixed,category,product,flash',
            'discount_type' => 'required_if:type,category,product,flash|in:percentage,flat',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'category_id' => 'required_if:type,category|nullable|exists:categories,id',
            'product_id' => 'required_if:type,product|nullable|exists:products,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'nullable',
            'usage_limit' => 'nullable|integer|min:1',
            // Flash offer fields
            'is_flash_offer' => 'nullable',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner_title' => 'nullable|string|max:255',
            'banner_description' => 'nullable|string',
            'banner_button_text' => 'nullable|string|max:100',
            'banner_button_url' => 'nullable|url',
            'show_popup' => 'nullable',
            'popup_delay' => 'nullable|integer|min:0|max:60',
            'show_countdown' => 'nullable',
            'countdown_text' => 'nullable|string|max:255'
        ], [
            'category_id.required_if' => 'Please select a category for category-specific offers.',
            'product_id.required_if' => 'Please select a product for product-specific offers.',
            'discount_type.required_if' => 'Please select discount type (percentage or flat amount).',
            'banner_image.image' => 'Banner image must be a valid image file.',
            'banner_image.max' => 'Banner image must not be larger than 2MB.',
        ]);

        $data = $request->all();
        
        // Handle checkbox values
        $data['is_active'] = $request->input('is_active', 0) == '1';
        $data['is_flash_offer'] = $request->input('is_flash_offer', 0) == '1';
        $data['show_popup'] = $request->input('show_popup', 0) == '1';
        $data['show_countdown'] = $request->input('show_countdown', 0) == '1';

        // Set default discount_type for percentage and fixed types
        if ($request->type === 'percentage') {
            $data['discount_type'] = 'percentage';
        } elseif ($request->type === 'fixed') {
            $data['discount_type'] = 'flat';
        } elseif ($request->type === 'flash') {
            // For flash offers, auto-enable flash offer
            $data['is_flash_offer'] = true;
            if (!$request->has('discount_type')) {
                $data['discount_type'] = 'percentage';
            }
        }
        
        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            // Delete old image if it exists
            if ($offer->banner_image && \Storage::disk('public')->exists($offer->banner_image)) {
                \Storage::disk('public')->delete($offer->banner_image);
            }
            
            $image = $request->file('banner_image');
            $filename = time() . '_flash_offer_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('offers/banners', $filename, 'public');
            $data['banner_image'] = $path;
        }
        
        // Set default values for flash offer fields
        if ($data['is_flash_offer']) {
            $data['popup_delay'] = $request->input('popup_delay', 3) * 1000; // Convert to milliseconds
            $data['banner_button_text'] = $request->input('banner_button_text', 'Shop Now');
            $data['countdown_text'] = $request->input('countdown_text', 'Hurry! Limited time offer');
        }

        // Validate percentage values
        if (($request->type === 'percentage' || $request->discount_type === 'percentage') && $request->value > 100) {
            return back()->withErrors(['value' => 'Percentage discount cannot be more than 100%'])->withInput();
        }

        // Clear unused IDs based on type
        if ($request->type !== 'category') {
            $data['category_id'] = null;
        }
        if ($request->type !== 'product') {
            $data['product_id'] = null;
        }

        try {
            $offer->update($data);
            return redirect()->route('admin.offers.index')
                            ->with('success', 'Offer updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update offer: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Offer $offer)
    {
        try {
            $offer->delete();
            return redirect()->route('admin.offers.index')
                            ->with('success', 'Offer deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.offers.index')
                            ->with('error', 'Failed to delete offer: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Offer $offer)
    {
        try {
            $offer->update(['is_active' => !$offer->is_active]);
            $status = $offer->is_active ? 'activated' : 'deactivated';
            return redirect()->back()->with('success', "Offer {$status} successfully!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update offer status: ' . $e->getMessage());
        }
    }
}
