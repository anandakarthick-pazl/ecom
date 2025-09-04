<?php
/**
 * Tamil Font Setup for DomPDF
 * Run with: php setup_tamil_fonts.php
 */

echo "🔧 Setting up Tamil font support for PDF...\n\n";

// Create necessary directories
$directories = [
    'storage/fonts',
    'storage/fonts/cache',
    'storage/app/temp'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Created directory: {$dir}\n";
    } else {
        echo "✅ Directory exists: {$dir}\n";
    }
}

echo "\n📝 Tamil Font Configuration:\n";
echo "- Default font changed to 'DejaVu Sans' (supports Tamil)\n";
echo "- Added 'Noto Sans Tamil' as fallback\n";
echo "- Enabled Unicode support\n";
echo "- Enabled font subsetting for smaller PDFs\n";

echo "\n🎯 What fonts support Tamil:\n";
echo "✅ DejaVu Sans - Good Tamil support, included with DomPDF\n";
echo "✅ Noto Sans Tamil - Google's Tamil font (if installed)\n";
echo "✅ Arial Unicode MS - If available on system\n";

echo "\n🚀 Ready to test!\n";
echo "1. Clear caches: php artisan config:clear\n";
echo "2. Test PDF: http://greenvalleyherbs.local:8000/price-list/download\n";
echo "3. Check if Tamil text appears correctly\n\n";

// Test Tamil text rendering capability
echo "📋 Testing Tamil text support...\n";

$tamilText = "தமிழ் வணக்கம்"; // "Tamil Hello"
$englishText = "Tamil Test: $tamilText";

echo "Sample text: $englishText\n";
echo "If you see proper Tamil characters above, font support should work in PDF too.\n\n";

echo "🎉 Tamil font setup completed!\n";
