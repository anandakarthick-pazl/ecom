<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Load auth routes first for tenant domains
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));
            Route::middleware('web')
                ->group(base_path('routes/landing.php'));
            Route::middleware('web')
                ->group(base_path('routes/auth-test.php'));
            Route::middleware('web')
                ->group(base_path('routes/debug-auth.php'));
            Route::middleware('web')
                ->group(base_path('routes/debug-companies.php'));
            Route::middleware('web')
                ->group(base_path('routes/debug-tenant.php'));
            Route::middleware('web')
                ->group(base_path('routes/super_admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'super.admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'tenant' => \App\Http\Middleware\TenantMiddleware::class,
            'company.admin' => \App\Http\Middleware\CompanyAdminMiddleware::class,
            'company.context' => \App\Http\Middleware\EnsureCompanyContext::class,
            'main.domain' => \App\Http\Middleware\MainDomainMiddleware::class,
            'enhanced.session' => \App\Http\Middleware\EnhancedSessionManagement::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'permission.any' => \App\Http\Middleware\CheckAnyPermission::class,
            'permission.all' => \App\Http\Middleware\CheckAllPermissions::class,
        ]);
        
        // Add enhanced session management to web middleware group
        $middleware->web(append: [
            \App\Http\Middleware\EnhancedSessionManagement::class,
        ]);
        
        // Override default authenticate middleware
        $middleware->replace(\Illuminate\Auth\Middleware\Authenticate::class, \App\Http\Middleware\Authenticate::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
