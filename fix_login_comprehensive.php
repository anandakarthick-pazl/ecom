<?php

/**
 * Comprehensive Login Fix Script
 * 
 * This script fixes all common login issues in the multi-tenant application
 * Run this via: php fix_login_comprehensive.php
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔧 Starting Comprehensive Login Fix...\n\n";

// Check if we're in the Laravel directory
if (!file_exists('artisan')) {
    die("❌ Error: This script must be run from the Laravel root directory.\n");
}

try {
    // 1. Create sessions table if using database driver
    echo "📊 Setting up database sessions...\n";
    
    $sessionTableSQL = "
    CREATE TABLE IF NOT EXISTS `sessions` (
        `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `user_id` bigint(20) unsigned DEFAULT NULL,
        `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `user_agent` text COLLATE utf8mb4_unicode_ci,
        `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
        `last_activity` int(11) NOT NULL,
        PRIMARY KEY (`id`),
        KEY `sessions_user_id_index` (`user_id`),
        KEY `sessions_last_activity_index` (`last_activity`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    execSQL($sessionTableSQL);
    echo "✅ Sessions table created/verified\n";

    // 2. Verify companies table and sample data
    echo "🏢 Checking companies table...\n";
    
    $companies = execSQL("SELECT * FROM companies WHERE domain = 'greenvalleyherbs.local'");
    
    if (empty($companies)) {
        echo "⚠️  Creating sample company for greenvalleyherbs.local...\n";
        execSQL("
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
        $companyId = execSQL("SELECT LAST_INSERT_ID() as id")[0]['id'];
        echo "✅ Created company with ID: $companyId\n";
    } else {
        $companyId = $companies[0]['id'];
        echo "✅ Found existing company with ID: $companyId\n";
    }

    // 3. Verify admin user exists
    echo "👤 Checking admin users...\n";
    
    $adminUsers = execSQL("
        SELECT * FROM users 
        WHERE company_id = ? AND role IN ('admin', 'manager')
    ", [$companyId]);
    
    if (empty($adminUsers)) {
        echo "⚠️  Creating admin user...\n";
        
        // Create admin user with default password
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        execSQL("
            INSERT INTO users (name, email, password, role, company_id, email_verified_at, created_at, updated_at) 
            VALUES (
                'Admin User', 
                'admin@greenvalleyherbs.local', 
                ?, 
                'admin', 
                ?, 
                NOW(), 
                NOW(), 
                NOW()
            )
        ", [$hashedPassword, $companyId]);
        
        echo "✅ Created admin user:\n";
        echo "   Email: admin@greenvalleyherbs.local\n";
        echo "   Password: admin123\n";
        echo "   ⚠️  Please change this password after first login!\n";
    } else {
        echo "✅ Found " . count($adminUsers) . " admin user(s)\n";
        foreach ($adminUsers as $user) {
            echo "   - " . $user['email'] . " (Role: " . $user['role'] . ")\n";
        }
    }

    // 4. Clear all caches
    echo "🧹 Clearing application caches...\n";
    
    $cacheCommands = [
        'config:clear',
        'cache:clear', 
        'route:clear',
        'view:clear',
        'session:flush'
    ];
    
    foreach ($cacheCommands as $command) {
        exec("php artisan $command", $output, $return_var);
        if ($return_var === 0) {
            echo "✅ Cleared: $command\n";
        } else {
            echo "⚠️  Warning: Could not clear $command\n";
        }
    }

    // 5. Set proper file permissions
    echo "🔑 Setting file permissions...\n";
    
    $directories = [
        'storage/framework/sessions',
        'storage/framework/cache',
        'storage/logs',
        'bootstrap/cache'
    ];
    
    foreach ($directories as $dir) {
        if (is_dir($dir)) {
            chmod($dir, 0755);
            echo "✅ Set permissions for: $dir\n";
        }
    }

    // 6. Test database connection
    echo "🔌 Testing database connection...\n";
    $dbTest = execSQL("SELECT 1 as test");
    if (!empty($dbTest)) {
        echo "✅ Database connection successful\n";
    }

    // 7. Generate diagnostic report
    echo "\n📋 LOGIN DIAGNOSTIC REPORT:\n";
    echo "================================\n";
    
    echo "🌐 Domain: greenvalleyherbs.local\n";
    echo "🏢 Company: " . ($companies[0]['name'] ?? 'Green Valley Herbs') . "\n";
    echo "📧 Admin Users:\n";
    
    $allAdmins = execSQL("
        SELECT email, role FROM users 
        WHERE company_id = ? AND role IN ('admin', 'manager')
    ", [$companyId]);
    
    foreach ($allAdmins as $admin) {
        echo "   - " . $admin['email'] . " (" . $admin['role'] . ")\n";
    }
    
    echo "\n🔗 TEST LOGIN:\n";
    echo "URL: http://greenvalleyherbs.local:8000/login\n";
    echo "Email: admin@greenvalleyherbs.local\n";
    echo "Password: admin123 (if new user was created)\n";
    
    echo "\n✅ Comprehensive login fix completed successfully!\n";
    echo "\n📝 NEXT STEPS:\n";
    echo "1. Visit: http://greenvalleyherbs.local:8000/login\n";
    echo "2. Login with the admin credentials shown above\n";
    echo "3. You should be redirected to: /admin/dashboard\n";
    echo "4. If issues persist, check Laravel logs: storage/logs/laravel.log\n";

} catch (Exception $e) {
    echo "❌ Error during fix: " . $e->getMessage() . "\n";
    echo "Please check your database connection and permissions.\n";
}

/**
 * Execute SQL with proper error handling
 */
function execSQL($sql, $params = []) {
    static $pdo = null;
    
    if ($pdo === null) {
        // Read database config from .env
        $env = parse_ini_file('.env');
        $dsn = "mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4";
        
        try {
            $pdo = new PDO($dsn, $env['DB_USERNAME'], $env['DB_PASSWORD'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        if (stripos($sql, 'SELECT') === 0 || stripos($sql, 'SHOW') === 0) {
            return $stmt->fetchAll();
        } elseif (stripos($sql, 'INSERT') === 0) {
            return $pdo->lastInsertId();
        }
        
        return true;
    } catch (PDOException $e) {
        throw new Exception("SQL execution failed: " . $e->getMessage() . "\nSQL: $sql");
    }
}
