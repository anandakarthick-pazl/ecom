<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Fix existing S3 file URLs that are pointing to localhost
        $bucket = config('filesystems.disks.s3.bucket');
        $region = config('filesystems.disks.s3.region');
        $customUrl = config('filesystems.disks.s3.url');
        
        if ($bucket && $region) {
            $s3Files = DB::table('storage_files')
                ->where('storage_type', 's3')
                ->where('url', 'like', 'http://localhost%')
                ->get();
                
            foreach ($s3Files as $file) {
                // Generate correct S3 URL
                if ($customUrl) {
                    $correctUrl = rtrim($customUrl, '/') . '/' . ltrim($file->file_path, '/');
                } else {
                    if ($region === 'us-east-1') {
                        $correctUrl = "https://{$bucket}.s3.amazonaws.com/{$file->file_path}";
                    } else {
                        $correctUrl = "https://{$bucket}.s3.{$region}.amazonaws.com/{$file->file_path}";
                    }
                }
                
                // Update the URL
                DB::table('storage_files')
                    ->where('id', $file->id)
                    ->update(['url' => $correctUrl]);
            }
        }
    }
    
    public function down()
    {
        // No rollback needed
    }
};
