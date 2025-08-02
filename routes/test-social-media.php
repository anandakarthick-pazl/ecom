<?php

use Illuminate\Support\Facades\Route;

// Test route to verify social media edit fix
Route::get('/test-social-media-routes', function () {
    $html = '<!DOCTYPE html><html><head><title>Social Media Routes Test</title>';
    $html .= '<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style></head><body>';
    
    $html .= '<h1>Social Media Routes Test</h1>';
    
    try {
        // Check if routes are loaded
        $routes = [
            'admin.social-media.index',
            'admin.social-media.create', 
            'admin.social-media.edit',
            'admin.social-media.update',
            'admin.social-media.destroy'
        ];
        
        $html .= '<h2>Route Status:</h2><ul>';
        foreach ($routes as $routeName) {
            try {
                $url = route($routeName, $routeName === 'admin.social-media.edit' || $routeName === 'admin.social-media.update' || $routeName === 'admin.social-media.destroy' ? 1 : []);
                $html .= '<li class="success">✓ ' . $routeName . ' → ' . $url . '</li>';
            } catch (\Exception $e) {
                $html .= '<li class="error">✗ ' . $routeName . ' → ERROR: ' . $e->getMessage() . '</li>';
            }
        }
        $html .= '</ul>';
        
        // Check if IconClass is available
        $html .= '<h2>IconClass Helper Status:</h2>';
        if (class_exists('\App\Helpers\IconClass')) {
            $html .= '<p class="success">✓ IconClass helper is available</p>';
            
            try {
                $locationIcons = \App\Helpers\IconClass::getLocationIcons();
                $html .= '<p class="success">✓ Location icons loaded: ' . count($locationIcons) . ' icons</p>';
                
                $socialIcons = \App\Helpers\IconClass::getSocialMediaIcons();
                $html .= '<p class="success">✓ Social media icons loaded: ' . count($socialIcons) . ' icons</p>';
                
                $defaultIcon = \App\Helpers\IconClass::getDefaultLocationIcon();
                $html .= '<p class="success">✓ Default location icon: ' . $defaultIcon . '</p>';
                
            } catch (\Exception $e) {
                $html .= '<p class="error">✗ IconClass methods error: ' . $e->getMessage() . '</p>';
            }
        } else {
            $html .= '<p class="error">✗ IconClass helper not found</p>';
        }
        
        // Test social media links
        $html .= '<h2>Social Media Links Test:</h2>';
        try {
            $socialMediaLinks = \App\Models\SocialMediaLink::currentTenant()->take(5)->get();
            if ($socialMediaLinks->count() > 0) {
                $html .= '<p class="success">✓ Found ' . $socialMediaLinks->count() . ' social media links</p>';
                $html .= '<ul>';
                foreach ($socialMediaLinks as $link) {
                    $editUrl = route('admin.social-media.edit', $link->id);
                    $html .= '<li><a href="' . $editUrl . '" target="_blank">' . $link->name . ' (ID: ' . $link->id . ')</a></li>';
                }
                $html .= '</ul>';
            } else {
                $html .= '<p class="info">ℹ No social media links found in current tenant</p>';
            }
        } catch (\Exception $e) {
            $html .= '<p class="error">✗ Social media links error: ' . $e->getMessage() . '</p>';
        }
        
        $html .= '<h2>Quick Links:</h2>';
        $html .= '<ul>';
        $html .= '<li><a href="' . route('admin.social-media.index') . '">Social Media Index</a></li>';
        $html .= '<li><a href="' . route('admin.social-media.create') . '">Create New Social Media Link</a></li>';
        $html .= '<li><a href="/location-icon-examples.html">Location Icon Examples</a></li>';
        $html .= '</ul>';
        
    } catch (\Exception $e) {
        $html .= '<p class="error">General Error: ' . $e->getMessage() . '</p>';
    }
    
    $html .= '</body></html>';
    return $html;
})->middleware(['web', 'auth'])->name('test.social-media-routes');
