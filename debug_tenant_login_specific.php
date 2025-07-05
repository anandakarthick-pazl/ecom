<?php

/**
 * Tenant Login Specific Diagnostic Script
 * Run this to diagnose tenant-specific login issues
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 TENANT LOGIN DIAGNOSTIC\n";
echo "=========================\n\n";

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

    // 1. Check company record for greenvalleyherbs.local
    echo "🏢 COMPANY VERIFICATION:\n";
    echo "------------------------\n";
    
    $company = $pdo->query("
        SELECT * FROM companies 
        WHERE domain = 'greenvalleyherbs.local'
    ")->fetch();
    
    if ($company) {
        echo "✅ Company found:\n";
        echo "   ID: {$company['id']}\n";
        echo "   Name: {$company['name']}\n";
        echo "   Domain: {$company['domain']}\n";
        echo "   Status: {$company['status']}\n";
        echo "   Slug: {$company['slug']}\n\n";
        
        $companyId = $company['id'];
    } else {
        echo "❌ No company found for domain 'greenvalleyherbs.local'\n";
        echo "🔧 Creating company record...\n";
        
        $pdo->exec("
            INSERT INTO companies (name, domain, slug, status, trial_ends_at, created_at, updated_at) 
            VALUES (
                'Green Valley Herbs', 
                'greenvalleyherbs.local', 
                'greenvalleyherbs', 
                'active', 
                DATE_ADD(NOW(), INTERVAL 30 DAY),
                NOW(), 
                NOW()
            )
        ");
        
        $companyId = $pdo->lastInsertId();
        echo "✅ Created company with ID: $companyId\n\n";
    }

    // 2. Check users for this company
    echo "👤 USER VERIFICATION:\n";
    echo "---------------------\n";
    
    $users = $pdo->prepare("
        SELECT * FROM users 
        WHERE company_id = ? 
        ORDER BY role, email
    ");
    $users->execute([$companyId]);
    $userList = $users->fetchAll();
    
    if (empty($userList)) {
        echo "❌ No users found for company ID: $companyId\n";
        echo "🔧 Creating admin user...\n";
        
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
        echo "   Role: admin\n\n";
        
        // Refresh user list
        $users->execute([$companyId]);
        $userList = $users->fetchAll();
    } else {
        echo "✅ Found " . count($userList) . " user(s) for company:\n";
        foreach ($userList as $user) {
            echo "   - {$user['email']} (Role: {$user['role']}, ID: {$user['id']})\n";
        }
        echo "\n";
    }

    // 3. Check admin users specifically
    echo "🔑 ADMIN USER VERIFICATION:\n";
    echo "---------------------------\n";
    
    $adminUsers = $pdo->prepare("
        SELECT * FROM users 
        WHERE company_id = ? AND role IN ('admin', 'manager')
    ");
    $adminUsers->execute([$companyId]);
    $adminList = $adminUsers->fetchAll();
    
    if (empty($adminList)) {
        echo "❌ No admin/manager users found\n";
        echo "🔧 Promoting first user to admin...\n";
        
        if (!empty($userList)) {
            $pdo->prepare("
                UPDATE users SET role = 'admin' 
                WHERE id = ? AND company_id = ?
            ")->execute([$userList[0]['id'], $companyId]);
            
            echo "✅ Promoted {$userList[0]['email']} to admin\n\n";
        }
    } else {
        echo "✅ Found " . count($adminList) . " admin user(s):\n";
        foreach ($adminList as $admin) {
            echo "   - {$admin['email']} (Role: {$admin['role']})\n";
        }
        echo "\n";
    }

    // 4. Test password verification for first admin user
    echo "🔐 PASSWORD VERIFICATION TEST:\n";
    echo "------------------------------\n";
    
    $testAdmin = $pdo->prepare("
        SELECT * FROM users 
        WHERE company_id = ? AND role IN ('admin', 'manager')
        LIMIT 1
    ");
    $testAdmin->execute([$companyId]);
    $adminUser = $testAdmin->fetch();
    
    if ($adminUser) {
        echo "Testing password for: {$adminUser['email']}\n";
        
        // Test common passwords
        $testPasswords = ['admin123', 'password', '123456', 'admin'];
        $passwordFound = false;
        
        foreach ($testPasswords as $testPass) {
            if (password_verify($testPass, $adminUser['password'])) {
                echo "✅ Password verified: '$testPass'\n";
                $passwordFound = true;
                break;
            }
        }
        
        if (!$passwordFound) {
            echo "⚠️  Could not verify password with common passwords\n";
            echo "🔧 Resetting password to 'admin123'...\n";
            
            $newHash = password_hash('admin123', PASSWORD_DEFAULT);
            $pdo->prepare("
                UPDATE users SET password = ? WHERE id = ?
            ")->execute([$newHash, $adminUser['id']]);
            
            echo "✅ Password reset for {$adminUser['email']}\n";
        }
        echo "\n";
    }

    // 5. Check sessions table
    echo "🗄️  SESSION TABLE VERIFICATION:\n";
    echo "-------------------------------\n";
    
    try {
        $sessionCheck = $pdo->query("SHOW TABLES LIKE 'sessions'")->fetch();
        if ($sessionCheck) {
            echo "✅ Sessions table exists\n";
            
            $sessionCount = $pdo->query("SELECT COUNT(*) as count FROM sessions")->fetch();
            echo "   Active sessions: {$sessionCount['count']}\n";
            
            // Clean old sessions
            $pdo->exec("DELETE FROM sessions WHERE last_activity < " . (time() - 86400));
            echo "   Cleaned old sessions\n";
        } else {
            echo "❌ Sessions table missing\n";
            echo "🔧 Creating sessions table...\n";
            
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
        }
        echo "\n";
    } catch (Exception $e) {
        echo "⚠️  Session table check failed: " . $e->getMessage() . "\n\n";
    }

    // 6. Final test summary
    echo "📋 TENANT LOGIN TEST SUMMARY:\n";
    echo "=============================\n";
    echo "🌐 Domain: greenvalleyherbs.local\n";
    echo "🏢 Company: " . ($company['name'] ?? 'Green Valley Herbs') . " (ID: $companyId)\n";
    echo "👤 Admin Users: " . count($adminList) . "\n";
    
    if (!empty($adminList)) {
        echo "📧 Test Login Credentials:\n";
        echo "   URL: http://greenvalleyherbs.local:8000/login\n";
        echo "   Email: {$adminList[0]['email']}\n";
        echo "   Password: admin123\n";
    }
    
    echo "\n🔍 POTENTIAL ISSUES TO CHECK:\n";
    echo "- Browser cache/cookies (clear them)\n";
    echo "- Hosts file entry: 127.0.0.1 greenvalleyherbs.local\n";
    echo "- Laravel server running: php artisan serve --host=0.0.0.0 --port=8000\n";
    echo "- Check Laravel logs: storage/logs/laravel.log\n";
    
    echo "\n✅ Diagnostic complete!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
