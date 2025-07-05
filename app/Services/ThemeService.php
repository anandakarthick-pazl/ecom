<?php

namespace App\Services;

use App\Models\SuperAdmin\Theme;
use App\Models\SuperAdmin\Company;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class ThemeService
{
    /**
     * Get the active theme for a company
     */
    public function getCompanyTheme(Company $company): ?Theme
    {
        return Cache::remember("company_theme_{$company->id}", 3600, function () use ($company) {
            return $company->theme;
        });
    }

    /**
     * Apply theme to a company
     */
    public function applyTheme(Company $company, Theme $theme): bool
    {
        try {
            $company->update(['theme_id' => $theme->id]);
            
            // Update theme statistics
            $theme->increment('downloads_count');
            
            // Clear cache
            Cache::forget("company_theme_{$company->id}");
            Cache::forget("company_settings_{$company->id}");
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error applying theme: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove theme from a company
     */
    public function removeTheme(Company $company): bool
    {
        try {
            $company->update(['theme_id' => null]);
            
            // Clear cache
            Cache::forget("company_theme_{$company->id}");
            Cache::forget("company_settings_{$company->id}");
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error removing theme: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get theme CSS variables
     */
    public function getThemeVariables(Theme $theme): array
    {
        $variables = [];
        
        if ($theme->color_scheme) {
            foreach ($theme->color_scheme as $key => $color) {
                $variables["--theme-{$key}"] = $color;
            }
        }
        
        return $variables;
    }

    /**
     * Generate theme CSS
     */
    public function generateThemeCSS(Theme $theme): string
    {
        $variables = $this->getThemeVariables($theme);
        
        $css = ":root {\n";
        foreach ($variables as $property => $value) {
            $css .= "    {$property}: {$value};\n";
        }
        $css .= "}\n";
        
        // Add theme-specific styles
        $css .= $this->getThemeSpecificCSS($theme);
        
        return $css;
    }

    /**
     * Get theme-specific CSS
     */
    private function getThemeSpecificCSS(Theme $theme): string
    {
        $css = '';
        
        // Add body class for theme
        $css .= "body.theme-{$theme->slug} {\n";
        
        if (isset($theme->color_scheme['background'])) {
            $css .= "    background-color: {$theme->color_scheme['background']};\n";
        }
        
        if (isset($theme->color_scheme['text'])) {
            $css .= "    color: {$theme->color_scheme['text']};\n";
        }
        
        $css .= "}\n";
        
        // Add component-specific styles
        if ($theme->components) {
            foreach ($theme->components as $component => $config) {
                $css .= $this->generateComponentCSS($theme, $component, $config);
            }
        }
        
        return $css;
    }

    /**
     * Generate component-specific CSS
     */
    private function generateComponentCSS(Theme $theme, string $component, array $config): string
    {
        $css = '';
        
        switch ($component) {
            case 'hero_section':
                $css .= "body.theme-{$theme->slug} .hero-section {\n";
                
                if (isset($config['background'])) {
                    if ($config['background'] === 'gradient' && isset($theme->color_scheme['gradient'])) {
                        $css .= "    background: {$theme->color_scheme['gradient']};\n";
                    }
                }
                
                if (isset($config['text_color'])) {
                    $css .= "    color: {$config['text_color']};\n";
                }
                
                $css .= "}\n";
                break;
                
            case 'product_grid':
                $css .= "body.theme-{$theme->slug} .product-card {\n";
                
                if (isset($config['border_radius'])) {
                    $css .= "    border-radius: {$config['border_radius']};\n";
                }
                
                if (isset($config['hover_effect'])) {
                    $css .= "    transition: transform 0.3s ease;\n";
                }
                
                $css .= "}\n";
                
                if (isset($config['hover_effect'])) {
                    $css .= "body.theme-{$theme->slug} .product-card:hover {\n";
                    
                    switch ($config['hover_effect']) {
                        case 'scale-up':
                            $css .= "    transform: scale(1.05);\n";
                            break;
                        case 'lift':
                            $css .= "    transform: translateY(-5px);\n";
                            break;
                    }
                    
                    $css .= "}\n";
                }
                break;
                
            case 'navbar':
                $css .= "body.theme-{$theme->slug} .navbar {\n";
                
                if (isset($config['background'])) {
                    $css .= "    background: {$config['background']} !important;\n";
                }
                
                if (isset($config['position'])) {
                    $css .= "    position: {$config['position']};\n";
                }
                
                $css .= "}\n";
                break;
        }
        
        return $css;
    }

    /**
     * Get theme customization options
     */
    public function getCustomizationOptions(Theme $theme): array
    {
        return [
            'colors' => [
                'primary' => $theme->color_scheme['primary'] ?? '#007bff',
                'secondary' => $theme->color_scheme['secondary'] ?? '#6c757d',
                'accent' => $theme->color_scheme['accent'] ?? '#28a745',
                'background' => $theme->color_scheme['background'] ?? '#ffffff',
                'text' => $theme->color_scheme['text'] ?? '#212529'
            ],
            'layout' => [
                'header_style' => ['fixed', 'sticky', 'static'],
                'footer_style' => ['minimal', 'detailed', 'contact'],
                'sidebar_position' => ['left', 'right', 'none'],
                'layout_width' => ['full', 'boxed', 'fluid']
            ],
            'typography' => [
                'font_family' => ['Arial', 'Helvetica', 'Times New Roman', 'Georgia'],
                'font_size' => ['small', 'medium', 'large'],
                'line_height' => ['normal', 'relaxed', 'loose']
            ],
            'effects' => [
                'animations' => $theme->components['animations'] ?? [],
                'transitions' => $theme->components['transitions'] ?? [],
                'hover_effects' => $theme->components['hover_effects'] ?? []
            ]
        ];
    }

    /**
     * Apply theme customizations
     */
    public function applyCustomizations(Company $company, array $customizations): bool
    {
        try {
            $settings = $company->settings ?? [];
            $settings['theme_customizations'] = $customizations;
            
            $company->update(['settings' => $settings]);
            
            // Clear cache
            Cache::forget("company_theme_{$company->id}");
            Cache::forget("company_settings_{$company->id}");
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error applying customizations: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get company theme customizations
     */
    public function getCompanyCustomizations(Company $company): array
    {
        return Cache::remember("company_customizations_{$company->id}", 3600, function () use ($company) {
            return $company->settings['theme_customizations'] ?? [];
        });
    }

    /**
     * Generate customized theme CSS
     */
    public function generateCustomizedCSS(Company $company): string
    {
        $theme = $this->getCompanyTheme($company);
        
        if (!$theme) {
            return '';
        }
        
        $customizations = $this->getCompanyCustomizations($company);
        $css = $this->generateThemeCSS($theme);
        
        // Apply customizations
        if (!empty($customizations)) {
            $css .= $this->generateCustomizationCSS($theme, $customizations);
        }
        
        return $css;
    }

    /**
     * Generate customization CSS
     */
    private function generateCustomizationCSS(Theme $theme, array $customizations): string
    {
        $css = '';
        
        // Color customizations
        if (isset($customizations['colors'])) {
            $css .= "body.theme-{$theme->slug} {\n";
            
            foreach ($customizations['colors'] as $property => $value) {
                $css .= "    --theme-{$property}: {$value};\n";
            }
            
            $css .= "}\n";
        }
        
        // Layout customizations
        if (isset($customizations['layout'])) {
            foreach ($customizations['layout'] as $property => $value) {
                $css .= $this->generateLayoutCSS($theme, $property, $value);
            }
        }
        
        // Typography customizations
        if (isset($customizations['typography'])) {
            $css .= "body.theme-{$theme->slug} {\n";
            
            foreach ($customizations['typography'] as $property => $value) {
                switch ($property) {
                    case 'font_family':
                        $css .= "    font-family: {$value}, sans-serif;\n";
                        break;
                    case 'font_size':
                        $multiplier = $value === 'small' ? 0.9 : ($value === 'large' ? 1.1 : 1);
                        $css .= "    font-size: {$multiplier}rem;\n";
                        break;
                    case 'line_height':
                        $height = $value === 'relaxed' ? 1.6 : ($value === 'loose' ? 1.8 : 1.4);
                        $css .= "    line-height: {$height};\n";
                        break;
                }
            }
            
            $css .= "}\n";
        }
        
        return $css;
    }

    /**
     * Generate layout CSS
     */
    private function generateLayoutCSS(Theme $theme, string $property, string $value): string
    {
        $css = '';
        
        switch ($property) {
            case 'header_style':
                $css .= "body.theme-{$theme->slug} .navbar {\n";
                $css .= "    position: {$value};\n";
                if ($value === 'fixed') {
                    $css .= "    top: 0;\n    z-index: 1000;\n";
                }
                $css .= "}\n";
                break;
                
            case 'layout_width':
                $css .= "body.theme-{$theme->slug} .container {\n";
                if ($value === 'boxed') {
                    $css .= "    max-width: 1200px;\n";
                } elseif ($value === 'fluid') {
                    $css .= "    max-width: 100%;\n";
                }
                $css .= "}\n";
                break;
        }
        
        return $css;
    }

    /**
     * Get theme preview data
     */
    public function getThemePreviewData(Theme $theme): array
    {
        return [
            'name' => $theme->name,
            'slug' => $theme->slug,
            'colors' => $theme->color_scheme,
            'components' => $theme->components,
            'features' => $theme->features,
            'css' => $this->generateThemeCSS($theme)
        ];
    }

    /**
     * Get popular themes
     */
    public function getPopularThemes(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Theme::active()
            ->orderBy('downloads_count', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get themes by category
     */
    public function getThemesByCategory(string $category): \Illuminate\Database\Eloquent\Collection
    {
        return Theme::active()
            ->where('category', $category)
            ->orderBy('rating', 'desc')
            ->get();
    }

    /**
     * Search themes
     */
    public function searchThemes(string $query): \Illuminate\Database\Eloquent\Collection
    {
        return Theme::active()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('category', 'like', "%{$query}%")
                  ->orWhereJsonContains('tags', $query)
                  ->orWhereJsonContains('features', $query);
            })
            ->orderBy('rating', 'desc')
            ->get();
    }

    /**
     * Get theme statistics
     */
    public function getThemeStatistics(): array
    {
        return [
            'total_themes' => Theme::count(),
            'active_themes' => Theme::active()->count(),
            'free_themes' => Theme::where('is_free', true)->count(),
            'premium_themes' => Theme::where('is_free', false)->count(),
            'total_downloads' => Theme::sum('downloads_count'),
            'companies_with_themes' => Company::whereNotNull('theme_id')->count(),
            'companies_without_themes' => Company::whereNull('theme_id')->count(),
            'popular_categories' => Theme::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->orderBy('count', 'desc')
                ->take(5)
                ->pluck('count', 'category')
                ->toArray()
        ];
    }

    /**
     * Export theme
     */
    public function exportTheme(Theme $theme): array
    {
        return [
            'name' => $theme->name,
            'slug' => $theme->slug,
            'description' => $theme->description,
            'category' => $theme->category,
            'layout_type' => $theme->layout_type,
            'color_scheme' => $theme->color_scheme,
            'components' => $theme->components,
            'features' => $theme->features,
            'tags' => $theme->tags,
            'settings' => $theme->settings,
            'css' => $this->generateThemeCSS($theme),
            'customization_options' => $this->getCustomizationOptions($theme),
            'version' => '1.0.0',
            'exported_at' => now()->toISOString()
        ];
    }

    /**
     * Import theme
     */
    public function importTheme(array $themeData): ?Theme
    {
        try {
            $theme = Theme::create([
                'name' => $themeData['name'],
                'slug' => $themeData['slug'],
                'description' => $themeData['description'],
                'category' => $themeData['category'],
                'layout_type' => $themeData['layout_type'],
                'color_scheme' => $themeData['color_scheme'],
                'components' => $themeData['components'],
                'features' => $themeData['features'],
                'tags' => $themeData['tags'],
                'settings' => $themeData['settings'] ?? [],
                'is_free' => true,
                'status' => 'active',
                'price' => 0,
                'author' => 'Imported',
                'rating' => 0,
                'downloads_count' => 0
            ]);
            
            return $theme;
        } catch (\Exception $e) {
            Log::error('Error importing theme: ' . $e->getMessage());
            return null;
        }
    }
}
