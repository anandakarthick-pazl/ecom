<?php

/*
|--------------------------------------------------------------------------
| Social Media Routes - Direct Registration
|--------------------------------------------------------------------------
| This file provides direct route registration for social media functionality
| Use this if the main web.php routes are not working due to cache issues
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SocialMediaController;

// Social Media Routes with direct registration
Route::middleware(['web', 'auth', 'company.context'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // Main resource routes
        Route::get('social-media', [SocialMediaController::class, 'index'])
            ->name('social-media.index');
            
        Route::get('social-media/create', [SocialMediaController::class, 'create'])
            ->name('social-media.create');
            
        Route::post('social-media', [SocialMediaController::class, 'store'])
            ->name('social-media.store');
            
        Route::get('social-media/{social_medium}', [SocialMediaController::class, 'show'])
            ->name('social-media.show');
            
        Route::get('social-media/{social_medium}/edit', [SocialMediaController::class, 'edit'])
            ->name('social-media.edit');
            
        Route::put('social-media/{social_medium}', [SocialMediaController::class, 'update'])
            ->name('social-media.update');
            
        Route::patch('social-media/{social_medium}', [SocialMediaController::class, 'update'])
            ->name('social-media.patch');
            
        Route::delete('social-media/{social_medium}', [SocialMediaController::class, 'destroy'])
            ->name('social-media.destroy');
        
        // Additional helper routes
        Route::patch('social-media/{social_medium}/toggle-status', [SocialMediaController::class, 'toggleStatus'])
            ->name('social-media.toggle-status');
            
        Route::post('social-media/update-sort-order', [SocialMediaController::class, 'updateSortOrder'])
            ->name('social-media.update-sort-order');
            
        Route::post('social-media/quick-add', [SocialMediaController::class, 'quickAdd'])
            ->name('social-media.quick-add');
    });

// API route for frontend (social media links display)
Route::middleware(['web', 'tenant'])
    ->get('/api/social-media-links', [SocialMediaController::class, 'getActiveLinks'])
    ->name('api.social-media-links');
