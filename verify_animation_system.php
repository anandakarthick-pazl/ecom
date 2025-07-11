<?php

/**
 * Animation System Verification Script
 * 
 * This script verifies that all animation system components are properly installed
 * and configured for the multitenant ecommerce system.
 */

echo "🎨 Animation System Verification\n";
echo "================================\n\n";

$checks = [];
$errors = [];

// Check if AnimationService exists
if (file_exists(__DIR__ . '/app/Services/AnimationService.php')) {
    $checks[] = "✅ AnimationService.php exists";
} else {
    $errors[] = "❌ AnimationService.php missing";
}

// Check if AnimationComposer exists
if (file_exists(__DIR__ . '/app/View/Composers/AnimationComposer.php')) {
    $checks[] = "✅ AnimationComposer.php exists";
} else {
    $errors[] = "❌ AnimationComposer.php missing";
}

// Check if AnimationHelper exists
if (file_exists(__DIR__ . '/app/Helpers/AnimationHelper.php')) {
    $checks[] = "✅ AnimationHelper.php exists";
} else {
    $errors[] = "❌ AnimationHelper.php missing";
}

// Check if ThemeServiceProvider is updated
$themeProvider = file_get_contents(__DIR__ . '/app/Providers/ThemeServiceProvider.php');
if (strpos($themeProvider, 'AnimationComposer') !== false) {
    $checks[] = "✅ ThemeServiceProvider updated with AnimationComposer";
} else {
    $errors[] = "❌ ThemeServiceProvider not updated";
}

// Check if SettingsController is updated
$settingsController = file_get_contents(__DIR__ . '/app/Http/Controllers/Admin/SettingsController.php');
if (strpos($settingsController, 'AnimationService::clearCache') !== false) {
    $checks[] = "✅ SettingsController updated with animation cache clearing";
} else {
    $errors[] = "❌ SettingsController not updated";
}

// Check if main layout is updated
$layout = file_get_contents(__DIR__ . '/resources/views/layouts/app.blade.php');
if (strpos($layout, 'animationCSS') !== false) {
    $checks[] = "✅ Main layout updated with animation support";
} else {
    $errors[] = "❌ Main layout not updated";
}

// Check if animation test view exists
if (file_exists(__DIR__ . '/resources/views/animation-test.blade.php')) {
    $checks[] = "✅ Animation test view exists";
} else {
    $errors[] = "❌ Animation test view missing";
}

// Check if HomeController is updated
$homeController = file_get_contents(__DIR__ . '/app/Http/Controllers/HomeController.php');
if (strpos($homeController, 'animationTest') !== false) {
    $checks[] = "✅ HomeController updated with animation test route";
} else {
    $errors[] = "❌ HomeController animation test method missing";
}

// Check if animation demo component exists
if (file_exists(__DIR__ . '/resources/views/components/animation-demo.blade.php')) {
    $checks[] = "✅ Animation demo component exists";
} else {
    $errors[] = "❌ Animation demo component missing";
}

// Check if HasPagination trait is fixed
$paginationTrait = file_get_contents(__DIR__ . '/app/Traits/HasPagination.php');
if (strpos($paginationTrait, 'isset($settings[\'default\'])') !== false) {
    $checks[] = "✅ HasPagination trait fixed";
} else {
    $errors[] = "❌ HasPagination trait not fixed";
}

// Display results
echo "COMPONENT CHECKS:\n";
echo "-----------------\n";
foreach ($checks as $check) {
    echo $check . "\n";
}

if (!empty($errors)) {
    echo "\nERRORS FOUND:\n";
    echo "-------------\n";
    foreach ($errors as $error) {
        echo $error . "\n";
    }
}

echo "\n";

// Summary
$totalChecks = count($checks) + count($errors);
$passedChecks = count($checks);

echo "SUMMARY:\n";
echo "--------\n";
echo "Passed: {$passedChecks}/{$totalChecks} checks\n";

if (empty($errors)) {
    echo "🎉 All animation system components are properly installed!\n\n";
    
    echo "NEXT STEPS:\n";
    echo "-----------\n";
    echo "1. Add this route to your web.php file:\n";
    echo "   Route::get('/animation-test', [HomeController::class, 'animationTest'])->name('animation.test');\n\n";
    echo "2. Clear application cache:\n";
    echo "   php artisan cache:clear\n";
    echo "   php artisan view:clear\n";
    echo "   php artisan config:clear\n\n";
    echo "3. Access your admin panel at: /admin/settings\n";
    echo "4. Go to 'Frontend Animations' tab\n";
    echo "5. Enable animations and configure settings\n";
    echo "6. Visit /animation-test to test the system\n\n";
    echo "✨ Your animation system is ready to use!\n";
} else {
    echo "⚠️  Please fix the errors above before proceeding.\n";
}

echo "\n🔗 For detailed documentation, see: ANIMATION_SYSTEM_DOCUMENTATION.md\n";
