<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Helpers\IconClass;

class IconServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('icon', function () {
            return new IconClass();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share icon data with all views
        View::composer('*', function ($view) {
            $view->with([
                'predefinedPlatforms' => IconClass::getPredefinedPlatforms(),
                'allIcons' => IconClass::getAllIcons(),
                'locationIcons' => IconClass::getLocationIcons(),
                'socialIcons' => IconClass::getSocialMediaIcons(),
                'generalIcons' => IconClass::getGeneralIcons(),
            ]);
        });
    }
}
