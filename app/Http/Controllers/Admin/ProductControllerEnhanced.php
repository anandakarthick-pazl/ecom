<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\EnhancedDynamicStorage; // Updated trait
use App\Traits\HasPagination;

class ProductControllerEnhanced extends BaseAdminController
{
    use EnhancedDynamicStorage, HasPagination;
    
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
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                $this->getTenantUniqueRule('products', 'name')
            ],
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'stock' => 'required|integer|min:0',
            'sku' => [
                'nullable',
                'string',
                $this->getTenantUniqueRule('products', 'sku')
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

        // Enhanced file upload handling for featured image
        if ($request->hasFile('featured_image')) {
            $uploadOptions = [
                'max_size' => 2 * 1024 * 1024, // 2MB
                'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
                'max_dimensions' => [2000, 2000] // Max 2000x2000px
            ];
            
            try {
                $uploadResult = $this->storeFileDynamically(
                    $request->file('featured_image'), 
                    'products', 
                    'products',
                    $uploadOptions
                );
                $data['featured_image'] = $uploadResult['file_path'];
                
                // Generate thumbnail
                $thumbnailPath = $this->generateThumbnailDynamically($uploadResult['file_path']);
                if ($thumbnailPath) {
                    $data['featured_image_thumb'] = $thumbnailPath;
                }
                
            } catch (\Exception $e) {
                return $this->handleError(
                    'Featured image upload failed: ' . $e->getMessage(),
                    'admin.products.create'
                );
            }
        }

        // Enhanced multiple images upload
        $images = [];
        if ($request->hasFile('images')) {
            $uploadOptions = [
                'max_size' => 2 * 1024 * 1024, // 2MB per image
                'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
                'max_dimensions' => [2000, 2000]
            ];
            
            $uploadResults = $this->storeMultipleFilesDynamically(
                $request->file('images'),
                'products',
                'products',
                $uploadOptions
            );
            
            // Process successful uploads
            foreach ($uploadResults['results'] as $result) {
                if (isset($result['file_path'])) {
                    $images[] = $result['file_path'];
                    
                    // Generate thumbnail for each image
                    $thumbnailPath = $this->generateThumbnailDynamically($result['file_path']);
                    if ($thumbnailPath) {
                        $images[] = $thumbnailPath; // Store thumb path as well
                    }
                }
            }
            
            // Log any upload errors
            if ($uploadResults['error_count'] > 0) {
                \Log::warning('Some product images failed to upload', [
                    'errors' => $uploadResults['error_count'],
                    'total' => $uploadResults['total']
                ]);
            }
        }
        $data['images'] = $images;

        // Create with tenant scope (company_id is automatically added via trait)
        $product = Product::create($data);

        $this->logActivity('Product created', $product, ['name' => $product->name]);

        return $this->handleSuccess(
            'Product created successfully!',
            'admin.products.index'
        );
    }

    public function show(Product $product)
    {
        $this->validateTenantOwnership($product);
        $product->load('category');
        
        // Get enhanced file information
        if ($product->featured_image) {
            $product->featured_image_info = $this->getFileInfoDynamically($product->featured_image);
        }
        
        if ($product->images) {
            $product->images_info = [];
            foreach ($product->images as $image) {
                $imageInfo = $this->getFileInfoDynamically($image);
                if ($imageInfo) {
                    $product->images_info[] = $imageInfo;
                }
            }
        }
        
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->validateTenantOwnership($product);
        $categories = Category::active()->orderBy('name')->get();
        
        // Get file information for editing
        if ($product->featured_image) {
            $product->featured_image_info = $this->getFileInfoDynamically($product->featured_image);
        }
        
        return view('admin.products.edit', compact('product', 'categories'));
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
            'discount_price' => 'nullable|numeric|min:0|lt:price',
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

        // Handle featured image upload with enhanced functionality
        if ($request->hasFile('featured_image')) {
            // Delete old featured image and its thumbnail
            if ($product->featured_image) {
                $this->deleteFileDynamically($product->featured_image);
                
                // Try to delete thumbnail
                if (isset($product->featured_image_thumb)) {
                    $this->deleteFileDynamically($product->featured_image_thumb);
                }
            }
            
            $uploadOptions = [
                'max_size' => 2 * 1024 * 1024,
                'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
                'max_dimensions' => [2000, 2000]
            ];
            
            try {
                $uploadResult = $this->storeFileDynamically(
                    $request->file('featured_image'), 
                    'products', 
                    'products',
                    $uploadOptions
                );
                $data['featured_image'] = $uploadResult['file_path'];
                
                // Generate thumbnail
                $thumbnailPath = $this->generateThumbnailDynamically($uploadResult['file_path']);
                if ($thumbnailPath) {
                    $data['featured_image_thumb'] = $thumbnailPath;
                }
                
            } catch (\Exception $e) {
                return $this->handleError(
                    'Featured image upload failed: ' . $e->getMessage(),
                    'admin.products.edit',
                    ['id' => $product->id]
                );
            }
        }

        // Handle additional images with enhanced functionality
        $images = $product->images ?? [];
        if ($request->hasFile('images')) {
            $uploadOptions = [
                'max_size' => 2 * 1024 * 1024,
                'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
                'max_dimensions' => [2000, 2000]
            ];
            
            $uploadResults = $this->storeMultipleFilesDynamically(
                $request->file('images'),
                'products',
                'products',
                $uploadOptions
            );
            
            // Process successful uploads
            foreach ($uploadResults['results'] as $result) {
                if (isset($result['file_path'])) {
                    $images[] = $result['file_path'];
                    
                    // Generate thumbnail
                    $thumbnailPath = $this->generateThumbnailDynamically($result['file_path']);
                    if ($thumbnailPath) {
                        $images[] = $thumbnailPath;
                    }
                }
            }
            
            if ($uploadResults['error_count'] > 0) {
                \Log::warning('Some product images failed to upload during update', [
                    'product_id' => $product->id,
                    'errors' => $uploadResults['error_count']
                ]);
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

    public function destroy(Product $product)
    {
        $this->validateTenantOwnership($product);
        
        if ($product->orderItems()->count() > 0) {
            return $this->handleError(
                'Cannot delete product with order history!',
                'admin.products.index'
            );
        }

        // Enhanced file deletion - delete featured image and all additional images
        $filesToDelete = [];
        
        if ($product->featured_image) {
            $filesToDelete[] = $product->featured_image;
            
            // Add thumbnail if exists
            if (isset($product->featured_image_thumb)) {
                $filesToDelete[] = $product->featured_image_thumb;
            }
        }

        if ($product->images) {
            $filesToDelete = array_merge($filesToDelete, $product->images);
        }
        
        // Delete all files
        if (!empty($filesToDelete)) {
            $deleteResults = $this->deleteMultipleFilesDynamically($filesToDelete);
            
            if ($deleteResults['error_count'] > 0) {
                \Log::warning('Some product files failed to delete', [
                    'product_id' => $product->id,
                    'errors' => $deleteResults['error_count'],
                    'total' => $deleteResults['total']
                ]);
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
        $this->validateTenantOwnership($product);
        
        $product->update(['is_active' => !$product->is_active]);
        
        $status = $product->is_active ? 'activated' : 'deactivated';
        
        $this->logActivity("Product {$status}", $product, ['name' => $product->name]);
        
        // Clean the message to ensure no newline characters
        $message = trim("Product {$status} successfully!");
        
        return $this->handleSuccess($message, 'admin.products.index');
    }

    public function toggleFeatured(Product $product)
    {
        $this->validateTenantOwnership($product);
        
        $product->update(['is_featured' => !$product->is_featured]);
        
        $status = $product->is_featured ? 'marked as featured' : 'removed from featured';
        
        $this->logActivity("Product {$status}", $product, ['name' => $product->name]);
        
        // Clean the message to ensure no newline characters
        $message = trim("Product {$status} successfully!");
        
        return $this->handleSuccess($message, 'admin.products.index');
    }

    /**
     * Remove image from product (Enhanced version)
     */
    public function removeImage(Request $request, Product $product)
    {
        $this->validateTenantOwnership($product);
        
        $imageIndex = $request->input('image_index');
        $images = $product->images ?? [];
        
        if (isset($images[$imageIndex])) {
            $imageToDelete = $images[$imageIndex];
            
            // Delete the file
            $deleteSuccess = $this->deleteFileDynamically($imageToDelete);
            
            if ($deleteSuccess) {
                // Remove from array
                unset($images[$imageIndex]);
                $product->update(['images' => array_values($images)]);
                
                $this->logActivity('Product image removed', $product, [
                    'name' => $product->name,
                    'image_path' => $imageToDelete
                ]);
                
                return $this->handleSuccess('Image removed successfully!');
            } else {
                return $this->handleError('Failed to delete image file!');
            }
        }
        
        return $this->handleError('Image not found!');
    }

    /**
     * Export products to CSV (Enhanced version)
     */
    private function exportProducts($products)
    {
        $filename = 'products_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
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
                'Featured Image URL',
                'Additional Images Count',
                'Description',
                'Short Description',
                'Meta Title',
                'Meta Description',
                'Created At',
                'Updated At'
            ]);
            
            // Add product data
            foreach ($products as $product) {
                $featuredImageUrl = '';
                if ($product->featured_image) {
                    $featuredImageUrl = $this->getFileUrlDynamically($product->featured_image);
                }
                
                $additionalImagesCount = $product->images ? count($product->images) : 0;
                
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
                    $featuredImageUrl,
                    $additionalImagesCount,
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
     * Enhanced bulk actions for products
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,export',
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

                // Enhanced bulk file deletion
                $allFilesToDelete = [];
                
                foreach ($products as $product) {
                    if ($product->featured_image) {
                        $allFilesToDelete[] = $product->featured_image;
                        
                        if (isset($product->featured_image_thumb)) {
                            $allFilesToDelete[] = $product->featured_image_thumb;
                        }
                    }
                    
                    if ($product->images) {
                        $allFilesToDelete = array_merge($allFilesToDelete, $product->images);
                    }
                }
                
                // Delete all files
                if (!empty($allFilesToDelete)) {
                    $deleteResults = $this->deleteMultipleFilesDynamically($allFilesToDelete);
                    
                    if ($deleteResults['error_count'] > 0) {
                        \Log::warning('Some files failed to delete during bulk product deletion', [
                            'failed_files' => $deleteResults['error_count'],
                            'total_files' => $deleteResults['total']
                        ]);
                    }
                }

                Product::whereIn('id', $request->products)->delete();
                $message = "{$count} products deleted successfully!";
                break;
                
            case 'export':
                return $this->exportProducts($products);
                break;
        }

        $this->logActivity("Bulk action: {$request->action}", null, [
            'action' => $request->action,
            'count' => $count,
            'product_ids' => $request->products
        ]);

        return $this->handleSuccess($message);
    }
}
