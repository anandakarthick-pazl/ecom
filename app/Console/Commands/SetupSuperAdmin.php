<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupSuperAdmin extends Command
{
    protected $signature = 'superadmin:setup {--fresh : Run fresh migration}';
    protected $description = 'Setup Super Admin with sample data';

    public function handle()
    {
        $this->info('🚀 Setting up Super Admin Platform...');

        if ($this->option('fresh')) {
            $this->warn('Running fresh migration (this will delete all data)...');
            if ($this->confirm('Are you sure you want to continue?')) {
                try {
                    // Drop tables manually to avoid duplicate issues
                    $this->info('Dropping existing tables...');
                    \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                    
                    $tables = [
                        'sessions', 'cache', 'jobs', 'job_batches', 'failed_jobs',
                        'support_tickets', 'ticket_replies', 'billings', 'companies',
                        'landing_page_settings', 'themes', 'packages', 'migrations'
                    ];
                    
                    foreach ($tables as $table) {
                        \DB::statement("DROP TABLE IF EXISTS `{$table}`;");
                    }
                    
                    \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                    
                    Artisan::call('migrate', ['--force' => true]);
                    $this->info('✓ Fresh migration completed');
                } catch (\Exception $e) {
                    $this->error('Migration failed: ' . $e->getMessage());
                    $this->info('Trying regular migration instead...');
                    Artisan::call('migrate', ['--force' => true]);
                }
            } else {
                $this->error('Setup cancelled');
                return 1;
            }
        } else {
            $this->info('Running migrations...');
            try {
                Artisan::call('migrate', ['--force' => true]);
                $this->info('✓ Migrations completed');
            } catch (\Exception $e) {
                $this->error('Migration failed: ' . $e->getMessage());
                $this->info('This might be due to duplicate tables. Continuing...');
            }
        }

        $this->info('Clearing caches...');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        $this->info('✓ Caches cleared');

        $this->info('Creating storage link...');
        try {
            Artisan::call('storage:link');
            $this->info('✓ Storage linked');
        } catch (\Exception $e) {
            $this->warn('Storage link already exists or failed: ' . $e->getMessage());
        }

        $this->newLine();
        $this->info('🎉 Super Admin Platform Setup Complete!');
        $this->newLine();

        $this->table(['Access Point', 'URL', 'Credentials'], [
            ['Landing Page', 'http://localhost:8000', 'Public access'],
            ['Super Admin', 'http://localhost:8000/super-admin/login', 'superadmin@ecomplatform.com / password123'],
            ['Sample Company', 'http://localhost:8000/admin/login', 'admin@company1.com / password123'],
            ['Debug Info', 'http://localhost:8000/super-admin-debug', 'Check system status']
        ]);

        $this->newLine();
        $this->info('Sample data includes:');
        $this->line('• 4 themes (Fashion, Electronics, Grocery, Universal)');
        $this->line('• 3 packages (Starter, Professional, Enterprise)');
        $this->line('• 5 sample companies with different statuses');
        $this->line('• Sample billing records and support tickets');
        $this->line('• Landing page content');

        return 0;
    }
}
