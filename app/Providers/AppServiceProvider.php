<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Http\Livewire\Home\SearchBox;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ThemeManager servis sağlayıcısını kaydet
        $this->app->register(\App\Plugins\ThemeManager\ThemeManagerServiceProvider::class);
        
        // Register SSR related config
        config([
            'app.ssr_enabled' => env('APP_SSR_ENABLED', false),
            'app.ssr_port' => env('APP_SSR_PORT', 13714),
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // \URL::forceScheme('https');

        // Force Vite to use production assets
        if (!app()->environment('local')) {
            \Illuminate\Support\Facades\URL::forceRootUrl(config('app.url'));
        }

        // Create a debug log file for authentication
        \Illuminate\Support\Facades\Log::channel('single')->info('AppServiceProvider booted');
        
        // Add listener for authentication attempts
        \Illuminate\Support\Facades\Event::listen('Illuminate\Auth\Events\Attempting', function ($event) {
            \Illuminate\Support\Facades\Log::channel('single')->info('Auth attempting', [
                'email' => $event->credentials['email'] ?? 'unknown',
                'remember' => $event->remember ?? false,
            ]);
        });
        
        // Add listener for login success
        \Illuminate\Support\Facades\Event::listen('Illuminate\Auth\Events\Login', function ($event) {
            \Illuminate\Support\Facades\Log::channel('single')->info('Auth success', [
                'id' => $event->user->id ?? 'unknown',
                'email' => $event->user->email ?? 'unknown',
                'roles' => $event->user->getRoleNames() ?? [],
            ]);
        });
        
        // Add listener for login failure
        \Illuminate\Support\Facades\Event::listen('Illuminate\Auth\Events\Failed', function ($event) {
            \Illuminate\Support\Facades\Log::channel('single')->info('Auth failed', [
                'email' => $event->credentials['email'] ?? 'unknown',
            ]);
        });

        // Register original Livewire components
        Livewire::component('home.search-box', SearchBox::class);

        // Register B2C Livewire Components
        $this->registerB2CLivewireComponents();

        // PricingV2 plugin removed for redesign
    }

    /**
     * Register B2C Livewire components
     */
    private function registerB2CLivewireComponents(): void
    {
        // Home Components
        Livewire::component('b2c.home.search-hero', \App\Http\Livewire\B2C\Home\SearchHero::class);

        // Region Components
        Livewire::component('b2c.regions.featured-regions', \App\Http\Livewire\B2C\Regions\FeaturedRegions::class);

        // Hotel Components
        Livewire::component('b2c.hotels.popular-hotels', \App\Http\Livewire\B2C\Hotels\PopularHotels::class);
    }
}