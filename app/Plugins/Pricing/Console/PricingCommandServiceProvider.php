<?php

namespace App\Plugins\Pricing\Console;

use App\Plugins\Pricing\Console\Commands\CleanupPricingData;
use Illuminate\Support\ServiceProvider;

class PricingCommandServiceProvider extends ServiceProvider
{
    /**
     * Register the commands.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap the commands.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanupPricingData::class,
            ]);
        }
    }
}