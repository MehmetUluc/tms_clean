<?php

namespace App\Plugins\Hotel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class HotelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Route'ları kaydet
        $this->registerRoutes();
        
        // View'ları kaydet
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'hotel');
        
        // Migration'ları kaydet
        if (is_dir(__DIR__ . '/database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        }
    }
    
    protected function registerRoutes(): void
    {
        Route::middleware('web')
            ->prefix('admin/hotel')
            ->group(function () {
                // Hotel route'ları
            });
    }
}