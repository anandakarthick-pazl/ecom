<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING COMPANIES TABLE STRUCTURE ===\n\n";

try {
    // Get table columns
    $columns = \Illuminate\Support\Facades\DB::select("DESCRIBE companies");
    
    echo "📋 COMPANIES TABLE STRUCTURE:\n";
    foreach ($columns as $column) {
        $required = $column->Null === 'NO' && $column->Default === null && $column->Extra !== 'auto_increment' ? '⚠️ REQUIRED' : '';
        echo "   {$column->Field} ({$column->Type}) - {$column->Null} - Default: '{$column->Default}' {$required}\n";
    }
    
    echo "\n🔍 SAMPLE EXISTING COMPANY:\n";
    $sampleCompany = \App\Models\SuperAdmin\Company::first();
    if ($sampleCompany) {
        echo "   ID: {$sampleCompany->id}\n";
        echo "   Name: {$sampleCompany->name}\n";
        echo "   Theme ID: " . ($sampleCompany->theme_id ?? 'NULL') . "\n";
        echo "   Package ID: " . ($sampleCompany->package_id ?? 'NULL') . "\n";
        echo "   Created By: " . ($sampleCompany->created_by ?? 'NULL') . "\n";
    } else {
        echo "   No existing companies found\n";
    }

    echo "\n🎨 CHECKING THEMES TABLE:\n";
    $themes = \App\Models\SuperAdmin\Theme::all(['id', 'name', 'slug']);
    if ($themes->count() > 0) {
        foreach ($themes as $theme) {
            echo "   Theme ID: {$theme->id} - {$theme->name} ({$theme->slug})\n";
        }
    } else {
        echo "   No themes found - need to create default theme\n";
    }

    echo "\n📦 CHECKING PACKAGES TABLE:\n";
    $packages = \App\Models\SuperAdmin\Package::all(['id', 'name', 'slug']);
    if ($packages->count() > 0) {
        foreach ($packages as $package) {
            echo "   Package ID: {$package->id} - {$package->name} ({$package->slug})\n";
        }
    } else {
        echo "   No packages found - need to create default package\n";
    }

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
