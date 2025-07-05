<?php

/**
 * Route Conflict Detection Script
 * 
 * This script scans for potential route naming conflicts
 * Run this via: php check_route_conflicts.php
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 ROUTE CONFLICT DETECTION\n";
echo "===========================\n\n";

// Check if we're in the Laravel directory
if (!file_exists('artisan')) {
    die("❌ Error: This script must be run from the Laravel root directory.\n");
}

try {
    echo "📋 Checking route files for potential conflicts...\n\n";
    
    // Route files to check
    $routeFiles = [
        'routes/web.php',
        'routes/api.php', 
        'routes/auth.php',
        'routes/super_admin.php',
        'routes/landing.php'
    ];
    
    $allRouteNames = [];
    $conflicts = [];
    
    foreach ($routeFiles as $file) {
        if (file_exists($file)) {
            echo "🔍 Checking: $file\n";
            
            $content = file_get_contents($file);
            
            // Find all route names using regex
            preg_match_all('/->name\([\'"]([^\'"]+)[\'"]\)/', $content, $matches);
            
            if (!empty($matches[1])) {
                foreach ($matches[1] as $routeName) {
                    if (isset($allRouteNames[$routeName])) {
                        $conflicts[] = [
                            'name' => $routeName,
                            'files' => [$allRouteNames[$routeName], $file]
                        ];
                        echo "   ❌ CONFLICT: '$routeName' found in both {$allRouteNames[$routeName]} and $file\n";
                    } else {
                        $allRouteNames[$routeName] = $file;
                        echo "   ✅ '$routeName'\n";
                    }
                }
            }
            echo "\n";
        }
    }
    
    if (empty($conflicts)) {
        echo "🎉 NO ROUTE CONFLICTS FOUND!\n";
        echo "All route names are unique across files.\n\n";
    } else {
        echo "⚠️  ROUTE CONFLICTS DETECTED:\n";
        echo "============================\n";
        
        foreach ($conflicts as $conflict) {
            echo "❌ Route name: '{$conflict['name']}'\n";
            echo "   Defined in: " . implode(' and ', $conflict['files']) . "\n\n";
        }
    }
    
    // Check for duplicate route patterns within route groups
    echo "🔍 CHECKING FOR GROUPED ROUTE CONFLICTS:\n";
    echo "========================================\n";
    
    // Check web.php for potential group conflicts
    if (file_exists('routes/web.php')) {
        $webContent = file_get_contents('routes/web.php');
        
        // Look for admin.settings routes specifically
        preg_match_all('/Route::prefix\([\'"]settings[\'"]\)->name\([\'"]settings\.[\'"]\).*?->group\(function.*?\{(.*?)\}/s', $webContent, $groupMatches);
        
        if (!empty($groupMatches[1])) {
            foreach ($groupMatches[1] as $groupContent) {
                preg_match_all('/->name\([\'"]([^\'"]+)[\'"]\)/', $groupContent, $nameMatches);
                
                $groupRouteNames = [];
                foreach ($nameMatches[1] as $name) {
                    if (isset($groupRouteNames[$name])) {
                        echo "❌ DUPLICATE in settings group: '$name'\n";
                    } else {
                        $groupRouteNames[$name] = true;
                        echo "✅ Settings route: '$name'\n";
                    }
                }
            }
        }
    }
    
    echo "\n📊 ROUTE SUMMARY:\n";
    echo "=================\n";
    echo "Total unique route names found: " . count($allRouteNames) . "\n";
    echo "Route conflicts detected: " . count($conflicts) . "\n";
    
    if (count($conflicts) === 0) {
        echo "\n✅ Your routing configuration is clean!\n";
    } else {
        echo "\n⚠️  Please resolve the conflicts listed above.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error during route conflict check: " . $e->getMessage() . "\n";
}
