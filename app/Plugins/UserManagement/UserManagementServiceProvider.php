<?php

namespace App\Plugins\UserManagement;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class UserManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Config dosyasını kaydet
        $this->mergeConfigFrom(
            __DIR__ . '/config/user-management.php', 'user-management'
        );
    }

    public function boot(): void
    {
        // Route'ları kaydet
        $this->registerRoutes();
        
        // View'ları kaydet
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'user-management');
        
        // Migration'ları kaydet
        if (is_dir(__DIR__ . '/database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        }
        
        // Config dosyasını publish et
        $this->publishes([
            __DIR__ . '/config/user-management.php' => config_path('user-management.php'),
        ], 'user-management-config');
        
        // View'ları publish et
        $this->publishes([
            __DIR__ . '/resources/views' => resource_path('views/vendor/user-management'),
        ], 'user-management-views');
    }
    
    protected function registerRoutes(): void
    {
        Route::middleware('web')
            ->prefix('admin/users')
            ->group(function () {
                // UserManagement route'ları
            });
    }
}