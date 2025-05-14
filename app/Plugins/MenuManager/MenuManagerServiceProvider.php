<?php

namespace App\Plugins\MenuManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;

class MenuManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register config
        $configPath = __DIR__ . '/config/menu-manager.php';
        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'menu-manager');
        } else {
            // Log a warning or handle the error
            \Log::warning("Menu Manager config file not found at: {$configPath}");
        }

        // Register services
        $this->app->singleton('menu-manager.service', function ($app) {
            return new \App\Plugins\MenuManager\Services\MenuManagerService();
        });
    }

    public function boot()
    {
        // Load views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'menu-manager');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Load routes
        $this->mapRoutes();

        // Publish config
        $configPath = __DIR__ . '/config/menu-manager.php';
        if (file_exists($configPath)) {
            $this->publishes([
                $configPath => config_path('menu-manager.php'),
            ], 'menu-manager-config');
            
            // Yayınlama yapıldıktan sonra, config cache'ini temizleyelim
            if ($this->app->runningInConsole()) {
                $this->commands([
                    // Buraya custom komutlar eklenebilir
                ]);
            }
        }

        // Register blade components
        Blade::componentNamespace('App\\Plugins\\MenuManager\\View\\Components', 'menu-manager');
        
        // Register specific components
        Blade::component('menu-manager-mega', \App\Plugins\MenuManager\View\Components\MegaMenu::class);
        
        // Publish assets
        $this->publishes([
            __DIR__ . '/resources/js' => public_path('vendor/menu-manager/js'),
        ], 'menu-manager-assets');
    }

    protected function mapRoutes()
    {
        // Map web routes
        if (file_exists(__DIR__ . '/routes/web.php')) {
            Route::middleware('web')
                ->group(__DIR__ . '/routes/web.php');
        }
        
        // Map API routes
        if (file_exists(__DIR__ . '/routes/api.php')) {
            Route::middleware('api')
                ->prefix('api')
                ->group(__DIR__ . '/routes/api.php');
        }
    }
}