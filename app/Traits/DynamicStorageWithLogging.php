<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\StorageManagementService;
use App\Models\UploadLog;

trait DynamicStorageWithLogging
{
    use DynamicStorage;

    /**
     * Store file using dynamic storage with logging
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param string $uploadType
     * @param mixed $sourceModel
     * @param array $metaData
     * @return array
     */
    protected function storeFileWithLogging($file, $directory = 'general', $uploadType = 'general', $sourceModel = null, $metaData = [])
    {
        $storageService = app(StorageManagementService::class);
        $uploadResult = $storageService->uploadFile($file, null, $directory, $uploadType);
        
        // Create upload log entry
        $this->logFileUpload($file, $uploadResult, $uploadType, $sourceModel, $metaData);
        
        return $uploadResult;
    }

    /**
     * Log file upload to database
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @param array $uploadResult
     * @param string $uploadType
     * @param mixed $sourceModel
     * @param array $metaData
     * @return UploadLog
     */
    protected function logFileUpload($file, $uploadResult, $uploadType = 'general', $sourceModel = null, $metaData = [])
    {
        return UploadLog::create([
            'file_name' => basename($uploadResult['file_path']),
            'original_name' => $file->getClientOriginalName(),
            'file_path' => $uploadResult['file_path'],
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'storage_type' => $this->getCurrentStorageType(),
            'upload_type' => $uploadType,
            'source_id' => $sourceModel ? $sourceModel->id : null,
            'source_type' => $sourceModel ? get_class($sourceModel) : null,
            'uploaded_by' => Auth::id(),
            'meta_data' => array_merge($metaData, [
                'uploaded_at' => now(),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
                'upload_context' => $this->getUploadContext()
            ])
        ]);
    }

    /**
     * Get upload logs for a specific type and optionally source
     * 
     * @param string $uploadType
     * @param mixed $sourceModel
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getUploadLogs($uploadType, $sourceModel = null, $limit = 50)
    {
        $query = UploadLog::where('upload_type', $uploadType)
                          ->with('uploader')
                          ->orderBy('created_at', 'desc')
                          ->limit($limit);

        if ($sourceModel) {
            $query->where('source_id', $sourceModel->id)
                  ->where('source_type', get_class($sourceModel));
        }

        return $query->get();
    }

    /**
     * Get recent product uploads
     * 
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getRecentProductUploads($limit = 20)
    {
        return $this->getUploadLogs('product', null, $limit);
    }

    /**
     * Get recent category uploads
     * 
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getRecentCategoryUploads($limit = 20)
    {
        return $this->getUploadLogs('category', null, $limit);
    }

    /**
     * Get upload statistics
     * 
     * @return array
     */
    protected function getUploadStats()
    {
        $baseQuery = UploadLog::query();
        
        // Apply tenant scope if available
        if (method_exists($this, 'applyTenantScope')) {
            $baseQuery = $this->applyTenantScope($baseQuery);
        }

        return [
            'total_uploads' => $baseQuery->count(),
            'product_uploads' => $baseQuery->clone()->where('upload_type', 'product')->count(),
            'category_uploads' => $baseQuery->clone()->where('upload_type', 'category')->count(),
            'banner_uploads' => $baseQuery->clone()->where('upload_type', 'banner')->count(),
            'total_size' => $baseQuery->sum('file_size'),
            'today_uploads' => $baseQuery->clone()->whereDate('created_at', today())->count(),
            'this_week_uploads' => $baseQuery->clone()->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count()
        ];
    }

    /**
     * Delete file with logging cleanup
     * 
     * @param string $filePath
     * @param mixed $sourceModel
     * @return bool
     */
    protected function deleteFileWithLogging($filePath, $sourceModel = null)
    {
        // Delete the actual file
        $deleted = $this->deleteFileDynamically($filePath);
        
        if ($deleted) {
            // Clean up upload logs
            $query = UploadLog::where('file_path', $filePath);
            
            if ($sourceModel) {
                $query->where('source_id', $sourceModel->id)
                      ->where('source_type', get_class($sourceModel));
            }
            
            $query->delete();
        }
        
        return $deleted;
    }

    /**
     * Get upload context for logging
     * 
     * @return string
     */
    private function getUploadContext()
    {
        $route = request()->route();
        if ($route) {
            return $route->getName() ?? $route->uri();
        }
        
        return request()->path();
    }

    /**
     * Cleanup old upload logs (optional maintenance method)
     * 
     * @param int $daysOld
     * @return int
     */
    protected function cleanupOldUploadLogs($daysOld = 90)
    {
        return UploadLog::where('created_at', '<', now()->subDays($daysOld))->delete();
    }

    /**
     * Get upload logs with pagination for admin interface
     * 
     * @param string|null $uploadType
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    protected function getUploadLogsPaginated($uploadType = null, $perPage = 20)
    {
        $query = UploadLog::with(['uploader'])
                          ->orderBy('created_at', 'desc');

        if ($uploadType) {
            $query->where('upload_type', $uploadType);
        }

        // Apply tenant scope if available
        if (method_exists($this, 'applyTenantScope')) {
            $query = $this->applyTenantScope($query);
        }

        return $query->paginate($perPage);
    }
}
