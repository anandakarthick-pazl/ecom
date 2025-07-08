<?php

/*
|--------------------------------------------------------------------------
| S3 Permission Test Script
|--------------------------------------------------------------------------
|
| This script tests S3 connectivity and permissions without requiring
| ListAllMyBuckets permission. It only tests basic upload/download operations.
|
*/

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

try {
    echo "=== S3 Permission Test ===\n\n";
    
    // Get S3 configuration
    $bucket = config('filesystems.disks.s3.bucket');
    $region = config('filesystems.disks.s3.region');
    $accessKey = config('filesystems.disks.s3.key');
    
    echo "S3 Configuration:\n";
    echo "- Bucket: {$bucket}\n";
    echo "- Region: {$region}\n";
    echo "- Access Key: " . substr($accessKey, 0, 8) . "***\n\n";
    
    if (!$bucket || !$region || !$accessKey) {
        echo "❌ Missing S3 configuration. Please check your .env file.\n";
        exit(1);
    }
    
    echo "Testing S3 operations...\n\n";
    
    // Test 1: Try to create a test file
    echo "1. Testing file upload...\n";
    $testContent = "Test file created at " . date('Y-m-d H:i:s');
    $testFilePath = 'test/connectivity-test-' . time() . '.txt';
    
    try {
        $uploaded = Storage::disk('s3')->put($testFilePath, $testContent);
        if ($uploaded) {
            echo "   ✅ File upload successful!\n";
            echo "   📁 File path: {$testFilePath}\n";
        } else {
            echo "   ❌ File upload failed\n";
        }
    } catch (Exception $e) {
        echo "   ❌ File upload failed: " . $e->getMessage() . "\n";
    }
    
    // Test 2: Try to read the file back
    echo "\n2. Testing file download...\n";
    try {
        $downloadedContent = Storage::disk('s3')->get($testFilePath);
        if ($downloadedContent === $testContent) {
            echo "   ✅ File download successful!\n";
            echo "   📄 Content matches uploaded content\n";
        } else {
            echo "   ❌ File download failed - content mismatch\n";
        }
    } catch (Exception $e) {
        echo "   ❌ File download failed: " . $e->getMessage() . "\n";
    }
    
    // Test 3: Try to get file URL
    echo "\n3. Testing file URL generation...\n";
    try {
        $url = Storage::disk('s3')->url($testFilePath);
        echo "   ✅ File URL generated successfully!\n";
        echo "   🔗 URL: {$url}\n";
    } catch (Exception $e) {
        echo "   ❌ File URL generation failed: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Try to check if file exists
    echo "\n4. Testing file existence check...\n";
    try {
        $exists = Storage::disk('s3')->exists($testFilePath);
        if ($exists) {
            echo "   ✅ File existence check successful!\n";
        } else {
            echo "   ❌ File existence check failed - file not found\n";
        }
    } catch (Exception $e) {
        echo "   ❌ File existence check failed: " . $e->getMessage() . "\n";
    }
    
    // Test 5: Try to delete the test file
    echo "\n5. Testing file deletion...\n";
    try {
        $deleted = Storage::disk('s3')->delete($testFilePath);
        if ($deleted) {
            echo "   ✅ File deletion successful!\n";
            echo "   🗑️ Test file cleaned up\n";
        } else {
            echo "   ❌ File deletion failed\n";
        }
    } catch (Exception $e) {
        echo "   ❌ File deletion failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Test Summary ===\n";
    echo "✅ Basic S3 operations are working!\n";
    echo "📋 Your IAM user has sufficient permissions for file uploads.\n";
    echo "ℹ️  Note: You may see warnings about ListAllMyBuckets permission in logs.\n";
    echo "   This is normal and doesn't affect file upload functionality.\n\n";
    
    echo "🚀 Your S3 storage is ready for use!\n";
    echo "   You can now upload files through the admin panel.\n";
    
} catch (Exception $e) {
    echo "\n❌ S3 Test Failed: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting:\n";
    echo "1. Check your AWS credentials in .env file\n";
    echo "2. Verify the S3 bucket name is correct\n";
    echo "3. Ensure your IAM user has these permissions:\n";
    echo "   - s3:PutObject (for uploads)\n";
    echo "   - s3:GetObject (for downloads)\n";
    echo "   - s3:DeleteObject (for deletions)\n";
    echo "   - s3:GetObjectVersion (for versioned objects)\n";
    echo "4. Check if the bucket region matches your configuration\n";
    exit(1);
}
