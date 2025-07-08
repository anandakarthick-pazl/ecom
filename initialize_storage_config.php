<?php

/*
|--------------------------------------------------------------------------
| Storage Configuration Initialization Script
|--------------------------------------------------------------------------
|
| This script ensures that the primary_storage_type setting exists in the 
| app_settings table. Run this once after implementing the dynamic storage fix.
|
*/

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Initializing storage configuration...\n";
    
    // Check if the primary_storage_type setting exists
    $existingSetting = DB::table('app_settings')
        ->where('key', 'primary_storage_type')
        ->whereNull('company_id')
        ->first();
    
    if (!$existingSetting) {
        // Create the setting with current value from .env
        $currentStorageType = env('STORAGE_TYPE', 'local');
        
        DB::table('app_settings')->insert([
            'key' => 'primary_storage_type',
            'value' => $currentStorageType,
            'type' => 'string',
            'group' => 'storage',
            'label' => 'Primary Storage Type',
            'description' => 'Default storage type for all file uploads (local or s3)',
            'company_id' => null, // Global setting
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        echo "✅ Created primary_storage_type setting with value: {$currentStorageType}\n";
    } else {
        echo "✅ Primary storage type setting already exists with value: {$existingSetting->value}\n";
    }
    
    echo "✅ Storage configuration initialization completed successfully!\n";
    echo "\nNow you can:\n";
    echo "1. Go to Super Admin Settings > Storage to change the Primary Storage Type\n";
    echo "2. When you select S3, all new uploads will go to S3\n";
    echo "3. When you select Local, all new uploads will go to local storage\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
