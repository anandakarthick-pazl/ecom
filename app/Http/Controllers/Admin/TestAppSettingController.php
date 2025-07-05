<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppSetting;

class TestAppSettingController extends Controller
{
    public function test()
    {
        $results = [];
        
        // Test 1: Check if class exists
        $results['class_exists'] = class_exists(AppSetting::class);
        
        // Test 2: Check methods
        if ($results['class_exists']) {
            $reflection = new \ReflectionClass(AppSetting::class);
            
            $results['methods'] = [
                'getCurrentTenantId' => $reflection->hasMethod('getCurrentTenantId'),
                'withoutTenantScope' => method_exists(AppSetting::class, 'withoutTenantScope'),
                'get' => method_exists(AppSetting::class, 'get'),
                'set' => method_exists(AppSetting::class, 'set'),
            ];
            
            // Test 3: Check traits
            $results['traits'] = array_values(class_uses(AppSetting::class));
            
            // Test 4: Try to call getCurrentTenantId
            try {
                $method = $reflection->getMethod('getCurrentTenantId');
                $method->setAccessible(true);
                $tenantId = $method->invoke(null);
                $results['getCurrentTenantId_result'] = [
                    'success' => true,
                    'value' => $tenantId
                ];
            } catch (\Exception $e) {
                $results['getCurrentTenantId_result'] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
            
            // Test 5: Try to get a setting
            try {
                $value = AppSetting::get('test_key', 'default_value');
                $results['get_setting_result'] = [
                    'success' => true,
                    'value' => $value
                ];
            } catch (\Exception $e) {
                $results['get_setting_result'] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        // Test 6: File contents check
        $filePath = app_path('Models/AppSetting.php');
        if (file_exists($filePath)) {
            $contents = file_get_contents($filePath);
            $results['file_check'] = [
                'exists' => true,
                'has_getCurrentTenantId' => strpos($contents, 'function getCurrentTenantId') !== false,
                'has_withoutTenantScope' => strpos($contents, 'withoutTenantScope') !== false,
            ];
        } else {
            $results['file_check'] = ['exists' => false];
        }
        
        return response()->json($results, 200, [], JSON_PRETTY_PRINT);
    }
}
