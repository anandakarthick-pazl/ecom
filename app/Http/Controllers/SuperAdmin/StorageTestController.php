<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StorageTestController extends Controller
{
    public function index()
    {
        try {
            // Basic test data
            $storageConfig = [
                'current_storage' => config('app.storage_type', 'local'),
                'local_config' => [
                    'available' => true,
                    'path' => storage_path('app/public'),
                    'url' => asset('storage')
                ],
                's3_config' => [
                    'available' => !empty(config('filesystems.disks.s3.key')),
                    'bucket' => config('filesystems.disks.s3.bucket'),
                    'region' => config('filesystems.disks.s3.region')
                ]
            ];

            $storageStats = [
                'total_files' => 0,
                'local' => ['file_count' => 0, 'total_size_formatted' => '0 B'],
                's3' => ['file_count' => 0, 'total_size_formatted' => '0 B'],
                'categories' => []
            ];

            return view('super-admin.storage.index', compact('storageConfig', 'storageStats'));
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Storage page failed to load',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
