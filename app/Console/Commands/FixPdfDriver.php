<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixPdfDriver extends Command
{
    protected $signature = 'pdf:fix';
    protected $description = 'Fix PDF driver to use DomPDF instead of Snappy';

    public function handle()
    {
        $this->info('Checking PDF configuration...');
        
        // Check if facades are loaded
        if (class_exists('\PDF')) {
            $pdfClass = get_class(app('pdf'));
            $this->info('Current PDF driver class: ' . $pdfClass);
            
            if (str_contains($pdfClass, 'Snappy')) {
                $this->error('❌ Snappy is currently being used!');
                $this->info('Attempting to fix...');
                
                // Clear the binding
                app()->forgetInstance('pdf');
                
                // Re-bind with DomPDF
                app()->singleton('pdf', function ($app) {
                    return new \Barryvdh\DomPDF\PDF($app);
                });
                
                $this->info('✅ Re-bound to DomPDF');
            } else {
                $this->info('✅ DomPDF is already being used');
            }
        }
        
        // Check composer packages
        $composerJson = json_decode(file_get_contents(base_path('composer.json')), true);
        
        if (isset($composerJson['require']['barryvdh/laravel-snappy'])) {
            $this->warn('⚠️ laravel-snappy is installed. Consider removing it:');
            $this->line('composer remove barryvdh/laravel-snappy');
        }
        
        if (isset($composerJson['require']['barryvdh/laravel-dompdf'])) {
            $this->info('✅ laravel-dompdf is installed');
        } else {
            $this->error('❌ laravel-dompdf is NOT installed');
            $this->line('Run: composer require barryvdh/laravel-dompdf');
        }
        
        // Clear all caches
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->call('view:clear');
        
        $this->info('Configuration cleared. Please test PDF generation now.');
        
        return 0;
    }
}
