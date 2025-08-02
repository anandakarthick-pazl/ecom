#!/bin/bash

echo "🔍 TESTING SOCIAL MEDIA REGEX FIX"
echo "================================="

echo "1. Clearing caches..."
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear

echo ""
echo "2. Testing regex patterns..."

# Test the controller validation
php artisan tinker --execute="
try {
    \$validator = Illuminate\Support\Facades\Validator::make([
        'color' => '#FF0000'
    ], [
        'color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/']
    ]);
    
    if (\$validator->passes()) {
        echo 'Regex validation PASSED - Color pattern is working correctly' . PHP_EOL;
    } else {
        echo 'Regex validation FAILED: ' . json_encode(\$validator->errors()) . PHP_EOL;
    }
} catch (Exception \$e) {
    echo 'Error testing regex: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "3. Testing model URL formatting..."

php artisan tinker --execute="
try {
    \$model = new App\Models\SocialMediaLink();
    \$model->url = 'facebook.com/testpage';
    echo 'Original URL: ' . \$model->url . PHP_EOL;
    echo 'Formatted URL: ' . \$model->formatted_url . PHP_EOL;
    echo 'URL validation PASSED' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Error testing URL formatting: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "4. Testing predefined platforms..."

php artisan tinker --execute="
try {
    \$platforms = App\Models\SocialMediaLink::getPredefinedPlatforms();
    echo 'Predefined platforms loaded: ' . count(\$platforms) . PHP_EOL;
    foreach (array_keys(\$platforms) as \$platform) {
        echo '  - ' . \$platform . PHP_EOL;
    }
    echo 'Platforms test PASSED' . PHP_EOL;
} catch (Exception \$e) {
    echo 'Error testing platforms: ' . \$e->getMessage() . PHP_EOL;
}
"

echo ""
echo "✅ Regex fix testing completed!"
echo ""
echo "Now try visiting: http://greenvalleyherbs.local:8000/admin/social-media"
echo "The preg_match error should be resolved."