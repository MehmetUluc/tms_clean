<?php

namespace App\Plugins\Integration;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class IntegrationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Config dosyasını kaydet
        $this->mergeConfigFrom(
            __DIR__ . '/config/integration.php', 'integration'
        );
    }

    public function boot(): void
    {
        // Route'ları kaydet
        $this->registerRoutes();
        
        // View'ları kaydet
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'integration');
        
        // Migration'ları kaydet
        if (is_dir(__DIR__ . '/database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        }
        
        // Config dosyasını publish et
        $this->publishes([
            __DIR__ . '/config/integration.php' => config_path('integration.php'),
        ], 'integration-config');
        
        // View'ları publish et
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/integration'),
        ], 'integration-views');
    }
    
    protected function registerRoutes(): void
    {
        // Web Route'ları
        Route::middleware('web')
            ->prefix('admin/integration')
            ->group(function () {
                // Integration web route'ları
            });
            
        // API Route'ları
        Route::middleware('api')
            ->prefix('api')
            ->group(function () {
                Route::prefix('v1')->group(base_path('app/Plugins/Integration/routes/api.php'));
            });
    }
}