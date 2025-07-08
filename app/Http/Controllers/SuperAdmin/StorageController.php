<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\StorageManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StorageController extends Controller
{
    protected $storageService;

    public function __construct(StorageManagementService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * Display storage management dashboard
     */
    public function index()
    {
        $storageConfig = $this->storageService->getStorageConfig();
        $storageStats = $this->storageService->getStorageStats();
        
        return view('super-admin.storage.index', compact('storageConfig', 'storageStats'));
    }

    /**
     * Display local storage management
     */
    public function localStorage()
    {
        $localFiles = $this->storageService->getLocalFiles();
        $directories = $this->storageService->getLocalDirectories();
        $storageStats = $this->storageService->getLocalStorageStats();
        
        return view('super-admin.storage.local', compact('localFiles', 'directories', 'storageStats'));
    }

    /**
     * Display S3 storage management
     */
    public function s3Storage()
    {
        $s3Config = $this->storageService->getS3Config();
        $s3Files = $this->storageService->getS3Files();
        $buckets = $this->storageService->getS3Buckets();
        $s3Stats = $this->storageService->getS3StorageStats();
        
        return view('super-admin.storage.s3', compact('s3Config', 's3Files', 'buckets', 's3Stats'));
    }

    /**
     * Update storage configuration
     */
    public function updateConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'storage_type' => 'required|in:local,s3',
            'aws_access_key_id' => 'nullable|string',
            'aws_secret_access_key' => 'nullable|string',
            'aws_default_region' => 'nullable|string',
            'aws_bucket' => 'nullable|string',
            'aws_url' => 'nullable|url',
            'local_path' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $this->storageService->updateStorageConfig($request->all());
            
            return redirect()->back()->with('success', 'Storage configuration updated successfully!');
        } catch (\Exception $e) {
            Log::error('Storage config update failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update storage configuration: ' . $e->getMessage());
        }
    }

    /**
     * Upload file to selected storage
     */
    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpeg,jpg,png,gif,webp,svg,pdf,doc,docx|max:10240',
            'storage_type' => 'required|in:local,s3',
            'directory' => 'nullable|string',
            'category' => 'required|in:products,banners,categories,general'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->storageService->uploadFile(
                $request->file('file'),
                $request->storage_type,
                $request->directory,
                $request->category
            );

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully!',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete file from storage
     */
    public function deleteFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_path' => 'required|string',
            'storage_type' => 'required|in:local,s3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->storageService->deleteFile(
                $request->file_path,
                $request->storage_type
            );

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('File deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Deletion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create directory
     */
    public function createDirectory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'directory_name' => 'required|string|max:255',
            'storage_type' => 'required|in:local,s3',
            'parent_directory' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->storageService->createDirectory(
                $request->directory_name,
                $request->storage_type,
                $request->parent_directory
            );

            return response()->json([
                'success' => true,
                'message' => 'Directory created successfully!',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Directory creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Directory creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test storage connection
     */
    public function testConnection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'storage_type' => 'required|in:local,s3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->storageService->testConnection($request->storage_type);

            return response()->json([
                'success' => true,
                'message' => 'Connection test successful!',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Connection test failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync files between local and S3
     */
    public function syncFiles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sync_direction' => 'required|in:local_to_s3,s3_to_local',
            'category' => 'nullable|in:products,banners,categories,general'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->storageService->syncFiles(
                $request->sync_direction,
                $request->category
            );

            return response()->json([
                'success' => true,
                'message' => 'File synchronization completed!',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('File sync failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Synchronization failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get file URL
     */
    public function getFileUrl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_path' => 'required|string',
            'storage_type' => 'required|in:local,s3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $url = $this->storageService->getFileUrl(
                $request->file_path,
                $request->storage_type
            );

            return response()->json([
                'success' => true,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            Log::error('Get file URL failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get file URL: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Backup storage
     */
    public function backupStorage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'storage_type' => 'required|in:local,s3',
            'backup_type' => 'required|in:full,incremental'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->storageService->backupStorage(
                $request->storage_type,
                $request->backup_type
            );

            return response()->json([
                'success' => true,
                'message' => 'Storage backup completed!',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Storage backup failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clean up old files
     */
    public function cleanupFiles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'storage_type' => 'required|in:local,s3',
            'days_old' => 'required|integer|min:1',
            'dry_run' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->storageService->cleanupOldFiles(
                $request->storage_type,
                $request->days_old,
                $request->get('dry_run', false)
            );

            return response()->json([
                'success' => true,
                'message' => $request->get('dry_run') ? 'Cleanup preview completed!' : 'Files cleaned up successfully!',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('File cleanup failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
