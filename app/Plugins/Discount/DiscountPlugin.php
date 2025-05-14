<?php

namespace App\Plugins\Discount;

use App\Plugins\Discount\Filament\Pages\PresetDiscounts;
use App\Plugins\Discount\Filament\Resources\DiscountResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class DiscountPlugin implements Plugin
{
    public static function make(): static
    {
        return new static();
    }

    public function getId(): string
    {
        return 'discount';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                DiscountResource::class,
            ])
            ->pages([
                PresetDiscounts::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // Boot the plugin
    }
}