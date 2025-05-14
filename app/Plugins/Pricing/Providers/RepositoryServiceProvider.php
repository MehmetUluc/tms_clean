<?php

namespace App\Plugins\Pricing\Providers;

use App\Plugins\Pricing\Repositories\RatePlanRepository;
use App\Plugins\Pricing\Repositories\RatePeriodRepository;
use App\Plugins\Pricing\Repositories\RateExceptionRepository;
use App\Plugins\Pricing\Repositories\BookingPriceRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(RatePlanRepository::class, function ($app) {
            return new RatePlanRepository();
        });

        $this->app->singleton(RatePeriodRepository::class, function ($app) {
            return new RatePeriodRepository();
        });

        $this->app->singleton(RateExceptionRepository::class, function ($app) {
            return new RateExceptionRepository();
        });

        $this->app->singleton(BookingPriceRepository::class, function ($app) {
            return new BookingPriceRepository();
        });
    }
}