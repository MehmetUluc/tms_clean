<?php

namespace App\Plugins\API;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ApiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Config dosyasını kaydet
        if (file_exists(__DIR__ . '/config/api.php')) {
            $this->mergeConfigFrom(
                __DIR__ . '/config/api.php', 'api'
            );
        }
    }

    public function boot(): void
    {
        // Route'ları kaydet
        $this->registerRoutes();
        
        // View'ları kaydet
        if (is_dir(__DIR__ . '/resources/views')) {
            $this->loadViewsFrom(__DIR__ . '/resources/views', 'api');
        }
        
        // Migration'ları kaydet
        if (is_dir(__DIR__ . '/database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        }
        
        // Config dosyasını publish et
        if (file_exists(__DIR__ . '/config/api.php')) {
            $this->publishes([
                __DIR__ . '/config/api.php' => config_path('api.php'),
            ], 'api-config');
        }
        
        // View'ları publish et
        if (is_dir(__DIR__ . '/resources/views')) {
            $this->publishes([
                __DIR__ . '/resources/views' => resource_path('views/vendor/api'),
            ], 'api-views');
        }
    }
    
    protected function registerRoutes(): void
    {
        // API route klasörünü kontrol et
        if (is_dir(__DIR__ . '/routes')) {
            // API Route'ları
            Route::middleware('api')
                ->prefix('api')
                ->group(function () {
                    if (file_exists(__DIR__ . '/routes/api.php')) {
                        Route::prefix('v1')->group(base_path('app/Plugins/API/routes/api.php'));
                    }
                });
        }
    }
}