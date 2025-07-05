<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuperAdmin\WhatsAppConfig;
use App\Models\SuperAdmin\Company;
use App\Services\TwilioWhatsAppService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WhatsAppController extends Controller
{
    /**
     * Display WhatsApp configuration form
     */
    public function index()
    {
        $companies = Company::all();
        $configs = WhatsAppConfig::with('company')->get()->keyBy('company_id');
        
        return view('super-admin.settings.whatsapp', compact('companies', 'configs'));
    }

    /**
     * Show configuration form for a specific company
     */
    public function show($companyId)
    {
        $company = Company::findOrFail($companyId);
        $config = WhatsAppConfig::where('company_id', $companyId)->first();
        
        if (!$config) {
            $config = new WhatsAppConfig([
                'company_id' => $companyId,
                'is_enabled' => false,
                'max_file_size_mb' => 5,
                'allowed_file_types' => ['pdf'],
                'rate_limit_per_minute' => 10,
                'default_message_template' => "Hello {{customer_name}},\n\nYour order #{{order_number}} has been processed. Please find your bill attached.\n\nOrder Total: ₹{{total}}\n\nThank you for your business!\n\n{{company_name}}"
            ]);
        }
        
        return view('super-admin.settings.whatsapp-config', compact('company', 'config'));
    }

    /**
     * Update WhatsApp configuration
     */
    public function update(Request $request, $companyId)
    {
        $validator = Validator::make($request->all(), [
            'twilio_account_sid' => 'required|string|max:255',
            'twilio_auth_token' => 'required|string|max:255',
            'whatsapp_business_number' => 'required|string|max:50',
            'is_enabled' => 'boolean',
            'default_message_template' => 'required|string|max:1000',
            'test_number' => 'nullable|string|max:15',
            'webhook_url' => 'nullable|url|max:255',
            'webhook_secret' => 'nullable|string|max:255',
            'max_file_size_mb' => 'required|integer|min:1|max:20',
            'allowed_file_types' => 'required|array|min:1',
            'allowed_file_types.*' => 'in:pdf,jpg,jpeg,png,gif,doc,docx',
            'rate_limit_per_minute' => 'required|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $company = Company::findOrFail($companyId);
            
            // Validate phone number format
            if (!WhatsAppConfig::validatePhoneNumber($request->whatsapp_business_number)) {
                return redirect()->back()
                    ->withErrors(['whatsapp_business_number' => 'Please provide a valid international phone number format (e.g., +1234567890)'])
                    ->withInput();
            }

            // Test Twilio credentials if provided
            if ($request->filled('twilio_account_sid') && $request->filled('twilio_auth_token')) {
                $testConfig = new WhatsAppConfig([
                    'twilio_account_sid' => $request->twilio_account_sid,
                    'twilio_auth_token' => $request->twilio_auth_token,
                    'whatsapp_business_number' => $request->whatsapp_business_number
                ]);
                
                $service = new TwilioWhatsAppService($testConfig);
                $validation = $service->validateCredentials($request->twilio_account_sid, $request->twilio_auth_token);
                
                if (!$validation['success']) {
                    return redirect()->back()
                        ->withErrors(['twilio_credentials' => $validation['error']])
                        ->withInput();
                }
            }

            // Update or create configuration
            $config = WhatsAppConfig::updateOrCreate(
                ['company_id' => $companyId],
                [
                    'twilio_account_sid' => $request->twilio_account_sid,
                    'twilio_auth_token' => $request->twilio_auth_token,
                    'whatsapp_business_number' => $request->whatsapp_business_number,
                    'is_enabled' => $request->boolean('is_enabled'),
                    'default_message_template' => $request->default_message_template,
                    'test_number' => $request->test_number,
                    'webhook_url' => $request->webhook_url,
                    'webhook_secret' => $request->webhook_secret,
                    'max_file_size_mb' => $request->max_file_size_mb,
                    'allowed_file_types' => $request->allowed_file_types,
                    'rate_limit_per_minute' => $request->rate_limit_per_minute
                ]
            );

            Log::info('WhatsApp configuration updated', [
                'company_id' => $companyId,
                'config_id' => $config->id,
                'is_enabled' => $config->is_enabled
            ]);

            return redirect()->route('super-admin.whatsapp.show', $companyId)
                ->with('success', 'WhatsApp configuration updated successfully!');

        } catch (\Exception $e) {
            Log::error('WhatsApp configuration update failed', [
                'company_id' => $companyId,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Failed to update configuration: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Test WhatsApp configuration
     */
    public function test(Request $request, $companyId)
    {
        $request->validate([
            'test_number' => 'required|string|max:15',
            'test_message' => 'nullable|string|max:500'
        ]);

        try {
            $company = Company::findOrFail($companyId);
            $config = WhatsAppConfig::where('company_id', $companyId)->first();

            if (!$config || !$config->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp is not configured for this company'
                ], 400);
            }

            $service = new TwilioWhatsAppService($config);
            $testMessage = $request->test_message ?: 
                "Test message from {$company->name}\n\nWhatsApp integration is working correctly!\n\nSent at: " . now()->format('Y-m-d H:i:s');

            $result = $service->sendTestMessage($request->test_number, $testMessage);

            if ($result['success']) {
                Log::info('WhatsApp test message sent successfully', [
                    'company_id' => $companyId,
                    'test_number' => $request->test_number,
                    'message_sid' => $result['message_sid']
                ]);
            } else {
                Log::error('WhatsApp test message failed', [
                    'company_id' => $companyId,
                    'test_number' => $request->test_number,
                    'error' => $result['error']
                ]);
            }

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('WhatsApp test failed', [
                'company_id' => $companyId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get account information
     */
    public function accountInfo($companyId)
    {
        try {
            $config = WhatsAppConfig::where('company_id', $companyId)->first();

            if (!$config || !$config->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp is not configured'
                ], 400);
            }

            $service = new TwilioWhatsAppService($config);
            $accountInfo = $service->getAccountInfo();

            return response()->json($accountInfo);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get account info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get usage statistics
     */
    public function usage($companyId)
    {
        try {
            $config = WhatsAppConfig::where('company_id', $companyId)->first();

            if (!$config || !$config->isConfigured()) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp is not configured'
                ], 400);
            }

            $service = new TwilioWhatsAppService($config);
            $usage = $service->getUsageStats();

            return response()->json($usage);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get usage stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle WhatsApp status
     */
    public function toggle($companyId)
    {
        try {
            $config = WhatsAppConfig::where('company_id', $companyId)->first();

            if (!$config) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp configuration not found'
                ], 404);
            }

            $config->is_enabled = !$config->is_enabled;
            $config->save();

            $status = $config->is_enabled ? 'enabled' : 'disabled';

            Log::info('WhatsApp status toggled', [
                'company_id' => $companyId,
                'new_status' => $status
            ]);

            return response()->json([
                'success' => true,
                'message' => "WhatsApp {$status} successfully",
                'is_enabled' => $config->is_enabled
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle WhatsApp: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete WhatsApp configuration
     */
    public function destroy($companyId)
    {
        try {
            $config = WhatsAppConfig::where('company_id', $companyId)->first();

            if (!$config) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configuration not found'
                ], 404);
            }

            $config->delete();

            Log::info('WhatsApp configuration deleted', [
                'company_id' => $companyId
            ]);

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp configuration deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export configuration
     */
    public function export($companyId)
    {
        try {
            $company = Company::findOrFail($companyId);
            $config = WhatsAppConfig::where('company_id', $companyId)->first();

            if (!$config) {
                return response()->json([
                    'success' => false,
                    'message' => 'Configuration not found'
                ], 404);
            }

            $exportData = [
                'company_name' => $company->name,
                'company_id' => $companyId,
                'whatsapp_business_number' => $config->whatsapp_business_number,
                'is_enabled' => $config->is_enabled,
                'default_message_template' => $config->default_message_template,
                'webhook_url' => $config->webhook_url,
                'max_file_size_mb' => $config->max_file_size_mb,
                'allowed_file_types' => $config->allowed_file_types,
                'rate_limit_per_minute' => $config->rate_limit_per_minute,
                'exported_at' => now()->toISOString()
            ];

            $filename = 'whatsapp_config_' . $company->name . '_' . now()->format('Y-m-d_H-i-s') . '.json';

            return response()->json($exportData)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
