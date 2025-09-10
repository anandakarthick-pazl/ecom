<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\User;
use App\Models\SuperAdmin\Company;
use App\Services\InvoiceNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    public function index()
    {
        // Get current company
        $company = $this->getCurrentCompany();
        
        // Debug: Log company info
        \Log::info('Settings page debug', [
            'company_id' => $this->getCurrentTenantId(),
            'company_exists' => $company ? true : false,
            'company_data' => $company ? $company->toArray() : null,
            'user_id' => auth()->id(),
            'session_company' => session('selected_company_id')
        ]);
        
        // Get settings from app_settings table (for non-company settings)
        $appearanceSettings = AppSetting::getGroup('appearance');
        $themeSettings = AppSetting::getGroup('theme');
        $notificationSettings = AppSetting::getGroup('notifications');
        $emailSettings = AppSetting::getGroup('email');
        $inventorySettings = AppSetting::getGroup('inventory');
        $deliverySettings = AppSetting::getGroup('delivery');
        $paginationSettings = AppSetting::getGroup('pagination');
        
        // Ensure delivery settings have proper default values
        $deliveryDefaults = [
            'delivery_enabled' => true,
            'delivery_charge' => 50.00,
            'free_delivery_enabled' => true,
            'free_delivery_threshold' => 500.00,
            'delivery_max_amount' => null,
            'delivery_time_estimate' => '3-5 business days',
            'delivery_description' => '',
            'min_order_validation_enabled' => false,
            'min_order_amount' => 1000.00,
            'min_order_message' => 'Minimum order amount is ₹1000 for online orders.'
        ];
        
        foreach ($deliveryDefaults as $key => $defaultValue) {
            if (!isset($deliverySettings[$key])) {
                $deliverySettings[$key] = $defaultValue;
            } else {
                // Ensure proper type conversion for boolean settings
                if (in_array($key, ['delivery_enabled', 'free_delivery_enabled', 'min_order_validation_enabled'])) {
                    if (is_string($deliverySettings[$key])) {
                        $deliverySettings[$key] = filter_var($deliverySettings[$key], FILTER_VALIDATE_BOOLEAN);
                    } else {
                        $deliverySettings[$key] = (bool) $deliverySettings[$key];
                    }
                } elseif (in_array($key, ['delivery_charge', 'free_delivery_threshold', 'delivery_max_amount', 'min_order_amount'])) {
                    if ($deliverySettings[$key] !== null && $deliverySettings[$key] !== '') {
                        $deliverySettings[$key] = (float) $deliverySettings[$key];
                    }
                }
            }
        }
        
        // Ensure pagination settings have proper default values
        $paginationDefaults = [
            'frontend_pagination_enabled' => true,
            'admin_pagination_enabled' => true,
            'frontend_records_per_page' => 12,
            'admin_records_per_page' => 20,
            'frontend_load_more_enabled' => false,
            'admin_show_per_page_selector' => true,
            'admin_default_sort_order' => 'desc',
            'frontend_default_sort_order' => 'desc',
        ];
        
        foreach ($paginationDefaults as $key => $defaultValue) {
            if (!isset($paginationSettings[$key])) {
                $paginationSettings[$key] = $defaultValue;
            } else {
                // Ensure proper type conversion for boolean settings
                if (in_array($key, ['frontend_pagination_enabled', 'admin_pagination_enabled', 'frontend_load_more_enabled', 'admin_show_per_page_selector'])) {
                    if (is_string($paginationSettings[$key])) {
                        $paginationSettings[$key] = filter_var($paginationSettings[$key], FILTER_VALIDATE_BOOLEAN);
                    } else {
                        $paginationSettings[$key] = (bool) $paginationSettings[$key];
                    }
                } elseif (in_array($key, ['frontend_records_per_page', 'admin_records_per_page'])) {
                    $paginationSettings[$key] = (int) $paginationSettings[$key];
                }
            }
        }
        $whatsappSettings = AppSetting::getGroup('whatsapp');
        $billFormatSettings = AppSetting::getGroup('bill_format');
        $animationSettings = AppSetting::getGroup('animations');
        $invoiceNumberingSettings = AppSetting::getGroup('invoice_numbering');
        
        // Merge theme settings
        $appearanceSettings = array_merge($appearanceSettings, $themeSettings);
        
        // Ensure notification settings have proper default values and boolean conversion
        $notificationDefaults = [
            'email_notifications' => true,
            'whatsapp_notifications' => false,
            'sound_notifications' => true,
            'popup_notifications' => true,
            'order_notifications' => true,
            'low_stock_alert' => true
        ];
        
        foreach ($notificationDefaults as $key => $defaultValue) {
            if (!isset($notificationSettings[$key])) {
                $notificationSettings[$key] = $defaultValue;
            } else {
                // Ensure proper boolean conversion for database values
                if (is_string($notificationSettings[$key])) {
                    $notificationSettings[$key] = filter_var($notificationSettings[$key], FILTER_VALIDATE_BOOLEAN);
                } else {
                    $notificationSettings[$key] = (bool) $notificationSettings[$key];
                }
            }
        }
        
        // Ensure animation settings have proper default values
        $animationDefaults = [
            'frontend_animations_enabled' => 'true',
            'frontend_animation_intensity' => '3',
            'frontend_animation_style' => 'crackers',
            'frontend_celebration_enabled' => 'true',
            'frontend_fireworks_enabled' => 'true',
            'frontend_hover_effects_enabled' => 'true',
            'frontend_loading_animations' => 'true',
            'frontend_page_transitions' => 'true',
            'frontend_welcome_animation' => 'true',
            'animation_duration' => '600',
            'reduce_motion_respect' => 'true'
        ];
        
        foreach ($animationDefaults as $key => $defaultValue) {
            if (!isset($animationSettings[$key])) {
                $animationSettings[$key] = $defaultValue;
            }
        }
        
        // Ensure bill format settings have proper default values and boolean conversion
        $billFormatDefaults = [
            'thermal_printer_enabled' => false,
            'a4_sheet_enabled' => true,
            'default_bill_format' => 'a4_sheet',
            'thermal_printer_width' => 80,
            'thermal_printer_auto_cut' => true,
            'a4_sheet_orientation' => 'portrait',
            'bill_logo_enabled' => true,
            'bill_company_info_enabled' => true
        ];
        
        foreach ($billFormatDefaults as $key => $defaultValue) {
            if (!isset($billFormatSettings[$key])) {
                $billFormatSettings[$key] = $defaultValue;
            } else {
                // Ensure proper boolean conversion for boolean settings
                if (in_array($key, ['thermal_printer_enabled', 'a4_sheet_enabled', 'thermal_printer_auto_cut', 'bill_logo_enabled', 'bill_company_info_enabled'])) {
                    if (is_string($billFormatSettings[$key])) {
                        $billFormatSettings[$key] = filter_var($billFormatSettings[$key], FILTER_VALIDATE_BOOLEAN);
                    } else {
                        $billFormatSettings[$key] = (bool) $billFormatSettings[$key];
                    }
                }
            }
        }
        
        // Ensure invoice numbering settings have proper default values
        $invoiceNumberingDefaults = [
            'order_invoice_prefix' => 'ORD',
            'order_invoice_separator' => '-',
            'order_invoice_digits' => 5,
            'order_invoice_include_year' => true,
            'order_invoice_include_month' => false,
            'order_invoice_reset_yearly' => true,
            'order_invoice_reset_monthly' => false,
            'pos_invoice_prefix' => 'POS',
            'pos_invoice_separator' => '-',
            'pos_invoice_digits' => 4,
            'pos_invoice_include_year' => true,
            'pos_invoice_include_month' => false,
            'pos_invoice_reset_yearly' => true,
            'pos_invoice_reset_monthly' => false
        ];
        
        foreach ($invoiceNumberingDefaults as $key => $defaultValue) {
            if (!isset($invoiceNumberingSettings[$key])) {
                $invoiceNumberingSettings[$key] = $defaultValue;
            } else {
                // Ensure proper type conversion
                if (str_contains($key, '_digits')) {
                    $invoiceNumberingSettings[$key] = (int) $invoiceNumberingSettings[$key];
                } elseif (str_contains($key, '_include_') || str_contains($key, '_reset_')) {
                    if (is_string($invoiceNumberingSettings[$key])) {
                        $invoiceNumberingSettings[$key] = filter_var($invoiceNumberingSettings[$key], FILTER_VALIDATE_BOOLEAN);
                    } else {
                        $invoiceNumberingSettings[$key] = (bool) $invoiceNumberingSettings[$key];
                    }
                }
            }
        }
        
        // Check if WhatsApp is configured (you can enhance this to check Super Admin settings)
        $whatsappConfigured = !empty($whatsappSettings); // Basic check
        $whatsappEnabled = $notificationSettings['whatsapp_notifications'] && $whatsappConfigured;
        
        // Log the notification settings for debugging
        \Log::info('Notification settings loaded', [
            'notificationSettings' => $notificationSettings,
            'whatsappConfigured' => $whatsappConfigured,
            'whatsappEnabled' => $whatsappEnabled,
            'tenant_id' => session('selected_company_id')
        ]);
        
        return view('admin.settings.index', compact(
            'company',
            'appearanceSettings', 
            'notificationSettings',
            'emailSettings',
            'inventorySettings',
            'deliverySettings',
            'paginationSettings',
            'whatsappSettings',
            'billFormatSettings',
            'animationSettings',
            'invoiceNumberingSettings',
            'whatsappConfigured',
            'whatsappEnabled'
        ));
    }

    public function updateCompany(Request $request)
    {
        // Debug: Log all request data to identify any issues
        \Log::info('Company update request data', [
            'all_data' => $request->all(),
            'gpay_number' => $request->gpay_number,
            'gpay_number_exists' => $request->has('gpay_number'),
            'gpay_number_filled' => $request->filled('gpay_number'),
            'user_id' => auth()->id(),
            'company_id' => $this->getCurrentTenantId()
        ]);
        
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'nullable|string|max:30',
            'whatsapp_number' => 'nullable|string|max:30',
            'mobile_number' => 'nullable|string|max:30',
            'alternate_phone' => 'nullable|string|max:30',
            'gpay_number' => 'nullable|string|max:30',
            'company_address' => 'nullable|string|max:500',
            'company_city' => 'nullable|string|max:100',
            'company_state' => 'nullable|string|max:100',
            'company_postal_code' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:15|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9A-Z]{1}[Z]{1}[0-9A-Z]{1}$/',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'announcement_text' => 'nullable|string|max:500',
            'custom_tax_enabled' => 'nullable|boolean',
        ]);
        
        // Additional debug: Log validated data
        \Log::info('Validation passed for company update', [
            'gpay_number_validated' => $validatedData['gpay_number'] ?? 'not present',
            'gpay_from_request' => $request->input('gpay_number', 'not present')
        ]);

        // Get current company
        $company = $this->getCurrentCompany();
        
        if (!$company) {
            return redirect()->back()->with('error', 'Company not found.');
        }

        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            // Delete old logo
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            
            $logoPath = $request->file('company_logo')->store('logos', 'public');
            $company->logo = $logoPath;
        }

        // Update company data
        $company->name = $request->company_name;
        $company->email = $request->company_email;
        $company->phone = $request->company_phone;
        $company->whatsapp_number = $request->whatsapp_number;
        $company->mobile_number = $request->mobile_number;
        $company->alternate_phone = $request->alternate_phone;
        $company->gpay_number = $request->gpay_number;
        $company->address = $request->company_address;
        $company->city = $request->company_city;
        $company->state = $request->company_state;
        $company->postal_code = $request->company_postal_code;
        $company->gst_number = $request->gst_number;
        $company->announcement_text = $request->announcement_text;
        
        // Debug: Log what we're trying to save
        \Log::info('About to save company data', [
            'company_id' => $company->id,
            'gpay_number_before' => $company->getOriginal('gpay_number'),
            'gpay_number_new' => $company->gpay_number,
            'is_dirty' => $company->isDirty('gpay_number'),
            'dirty_attributes' => $company->getDirty()
        ]);
        
        try {
            $saved = $company->save();
            \Log::info('Company save result', ['success' => $saved]);
        } catch (\Exception $e) {
            \Log::error('Failed to save company', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }

        // Debug: Log saved company data
        \Log::info('Company data saved', [
            'company_id' => $company->id,
            'gpay_number' => $company->gpay_number,
            'whatsapp_number' => $company->whatsapp_number,
            'mobile_number' => $company->mobile_number,
            'alternate_phone' => $company->alternate_phone,
        ]);
        
        // Reload from database to verify save
        $company->refresh();
        \Log::info('Company data after refresh', [
            'gpay_number_after_refresh' => $company->gpay_number,
        ]);
        
        // If G Pay number still not saved, try alternative method
        if ($request->filled('gpay_number') && !$company->gpay_number) {
            \Log::warning('G Pay number not saved, trying alternative method');
            try {
                $alternativeResult = $company->update(['gpay_number' => $request->gpay_number]);
                \Log::info('Alternative save method result', ['success' => $alternativeResult]);
                $company->refresh();
                \Log::info('G Pay after alternative method', ['gpay_number' => $company->gpay_number]);
            } catch (\Exception $e) {
                \Log::error('Alternative save method failed', ['error' => $e->getMessage()]);
            }
        }

        // Clear cache
        \Artisan::call('view:clear');
        \Cache::flush();

        return redirect()->back()->with('success', 'Company settings updated successfully!');
    }

    public function updateTheme(Request $request)
    {
        $request->validate([
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'sidebar_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'theme_mode' => 'required|in:light,dark',
        ]);

        AppSetting::set('primary_color', $request->primary_color, 'string', 'theme');
        AppSetting::set('secondary_color', $request->secondary_color, 'string', 'theme');
        AppSetting::set('sidebar_color', $request->sidebar_color, 'string', 'theme');
        AppSetting::set('theme_mode', $request->theme_mode, 'string', 'theme');

        AppSetting::clearCache();

        // Clear view cache to refresh theme changes
        \Artisan::call('view:clear');
        \Cache::flush();

        return redirect()->back()->with('success', 'Theme settings updated successfully!');
    }

    public function updateNotifications(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'whatsapp_notifications' => 'boolean',
            'sound_notifications' => 'boolean',
            'popup_notifications' => 'boolean',
            'low_stock_alert' => 'boolean',
            'order_notifications' => 'boolean',
        ]);

        AppSetting::set('email_notifications', $request->boolean('email_notifications'), 'boolean', 'notifications');
        AppSetting::set('whatsapp_notifications', $request->boolean('whatsapp_notifications'), 'boolean', 'notifications');
        AppSetting::set('sound_notifications', $request->boolean('sound_notifications'), 'boolean', 'notifications');
        AppSetting::set('popup_notifications', $request->boolean('popup_notifications'), 'boolean', 'notifications');
        AppSetting::set('low_stock_alert', $request->boolean('low_stock_alert'), 'boolean', 'notifications');
        AppSetting::set('order_notifications', $request->boolean('order_notifications'), 'boolean', 'notifications');

        AppSetting::clearCache();
        
        return redirect()->back()->with('success', 'Notification settings updated successfully!');
        }
    
    public function updateAnimations(Request $request)
    {
        $request->validate([
            'frontend_animations_enabled' => 'boolean',
            'frontend_animation_intensity' => 'required|integer|min:1|max:5',
            'frontend_animation_style' => 'required|in:modern,crackers,festive,minimal',
            'frontend_celebration_enabled' => 'boolean',
            'frontend_fireworks_enabled' => 'boolean',
            'frontend_hover_effects_enabled' => 'boolean',
            'frontend_loading_animations' => 'boolean',
            'frontend_page_transitions' => 'boolean',
            'frontend_welcome_animation' => 'boolean',
            'animation_duration' => 'required|integer|in:300,600,1000,1500',
            'reduce_motion_respect' => 'boolean',
        ]);

        // Store all animation settings
        AppSetting::set('frontend_animations_enabled', $request->boolean('frontend_animations_enabled') ? 'true' : 'false', 'string', 'animations');
        AppSetting::set('frontend_animation_intensity', $request->input('frontend_animation_intensity', 3), 'string', 'animations');
        AppSetting::set('frontend_animation_style', $request->input('frontend_animation_style', 'crackers'), 'string', 'animations');
        AppSetting::set('frontend_celebration_enabled', $request->boolean('frontend_celebration_enabled') ? 'true' : 'false', 'string', 'animations');
        AppSetting::set('frontend_fireworks_enabled', $request->boolean('frontend_fireworks_enabled') ? 'true' : 'false', 'string', 'animations');
        AppSetting::set('frontend_hover_effects_enabled', $request->boolean('frontend_hover_effects_enabled') ? 'true' : 'false', 'string', 'animations');
        AppSetting::set('frontend_loading_animations', $request->boolean('frontend_loading_animations') ? 'true' : 'false', 'string', 'animations');
        AppSetting::set('frontend_page_transitions', $request->boolean('frontend_page_transitions') ? 'true' : 'false', 'string', 'animations');
        AppSetting::set('frontend_welcome_animation', $request->boolean('frontend_welcome_animation') ? 'true' : 'false', 'string', 'animations');
        AppSetting::set('animation_duration', $request->input('animation_duration', 600), 'string', 'animations');
        AppSetting::set('reduce_motion_respect', $request->boolean('reduce_motion_respect') ? 'true' : 'false', 'string', 'animations');

        AppSetting::clearCache();
        
        // Clear animation cache
        \App\Services\AnimationService::clearCache();
        
        // Clear view cache to ensure new settings take effect
        \Artisan::call('view:clear');
        \Cache::flush();
        
        \Log::info('Animation settings updated', [
            'settings' => $request->only([
                'frontend_animations_enabled',
                'frontend_animation_intensity',
                'frontend_animation_style',
                'frontend_celebration_enabled',
                'frontend_fireworks_enabled',
                'frontend_hover_effects_enabled',
                'frontend_loading_animations',
                'frontend_page_transitions',
                'frontend_welcome_animation',
                'animation_duration',
                'reduce_motion_respect'
            ]),
            'tenant_id' => session('selected_company_id'),
            'user_id' => auth()->id()
        ]);
        
        return redirect()->back()->with('success', 'Animation settings updated successfully!');
    }

    public function updateInvoiceNumbering(Request $request)
    {
        $request->validate([
            'order_invoice_prefix' => 'required|string|max:10|regex:/^[A-Z0-9]+$/',
            'order_invoice_separator' => 'required|string|max:5',
            'order_invoice_digits' => 'required|integer|min:1|max:10',
            'order_invoice_include_year' => 'boolean',
            'order_invoice_include_month' => 'boolean',
            'order_invoice_reset_yearly' => 'boolean',
            'order_invoice_reset_monthly' => 'boolean',
            'pos_invoice_prefix' => 'required|string|max:10|regex:/^[A-Z0-9]+$/',
            'pos_invoice_separator' => 'required|string|max:5',
            'pos_invoice_digits' => 'required|integer|min:1|max:10',
            'pos_invoice_include_year' => 'boolean',
            'pos_invoice_include_month' => 'boolean',
            'pos_invoice_reset_yearly' => 'boolean',
            'pos_invoice_reset_monthly' => 'boolean',
        ]);

        try {
            // Update order invoice settings
            AppSetting::set('order_invoice_prefix', $request->input('order_invoice_prefix'), 'string', 'invoice_numbering');
            AppSetting::set('order_invoice_separator', $request->input('order_invoice_separator'), 'string', 'invoice_numbering');
            AppSetting::set('order_invoice_digits', $request->input('order_invoice_digits'), 'integer', 'invoice_numbering');
            AppSetting::set('order_invoice_include_year', $request->boolean('order_invoice_include_year'), 'boolean', 'invoice_numbering');
            AppSetting::set('order_invoice_include_month', $request->boolean('order_invoice_include_month'), 'boolean', 'invoice_numbering');
            AppSetting::set('order_invoice_reset_yearly', $request->boolean('order_invoice_reset_yearly'), 'boolean', 'invoice_numbering');
            AppSetting::set('order_invoice_reset_monthly', $request->boolean('order_invoice_reset_monthly'), 'boolean', 'invoice_numbering');

            // Update POS invoice settings
            AppSetting::set('pos_invoice_prefix', $request->input('pos_invoice_prefix'), 'string', 'invoice_numbering');
            AppSetting::set('pos_invoice_separator', $request->input('pos_invoice_separator'), 'string', 'invoice_numbering');
            AppSetting::set('pos_invoice_digits', $request->input('pos_invoice_digits'), 'integer', 'invoice_numbering');
            AppSetting::set('pos_invoice_include_year', $request->boolean('pos_invoice_include_year'), 'boolean', 'invoice_numbering');
            AppSetting::set('pos_invoice_include_month', $request->boolean('pos_invoice_include_month'), 'boolean', 'invoice_numbering');
            AppSetting::set('pos_invoice_reset_yearly', $request->boolean('pos_invoice_reset_yearly'), 'boolean', 'invoice_numbering');
            AppSetting::set('pos_invoice_reset_monthly', $request->boolean('pos_invoice_reset_monthly'), 'boolean', 'invoice_numbering');

            AppSetting::clearCache();
            
            \Log::info('Invoice numbering settings updated', [
                'order_settings' => $request->only([
                    'order_invoice_prefix', 'order_invoice_separator', 'order_invoice_digits',
                    'order_invoice_include_year', 'order_invoice_include_month',
                    'order_invoice_reset_yearly', 'order_invoice_reset_monthly'
                ]),
                'pos_settings' => $request->only([
                    'pos_invoice_prefix', 'pos_invoice_separator', 'pos_invoice_digits',
                    'pos_invoice_include_year', 'pos_invoice_include_month',
                    'pos_invoice_reset_yearly', 'pos_invoice_reset_monthly'
                ]),
                'tenant_id' => $this->getCurrentTenantId(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('success', 'Invoice numbering settings updated successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Failed to update invoice numbering settings', [
                'error' => $e->getMessage(),
                'tenant_id' => $this->getCurrentTenantId(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('error', 'Failed to update invoice numbering settings: ' . $e->getMessage());
        }
    }

    public function previewInvoiceNumbers()
    {
        try {
            $invoiceService = new InvoiceNumberService();
            $preview = $invoiceService->previewInvoiceNumbers($this->getCurrentTenantId());
            
            return response()->json([
                'success' => true,
                'preview' => $preview
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to preview invoice numbers', [
                'error' => $e->getMessage(),
                'tenant_id' => $this->getCurrentTenantId()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function resetInvoiceSequences(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:order,pos',
            'confirm' => 'required|boolean|accepted'
        ]);
        
        try {
            $invoiceService = new InvoiceNumberService();
            $result = $invoiceService->resetInvoiceSequences(
                $this->getCurrentTenantId(),
                $request->input('type')
            );
            
            if ($result) {
                $type = $request->input('type') ? ucfirst($request->input('type')) : 'All';
                return redirect()->back()->with('success', $type . ' invoice sequences reset successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to reset invoice sequences.');
            }
            
        } catch (\Exception $e) {
            \Log::error('Failed to reset invoice sequences', [
                'error' => $e->getMessage(),
                'type' => $request->input('type'),
                'tenant_id' => $this->getCurrentTenantId()
            ]);
            
            return redirect()->back()->with('error', 'Failed to reset invoice sequences: ' . $e->getMessage());
        }
    }

    public function profile()
    {
        $user = Auth::user();
        return view('admin.settings.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'address']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }

    public function updateEmail(Request $request)
    {
        $request->validate([
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_username' => 'required|string|max:255',
            'smtp_password' => 'required|string|max:255',
            'smtp_encryption' => 'required|in:tls,ssl,null',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ]);

        // Update email settings
        AppSetting::set('smtp_host', $request->smtp_host, 'string', 'email');
        AppSetting::set('smtp_port', $request->smtp_port, 'string', 'email');
        AppSetting::set('smtp_username', $request->smtp_username, 'string', 'email');
        AppSetting::set('smtp_password', $request->smtp_password, 'string', 'email');
        AppSetting::set('smtp_encryption', $request->smtp_encryption, 'string', 'email');
        AppSetting::set('mail_from_address', $request->mail_from_address, 'string', 'email');
        AppSetting::set('mail_from_name', $request->mail_from_name, 'string', 'email');

        AppSetting::clearCache();

        // Update config dynamically
        $this->updateMailConfig();

        return redirect()->back()->with('success', 'Email settings updated successfully!');
    }

    public function updateInventory(Request $request)
    {
        $request->validate([
            'low_stock_threshold' => 'required|integer|min:1|max:1000',
        ]);

        AppSetting::set('low_stock_threshold', $request->low_stock_threshold, 'integer', 'inventory');
        AppSetting::clearCache();

        return redirect()->back()->with('success', 'Inventory settings updated successfully!');
    }

    public function updateDelivery(Request $request)
    {
        // Debug: Log all request data to identify any issues
        \Log::info('Delivery settings update request data', [
            'all_data' => $request->all(),
            'user_id' => auth()->id(),
            'company_id' => $this->getCurrentTenantId()
        ]);
        
        $request->validate([
            'delivery_enabled' => 'boolean',
            'delivery_charge' => 'required|numeric|min:0|max:9999.99',
            'free_delivery_enabled' => 'boolean',
            'free_delivery_threshold' => 'nullable|numeric|min:0.01|max:999999.99',
            'delivery_max_amount' => 'nullable|numeric|min:0.01|max:999999.99',
            'delivery_time_estimate' => 'nullable|string|max:255',
            'delivery_description' => 'nullable|string|max:500',
            'min_order_validation_enabled' => 'boolean',
            'min_order_amount' => 'nullable|numeric|min:1|max:999999.99',
            'min_order_message' => 'nullable|string|max:255',
        ]);

        try {
            // Always save delivery enabled/disabled status
            AppSetting::set('delivery_enabled', $request->boolean('delivery_enabled'), 'boolean', 'delivery');
            
            // Always save delivery charge (required field)
            AppSetting::set('delivery_charge', $request->delivery_charge, 'float', 'delivery');
            
            // Always save free delivery settings
            AppSetting::set('free_delivery_enabled', $request->boolean('free_delivery_enabled'), 'boolean', 'delivery');
            
            // Save free delivery threshold (always save, even if null/empty to allow clearing)
            AppSetting::set('free_delivery_threshold', $request->free_delivery_threshold ?? null, 'float', 'delivery');
            
            // Always save optional settings (to allow clearing/updating them)
            AppSetting::set('delivery_max_amount', $request->delivery_max_amount ?? null, 'float', 'delivery');
            AppSetting::set('delivery_time_estimate', $request->delivery_time_estimate ?? '', 'string', 'delivery');
            AppSetting::set('delivery_description', $request->delivery_description ?? '', 'string', 'delivery');

            // Always save minimum order validation settings
            AppSetting::set('min_order_validation_enabled', $request->boolean('min_order_validation_enabled'), 'boolean', 'delivery');
            AppSetting::set('min_order_amount', $request->min_order_amount ?? 1000.00, 'float', 'delivery');
            AppSetting::set('min_order_message', $request->min_order_message ?? 'Minimum order amount is ₹1000 for online orders.', 'string', 'delivery');

            // Clear cache to ensure changes take effect immediately
            AppSetting::clearCache();
            
            // Additional cache clearing for reliability
            \Artisan::call('view:clear');
            \Cache::flush();
            
            // Log successful update for debugging
            \Log::info('Delivery settings updated successfully', [
                'settings_saved' => [
                    'delivery_enabled' => $request->boolean('delivery_enabled'),
                    'delivery_charge' => $request->delivery_charge,
                    'free_delivery_enabled' => $request->boolean('free_delivery_enabled'),
                    'free_delivery_threshold' => $request->free_delivery_threshold,
                    'delivery_max_amount' => $request->delivery_max_amount,
                    'delivery_time_estimate' => $request->delivery_time_estimate,
                    'delivery_description' => $request->delivery_description,
                    'min_order_validation_enabled' => $request->boolean('min_order_validation_enabled'),
                    'min_order_amount' => $request->min_order_amount,
                    'min_order_message' => $request->min_order_message,
                ],
                'tenant_id' => $this->getCurrentTenantId(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()->with('success', 'Delivery settings updated successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Failed to update delivery settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'tenant_id' => $this->getCurrentTenantId(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('error', 'Failed to update delivery settings: ' . $e->getMessage());
        }
    }

    public function updatePagination(Request $request)
    {
        $request->validate([
            'frontend_pagination_enabled' => 'boolean',
            'admin_pagination_enabled' => 'boolean',
            'frontend_records_per_page' => 'required|integer|min:5|max:100',
            'admin_records_per_page' => 'required|integer|min:10|max:200',
            'frontend_load_more_enabled' => 'boolean',
            'admin_show_per_page_selector' => 'boolean',
            'admin_default_sort_order' => 'required|in:asc,desc',
            'frontend_default_sort_order' => 'required|in:asc,desc',
        ]);

        try {
            // Update basic pagination settings
            AppSetting::set('frontend_pagination_enabled', $request->boolean('frontend_pagination_enabled'), 'boolean', 'pagination');
            AppSetting::set('admin_pagination_enabled', $request->boolean('admin_pagination_enabled'), 'boolean', 'pagination');
            AppSetting::set('frontend_records_per_page', $request->frontend_records_per_page, 'integer', 'pagination');
            AppSetting::set('admin_records_per_page', $request->admin_records_per_page, 'integer', 'pagination');
            
            // Update advanced pagination settings
            AppSetting::set('frontend_load_more_enabled', $request->boolean('frontend_load_more_enabled'), 'boolean', 'pagination');
            AppSetting::set('admin_show_per_page_selector', $request->boolean('admin_show_per_page_selector'), 'boolean', 'pagination');
            AppSetting::set('admin_default_sort_order', $request->admin_default_sort_order, 'string', 'pagination');
            AppSetting::set('frontend_default_sort_order', $request->frontend_default_sort_order, 'string', 'pagination');
            
            AppSetting::clearCache();
            
            \Log::info('Pagination settings updated successfully', [
                'frontend_pagination_enabled' => $request->boolean('frontend_pagination_enabled'),
                'admin_pagination_enabled' => $request->boolean('admin_pagination_enabled'),
                'frontend_records_per_page' => $request->frontend_records_per_page,
                'admin_records_per_page' => $request->admin_records_per_page,
                'frontend_load_more_enabled' => $request->boolean('frontend_load_more_enabled'),
                'admin_show_per_page_selector' => $request->boolean('admin_show_per_page_selector'),
                'admin_default_sort_order' => $request->admin_default_sort_order,
                'frontend_default_sort_order' => $request->frontend_default_sort_order,
                'tenant_id' => $this->getCurrentTenantId(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()->with('success', 'Pagination settings updated successfully!');
            
        } catch (\Exception $e) {
            \Log::error('Failed to update pagination settings', [
                'error' => $e->getMessage(),
                'tenant_id' => $this->getCurrentTenantId(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('error', 'Failed to update pagination settings: ' . $e->getMessage());
        }
    }

    public function updateWhatsAppTemplates(Request $request)
    {
        $request->validate([
            'whatsapp_template_pending' => 'nullable|string|max:1000',
            'whatsapp_template_processing' => 'nullable|string|max:1000',
            'whatsapp_template_shipped' => 'nullable|string|max:1000',
            'whatsapp_template_delivered' => 'nullable|string|max:1000',
            'whatsapp_template_cancelled' => 'nullable|string|max:1000',
            'whatsapp_template_payment_confirmed' => 'nullable|string|max:1000',
        ]);

        // Log the request data for debugging
        \Log::info('WhatsApp templates update request', [
            'templates' => $request->only([
                'whatsapp_template_pending',
                'whatsapp_template_processing',
                'whatsapp_template_shipped',
                'whatsapp_template_delivered',
                'whatsapp_template_cancelled',
                'whatsapp_template_payment_confirmed'
            ]),
            'tenant_id' => session('selected_company_id'),
            'user_id' => auth()->id()
        ]);

        // Update WhatsApp message templates with explicit empty string handling
        AppSetting::set('whatsapp_template_pending', $request->whatsapp_template_pending ?? '', 'string', 'whatsapp');
        AppSetting::set('whatsapp_template_processing', $request->whatsapp_template_processing ?? '', 'string', 'whatsapp');
        AppSetting::set('whatsapp_template_shipped', $request->whatsapp_template_shipped ?? '', 'string', 'whatsapp');
        AppSetting::set('whatsapp_template_delivered', $request->whatsapp_template_delivered ?? '', 'string', 'whatsapp');
        AppSetting::set('whatsapp_template_cancelled', $request->whatsapp_template_cancelled ?? '', 'string', 'whatsapp');
        AppSetting::set('whatsapp_template_payment_confirmed', $request->whatsapp_template_payment_confirmed ?? '', 'string', 'whatsapp');
        
        // Force clear all caches
        AppSetting::clearCache();
        
        // Clear Laravel caches
        try {
            \Artisan::call('view:clear');
            \Artisan::call('config:clear');
            \Artisan::call('cache:clear');
            \Log::info('WhatsApp templates caches cleared');
        } catch (\Exception $e) {
            \Log::warning('Failed to clear some caches: ' . $e->getMessage());
        }

        // Verify the save by reading back the values
        $savedTemplates = [];
        $templateKeys = [
            'whatsapp_template_pending',
            'whatsapp_template_processing',
            'whatsapp_template_shipped',
            'whatsapp_template_delivered',
            'whatsapp_template_cancelled',
            'whatsapp_template_payment_confirmed'
        ];
        
        foreach ($templateKeys as $key) {
            $savedTemplates[$key] = AppSetting::get($key);
        }
        
        \Log::info('WhatsApp templates saved values', $savedTemplates);

        return redirect()->back()->with('success', 'WhatsApp message templates updated successfully!');
    }

    /**
     * Update bill format settings
     */
    public function updateBillFormat(Request $request)
    {
        $request->validate([
            'thermal_printer_enabled' => 'boolean',
            'a4_sheet_enabled' => 'boolean',
            'default_bill_format' => 'required|in:thermal,a4_sheet',
            'thermal_printer_width' => 'nullable|integer|min:50|max:120',
            'thermal_printer_auto_cut' => 'boolean',
            'a4_sheet_orientation' => 'required|in:portrait,landscape',
            'bill_logo_enabled' => 'boolean',
            'bill_company_info_enabled' => 'boolean'
        ]);

        // Ensure at least one format is enabled
        $thermalEnabled = $request->boolean('thermal_printer_enabled');
        $a4Enabled = $request->boolean('a4_sheet_enabled');
        
        if (!$thermalEnabled && !$a4Enabled) {
            return redirect()->back()->withErrors([
                'bill_format' => 'At least one bill format must be enabled.'
            ]);
        }

        // Validate default format is among enabled formats
        $defaultFormat = $request->default_bill_format;
        if ($defaultFormat === 'thermal' && !$thermalEnabled) {
            return redirect()->back()->withErrors([
                'default_bill_format' => 'Cannot set thermal as default when thermal printer is disabled.'
            ]);
        }
        if ($defaultFormat === 'a4_sheet' && !$a4Enabled) {
            return redirect()->back()->withErrors([
                'default_bill_format' => 'Cannot set A4 sheet as default when A4 sheet is disabled.'
            ]);
        }

        try {
            // Update bill format settings
            AppSetting::set('thermal_printer_enabled', $thermalEnabled, 'boolean', 'bill_format');
            AppSetting::set('a4_sheet_enabled', $a4Enabled, 'boolean', 'bill_format');
            AppSetting::set('default_bill_format', $defaultFormat, 'string', 'bill_format');
            AppSetting::set('thermal_printer_width', $request->thermal_printer_width ?? 80, 'integer', 'bill_format');
            AppSetting::set('thermal_printer_auto_cut', $request->boolean('thermal_printer_auto_cut'), 'boolean', 'bill_format');
            AppSetting::set('a4_sheet_orientation', $request->a4_sheet_orientation, 'string', 'bill_format');
            AppSetting::set('bill_logo_enabled', $request->boolean('bill_logo_enabled'), 'boolean', 'bill_format');
            AppSetting::set('bill_company_info_enabled', $request->boolean('bill_company_info_enabled'), 'boolean', 'bill_format');

            // Clear cache to ensure settings take effect immediately
            AppSetting::clearCache();
            
            // Clear other caches
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }

            // Log the successful update
            \Log::info('Bill format settings updated', [
                'thermal_enabled' => $thermalEnabled,
                'a4_enabled' => $a4Enabled,
                'default_format' => $defaultFormat,
                'company_id' => $this->getCurrentTenantId(),
                'user_id' => auth()->id()
            ]);

            return redirect()->back()->with('success', 'Bill format settings updated successfully! Changes will apply to new bills immediately.');
            
        } catch (\Exception $e) {
            \Log::error('Failed to update bill format settings', [
                'error' => $e->getMessage(),
                'company_id' => $this->getCurrentTenantId(),
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('error', 'Failed to update bill format settings: ' . $e->getMessage());
        }
    }

    /**
     * Get current tenant ID for settings operations
     */
    private function getCurrentTenantId()
    {
        // Try multiple sources for company_id
        if (app()->has('current_tenant')) {
            return app('current_tenant')->id;
        } elseif (request()->has('current_company_id')) {
            return request()->get('current_company_id');
        } elseif (session()->has('selected_company_id')) {
            return session('selected_company_id');
        } elseif (auth()->check() && auth()->user()->company_id) {
            return auth()->user()->company_id;
        }
        
        return null;
    }

    public function testEmail(Request $request)
    {
        try {
            $this->updateMailConfig();
            
            Mail::raw('This is a test email from your Herbal ERP system.', function ($message) {
                $message->to(Auth::user()->email)
                        ->subject('Test Email - Herbal ERP');
            });
            
            return response()->json(['success' => true, 'message' => 'Test email sent successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send test email: ' . $e->getMessage()]);
        }
    }

    private function updateMailConfig()
    {
        Config::set('mail.mailers.smtp.host', AppSetting::get('smtp_host'));
        Config::set('mail.mailers.smtp.port', AppSetting::get('smtp_port'));
        Config::set('mail.mailers.smtp.username', AppSetting::get('smtp_username'));
        Config::set('mail.mailers.smtp.password', AppSetting::get('smtp_password'));
        Config::set('mail.mailers.smtp.encryption', AppSetting::get('smtp_encryption'));
        Config::set('mail.from.address', AppSetting::get('mail_from_address'));
        Config::set('mail.from.name', AppSetting::get('mail_from_name'));
    }
    
    /**
     * Get current company from various sources
     */
    private function getCurrentCompany()
    {
        $companyId = $this->getCurrentTenantId();
        return $companyId ? Company::find($companyId) : null;
    }
    
    /**
     * Test method to debug G Pay field - REMOVE AFTER TESTING
     */
    public function testGPay()
    {
        $company = $this->getCurrentCompany();
        
        if (!$company) {
            return response()->json([
                'error' => 'No company found',
                'tenant_id' => $this->getCurrentTenantId()
            ]);
        }
        
        return response()->json([
            'company_id' => $company->id,
            'company_name' => $company->name,
            'gpay_number' => $company->gpay_number ?? 'NULL',
            'whatsapp_number' => $company->whatsapp_number ?? 'NULL',
            'mobile_number' => $company->mobile_number ?? 'NULL',
            'alternate_phone' => $company->alternate_phone ?? 'NULL',
            'fillable_fields' => $company->getFillable(),
            'all_attributes' => $company->getAttributes()
        ]);
    }
    
    /**
     * Show G Pay test page
     */
    public function showGPayTest()
    {
        $company = $this->getCurrentCompany();
        return view('admin.settings.gpay-test', compact('company'));
    }
    
    /**
     * Test G Pay save functionality
     */
    public function testGPaySave(Request $request)
    {
        $company = $this->getCurrentCompany();
        
        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'No company found'
            ]);
        }
        
        $gpayNumber = $request->input('gpay_number');
        $originalValue = $company->gpay_number;
        
        try {
            // Test direct assignment
            $company->gpay_number = $gpayNumber;
            $saved = $company->save();
            
            // Refresh from database
            $company->refresh();
            $dbValue = $company->gpay_number;
            
            // Restore original if this was just a test
            if ($request->input('test_mode')) {
                $company->gpay_number = $originalValue;
                $company->save();
            }
            
            return response()->json([
                'success' => $saved && $dbValue === $gpayNumber,
                'message' => $saved ? 'Save operation completed' : 'Save operation failed',
                'saved_value' => $gpayNumber,
                'db_value' => $dbValue,
                'original_value' => $originalValue,
                'save_result' => $saved
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'saved_value' => $gpayNumber,
                'db_value' => null
            ]);
        }
    }
}
