<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\Models\Category;
use App\Models\AppSetting;
use App\Models\SuperAdmin\Company;
use App\Observers\CompanyObserver;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure pagination views for Bootstrap 5
        Paginator::defaultView('pagination.bootstrap-5');
        Paginator::defaultSimpleView('pagination.simple-default');
        
        // Register observers
        Company::observe(CompanyObserver::class);
        
        // Share categories with all views for navigation
        View::composer('*', function ($view) {
            try {
                $view->with('globalCategories', Category::active()->parent()->orderBy('sort_order')->get());
            } catch (\Exception $e) {
                $view->with('globalCategories', collect());
            }
        });
        
        // Share company settings globally with all views
        View::composer('*', function ($view) {
            try {
                // Get current company from various sources
                $company = null;
                $companyId = null;
                
                // 1. Check session
                if (session('selected_company_id')) {
                    $companyId = session('selected_company_id');
                }
                // 2. Check authenticated user
                elseif (auth()->check() && auth()->user()->company_id) {
                    $companyId = auth()->user()->company_id;
                }
                // 3. Check domain
                elseif (Schema::hasTable('companies')) {
                    $host = request()->getHost();
                    $company = Company::where('domain', $host)->first();
                    if ($company) {
                        $companyId = $company->id;
                    }
                }
                
                // Get company if we have an ID
                if ($companyId && !$company) {
                    $company = Company::find($companyId);
                }
                
                // Get theme settings from app_settings
                $themeSettings = [];
                if (Schema::hasTable('app_settings')) {
                    $themeSettings = [
                        'primary_color' => AppSetting::get('primary_color', '#2d5016'),
                        'secondary_color' => AppSetting::get('secondary_color', '#4a7c28'),
                        'sidebar_color' => AppSetting::get('sidebar_color', '#2d5016'),
                    ];
                }
                
                // Build company settings object
                if ($company) {
                    $companySettings = [
                        'id' => $company->id,
                        'company_name' => $company->name,
                        'company_email' => $company->email,
                        'company_phone' => $company->phone,
                        'company_address' => trim($company->address . ' ' . $company->city . ' ' . $company->state . ' ' . $company->postal_code),
                        'company_logo' => $company->logo,
                        'gst_number' => $company->gst_number,
                        'primary_color' => $themeSettings['primary_color'] ?? '#2d5016',
                        'secondary_color' => $themeSettings['secondary_color'] ?? '#4a7c28',
                        'sidebar_color' => $themeSettings['sidebar_color'] ?? '#2d5016',
                    ];
                } else {
                    // Default values if no company found
                    $companySettings = [
                        'id' => null,
                        'company_name' => 'Herbal Bliss',
                        'company_email' => 'info@herbalbliss.com',
                        'company_phone' => '+91 9876543210',
                        'company_address' => '',
                        'company_logo' => '',
                        'gst_number' => null,
                        'primary_color' => $themeSettings['primary_color'] ?? '#2d5016',
                        'secondary_color' => $themeSettings['secondary_color'] ?? '#4a7c28',
                        'sidebar_color' => $themeSettings['sidebar_color'] ?? '#2d5016',
                    ];
                }
                
                $view->with('globalCompany', (object) $companySettings);
                
                // Share the actual company model too
                $view->with('currentCompany', $company);
                
            } catch (\Exception $e) {
                // Fallback with herbal theme defaults
                $view->with('globalCompany', (object) [
                    'id' => null,
                    'company_name' => 'Herbal Bliss',
                    'company_email' => 'info@herbalbliss.com',
                    'company_phone' => '+91 9876543210',
                    'company_address' => '',
                    'company_logo' => '',
                    'gst_number' => null,
                    'primary_color' => '#2d5016',
                    'secondary_color' => '#4a7c28',
                    'sidebar_color' => '#2d5016',
                ]);
                
                $view->with('currentCompany', null);
            }
        });
    }
}
