<?php

// Test file to verify HasTenant trait syntax
require_once __DIR__ . '/../../vendor/autoload.php';

// This is just a syntax check - don't run this in production
class TestTraitSyntax
{
    public static function checkHasTenantTrait()
    {
        echo "Checking HasTenant trait syntax...\n";
        
        // Check if the trait file exists and can be included
        $traitPath = __DIR__ . '/../Traits/HasTenant.php';
        
        if (file_exists($traitPath)) {
            echo "✅ HasTenant trait file exists\n";
            
            // Check for syntax errors
            $output = [];
            $returnCode = 0;
            exec("php -l " . escapeshellarg($traitPath), $output, $returnCode);
            
            if ($returnCode === 0) {
                echo "✅ HasTenant trait syntax is valid\n";
            } else {
                echo "❌ HasTenant trait has syntax errors:\n";
                echo implode("\n", $output) . "\n";
            }
        } else {
            echo "❌ HasTenant trait file not found\n";
        }
        
        echo "Syntax check complete.\n";
    }
}

// Only run if called directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'] ?? '')) {
    TestTraitSyntax::checkHasTenantTrait();
}
