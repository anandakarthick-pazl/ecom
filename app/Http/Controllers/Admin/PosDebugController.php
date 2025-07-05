<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PosDebugController extends Controller
{
    public function debug(Request $request)
    {
        $debugInfo = [
            'session' => [
                'selected_company_id' => session('selected_company_id'),
                'selected_company_name' => session('selected_company_name'),
            ],
            'request' => [
                'current_company_id' => $request->get('current_company_id'),
                'current_company' => $request->get('current_company') ? [
                    'id' => $request->get('current_company')->id,
                    'name' => $request->get('current_company')->name,
                ] : null,
            ],
            'app_container' => [
                'has_current_tenant' => app()->has('current_tenant'),
                'current_tenant' => app()->has('current_tenant') ? [
                    'id' => app('current_tenant')->id,
                    'name' => app('current_tenant')->name,
                ] : null,
            ],
            'auth' => [
                'user_id' => auth()->id(),
                'user_company_id' => auth()->user() ? auth()->user()->company_id : null,
            ],
            'test_model_creation' => null,
        ];
        
        // Test creating a model with BelongsToTenant trait
        try {
            $testSale = new \App\Models\PosSale();
            $testSale->invoice_number = 'TEST' . time();
            $testSale->sale_date = now();
            $testSale->subtotal = 100;
            $testSale->total_amount = 100;
            $testSale->paid_amount = 100;
            $testSale->cashier_id = auth()->id();
            
            // Don't save, just check what would be set
            $debugInfo['test_model_creation'] = [
                'would_set_company_id' => $testSale->company_id ?? 'NULL',
                'fillable_includes_company_id' => in_array('company_id', $testSale->getFillable()),
                'uses_belongs_to_tenant' => in_array('App\Traits\BelongsToTenant', class_uses($testSale)),
            ];
        } catch (\Exception $e) {
            $debugInfo['test_model_creation'] = [
                'error' => $e->getMessage(),
            ];
        }
        
        return response()->json($debugInfo, 200, [], JSON_PRETTY_PRINT);
    }
}
