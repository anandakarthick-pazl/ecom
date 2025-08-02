<?php

namespace App\Services;

class AdaptiveNavbarService
{
    /**
     * Determine the appropriate navbar size based on company data
     * 
     * @param object $company
     * @return array
     */
    public static function determineNavbarSize($company)
    {
        $companyName = $company->company_name ?? 'Your Store';
        $hasLogo = !empty($company->company_logo);
        
        // Analyze company name length
        $nameLength = strlen($companyName);
        $wordCount = str_word_count($companyName);
        
        // Check for special characters or complex formatting
        $hasSpecialChars = preg_match('/[^\w\s]/', $companyName);
        
        // Determine size class
        $sizeClass = 'normal';
        $reasons = [];
        
        if ($nameLength > 25) {
            $sizeClass = 'extra-compact';
            $reasons[] = 'Company name exceeds 25 characters';
        } elseif ($nameLength > 15 || $wordCount > 3) {
            $sizeClass = 'compact';
            $reasons[] = 'Company name is moderately long';
        }
        
        if ($hasSpecialChars) {
            $reasons[] = 'Company name contains special characters';
        }
        
        if (!$hasLogo && $nameLength > 20) {
            $sizeClass = 'extra-compact';
            $reasons[] = 'No logo and long company name';
        }
        
        // Calculate estimated width (rough estimation)
        $estimatedWidth = $nameLength * 12; // Approximate pixels per character
        if ($hasLogo) {
            $estimatedWidth += 50; // Logo width + spacing
        }
        
        return [
            'size_class' => $sizeClass,
            'company_name' => $companyName,
            'name_length' => $nameLength,
            'word_count' => $wordCount,
            'has_logo' => $hasLogo,
            'has_special_chars' => $hasSpecialChars,
            'estimated_width' => $estimatedWidth,
            'reasons' => $reasons,
            'recommendations' => self::getRecommendations($sizeClass, $nameLength, $hasLogo)
        ];
    }
    
    /**
     * Get recommendations for improving navbar appearance
     * 
     * @param string $sizeClass
     * @param int $nameLength
     * @param bool $hasLogo
     * @return array
     */
    private static function getRecommendations($sizeClass, $nameLength, $hasLogo)
    {
        $recommendations = [];
        
        if ($sizeClass === 'extra-compact') {
            $recommendations[] = 'Consider shortening the company name for better display';
            if (!$hasLogo) {
                $recommendations[] = 'Adding a company logo would improve brand recognition';
            }
            $recommendations[] = 'Consider using an abbreviated version of the company name';
        }
        
        if ($sizeClass === 'compact') {
            $recommendations[] = 'Company name length is acceptable but might benefit from a logo';
            if (!$hasLogo) {
                $recommendations[] = 'A company logo would help with brand identity';
            }
        }
        
        if ($nameLength > 30) {
            $recommendations[] = 'Company name is quite long - consider creating a shorter brand name';
        }
        
        return $recommendations;
    }
    
    /**
     * Get CSS variables for the navbar based on size
     * 
     * @param string $sizeClass
     * @return array
     */
    public static function getCSSVariables($sizeClass)
    {
        $variables = [
            'normal' => [
                '--navbar-height' => '80px',
                '--navbar-padding' => '1rem 0',
                '--logo-size' => '40px',
                '--font-size-brand' => '1.5rem',
                '--font-size-nav' => '1rem',
                '--search-width' => '300px'
            ],
            'compact' => [
                '--navbar-height' => '60px',
                '--navbar-padding' => '0.5rem 0',
                '--logo-size' => '30px',
                '--font-size-brand' => '1.2rem',
                '--font-size-nav' => '0.9rem',
                '--search-width' => '250px'
            ],
            'extra-compact' => [
                '--navbar-height' => '50px',
                '--navbar-padding' => '0.25rem 0',
                '--logo-size' => '25px',
                '--font-size-brand' => '1rem',
                '--font-size-nav' => '0.85rem',
                '--search-width' => '200px'
            ]
        ];
        
        return $variables[$sizeClass] ?? $variables['normal'];
    }
    
    /**
     * Generate inline CSS for immediate application
     * 
     * @param object $company
     * @return string
     */
    public static function generateInlineCSS($company)
    {
        $analysis = self::determineNavbarSize($company);
        $variables = self::getCSSVariables($analysis['size_class']);
        
        $css = ':root {';
        foreach ($variables as $property => $value) {
            $css .= "$property: $value; ";
        }
        $css .= '}';
        
        // Add specific overrides if needed
        if ($analysis['size_class'] === 'extra-compact') {
            $css .= '
            .navbar-text {
                max-width: 150px !important;
                font-weight: 600 !important;
            }
            .dropdown-menu-modern {
                max-height: 250px !important;
            }';
        }
        
        return $css;
    }
    
    /**
     * Check if navbar needs to be updated based on company changes
     * 
     * @param object $oldCompany
     * @param object $newCompany
     * @return bool
     */
    public static function needsUpdate($oldCompany, $newCompany)
    {
        $oldAnalysis = self::determineNavbarSize($oldCompany);
        $newAnalysis = self::determineNavbarSize($newCompany);
        
        return $oldAnalysis['size_class'] !== $newAnalysis['size_class'];
    }
    
    /**
     * Get navbar configuration for JavaScript
     * 
     * @param object $company
     * @return array
     */
    public static function getJSConfig($company)
    {
        $analysis = self::determineNavbarSize($company);
        
        return [
            'sizeClass' => $analysis['size_class'],
            'companyName' => $analysis['company_name'],
            'nameLength' => $analysis['name_length'],
            'hasLogo' => $analysis['has_logo'],
            'estimatedWidth' => $analysis['estimated_width'],
            'autoResize' => true,
            'breakpoints' => [
                'extraCompact' => 25,
                'compact' => 15,
                'normal' => 0
            ]
        ];
    }
}
