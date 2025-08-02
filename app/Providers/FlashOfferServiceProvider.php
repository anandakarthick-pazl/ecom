<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Offer;

class FlashOfferServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share active flash offer with all views
        View::composer('*', function ($view) {
            try {
                $activeFlashOffer = Offer::where('is_flash_offer', true)
                    ->where('is_active', true)
                    ->where('show_popup', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();
                
                $view->with('activeFlashOffer', $activeFlashOffer);
            } catch (\Exception $e) {
                // If there's an error (like database not ready), just pass null
                $view->with('activeFlashOffer', null);
            }
        });
    }
}
