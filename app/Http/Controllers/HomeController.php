<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;
use App\Models\Order;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use App\Traits\HasPagination;
use App\Services\OfferService;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    use HasPagination;
    
    protected $offerService;
    
    public function __construct(OfferService $offerService)
    {
        $this->offerService = $offerService;
    }
    
    public function index(Request $request)
    {
        $banners = Banner::active()
            ->current()
            ->byPosition('top')
            ->orderBy('sort_order')
            ->get();

        // Debug banner information if in development
        if (config('app.debug')) {
            \Log::info('Banner Debug Info', [
                'total_banners' => Banner::count(),
                'active_banners' => Banner::active()->count(),
                'current_banners' => Banner::active()->current()->count(),
                'top_position_banners' => Banner::active()->current()->byPosition('top')->count(),
                'fetched_banners' => $banners->count(),
                'banner_details' => $banners->map(function($banner) {
                    return [
                        'id' => $banner->id,
                        'title' => $banner->title,
                        'image' => $banner->image,
                        'image_url' => $banner->image_url,
                        'is_active' => $banner->is_active,
                        'position' => $banner->position,
                        'file_exists' => $banner->image ? file_exists(public_path('storage/public/banner/banners/' . basename($banner->image))) : false
                    ];
                })->toArray()
            ]);
        }

        // Featured products - include out of stock but prioritize in-stock
        $featuredProducts = Product::active()
            ->featured()
            ->with('category')
            ->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END') // In-stock first
            ->orderBy('sort_order')
            ->limit(50)
            ->get();

        // Fix category products count - use direct query instead of withCount
        $categories = Category::active()
            ->parent()
            ->orderBy('sort_order')
            ->limit(6)
            ->get()
            ->map(function ($category) {
                // Count active products for this category
                $category->products_count = Product::where('category_id', $category->id)
                    ->active()
                    ->count();
                return $category;
            });

        // Handle menu filter - default to 'all' (All Products first)
        $activeMenu = $request->get('menu', 'all');
        $products = collect();

        // Get frontend pagination settings using the trait
        $frontendPaginationSettings = $this->getFrontendPaginationSettings($request, '50');
        $frontendPaginationControls = $this->getPaginationControlsData($request, 'frontend');

        if ($activeMenu === 'all') {
            // All Products - include out of stock but prioritize in-stock
            $query = Product::active()
                ->with('category')
                ->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END') // In-stock first
                ->orderBy('sort_order');

            $products = $this->applyFrontendPagination($query, $request, '50');
        } elseif ($activeMenu === 'offers') {
            // Offer Products - include out of stock offers
            $query = Product::active()
                ->whereNotNull('discount_price')
                ->where('discount_price', '>', 0)
                ->with('category')
                ->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END') // In-stock first
                ->orderBy('sort_order');

            $products = $this->applyFrontendPagination($query, $request, '50');
        }

        return view('home-enhanced', compact(
            'banners',
            'featuredProducts',
            'categories',
            'products',
            'activeMenu',
            'frontendPaginationSettings',
            'frontendPaginationControls'
        ));
    }

    public function products(Request $request)
    {
        $query = Product::active()->with('category');

        // Filter by category if specified
        if ($request->has('category') && $request->category != 'all') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Order by stock status (in-stock first) then by sort order
        $query->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END')
              ->orderBy('sort_order');

        // Apply frontend pagination using the trait
        $products = $this->applyFrontendPagination($query, $request, '50');
        
        // Apply offers to products using the OfferService
        if (method_exists($products, 'getCollection')) {
            // For paginated results
            $productsCollection = $products->getCollection();
            $productsWithOffers = $this->offerService->applyOffersToProducts($productsCollection);
            $products->setCollection($productsWithOffers);
        } else {
            // For regular collection
            $products = $this->offerService->applyOffersToProducts($products);
        }

        // Get frontend pagination settings and controls
        $frontendPaginationSettings = $this->getFrontendPaginationSettings($request, '50');
        $frontendPaginationControls = $this->getPaginationControlsData($request, 'frontend');

        // Get categories that have products (including out of stock) - fix count here too
        $categories = Category::active()
            ->parent()
            ->orderBy('sort_order')
            ->get()
            ->filter(function ($category) {
                return Product::where('category_id', $category->id)->active()->count() > 0;
            });
        
        // Get category-specific offers if filtering by category
        $categoryOffers = collect();
        if ($request->has('category') && $request->category != 'all') {
            $category = Category::where('slug', $request->category)->active()->first();
            if ($category) {
                $categoryOffers = $this->offerService->getCategoryOffers($category);
            }
        }

        if ($request->ajax()) {
            $paginationHtml = '';
            if ($frontendPaginationSettings['enabled'] && method_exists($products, 'appends')) {
                $paginationHtml = $products->appends($request->query())->links()->render();
            }

            $productsHtml = '';
            $productsCount = method_exists($products, 'count') ? $products->count() : count($products);

            if ($productsCount > 0) {
                $productsHtml = '<div class="products-grid-compact">';
                foreach ($products as $product) {
                    $productsHtml .= view('partials.product-card-modern', compact('product'))->render();
                }
                $productsHtml .= '</div>';

                if ($frontendPaginationSettings['enabled'] && method_exists($products, 'appends')) {
                    $productsHtml .= '<div class="pagination-container" id="pagination-container">' . $paginationHtml . '</div>';
                }
            } else {
                $productsHtml = view('partials.empty-state', [
                    'icon' => 'box-open',
                    'title' => 'No Products Found',
                    'message' => 'Try adjusting your filters or check back later for new products.',
                    'action' => 'Browse All',
                    'actionUrl' => route('products')
                ])->render();
            }

            $totalCount = $frontendPaginationSettings['enabled'] && method_exists($products, 'total')
                ? $products->total()
                : $productsCount;

            return response()->json([
                'html' => $productsHtml,
                'total' => $totalCount,
                'pagination' => $paginationHtml
            ]);
        }

        return view('products', compact(
            'products',
            'categories',
            'categoryOffers',
            'frontendPaginationSettings',
            'frontendPaginationControls'
        ));
    }

    public function offerProducts(Request $request)
    {
        // Start with products that have manual discount_price OR products with category/product offers
        $query = Product::active()
            ->with('category')
            ->where(function($q) {
                // Products with manual discount_price
                $q->whereNotNull('discount_price')
                  ->where('discount_price', '>', 0);
                
                // OR products that have category-specific offers
                $q->orWhereHas('category', function($categoryQuery) {
                    $categoryQuery->whereHas('offers', function($offerQuery) {
                        $offerQuery->where('type', 'category')
                                  ->where('is_active', true)
                                  ->where('start_date', '<=', now())
                                  ->where('end_date', '>=', now());
                    });
                });
                
                // OR products that have product-specific offers
                $q->orWhereExists(function($productOfferQuery) {
                    $productOfferQuery->select(DB::raw(1))
                                     ->from('offers')
                                     ->where('type', 'product')
                                     ->whereColumn('offers.product_id', 'products.id')
                                     ->where('is_active', true)
                                     ->where('start_date', '<=', now())
                                     ->where('end_date', '>=', now());
                });
            });

        // Filter by category if specified
        if ($request->has('category') && $request->category != 'all') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Order by stock status (in-stock first) then by sort order
        $query->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END')
              ->orderBy('sort_order');

        // Apply frontend pagination using the trait
        $products = $this->applyFrontendPagination($query, $request, '50');
        
        // Apply offers to products using the OfferService
        if (method_exists($products, 'getCollection')) {
            // For paginated results
            $productsCollection = $products->getCollection();
            $productsWithOffers = $this->offerService->applyOffersToProducts($productsCollection);
            $products->setCollection($productsWithOffers);
        } else {
            // For regular collection
            $products = $this->offerService->applyOffersToProducts($products);
        }

        // Get frontend pagination settings and controls
        $frontendPaginationSettings = $this->getFrontendPaginationSettings($request, '50');
        $frontendPaginationControls = $this->getPaginationControlsData($request, 'frontend');

        // Get categories that have offer products - fix count here too
        $categories = Category::active()
            ->parent()
            ->orderBy('sort_order')
            ->get()
            ->filter(function($category) {
                // Check if category has products with manual discount_price
                $hasDiscountProducts = Product::where('category_id', $category->id)
                    ->active()
                    ->whereNotNull('discount_price')
                    ->where('discount_price', '>', 0)
                    ->count() > 0;
                
                // Check if category has category-specific offers
                $hasCategoryOffers = $category->offers()
                    ->where('type', 'category')
                    ->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->count() > 0;
                
                return $hasDiscountProducts || $hasCategoryOffers;
            });

        if ($request->ajax()) {
            $paginationHtml = '';
            if ($frontendPaginationSettings['enabled'] && method_exists($products, 'appends')) {
                $paginationHtml = $products->appends($request->query())->links()->render();
            }

            $productsHtml = '';
            $productsCount = method_exists($products, 'count') ? $products->count() : count($products);

            if ($productsCount > 0) {
                $productsHtml = '<div class="products-grid-compact offers">';
                foreach ($products as $product) {
                    $productsHtml .= view('partials.product-card-modern', ['product' => $product, 'offer' => true])->render();
                }
                $productsHtml .= '</div>';

                if ($frontendPaginationSettings['enabled'] && method_exists($products, 'appends')) {
                    $productsHtml .= '<div class="pagination-container" id="pagination-container">' . $paginationHtml . '</div>';
                }
            } else {
                $productsHtml = view('partials.empty-state', [
                    'icon' => 'fire',
                    'title' => 'No Offers Available',
                    'message' => 'Stay tuned for amazing deals and special offers.',
                    'action' => 'Browse All Products',
                    'actionUrl' => route('products')
                ])->render();
            }

            $totalCount = $frontendPaginationSettings['enabled'] && method_exists($products, 'total')
                ? $products->total()
                : $productsCount;

            return response()->json([
                'html' => $productsHtml,
                'total' => $totalCount,
                'pagination' => $paginationHtml
            ]);
        }

        return view('offer-products', compact(
            'products',
            'categories',
            'frontendPaginationSettings',
            'frontendPaginationControls'
        ));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)
            ->active()
            ->firstOrFail();

        $query = Product::where('category_id', $category->id)
            ->active()
            ->with('category')
            ->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END') // In-stock first
            ->orderBy('sort_order');

        // Apply frontend pagination using the trait
        $products = $this->applyFrontendPagination($query, request(), '12');
        
        // Apply offers to products using the OfferService
        if (method_exists($products, 'getCollection')) {
            // For paginated results
            $productsCollection = $products->getCollection();
            $productsWithOffers = $this->offerService->applyOffersToProducts($productsCollection);
            $products->setCollection($productsWithOffers);
        } else {
            // For regular collection
            $products = $this->offerService->applyOffersToProducts($products);
        }

        // Get frontend pagination settings and controls
        $frontendPaginationSettings = $this->getFrontendPaginationSettings(request(), '12');
        $frontendPaginationControls = $this->getPaginationControlsData(request(), 'frontend');
        
        // Get category-specific offers for display
        $categoryOffers = $this->offerService->getCategoryOffers($category);

        return view('category', compact(
            'category',
            'products',
            'categoryOffers',
            'frontendPaginationSettings',
            'frontendPaginationControls'
        ));
    }

    public function product($slug)
    {
        $product = Product::where('slug', $slug)
            ->active()
            ->with('category')
            ->firstOrFail();

        // Related products - prioritize in-stock but include out-of-stock
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END') // In-stock first
            ->orderBy('sort_order')
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

        $productQuery = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->active()
            ->with('category')
            ->orderByRaw('CASE WHEN stock > 0 THEN 0 ELSE 1 END') // In-stock first
            ->orderBy('sort_order');

        // Apply frontend pagination using the trait
        $products = $this->applyFrontendPagination($productQuery, $request, '50');

        // Get frontend pagination settings and controls
        $frontendPaginationSettings = $this->getFrontendPaginationSettings($request, '50');
        $frontendPaginationControls = $this->getPaginationControlsData($request, 'frontend');

        return view('search', compact(
            'products',
            'query',
            'frontendPaginationSettings',
            'frontendPaginationControls'
        ));
    }

    public function trackOrder(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'mobile_number' => 'required|string|size:10',
                'order_number' => 'nullable|string',
            ]);

            $query = Order::where('customer_mobile', '+91' . $request->mobile_number);

            if ($request->order_number) {
                $query->where('order_number', $request->order_number);
            }

            $orders = $query->with('items.product')->latest()->get();

            return view('track-order', compact('orders'));
        }

        return view('track-order');
    }
    
    /**
     * Animation test page
     */
    public function animationTest()
    {
        return view('animation-test');
    }
    
    /**
     * Minimum order test page
     */
    public function minimumOrderTest()
    {
        return view('minimum-order-test');
    }
}
