<?php

/**
 * Verification script for currentTenant() method fix
 * Run this via: php artisan tinker < verify_current_tenant_fix.php
 * or copy and paste into php artisan tinker
 */

echo "=== Testing currentTenant() Method Fix ===\n";

try {
    // Test 1: Check if the macro is registered
    echo "1. Testing if currentTenant macro is registered...\n";
    
    // Try using currentTenant on a query builder
    $query = \App\Models\Product::query();
    $hasCurrentTenantMethod = method_exists($query, 'currentTenant');
    
    echo "   currentTenant method available: " . ($hasCurrentTenantMethod ? "YES" : "NO") . "\n";
    
    // Test 2: Try calling currentTenant method
    echo "2. Testing currentTenant() method call...\n";
    
    try {
        $result = \App\Models\Product::currentTenant();
        echo "   currentTenant() call: SUCCESS\n";
        echo "   Query class: " . get_class($result) . "\n";
    } catch (Exception $e) {
        echo "   currentTenant() call: FAILED - " . $e->getMessage() . "\n";
    }
    
    // Test 3: Check tenant resolution
    echo "3. Testing tenant resolution...\n";
    
    $tenant = app('tenant');
    if ($tenant) {
        echo "   Tenant resolved: YES (ID: {$tenant->id}, Name: {$tenant->name})\n";
    } else {
        echo "   Tenant resolved: NO\n";
        echo "   This is expected if not in a tenant context (e.g., running in artisan)\n";
    }
    
    // Test 4: Test other macros
    echo "4. Testing other query macros...\n";
    
    try {
        $result = \App\Models\Product::forTenant(1);
        echo "   forTenant() macro: SUCCESS\n";
    } catch (Exception $e) {
        echo "   forTenant() macro: FAILED - " . $e->getMessage() . "\n";
    }
    
    try {
        $result = \App\Models\Product::query()->getCurrentTenant();
        echo "   getCurrentTenant() macro: SUCCESS\n";
    } catch (Exception $e) {
        echo "   getCurrentTenant() macro: FAILED - " . $e->getMessage() . "\n";
    }
    
    // Test 5: Test with trait methods
    echo "5. Testing existing trait methods...\n";
    
    try {
        $result = \App\Models\Product::query()->currentTenant();
        echo "   Trait currentTenant scope: SUCCESS\n";
    } catch (Exception $e) {
        echo "   Trait currentTenant scope: FAILED - " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Fix Verification Complete ===\n";
    echo "The currentTenant() method should now be available on all Eloquent queries.\n";
    echo "If all tests pass, the original error should be resolved.\n";
    
} catch (Exception $e) {
    echo "ERROR during verification: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

