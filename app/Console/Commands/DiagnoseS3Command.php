<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class DiagnoseS3Command extends Command
{
    protected $signature = 'storage:diagnose-s3';
    protected $description = 'Diagnose S3 connection issues';

    public function handle()
    {
        $this->info('🔍 Diagnosing S3 Connection Issues...');
        $this->line('');

        // Check environment variables
        $this->checkEnvironmentVariables();
        
        // Check credentials
        $this->checkCredentials();
        
        // Check bucket access
        $this->checkBucketAccess();
        
        // Provide recommendations
        $this->provideRecommendations();
    }

    private function checkEnvironmentVariables()
    {
        $this->info('📋 Checking Environment Variables:');
        
        $required = [
            'AWS_ACCESS_KEY_ID',
            'AWS_SECRET_ACCESS_KEY', 
            'AWS_DEFAULT_REGION',
            'AWS_BUCKET'
        ];

        foreach ($required as $var) {
            $value = config("filesystems.disks.s3." . str_replace('AWS_', '', strtolower($var)));
            if ($var === 'AWS_SECRET_ACCESS_KEY') {
                $display = $value ? str_repeat('*', min(strlen($value), 20)) : 'NOT SET';
            } else {
                $display = $value ?: 'NOT SET';
            }
            
            $status = $value ? '✅' : '❌';
            $this->line("  {$status} {$var}: {$display}");
        }
        $this->line('');
    }

    private function checkCredentials()
    {
        $this->info('🔐 Testing AWS Credentials:');
        
        try {
            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => config('filesystems.disks.s3.region'),
                'credentials' => [
                    'key' => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ],
            ]);

            // Test credentials with a simple STS call
            $stsClient = new \Aws\Sts\StsClient([
                'version' => 'latest',
                'region' => config('filesystems.disks.s3.region'),
                'credentials' => [
                    'key' => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ],
            ]);

            $result = $stsClient->getCallerIdentity();
            $this->line('  ✅ Credentials are valid');
            $this->line('  📝 Account: ' . $result['Account']);
            $this->line('  👤 User ARN: ' . $result['Arn']);
            
        } catch (AwsException $e) {
            $this->line('  ❌ Credential validation failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->line('  ❌ Error validating credentials: ' . $e->getMessage());
        }
        $this->line('');
    }

    private function checkBucketAccess()
    {
        $this->info('🪣 Testing Bucket Access:');
        
        $bucket = config('filesystems.disks.s3.bucket');
        $region = config('filesystems.disks.s3.region');
        
        try {
            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => $region,
                'credentials' => [
                    'key' => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ],
            ]);

            // Test 1: List all buckets (to see what we have access to)
            $this->line('  🔍 Checking accessible buckets...');
            try {
                $result = $s3Client->listBuckets();
                $buckets = array_column($result['Buckets'], 'Name');
                
                if (in_array($bucket, $buckets)) {
                    $this->line("  ✅ Bucket '{$bucket}' found in your account");
                } else {
                    $this->line("  ❌ Bucket '{$bucket}' not found in your account");
                    $this->line('  📋 Available buckets: ' . implode(', ', $buckets));
                }
            } catch (AwsException $e) {
                $this->line('  ❌ Cannot list buckets: ' . $e->getMessage());
            }

            // Test 2: Check bucket location
            $this->line('  🌍 Checking bucket region...');
            try {
                $result = $s3Client->getBucketLocation(['Bucket' => $bucket]);
                $bucketRegion = $result['LocationConstraint'] ?: 'us-east-1';
                
                if ($bucketRegion === $region) {
                    $this->line("  ✅ Bucket region matches: {$bucketRegion}");
                } else {
                    $this->line("  ❌ Region mismatch! Bucket: {$bucketRegion}, Config: {$region}");
                }
            } catch (AwsException $e) {
                $this->line('  ❌ Cannot get bucket location: ' . $e->getMessage());
            }

            // Test 3: Head bucket (the failing operation)
            $this->line('  🏠 Testing HeadBucket operation...');
            try {
                $s3Client->headBucket(['Bucket' => $bucket]);
                $this->line('  ✅ HeadBucket successful');
            } catch (AwsException $e) {
                $this->line('  ❌ HeadBucket failed: ' . $e->getMessage());
                $this->line('  📋 Error Code: ' . $e->getAwsErrorCode());
            }

            // Test 4: List bucket contents
            $this->line('  📁 Testing ListObjects permission...');
            try {
                $result = $s3Client->listObjectsV2([
                    'Bucket' => $bucket,
                    'MaxKeys' => 1
                ]);
                $this->line('  ✅ ListObjects successful');
            } catch (AwsException $e) {
                $this->line('  ❌ ListObjects failed: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            $this->line('  ❌ Failed to create S3 client: ' . $e->getMessage());
        }
        $this->line('');
    }

    private function provideRecommendations()
    {
        $this->info('💡 Recommendations:');
        $this->line('');
        
        $this->line('  1. 🔑 Verify IAM User Permissions:');
        $this->line('     - Attach S3FullAccess policy OR create custom policy');
        $this->line('     - Ensure user has s3:HeadBucket, s3:ListBucket permissions');
        $this->line('');
        
        $this->line('  2. 🪣 Check Bucket Configuration:');
        $this->line('     - Verify bucket name is exactly: ' . config('filesystems.disks.s3.bucket'));
        $this->line('     - Confirm bucket exists in region: ' . config('filesystems.disks.s3.region'));
        $this->line('     - Check bucket policy doesn\'t block your IP/user');
        $this->line('');
        
        $this->line('  3. 🌍 Region Settings:');
        $this->line('     - Ensure AWS_DEFAULT_REGION matches bucket region');
        $this->line('     - Current setting: ' . config('filesystems.disks.s3.region'));
        $this->line('');
        
        $this->line('  4. 🔧 Quick Fixes to Try:');
        $this->line('     - php artisan config:clear');
        $this->line('     - Check .env file has no extra spaces');
        $this->line('     - Try with a different bucket name');
        $this->line('');
        
        $this->line('  5. 📝 Minimal IAM Policy for Testing:');
        $this->line('     {');
        $this->line('       "Version": "2012-10-17",');
        $this->line('       "Statement": [');
        $this->line('         {');
        $this->line('           "Effect": "Allow",');
        $this->line('           "Action": ["s3:*"],');
        $this->line('           "Resource": [');
        $this->line('             "arn:aws:s3:::' . config('filesystems.disks.s3.bucket') . '",');
        $this->line('             "arn:aws:s3:::' . config('filesystems.disks.s3.bucket') . '/*"');
        $this->line('           ]');
        $this->line('         }');
        $this->line('       ]');
        $this->line('     }');
    }
}
