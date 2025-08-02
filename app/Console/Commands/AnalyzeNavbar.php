<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AdaptiveNavbarService;

class AnalyzeNavbar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'navbar:analyze {--company-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze navbar requirements and provide optimization recommendations';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔍 Analyzing Navbar Configuration...');
        $this->newLine();
        
        // Get company data
        $companyId = $this->option('company-id');
        $company = $this->getCompanyData($companyId);
        
        if (!$company) {
            $this->error('No company data found!');
            return 1;
        }
        
        // Analyze navbar requirements
        $analysis = AdaptiveNavbarService::determineNavbarSize($company);
        
        // Display company information
        $this->info('📊 Company Information:');
        $this->table(
            ['Property', 'Value'],
            [
                ['Company Name', $analysis['company_name']],
                ['Name Length', $analysis['name_length'] . ' characters'],
                ['Word Count', $analysis['word_count'] . ' words'],
                ['Has Logo', $analysis['has_logo'] ? '✅ Yes' : '❌ No'],
                ['Special Characters', $analysis['has_special_chars'] ? '⚠️ Yes' : '✅ No'],
                ['Estimated Width', $analysis['estimated_width'] . 'px']
            ]
        );
        
        $this->newLine();
        
        // Display size recommendation
        $sizeClass = $analysis['size_class'];
        $sizeEmoji = [
            'normal' => '🟢',
            'compact' => '🟡',
            'extra-compact' => '🔴'
        ];
        
        $this->info("📏 Recommended Size: {$sizeEmoji[$sizeClass]} " . strtoupper($sizeClass));
        
        // Display reasons
        if (!empty($analysis['reasons'])) {
            $this->newLine();
            $this->warn('📋 Analysis Reasons:');
            foreach ($analysis['reasons'] as $reason) {
                $this->line("  • $reason");
            }
        }
        
        // Display recommendations
        if (!empty($analysis['recommendations'])) {
            $this->newLine();
            $this->info('💡 Recommendations:');
            foreach ($analysis['recommendations'] as $recommendation) {
                $this->line("  • $recommendation");
            }
        }
        
        // Display CSS variables
        $this->newLine();
        $this->info('🎨 CSS Variables for ' . strtoupper($sizeClass) . ' size:');
        $variables = AdaptiveNavbarService::getCSSVariables($sizeClass);
        
        $variableRows = [];
        foreach ($variables as $property => $value) {
            $variableRows[] = [$property, $value];
        }
        
        $this->table(['CSS Variable', 'Value'], $variableRows);
        
        // Performance suggestions
        $this->newLine();
        $this->info('⚡ Performance Suggestions:');
        
        if ($analysis['name_length'] > 30) {
            $this->warn('  • Consider creating a shorter brand name for mobile devices');
        }
        
        if (!$analysis['has_logo']) {
            $this->line('  • Adding a logo will improve brand recognition and reduce text dependency');
        }
        
        if ($sizeClass === 'extra-compact') {
            $this->warn('  • Current setup requires maximum compression - consider optimization');
        } else {
            $this->info('  • Navbar size is well-optimized for current content');
        }
        
        // Generate sample implementation
        $this->newLine();
        if ($this->confirm('Generate sample implementation code?', true)) {
            $this->generateSampleCode($company, $analysis);
        }
        
        $this->newLine();
        $this->info('✅ Analysis complete!');
        
        return 0;
    }
    
    /**
     * Get company data
     */
    private function getCompanyData($companyId = null)
    {
        try {
            if ($companyId) {
                $companyModel = app()->make('App\\Models\\Company');
                return $companyModel::find($companyId);
            }
            
            // Try to get first company
            $companyModel = app()->make('App\\Models\\Company');
            return $companyModel::first();
        } catch (\Exception $e) {
            // Create sample data for testing
            return (object)[
                'id' => 1,
                'company_name' => $this->ask('Enter company name for analysis', 'Sample Company Store'),
                'company_logo' => $this->confirm('Does the company have a logo?') ? 'logo.png' : null,
                'company_email' => 'info@example.com'
            ];
        }
    }
    
    /**
     * Generate sample implementation code
     */
    private function generateSampleCode($company, $analysis)
    {
        $this->newLine();
        $this->info('📝 Sample Implementation:');
        
        // Blade component usage
        $this->line('<comment>Blade Component Usage:</comment>');
        $this->line('```blade');
        $this->line('<x-adaptive-navbar 
    :company="$globalCompany" 
    force-size="' . $analysis['size_class'] . '" />');
        $this->line('```');
        
        $this->newLine();
        
        // CSS implementation
        $this->line('<comment>Direct CSS Implementation:</comment>');
        $this->line('```css');
        $css = AdaptiveNavbarService::generateInlineCSS($company);
        $this->line($css);
        $this->line('```');
        
        $this->newLine();
        
        // JavaScript configuration
        $this->line('<comment>JavaScript Configuration:</comment>');
        $this->line('```javascript');
        $jsConfig = AdaptiveNavbarService::getJSConfig($company);
        $this->line('window.adaptiveNavbarConfig = ' . json_encode($jsConfig, JSON_PRETTY_PRINT) . ';');
        $this->line('```');
    }
}
