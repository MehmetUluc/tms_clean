<?php

namespace App\Plugins\Amenities;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AmenitiesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Config dosyasını kaydet
        $this->mergeConfigFrom(
            __DIR__ . '/config/amenities.php', 'amenities'
        );
    }

    public function boot(): void
    {
        // Route'ları kaydet
        $this->registerRoutes();
        
        // View'ları kaydet
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'amenities');
        
        // Migration'ları kaydet
        if (is_dir(__DIR__ . '/database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        }
        
        // Config dosyasını publish et
        $this->publishes([
            __DIR__ . '/config/amenities.php' => config_path('amenities.php'),
        ], 'amenities-config');
        
        // View'ları publish et
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/amenities'),
        ], 'amenities-views');
    }
    
    protected function registerRoutes(): void
    {
        Route::middleware('web')
            ->prefix('admin/amenities')
            ->group(function () {
                // Amenities route'ları
            });
            
        // API route'ları
        Route::middleware('api')
            ->prefix('api/amenities')
            ->group(function () {
                // Amenities API route'ları
            });
    }
}