<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING ALL TABLE STRUCTURES ===\n\n";

try {
    // Check themes table
    echo "🎨 THEMES TABLE STRUCTURE:\n";
    $themeColumns = \Illuminate\Support\Facades\DB::select("DESCRIBE themes");
    foreach ($themeColumns as $column) {
        $required = $column->Null === 'NO' && $column->Default === null && $column->Extra !== 'auto_increment' ? '⚠️ REQUIRED' : '';
        echo "   {$column->Field} ({$column->Type}) - Default: '{$column->Default}' {$required}\n";
    }
    
    echo "\n📦 PACKAGES TABLE STRUCTURE:\n";
    $packageColumns = \Illuminate\Support\Facades\DB::select("DESCRIBE packages");
    foreach ($packageColumns as $column) {
        $required = $column->Null === 'NO' && $column->Default === null && $column->Extra !== 'auto_increment' ? '⚠️ REQUIRED' : '';
        echo "   {$column->Field} ({$column->Type}) - Default: '{$column->Default}' {$required}\n";
    }
    
    echo "\n🏢 COMPANIES TABLE STRUCTURE:\n";
    $companyColumns = \Illuminate\Support\Facades\DB::select("DESCRIBE companies");
    foreach ($companyColumns as $column) {
        $required = $column->Null === 'NO' && $column->Default === null && $column->Extra !== 'auto_increment' ? '⚠️ REQUIRED' : '';
        echo "   {$column->Field} ({$column->Type}) - Default: '{$column->Default}' {$required}\n";
    }

    echo "\n👥 USERS TABLE STRUCTURE:\n";
    $userColumns = \Illuminate\Support\Facades\DB::select("DESCRIBE users");
    foreach ($userColumns as $column) {
        $required = $column->Null === 'NO' && $column->Default === null && $column->Extra !== 'auto_increment' ? '⚠️ REQUIRED' : '';
        echo "   {$column->Field} ({$column->Type}) - Default: '{$column->Default}' {$required}\n";
    }

    echo "\n📋 SUMMARY OF REQUIRED FIELDS:\n";
    echo "Themes: " . implode(', ', array_filter(array_map(function($col) {
        return ($col->Null === 'NO' && $col->Default === null && $col->Extra !== 'auto_increment') ? $col->Field : null;
    }, $themeColumns))) . "\n";
    
    echo "Packages: " . implode(', ', array_filter(array_map(function($col) {
        return ($col->Null === 'NO' && $col->Default === null && $col->Extra !== 'auto_increment') ? $col->Field : null;
    }, $packageColumns))) . "\n";
    
    echo "Companies: " . implode(', ', array_filter(array_map(function($col) {
        return ($col->Null === 'NO' && $col->Default === null && $col->Extra !== 'auto_increment') ? $col->Field : null;
    }, $companyColumns))) . "\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
