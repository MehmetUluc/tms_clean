<?php

namespace App\Plugins\ThemeManager;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Blade;
use Filament\Support\Facades\FilamentView;
use App\Plugins\ThemeManager\Services\ThemeManagerService;
use Livewire\Livewire;
use App\Plugins\ThemeManager\Http\Livewire\ColorPreview;

class ThemeManagerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register config
        $this->mergeConfigFrom(
            __DIR__ . '/config/theme-manager.php', 'theme-manager'
        );
        
        // Register ThemeManagerService as a singleton
        $this->app->singleton('theme.manager', function ($app) {
            return new ThemeManagerService();
        });
        
        // Register facade accessor
        $this->app->bind('theme', function ($app) {
            return $app['theme.manager'];
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        
        // Load views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'theme-manager');
        
        // Register the correct paths for namespaced views
        view()->addNamespace('theme-manager', __DIR__ . '/resources/views');
        
        // Publish config
        $this->publishes([
            __DIR__ . '/config/theme-manager.php' => config_path('theme-manager.php'),
        ], 'theme-manager-config');
        
        // Publish assets
        $this->publishes([
            __DIR__ . '/resources/css' => public_path('vendor/theme-manager/css'),
            __DIR__ . '/resources/js' => public_path('vendor/theme-manager/js'),
        ], 'theme-manager-assets');
        
        // Register Blade Directives
        Blade::directive('themeSetting', function ($expression) {
            return "<?php echo app('theme.manager')->get({$expression}) ?? ''; ?>";
        });
        
        // Register View Components
        Blade::componentNamespace('App\\Plugins\\ThemeManager\\View\\Components', 'theme');
        
        // Register Livewire Components for Livewire 3
        Livewire::component('color-preview', ColorPreview::class);
        
        // Add Filament customizations
        $this->configureFilament();
    }
    
    /**
     * Configure Filament related settings
     */
    protected function configureFilament(): void
    {
        // Register assets for admin panel
        FilamentAsset::register([
            Css::make('theme-manager-styles', __DIR__ . '/resources/css/theme-manager.css'),
            Js::make('theme-manager-scripts', __DIR__ . '/resources/js/theme-manager.js'),
        ]);

        // Register views for admin panel - Ancak şu an için devre dışı bıraktık
        /*
        FilamentView::registerRenderHook(
            'panels::head.end',
            fn (): string => Blade::render("@themeSetting('custom_admin_head_code') ?? ''")
        );
        */
    }
}