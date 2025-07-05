<?php

// Test script to verify POS sale functionality
// Place this in your routes/web.php temporarily for testing

use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

Route::get('/test-pos-fix', function() {
    try {
        // Check if the table structure is correct
        $columns = DB::select("DESCRIBE pos_sale_items");
        $columnNames = array_column($columns, 'Field');
        
        echo "<h2>✅ POS Sale Fix Verification</h2>";
        echo "<h3>📋 Database Table Structure:</h3>";
        echo "<ul>";
        foreach ($columns as $column) {
            $required = $column->Null === 'NO' ? ' (Required)' : ' (Optional)';
            $default = $column->Default ? " - Default: {$column->Default}" : '';
            echo "<li><strong>{$column->Field}</strong>: {$column->Type}{$required}{$default}</li>";
        }
        echo "</ul>";
        
        // Check required fields
        $requiredFields = ['product_name', 'total_amount', 'discount_amount'];
        $missingFields = array_diff($requiredFields, $columnNames);
        
        if (empty($missingFields)) {
            echo "<p style='color: green;'>✅ All required fields are present in the database table.</p>";
        } else {
            echo "<p style='color: red;'>❌ Missing fields: " . implode(', ', $missingFields) . "</p>";
            echo "<p><strong>Solution:</strong> Run database migration: <code>php artisan migrate</code></p>";
        }
        
        // Test data creation (dry run)
        echo "<h3>🧪 Test POS Sale Item Creation (Dry Run):</h3>";
        
        // Get a sample product
        $product = Product::where('stock', '>', 0)->first();
        
        if (!$product) {
            echo "<p style='color: orange;'>⚠️ No products with stock found. Please add some products first.</p>";
            return;
        }
        
        echo "<p><strong>Test Product:</strong> {$product->name} (ID: {$product->id}, Stock: {$product->stock})</p>";
        
        // Simulate the data that would be sent to create a sale item
        $testData = [
            'pos_sale_id' => 999, // Fake ID for testing
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => $product->price,
            'discount_amount' => 0,
            'total_amount' => $product->price * 1
        ];
        
        echo "<h4>📝 Test Data Structure:</h4>";
        echo "<pre>" . json_encode($testData, JSON_PRETTY_PRINT) . "</pre>";
        
        // Validate the data structure
        $model = new PosSaleItem();
        $fillableFields = $model->getFillable();
        
        echo "<h4>🔧 Model Fillable Fields:</h4>";
        echo "<ul>";
        foreach ($fillableFields as $field) {
            $hasData = array_key_exists($field, $testData) ? '✅' : '❌';
            echo "<li>{$hasData} {$field}</li>";
        }
        echo "</ul>";
        
        $missingInData = array_diff($fillableFields, array_keys($testData));
        if (empty($missingInData)) {
            echo "<p style='color: green;'>✅ All fillable fields have corresponding data.</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Fields that might need attention: " . implode(', ', $missingInData) . "</p>";
        }
        
        echo "<h3>🚀 Recommended Next Steps:</h3>";
        echo "<ol>";
        echo "<li>Clear cache: <code>php artisan cache:clear</code></li>";
        echo "<li>Test POS sale creation through the actual POS interface</li>";
        echo "<li>Check error logs if issues persist: <code>storage/logs/laravel.log</code></li>";
        echo "<li>Remove this test route after verification</li>";
        echo "</ol>";
        
        echo "<hr>";
        echo "<p><small>Generated: " . now() . "</small></p>";
        
    } catch (Exception $e) {
        echo "<h2 style='color: red;'>❌ Error During Test:</h2>";
        echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
        echo "<p><strong>File:</strong> " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
        
        if (str_contains($e->getMessage(), "doesn't exist")) {
            echo "<h3>🔧 Quick Fix:</h3>";
            echo "<p>Run the database migration: <code>php artisan migrate</code></p>";
        }
    }
});
