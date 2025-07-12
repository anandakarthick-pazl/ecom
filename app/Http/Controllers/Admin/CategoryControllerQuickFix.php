<?php

// Quick Fix for Category Image Upload Issues
// Add this to your CategoryController.php or create a new fixed version

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\DynamicStorage;
use App\Traits\HasPagination;
use Illuminate\Support\Facades\Log;

class CategoryControllerQuickFix extends BaseAdminController
{
    use DynamicStorage, HasPagination;

    // ... (keep all existing methods, but update store and update methods)

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                $this->getTenantUniqueRule('categories', 'name')
            ],
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'parent_id' => [
                'nullable',
                $this->getTenantExistsRule('categories')
            ],
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_active' => 'nullable',
            'sort_order' => 'integer|min:0'
        ]);

        $data = $request->all();
        
        // Handle checkbox value
        $data['is_active'] = $request->input('is_active', 0) == '1';

        // FIXED IMAGE UPLOAD LOGIC
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                
                // Method 1: Try enhanced upload if available
                if (method_exists($this, 'storeFileDynamically')) {
                    $uploadResult = $this->storeFileDynamically($file, 'categories', 'categories');
                    $imagePath = $uploadResult['file_path'];
                } else {
                    // Method 2: Fallback to direct Laravel upload
                    $imagePath = $file->store('categories', 'public');
                }
                
                // Ensure the path is clean (no 'public/' prefix for storage)
                $imagePath = str_replace('public/', '', $imagePath);
                
                $data['image'] = $imagePath;
                
                // Log successful upload
                Log::info('Category image uploaded successfully', [
                    'original_name' => $file->getClientOriginalName(),
                    'stored_path' => $imagePath,
                    'full_path' => storage_path('app/public/' . $imagePath),
                    'file_exists' => file_exists(storage_path('app/public/' . $imagePath))
                ]);
                
            } catch (\Exception $e) {
                Log::error('Category image upload failed', [
                    'error' => $e->getMessage(),
                    'file_name' => $request->file('image')->getClientOriginalName()
                ]);
                
                return $this->handleError(
                    'Image upload failed: ' . $e->getMessage(),
                    'admin.categories.create'
                );
            }
        }

        // Create with tenant scope
        $category = Category::create($data);

        // Log the created category details
        Log::info('Category created', [
            'id' => $category->id,
            'name' => $category->name,
            'image_path' => $category->image,
            'image_url' => $category->image ? asset('storage/' . $category->image) : null
        ]);

        $this->logActivity('Category created', $category, ['name' => $category->name]);

        return $this->handleSuccess(
            'Category created successfully!',
            'admin.categories.index'
        );
    }

    public function update(Request $request, Category $category)
    {
        $this->validateTenantOwnership($category);
        
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                $this->getTenantUniqueRule('categories', 'name', $category->id)
            ],
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'parent_id' => [
                'nullable',
                $this->getTenantExistsRule('categories')
            ],
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_active' => 'nullable',
            'sort_order' => 'integer|min:0'
        ]);

        $data = $request->all();
        
        // Handle checkbox value
        $data['is_active'] = $request->input('is_active', 0) == '1';

        // FIXED IMAGE UPDATE LOGIC
        if ($request->hasFile('image')) {
            try {
                // Delete old image if exists
                if ($category->image) {
                    $oldImagePath = str_replace('public/', '', $category->image);
                    $fullOldPath = storage_path('app/public/' . $oldImagePath);
                    
                    if (file_exists($fullOldPath)) {
                        unlink($fullOldPath);
                        Log::info('Old category image deleted', ['path' => $fullOldPath]);
                    }
                }
                
                $file = $request->file('image');
                
                // Upload new image
                if (method_exists($this, 'storeFileDynamically')) {
                    $uploadResult = $this->storeFileDynamically($file, 'categories', 'categories');
                    $imagePath = $uploadResult['file_path'];
                } else {
                    $imagePath = $file->store('categories', 'public');
                }
                
                // Clean the path
                $imagePath = str_replace('public/', '', $imagePath);
                
                $data['image'] = $imagePath;
                
                Log::info('Category image updated successfully', [
                    'category_id' => $category->id,
                    'old_path' => $category->image,
                    'new_path' => $imagePath
                ]);
                
            } catch (\Exception $e) {
                Log::error('Category image update failed', [
                    'category_id' => $category->id,
                    'error' => $e->getMessage()
                ]);
                
                return $this->handleError(
                    'Image upload failed: ' . $e->getMessage(),
                    'admin.categories.edit',
                    ['category' => $category->id]
                );
            }
        }

        $category->update($data);

        $this->logActivity('Category updated', $category, ['name' => $category->name]);

        return $this->handleSuccess(
            'Category updated successfully!',
            'admin.categories.index'
        );
    }

    /**
     * Debug method to check category image
     */
    public function debugImage(Category $category)
    {
        $this->validateTenantOwnership($category);
        
        $debugInfo = [
            'category_id' => $category->id,
            'category_name' => $category->name,
            'stored_image_path' => $category->image,
            'clean_path' => $category->image ? str_replace('public/', '', $category->image) : null,
            'full_filesystem_path' => $category->image ? storage_path('app/public/' . str_replace('public/', '', $category->image)) : null,
            'file_exists' => $category->image ? file_exists(storage_path('app/public/' . str_replace('public/', '', $category->image))) : false,
            'expected_url' => $category->image ? asset('storage/' . str_replace('public/', '', $category->image)) : null,
            'storage_link_exists' => is_link(public_path('storage'))
        ];
        
        return response()->json($debugInfo);
    }
}
