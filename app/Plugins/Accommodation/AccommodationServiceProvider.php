<?php

namespace App\Plugins\Accommodation;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AccommodationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Config dosyasını kaydet
        $this->mergeConfigFrom(
            __DIR__ . '/config/accommodation.php', 'accommodation'
        );
    }

    public function boot(): void
    {
        // Route'ları kaydet
        $this->registerRoutes();
        
        // View'ları kaydet
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'accommodation');
        
        // Migration'ları kaydet
        if (is_dir(__DIR__ . '/database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        }
        
        // Config dosyasını publish et
        $this->publishes([
            __DIR__ . '/config/accommodation.php' => config_path('accommodation.php'),
        ], 'accommodation-config');
        
        // View'ları publish et
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/accommodation'),
        ], 'accommodation-views');
    }
    
    protected function registerRoutes(): void
    {
        Route::middleware('web')
            ->prefix('admin/accommodation')
            ->group(function () {
                // Accommodation route'ları
            });
            
        // API route'ları
        Route::middleware('api')
            ->prefix('api/accommodation')
            ->group(function () {
                // Accommodation API route'ları
            });
    }
}