<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Traits\BelongsToTenantEnhanced;

class AppSetting extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'company_id'
    ];

    protected $casts = [
        'value' => 'string'
    ];

    /**
     * Get the current tenant ID from various sources
     */
    protected static function getCurrentTenantId()
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

    /**
     * Get setting value by key for current tenant
     */
    public static function get($key, $default = null)
    {
        $tenantId = static::getCurrentTenantId();
        $cacheKey = "setting:{$tenantId}:{$key}";
        
        $setting = Cache::remember($cacheKey, 3600, function () use ($key, $tenantId) {
            $query = self::where('key', $key);
            if ($tenantId) {
                $query->where('company_id', $tenantId);
            }
            return $query->first();
        });

        if (!$setting) {
            return $default;
        }

        return match($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            'integer' => (int) $setting->value,
            'float' => (float) $setting->value,
            default => $setting->value
        };
    }

    /**
     * Set setting value for current tenant
     */
    public static function set($key, $value, $type = 'string', $group = 'general')
    {
        $tenantId = static::getCurrentTenantId();
        
        $data = [
            'value' => is_array($value) || is_object($value) ? json_encode($value) : $value,
            'type' => $type,
            'group' => $group
        ];
        
        // Build the where conditions
        $whereConditions = ['key' => $key];
        
        if ($tenantId) {
            $whereConditions['company_id'] = $tenantId;
            $data['company_id'] = $tenantId;
        } else {
            // For settings without a company_id, ensure we're updating the right record
            $whereConditions['company_id'] = null;
        }
        
        $setting = self::updateOrCreate(
            $whereConditions,
            $data
        );

        // Clear cache
        $cacheKey = "setting:{$tenantId}:{$key}";
        Cache::forget($cacheKey);
        return $setting;
    }

    /**
     * Get all settings by group for current tenant
     */
    public static function getGroup($group)
    {
        $tenantId = static::getCurrentTenantId();
        $cacheKey = "settings:group:{$tenantId}:{$group}";
        
        return Cache::remember($cacheKey, 3600, function () use ($group, $tenantId) {
            $query = self::where('group', $group);
            if ($tenantId) {
                $query->where('company_id', $tenantId);
            }
            return $query->get()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Clear settings cache for current tenant
     */
    public static function clearCache()
    {
        $tenantId = static::getCurrentTenantId();
        
        if ($tenantId) {
            // Clear all WhatsApp template cache keys specifically
            $whatsappTemplateKeys = [
                'whatsapp_template_pending',
                'whatsapp_template_processing', 
                'whatsapp_template_shipped',
                'whatsapp_template_delivered',
                'whatsapp_template_cancelled',
                'whatsapp_template_payment_confirmed'
            ];
            
            foreach ($whatsappTemplateKeys as $key) {
                Cache::forget("setting:{$tenantId}:{$key}");
            }
            
            // Clear group cache for whatsapp
            Cache::forget("settings:group:{$tenantId}:whatsapp");
            
            // Clear other common setting groups
            $groups = ['theme', 'notifications', 'email', 'inventory', 'delivery', 'pagination'];
            foreach ($groups as $group) {
                Cache::forget("settings:group:{$tenantId}:{$group}");
            }
            
            // Clear any opcode cache
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
        } else {
            Cache::flush();
        }
    }

    /**
     * Get setting for specific tenant (for super admin use)
     */
    public static function getForTenant($key, $tenantId, $default = null)
    {
        $cacheKey = "setting:{$tenantId}:{$key}";
        
        $setting = Cache::remember($cacheKey, 3600, function () use ($key, $tenantId) {
            return self::withoutTenantScope()
                ->where('key', $key)
                ->where('company_id', $tenantId)
                ->first();
        });

        if (!$setting) {
            return $default;
        }

        return match($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            'integer' => (int) $setting->value,
            'float' => (float) $setting->value,
            default => $setting->value
        };
    }

    /**
     * Set setting for specific tenant (for super admin use)
     */
    public static function setForTenant($key, $value, $tenantId, $type = 'string', $group = 'general')
    {
        $data = [
            'value' => is_array($value) || is_object($value) ? json_encode($value) : $value,
            'type' => $type,
            'group' => $group,
            'company_id' => $tenantId
        ];
        
        $setting = self::withoutTenantScope()->updateOrCreate(
            [
                'key' => $key,
                'company_id' => $tenantId
            ],
            $data
        );

        $cacheKey = "setting:{$tenantId}:{$key}";
        Cache::forget($cacheKey);
        return $setting;
    }
}
