<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use App\Services\ThemeService;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ThemeService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Blade directives for theme functionality
        $this->registerBladeDirectives();
        
        // Register view composers
        $this->registerViewComposers();
    }

    /**
     * Register Blade directives
     */
    private function registerBladeDirectives(): void
    {
        // Directive to render theme styles
        Blade::directive('themeStyles', function ($expression) {
            return "<?php echo app(App\Services\ThemeService::class)->generateCustomizedCSS(app('currentCompany')); ?>";
        });

        // Directive to render theme variables
        Blade::directive('themeVariables', function ($expression) {
            return "<?php 
                \$theme = app('currentTheme') ?? null;
                if (\$theme) {
                    \$variables = app(App\Services\ThemeService::class)->getThemeVariables(\$theme);
                    echo '<style>:root {';
                    foreach (\$variables as \$property => \$value) {
                        echo \$property . ': ' . \$value . ';';
                    }
                    echo '}</style>';
                }
            ?>";
        });

        // Directive to render theme class
        Blade::directive('themeClass', function ($expression) {
            return "<?php echo app('themeClass') ?? ''; ?>";
        });

        // Directive to check if theme is active
        Blade::directive('hasTheme', function ($expression) {
            return "<?php if (app('currentTheme') ?? null): ?>";
        });

        Blade::directive('endhasTheme', function ($expression) {
            return "<?php endif; ?>";
        });

        // Directive to render theme-specific content
        Blade::directive('themeContent', function ($expression) {
            return "<?php 
                \$theme = app('currentTheme') ?? null;
                if (\$theme && \$theme->slug === {$expression}): 
            ?>";
        });

        Blade::directive('endthemeContent', function ($expression) {
            return "<?php endif; ?>";
        });

        // Directive to render color scheme
        Blade::directive('themeColor', function ($expression) {
            return "<?php 
                \$theme = app('currentTheme') ?? null;
                if (\$theme && isset(\$theme->color_scheme[{$expression}])) {
                    echo \$theme->color_scheme[{$expression}];
                }
            ?>";
        });

        // Directive to render theme component
        Blade::directive('themeComponent', function ($expression) {
            return "<?php 
                \$theme = app('currentTheme') ?? null;
                if (\$theme && isset(\$theme->components[{$expression}])) {
                    echo json_encode(\$theme->components[{$expression}]);
                }
            ?>";
        });
    }

    /**
     * Register view composers
     */
    private function registerViewComposers(): void
    {
        // Share theme data with all views
        View::composer('*', function ($view) {
            $themeService = app(ThemeService::class);
            
            // Get current company from request
            $company = request()->attributes->get('company');
            
            if ($company) {
                $theme = $themeService->getCompanyTheme($company);
                
                if ($theme) {
                    $view->with([
                        'currentTheme' => $theme,
                        'themeService' => $themeService,
                        'themeClass' => 'theme-' . $theme->slug,
                        'themeVariables' => $themeService->getThemeVariables($theme),
                        'themeCustomizations' => $themeService->getCompanyCustomizations($company)
                    ]);
                }
            }
        });
    }
}
