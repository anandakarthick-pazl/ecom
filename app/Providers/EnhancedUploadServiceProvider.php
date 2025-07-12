<?php

namespace App\Services;

use Illuminate\Support\ServiceProvider;
use App\Services\EnhancedFileUploadService;

class EnhancedUploadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EnhancedFileUploadService::class, function ($app) {
            return new EnhancedFileUploadService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
