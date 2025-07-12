<?php
// Add this to your routes/web.php file temporarily

Route::get('/debug-category-upload', function() {
    // Get the latest category
    $latestCategory = \App\Models\Category::latest()->first();
    
    if (!$latestCategory) {
        return 'No categories found';
    }
    
    $imagePath = $latestCategory->image;
    $cleanPath = str_replace('public/', '', $imagePath);
    $fullPath = storage_path('app/public/' . $cleanPath);
    
    return [
        'category_id' => $latestCategory->id,
        'category_name' => $latestCategory->name,
        'stored_image_path' => $imagePath,
        'clean_path' => $cleanPath,
        'full_filesystem_path' => $fullPath,
        'file_exists' => file_exists($fullPath),
        'file_size' => file_exists($fullPath) ? filesize($fullPath) : 0,
        'expected_url' => asset('storage/' . $cleanPath),
        'model_image_url' => $latestCategory->image_url,
        'storage_link_exists' => is_link(public_path('storage')),
        'storage_link_target' => is_link(public_path('storage')) ? readlink(public_path('storage')) : null,
        'categories_dir_exists' => is_dir(storage_path('app/public/categories')),
        'categories_dir_files' => is_dir(storage_path('app/public/categories')) ? 
            array_slice(scandir(storage_path('app/public/categories')), 2) : []
    ];
});
