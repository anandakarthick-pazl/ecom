<?php

/**
 * Ensure required directories exist for bulk upload functionality
 * Run this once: php setup_bulk_upload_directories.php
 */

echo "Setting up directories for bulk upload functionality...\n";
echo "====================================================\n\n";

$directories = [
    'storage/app/temp' => 'Temporary file storage',
    'storage/app/public/products' => 'Product images storage',
    'public/storage' => 'Public storage symlink'
];

foreach ($directories as $dir => $description) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Created directory: {$dir} ({$description})\n";
        } else {
            echo "❌ Failed to create directory: {$dir}\n";
        }
    } else {
        echo "✅ Directory already exists: {$dir}\n";
    }
}

// Check if storage symlink exists
if (!is_link('public/storage') && !is_dir('public/storage')) {
    echo "\n⚠️  Storage symlink missing. Creating symlink...\n";
    if (symlink('../storage/app/public', 'public/storage')) {
        echo "✅ Created storage symlink\n";
    } else {
        echo "❌ Failed to create storage symlink. Run: php artisan storage:link\n";
    }
}

echo "\n====================================================\n";
echo "Directory setup complete!\n";
echo "You can now use the bulk upload functionality.\n";
