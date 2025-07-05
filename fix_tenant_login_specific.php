<?php

/**
 * Tenant Company Admin Login Fix Script
 * 
 * Specifically fixes company admin login issues on tenant domains
 * Run this via: php fix_tenant_login_specific.php
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔧 TENANT COMPANY ADMIN LOGIN FIX\n";
echo "=================================\n\n";

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

    // 1. Ensure greenvalleyherbs.local company exists and is active
    echo "🏢 VERIFYING COMPANY SETUP:\n";
    echo "---------------------------\n";
    
    $company = $pdo->query("SELECT * FROM companies WHERE domain = 'greenvalleyherbs.local'")->fetch();
    
    if (!$company) {
        echo "⚠️  Company not found. Creating greenvalleyherbs.local...\n";
        $pdo->exec("
            INSERT INTO companies (name, domain, slug, status, trial_ends_at, subscription_ends_at, created_at, updated_at) 
            VALUES (
                'Green Valley Herbs', 
                'greenvalleyherbs.local', 
                'greenvalleyherbs', 
                'active', 
                DATE_ADD(NOW(), INTERVAL 30 DAY),
                DATE_ADD(NOW(), INTERVAL 365 DAY),
                NOW(), 
                NOW()
            )
        ");
        $companyId = $pdo->lastInsertId();
        echo "✅ Created company with ID: $companyId\n";
    } else {
        $companyId = $company['id'];
        echo "✅ Company found with ID: $companyId\n";
        
        // Ensure company is active
        if ($company['status'] !== 'active') {
            echo "⚠️  Company status is '{$company['status']}'. Setting to active...\n";
            $pdo->prepare("UPDATE companies SET status = 'active' WHERE id = ?")->execute([$companyId]);
            echo "✅ Company status updated to active\n";
        }
    }

    // 2. Ensure admin user exists and has correct credentials
    echo "\n👤 VERIFYING ADMIN USERS:\n";
    echo "-------------------------\n";
    
    $adminUsers = $pdo->prepare("
        SELECT * FROM users 
        WHERE company_id = ? AND role IN ('admin', 'manager')
        ORDER BY role = 'admin' DESC, created_at ASC
    ");
    $adminUsers->execute([$companyId]);
    $admins = $adminUsers->fetchAll();
    
    if (empty($admins)) {
        echo "⚠️  No admin users found. Creating default admin...\n";
        
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("
            INSERT INTO users (name, email, password, role, company_id, email_verified_at, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW(), NOW())
        ")->execute([
            'Admin User',
            'admin@greenvalleyherbs.local',
            $hashedPassword,
            'admin',
            $companyId
        ]);
        
        echo "✅ Created admin user:\n";
        echo "   Email: admin@greenvalleyherbs.local\n";
        echo "   Password: admin123\n";
        echo "   Role: admin\n";
        echo "   Company ID: $companyId\n";
    } else {
        echo "✅ Found " . count($admins) . " admin user(s):\n";
        
        foreach ($admins as $index => $admin) {
            echo "   " . ($index + 1) . ". {$admin['email']} (Role: {$admin['role']}, ID: {$admin['id']})\n";
        }
        
        // Test password for first admin and reset if needed
        $firstAdmin = $admins[0];
        $testPasswords = ['admin123', 'password', '123456', 'admin'];
        $passwordWorking = false;
        
        echo "\n🔐 Testing password for {$firstAdmin['email']}...\n";
        
        foreach ($testPasswords as $testPass) {
            if (password_verify($testPass, $firstAdmin['password'])) {
                echo "✅ Password '$testPass' is working\n";
                $passwordWorking = true;
                break;
            }
        }
        
        if (!$passwordWorking) {
            echo "⚠️  Password not working with common passwords. Resetting to 'admin123'...\n";
            
            $newHash = password_hash('admin123', PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$newHash, $firstAdmin['id']]);
            
            echo "✅ Password reset for {$firstAdmin['email']} to: admin123\n";
        }
    }

    // 3. Verify user company_id matches exactly
    echo "\n🔗 VERIFYING USER-COMPANY RELATIONSHIP:\n";
    echo "--------------------------------------\n";
    
    $userCompanyCheck = $pdo->prepare("
        SELECT u.*, c.name as company_name, c.domain 
        FROM users u 
        JOIN companies c ON u.company_id = c.id 
        WHERE c.domain = 'greenvalleyherbs.local' 
        AND u.role IN ('admin', 'manager')
    ");
    $userCompanyCheck->execute();
    $userCompanyData = $userCompanyCheck->fetchAll();
    
    if (empty($userCompanyData)) {
        echo "❌ No users properly linked to company!\n";
        
        // Fix any orphaned admin users by linking them to our company
        $orphanedAdmins = $pdo->prepare("SELECT * FROM users WHERE role IN ('admin', 'manager') AND company_id != ?");
        $orphanedAdmins->execute([$companyId]);
        $orphans = $orphanedAdmins->fetchAll();
        
        if (!empty($orphans)) {
            echo "⚠️  Found orphaned admin users. Linking to company...\n";
            foreach ($orphans as $orphan) {
                $pdo->prepare("UPDATE users SET company_id = ? WHERE id = ?")->execute([$companyId, $orphan['id']]);
                echo "✅ Linked {$orphan['email']} to company $companyId\n";
            }
        }
    } else {
        echo "✅ Found properly linked users:\n";
        foreach ($userCompanyData as $user) {
            echo "   - {$user['email']} → {$user['company_name']} (Company ID: {$user['company_id']})\n";
        }
    }

    // 4. Clean up sessions and ensure database sessions table exists
    echo "\n🗄️  SESSION MANAGEMENT SETUP:\n";
    echo "-----------------------------\n";
    
    try {
        // Check if sessions table exists
        $sessionTableExists = $pdo->query("SHOW TABLES LIKE 'sessions'")->fetch();
        
        if (!$sessionTableExists) {
            echo "⚠️  Sessions table missing. Creating...\n";
            $pdo->exec("
                CREATE TABLE `sessions` (
                    `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                    `user_id` bigint(20) unsigned DEFAULT NULL,
                    `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                    `user_agent` text COLLATE utf8mb4_unicode_ci,
                    `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                    `last_activity` int(11) NOT NULL,
                    PRIMARY KEY (`id`),
                    KEY `sessions_user_id_index` (`user_id`),
                    KEY `sessions_last_activity_index` (`last_activity`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            echo "✅ Sessions table created\n";
        } else {
            echo "✅ Sessions table exists\n";
        }
        
        // Clear old sessions
        $deletedSessions = $pdo->exec("DELETE FROM sessions WHERE last_activity < " . (time() - 86400));
        echo "✅ Cleaned $deletedSessions old sessions\n";
        
    } catch (Exception $e) {
        echo "⚠️  Session table setup warning: " . $e->getMessage() . "\n";
    }

    // 5. Test the complete login flow
    echo "\n🧪 TESTING LOGIN FLOW:\n";
    echo "----------------------\n";
    
    // Get the admin user to test with
    $testUser = $pdo->prepare("
        SELECT * FROM users 
        WHERE company_id = ? AND role IN ('admin', 'manager')
        LIMIT 1
    ");
    $testUser->execute([$companyId]);
    $adminUser = $testUser->fetch();
    
    if ($adminUser) {
        echo "✅ Test user: {$adminUser['email']}\n";
        echo "✅ User ID: {$adminUser['id']}\n";
        echo "✅ Company ID: {$adminUser['company_id']}\n";
        echo "✅ Role: {$adminUser['role']}\n";
        
        // Verify password hash
        if (password_verify('admin123', $adminUser['password'])) {
            echo "✅ Password verification: WORKING\n";
        } else {
            echo "❌ Password verification: FAILED\n";
        }
        
        // Check company match
        if ((int)$adminUser['company_id'] === (int)$companyId) {
            echo "✅ Company relationship: CORRECT\n";
        } else {
            echo "❌ Company relationship: INCORRECT ({$adminUser['company_id']} != $companyId)\n";
        }
        
        // Check role
        if (in_array($adminUser['role'], ['admin', 'manager'])) {
            echo "✅ User role: AUTHORIZED\n";
        } else {
            echo "❌ User role: UNAUTHORIZED ({$adminUser['role']})\n";
        }
    } else {
        echo "❌ No test user found!\n";
    }

    // 6. Final summary and instructions
    echo "\n📋 TENANT LOGIN FIX SUMMARY:\n";
    echo "============================\n";
    echo "🌐 Domain: greenvalleyherbs.local\n";
    echo "🏢 Company: Green Valley Herbs (ID: $companyId)\n";
    
    $finalAdminCheck = $pdo->prepare("SELECT email, role FROM users WHERE company_id = ? AND role IN ('admin', 'manager')");
    $finalAdminCheck->execute([$companyId]);
    $finalAdmins = $finalAdminCheck->fetchAll();
    
    echo "👤 Admin Users: " . count($finalAdmins) . "\n";
    foreach ($finalAdmins as $admin) {
        echo "   - {$admin['email']} ({$admin['role']})\n";
    }
    
    echo "\n🎯 TEST LOGIN NOW:\n";
    echo "==================\n";
    echo "URL: http://greenvalleyherbs.local:8000/login\n";
    echo "Email: {$finalAdmins[0]['email']}\n";
    echo "Password: admin123\n";
    echo "Expected: Redirect to /admin/dashboard\n";
    
    echo "\n🔍 IF LOGIN STILL FAILS:\n";
    echo "========================\n";
    echo "1. Clear browser cache and cookies\n";
    echo "2. Try incognito/private mode\n";
    echo "3. Check Laravel logs: storage/logs/laravel.log\n";
    echo "4. Verify hosts file: 127.0.0.1 greenvalleyherbs.local\n";
    echo "5. Check server is running: php artisan serve --host=0.0.0.0 --port=8000\n";
    
    echo "\n✅ Tenant login fix completed!\n";

} catch (Exception $e) {
    echo "❌ Error during tenant login fix: " . $e->getMessage() . "\n";
    echo "Please check your database connection and configuration.\n";
}
