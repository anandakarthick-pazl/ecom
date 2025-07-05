<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuperAdmin\Company;
use App\Services\TenantHelper;

class TenantManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:manage 
                            {action : The action to perform (list|create|delete|migrate|seed)}
                            {--company= : Company ID for specific operations}
                            {--name= : Company name for creation}
                            {--domain= : Company domain for creation}
                            {--force : Force action without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Manage tenant companies and their data';

    /**
     * Tenant helper service
     */
    protected $tenantHelper;

    /**
     * Create a new command instance.
     */
    public function __construct(TenantHelper $tenantHelper)
    {
        parent::__construct();
        $this->tenantHelper = $tenantHelper;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list':
                $this->listTenants();
                break;
            case 'create':
                $this->createTenant();
                break;
            case 'delete':
                $this->deleteTenant();
                break;
            case 'migrate':
                $this->migrateTenant();
                break;
            case 'seed':
                $this->seedTenant();
                break;
            default:
                $this->error("Unknown action: {$action}");
                $this->info("Available actions: list, create, delete, migrate, seed");
                return 1;
        }

        return 0;
    }

    /**
     * List all tenants
     */
    protected function listTenants()
    {
        $companies = Company::orderBy('name')->get();

        if ($companies->isEmpty()) {
            $this->info('No tenants found.');
            return;
        }

        $this->info('Tenants:');
        $this->table(
            ['ID', 'Name', 'Domain', 'Status', 'Created At'],
            $companies->map(function ($company) {
                return [
                    $company->id,
                    $company->name,
                    $company->domain ?: 'N/A',
                    $company->status,
                    $company->created_at->format('Y-m-d H:i:s')
                ];
            })
        );
    }

    /**
     * Create a new tenant
     */
    protected function createTenant()
    {
        $name = $this->option('name') ?: $this->ask('Company name');
        $domain = $this->option('domain') ?: $this->ask('Company domain (optional)', null);

        if (!$name) {
            $this->error('Company name is required');
            return 1;
        }

        // Check if company name already exists
        if (Company::where('name', $name)->exists()) {
            $this->error('Company with this name already exists');
            return 1;
        }

        // Check if domain already exists
        if ($domain && Company::where('domain', $domain)->exists()) {
            $this->error('Company with this domain already exists');
            return 1;
        }

        $company = Company::create([
            'name' => $name,
            'domain' => $domain,
            'status' => 'active',
            'trial_ends_at' => now()->addDays(30), // 30-day trial
        ]);

        $this->info("Tenant created successfully!");
        $this->info("ID: {$company->id}");
        $this->info("Name: {$company->name}");
        $this->info("Domain: " . ($company->domain ?: 'N/A'));
        $this->info("Status: {$company->status}");

        if ($this->confirm('Would you like to seed this tenant with sample data?')) {
            $this->seedTenantData($company);
        }
    }

    /**
     * Delete a tenant
     */
    protected function deleteTenant()
    {
        $companyId = $this->option('company') ?: $this->ask('Company ID to delete');

        if (!$companyId) {
            $this->error('Company ID is required');
            return 1;
        }

        $company = Company::find($companyId);

        if (!$company) {
            $this->error('Company not found');
            return 1;
        }

        $this->warn("This will delete tenant: {$company->name} (ID: {$company->id})");
        $this->warn("All tenant data will be permanently deleted!");

        if (!$this->option('force') && !$this->confirm('Are you sure you want to continue?')) {
            $this->info('Operation cancelled');
            return 0;
        }

        // Delete tenant data
        $this->deleteTenantData($company);

        // Delete the company
        $company->delete();

        $this->info('Tenant deleted successfully!');
    }

    /**
     * Run migrations for a specific tenant
     */
    protected function migrateTenant()
    {
        $companyId = $this->option('company') ?: $this->ask('Company ID (leave empty for all)');

        if ($companyId) {
            $company = Company::find($companyId);
            if (!$company) {
                $this->error('Company not found');
                return 1;
            }

            $this->info("Running migrations for tenant: {$company->name}");
            $this->migrateTenantData($company);
        } else {
            $this->info('Running migrations for all tenants');
            Company::chunk(10, function ($companies) {
                foreach ($companies as $company) {
                    $this->info("Migrating tenant: {$company->name}");
                    $this->migrateTenantData($company);
                }
            });
        }

        $this->info('Migrations completed successfully!');
    }

    /**
     * Seed a specific tenant
     */
    protected function seedTenant()
    {
        $companyId = $this->option('company') ?: $this->ask('Company ID');

        if (!$companyId) {
            $this->error('Company ID is required');
            return 1;
        }

        $company = Company::find($companyId);

        if (!$company) {
            $this->error('Company not found');
            return 1;
        }

        $this->info("Seeding tenant: {$company->name}");
        $this->seedTenantData($company);
        $this->info('Seeding completed successfully!');
    }

    /**
     * Delete all data for a tenant
     */
    protected function deleteTenantData(Company $company)
    {
        $this->tenantHelper->withTenant($company, function () {
            $models = [
                \App\Models\OrderItem::class,
                \App\Models\Order::class,
                \App\Models\Cart::class,
                \App\Models\Product::class,
                \App\Models\Category::class,
                \App\Models\Customer::class,
                \App\Models\Banner::class,
                \App\Models\Offer::class,
                \App\Models\Supplier::class,
                \App\Models\AppSetting::class,
                \App\Models\Notification::class,
            ];

            foreach ($models as $model) {
                if (class_exists($model)) {
                    $count = $model::count();
                    $model::truncate();
                    $this->info("Deleted {$count} records from " . class_basename($model));
                }
            }
        });
    }

    /**
     * Run tenant-specific migrations
     */
    protected function migrateTenantData(Company $company)
    {
        $this->tenantHelper->withTenant($company, function () use ($company) {
            // Run any tenant-specific setup
            $this->info("Tenant context set for: {$company->name}");
            
            // You can add tenant-specific migration logic here
            // For example, creating default categories, settings, etc.
        });
    }

    /**
     * Seed sample data for a tenant
     */
    protected function seedTenantData(Company $company)
    {
        $this->tenantHelper->withTenant($company, function () use ($company) {
            $this->info("Creating sample data for: {$company->name}");

            // Create default categories
            $categories = [
                ['name' => 'Electronics', 'is_active' => true, 'sort_order' => 1],
                ['name' => 'Clothing', 'is_active' => true, 'sort_order' => 2],
                ['name' => 'Books', 'is_active' => true, 'sort_order' => 3],
                ['name' => 'Home & Garden', 'is_active' => true, 'sort_order' => 4],
            ];

            foreach ($categories as $categoryData) {
                \App\Models\Category::create($categoryData);
            }

            // Create default app settings
            $settings = [
                ['key' => 'site_name', 'value' => $company->name, 'type' => 'string', 'group' => 'general'],
                ['key' => 'currency', 'value' => 'INR', 'type' => 'string', 'group' => 'general'],
                ['key' => 'timezone', 'value' => 'Asia/Kolkata', 'type' => 'string', 'group' => 'general'],
                ['key' => 'products_per_page', 'value' => '20', 'type' => 'integer', 'group' => 'display'],
            ];

            foreach ($settings as $settingData) {
                \App\Models\AppSetting::create($settingData);
            }

            $this->info("Sample data created successfully!");
        });
    }
}
