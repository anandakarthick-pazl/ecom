<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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
    
    /**
     * Store file content to a path
     */
    protected function storeFile($content, $path)
    {
        try {
            $fullPath = storage_path('app/public/' . $path);
            $directory = dirname($fullPath);
            
            // Ensure directory exists
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Store the file
            return file_put_contents($fullPath, $content) !== false;
        } catch (\Exception $e) {
            \Log::error('Failed to store file', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
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
        $data = $request->all();
        // echo "<pre>";print_r($data);exit;
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // $this->getTenantUniqueRule('products', 'name')
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
                // $this->getTenantUniqueRule('products', 'sku')
            ],
            'category_id' => [
                'required',
                // $this->getTenantExistsRule('categories')
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
            'sort_order' => 'integer|min:0'
        ]);

        
       
        
        // Handle checkbox values
        $data['is_active'] = $request->input('is_active', 0) == '1';
        $data['is_featured'] = $request->input('is_featured', 0) == '1';
        
        // Handle discount calculation based on type
        $this->processDiscountData($data, $request);

        // FIXED FEATURED IMAGE UPLOAD
        if ($request->hasFile('featured_image')) {
            try {
                $file = $request->file('featured_image');
                $filename = time() . '_featured_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $targetDir = storage_path('app/public/products');
                
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                
                $targetPath = $targetDir . '/' . $filename;
                
                if ($file->move($targetDir, $filename)) {
                    $data['featured_image'] = 'products/' . $filename;
                    
                    Log::info('Product featured image uploaded', [
                        'filename' => $filename,
                        'stored_path' => $data['featured_image']
                    ]);
                } else {
                    throw new \Exception('Failed to move featured image');
                }
                
            } catch (\Exception $e) {
                return $this->handleError('Featured image upload failed: ' . $e->getMessage());
            }
        }

        // FIXED MULTIPLE IMAGES UPLOAD
        $images = [];
        if ($request->hasFile('images')) {
            $targetDir = storage_path('app/public/products');
            
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            foreach ($request->file('images') as $index => $file) {
                try {
                    $filename = time() . '_img' . $index . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $targetPath = $targetDir . '/' . $filename;
                    
                    if ($file->move($targetDir, $filename)) {
                        $images[] = 'products/' . $filename;
                        
                        Log::info('Product image uploaded', [
                            'filename' => $filename,
                            'index' => $index
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to upload product image', [
                        'index' => $index,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        $data['images'] = $images;

        // Create with tenant scope
        $product = Product::create($data);

        $this->logActivity('Product created', $product, ['name' => $product->name]);

        return $this->handleSuccess(
            'Product created successfully!',
            'admin.products.index'
        );
    }

    public function update(Request $request, Product $product)
    {
        $this->validateTenantOwnership($product);
        
        $request->validate([
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
            'sort_order' => 'integer|min:0'
        ]);

        $data = $request->all();
        
        // Handle checkbox values
        $data['is_active'] = $request->input('is_active', 0) == '1';
        $data['is_featured'] = $request->input('is_featured', 0) == '1';

        // FIXED FEATURED IMAGE UPDATE
        if ($request->hasFile('featured_image')) {
            try {
                // Delete old featured image
                if ($product->featured_image) {
                    $oldPath = storage_path('app/public/' . $product->featured_image);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
                
                $file = $request->file('featured_image');
                $filename = time() . '_featured_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $targetDir = storage_path('app/public/products');
                
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                
                if ($file->move($targetDir, $filename)) {
                    $data['featured_image'] = 'products/' . $filename;
                }
                
            } catch (\Exception $e) {
                return $this->handleError('Featured image upload failed: ' . $e->getMessage());
            }
        }

        // FIXED ADDITIONAL IMAGES UPDATE
        $images = $product->images ?? [];
        if ($request->hasFile('images')) {
            $targetDir = storage_path('app/public/products');
            
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            
            foreach ($request->file('images') as $index => $file) {
                try {
                    $filename = time() . '_img' . $index . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
                    if ($file->move($targetDir, $filename)) {
                        $images[] = 'products/' . $filename;
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to upload product image during update', [
                        'product_id' => $product->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        $data['images'] = $images;

        $product->update($data);

        $this->logActivity('Product updated', $product, ['name' => $product->name]);

        return $this->handleSuccess(
            'Product updated successfully!',
            'admin.products.index'
        );
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
            return $this->handleError(
                'Cannot delete product with order history!',
                'admin.products.index'
            );
        }

        // Delete associated files using dynamic storage
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

        $this->logActivity('Product deleted', null, ['name' => $productName]);

        return $this->handleSuccess(
            'Product deleted successfully!',
            'admin.products.index'
        );
    }

    public function toggleStatus(Product $product)
    {
        try {
            $this->validateTenantOwnership($product);
            
            $product->update(['is_active' => !$product->is_active]);
            
            $status = $product->is_active ? 'activated' : 'deactivated';
            
            $this->logActivity("Product {$status}", $product, ['name' => $product->name]);
            
            // Clean the message to ensure no newline characters
            $message = "Product {$status} successfully!";
            
            // Return proper JSON response for AJAX calls
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_active' => $product->is_active
                ]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Failed to toggle product status', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update product status'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to update product status!');
        }
    }

    public function toggleFeatured(Product $product)
    {
        try {
            $this->validateTenantOwnership($product);
            
            $product->update(['is_featured' => !$product->is_featured]);
            
            $status = $product->is_featured ? 'marked as featured' : 'removed from featured';
            
            $this->logActivity("Product {$status}", $product, ['name' => $product->name]);
            
            // Clean the message to ensure no newline characters
            $message = "Product {$status} successfully!";
            
            // Return proper JSON response for AJAX calls
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'is_featured' => $product->is_featured
                ]);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            Log::error('Failed to toggle product featured status', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            
            if (request()->expectsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update featured status'
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to update featured status!');
        }
    }

    /**
     * Remove image from product
     */
    public function removeImage(Request $request, Product $product)
    {
        try {
            $this->validateTenantOwnership($product);
            
            $imageIndex = $request->input('image_index');
            $images = $product->images ?? [];
            
            if (isset($images[$imageIndex])) {
                // Delete the file using dynamic storage
                $this->deleteFileDynamically($images[$imageIndex]);
                
                // Remove from array and reindex
                unset($images[$imageIndex]);
                $images = array_values($images);
                
                // Update product
                $product->update(['images' => $images]);
                
                $this->logActivity('Product image removed', $product, [
                    'name' => $product->name,
                    'image_index' => $imageIndex
                ]);
                
                // Return proper JSON response
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Image removed successfully!',
                        'remaining_images' => count($images)
                    ]);
                }
                
                return redirect()->back()->with('success', 'Image removed successfully!');
            }
            
            // Image not found
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found!'
                ], 404);
            }
            
            return redirect()->back()->with('error', 'Image not found!');
            
        } catch (\Exception $e) {
            Log::error('Failed to remove product image', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'image_index' => $request->input('image_index')
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove image: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to remove image!');
        }
    }

    /**
     * Bulk actions for products
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'products' => 'required|array',
            'products.*' => 'exists:products,id'
        ]);

        $products = Product::whereIn('id', $request->products)->get();
        
        // Validate tenant ownership for all products
        foreach ($products as $product) {
            $this->validateTenantOwnership($product);
        }

        $count = $products->count();

        switch ($request->action) {
            case 'activate':
                Product::whereIn('id', $request->products)->update(['is_active' => true]);
                $message = "{$count} products activated successfully!";
                break;
                
            case 'deactivate':
                Product::whereIn('id', $request->products)->update(['is_active' => false]);
                $message = "{$count} products deactivated successfully!";
                break;
                
            case 'delete':
                // Check if any product has order history
                $hasOrders = $products->filter(function ($product) {
                    return $product->orderItems()->count() > 0;
                });

                if ($hasOrders->count() > 0) {
                    return $this->handleError('Cannot delete products with order history!');
                }

                // Delete files and products using dynamic storage
                foreach ($products as $product) {
                    if ($product->featured_image) {
                        $this->deleteFileDynamically($product->featured_image);
                    }
                    if ($product->images) {
                        foreach ($product->images as $image) {
                            $this->deleteFileDynamically($image);
                        }
                    }
                }

                Product::whereIn('id', $request->products)->delete();
                $message = "{$count} products deleted successfully!";
                break;
        }

        $this->logActivity("Bulk action: {$request->action}", null, [
            'action' => $request->action,
            'count' => $count,
            'product_ids' => $request->products
        ]);

        return $this->handleSuccess($message);
    }

    /**
     * Export products to CSV
     */
    private function exportProducts($products)
    {
        $filename = 'products_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Name',
                'SKU',
                'Category',
                'Price',
                'Discount Price',
                'Stock',
                'Weight',
                'Weight Unit',
                'Tax Percentage',
                'Status',
                'Featured',
                'Description',
                'Short Description',
                'Meta Title',
                'Meta Description',
                'Created At',
                'Updated At'
            ]);
            
            // Add product data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->sku ?? '',
                    $product->category->name ?? '',
                    $product->price,
                    $product->discount_price ?? '',
                    $product->stock,
                    $product->weight ?? '',
                    $product->weight_unit ?? '',
                    $product->tax_percentage ?? '',
                    $product->is_active ? 'Active' : 'Inactive',
                    $product->is_featured ? 'Yes' : 'No',
                    strip_tags($product->description ?? ''),
                    strip_tags($product->short_description ?? ''),
                    $product->meta_title ?? '',
                    $product->meta_description ?? '',
                    $product->created_at->format('Y-m-d H:i:s'),
                    $product->updated_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process discount data based on discount type
     */
    private function processDiscountData(&$data, Request $request)
    {
        $discountType = $request->input('discount_type');
        $price = (float) $request->input('price', 0);
        
        // Reset discount_price initially
        $data['discount_price'] = null;
        
        if ($discountType === 'percentage') {
            $discountPercentage = (float) $request->input('discount_percentage', 0);
            if ($discountPercentage > 0 && $price > 0) {
                $discountAmount = ($price * $discountPercentage) / 100;
                $data['discount_price'] = max(0, $price - $discountAmount);
            }
        } elseif ($discountType === 'fixed') {
            $discountPrice = (float) $request->input('discount_price', 0);
            if ($discountPrice > 0 && $discountPrice < $price) {
                $data['discount_price'] = $discountPrice;
            }
        }
        
        // Remove temporary fields that shouldn't be saved to database
        unset($data['discount_type'], $data['discount_percentage']);
    }

    /**
     * Show bulk upload form
     */
    public function showBulkUpload()
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.products.bulk-upload', compact('categories'));
    }

    /**
     * Download CSV template for bulk upload
     */
    public function downloadTemplate()
    {
        $filename = 'product_upload_template.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers with all product fields
            fputcsv($file, [
                'name',                 // Required
                'description',          // Required
                'short_description',    // Optional
                'price',               // Required
                'discount_price',      // Optional
                'cost_price',          // Optional
                'stock',               // Required
                'sku',                 // Optional but recommended
                'barcode',             // Optional
                'code',                // Optional
                'category_name',       // Required (category name)
                'weight',              // Optional
                'weight_unit',         // Optional (kg, g, lb, oz)
                'tax_percentage',      // Required
                'low_stock_threshold', // Optional
                'is_active',           // Optional (1 or 0)
                'is_featured',         // Optional (1 or 0)
                'sort_order',          // Optional
                'meta_title',          // Optional
                'meta_description',    // Optional
                'meta_keywords',       // Optional
                'featured_image_url',  // Optional (URL to image)
                'additional_images'    // Optional (comma-separated URLs)
            ]);
            
            // Add sample data row for reference
            fputcsv($file, [
                'Sample Product Name',
                'This is a detailed description of the product with all features and benefits.',
                'Short product description for listings.',
                '99.99',
                '79.99',
                '50.00',
                '100',
                'SKU001',
                '1234567890123',
                'PROD001',
                'Electronics',
                '1.5',
                'kg',
                '18',
                '10',
                '1',
                '1',
                '1',
                'Sample Product - Buy Online',
                'Buy sample product online at best price.',
                'sample, product, online, buy',
                'https://example.com/product-image.jpg',
                'https://example.com/image1.jpg,https://example.com/image2.jpg'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Process bulk upload
     */
    public function processBulkUpload(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB max
                function ($attribute, $value, $fail) {
                    $allowedExtensions = ['csv', 'xlsx', 'xls'];
                    $allowedMimeTypes = [
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'application/excel',
                        'application/vnd.ms-excel',
                        'application/vnd.msexcel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ];
                    
                    $extension = strtolower($value->getClientOriginalExtension());
                    $mimeType = $value->getMimeType();
                    
                    if (!in_array($extension, $allowedExtensions) && !in_array($mimeType, $allowedMimeTypes)) {
                        $fail('The file must be a CSV (.csv) or Excel (.xlsx, .xls) file.');
                    }
                }
            ],
            'update_existing' => 'nullable|boolean'
        ]);

        try {
            $file = $request->file('file');
            $updateExisting = $request->boolean('update_existing');
            
            // Create temp directory with proper Windows path handling
            $tempDir = storage_path('app') . DIRECTORY_SEPARATOR . 'temp';
            if (!is_dir($tempDir)) {
                if (!mkdir($tempDir, 0755, true)) {
                    throw new \Exception('Failed to create temporary directory: ' . $tempDir);
                }
            }
            
            // Use direct file move instead of Laravel storage
            $tempFileName = 'bulk_upload_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $fullPath = $tempDir . DIRECTORY_SEPARATOR . $tempFileName;
            
            // Move uploaded file directly to temp directory
            if (!$file->move($tempDir, $tempFileName)) {
                throw new \Exception('Failed to move uploaded file to: ' . $fullPath);
            }
            
            // Verify file was moved successfully
            if (!file_exists($fullPath)) {
                throw new \Exception('File was not found after move operation: ' . $fullPath);
            }
            
            \Log::info('File uploaded successfully for bulk processing', [
                'original_name' => $file->getClientOriginalName(),
                'temp_path' => $fullPath,
                'file_size' => filesize($fullPath),
                'temp_dir_exists' => is_dir($tempDir),
                'temp_dir_writable' => is_writable($tempDir)
            ]);
            
            // Parse the file based on extension
            $data = $this->parseUploadFile($fullPath, $file->getClientOriginalExtension());
            
            if (empty($data)) {
                return redirect()->back()->with('error', 'No valid data found in the uploaded file.');
            }

            // Process the data
            $result = $this->processProductData($data, $updateExisting);
            
            // Clean up temp file
            if (file_exists($fullPath)) {
                unlink($fullPath);
                \Log::info('Temporary file cleaned up', ['path' => $fullPath]);
            }
            
            // Log the upload
            $this->logBulkUpload($file, $result);
            
            $message = "Bulk upload completed! ";
            $message .= "Created: {$result['created']}, ";
            $message .= "Updated: {$result['updated']}, ";
            $message .= "Errors: {$result['errors']}";
            
            if ($result['errors'] > 0) {
                session()->flash('upload_errors', $result['error_details']);
                return redirect()->back()->with('warning', $message);
            }
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            // Enhanced error logging for debugging
            $debugInfo = [
                'error_message' => $e->getMessage(),
                'file_name' => isset($file) ? $file->getClientOriginalName() : 'unknown',
                'file_size' => isset($file) ? $file->getSize() : 'unknown',
                'temp_dir' => isset($tempDir) ? $tempDir : 'not_set',
                'temp_dir_exists' => isset($tempDir) ? is_dir($tempDir) : false,
                'temp_dir_writable' => isset($tempDir) && is_dir($tempDir) ? is_writable($tempDir) : false,
                'full_path' => isset($fullPath) ? $fullPath : 'not_set',
                'storage_path' => storage_path('app'),
                'directory_separator' => DIRECTORY_SEPARATOR,
                'php_upload_max_filesize' => ini_get('upload_max_filesize'),
                'php_post_max_size' => ini_get('post_max_size'),
                'php_tmp_dir' => sys_get_temp_dir(),
                'trace' => $e->getTraceAsString()
            ];
            
            // Clean up temp file if it exists
            if (isset($fullPath) && file_exists($fullPath)) {
                unlink($fullPath);
                $debugInfo['cleanup'] = 'temp file removed';
            }
            
            \Log::error('Bulk upload failed with detailed debug info', $debugInfo);
            
            return redirect()->back()->with('error', 'Upload failed: ' . $e->getMessage() . ' (Check logs for details)');
        }
    }

    /**
     * Parse uploaded file (CSV or Excel)
     */
    private function parseUploadFile($filePath, $extension)
    {
        if ($extension === 'csv') {
            return $this->parseCsvFile($filePath);
        } else {
            return $this->parseExcelFile($filePath);
        }
    }

    /**
     * Parse CSV file
     */
    private function parseCsvFile($filePath)
    {
        $data = [];
        $headers = [];
        
        // Verify file exists
        if (!file_exists($filePath)) {
            throw new \Exception('CSV file not found: ' . $filePath);
        }
        
        // Check if file is readable
        if (!is_readable($filePath)) {
            throw new \Exception('CSV file is not readable: ' . $filePath);
        }
        
        try {
            if (($handle = fopen($filePath, 'r')) !== FALSE) {
                // Read header row
                $headers = fgetcsv($handle);
                
                if ($headers === FALSE || empty($headers)) {
                    fclose($handle);
                    throw new \Exception('Could not read CSV headers or file is empty.');
                }
                
                // Read data rows
                $rowCount = 0;
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $rowCount++;
                    if (count($row) === count($headers)) {
                        $data[] = array_combine($headers, $row);
                    } else {
                        \Log::warning("CSV row {$rowCount} has mismatched column count", [
                            'expected' => count($headers),
                            'actual' => count($row),
                            'row' => $row
                        ]);
                    }
                }
                fclose($handle);
                
                \Log::info('CSV file parsed successfully', [
                    'file' => $filePath,
                    'headers' => count($headers),
                    'rows' => count($data)
                ]);
            } else {
                throw new \Exception('Could not open CSV file for reading.');
            }
        } catch (\Exception $e) {
            \Log::error('CSV parsing failed', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Failed to parse CSV file: ' . $e->getMessage());
        }
        
        return $data;
    }

    /**
     * Parse Excel file using PhpSpreadsheet
     */
    private function parseExcelFile($filePath)
    {
        try {
            // Check if Laravel Excel is available
            if (!class_exists('\Maatwebsite\Excel\Facades\Excel')) {
                throw new \Exception('Laravel Excel package is not installed. Please install maatwebsite/excel.');
            }
            
            // Use Laravel Excel package which includes PhpSpreadsheet
            $collection = \Maatwebsite\Excel\Facades\Excel::toArray([], $filePath);
            
            if (empty($collection) || empty($collection[0])) {
                return [];
            }
            
            $rows = $collection[0]; // Get first sheet
            
            if (empty($rows)) {
                return [];
            }
            
            $headers = array_shift($rows); // Remove header row
            $data = [];
            
            foreach ($rows as $row) {
                if (count($row) === count($headers)) {
                    $data[] = array_combine($headers, $row);
                }
            }
            
            return $data;
        } catch (\Exception $e) {
            \Log::error('Excel parsing failed', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Failed to parse Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Process product data from upload
     */
    private function processProductData($data, $updateExisting = false)
    {
        $result = [
            'created' => 0,
            'updated' => 0,
            'errors' => 0,
            'error_details' => []
        ];
        
        // Cache categories for performance
        $categories = Category::active()->pluck('id', 'name')->toArray();
        
        foreach ($data as $index => $row) {
            $rowNumber = $index + 2; // +2 because array is 0-indexed and we skip header
            
            try {
                // Validate required fields
                if (empty($row['name']) || empty($row['description']) || empty($row['price']) || empty($row['category_name'])) {
                    $result['errors']++;
                    $result['error_details'][] = "Row $rowNumber: Missing required fields (name, description, price, category_name)";
                    continue;
                }
                
                // Find category
                $categoryId = $categories[$row['category_name']] ?? null;
                if (!$categoryId) {
                    $result['errors']++;
                    $result['error_details'][] = "Row $rowNumber: Category '{$row['category_name']}' not found";
                    continue;
                }
                
                // Prepare product data
                $productData = [
                    'name' => trim($row['name']),
                    'description' => trim($row['description']),
                    'short_description' => trim($row['short_description'] ?? ''),
                    'price' => (float) $row['price'],
                    'discount_price' => !empty($row['discount_price']) ? (float) $row['discount_price'] : null,
                    'cost_price' => !empty($row['cost_price']) ? (float) $row['cost_price'] : null,
                    'stock' => (int) ($row['stock'] ?? 0),
                    'sku' => trim($row['sku'] ?? ''),
                    'barcode' => trim($row['barcode'] ?? ''),
                    'code' => trim($row['code'] ?? ''),
                    'category_id' => $categoryId,
                    'weight' => !empty($row['weight']) ? (float) $row['weight'] : null,
                    'weight_unit' => trim($row['weight_unit'] ?? 'kg'),
                    'tax_percentage' => (float) ($row['tax_percentage'] ?? 0),
                    'low_stock_threshold' => (int) ($row['low_stock_threshold'] ?? 5),
                    'is_active' => !empty($row['is_active']) ? (bool) $row['is_active'] : true,
                    'is_featured' => !empty($row['is_featured']) ? (bool) $row['is_featured'] : false,
                    'sort_order' => (int) ($row['sort_order'] ?? 0),
                    'meta_title' => trim($row['meta_title'] ?? ''),
                    'meta_description' => trim($row['meta_description'] ?? ''),
                    'meta_keywords' => trim($row['meta_keywords'] ?? ''),
                ];
                
                // Apply tenant scope data
                $productData['company_id'] = $this->getCurrentCompanyId();
                $productData['branch_id'] = session('selected_branch_id');
                
                // Check for existing product
                $existingProduct = null;
                if (!empty($productData['sku'])) {
                    $existingProduct = Product::where('sku', $productData['sku'])
                        ->where('company_id', $productData['company_id'])
                        ->first();
                }
                
                if (!$existingProduct) {
                    $existingProduct = Product::where('name', $productData['name'])
                        ->where('company_id', $productData['company_id'])
                        ->first();
                }
                
                if ($existingProduct && $updateExisting) {
                    // Update existing product
                    $existingProduct->update($productData);
                    
                    // Handle images if provided
                    $this->processProductImages($existingProduct, $row);
                    
                    $result['updated']++;
                } elseif (!$existingProduct) {
                    // Create new product
                    $product = Product::create($productData);
                    
                    // Handle images if provided
                    $this->processProductImages($product, $row);
                    
                    $result['created']++;
                } else {
                    // Product exists but update is not enabled
                    $result['errors']++;
                    $result['error_details'][] = "Row $rowNumber: Product '{$productData['name']}' already exists (enable update to modify)";
                }
                
            } catch (\Exception $e) {
                $result['errors']++;
                $result['error_details'][] = "Row $rowNumber: {$e->getMessage()}";
            }
        }
        
        return $result;
    }

    /**
     * Process product images from upload data
     */
    private function processProductImages($product, $row)
    {
        // Handle featured image
        if (!empty($row['featured_image_url'])) {
            try {
                $imagePath = $this->downloadAndStoreImage($row['featured_image_url'], 'featured');
                if ($imagePath) {
                    $product->featured_image = $imagePath;
                }
            } catch (\Exception $e) {
                Log::warning("Failed to download featured image for product {$product->id}: {$e->getMessage()}");
            }
        }
        
        // Handle additional images
        if (!empty($row['additional_images'])) {
            $imageUrls = explode(',', $row['additional_images']);
            $imagePaths = [];
            
            foreach ($imageUrls as $url) {
                $url = trim($url);
                if (!empty($url)) {
                    try {
                        $imagePath = $this->downloadAndStoreImage($url, 'gallery');
                        if ($imagePath) {
                            $imagePaths[] = $imagePath;
                        }
                    } catch (\Exception $e) {
                        Log::warning("Failed to download additional image for product {$product->id}: {$e->getMessage()}");
                    }
                }
            }
            
            if (!empty($imagePaths)) {
                $product->images = $imagePaths;
            }
        }
        
        if ($product->isDirty()) {
            $product->save();
        }
    }

    /**
     * Download and store image from URL
     */
    private function downloadAndStoreImage($url, $type = 'gallery')
    {
        try {
            $contents = file_get_contents($url);
            if ($contents === false) {
                return null;
            }
            
            $extension = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
            if (empty($extension)) {
                $extension = 'jpg';
            }
            
            $filename = $type . '_' . time() . '_' . uniqid() . '.' . $extension;
            $path = 'products/' . $filename;
            
            if ($this->storeFile($contents, $path)) {
                return $path;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error("Failed to download image from $url: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Log bulk upload activity
     */
    private function logBulkUpload($file, $result)
    {
        try {
            \App\Models\UploadLog::create([
                'file_name' => 'bulk_upload_' . time() . '.' . $file->getClientOriginalExtension(),
                'original_name' => $file->getClientOriginalName(),
                'file_path' => '',
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'storage_type' => 'local',
                'upload_type' => 'product_bulk',
                'uploaded_by' => auth()->id(),
                'meta_data' => $result,
                'company_id' => session('selected_company_id')
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log bulk upload: ' . $e->getMessage());
            // Continue without logging - don't fail the upload process
        }
    }

    /**
     * Show upload history
     */
    public function uploadHistory()
    {
        try {
            $uploads = \App\Models\UploadLog::where('upload_type', 'product_bulk')
                ->with('uploader')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } catch (\Exception $e) {
            // If UploadLog table doesn't exist or there's an error, create empty collection
            \Log::warning('Upload history unavailable: ' . $e->getMessage());
            $uploads = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }
            
        return view('admin.products.upload-history', compact('uploads'));
    }
}
