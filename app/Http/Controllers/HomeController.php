<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;
use App\Models\Order;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use App\Traits\HasPagination;

class HomeController extends Controller
{
    use HasPagination;
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
            ->withCount(['products' => function ($query) {
                $query->active()->where('stock', '>', 0);
            }])
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        // Handle menu filter
        $activeMenu = $request->get('menu', 'featured');
        $products = collect();

        // Get frontend pagination settings using the trait
        $frontendPaginationSettings = $this->getFrontendPaginationSettings($request, '12');
        $frontendPaginationControls = $this->getPaginationControlsData($request, 'frontend');

        if ($activeMenu === 'all') {
            // All Products
            $query = Product::active()
                ->inStock()
                ->with('category')
                ->orderBy('sort_order');

            $products = $this->applyFrontendPagination($query, $request, '12');
        } elseif ($activeMenu === 'offers') {
            // Offer Products (products with discount_price)
            $query = Product::active()
                ->inStock()
                ->whereNotNull('discount_price')
                ->where('discount_price', '>', 0)
                ->with('category')
                ->orderBy('sort_order');

            $products = $this->applyFrontendPagination($query, $request, '12');
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
        $query = Product::active()->inStock()->with('category');

        // Filter by category if specified
        if ($request->has('category') && $request->category != 'all') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Apply frontend pagination using the trait
        $products = $this->applyFrontendPagination($query->orderBy('sort_order'), $request, '12');

        // Get frontend pagination settings and controls
        $frontendPaginationSettings = $this->getFrontendPaginationSettings($request, '12');
        $frontendPaginationControls = $this->getPaginationControlsData($request, 'frontend');

        // Get categories that have products
        $categories = Category::active()
            ->parent()
            ->whereHas('products', function ($q) {
                $q->active()->where('stock', '>', 0);
            })
            ->orderBy('sort_order')
            ->get();

        if ($request->ajax()) {
            $paginationHtml = '';
            if ($frontendPaginationSettings['enabled'] && method_exists($products, 'appends')) {
                $paginationHtml = $products->appends($request->query())->links()->render();
            }

            $productsHtml = '';
            $productsCount = method_exists($products, 'count') ? $products->count() : count($products);

            if ($productsCount > 0) {
                $productsHtml = '<div class="products-grid">';
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
            'frontendPaginationSettings',
            'frontendPaginationControls'
        ));
    }

    public function offerProducts(Request $request)
    {
        $query = Product::active()
            ->inStock()
            ->whereNotNull('discount_price')
            ->where('discount_price', '>', 0)
            ->with('category');

        // Filter by category if specified
        if ($request->has('category') && $request->category != 'all') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Apply frontend pagination using the trait
        $products = $this->applyFrontendPagination($query->orderBy('sort_order'), $request, '12');

        // Get frontend pagination settings and controls
        $frontendPaginationSettings = $this->getFrontendPaginationSettings($request, '12');
        $frontendPaginationControls = $this->getPaginationControlsData($request, 'frontend');

        // Get categories that have offer products
        $categories = Category::active()
            ->parent()
            ->whereHas('products', function ($q) {
                $q->active()
                    ->where('stock', '>', 0)
                    ->whereNotNull('discount_price')
                    ->where('discount_price', '>', 0);
            })
            ->orderBy('sort_order')
            ->get();

        if ($request->ajax()) {
            $paginationHtml = '';
            if ($frontendPaginationSettings['enabled'] && method_exists($products, 'appends')) {
                $paginationHtml = $products->appends($request->query())->links()->render();
            }

            $productsHtml = '';
            $productsCount = method_exists($products, 'count') ? $products->count() : count($products);

            if ($productsCount > 0) {
                $productsHtml = '<div class="products-grid offers">';
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
            ->inStock()
            ->with('category')
            ->orderBy('sort_order');

        // Apply frontend pagination using the trait
        $products = $this->applyFrontendPagination($query, request(), '12');

        // Get frontend pagination settings and controls
        $frontendPaginationSettings = $this->getFrontendPaginationSettings(request(), '12');
        $frontendPaginationControls = $this->getPaginationControlsData(request(), 'frontend');

        return view('category', compact(
            'category',
            'products',
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

        $productQuery = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->active()
            ->inStock()
            ->with('category')
            ->orderBy('sort_order');

        // Apply frontend pagination using the trait
        $products = $this->applyFrontendPagination($productQuery, $request, '12');

        // Get frontend pagination settings and controls
        $frontendPaginationSettings = $this->getFrontendPaginationSettings($request, '12');
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
                $query->where('order_number',   $request->order_number);
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
