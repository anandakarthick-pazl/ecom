<?php

use Illuminate\Support\Facades\Route;

// Simple debug route to test storage system
Route::get('/debug-storage', function() {
    try {
        // Test 1: Check if helper functions are loaded
        $helperTest = function_exists('storage_url') ? '✅' : '❌';
        
        // Test 2: Check if service can be instantiated
        $serviceTest = '❌';
        try {
            $service = app(\App\Services\StorageManagementService::class);
            $serviceTest = '✅';
        } catch (\Exception $e) {
            $serviceError = $e->getMessage();
        }
        
        // Test 3: Check storage configuration
        $storageType = config('app.storage_type', 'not set');
        
        // Test 4: Check if migration was run
        $migrationTest = '❌';
        try {
            \DB::table('storage_files')->count();
            $migrationTest = '✅';
        } catch (\Exception $e) {
            $migrationError = $e->getMessage();
        }
        
        // Test 5: Check AWS SDK
        $awsTest = class_exists('Aws\S3\S3Client') ? '✅' : '❌';
        
        return response()->json([
            'status' => 'debug',
            'tests' => [
                'helper_functions' => $helperTest,
                'storage_service' => $serviceTest,
                'service_error' => $serviceError ?? null,
                'storage_type' => $storageType,
                'migration_applied' => $migrationTest,
                'migration_error' => $migrationError ?? null,
                'aws_sdk_loaded' => $awsTest,
            ],
            'config' => [
                'storage_type' => config('app.storage_type'),
                'aws_configured' => !empty(config('filesystems.disks.s3.key')),
                'local_path' => storage_path('app/public'),
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
