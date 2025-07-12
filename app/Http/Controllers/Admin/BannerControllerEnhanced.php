<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\EnhancedDynamicStorage;
use App\Traits\HasPagination;

class BannerControllerEnhanced extends Controller
{
    use EnhancedDynamicStorage, HasPagination;
    
    public function index(Request $request)
    {
        $query = Banner::orderBy('position')
                      ->orderBy('sort_order');
        
        // Apply search filter if provided
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'LIKE', "%{$request->search}%")
                  ->orWhere('alt_text', 'LIKE', "%{$request->search}%");
            });
        }
        
        // Filter by position
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->boolean('status'));
        }
        
        // Get paginated results
        $banners = $this->applyAdminPagination($query, $request, '20');
        
        // Get pagination controls data for the view
        $paginationControls = $this->getPaginationControlsData($request, 'admin');
        
        return view('admin.banners.index', compact('banners', 'paginationControls'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB for banners
            'link_url' => 'nullable|url',
            'position' => 'required|in:top,middle,bottom,sidebar,hero',
            'is_active' => 'nullable',
            'sort_order' => 'integer|min:0',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'alt_text' => 'nullable|string|max:255',
            'target' => 'nullable|in:_self,_blank',
            'button_text' => 'nullable|string|max:50',
            'button_color' => 'nullable|string|max:7'
        ]);

        $data = $request->all();
        
        // Handle checkbox value
        $data['is_active'] = $request->input('is_active', 0) == '1';
        
        // Enhanced image upload handling for banners
        $uploadOptions = [
            'max_size' => 5 * 1024 * 1024, // 5MB for banners
            'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
            'max_dimensions' => [3000, 2000] // Large dimensions for banners
        ];
        
        try {
            $uploadResult = $this->storeFileDynamically(
                $request->file('image'), 
                'banners', 
                'banners',
                $uploadOptions
            );
            $data['image'] = $uploadResult['file_path'];
            
            // Generate different sized thumbnails for different positions
            $thumbnailDimensions = $this->getBannerThumbnailDimensions($data['position']);
            
            foreach ($thumbnailDimensions as $size => $dimensions) {
                $thumbnailPath = $this->generateThumbnailDynamically(
                    $uploadResult['file_path'], 
                    $dimensions
                );
                if ($thumbnailPath) {
                    $data["image_thumb_{$size}"] = $thumbnailPath;
                }
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->withErrors(['image' => 'Banner image upload failed: ' . $e->getMessage()]);
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')
                        ->with('success', 'Banner created successfully!');
    }

    public function show(Banner $banner)
    {
        // Get enhanced file information
        if ($banner->image) {
            $banner->image_info = $this->getFileInfoDynamically($banner->image);
        }
        
        return view('admin.banners.show', compact('banner'));
    }

    public function edit(Banner $banner)
    {
        // Get file information for editing
        if ($banner->image) {
            $banner->image_info = $this->getFileInfoDynamically($banner->image);
        }
        
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'link_url' => 'nullable|url',
            'position' => 'required|in:top,middle,bottom,sidebar,hero',
            'is_active' => 'nullable',
            'sort_order' => 'integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'alt_text' => 'nullable|string|max:255',
            'target' => 'nullable|in:_self,_blank',
            'button_text' => 'nullable|string|max:50',
            'button_color' => 'nullable|string|max:7'
        ]);

        $data = $request->all();
        
        // Handle checkbox value
        $data['is_active'] = $request->input('is_active', 0) == '1';

        // Enhanced image upload handling
        if ($request->hasFile('image')) {
            // Delete old image and all its thumbnails
            if ($banner->image) {
                $filesToDelete = [$banner->image];
                
                // Add all thumbnail variants
                $thumbnailSizes = ['small', 'medium', 'large'];
                foreach ($thumbnailSizes as $size) {
                    $thumbField = "image_thumb_{$size}";
                    if (isset($banner->$thumbField)) {
                        $filesToDelete[] = $banner->$thumbField;
                    }
                }
                
                $this->deleteMultipleFilesDynamically($filesToDelete);
            }
            
            $uploadOptions = [
                'max_size' => 5 * 1024 * 1024,
                'allowed_types' => ['jpg', 'jpeg', 'png', 'webp'],
                'max_dimensions' => [3000, 2000]
            ];
            
            try {
                $uploadResult = $this->storeFileDynamically(
                    $request->file('image'), 
                    'banners', 
                    'banners',
                    $uploadOptions
                );
                $data['image'] = $uploadResult['file_path'];
                
                // Generate new thumbnails
                $thumbnailDimensions = $this->getBannerThumbnailDimensions($data['position']);
                
                foreach ($thumbnailDimensions as $size => $dimensions) {
                    $thumbnailPath = $this->generateThumbnailDynamically(
                        $uploadResult['file_path'], 
                        $dimensions
                    );
                    if ($thumbnailPath) {
                        $data["image_thumb_{$size}"] = $thumbnailPath;
                    }
                }
                
            } catch (\Exception $e) {
                return redirect()->back()
                               ->withInput()
                               ->withErrors(['image' => 'Banner image upload failed: ' . $e->getMessage()]);
            }
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')
                        ->with('success', 'Banner updated successfully!');
    }

    public function destroy(Banner $banner)
    {
        // Enhanced file deletion - delete banner image and all thumbnails
        $filesToDelete = [];
        
        if ($banner->image) {
            $filesToDelete[] = $banner->image;
            
            // Add all thumbnail variants
            $thumbnailSizes = ['small', 'medium', 'large'];
            foreach ($thumbnailSizes as $size) {
                $thumbField = "image_thumb_{$size}";
                if (isset($banner->$thumbField)) {
                    $filesToDelete[] = $banner->$thumbField;
                }
            }
        }
        
        if (!empty($filesToDelete)) {
            $deleteResults = $this->deleteMultipleFilesDynamically($filesToDelete);
            
            if ($deleteResults['error_count'] > 0) {
                \Log::warning('Some banner files failed to delete', [
                    'banner_id' => $banner->id,
                    'errors' => $deleteResults['error_count']
                ]);
            }
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
                        ->with('success', 'Banner deleted successfully!');
    }

    public function toggleStatus(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);
        
        $status = $banner->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Banner {$status} successfully!");
    }

    /**
     * Enhanced bulk actions for banners
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'banners' => 'required|array',
            'banners.*' => 'exists:banners,id'
        ]);

        $banners = Banner::whereIn('id', $request->banners)->get();
        $count = $banners->count();

        switch ($request->action) {
            case 'activate':
                Banner::whereIn('id', $request->banners)->update(['is_active' => true]);
                $message = "{$count} banners activated successfully!";
                break;
                
            case 'deactivate':
                Banner::whereIn('id', $request->banners)->update(['is_active' => false]);
                $message = "{$count} banners deactivated successfully!";
                break;
                
            case 'delete':
                // Enhanced bulk file deletion
                $allFilesToDelete = [];
                
                foreach ($banners as $banner) {
                    if ($banner->image) {
                        $allFilesToDelete[] = $banner->image;
                        
                        // Add all thumbnail variants
                        $thumbnailSizes = ['small', 'medium', 'large'];
                        foreach ($thumbnailSizes as $size) {
                            $thumbField = "image_thumb_{$size}";
                            if (isset($banner->$thumbField)) {
                                $allFilesToDelete[] = $banner->$thumbField;
                            }
                        }
                    }
                }
                
                if (!empty($allFilesToDelete)) {
                    $deleteResults = $this->deleteMultipleFilesDynamically($allFilesToDelete);
                    
                    if ($deleteResults['error_count'] > 0) {
                        \Log::warning('Some files failed to delete during bulk banner deletion', [
                            'failed_files' => $deleteResults['error_count'],
                            'total_files' => $deleteResults['total']
                        ]);
                    }
                }

                Banner::whereIn('id', $request->banners)->delete();
                $message = "{$count} banners deleted successfully!";
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get banner thumbnail dimensions based on position
     */
    private function getBannerThumbnailDimensions($position)
    {
        $dimensions = [
            'hero' => [
                'small' => [400, 200],
                'medium' => [800, 400],
                'large' => [1200, 600]
            ],
            'top' => [
                'small' => [300, 100],
                'medium' => [600, 200],
                'large' => [1200, 400]
            ],
            'middle' => [
                'small' => [300, 150],
                'medium' => [600, 300],
                'large' => [1000, 500]
            ],
            'bottom' => [
                'small' => [300, 100],
                'medium' => [600, 200],
                'large' => [1200, 400]
            ],
            'sidebar' => [
                'small' => [150, 200],
                'medium' => [300, 400],
                'large' => [400, 600]
            ]
        ];

        return $dimensions[$position] ?? $dimensions['middle'];
    }

    /**
     * Get storage statistics for banners
     */
    public function getStorageStats()
    {
        $stats = $this->getDirectoryStatsDynamically('banners');
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Clean up unused banner files
     */
    public function cleanupFiles()
    {
        $cleanupResults = $this->cleanupOldFilesDynamically('banners', 30);
        
        return response()->json([
            'success' => true,
            'cleanup_results' => $cleanupResults
        ]);
    }

    /**
     * Duplicate banner
     */
    public function duplicate(Banner $banner)
    {
        $newBanner = $banner->replicate();
        $newBanner->title = $banner->title . ' (Copy)';
        $newBanner->is_active = false;
        $newBanner->sort_order = $banner->sort_order + 1;
        
        // If the banner has an image, we should copy it
        if ($banner->image) {
            try {
                $originalPath = storage_path('app/public/' . $banner->image);
                
                if (file_exists($originalPath)) {
                    $fileInfo = pathinfo($banner->image);
                    $newFilename = time() . '_copy_' . $fileInfo['basename'];
                    $newPath = $fileInfo['dirname'] . '/' . $newFilename;
                    
                    copy($originalPath, storage_path('app/public/' . $newPath));
                    $newBanner->image = $newPath;
                    
                    // Copy thumbnails if they exist
                    $thumbnailSizes = ['small', 'medium', 'large'];
                    foreach ($thumbnailSizes as $size) {
                        $thumbField = "image_thumb_{$size}";
                        if (isset($banner->$thumbField)) {
                            $thumbOriginalPath = storage_path('app/public/' . $banner->$thumbField);
                            if (file_exists($thumbOriginalPath)) {
                                $thumbFileInfo = pathinfo($banner->$thumbField);
                                $newThumbFilename = time() . '_copy_' . $thumbFileInfo['basename'];
                                $newThumbPath = $thumbFileInfo['dirname'] . '/' . $newThumbFilename;
                                
                                copy($thumbOriginalPath, storage_path('app/public/' . $newThumbPath));
                                $newBanner->$thumbField = $newThumbPath;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to copy banner image during duplication: ' . $e->getMessage());
            }
        }
        
        $newBanner->save();
        
        return redirect()->route('admin.banners.index')
                        ->with('success', 'Banner duplicated successfully!');
    }

    /**
     * Update banner order via AJAX
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'banners' => 'required|array',
            'banners.*.id' => 'required|exists:banners,id',
            'banners.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->banners as $bannerData) {
            Banner::where('id', $bannerData['id'])
                  ->update(['sort_order' => $bannerData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Banner order updated successfully!'
        ]);
    }

    /**
     * Get banners by position for frontend
     */
    public function getByPosition($position)
    {
        $banners = Banner::where('position', $position)
                        ->where('is_active', true)
                        ->where(function($query) {
                            $query->whereNull('start_date')
                                  ->orWhere('start_date', '<=', now());
                        })
                        ->where(function($query) {
                            $query->whereNull('end_date')
                                  ->orWhere('end_date', '>=', now());
                        })
                        ->orderBy('sort_order')
                        ->get();
        
        // Add image URLs
        foreach ($banners as $banner) {
            if ($banner->image) {
                $banner->image_url = $this->getFileUrlDynamically($banner->image);
                
                // Add thumbnail URLs
                $thumbnailSizes = ['small', 'medium', 'large'];
                foreach ($thumbnailSizes as $size) {
                    $thumbField = "image_thumb_{$size}";
                    if (isset($banner->$thumbField)) {
                        $banner->{$thumbField . '_url'} = $this->getFileUrlDynamically($banner->$thumbField);
                    }
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'banners' => $banners
        ]);
    }
}
