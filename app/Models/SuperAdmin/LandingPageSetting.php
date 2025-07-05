<?php

namespace App\Models\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'section',
        'key',
        'value',
        'type',
        'is_active'
    ];

    protected $casts = [
        'value' => 'array',
        'is_active' => 'boolean'
    ];

    const SECTIONS = [
        'hero' => 'Hero Section',
        'features' => 'Features Section',
        'themes' => 'Themes Section',
        'pricing' => 'Pricing Section',
        'testimonials' => 'Testimonials Section',
        'contact' => 'Contact Section',
        'footer' => 'Footer Section'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySection($query, $section)
    {
        return $query->where('section', $section);
    }
}
