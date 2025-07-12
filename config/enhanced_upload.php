<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enhanced File Upload Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the enhanced file upload system
    | that works with your existing DynamicStorage functionality.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Base Upload Path
    |--------------------------------------------------------------------------
    |
    | The base path where files will be uploaded within storage/app/public
    |
    */
    'base_path' => storage_path('app/public'),

    /*
    |--------------------------------------------------------------------------
    | Default File Size Limits (in bytes)
    |--------------------------------------------------------------------------
    */
    'max_file_sizes' => [
        'images' => 5 * 1024 * 1024,     // 5MB for regular images
        'banners' => 10 * 1024 * 1024,   // 10MB for banner images  
        'products' => 5 * 1024 * 1024,   // 5MB for product images
        'categories' => 3 * 1024 * 1024, // 3MB for category images
        'documents' => 20 * 1024 * 1024, // 20MB for documents
        'default' => 5 * 1024 * 1024,    // 5MB default
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed File Types by Category
    |--------------------------------------------------------------------------
    */
    'allowed_types' => [
        'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        'banners' => ['jpg', 'jpeg', 'png', 'webp'],
        'products' => ['jpg', 'jpeg', 'png', 'webp'],
        'categories' => ['jpg', 'jpeg', 'png', 'webp'],
        'documents' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv'],
        'invoices' => ['pdf', 'jpg', 'jpeg', 'png'],
        'all' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv']
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Dimension Limits
    |--------------------------------------------------------------------------
    */
    'max_dimensions' => [
        'products' => [2000, 2000],     // 2000x2000px for products
        'categories' => [1500, 1500],   // 1500x1500px for categories
        'banners' => [3000, 2000],      // 3000x2000px for banners
        'default' => [2000, 2000],      // Default max dimensions
    ],

    /*
    |--------------------------------------------------------------------------
    | Thumbnail Configurations
    |--------------------------------------------------------------------------
    */
    'thumbnails' => [
        'products' => [
            'small' => [150, 150],
            'medium' => [300, 300],
            'large' => [600, 600]
        ],
        'categories' => [
            'small' => [100, 100],
            'medium' => [200, 200],
            'large' => [400, 400]
        ],
        'banners' => [
            'hero' => [
                'small' => [400, 200],
                'medium' => [800, 400],
                'large' => [1200, 600]
            ],
            'top' => [
                'small' => [300, 100],
                'medium' => [600, 200],
                'large' => [1200, 400]
            ],
            'middle' => [
                'small' => [300, 150],
                'medium' => [600, 300],
                'large' => [1000, 500]
            ],
            'bottom' => [
                'small' => [300, 100],
                'medium' => [600, 200],
                'large' => [1200, 400]
            ],
            'sidebar' => [
                'small' => [150, 200],
                'medium' => [300, 400],
                'large' => [400, 600]
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Optimization Settings
    |--------------------------------------------------------------------------
    */
    'optimization' => [
        'enabled' => true,
        'jpeg_quality' => 85,
        'png_compression' => 6,
        'webp_quality' => 85,
        'auto_orient' => true,
        'strip_meta' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Directory Structure
    |--------------------------------------------------------------------------
    */
    'directories' => [
        'products' => 'products',
        'categories' => 'categories',
        'banners' => 'banners',
        'invoices' => 'invoices',
        'logos' => 'logos',
        'settings' => 'settings',
        'payment-methods' => 'payment-methods',
        'whatsapp-bills' => 'whatsapp-bills',
        'temp' => 'temp',
        'exports' => 'exports',
        'imports' => 'imports',
        'backups' => 'backups'
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        'scan_files' => true,
        'check_mime_type' => true,
        'validate_extensions' => true,
        'sanitize_filename' => true,
        'max_filename_length' => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup Settings
    |--------------------------------------------------------------------------
    */
    'cleanup' => [
        'temp_files_lifetime' => 24, // hours
        'orphaned_files_check' => 7, // days
        'enable_auto_cleanup' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Backend Integration
    |--------------------------------------------------------------------------
    */
    'storage_backends' => [
        'local' => [
            'enabled' => true,
            'generate_thumbnails' => true,
            'optimize_images' => true,
        ],
        's3' => [
            'enabled' => true,
            'generate_thumbnails' => false, // S3 thumbnails handled differently
            'optimize_images' => false,     // Optimize before upload
            'acl' => 'public-read',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Handling
    |--------------------------------------------------------------------------
    */
    'error_handling' => [
        'log_errors' => true,
        'log_level' => 'warning',
        'fallback_to_original' => true, // If enhanced upload fails, try original method
        'detailed_error_messages' => env('APP_DEBUG', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'memory_limit' => '256M',
        'max_execution_time' => 300, // 5 minutes
        'chunk_size' => 1024 * 1024, // 1MB chunks for large files
        'enable_progressive_jpeg' => true,
    ]
];
