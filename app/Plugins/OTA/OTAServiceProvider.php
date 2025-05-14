<?php

namespace App\Plugins\OTA;

use App\Plugins\OTA\Services\DataMappingService;
use App\Plugins\OTA\Services\TemplateEngine;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
use Filament\Facades\Filament;

class OTAServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/ota.php', 'ota'
        );
        
        // Register service bindings
        $this->app->singleton(DataMappingService::class, function ($app) {
            return new DataMappingService();
        });
        
        $this->app->bind(TemplateEngine::class, function ($app) {
            return new TemplateEngine();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'ota');
        
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        
        // Routes
        $this->registerRoutes();
        
        // Register view components
        Blade::componentNamespace('App\\Plugins\\OTA\\View\\Components', 'ota');
        
        // Publishing config
        $this->publishes([
            __DIR__.'/config/ota.php' => config_path('ota.php'),
        ], 'ota-config');
        
        // Publishing views
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/ota'),
        ], 'ota-views');
    }
    
    /**
     * Register the package routes.
     */
    protected function registerRoutes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware('web')
            ->group(__DIR__ . '/routes/web.php');
            
        Route::middleware('api')
            ->prefix('api/v1')
            ->group(__DIR__ . '/routes/api.php');
    }
}