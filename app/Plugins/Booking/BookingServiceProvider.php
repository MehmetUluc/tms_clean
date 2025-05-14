<?php

namespace App\Plugins\Booking;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class BookingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Config dosyasını kaydet
        $this->mergeConfigFrom(
            __DIR__ . '/config/booking.php', 'booking'
        );
    }

    public function boot(): void
    {
        // Route'ları kaydet
        $this->registerRoutes();
        
        // View'ları kaydet
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'booking');
        
        // Migration'ları kaydet
        if (is_dir(__DIR__ . '/database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        }
        
        // Config dosyasını publish et
        $this->publishes([
            __DIR__ . '/config/booking.php' => config_path('booking.php'),
        ], 'booking-config');
        
        // View'ları publish et
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/booking'),
        ], 'booking-views');
    }
    
    protected function registerRoutes(): void
    {
        Route::middleware('web')
            ->prefix('admin/booking')
            ->group(function () {
                // Booking route'ları
            });
            
        // API route'ları
        Route::middleware('api')
            ->prefix('api/booking')
            ->group(function () {
                // Booking API route'ları
            });
    }
}