<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== LOCALHOST TESTING SETUP ===\n\n";

try {
    // Find or create a company with localhost domain for testing
    $testCompany = \App\Models\SuperAdmin\Company::updateOrCreate(
        ['slug' => 'localhost-test'],
        [
            'name' => 'Localhost Test Store',
            'email' => 'admin@localhost.test',
            'domain' => 'localhost', // Use localhost domain
            'status' => 'active',
            'trial_ends_at' => now()->addDays(30),
            'theme_id' => 1, // Use existing theme ID
            'package_id' => 1, // Use existing package ID
            'created_by' => 1
        ]
    );

    echo "✅ Test company created: {$testCompany->name}\n";
    echo "   Domain: {$testCompany->domain}\n";
    echo "   ID: {$testCompany->id}\n\n";

    // Create test admin user
    $testUser = \App\Models\User::updateOrCreate(
        ['email' => 'admin@localhost.test'],
        [
            'name' => 'Localhost Test Admin',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'admin',
            'is_super_admin' => false,
            'status' => 'active',
            'company_id' => $testCompany->id,
            'email_verified_at' => now()
        ]
    );

    echo "✅ Test admin created: {$testUser->email}\n\n";

    echo "🔧 TEST URLS (No hosts file needed):\n";
    echo "Debug tenant: http://localhost:8000/debug-tenant\n";
    echo "Store: http://localhost:8000/shop\n";
    echo "Admin: http://localhost:8000/admin/login\n";
    echo "Main login: http://localhost:8000/login\n\n";

    echo "🔑 TEST CREDENTIALS:\n";
    echo "Email: admin@localhost.test\n";
    echo "Password: password123\n";
    echo "Company: Localhost Test Store\n\n";

    echo "💡 NOTE:\n";
    echo "This creates a company with 'localhost' as domain for immediate testing.\n";
    echo "After you add custom domains to hosts file, you can test those too.\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
