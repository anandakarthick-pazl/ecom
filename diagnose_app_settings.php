<?php
// Diagnostic script for app_settings table

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== App Settings Table Diagnostic ===\n\n";

try {
    // Check if table exists
    if (!Schema::hasTable('app_settings')) {
        echo "ERROR: app_settings table does not exist!\n";
        exit(1);
    }
    
    echo "✓ Table 'app_settings' exists\n\n";
    
    // Check columns
    echo "Columns in app_settings table:\n";
    $columns = Schema::getColumnListing('app_settings');
    foreach ($columns as $column) {
        $type = DB::getSchemaBuilder()->getColumnType('app_settings', $column);
        echo "  - $column ($type)";
        if ($column == 'company_id') {
            echo " ✓";
        }
        echo "\n";
    }
    
    echo "\n";
    
    // Check if company_id exists
    if (!in_array('company_id', $columns)) {
        echo "WARNING: company_id column is missing!\n";
    } else {
        echo "✓ company_id column exists\n";
    }
    
    echo "\n";
    
    // Check indexes
    echo "Indexes on app_settings table:\n";
    $indexes = DB::select("SHOW INDEX FROM app_settings");
    foreach ($indexes as $index) {
        echo "  - {$index->Key_name} on column {$index->Column_name}";
        if ($index->Non_unique == 0) {
            echo " (UNIQUE)";
        }
        echo "\n";
    }
    
    echo "\n";
    
    // Check for problematic unique constraint
    $hasOldUnique = false;
    $hasNewUnique = false;
    foreach ($indexes as $index) {
        if ($index->Key_name == 'app_settings_key_unique' && $index->Column_name == 'key') {
            $hasOldUnique = true;
        }
        if ($index->Key_name == 'app_settings_key_company_unique') {
            $hasNewUnique = true;
        }
    }
    
    if ($hasOldUnique) {
        echo "ERROR: Old unique constraint on 'key' column still exists!\n";
        echo "This is causing the duplicate entry error.\n";
    }
    
    if ($hasNewUnique) {
        echo "✓ New composite unique constraint exists\n";
    } else {
        echo "WARNING: Composite unique constraint (key, company_id) is missing\n";
    }
    
    echo "\n";
    
    // Show existing settings
    echo "Current settings in database:\n";
    $settings = DB::table('app_settings')->select('id', 'key', 'company_id', 'group')->get();
    foreach ($settings as $setting) {
        echo "  - ID: {$setting->id}, Key: {$setting->key}, Company: " . ($setting->company_id ?? 'NULL') . ", Group: {$setting->group}\n";
    }
    
    echo "\n";
    
    // Check for duplicates
    $duplicates = DB::table('app_settings')
        ->select('key', DB::raw('COUNT(*) as count'))
        ->groupBy('key')
        ->having('count', '>', 1)
        ->get();
        
    if ($duplicates->count() > 0) {
        echo "WARNING: Duplicate keys found:\n";
        foreach ($duplicates as $dup) {
            echo "  - '{$dup->key}' appears {$dup->count} times\n";
        }
    } else {
        echo "✓ No duplicate keys found\n";
    }
    
    echo "\n=== Diagnostic Complete ===\n";
    
} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
}
