<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;
use App\Models\Order;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $banners = Banner::active()
                        ->current()
                        ->byPosition('top')
                        ->orderBy('sort_order')
                        ->get();
        
        $featuredProducts = Product::active()
                                  ->featured()
                                  ->inStock()
                                  ->with('category')
                                  ->limit(8)
                                  ->get();
        
        $categories = Category::active()
                             ->parent()
                             ->orderBy('sort_order')
                             ->limit(6)
                             ->get();

        // Handle menu filter
        $activeMenu = $request->get('menu', 'featured');
        $products = collect();
        
        // Get pagination settings from admin settings
        $enablePagination = AppSetting::get('frontend_pagination_enabled', 'true');
        $recordsPerPage = AppSetting::get('frontend_records_per_page', 12);
        
        $enablePagination = ($enablePagination === 'true');
        $recordsPerPage = (int) $recordsPerPage;
        
        if ($activeMenu === 'all') {
            // All Products
            $query = Product::active()
                           ->inStock()
                           ->with('category')
                           ->orderBy('sort_order');
            
            $products = $enablePagination 
                      ? $query->paginate($recordsPerPage)
                      : $query->get();
        } elseif ($activeMenu === 'offers') {
            // Offer Products (products with discount_price)
            $query = Product::active()
                           ->inStock()
                           ->whereNotNull('discount_price')
                           ->where('discount_price', '>', 0)
                           ->with('category')
                           ->orderBy('sort_order');
                           
            $products = $enablePagination 
                      ? $query->paginate($recordsPerPage)
                      : $query->get();
        }

        return view('home', compact('banners', 'featuredProducts', 'categories', 'products', 'activeMenu', 'enablePagination'));
    }

    public function products(Request $request)
    {
        // Get pagination settings from admin settings
        $enablePagination = AppSetting::get('frontend_pagination_enabled', 'true');
        $recordsPerPage = AppSetting::get('frontend_records_per_page', 12);
        
        $enablePagination = ($enablePagination === 'true');
        $recordsPerPage = (int) $recordsPerPage;
        
        $query = Product::active()->inStock()->with('category');
        
        // Filter by category if specified
        if ($request->has('category') && $request->category != 'all') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        $products = $enablePagination 
                  ? $query->orderBy('sort_order')->paginate($recordsPerPage)
                  : $query->orderBy('sort_order')->get();
        
        // Get categories that have products
        $categories = Category::active()
                             ->parent()
                             ->whereHas('products', function($q) {
                                 $q->active()->where('stock', '>', 0);
                             })
                             ->orderBy('sort_order')
                             ->get();

        if ($request->ajax()) {
            $paginationHtml = '';
            if ($enablePagination && method_exists($products, 'appends')) {
                $paginationHtml = $products->appends($request->query())->links()->render();
            }
            
            return response()->json([
                'html' => view('partials.products-grid-enhanced', compact('products'))->render(),
                'total' => $enablePagination ? $products->total() : $products->count(),
                'pagination' => $paginationHtml
            ]);
        }

        return view('products', compact('products', 'categories', 'enablePagination'));
    }

    public function offerProducts(Request $request)
    {
        // Get pagination settings from admin settings
        $enablePagination = AppSetting::get('frontend_pagination_enabled', 'true');
        $recordsPerPage = AppSetting::get('frontend_records_per_page', 12);
        
        $enablePagination = ($enablePagination === 'true');
        $recordsPerPage = (int) $recordsPerPage;
        
        $query = Product::active()
                        ->inStock()
                        ->whereNotNull('discount_price')
                        ->where('discount_price', '>', 0)
                        ->with('category');
        
        // Filter by category if specified
        if ($request->has('category') && $request->category != 'all') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        $products = $enablePagination 
                  ? $query->orderBy('sort_order')->paginate($recordsPerPage)
                  : $query->orderBy('sort_order')->get();
        
        // Get categories that have offer products
        $categories = Category::active()
                             ->parent()
                             ->whereHas('products', function($q) {
                                 $q->active()
                                   ->where('stock', '>', 0)
                                   ->whereNotNull('discount_price')
                                   ->where('discount_price', '>', 0);
                             })
                             ->orderBy('sort_order')
                             ->get();

        if ($request->ajax()) {
            $paginationHtml = '';
            if ($enablePagination && method_exists($products, 'appends')) {
                $paginationHtml = $products->appends($request->query())->links()->render();
            }
            
            return response()->json([
                'html' => view('partials.offer-products-grid', compact('products'))->render(),
                'total' => $enablePagination ? $products->total() : $products->count(),
                'pagination' => $paginationHtml
            ]);
        }

        return view('offer-products', compact('products', 'categories', 'enablePagination'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)
                          ->active()
                          ->firstOrFail();
        
        // Get pagination settings from admin settings
        $enablePagination = AppSetting::get('frontend_pagination_enabled', 'true');
        $recordsPerPage = AppSetting::get('frontend_records_per_page', 12);
        
        $enablePagination = ($enablePagination === 'true');
        $recordsPerPage = (int) $recordsPerPage;
        
        $query = Product::where('category_id', $category->id)
                        ->active()
                        ->inStock()
                        ->with('category')
                        ->orderBy('sort_order');
                        
        $products = $enablePagination 
                  ? $query->paginate($recordsPerPage)
                  : $query->get();

        return view('category', compact('category', 'products', 'enablePagination'));
    }

    public function product($slug)
    {
        $product = Product::where('slug', $slug)
                         ->active()
                         ->with('category')
                         ->firstOrFail();
        
        $relatedProducts = Product::where('category_id', $product->category_id)
                                ->where('id', '!=', $product->id)
                                ->active()
                                ->inStock()
                                ->limit(4)
                                ->get();

        return view('product', compact('product', 'relatedProducts'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->route('home');
        }

        // Get pagination settings from admin settings
        $enablePagination = AppSetting::get('frontend_pagination_enabled', 'true');
        $recordsPerPage = AppSetting::get('frontend_records_per_page', 12);
        
        $enablePagination = ($enablePagination === 'true');
        $recordsPerPage = (int) $recordsPerPage;
        
        $productQuery = Product::where('name', 'LIKE', "%{$query}%")
                              ->orWhere('description', 'LIKE', "%{$query}%")
                              ->active()
                              ->inStock()
                              ->with('category')
                              ->orderBy('sort_order');
                              
        $products = $enablePagination 
                  ? $productQuery->paginate($recordsPerPage)
                  : $productQuery->get();

        return view('search', compact('products', 'query', 'enablePagination'));
    }

    public function trackOrder(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'mobile_number' => 'required|string|size:10',
                'order_number' => 'nullable|string',
            ]);

            $query = Order::where('customer_mobile', $request->mobile_number);
            
            if ($request->order_number) {
                $query->where('order_number', $request->order_number);
            }
            
            $orders = $query->with('items.product')->latest()->get();
            
            return view('track-order', compact('orders'));
        }

        return view('track-order');
    }
}
