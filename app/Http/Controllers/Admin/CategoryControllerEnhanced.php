<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\EnhancedDynamicStorage;
use App\Traits\HasPagination;

class CategoryControllerEnhanced extends BaseAdminController
{
    use EnhancedDynamicStorage, HasPagination;
    
    public function index(Request $request)
    {
        $query = Category::with('parent')
                        ->orderBy('sort_order');
                        
        // Apply search filter if provided
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('description', 'LIKE', "%{$request->search}%");
            });
        }
        
        // Apply tenant scope
        $query = $this->applyTenantScope($query);
        
        // Get paginated results using dynamic pagination settings
        $categories = $this->applyAdminPagination($query, $request, '20');
        
        // Get pagination controls data for the view
        $paginationControls = $this->getPaginationControlsData($request, 'admin');
        
        return view('admin.categories.index', compact('categories', 'paginationControls'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')
                                  ->orderBy('name')
                                  ->get();
        
        return view('admin.categories.create', compact('parentCategories'));
    }

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

        // Enhanced image upload handling
        if ($request->hasFile('image')) {
            $uploadOptions = [
                'max_size' => 2 * 1024 * 1024, // 2MB
                'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
                'max_dimensions' => [1500, 1500] // Max 1500x1500px for categories
            ];
            
            try {
                $uploadResult = $this->storeFileDynamically(
                    $request->file('image'), 
                    'categories', 
                    'categories',
                    $uploadOptions
                );
                $data['image'] = $uploadResult['file_path'];
                
                // Generate thumbnail for category image
                $thumbnailPath = $this->generateThumbnailDynamically($uploadResult['file_path'], [300, 300]);
                if ($thumbnailPath) {
                    $data['image_thumb'] = $thumbnailPath;
                }
                
            } catch (\Exception $e) {
                return $this->handleError(
                    'Category image upload failed: ' . $e->getMessage(),
                    'admin.categories.create'
                );
            }
        }

        // Create with tenant scope (company_id is automatically added via trait)
        $category = Category::create($data);

        $this->logActivity('Category created', $category, ['name' => $category->name]);

        return $this->handleSuccess(
            'Category created successfully!',
            'admin.categories.index'
        );
    }

    public function show(Category $category)
    {
        $this->validateTenantOwnership($category);
        $category->load('products');
        
        // Get enhanced file information
        if ($category->image) {
            $category->image_info = $this->getFileInfoDynamically($category->image);
        }
        
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $this->validateTenantOwnership($category);
        
        $parentCategories = Category::whereNull('parent_id')
                                  ->where('id', '!=', $category->id)
                                  ->orderBy('name')
                                  ->get();
        
        // Get file information for editing
        if ($category->image) {
            $category->image_info = $this->getFileInfoDynamically($category->image);
        }
        
        return view('admin.categories.edit', compact('category', 'parentCategories'));
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

        // Enhanced image upload handling
        if ($request->hasFile('image')) {
            // Delete old image and thumbnail
            if ($category->image) {
                $this->deleteFileDynamically($category->image);
                
                if (isset($category->image_thumb)) {
                    $this->deleteFileDynamically($category->image_thumb);
                }
            }
            
            $uploadOptions = [
                'max_size' => 2 * 1024 * 1024,
                'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
                'max_dimensions' => [1500, 1500]
            ];
            
            try {
                $uploadResult = $this->storeFileDynamically(
                    $request->file('image'), 
                    'categories', 
                    'categories',
                    $uploadOptions
                );
                $data['image'] = $uploadResult['file_path'];
                
                // Generate new thumbnail
                $thumbnailPath = $this->generateThumbnailDynamically($uploadResult['file_path'], [300, 300]);
                if ($thumbnailPath) {
                    $data['image_thumb'] = $thumbnailPath;
                }
                
            } catch (\Exception $e) {
                return $this->handleError(
                    'Category image upload failed: ' . $e->getMessage(),
                    'admin.categories.edit',
                    ['id' => $category->id]
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

    public function destroy(Category $category)
    {
        $this->validateTenantOwnership($category);
        
        if ($category->products()->count() > 0) {
            return $this->handleError(
                'Cannot delete category with associated products!',
                'admin.categories.index'
            );
        }

        if ($category->children()->count() > 0) {
            return $this->handleError(
                'Cannot delete category with subcategories!',
                'admin.categories.index'
            );
        }

        // Enhanced file deletion
        $filesToDelete = [];
        
        if ($category->image) {
            $filesToDelete[] = $category->image;
            
            if (isset($category->image_thumb)) {
                $filesToDelete[] = $category->image_thumb;
            }
        }
        
        if (!empty($filesToDelete)) {
            $deleteResults = $this->deleteMultipleFilesDynamically($filesToDelete);
            
            if ($deleteResults['error_count'] > 0) {
                \Log::warning('Some category files failed to delete', [
                    'category_id' => $category->id,
                    'errors' => $deleteResults['error_count']
                ]);
            }
        }

        $categoryName = $category->name;
        $category->delete();

        $this->logActivity('Category deleted', null, ['name' => $categoryName]);

        return $this->handleSuccess(
            'Category deleted successfully!',
            'admin.categories.index'
        );
    }

    public function toggleStatus(Category $category)
    {
        $this->validateTenantOwnership($category);
        
        $category->update(['is_active' => !$category->is_active]);
        
        $status = $category->is_active ? 'activated' : 'deactivated';
        
        $this->logActivity("Category {$status}", $category, ['name' => $category->name]);
        
        return $this->handleSuccess("Category {$status} successfully!");
    }

    /**
     * Get category tree for current tenant
     */
    public function getTree()
    {
        $categories = Category::with('children')
                            ->whereNull('parent_id')
                            ->orderBy('sort_order')
                            ->get();
        
        return response()->json($categories);
    }

    /**
     * Update category order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->categories as $categoryData) {
            $category = Category::find($categoryData['id']);
            $this->validateTenantOwnership($category);
            
            $category->update(['sort_order' => $categoryData['sort_order']]);
        }

        $this->logActivity('Category order updated', null, [
            'categories_count' => count($request->categories)
        ]);

        return $this->handleSuccess('Category order updated successfully!');
    }

    /**
     * Enhanced bulk actions for categories
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id'
        ]);

        $categories = Category::whereIn('id', $request->categories)->get();
        
        // Validate tenant ownership for all categories
        foreach ($categories as $category) {
            $this->validateTenantOwnership($category);
        }

        $count = $categories->count();

        switch ($request->action) {
            case 'activate':
                Category::whereIn('id', $request->categories)->update(['is_active' => true]);
                $message = "{$count} categories activated successfully!";
                break;
                
            case 'deactivate':
                Category::whereIn('id', $request->categories)->update(['is_active' => false]);
                $message = "{$count} categories deactivated successfully!";
                break;
                
            case 'delete':
                // Check if any category has products or children
                $hasProducts = $categories->filter(function ($category) {
                    return $category->products()->count() > 0 || $category->children()->count() > 0;
                });

                if ($hasProducts->count() > 0) {
                    return $this->handleError('Cannot delete categories with products or subcategories!');
                }

                // Enhanced bulk file deletion
                $allFilesToDelete = [];
                
                foreach ($categories as $category) {
                    if ($category->image) {
                        $allFilesToDelete[] = $category->image;
                        
                        if (isset($category->image_thumb)) {
                            $allFilesToDelete[] = $category->image_thumb;
                        }
                    }
                }
                
                if (!empty($allFilesToDelete)) {
                    $deleteResults = $this->deleteMultipleFilesDynamically($allFilesToDelete);
                    
                    if ($deleteResults['error_count'] > 0) {
                        \Log::warning('Some files failed to delete during bulk category deletion', [
                            'failed_files' => $deleteResults['error_count'],
                            'total_files' => $deleteResults['total']
                        ]);
                    }
                }

                Category::whereIn('id', $request->categories)->delete();
                $message = "{$count} categories deleted successfully!";
                break;
        }

        $this->logActivity("Bulk action: {$request->action}", null, [
            'action' => $request->action,
            'count' => $count,
            'category_ids' => $request->categories
        ]);

        return $this->handleSuccess($message);
    }

    /**
     * Get storage statistics for categories
     */
    public function getStorageStats()
    {
        $stats = $this->getDirectoryStatsDynamically('categories');
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Clean up unused category files
     */
    public function cleanupFiles()
    {
        $cleanupResults = $this->cleanupOldFilesDynamically('categories', 30);
        
        return response()->json([
            'success' => true,
            'cleanup_results' => $cleanupResults
        ]);
    }
}
