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

        // SIMPLE WORKING UPLOAD - NO COMPLEX LOGIC
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // Store using Laravel's built-in method
            $path = $file->store('categories', 'public');
            
            // Store the clean path in database
            $data['image'] = $path;
            
            // Debug log
            \Log::info('Category image stored', [
                'path' => $path,
                'full_path' => storage_path('app/public/' . $path),
                'exists' => file_exists(storage_path('app/public/' . $path)),
                'url' => asset('storage/' . $path)
            ]);
        }

        // Create with tenant scope (company_id is automatically added via trait)
        $category = Category::create($data);

        $this->logActivity('Category created', $category, ['name' => $category->name]);

        return $this->handleSuccess(
            'Category created successfully!',
            'admin.categories.index'
        );
    }
