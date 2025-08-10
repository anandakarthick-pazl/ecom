<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Offer;

class FlashOfferComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // Get active flash offers for popup display
        $flashOffer = Offer::activeFlashOffers()
            ->where('show_popup', true)
            ->orderBy('created_at', 'desc')
            ->first();
        
        $view->with('flashOffer', $flashOffer);
    }
}
