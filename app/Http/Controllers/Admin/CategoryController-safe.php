<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends BaseAdminController
{
    public function index()
    {
        $categories = Category::with('parent')
                            ->orderBy('sort_order')
                            ->paginate(20);
        
        return view('admin.categories.index', compact('categories'));
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

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
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

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        $this->logActivity('Category updated', $category, ['name' => $category->name]);

        return $this->handleSuccess(
            'Category updated successfully!',
            'admin.categories.index'
        );
    }

    // ... rest of the methods remain the same
    
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

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
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
}
