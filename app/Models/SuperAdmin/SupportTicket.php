<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class SupportTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'user_id',
        'title',
        'description',
        'priority',
        'status',
        'category',
        'assigned_to',
        'resolution',
        'resolved_at'
    ];

    protected $casts = [
        'resolved_at' => 'datetime'
    ];

    const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent'
    ];

    const STATUSES = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'waiting_customer' => 'Waiting for Customer',
        'resolved' => 'Resolved',
        'closed' => 'Closed'
    ];

    const CATEGORIES = [
        'technical' => 'Technical Issue',
        'billing' => 'Billing',
        'feature_request' => 'Feature Request',
        'general' => 'General Inquiry',
        'bug_report' => 'Bug Report'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'waiting_customer']);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    public function isOpen()
    {
        return in_array($this->status, ['open', 'in_progress', 'waiting_customer']);
    }

    public function getPriorityNameAttribute()
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }

    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getCategoryNameAttribute()
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
