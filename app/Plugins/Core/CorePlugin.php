<?php

namespace App\Plugins\Core;

use Filament\Contracts\Plugin;
use Filament\Panel;

class CorePlugin implements Plugin
{
    public static function make(): static
    {
        return new static();
    }
    
    public function getId(): string
    {
        return 'core';
    }

    public function register(Panel $panel): void
    {
        // Core plugin kaynakları için resources ve pages
        $panel
            ->resources([
                // Core kaynaklar buraya eklenecek
                Filament\Resources\UserResource::class,
            ])
            ->pages([
                // Core sayfalar buraya eklenecek
                // Test sayfası devre dışı bırakıldı
            ])
            ->widgets([
                // Core widgets
                \App\Plugins\Core\src\Filament\Widgets\DashboardOverview::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // Core plugin başlatma işlemleri
    }
}