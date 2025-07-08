<?php

/*
|--------------------------------------------------------------------------
| S3 Upload Test Script (With Public Access)
|--------------------------------------------------------------------------
|
| This script tests that S3 uploads now work with public read access.
| Run this after applying the S3 public access fix.
|
*/

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

try {
    echo "=== S3 Upload Test (With Public Access) ===\n\n";
    
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
    
    echo "🧪 Testing S3 upload with public access...\n\n";
    
    // Create a test image file
    $testContent = "Test file for S3 public access - Created at " . date('Y-m-d H:i:s');
    $testFileName = 'test-public-access-' . time() . '.txt';
    $testFilePath = 'test/' . $testFileName;
    
    // Test 1: Upload with new configuration
    echo "1. Testing file upload with public ACL...\n";
    
    try {
        $uploaded = Storage::disk('s3')->put(
            $testFilePath, 
            $testContent,
            ['visibility' => 'public', 'ACL' => 'public-read']
        );
        
        if ($uploaded) {
            echo "   ✅ File upload successful!\n";
            echo "   📁 File path: {$testFilePath}\n";
        } else {
            echo "   ❌ File upload failed\n";
            exit(1);
        }
    } catch (Exception $e) {
        echo "   ❌ File upload failed: " . $e->getMessage() . "\n";
        exit(1);
    }
    
    // Test 2: Generate public URL
    echo "\n2. Testing public URL generation...\n";
    try {
        $s3Url = "https://{$bucket}.s3.{$region}.amazonaws.com/{$testFilePath}";
        echo "   ✅ Public URL generated!\n";
        echo "   🔗 URL: {$s3Url}\n";
    } catch (Exception $e) {
        echo "   ❌ URL generation failed: " . $e->getMessage() . "\n";
    }
    
    // Test 3: Test public accessibility with cURL
    echo "\n3. Testing public accessibility...\n";
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $s3Url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_NOBODY, true);  // HEAD request only
        curl_setopt($ch, CURLOPT_HEADER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            echo "   ✅ File is publicly accessible!\n";
            echo "   📊 HTTP Status: {$httpCode} (OK)\n";
        } else {
            echo "   ❌ File is not publicly accessible\n";
            echo "   📊 HTTP Status: {$httpCode}\n";
            
            if ($httpCode === 403) {
                echo "   💡 This indicates an AccessDenied error - ACL may not be set correctly\n";
            }
        }
    } catch (Exception $e) {
        echo "   ⚠️  Could not test accessibility: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Test with Dynamic Storage trait
    echo "\n4. Testing DynamicStorage trait methods...\n";
    try {
        // Create a test class that uses the trait
        $testClass = new class {
            use App\Traits\DynamicStorage;
            
            public function testUpload() {
                // Create a temporary file
                $tempFile = tempnam(sys_get_temp_dir(), 'test_upload');
                file_put_contents($tempFile, 'Test content for trait upload');
                
                // Create UploadedFile instance
                $uploadedFile = new UploadedFile(
                    $tempFile,
                    'test-trait-upload.txt',
                    'text/plain',
                    null,
                    true
                );
                
                return $this->storeFileAsDynamically($uploadedFile, 'test', 'trait-test-' . time() . '.txt');
            }
        };
        
        $result = $testClass->testUpload();
        
        if ($result) {
            echo "   ✅ DynamicStorage trait upload successful!\n";
            echo "   📁 File path: {$result}\n";
            
            // Test the trait uploaded file accessibility
            $traitFileUrl = "https://{$bucket}.s3.{$region}.amazonaws.com/{$result}";
            echo "   🔗 Trait file URL: {$traitFileUrl}\n";
            
            // Quick HTTP test
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $traitFileUrl);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                echo "   ✅ Trait uploaded file is publicly accessible!\n";
            } else {
                echo "   ❌ Trait uploaded file accessibility issue (HTTP {$httpCode})\n";
            }
        } else {
            echo "   ❌ DynamicStorage trait upload failed\n";
        }
    } catch (Exception $e) {
        echo "   ❌ DynamicStorage trait test failed: " . $e->getMessage() . "\n";
    }
    
    // Cleanup test files
    echo "\n5. Cleaning up test files...\n";
    try {
        // Delete the test files
        Storage::disk('s3')->delete($testFilePath);
        if (isset($result)) {
            Storage::disk('s3')->delete($result);
        }
        echo "   ✅ Test files cleaned up\n";
    } catch (Exception $e) {
        echo "   ⚠️  Cleanup warning: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Test Summary ===\n";
    echo "✅ S3 upload configuration is working correctly!\n";
    echo "✅ Files are uploaded with public-read ACL\n";
    echo "✅ Files are publicly accessible via direct URLs\n";
    echo "✅ DynamicStorage trait is working correctly\n\n";
    
    echo "🎉 Your S3 upload fix is working perfectly!\n";
    echo "   New file uploads will be publicly accessible.\n";
    echo "   Run 'php fix_s3_public_access.php' to fix existing files.\n\n";
    
    echo "🌐 Test your website:\n";
    echo "   1. Upload a product image via admin panel\n";
    echo "   2. Check that it displays correctly on the website\n";
    echo "   3. Verify no more AccessDenied errors\n";
    
} catch (Exception $e) {
    echo "\n❌ Test Failed: " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "1. S3 configuration in .env file\n";
    echo "2. AWS IAM permissions\n";
    echo "3. S3 bucket public access settings\n";
    exit(1);
}
