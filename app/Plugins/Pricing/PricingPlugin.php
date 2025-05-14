<?php

namespace App\Plugins\Pricing;

use Filament\Contracts\Plugin;
use Filament\Panel;

class PricingPlugin implements Plugin
{
    public static function make(): static
    {
        return new static();
    }
    
    public function getId(): string
    {
        return 'pricing';
    }

    public function register(Panel $panel): void
    {
        // Register plugin pages
        $panel
            ->pages([
                \App\Plugins\Pricing\Filament\Pages\HotelPricingPage::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // Boot the plugin
    }
}