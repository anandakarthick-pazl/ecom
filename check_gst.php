<?php
// Quick GST Field Check - Save as: check_gst.php

echo "<h2>GST Field Debug Check</h2>";

// Check if settings page file exists
$settingsFile = 'resources/views/admin/settings/index.blade.php';
if (file_exists($settingsFile)) {
    echo "✅ Settings file exists<br>";
    
    // Check if GST field is in the file
    $content = file_get_contents($settingsFile);
    if (strpos($content, 'gst_number') !== false) {
        echo "✅ GST field found in settings file<br>";
        
        // Check if the new prominent section exists
        if (strpos($content, 'GST Configuration for Invoices') !== false) {
            echo "✅ New prominent GST section found<br>";
        } else {
            echo "❌ New GST section missing<br>";
        }
    } else {
        echo "❌ GST field not found in settings file<br>";
    }
} else {
    echo "❌ Settings file not found<br>";
}

echo "<h3>Next Steps:</h3>";
echo "1. Run: php artisan view:clear<br>";
echo "2. Run: php artisan cache:clear<br>";
echo "3. Hard refresh browser (Ctrl+Shift+R)<br>";
echo "4. Access admin settings page<br>";
echo "5. Look for blue 'GST Configuration for Invoices' section<br>";
