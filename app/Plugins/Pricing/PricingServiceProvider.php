<?php

namespace App\Plugins\Pricing;

use App\Plugins\Pricing\Filament\Livewire\PricingForm;
use App\Plugins\Pricing\Filament\Pages\HotelPricingPage;
use App\Plugins\Pricing\Providers\RepositoryServiceProvider;
use App\Plugins\Pricing\Console\PricingCommandServiceProvider;
use App\Plugins\Pricing\Services\PricingService;
use App\Plugins\Pricing\Services\PricingPeriodsService;
use App\Plugins\Pricing\Services\DailyRateService;
use App\Plugins\Pricing\Services\DiscountedPriceService;
use App\Plugins\Pricing\Repositories\RatePlanRepository;
use App\Plugins\Pricing\Repositories\RatePeriodRepository;
use App\Plugins\Pricing\Repositories\RateExceptionRepository;
use App\Plugins\Pricing\Repositories\BookingPriceRepository;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class PricingServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register repositories
        $this->app->register(RepositoryServiceProvider::class);

        // Register commands
        $this->app->register(PricingCommandServiceProvider::class);

        // Register services as singletons
        $this->app->singleton(PricingService::class, function ($app) {
            return new PricingService(
                $app->make(RatePlanRepository::class),
                $app->make(RatePeriodRepository::class),
                $app->make(RateExceptionRepository::class),
                $app->make(BookingPriceRepository::class)
            );
        });

        // Register the new PricingPeriodsService
        $this->app->singleton(PricingPeriodsService::class, function ($app) {
            return new PricingPeriodsService();
        });

        // Register the DailyRateService
        $this->app->singleton(DailyRateService::class, function ($app) {
            return new DailyRateService();
        });

        // Register the DiscountedPriceService
        $this->app->singleton(DiscountedPriceService::class, function ($app) {
            return new DiscountedPriceService(
                $app->make(PricingService::class),
                $app->make(\App\Plugins\Discount\Contracts\DiscountServiceInterface::class)
            );
        });
    }

    public function boot()
    {
        // Register routes
        $this->registerRoutes();
        
        // Register assets
        $this->registerAssets();
        
        // Register livewire components
        $this->registerLivewireComponents();
        
        // Register Filament resources and pages
        $this->registerFilamentPages();
        
        // Register views
        $this->loadViewsFrom(resource_path('views/filament'), 'filament');
        $this->loadViewsFrom(resource_path('views/filament/livewire'), 'pricing');
        
        // Register migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
    
    protected function registerRoutes()
    {
        // Web routes (for admin panel)
        Route::middleware('web')
            ->prefix('admin/pricing')
            ->group(function () {
                // Define routes here
            });

        // API routes (for external systems)
        Route::middleware('api')
            ->prefix('api/pricing')
            ->group(function () {
                Route::post('/import', [\App\Plugins\Pricing\Http\Controllers\PriceImportController::class, 'import'])
                    ->name('pricing.import');

                Route::post('/bulk-update', [\App\Plugins\Pricing\Http\Controllers\PriceImportController::class, 'bulkUpdate'])
                    ->name('pricing.bulk-update');
            });
    }
    
    protected function registerAssets(): void
    {
        // No assets needed
    }
    
    protected function registerLivewireComponents(): void
    {
        Livewire::component('pricing::pricing-form', PricingForm::class);
    }
    
    protected function registerFilamentPages(): void
    {
        // Register Hotel Pricing Page to admin panel
        $this->app->resolving('filament', function () {
            // Register the admin page to be shown in the sidebar
            \Filament\Facades\Filament::registerPages([
                HotelPricingPage::class,
            ]);
        });
    }
}