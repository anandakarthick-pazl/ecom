<?php

/**
 * Verification script for access level fix
 * This script checks if the access level conflicts have been resolved
 * Run this via: php artisan tinker < verify_access_level_fix.php
 */

echo "=== Verifying Access Level Fix ===\n";

try {
    // Test 1: Check if controllers can be instantiated without errors
    echo "1. Testing controller instantiation...\n";
    
    try {
        $app = app();
        
        // Test RoleController
        $roleController = new \App\Http\Controllers\Admin\RoleController();
        echo "   ✓ RoleController: SUCCESS\n";
        
        // Test PermissionController  
        $permissionController = new \App\Http\Controllers\Admin\PermissionController();
        echo "   ✓ PermissionController: SUCCESS\n";
        
    } catch (Exception $e) {
        echo "   ✗ Controller instantiation FAILED: " . $e->getMessage() . "\n";
    }
    
    // Test 2: Check method inheritance
    echo "2. Testing method inheritance...\n";
    
    try {
        $roleController = new \App\Http\Controllers\Admin\RoleController();
        $permissionController = new \App\Http\Controllers\Admin\PermissionController();
        
        // Use reflection to check if getCurrentCompanyId method exists and is accessible
        $roleReflection = new ReflectionClass($roleController);
        $permissionReflection = new ReflectionClass($permissionController);
        
        // Check if method exists and has correct visibility
        if ($roleReflection->hasMethod('getCurrentCompanyId')) {
            $method = $roleReflection->getMethod('getCurrentCompanyId');
            $visibility = $method->isProtected() ? 'protected' : ($method->isPrivate() ? 'private' : 'public');
            echo "   ✓ RoleController::getCurrentCompanyId() visibility: {$visibility}\n";
        } else {
            echo "   ✗ RoleController::getCurrentCompanyId() method not found\n";
        }
        
        if ($permissionReflection->hasMethod('getCurrentCompanyId')) {
            $method = $permissionReflection->getMethod('getCurrentCompanyId');
            $visibility = $method->isProtected() ? 'protected' : ($method->isPrivate() ? 'private' : 'public');
            echo "   ✓ PermissionController::getCurrentCompanyId() visibility: {$visibility}\n";
        } else {
            echo "   ✗ PermissionController::getCurrentCompanyId() method not found\n";
        }
        
    } catch (Exception $e) {
        echo "   ✗ Method inheritance test FAILED: " . $e->getMessage() . "\n";
    }
    
    // Test 3: Check parent class method
    echo "3. Testing parent class method...\n";
    
    try {
        $baseController = new \App\Http\Controllers\Admin\BaseAdminController();
        $reflection = new ReflectionClass($baseController);
        
        if ($reflection->hasMethod('getCurrentCompanyId')) {
            $method = $reflection->getMethod('getCurrentCompanyId');
            $visibility = $method->isProtected() ? 'protected' : ($method->isPrivate() ? 'private' : 'public');
            echo "   ✓ BaseAdminController::getCurrentCompanyId() visibility: {$visibility}\n";
        } else {
            echo "   ✗ BaseAdminController::getCurrentCompanyId() method not found\n";
        }
        
    } catch (Exception $e) {
        echo "   ✗ Parent class test FAILED: " . $e->getMessage() . "\n";
    }
    
    // Test 4: Simulate route access (if possible)
    echo "4. Testing simulated route access...\n";
    
    try {
        // This test checks if the classes can be loaded without fatal errors
        $classes = [
            '\\App\\Http\\Controllers\\Admin\\RoleController',
            '\\App\\Http\\Controllers\\Admin\\PermissionController'
        ];
        
        foreach ($classes as $class) {
            if (class_exists($class)) {
                echo "   ✓ {$class}: Class loads successfully\n";
            } else {
                echo "   ✗ {$class}: Class not found\n";
            }
        }
        
    } catch (Exception $e) {
        echo "   ✗ Route simulation test FAILED: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Fix Verification Results ===\n";
    echo "If all tests show SUCCESS, the access level conflicts are resolved.\n";
    echo "You should now be able to access:\n";
    echo "- http://greenvalleyherbs.local:8000/admin/roles\n";
    echo "- http://greenvalleyherbs.local:8000/admin/permissions\n";
    echo "\nIf you still see errors, clear the application cache and try again.\n";
    
} catch (Exception $e) {
    echo "ERROR during verification: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

