<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\DynamicStorageWithLogging;
use App\Models\UploadLog;
use App\Models\User;

class BannerController extends Controller
{
    use DynamicStorageWithLogging;
    public function index()
    {
        $banners = Banner::orderBy('position')
                        ->orderBy('sort_order')
                        ->paginate(20);
        
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        // Get recent product and category uploads for reference
        $recentProductUploads = $this->getRecentProductUploads(10);
        $recentCategoryUploads = $this->getRecentCategoryUploads(10);
        $uploadStats = $this->getUploadStats();
        
        return view('admin.banners.create', compact(
            'recentProductUploads',
            'recentCategoryUploads', 
            'uploadStats'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_url' => 'nullable|url',
            'position' => 'required|in:top,middle,bottom',
            'is_active' => 'nullable',
            'sort_order' => 'integer|min:0',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'alt_text' => 'nullable|string|max:255'
        ]);

        $data = $request->all();
        
        // Handle checkbox value
        $data['is_active'] = $request->input('is_active', 0) == '1';
        // Store file in custom location for specific URL format
        $data['image'] = $this->storeBannerFile($request->file('image'));

        $banner = Banner::create($data);

        return redirect()->route('admin.banners.index')
                        ->with('success', 'Banner created successfully!');
    }
    
    /**
     * Store banner file in custom location
     * Path: public/storage/public/banner/banners/
     */
    private function storeBannerFile($file)
    {
        $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $destinationPath = public_path('storage/public/banner/banners/');
        
        // Create directory if it doesn't exist
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        
        // Move the file to the destination
        $file->move($destinationPath, $filename);
        
        return $filename;
    }
    
    /**
     * Delete banner file from custom location
     */
    private function deleteBannerFile($filename)
    {
        if ($filename) {
            $filePath = public_path('storage/public/banner/banners/' . $filename);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    public function show(Banner $banner)
    {
        return view('admin.banners.show', compact('banner'));
    }

    public function edit(Banner $banner)
    {
        // Get recent uploads and upload logs for this banner
        $recentProductUploads = $this->getRecentProductUploads(10);
        $recentCategoryUploads = $this->getRecentCategoryUploads(10);
        $bannerUploadLogs = $this->getUploadLogs('banner', $banner, 5);
        $uploadStats = $this->getUploadStats();
        
        return view('admin.banners.edit', compact(
            'banner',
            'recentProductUploads',
            'recentCategoryUploads',
            'bannerUploadLogs',
            'uploadStats'
        ));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_url' => 'nullable|url',
            'position' => 'required|in:top,middle,bottom',
            'is_active' => 'nullable',
            'sort_order' => 'integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'alt_text' => 'nullable|string|max:255'
        ]);

        $data = $request->all();
        
        // Handle checkbox value
        $data['is_active'] = $request->input('is_active', 0) == '1';

        if ($request->hasFile('image')) {
            if ($banner->image) {
                $this->deleteBannerFile($banner->image);
            }
            $data['image'] = $this->storeBannerFile($request->file('image'));
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')
                        ->with('success', 'Banner updated successfully!');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image) {
            $this->deleteBannerFile($banner->image);
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
     * Get upload logs for admin interface
     */
    public function uploadLogs(Request $request)
    {
        $uploadType = $request->get('type', null); // 'product', 'category', 'banner', or null for all
        $uploadLogs = $this->getUploadLogsPaginated($uploadType, 20);
        $uploadStats = $this->getUploadStats();
        
        return view('admin.banners.upload-logs', compact('uploadLogs', 'uploadStats', 'uploadType'));
    }

    /**
     * Use existing upload as banner image
     */
    public function useExistingUpload(Request $request)
    {
        $request->validate([
            'upload_log_id' => 'required|exists:upload_logs,id',
            'banner_id' => 'required|exists:banners,id'
        ]);

        $uploadLog = UploadLog::findOrFail($request->upload_log_id);
        $banner = Banner::findOrFail($request->banner_id);

        // Delete current banner image if exists
        if ($banner->image) {
            $this->deleteFileWithLogging($banner->image, $banner);
        }

        // Update banner with new image
        $banner->update([
            'image' => $uploadLog->file_path
        ]);

        // Create new upload log entry for banner usage
        UploadLog::create([
            'file_name' => basename($uploadLog->file_path),
            'original_name' => $uploadLog->original_name,
            'file_path' => $uploadLog->file_path,
            'file_size' => $uploadLog->file_size,
            'mime_type' => $uploadLog->mime_type,
            'storage_type' => $uploadLog->storage_type,
            'upload_type' => 'banner',
            'source_id' => $banner->id,
            'source_type' => get_class($banner),
            'uploaded_by' => auth()->id(),
            'meta_data' => [
                'action' => 'reused_from_' . $uploadLog->upload_type,
                'original_upload_id' => $uploadLog->id,
                'original_source_type' => $uploadLog->source_type,
                'original_source_id' => $uploadLog->source_id,
                'reused_at' => now(),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip()
            ]
        ]);

        return redirect()->back()->with('success', 'Banner image updated using existing upload!');
    }

    /**
     * Delete an upload log entry
     */
    public function deleteUploadLog(UploadLog $log)
    {
        try {
            // Optionally delete the actual file if it's not being used elsewhere
            // This is a safety check to prevent deleting files still in use
            $isFileInUse = $this->isFileInUse($log->file_path);
            
            if (!$isFileInUse) {
                $this->deleteFileFromStorage($log->file_path);
            }
            
            $log->delete();
            
            return redirect()->back()->with('success', 'Upload log deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete upload log: ' . $e->getMessage());
        }
    }
    
    /**
     * Check if a file is being used by any model
     */
    private function isFileInUse($filePath)
    {
        // Check if file is used in banners
        $bannerCount = Banner::where('image', $filePath)->count();
        
        // You can add more checks for other models that might use this file
        // $productCount = Product::where('featured_image', $filePath)->count();
        // $categoryCount = Category::where('image', $filePath)->count();
        
        return $bannerCount > 0; // || $productCount > 0 || $categoryCount > 0;
    }
    
    /**
     * Delete file from storage
     */
    private function deleteFileFromStorage($filePath)
    {
        // Handle different storage types
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        } else {
            // Handle custom banner storage location
            $fullPath = public_path('storage/public/banner/banners/' . basename($filePath));
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }
}
