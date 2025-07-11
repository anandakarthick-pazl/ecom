<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Services\AnimationService;

class AnimationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with([
            'animationSettings' => AnimationService::getAnimationSettings(),
            'animationsEnabled' => AnimationService::areAnimationsEnabled(),
            'animationClasses' => AnimationService::getAnimationClasses(),
            'animationCSS' => AnimationService::generateAnimationCSS(),
            'animationJS' => AnimationService::generateAnimationJS(),
        ]);
    }
}
