<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PDF Generation Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for PDF generation using
    | the BillPDFService. You can customize various aspects of PDF generation
    | including formats, optimization settings, and cache behavior.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Default PDF Settings
    |--------------------------------------------------------------------------
    |
    | These are the default settings used when generating PDFs. Individual
    | companies can override these settings through the admin panel.
    |
    */
    'defaults' => [
        'format' => 'a4_sheet', // Default format: 'thermal' or 'a4_sheet'
        'thermal_enabled' => true,
        'a4_enabled' => true,
        'thermal_width' => 80, // mm
        'a4_orientation' => 'portrait',
        'logo_enabled' => true,
        'company_info_enabled' => true,
        'auto_cut' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Thermal Printer Settings
    |--------------------------------------------------------------------------
    |
    | Configuration options specific to thermal printer receipts.
    |
    */
    'thermal' => [
        'paper_width' => 80, // mm
        'paper_height' => 'auto', // mm or 'auto'
        'margin' => 5, // mm
        'font_family' => 'Courier New, DejaVu Sans Mono, monospace',
        'font_size' => 11, // px
        'line_height' => 1.3,
        'logo_max_width' => 50, // px
        'logo_max_height' => 40, // px
        'paper_size' => [0, 0, 226.77, 841.89], // points (80mm width)
    ],

    /*
    |--------------------------------------------------------------------------
    | A4 Sheet Settings
    |--------------------------------------------------------------------------
    |
    | Configuration options for A4 format PDFs.
    |
    */
    'a4' => [
        'paper_size' => 'A4',
        'orientation' => 'portrait',
        'margin' => 15, // mm
        'font_family' => 'DejaVu Sans, Arial, sans-serif',
        'font_size' => 11, // px
        'line_height' => 1.4,
        'logo_max_width' => 80, // px
        'logo_max_height' => 60, // px
    ],

    /*
    |--------------------------------------------------------------------------
    | DomPDF Options
    |--------------------------------------------------------------------------
    |
    | Options passed to the DomPDF library for PDF generation.
    |
    */
    'dompdf' => [
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => false, // Security: disable remote resources
        'defaultFont' => 'Arial',
        'dpi' => 96,
        'isPhpEnabled' => false, // Security: disable PHP in templates
        'isJavascriptEnabled' => false, // Security: disable JS
        'debugKeepTemp' => false,
        'chroot' => false,
        'logOutputFile' => false,
        'fontDir' => null, // Will use storage_path('fonts') if null
        'fontCache' => null, // Will use storage_path('app/dompdf_font_cache') if null
        'tempDir' => null, // Will use sys_get_temp_dir() if null
        'isFontSubsettingEnabled' => false,
        'isCssFloatEnabled' => false,
        'isImageDragAndDropEnabled' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Optimization
    |--------------------------------------------------------------------------
    |
    | Settings to optimize PDF generation performance.
    |
    */
    'performance' => [
        'enable_caching' => true,
        'cache_ttl' => 600, // seconds (10 minutes)
        'company_settings_cache_ttl' => 600, // seconds
        'format_config_cache_ttl' => 300, // seconds
        'fast_generation' => true, // Use optimized generation methods
        'stream_output' => true, // Stream PDFs directly without disk storage
        'image_processing' => 'base64', // 'base64' or 'path'
        'memory_limit' => '256M',
        'max_execution_time' => 120, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Processing
    |--------------------------------------------------------------------------
    |
    | Configuration for handling images in PDFs (logos, etc.)
    |
    */
    'images' => [
        'supported_formats' => ['jpeg', 'jpg', 'png', 'gif'],
        'max_size' => 5242880, // 5MB in bytes
        'quality' => 85, // JPEG quality (1-100)
        'auto_resize' => true,
        'max_width' => 800, // px
        'max_height' => 600, // px
        'convert_to_base64' => true,
        'search_paths' => [
            'public/storage',
            'storage/app/public',
            'public',
            'storage/app',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Management
    |--------------------------------------------------------------------------
    |
    | Settings for managing generated PDF files.
    |
    */
    'files' => [
        'temp_directory' => 'temp/bills',
        'temp_cleanup_hours' => 24, // Delete temp files older than this
        'auto_cleanup' => true,
        'filename_format' => '{prefix}_{reference}_{timestamp}.pdf',
        'preserve_source_filename' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Control what gets logged during PDF generation.
    |
    */
    'logging' => [
        'enabled' => true,
        'level' => 'info', // debug, info, warning, error
        'log_performance' => true,
        'log_image_processing' => true,
        'log_cache_operations' => false,
        'log_memory_usage' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    |
    | Security-related settings for PDF generation.
    |
    */
    'security' => [
        'validate_file_paths' => true,
        'allowed_directories' => [
            'storage/app/public',
            'public/storage',
        ],
        'blocked_extensions' => ['php', 'js', 'exe', 'bat'],
        'sanitize_filenames' => true,
        'max_filename_length' => 255,
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Handling
    |--------------------------------------------------------------------------
    |
    | How to handle errors during PDF generation.
    |
    */
    'error_handling' => [
        'fallback_on_image_error' => true,
        'default_logo_fallback' => '🌿', // Emoji or text fallback
        'retry_attempts' => 2,
        'retry_delay' => 1000, // milliseconds
        'graceful_degradation' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Settings
    |--------------------------------------------------------------------------
    |
    | Settings useful during development and testing.
    |
    */
    'development' => [
        'debug_mode' => env('APP_DEBUG', false),
        'save_debug_pdfs' => false,
        'debug_directory' => 'debug/pdfs',
        'show_generation_time' => false,
        'enable_test_routes' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Company-Specific Overrides
    |--------------------------------------------------------------------------
    |
    | You can define company-specific settings here. These will override
    | the default settings for specific companies.
    |
    */
    'company_overrides' => [
        // Example:
        // 1 => [
        //     'defaults' => [
        //         'format' => 'thermal',
        //         'thermal_enabled' => true,
        //         'a4_enabled' => false,
        //     ],
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features.
    |
    */
    'features' => [
        'ultra_fast_generation' => true,
        'batch_processing' => true,
        'background_generation' => false, // Requires queue setup
        'pdf_merging' => false,
        'watermarks' => false,
        'digital_signatures' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Integration Settings
    |--------------------------------------------------------------------------
    |
    | Settings for integrating with external services.
    |
    */
    'integrations' => [
        'cloud_storage' => [
            'enabled' => false,
            'driver' => 's3', // s3, gcs, etc.
            'bucket' => env('PDF_STORAGE_BUCKET'),
            'path' => 'pdfs/',
        ],
        'email' => [
            'auto_attach' => false,
            'max_attachment_size' => 10485760, // 10MB
        ],
        'webhooks' => [
            'enabled' => false,
            'endpoints' => [],
        ],
    ],

];
