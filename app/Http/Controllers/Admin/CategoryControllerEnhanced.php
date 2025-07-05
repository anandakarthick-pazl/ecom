<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\HasCompanyContextEnhanced;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryControllerEnhanced extends Controller
{
    use HasCompanyContextEnhanced;

    public function index(Request $request)
    {
        // Automatic company filtering with search
        $searchTerm = $request->get('search');
        $categories = $this->searchAndPaginateForCompany(
            Category::class,
            $searchTerm,
            ['name', 'description'], // Search fields
            20 // Per page
        );

        return view('admin.categories.index', compact('categories', 'searchTerm'));
    }

    public function create()
    {
        // Get parent categories for current company only
        $parentCategories = $this->getCompanyQuery(Category::class)
                                ->whereNull('parent_id')
                                ->orderBy('name')
                                ->get();
        
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        // Enhanced validation with company context
        $validated = $this->validateWithCompanyContext($request, [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'parent_id' => 'nullable|exists:categories,id', // This will be auto-scoped to company
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'integer|min:0'
        ]);

        // Handle boolean values properly
        $validated['is_active'] = $request->boolean('is_active');
        
        // Handle file upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        // Create with automatic company context
        $category = $this->createWithCompanyContext(Category::class, $validated);

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Category created successfully!');
    }

    public function show(Category $category)
    {
        // Automatic ownership validation
        $this->validateModelOwnership($category);
        
        $category->load(['products' => function ($query) {
            // Products are also automatically scoped to company
            $query->active()->take(10);
        }]);

        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        // Automatic ownership validation
        $this->validateModelOwnership($category);
        
        // Get parent categories for current company only
        $parentCategories = $this->getCompanyQuery(Category::class)
                                ->whereNull('parent_id')
                                ->where('id', '!=', $category->id)
                                ->orderBy('name')
                                ->get();
        
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        // Enhanced validation with company context
        $validated = $this->validateWithCompanyContext($request, [
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'parent_id' => 'nullable|exists:categories,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'integer|min:0'
        ]);

        // Handle boolean values properly
        $validated['is_active'] = $request->boolean('is_active');

        // Handle file upload
        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        // Update with automatic ownership validation
        $this->updateWithCompanyContext($category, $validated);

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        // Check for related products (automatically scoped to company)
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                           ->with('error', 'Cannot delete category with associated products!');
        }

        // Check for subcategories (automatically scoped to company)
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.index')
                           ->with('error', 'Cannot delete category with subcategories!');
        }

        // Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        // Delete with automatic ownership validation
        $this->deleteWithCompanyContext($category);

        return redirect()->route('admin.categories.index')
                        ->with('success', 'Category deleted successfully!');
    }

    public function toggleStatus(Category $category)
    {
        $this->updateWithCompanyContext($category, [
            'is_active' => !$category->is_active
        ]);
        
        $status = $category->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
                        ->with('success', "Category {$status} successfully!");
    }

    /**
     * API endpoint for getting categories (with automatic tenant scoping)
     */
    public function apiIndex(Request $request)
    {
        $query = $this->getCompanyQuery(Category::class);
        
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }
        
        if ($request->has('active_only')) {
            $query->active();
        }
        
        $categories = $query->orderBy('name')->get();
        
        return response()->json($categories);
    }
}
