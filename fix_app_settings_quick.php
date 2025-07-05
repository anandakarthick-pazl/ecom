<?php
// Quick fix script to resolve app_settings constraint issue

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Fixing app_settings constraint issue...\n";

try {
    // Start transaction
    DB::beginTransaction();
    
    // Check if company_id column exists
    if (!Schema::hasColumn('app_settings', 'company_id')) {
        echo "Adding company_id column...\n";
        DB::statement('ALTER TABLE app_settings ADD COLUMN company_id BIGINT UNSIGNED NULL AFTER id');
    }
    
    // Get current company ID from session or first company
    $companyId = 1; // Default to company 1 for greenvalleyherbs
    if (class_exists('\App\Models\Company')) {
        $company = \App\Models\Company::where('slug', 'greenvalleyherbs')->first();
        if ($company) {
            $companyId = $company->id;
        }
    }
    
    echo "Using company_id: $companyId\n";
    
    // Update existing records to have company_id
    DB::table('app_settings')->whereNull('company_id')->update(['company_id' => $companyId]);
    
    // Drop the existing unique constraint
    echo "Dropping old unique constraint...\n";
    DB::statement('ALTER TABLE app_settings DROP INDEX app_settings_key_unique');
    
    // Add new composite unique constraint
    echo "Adding new composite unique constraint...\n";
    DB::statement('ALTER TABLE app_settings ADD UNIQUE KEY app_settings_key_company_unique (key, company_id)');
    
    // Add foreign key if not exists
    $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'app_settings' AND COLUMN_NAME = 'company_id' AND REFERENCED_TABLE_NAME = 'companies'");
    if (empty($foreignKeys)) {
        echo "Adding foreign key constraint...\n";
        DB::statement('ALTER TABLE app_settings ADD CONSTRAINT app_settings_company_id_foreign FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE');
    }
    
    // Commit transaction
    DB::commit();
    
    echo "\nSuccess! The app_settings table has been fixed.\n";
    echo "You can now update your company settings without errors.\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\nError: " . $e->getMessage() . "\n";
    echo "Please run the migration instead: php artisan migrate\n";
}
