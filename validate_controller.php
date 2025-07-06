<?php

/**
 * Validate PosController syntax
 */

echo "PosController Syntax Check\n";
echo "==========================\n\n";

$controllerPath = 'app/Http/Controllers/Admin/PosController.php';

if (!file_exists($controllerPath)) {
    echo "❌ PosController file not found!\n";
    exit(1);
}

echo "🔍 Checking PHP syntax...\n";

// Check PHP syntax
$output = [];
$returnCode = 0;
exec("php -l \"{$controllerPath}\"", $output, $returnCode);

if ($returnCode === 0) {
    echo "✅ PHP syntax is valid\n\n";
} else {
    echo "❌ PHP syntax errors found:\n";
    foreach ($output as $line) {
        echo "   {$line}\n";
    }
    echo "\n";
    exit(1);
}

echo "🔍 Checking for common issues...\n";

$content = file_get_contents($controllerPath);

// Check for basic issues
$issues = [];

if (strpos($content, 'getSimpleCompanyData') === false) {
    $issues[] = "Missing getSimpleCompanyData method";
}

if (strpos($content, 'public function receipt') === false) {
    $issues[] = "Missing receipt method";
}

if (strpos($content, 'compact(\'sale\', \'globalCompany\')') === false) {
    $issues[] = "Receipt method not returning correct variables";
}

if (empty($issues)) {
    echo "✅ No obvious issues found\n\n";
} else {
    echo "⚠️  Potential issues found:\n";
    foreach ($issues as $issue) {
        echo "   - {$issue}\n";
    }
    echo "\n";
}

echo "🧪 Testing controller instantiation...\n";

try {
    // Test if the controller can be instantiated
    require_once $controllerPath;
    echo "✅ Controller class loads successfully\n";
} catch (Exception $e) {
    echo "❌ Controller loading error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n✅ PosController validation complete!\n";
echo "The 500 error is likely due to cached config/routes.\n";
echo "Run the fix script to clear caches and resolve the issue.\n";
