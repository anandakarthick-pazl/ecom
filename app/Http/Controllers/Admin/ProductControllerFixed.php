<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\DynamicStorage;
use App\Traits\HasPagination;
use Illuminate\Support\Facades\Log;

class ProductController extends BaseAdminController
{
    use DynamicStorage, HasPagination;
    
    // ... keep existing index, create, show, edit methods ...

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

    // ... keep all other existing methods (destroy, toggleStatus, etc.)
}
