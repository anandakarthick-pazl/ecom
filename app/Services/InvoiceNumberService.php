<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\Order;
use App\Models\PosSale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceNumberService
{
    /**
     * Generate invoice number for online orders
     */
    public function generateOrderInvoiceNumber($companyId = null): string
    {
        try {
            $companyId = $companyId ?? $this->getCurrentTenantId();
            
            // Get configuration settings for online orders
            $prefix = AppSetting::getForTenant('order_invoice_prefix', $companyId) ?? 'ORD';
            $separator = AppSetting::getForTenant('order_invoice_separator', $companyId) ?? '-';
            $digits = (int) (AppSetting::getForTenant('order_invoice_digits', $companyId) ?? 5);
            $includeYear = AppSetting::getForTenant('order_invoice_include_year', $companyId) ?? true;
            $includeMonth = AppSetting::getForTenant('order_invoice_include_month', $companyId) ?? false;
            $resetYearly = AppSetting::getForTenant('order_invoice_reset_yearly', $companyId) ?? true;
            $resetMonthly = AppSetting::getForTenant('order_invoice_reset_monthly', $companyId) ?? false;
            
            // Convert string boolean values to actual booleans
            $includeYear = is_string($includeYear) ? filter_var($includeYear, FILTER_VALIDATE_BOOLEAN) : (bool) $includeYear;
            $includeMonth = is_string($includeMonth) ? filter_var($includeMonth, FILTER_VALIDATE_BOOLEAN) : (bool) $includeMonth;
            $resetYearly = is_string($resetYearly) ? filter_var($resetYearly, FILTER_VALIDATE_BOOLEAN) : (bool) $resetYearly;
            $resetMonthly = is_string($resetMonthly) ? filter_var($resetMonthly, FILTER_VALIDATE_BOOLEAN) : (bool) $resetMonthly;
            
            // Generate the next sequence number
            $nextNumber = $this->getNextSequenceNumber('order', $companyId, $resetYearly, $resetMonthly);
            
            // Build the invoice number
            $invoiceNumber = $this->buildInvoiceNumber(
                $prefix,
                $separator,
                $digits,
                $nextNumber,
                $includeYear,
                $includeMonth
            );
            
            // Ensure uniqueness
            $invoiceNumber = $this->ensureUniqueness($invoiceNumber, 'order', $companyId);
            
            Log::info('Generated order invoice number', [
                'company_id' => $companyId,
                'invoice_number' => $invoiceNumber,
                'sequence' => $nextNumber,
                'prefix' => $prefix,
                'separator' => $separator,
                'digits' => $digits
            ]);
            
            return $invoiceNumber;
            
        } catch (\Exception $e) {
            Log::error('Failed to generate order invoice number', [
                'company_id' => $companyId,
                'error' => $e->getMessage()
            ]);
            
            // Fallback to original random generation
            return 'HB' . date('Y') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        }
    }
    
    /**
     * Generate invoice number for POS sales
     */
    public function generatePosInvoiceNumber($companyId = null): string
    {
        try {
            $companyId = $companyId ?? $this->getCurrentTenantId();
            
            // Get configuration settings for POS sales
            $prefix = AppSetting::getForTenant('pos_invoice_prefix', $companyId) ?? 'POS';
            $separator = AppSetting::getForTenant('pos_invoice_separator', $companyId) ?? '-';
            $digits = (int) (AppSetting::getForTenant('pos_invoice_digits', $companyId) ?? 4);
            $includeYear = AppSetting::getForTenant('pos_invoice_include_year', $companyId) ?? true;
            $includeMonth = AppSetting::getForTenant('pos_invoice_include_month', $companyId) ?? false;
            $resetYearly = AppSetting::getForTenant('pos_invoice_reset_yearly', $companyId) ?? true;
            $resetMonthly = AppSetting::getForTenant('pos_invoice_reset_monthly', $companyId) ?? false;
            
            // Convert string boolean values to actual booleans
            $includeYear = is_string($includeYear) ? filter_var($includeYear, FILTER_VALIDATE_BOOLEAN) : (bool) $includeYear;
            $includeMonth = is_string($includeMonth) ? filter_var($includeMonth, FILTER_VALIDATE_BOOLEAN) : (bool) $includeMonth;
            $resetYearly = is_string($resetYearly) ? filter_var($resetYearly, FILTER_VALIDATE_BOOLEAN) : (bool) $resetYearly;
            $resetMonthly = is_string($resetMonthly) ? filter_var($resetMonthly, FILTER_VALIDATE_BOOLEAN) : (bool) $resetMonthly;
            
            // Generate the next sequence number
            $nextNumber = $this->getNextSequenceNumber('pos', $companyId, $resetYearly, $resetMonthly);
            
            // Build the invoice number
            $invoiceNumber = $this->buildInvoiceNumber(
                $prefix,
                $separator,
                $digits,
                $nextNumber,
                $includeYear,
                $includeMonth
            );
            
            // Ensure uniqueness
            $invoiceNumber = $this->ensureUniqueness($invoiceNumber, 'pos', $companyId);
            
            Log::info('Generated POS invoice number', [
                'company_id' => $companyId,
                'invoice_number' => $invoiceNumber,
                'sequence' => $nextNumber,
                'prefix' => $prefix,
                'separator' => $separator,
                'digits' => $digits
            ]);
            
            return $invoiceNumber;
            
        } catch (\Exception $e) {
            Log::error('Failed to generate POS invoice number', [
                'company_id' => $companyId,
                'error' => $e->getMessage()
            ]);
            
            // Fallback to original random generation
            return 'INV' . date('Ymd') . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        }
    }
    
    /**
     * Get the next sequence number for invoice generation
     */
    private function getNextSequenceNumber(string $type, $companyId, bool $resetYearly = true, bool $resetMonthly = false): int
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        
        // Build cache key based on reset preferences
        $cacheKey = "invoice_sequence_{$type}_{$companyId}";
        if ($resetYearly) {
            $cacheKey .= "_{$currentYear}";
        }
        if ($resetMonthly) {
            $cacheKey .= "_{$currentMonth}";
        }
        
        try {
            // Get current sequence from app_settings
            $currentSequence = (int) AppSetting::getForTenant($cacheKey, $companyId) ?? 0;
            
            // Increment the sequence
            $nextSequence = $currentSequence + 1;
            
            // Store the updated sequence
            AppSetting::setForTenant($cacheKey, $nextSequence, $companyId, 'integer', 'invoice_sequences');
            
            return $nextSequence;
            
        } catch (\Exception $e) {
            Log::error('Failed to get next sequence number', [
                'type' => $type,
                'company_id' => $companyId,
                'cache_key' => $cacheKey,
                'error' => $e->getMessage()
            ]);
            
            // Fallback to timestamp-based number
            return (int) substr(time(), -6);
        }
    }
    
    /**
     * Build the invoice number from components
     */
    private function buildInvoiceNumber(
        string $prefix,
        string $separator,
        int $digits,
        int $sequence,
        bool $includeYear = true,
        bool $includeMonth = false
    ): string {
        $parts = [$prefix];
        
        if ($includeYear) {
            $parts[] = date('Y');
        }
        
        if ($includeMonth) {
            $parts[] = date('m');
        }
        
        // Add zero-padded sequence number
        $parts[] = str_pad($sequence, $digits, '0', STR_PAD_LEFT);
        
        return implode($separator, $parts);
    }
    
    /**
     * Ensure the generated invoice number is unique
     */
    private function ensureUniqueness(string $invoiceNumber, string $type, $companyId): string
    {
        $originalNumber = $invoiceNumber;
        $counter = 1;
        
        while ($this->invoiceNumberExists($invoiceNumber, $type, $companyId)) {
            // Add suffix to make it unique
            $invoiceNumber = $originalNumber . '-' . str_pad($counter, 2, '0', STR_PAD_LEFT);
            $counter++;
            
            // Prevent infinite loop
            if ($counter > 999) {
                $invoiceNumber = $originalNumber . '-' . time();
                break;
            }
        }
        
        return $invoiceNumber;
    }
    
    /**
     * Check if invoice number already exists
     */
    private function invoiceNumberExists(string $invoiceNumber, string $type, $companyId): bool
    {
        if ($type === 'order') {
            $query = Order::where('order_number', $invoiceNumber);
            if ($companyId) {
                $query->where('company_id', $companyId);
            }
            return $query->exists();
        } else {
            $query = PosSale::where('invoice_number', $invoiceNumber);
            if ($companyId) {
                $query->where('company_id', $companyId);
            }
            return $query->exists();
        }
    }
    
    /**
     * Get current tenant ID
     */
    private function getCurrentTenantId()
    {
        try {
            if (app()->has('current_tenant')) {
                $tenant = app('current_tenant');
                if ($tenant && isset($tenant->id)) {
                    return $tenant->id;
                }
            }
            
            if (request()->has('current_company_id')) {
                return request()->get('current_company_id');
            }
            
            if (session()->has('selected_company_id')) {
                return session('selected_company_id');
            }
            
            if (auth()->check() && auth()->user()->company_id) {
                return auth()->user()->company_id;
            }
            
            // Try to get company from domain
            $host = request()->getHost();
            $company = \App\Models\SuperAdmin\Company::where('domain', $host)->first();
            if ($company) {
                return $company->id;
            }
            
        } catch (\Exception $e) {
            Log::warning('Error getting tenant ID in InvoiceNumberService', [
                'error' => $e->getMessage()
            ]);
        }
        
        return null;
    }
    
    /**
     * Reset invoice sequences for a company (admin function)
     */
    public function resetInvoiceSequences($companyId, $type = null): bool
    {
        try {
            $currentYear = date('Y');
            $currentMonth = date('m');
            
            // Define sequence keys to reset
            $sequenceKeys = [];
            
            if (!$type || $type === 'order') {
                $sequenceKeys[] = "invoice_sequence_order_{$companyId}";
                $sequenceKeys[] = "invoice_sequence_order_{$companyId}_{$currentYear}";
                $sequenceKeys[] = "invoice_sequence_order_{$companyId}_{$currentYear}_{$currentMonth}";
            }
            
            if (!$type || $type === 'pos') {
                $sequenceKeys[] = "invoice_sequence_pos_{$companyId}";
                $sequenceKeys[] = "invoice_sequence_pos_{$companyId}_{$currentYear}";
                $sequenceKeys[] = "invoice_sequence_pos_{$companyId}_{$currentMonth}";
            }
            
            // Reset all sequence keys to 0
            foreach ($sequenceKeys as $key) {
                AppSetting::setForTenant($key, 0, $companyId, 'integer', 'invoice_sequences');
            }
            
            Log::info('Invoice sequences reset successfully', [
                'company_id' => $companyId,
                'type' => $type ?? 'all',
                'keys_reset' => count($sequenceKeys)
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Failed to reset invoice sequences', [
                'company_id' => $companyId,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Get invoice configuration for a company
     */
    public function getInvoiceConfig($companyId = null): array
    {
        $companyId = $companyId ?? $this->getCurrentTenantId();
        
        return [
            'order' => [
                'prefix' => AppSetting::getForTenant('order_invoice_prefix', $companyId) ?? 'ORD',
                'separator' => AppSetting::getForTenant('order_invoice_separator', $companyId) ?? '-',
                'digits' => (int) (AppSetting::getForTenant('order_invoice_digits', $companyId) ?? 5),
                'include_year' => AppSetting::getForTenant('order_invoice_include_year', $companyId) ?? true,
                'include_month' => AppSetting::getForTenant('order_invoice_include_month', $companyId) ?? false,
                'reset_yearly' => AppSetting::getForTenant('order_invoice_reset_yearly', $companyId) ?? true,
                'reset_monthly' => AppSetting::getForTenant('order_invoice_reset_monthly', $companyId) ?? false,
            ],
            'pos' => [
                'prefix' => AppSetting::getForTenant('pos_invoice_prefix', $companyId) ?? 'POS',
                'separator' => AppSetting::getForTenant('pos_invoice_separator', $companyId) ?? '-',
                'digits' => (int) (AppSetting::getForTenant('pos_invoice_digits', $companyId) ?? 4),
                'include_year' => AppSetting::getForTenant('pos_invoice_include_year', $companyId) ?? true,
                'include_month' => AppSetting::getForTenant('pos_invoice_include_month', $companyId) ?? false,
                'reset_yearly' => AppSetting::getForTenant('pos_invoice_reset_yearly', $companyId) ?? true,
                'reset_monthly' => AppSetting::getForTenant('pos_invoice_reset_monthly', $companyId) ?? false,
            ]
        ];
    }
    
    /**
     * Preview what the next invoice numbers would look like
     */
    public function previewInvoiceNumbers($companyId = null): array
    {
        $companyId = $companyId ?? $this->getCurrentTenantId();
        $config = $this->getInvoiceConfig($companyId);
        
        // Get next sequence numbers without incrementing them
        $orderSequence = $this->getNextSequenceNumber('order', $companyId, false, false) + 1; // Preview next
        $posSequence = $this->getNextSequenceNumber('pos', $companyId, false, false) + 1; // Preview next
        
        return [
            'order' => [
                'current_config' => $config['order'],
                'preview' => $this->buildInvoiceNumber(
                    $config['order']['prefix'],
                    $config['order']['separator'],
                    $config['order']['digits'],
                    $orderSequence,
                    $config['order']['include_year'],
                    $config['order']['include_month']
                ),
                'next_sequence' => $orderSequence
            ],
            'pos' => [
                'current_config' => $config['pos'],
                'preview' => $this->buildInvoiceNumber(
                    $config['pos']['prefix'],
                    $config['pos']['separator'],
                    $config['pos']['digits'],
                    $posSequence,
                    $config['pos']['include_year'],
                    $config['pos']['include_month']
                ),
                'next_sequence' => $posSequence
            ]
        ];
    }
}
