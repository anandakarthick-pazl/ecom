<?php

/*
|--------------------------------------------------------------------------
| Fix S3 Public Access Script
|--------------------------------------------------------------------------
|
| This script updates the ACL (Access Control List) of existing S3 files
| to make them publicly accessible. Run this after updating the S3 
| configuration to fix "AccessDenied" errors for existing files.
|
*/

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Aws\S3\S3Client;

try {
    echo "=== S3 Public Access Fix Script ===\n\n";
    
    // Get S3 configuration
    $bucket = config('filesystems.disks.s3.bucket');
    $region = config('filesystems.disks.s3.region');
    $accessKey = config('filesystems.disks.s3.key');
    $secretKey = config('filesystems.disks.s3.secret');
    
    echo "S3 Configuration:\n";
    echo "- Bucket: {$bucket}\n";
    echo "- Region: {$region}\n";
    echo "- Access Key: " . substr($accessKey, 0, 8) . "***\n\n";
    
    if (!$bucket || !$region || !$accessKey || !$secretKey) {
        echo "❌ Missing S3 configuration. Please check your .env file.\n";
        exit(1);
    }
    
    // Initialize S3 Client
    $s3Client = new S3Client([
        'version' => 'latest',
        'region' => $region,
        'credentials' => [
            'key' => $accessKey,
            'secret' => $secretKey,
        ],
    ]);
    
    echo "🔍 Scanning S3 bucket for files...\n\n";
    
    $fixedFiles = [];
    $errorFiles = [];
    $totalFiles = 0;
    
    // Get all objects in the bucket
    $paginator = $s3Client->getPaginator('ListObjectsV2', [
        'Bucket' => $bucket
    ]);
    
    foreach ($paginator as $result) {
        if (isset($result['Contents'])) {
            foreach ($result['Contents'] as $object) {
                $key = $object['Key'];
                $totalFiles++;
                
                // Skip directory markers (keys ending with /)
                if (substr($key, -1) === '/') {
                    continue;
                }
                
                echo "Processing: {$key}... ";
                
                try {
                    // Check current ACL
                    $currentAcl = $s3Client->getObjectAcl([
                        'Bucket' => $bucket,
                        'Key' => $key
                    ]);
                    
                    // Check if file is already public
                    $isPublic = false;
                    foreach ($currentAcl['Grants'] as $grant) {
                        if (isset($grant['Grantee']['URI']) && 
                            $grant['Grantee']['URI'] === 'http://acs.amazonaws.com/groups/global/AllUsers' &&
                            in_array($grant['Permission'], ['READ', 'FULL_CONTROL'])) {
                            $isPublic = true;
                            break;
                        }
                    }
                    
                    if ($isPublic) {
                        echo "✅ Already public\n";
                        continue;
                    }
                    
                    // Update ACL to public-read
                    $s3Client->putObjectAcl([
                        'Bucket' => $bucket,
                        'Key' => $key,
                        'ACL' => 'public-read'
                    ]);
                    
                    $fixedFiles[] = $key;
                    echo "✅ Fixed\n";
                    
                } catch (Exception $e) {
                    $errorFiles[] = [
                        'file' => $key,
                        'error' => $e->getMessage()
                    ];
                    echo "❌ Error: " . $e->getMessage() . "\n";
                }
                
                // Small delay to avoid rate limiting
                usleep(100000); // 0.1 seconds
            }
        }
    }
    
    echo "\n=== Summary ===\n";
    echo "Total files scanned: {$totalFiles}\n";
    echo "Files fixed: " . count($fixedFiles) . "\n";
    echo "Files with errors: " . count($errorFiles) . "\n\n";
    
    if (!empty($fixedFiles)) {
        echo "✅ Fixed Files:\n";
        foreach (array_slice($fixedFiles, 0, 10) as $file) {
            echo "   - {$file}\n";
        }
        if (count($fixedFiles) > 10) {
            echo "   ... and " . (count($fixedFiles) - 10) . " more\n";
        }
        echo "\n";
    }
    
    if (!empty($errorFiles)) {
        echo "❌ Files with Errors:\n";
        foreach (array_slice($errorFiles, 0, 5) as $error) {
            echo "   - {$error['file']}: {$error['error']}\n";
        }
        if (count($errorFiles) > 5) {
            echo "   ... and " . (count($errorFiles) - 5) . " more\n";
        }
        echo "\n";
    }
    
    // Test one of the fixed files
    if (!empty($fixedFiles)) {
        $testFile = $fixedFiles[0];
        $testUrl = "https://{$bucket}.s3.{$region}.amazonaws.com/{$testFile}";
        echo "🧪 Test URL for first fixed file:\n";
        echo "   {$testUrl}\n";
        echo "   Try opening this URL in your browser to verify public access.\n\n";
    }
    
    echo "🎉 S3 Public Access Fix Complete!\n";
    echo "   Your uploaded images should now be publicly accessible.\n";
    echo "   Future uploads will automatically have public access.\n\n";
    
    // Update database records if they exist
    echo "🔄 Updating database URLs...\n";
    
    try {
        // Check if storage_files table exists
        $tables = DB::select("SHOW TABLES LIKE 'storage_files'");
        
        if (!empty($tables)) {
            foreach ($fixedFiles as $filePath) {
                $newUrl = "https://{$bucket}.s3.{$region}.amazonaws.com/{$filePath}";
                
                DB::table('storage_files')
                    ->where('file_path', $filePath)
                    ->update(['url' => $newUrl]);
            }
            echo "✅ Database URLs updated\n";
        } else {
            echo "ℹ️  No storage_files table found\n";
        }
    } catch (Exception $e) {
        echo "⚠️  Database update skipped: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ Script Failed: " . $e->getMessage() . "\n";
    echo "\nTroubleshooting:\n";
    echo "1. Check your AWS credentials in .env file\n";
    echo "2. Verify the S3 bucket name is correct\n";
    echo "3. Ensure your IAM user has these permissions:\n";
    echo "   - s3:GetObjectAcl\n";
    echo "   - s3:PutObjectAcl\n";
    echo "   - s3:ListBucket\n";
    echo "4. Check if the bucket region matches your configuration\n";
    exit(1);
}
