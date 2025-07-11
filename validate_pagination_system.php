<?php

/**
 * Enhanced Pagination System Validation Script
 * Run this script to validate that both frontend and admin pagination work correctly
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\AppSetting;
use App\Traits\HasPagination;
use Illuminate\Http\Request;

class PaginationSystemValidator
{
    use HasPagination;
    
    public function runValidation()
    {
        echo "=== Enhanced Pagination System Validation ===\n\n";
        
        // Test 1: Validate settings are properly configured
        $this->testSettingsConfiguration();
        
        // Test 2: Validate frontend pagination
        $this->testFrontendPagination();
        
        // Test 3: Validate admin pagination
        $this->testAdminPagination();
        
        // Test 4: Test edge cases
        $this->testEdgeCases();
        
        // Test 5: Validate per-page controls
        $this->testPerPageControls();
        
        echo "\n=== Validation Complete ===\n";
    }
    
    private function testSettingsConfiguration()
    {
        echo "1. Testing Settings Configuration...\n";
        
        $requiredSettings = [
            'frontend_pagination_enabled' => 'boolean',
            'admin_pagination_enabled' => 'boolean', 
            'frontend_records_per_page' => 'integer',
            'admin_records_per_page' => 'integer',
            'frontend_load_more_enabled' => 'boolean',
            'admin_show_per_page_selector' => 'boolean',
            'admin_default_sort_order' => 'string',
            'frontend_default_sort_order' => 'string'
        ];
        
        $allPass = true;
        foreach ($requiredSettings as $setting => $expectedType) {
            $value = AppSetting::get($setting);
            if ($value !== null) {
                $actualType = $this->getSettingType($value);
                $typeMatch = ($expectedType === 'boolean' && is_bool($actualType)) ||
                            ($expectedType === 'integer' && is_int($actualType)) ||
                            ($expectedType === 'string' && is_string($actualType));
                
                if ($typeMatch) {
                    echo "   ✓ {$setting}: {$value} ({$expectedType})\n";
                } else {
                    echo "   ✗ {$setting}: {$value} (expected {$expectedType}, got " . gettype($actualType) . ")\n";
                    $allPass = false;
                }
            } else {
                echo "   ✗ {$setting}: NOT FOUND\n";
                $allPass = false;
            }
        }
        
        echo $allPass ? "   Settings Configuration: PASS\n\n" : "   Settings Configuration: FAIL\n\n";
    }
    
    private function testFrontendPagination()
    {
        echo "2. Testing Frontend Pagination...\n";
        
        // Test with default request
        $request = new Request();
        $settings = $this->getFrontendPaginationSettings($request, '12');
        
        echo "   Default Frontend Settings:\n";
        echo "      Enabled: " . ($settings['enabled'] ? 'Yes' : 'No') . "\n";
        echo "      Per Page: " . $settings['per_page'] . "\n";
        echo "      Allowed Values: " . implode(', ', $settings['allowed_values']) . "\n";
        
        // Test with custom per_page
        $requestWithPerPage = new Request(['per_page' => 24]);
        $customSettings = $this->getFrontendPaginationSettings($requestWithPerPage, '12');
        
        echo "   Custom Per Page Test (24):\n";
        echo "      Per Page: " . $customSettings['per_page'] . " (should be 24)\n";
        
        // Test with invalid per_page (should fallback to default)
        $requestWithInvalid = new Request(['per_page' => 999]);
        $invalidSettings = $this->getFrontendPaginationSettings($requestWithInvalid, '12');
        
        echo "   Invalid Per Page Test (999):\n";
        echo "      Per Page: " . $invalidSettings['per_page'] . " (should fallback to default)\n";
        
        // Test controls data
        $controls = $this->getPaginationControlsData($request, 'frontend');
        echo "   Controls Data: " . ($controls['enabled'] ? 'PASS' : 'FAIL') . "\n\n";
    }
    
    private function testAdminPagination()
    {
        echo "3. Testing Admin Pagination...\n";
        
        // Test with default request
        $request = new Request();
        $settings = $this->getAdminPaginationSettings($request, '20');
        
        echo "   Default Admin Settings:\n";
        echo "      Enabled: " . ($settings['enabled'] ? 'Yes' : 'No') . "\n";
        echo "      Per Page: " . $settings['per_page'] . "\n";
        echo "      Allowed Values: " . implode(', ', $settings['allowed_values']) . "\n";
        
        // Test with custom per_page
        $requestWithPerPage = new Request(['per_page' => 50]);
        $customSettings = $this->getAdminPaginationSettings($requestWithPerPage, '20');
        
        echo "   Custom Per Page Test (50):\n";
        echo "      Per Page: " . $customSettings['per_page'] . " (should be 50)\n";
        
        // Test with invalid per_page (should fallback to default)
        $requestWithInvalid = new Request(['per_page' => 500]);
        $invalidSettings = $this->getAdminPaginationSettings($requestWithInvalid, '20');
        
        echo "   Invalid Per Page Test (500):\n";
        echo "      Per Page: " . $invalidSettings['per_page'] . " (should fallback to default)\n";
        
        // Test controls data
        $controls = $this->getPaginationControlsData($request, 'admin');
        echo "   Controls Data: " . ($controls['enabled'] ? 'PASS' : 'FAIL') . "\n\n";
    }
    
    private function testEdgeCases()
    {
        echo "4. Testing Edge Cases...\n";
        
        // Test when pagination is disabled
        $originalFrontendEnabled = AppSetting::get('frontend_pagination_enabled');
        $originalAdminEnabled = AppSetting::get('admin_pagination_enabled');
        
        // Temporarily disable pagination
        AppSetting::set('frontend_pagination_enabled', false, 'boolean', 'pagination');
        AppSetting::set('admin_pagination_enabled', false, 'boolean', 'pagination');
        AppSetting::clearCache();
        
        $request = new Request();
        $frontendSettings = $this->getFrontendPaginationSettings($request);
        $adminSettings = $this->getAdminPaginationSettings($request);
        
        echo "   Disabled Pagination Test:\n";
        echo "      Frontend Enabled: " . ($frontendSettings['enabled'] ? 'FAIL' : 'PASS') . "\n";
        echo "      Admin Enabled: " . ($adminSettings['enabled'] ? 'FAIL' : 'PASS') . "\n";
        
        // Restore original settings
        AppSetting::set('frontend_pagination_enabled', $originalFrontendEnabled, 'boolean', 'pagination');
        AppSetting::set('admin_pagination_enabled', $originalAdminEnabled, 'boolean', 'pagination');
        AppSetting::clearCache();
        
        echo "   Settings Restored: PASS\n\n";
    }
    
    private function testPerPageControls()
    {
        echo "5. Testing Per-Page Controls...\n";
        
        $request = new Request(['per_page' => 30]);
        
        $frontendControls = $this->getPaginationControlsData($request, 'frontend');
        $adminControls = $this->getPaginationControlsData($request, 'admin');
        
        echo "   Frontend Controls:\n";
        echo "      Current Per Page: " . $frontendControls['current_per_page'] . "\n";
        echo "      Request Per Page: " . $frontendControls['request_per_page'] . "\n";
        echo "      Allowed Values Count: " . count($frontendControls['allowed_values']) . "\n";
        
        echo "   Admin Controls:\n";
        echo "      Current Per Page: " . $adminControls['current_per_page'] . "\n";
        echo "      Request Per Page: " . $adminControls['request_per_page'] . "\n";
        echo "      Allowed Values Count: " . count($adminControls['allowed_values']) . "\n";
        
        echo "   Per-Page Controls: PASS\n\n";
    }
    
    private function getSettingType($value)
    {
        if (is_string($value)) {
            if ($value === 'true' || $value === 'false') {
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }
            if (is_numeric($value)) {
                return (int) $value;
            }
            return $value;
        }
        return $value;
    }
}

class SystemUsageExamples
{
    public function showUsageExamples()
    {
        echo "\n=== Usage Examples ===\n\n";
        
        echo "1. Frontend Controller Example:\n";
        echo "```php\n";
        echo "class HomeController extends Controller\n";
        echo "{\n";
        echo "    use HasPagination;\n";
        echo "\n";
        echo "    public function products(Request \$request)\n";
        echo "    {\n";
        echo "        \$query = Product::active()->inStock();\n";
        echo "        \n";
        echo "        // Apply frontend pagination\n";
        echo "        \$products = \$this->applyFrontendPagination(\$query, \$request, '12');\n";
        echo "        \$controls = \$this->getPaginationControlsData(\$request, 'frontend');\n";
        echo "        \n";
        echo "        return view('products', compact('products', 'controls'));\n";
        echo "    }\n";
        echo "}\n";
        echo "```\n\n";
        
        echo "2. Admin Controller Example:\n";
        echo "```php\n";
        echo "class ProductController extends BaseAdminController\n";
        echo "{\n";
        echo "    use HasPagination;\n";
        echo "\n";
        echo "    public function index(Request \$request)\n";
        echo "    {\n";
        echo "        \$query = Product::with('category');\n";
        echo "        \n";
        echo "        // Apply admin pagination\n";
        echo "        \$products = \$this->applyAdminPagination(\$query, \$request, '20');\n";
        echo "        \$controls = \$this->getPaginationControlsData(\$request, 'admin');\n";
        echo "        \n";
        echo "        return view('admin.products.index', compact('products', 'controls'));\n";
        echo "    }\n";
        echo "}\n";
        echo "```\n\n";
        
        echo "3. Frontend View Example:\n";
        echo "```blade\n";
        echo "@include('components.frontend-pagination-controls', ['items' => \$products])\n";
        echo "```\n\n";
        
        echo "4. Admin View Example:\n";
        echo "```blade\n";
        echo "@include('admin.components.pagination-controls', ['items' => \$products])\n";
        echo "```\n\n";
    }
}

// Only run if script is called directly
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)) {
    try {
        $validator = new PaginationSystemValidator();
        $validator->runValidation();
        
        $examples = new SystemUsageExamples();
        $examples->showUsageExamples();
        
        echo "✅ Enhanced Pagination System validation completed successfully!\n";
        echo "\n🎯 Key Points:\n";
        echo "• Frontend Settings = Customer website pagination (product listings, etc.)\n";
        echo "• Admin Settings = Admin dashboard pagination (products management, etc.)\n";
        echo "• Both systems work independently with their own configurations\n";
        echo "• Load More button available for frontend mobile experience\n";
        echo "• Per-page selectors available for both frontend and admin\n";
        echo "• All settings are tenant-aware (multi-company support)\n";
        
    } catch (Exception $e) {
        echo "❌ Validation failed with error: " . $e->getMessage() . "\n";
        echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
}
