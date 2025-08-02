<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenantEnhanced;

class Location extends Model
{
    use HasFactory, BelongsToTenantEnhanced;

    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'phone',
        'email',
        'description',
        'working_hours',
        'image',
        'is_active',
        'sort_order',
        'company_id'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'working_hours' => 'array',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Get image URL
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default-location.jpg');
    }

    // Get formatted working hours
    public function getFormattedWorkingHoursAttribute()
    {
        if (!$this->working_hours) {
            return 'Hours not specified';
        }

        $hours = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        foreach ($days as $day) {
            if (isset($this->working_hours[$day])) {
                $dayInfo = $this->working_hours[$day];
                if ($dayInfo['is_open']) {
                    $hours[] = ucfirst($day) . ': ' . $dayInfo['open'] . ' - ' . $dayInfo['close'];
                } else {
                    $hours[] = ucfirst($day) . ': Closed';
                }
            }
        }

        return implode('<br>', $hours);
    }

    // Check if location is open now
    public function getIsOpenNowAttribute()
    {
        if (!$this->working_hours) {
            return null;
        }

        $now = now();
        $currentDay = strtolower($now->format('l'));
        $currentTime = $now->format('H:i');

        if (!isset($this->working_hours[$currentDay])) {
            return false;
        }

        $dayInfo = $this->working_hours[$currentDay];
        
        if (!$dayInfo['is_open']) {
            return false;
        }

        return $currentTime >= $dayInfo['open'] && $currentTime <= $dayInfo['close'];
    }

    // Get distance from given coordinates (in kilometers)
    public function getDistanceFrom($lat, $lng)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latFrom = deg2rad($lat);
        $lonFrom = deg2rad($lng);
        $latTo = deg2rad($this->latitude);
        $lonTo = deg2rad($this->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }
}
