<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\DynamicStorage;
use App\Traits\HasPagination;
use Illuminate\Support\Facades\Log;

class CategoryController extends BaseAdminController
{
    use DynamicStorage, HasPagination;
    
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
        
        // Get results and ensure we always have a paginator
        try {
            $result = $this->applyAdminPagination($query, $request, '20');
            $categories = $this->ensurePaginator($result, $request, 20);
        } catch (\Exception $e) {
            // Fallback to simple pagination
            $categories = $query->paginate(20);
        }
        
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

        // FIXED IMAGE UPLOAD - SIMPLE DIRECT APPROACH
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                
                // Generate unique filename
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Define the target directory (directly under storage/app/public/categories)
                $targetDir = storage_path('app/public/categories');
               
                // Create directory if it doesn't exist
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                
                // Move file directly to target location
                $targetPath = $targetDir . '/' . $filename;
                
                if ($file->move($targetDir, $filename)) {
                    // Store path relative to storage/app/public (just categories/filename.jpg)
                    $data['image'] = 'categories/' . $filename;
                    
                    Log::info('Category image uploaded successfully', [
                        'filename' => $filename,
                        'stored_path' => $data['image'],
                        'full_path' => $targetPath,
                        'file_exists' => file_exists($targetPath),
                        'url' => asset('storage/categories/' . $filename)
                    ]);
                } else {
                    throw new \Exception('Failed to move uploaded file');
                }
                
            } catch (\Exception $e) {
                Log::error('Category image upload failed', [
                    'error' => $e->getMessage(),
                    'file' => $request->file('image')->getClientOriginalName()
                ]);
                
                return $this->handleError(
                    'Image upload failed: ' . $e->getMessage(),
                    'admin.categories.create'
                );
            }
        }

        // Create with tenant scope (company_id is automatically added via trait)
        $category = Category::create($data);

        Log::info('Category created', [
            'id' => $category->id,
            'name' => $category->name,
            'image_path' => $category->image,
            'expected_url' => $category->image ? asset('storage/' . $category->image) : null
        ]);

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
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $this->validateTenantOwnership($category);
        
        $parentCategories = Category::whereNull('parent_id')
                                  ->where('id', '!=', $category->id)
                                  ->orderBy('name')
                                  ->get();
        
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

        // FIXED IMAGE UPDATE - SIMPLE DIRECT APPROACH
        if ($request->hasFile('image')) {
            try {
                // Delete old image if exists
                if ($category->image) {
                    $oldImagePath = storage_path('app/public/' . $category->image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                        Log::info('Old category image deleted', ['path' => $oldImagePath]);
                    }
                }
                
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $targetDir = storage_path('app/public/categories');
              
                // Create directory if it doesn't exist
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                
                $targetPath = $targetDir . '/' . $filename;
                
                if ($file->move($targetDir, $filename)) {
                    $data['image'] = 'categories/' . $filename;
                    
                    Log::info('Category image updated successfully', [
                        'category_id' => $category->id,
                        'filename' => $filename,
                        'stored_path' => $data['image'],
                        'full_path' => $targetPath
                    ]);
                } else {
                    throw new \Exception('Failed to move uploaded file');
                }
                
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

        // Delete image file
        if ($category->image) {
            $imagePath = storage_path('app/public/' . $category->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
                Log::info('Category image deleted', ['path' => $imagePath]);
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
     * Bulk actions for categories
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

                // Delete images and categories
                foreach ($categories as $category) {
                    if ($category->image) {
                        $imagePath = storage_path('app/public/' . $category->image);
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
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
}
