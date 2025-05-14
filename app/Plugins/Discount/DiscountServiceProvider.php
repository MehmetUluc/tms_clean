<?php

namespace App\Plugins\Discount;

use App\Plugins\Discount\Contracts\DiscountServiceInterface;
use App\Plugins\Discount\Services\DiscountCalculatorFactory;
use App\Plugins\Discount\Services\DiscountService;
use App\Plugins\Discount\Services\Calculators\PercentageDiscountCalculator;
use App\Plugins\Discount\Services\Calculators\FixedAmountDiscountCalculator;
use App\Plugins\Discount\Services\Calculators\FreeNightsDiscountCalculator;
use App\Plugins\Discount\Services\Calculators\NthNightFreeDiscountCalculator;
use App\Plugins\Discount\Services\Calculators\EarlyBookingDiscountCalculator;
use App\Plugins\Discount\Services\Calculators\LastMinuteDiscountCalculator;
use App\Plugins\Discount\Services\Calculators\LongStayDiscountCalculator;
use App\Plugins\Discount\Services\Calculators\PackageDealDiscountCalculator;
use App\Plugins\Discount\Filament\Pages\PresetDiscounts;
use App\Plugins\Discount\Filament\Resources\DiscountResource;
use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class DiscountServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register Calculator classes
        $this->app->bind(PercentageDiscountCalculator::class);
        $this->app->bind(FixedAmountDiscountCalculator::class);
        $this->app->bind(FreeNightsDiscountCalculator::class);
        $this->app->bind(NthNightFreeDiscountCalculator::class);
        $this->app->bind(EarlyBookingDiscountCalculator::class);
        $this->app->bind(LastMinuteDiscountCalculator::class);
        $this->app->bind(LongStayDiscountCalculator::class);
        $this->app->bind(PackageDealDiscountCalculator::class);

        // Register Calculator Factory
        $this->app->singleton(DiscountCalculatorFactory::class);

        // Register main DiscountService
        $this->app->singleton(DiscountServiceInterface::class, function ($app) {
            return new DiscountService(
                $app->make(DiscountCalculatorFactory::class)
            );
        });

        $this->app->alias(DiscountServiceInterface::class, DiscountService::class);

        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/config/discount.php', 'discount'
        );
    }

    public function boot()
    {
        // Register routes if needed
        $this->registerRoutes();

        // Register assets
        $this->registerAssets();

        // Register livewire components
        $this->registerLivewireComponents();

        // Register views
        $this->loadViewsFrom(resource_path('views'), 'filament');

        // Register migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Publish config
        $this->publishes([
            __DIR__ . '/config/discount.php' => config_path('discount.php'),
        ], 'discount-config');
    }

    protected function registerRoutes()
    {
        // Web routes for admin panel
        /* - Skip for now as we'll use Filament interface
        Route::middleware('web')
            ->prefix('admin/discounts')
            ->group(function () {
                // Define routes here
            });
        */

        // Load API routes
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
    }

    protected function registerAssets(): void
    {
        // Register CSS or JS assets if needed
        /*
        FilamentAsset::register([
            Css::make('discount-styles', __DIR__ . '/../resources/css/discount.css'),
            Js::make('discount-scripts', __DIR__ . '/../resources/js/discount.js'),
        ]);
        */
    }

    protected function registerLivewireComponents(): void
    {
        // Register any Livewire components used in the discount plugin
        // Livewire::component('discount::discount-form', DiscountForm::class);
    }
}