<?php

namespace App\Plugins\Partner;

use App\Plugins\Partner\Filament\Pages\SimplePartnerDashboard;
use Filament\Contracts\Plugin;
use Filament\Panel;

class PartnerPlugin implements Plugin
{
    public static function make(): static
    {
        return new static();
    }

    public function getId(): string
    {
        return 'partner';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                SimplePartnerDashboard::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}