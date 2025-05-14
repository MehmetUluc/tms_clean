<?php

namespace App\Plugins\Core;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Eloquent\Builder;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Config dosyasını kaydet
        $this->mergeConfigFrom(
            __DIR__ . '/config/core.php', 'core'
        );
        
        // Plugin yükleme işlemleri için servisler
        $this->app->singleton('core.plugin-loader', function ($app) {
            return new Services\PluginLoader();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Route'ları kaydet
        $this->registerRoutes();
        
        // Özel viewlar kullanılmıyor
        
        // Migration'ları kaydet
        if (is_dir(__DIR__ . '/database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        }
        
        // Config dosyasını publish et
        $this->publishes([
            __DIR__ . '/config/core.php' => config_path('core.php'),
        ], 'core-config');
        
        // Özel viewlar publish edilmiyor
        
        // Global sorgu filtreleri ve makroları kaydet
        $this->registerMacros();
    }
    
    /**
     * Route'ları kaydet
     */
    protected function registerRoutes(): void
    {
        Route::middleware('web')
            ->prefix('admin/core')
            ->group(function () {
                // Core route'ları
            });
            
        // API route'ları
        Route::middleware('api')
            ->prefix('api/core')
            ->group(function () {
                // Core API route'ları
            });
    }
    
    /**
     * Global makroları kaydet
     */
    protected function registerMacros(): void
    {
        // Tenant (Acente) filtresi ekle
        Builder::macro('forTenant', function ($tenantId = null) {
            return $this->where('tenant_id', $tenantId ?: auth()->user()?->tenant_id);
        });
        
        // Aktif kayıtlar filtresi ekle
        Builder::macro('active', function () {
            return $this->where('is_active', true);
        });
    }
}