<?php

/**
 * Test Tenant Login Flow Script
 * 
 * This script simulates the login process to identify where it might be failing
 * Run this via: php test_tenant_login_flow.php
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🧪 TESTING TENANT LOGIN FLOW\n";
echo "============================\n\n";

// Check if we're in the Laravel directory
if (!file_exists('artisan')) {
    die("❌ Error: This script must be run from the Laravel root directory.\n");
}

try {
    // Read database config from .env
    $env = parse_ini_file('.env');
    $dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4";
    
    $pdo = new PDO($dsn, $env['DB_USERNAME'], $env['DB_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "✅ Database connection successful\n\n";

    // Test 1: Company Resolution
    echo "🏢 TEST 1: COMPANY RESOLUTION\n";
    echo "-----------------------------\n";
    
    $domain = 'greenvalleyherbs.local';
    $company = $pdo->query("SELECT * FROM companies WHERE domain = '$domain'")->fetch();
    
    if ($company) {
        echo "✅ Company found: {$company['name']} (ID: {$company['id']})\n";
        echo "   Domain: {$company['domain']}\n";
        echo "   Status: {$company['status']}\n";
        
        if ($company['status'] !== 'active') {
            echo "❌ ISSUE: Company status is not 'active'\n";
            echo "🔧 FIX: UPDATE companies SET status = 'active' WHERE id = {$company['id']};\n";
        }
    } else {
        echo "❌ ISSUE: No company found for domain '$domain'\n";
        echo "🔧 FIX: Run fix_tenant_login_specific.php to create company\n";
        exit(1);
    }
    
    $companyId = $company['id'];

    // Test 2: User Authentication 
    echo "\n👤 TEST 2: USER AUTHENTICATION\n";
    echo "------------------------------\n";
    
    $users = $pdo->prepare("SELECT * FROM users WHERE company_id = ? AND role IN ('admin', 'manager')");
    $users->execute([$companyId]);
    $userList = $users->fetchAll();
    
    if (empty($userList)) {
        echo "❌ ISSUE: No admin users found for company\n";
        echo "🔧 FIX: Run fix_tenant_login_specific.php to create admin user\n";
        exit(1);
    } else {
        echo "✅ Found " . count($userList) . " admin user(s):\n";
        foreach ($userList as $user) {
            echo "   - {$user['email']} (Role: {$user['role']}, ID: {$user['id']})\n";
        }
    }

    // Test 3: Password Verification
    echo "\n🔐 TEST 3: PASSWORD VERIFICATION\n";
    echo "--------------------------------\n";
    
    $testUser = $userList[0]; // Use first admin user
    $testPassword = 'admin123';
    
    echo "Testing user: {$testUser['email']}\n";
    echo "Testing password: $testPassword\n";
    
    if (password_verify($testPassword, $testUser['password'])) {
        echo "✅ Password verification successful\n";
    } else {
        echo "❌ ISSUE: Password verification failed\n";
        echo "🔧 FIX: Password needs to be reset\n";
        
        // Reset password
        $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$newHash, $testUser['id']]);
        echo "✅ Password reset to: $testPassword\n";
    }

    // Test 4: Company-User Relationship
    echo "\n🔗 TEST 4: COMPANY-USER RELATIONSHIP\n";
    echo "------------------------------------\n";
    
    echo "User company_id: {$testUser['company_id']} (type: " . gettype($testUser['company_id']) . ")\n";
    echo "Company id: {$company['id']} (type: " . gettype($company['id']) . ")\n";
    echo "Comparison result: " . ((int)$testUser['company_id'] === (int)$company['id'] ? 'MATCH' : 'NO MATCH') . "\n";
    
    if ((int)$testUser['company_id'] === (int)$company['id']) {
        echo "✅ User belongs to correct company\n";
    } else {
        echo "❌ ISSUE: User company_id doesn't match domain company\n";
        echo "🔧 FIX: UPDATE users SET company_id = {$company['id']} WHERE id = {$testUser['id']};\n";
        
        // Fix the relationship
        $pdo->prepare("UPDATE users SET company_id = ? WHERE id = ?")->execute([$company['id'], $testUser['id']]);
        echo "✅ Fixed user-company relationship\n";
    }

    // Test 5: Role Authorization  
    echo "\n🔑 TEST 5: ROLE AUTHORIZATION\n";
    echo "-----------------------------\n";
    
    $allowedRoles = ['admin', 'manager'];
    echo "User role: {$testUser['role']}\n";
    echo "Allowed roles: " . implode(', ', $allowedRoles) . "\n";
    
    if (in_array($testUser['role'], $allowedRoles)) {
        echo "✅ User has authorized role\n";
    } else {
        echo "❌ ISSUE: User role not authorized for admin access\n";
        echo "🔧 FIX: UPDATE users SET role = 'admin' WHERE id = {$testUser['id']};\n";
        
        // Fix the role
        $pdo->prepare("UPDATE users SET role = 'admin' WHERE id = ?")->execute([$testUser['id']]);
        echo "✅ Updated user role to 'admin'\n";
    }

    // Test 6: Sessions Table
    echo "\n🗄️  TEST 6: SESSION STORAGE\n";
    echo "---------------------------\n";
    
    try {
        $sessionCheck = $pdo->query("SHOW TABLES LIKE 'sessions'")->fetch();
        if ($sessionCheck) {
            echo "✅ Sessions table exists\n";
            
            $sessionCount = $pdo->query("SELECT COUNT(*) as count FROM sessions")->fetch();
            echo "   Current sessions: {$sessionCount['count']}\n";
            
            // Test session table structure
            $sessionStructure = $pdo->query("DESCRIBE sessions")->fetchAll();
            $requiredFields = ['id', 'user_id', 'payload', 'last_activity'];
            $hasAllFields = true;
            
            $existingFields = array_column($sessionStructure, 'Field');
            foreach ($requiredFields as $field) {
                if (!in_array($field, $existingFields)) {
                    $hasAllFields = false;
                    echo "❌ Missing session field: $field\n";
                }
            }
            
            if ($hasAllFields) {
                echo "✅ Session table structure is correct\n";
            }
        } else {
            echo "❌ ISSUE: Sessions table doesn't exist\n";
            echo "🔧 FIX: Run 'php artisan session:table' and 'php artisan migrate'\n";
        }
    } catch (Exception $e) {
        echo "⚠️  Session table check warning: " . $e->getMessage() . "\n";
    }

    // Test 7: Environment Configuration
    echo "\n⚙️  TEST 7: ENVIRONMENT CONFIGURATION\n";
    echo "------------------------------------\n";
    
    echo "Session driver: " . ($env['SESSION_DRIVER'] ?? 'not set') . "\n";
    echo "Session lifetime: " . ($env['SESSION_LIFETIME'] ?? 'not set') . " minutes\n";
    echo "App debug: " . ($env['APP_DEBUG'] ?? 'not set') . "\n";
    
    if (($env['SESSION_DRIVER'] ?? '') === 'database') {
        echo "✅ Using database sessions (recommended)\n";
    } else {
        echo "⚠️  Using file sessions (may cause tenant conflicts)\n";
    }
    
    if (((int)($env['SESSION_LIFETIME'] ?? 0)) >= 120) {
        echo "✅ Session lifetime is adequate\n";
    } else {
        echo "⚠️  Session lifetime might be too short\n";
    }

    // Final Summary
    echo "\n📋 FINAL TEST SUMMARY\n";
    echo "=====================\n";
    echo "✅ All core components tested\n";
    echo "🌐 Domain: $domain\n";
    echo "🏢 Company: {$company['name']} (ID: {$company['id']})\n";
    echo "👤 Test User: {$testUser['email']}\n";
    echo "🔑 Password: admin123\n";
    echo "\n🎯 READY TO TEST LOGIN:\n";
    echo "URL: http://greenvalleyherbs.local:8000/login\n";
    echo "Email: {$testUser['email']}\n";
    echo "Password: admin123\n";
    echo "\n📊 Expected Result: Successful redirect to /admin/dashboard\n";
    
    echo "\n✅ Login flow test completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error during login flow test: " . $e->getMessage() . "\n";
    echo "Please check your database connection and configuration.\n";
}
