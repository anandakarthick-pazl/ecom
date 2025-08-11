<?php

/**
 * Create required directories for bulk upload functionality
 * Run this: php create_upload_directories.php
 */

echo "Creating required directories for bulk upload...\n";
echo "================================================\n\n";

$directories = [
    'storage/app/temp' => 'Temporary file storage for uploads',
    'storage/app/public' => 'Public file storage',
    'storage/app/public/products' => 'Product images storage',
    'storage/logs' => 'Log files storage'
];

$created = 0;
$existing = 0;

foreach ($directories as $dir => $description) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Created: {$dir} ({$description})\n";
            $created++;
        } else {
            echo "❌ Failed to create: {$dir}\n";
        }
    } else {
        echo "✅ Already exists: {$dir}\n";
        $existing++;
    }
}

// Check and create storage symlink if needed
echo "\nChecking storage symlink...\n";
if (!is_link('public/storage') && !is_dir('public/storage')) {
    if (symlink('../storage/app/public', 'public/storage')) {
        echo "✅ Created storage symlink\n";
    } else {
        echo "❌ Failed to create storage symlink. Run: php artisan storage:link\n";
    }
} else {
    echo "✅ Storage symlink already exists\n";
}

// Set proper permissions
echo "\nSetting permissions...\n";
foreach (array_keys($directories) as $dir) {
    if (is_dir($dir)) {
        chmod($dir, 0755);
        echo "✅ Set permissions for: {$dir}\n";
    }
}

echo "\n================================================\n";
echo "Summary:\n";
echo "- Created: {$created} directories\n";
echo "- Already existed: {$existing} directories\n";
echo "- All required directories are now ready!\n";
echo "\nYou can now upload CSV files through the bulk upload feature.\n";
