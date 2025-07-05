<?php
/**
 * Quick Fix Script for Migration Issues
 * Run this script to resolve the migration conflicts
 */

echo "🔧 Starting migration fix process...\n";

try {
    // 1. Check current migration status
    echo "📋 Checking current migration status...\n";
    $migrations = DB::table('migrations')->orderBy('batch', 'desc')->get();
    
    echo "Latest migrations:\n";
    foreach ($migrations->take(5) as $migration) {
        echo "  - {$migration->migration} (Batch: {$migration->batch})\n";
    }
    
    // 2. Check if problematic migration exists in database
    $problematicMigration = DB::table('migrations')
        ->where('migration', 'like', '%create_roles_permissions_system%')
        ->first();
    
    if ($problematicMigration) {
        echo "⚠️  Found problematic migration in database: {$problematicMigration->migration}\n";
        echo "🗑️  Removing from migrations table...\n";
        
        DB::table('migrations')->where('id', $problematicMigration->id)->delete();
        echo "✅ Removed problematic migration from database\n";
    }
    
    // 3. Check existing table structure
    echo "\n📊 Checking existing table structure...\n";
    
    $tables = ['users', 'roles', 'permissions', 'role_permissions'];
    foreach ($tables as $table) {
        if (Schema::hasTable($table)) {
            echo "✅ Table '{$table}' exists\n";
            
            // Check some key columns
            if ($table === 'users') {
                $columns = ['employee_id', 'department', 'hire_date', 'role_id'];
                foreach ($columns as $column) {
                    $exists = Schema::hasColumn($table, $column);
                    echo "   - {$column}: " . ($exists ? "✅ exists" : "❌ missing") . "\n";
                }
            }
        } else {
            echo "❌ Table '{$table}' does not exist\n";
        }
    }
    
    // 4. Check role_permissions table structure
    if (Schema::hasTable('role_permissions')) {
        echo "\n🔍 Analyzing role_permissions table structure...\n";
        $columns = Schema::getColumnListing('role_permissions');
        echo "   Columns: " . implode(', ', $columns) . "\n";
        
        // Check if it has timestamps
        $hasTimestamps = in_array('created_at', $columns) && in_array('updated_at', $columns);
        echo "   Timestamps: " . ($hasTimestamps ? "✅ present" : "❌ missing") . "\n";
    }
    
    // 5. Recommendations
    echo "\n💡 Recommendations:\n";
    echo "1. Delete any problematic migration files from database/migrations/ folder\n";
    echo "2. Use the new safe migration files provided\n";
    echo "3. Run: php artisan migrate\n";
    echo "4. The new migrations will only add missing columns/tables\n";
    
    echo "\n✅ Fix process completed successfully!\n";
    echo "You can now run the new safe migrations.\n";
    
} catch (Exception $e) {
    echo "❌ Error during fix process: " . $e->getMessage() . "\n";
    echo "Please check your database connection and try again.\n";
}
