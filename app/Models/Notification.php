<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenantEnhanced;

class Notification extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

    protected $fillable = [
        'company_id',
        'type',
        'title',
        'message',
        'data',
        'user_id',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for admin notifications
     */
    public function scopeForAdmin($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId = null)
    {
        if ($notificationId) {
            $notification = self::find($notificationId);
            if ($notification) {
                $notification->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);
                return $notification;
            }
        } else {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
            return $this;
        }
        
        return null;
    }

    /**
     * Create admin notification
     */
    public static function createForAdmin($type, $title, $message, $data = [])
    {
        // Get current company ID from various sources
        $companyId = null;
        if (app()->has('current_tenant')) {
            $companyId = app('current_tenant')->id;
        } elseif (isset($data['order_id'])) {
            // If we have an order_id, get company_id from the order
            $order = \App\Models\Order::find($data['order_id']);
            if ($order && $order->company_id) {
                $companyId = $order->company_id;
            }
        } elseif (session()->has('selected_company_id')) {
            $companyId = session('selected_company_id');
        }
        
        return self::create([
            'company_id' => $companyId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'user_id' => null
        ]);
    }

    /**
     * Get icon for notification type
     */
    public function getIconAttribute()
    {
        return match($this->type) {
            'order_placed' => 'fas fa-shopping-cart',
            'order_updated' => 'fas fa-edit',
            'low_stock' => 'fas fa-exclamation-triangle',
            'customer_registered' => 'fas fa-user-plus',
            'payment_received' => 'fas fa-money-bill',
            default => 'fas fa-bell'
        };
    }

    /**
     * Get color for notification type
     */
    public function getColorAttribute()
    {
        return match($this->type) {
            'order_placed' => 'success',
            'order_updated' => 'info',
            'low_stock' => 'warning',
            'customer_registered' => 'primary',
            'payment_received' => 'success',
            default => 'secondary'
        };
    }
}
