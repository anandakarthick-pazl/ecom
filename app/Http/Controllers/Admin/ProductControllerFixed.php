<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Traits\DynamicStorage;
use App\Traits\HasPagination;

class ProductController extends BaseAdminController
{
    use DynamicStorage, HasPagination;
    
    /**
     * Validate that the user owns the resource (tenant isolation)
     */
    protected function validateTenantOwnership($model)
    {
        if ($model->company_id !== $this->getCurrentCompanyId()) {
            abort(403, 'You do not have access to this resource.');
        }
    }
    
    /**
     * Get tenant-specific unique validation rule
     */
    protected function getTenantUniqueRule($table, $column, $ignore = null)
    {
        $rule = "unique:{$table},{$column}";
        if ($ignore) {
            $rule .= ",{$ignore}";
        }
        $rule .= ",id,company_id,{$this->getCurrentCompanyId()}";
        return $rule;
    }
    
    /**
     * Get tenant-specific exists validation rule
     */
    protected function getTenantExistsRule($table, $column = 'id')
    {
        return "exists:{$table},{$column},company_id,{$this->getCurrentCompanyId()}";
    }
    
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('sku', 'LIKE', "%{$request->search}%")
                  ->orWhere('description', 'LIKE', "%{$request->search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        // Stock status filter
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('stock', '>', 10);
                    break;
                case 'low_stock':
                    $query->where('stock', '>', 0)->where('stock', '<=', 10);
                    break;
                case 'out_of_stock':
                    $query->where('stock', '=', 0);
                    break;
            }
        }

        // Apply tenant scope
        $query = $this->applyTenantScope($query);

        // Handle export
        if ($request->filled('export') && $request->export === 'csv') {
            return $this->exportProducts($query->get());
        }
        
        // Get paginated results using dynamic pagination settings
        $products = $this->applyAdminPagination($query->latest(), $request, '20');
        
        // Get pagination controls data for the view
        $paginationControls = $this->getPaginationControlsData($request, 'admin');
                  
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'paginationControls'));
    }

    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Store all form data first
        $data = $request->all();
        
        // Validate with comprehensive rules
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|string|in:percentage,fixed',
            'discount_percentage' => 'nullable|numeric|min:0|max:100|required_if:discount_type,percentage',
            'discount_price' => 'nullable|numeric|min:0|lt:price|required_if:discount_type,fixed',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'weight' => 'nullable|numeric|min:0',
            'weight_unit' => 'string|in:gm,kg,ml,ltr,box,pack',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_active' => 'nullable',
            'is_featured' => 'nullable',
            'sort_order' => 'nullable|integer|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:50',
            'code' => 'nullable|string|max:50',
            'low_stock_threshold' => 'nullable|integer|min:0'
        ]);

        try {
            DB::beginTransaction();
            
            // Process all data systematically
            $productData = $this->processProductData($validatedData, $request);
            
            // Handle file uploads
            $productData = $this->handleFileUploads($productData, $request);
            
            // Add tenant and branch information
            $productData['company_id'] = $this->getCurrentCompanyId();
            $productData['branch_id'] = session('selected_branch_id');
            
            // Create product
            $product = Product::create($productData);
            
            DB::commit();
            
            $this->logActivity('Product created', $product, ['name' => $product->name]);
            
            return $this->handleSuccess('Product created successfully!', 'admin.products.index');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Product creation failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->handleError('Product creation failed: ' . $e->getMessage());
        }
    }

    /**
     * FIXED UPDATE METHOD - Now handles all fields correctly
     */
    public function update(Request $request, Product $product)
    {
        $this->validateTenantOwnership($product);
        
        // Log incoming data for debugging
        Log::info('Product update attempt', [
            'product_id' => $product->id,
            'request_data' => $request->all(),
            'product_before' => $product->toArray()
        ]);
        
        // Validate with comprehensive rules
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                $this->getTenantUniqueRule('products', 'name', $product->id)
            ],
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|string|in:percentage,fixed',
            'discount_percentage' => 'nullable|numeric|min:0|max:100|required_if:discount_type,percentage',
            'discount_price' => 'nullable|numeric|min:0|lt:price|required_if:discount_type,fixed',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'stock' => 'required|integer|min:0',
            'sku' => [
                'nullable',
                'string',
                $this->getTenantUniqueRule('products', 'sku', $product->id)
            ],
            'category_id' => [
                'required',
                $this->getTenantExistsRule('categories')
            ],
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'weight' => 'nullable|numeric|min:0',
            'weight_unit' => 'string|in:gm,kg,ml,ltr,box,pack',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_active' => 'nullable',
            'is_featured' => 'nullable',
            'sort_order' => 'nullable|integer|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'barcode' => 'nullable|string|max:50',
            'code' => 'nullable|string|max:50',
            'low_stock_threshold' => 'nullable|integer|min:0'
        ]);

        try {
            DB::beginTransaction();
            
            // Process all data systematically
            $productData = $this->processProductData($validatedData, $request, $product);
            
            // Handle file uploads (only if new files are uploaded)
            if ($request->hasFile('featured_image') || $request->hasFile('images')) {
                $productData = $this->handleFileUploads($productData, $request, $product);
            }
            
            // Log the data being updated
            Log::info('Product update data processed', [
                'product_id' => $product->id,
                'update_data' => $productData
            ]);
            
            // Update the product with all processed data
            $result = $product->update($productData);
            
            if (!$result) {
                throw new \Exception('Product update failed - database did not confirm update');
            }
            
            // Refresh the product to get updated data
            $product->refresh();
            
            DB::commit();
            
            Log::info('Product updated successfully', [
                'product_id' => $product->id,
                'product_after' => $product->toArray()
            ]);
            
            $this->logActivity('Product updated', $product, ['name' => $product->name]);
            
            return $this->handleSuccess('Product updated successfully!', 'admin.products.index');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Product update failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'validated_data' => $validatedData,
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->handleError('Product update failed: ' . $e->getMessage());
        }
    }

    /**
     * Process product data comprehensively
     */
    private function processProductData(array $validatedData, Request $request, Product $existingProduct = null)
    {
        $data = [];
        
        // Basic fields - always include these
        $basicFields = [
            'name', 'description', 'short_description', 'price', 'tax_percentage', 
            'stock', 'sku', 'category_id', 'weight', 'weight_unit',
            'meta_title', 'meta_description', 'meta_keywords', 'sort_order',
            'cost_price', 'barcode', 'code', 'low_stock_threshold'
        ];
        
        foreach ($basicFields as $field) {
            if (array_key_exists($field, $validatedData)) {
                $data[$field] = $validatedData[$field];
            }
        }
        
        // Handle boolean fields explicitly
        $data['is_active'] = $request->has('is_active') && $request->input('is_active') == '1';
        $data['is_featured'] = $request->has('is_featured') && $request->input('is_featured') == '1';
        
        // Process discount data
        $data = $this->processDiscountData($data, $request);
        
        // Ensure numeric fields are properly cast
        if (isset($data['price'])) $data['price'] = (float) $data['price'];
        if (isset($data['discount_price'])) $data['discount_price'] = $data['discount_price'] ? (float) $data['discount_price'] : null;
        if (isset($data['cost_price'])) $data['cost_price'] = $data['cost_price'] ? (float) $data['cost_price'] : null;
        if (isset($data['weight'])) $data['weight'] = $data['weight'] ? (float) $data['weight'] : null;
        if (isset($data['tax_percentage'])) $data['tax_percentage'] = (float) $data['tax_percentage'];
        if (isset($data['stock'])) $data['stock'] = (int) $data['stock'];
        if (isset($data['sort_order'])) $data['sort_order'] = $data['sort_order'] ? (int) $data['sort_order'] : 0;
        if (isset($data['low_stock_threshold'])) $data['low_stock_threshold'] = $data['low_stock_threshold'] ? (int) $data['low_stock_threshold'] : 5;
        
        return $data;
    }

    /**
     * FIXED DISCOUNT PROCESSING - Now handles all scenarios correctly
     */
    private function processDiscountData(array $data, Request $request)
    {
        $discountType = $request->input('discount_type');
        $price = (float) $request->input('price', 0);
        
        // Initialize discount_price
        $data['discount_price'] = null;
        
        if ($discountType === 'percentage') {
            $discountPercentage = (float) $request->input('discount_percentage', 0);
            if ($discountPercentage > 0 && $price > 0) {
                $discountAmount = ($price * $discountPercentage) / 100;
                $data['discount_price'] = max(0, $price - $discountAmount);
                
                Log::info('Processing percentage discount', [
                    'price' => $price,
                    'percentage' => $discountPercentage,
                    'discount_amount' => $discountAmount,
                    'final_price' => $data['discount_price']
                ]);
            }
        } elseif ($discountType === 'fixed') {
            $discountPrice = (float) $request->input('discount_price', 0);
            if ($discountPrice > 0 && $discountPrice < $price) {
                $data['discount_price'] = $discountPrice;
                
                Log::info('Processing fixed discount', [
                    'price' => $price,
                    'discount_price' => $discountPrice
                ]);
            }
        } else {
            // No discount
            $data['discount_price'] = null;
            
            Log::info('No discount applied', [
                'price' => $price,
                'discount_type' => $discountType
            ]);
        }
        
        return $data;
    }

    /**
     * Handle file uploads separately
     */
    private function handleFileUploads(array $data, Request $request, Product $existingProduct = null)
    {
        // Handle featured image
        if ($request->hasFile('featured_image')) {
            try {
                // Delete old featured image if updating
                if ($existingProduct && $existingProduct->featured_image) {
                    $this->deleteFileDynamically($existingProduct->featured_image);
                }
                
                $file = $request->file('featured_image');
                $filename = time() . '_featured_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $targetDir = storage_path('app/public/products');
                
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                
                if ($file->move($targetDir, $filename)) {
                    $data['featured_image'] = 'products/' . $filename;
                    Log::info('Featured image uploaded successfully', [
                        'filename' => $filename,
                        'path' => $data['featured_image']
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Featured image upload failed', ['error' => $e->getMessage()]);
                throw new \Exception('Featured image upload failed: ' . $e->getMessage());
            }
        }
        
        // Handle additional images
        if ($request->hasFile('images')) {
            try {
                $images = $existingProduct ? ($existingProduct->images ?? []) : [];
                $targetDir = storage_path('app/public/products');
                
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                
                foreach ($request->file('images') as $index => $file) {
                    $filename = time() . '_img' . $index . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
                    if ($file->move($targetDir, $filename)) {
                        $images[] = 'products/' . $filename;
                        Log::info('Additional image uploaded', [
                            'filename' => $filename,
                            'index' => $index
                        ]);
                    }
                }
                
                $data['images'] = $images;
            } catch (\Exception $e) {
                Log::error('Additional images upload failed', ['error' => $e->getMessage()]);
                throw new \Exception('Additional images upload failed: ' . $e->getMessage());
            }
        }
        
        return $data;
    }

    public function show(Product $product)
    {
        $this->validateTenantOwnership($product);
        $product->load('category');
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->validateTenantOwnership($product);
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function destroy(Product $product)
    {
        $this->validateTenantOwnership($product);
        
        if ($product->orderItems()->count() > 0) {
            return $this->handleError('Cannot delete product with order history!', 'admin.products.index');
        }

        try {
            DB::beginTransaction();
            
            // Delete associated files
            if ($product->featured_image) {
                $this->deleteFileDynamically($product->featured_image);
            }

            if ($product->images) {
                foreach ($product->images as $image) {
                    $this->deleteFileDynamically($image);
                }
            }

            $productName = $product->name;
            $product->delete();
            
            DB::commit();
            
            $this->logActivity('Product deleted', null, ['name' => $productName]);
            
            return $this->handleSuccess('Product deleted successfully!', 'admin.products.index');
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Product deletion failed', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            return $this->handleError('Product deletion failed: ' . $e->getMessage());
        }
    }

    // ... (rest of the methods remain the same)
}
