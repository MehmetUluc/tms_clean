<?php

namespace App\Plugins\Integration;

use Filament\Contracts\Plugin;
use Filament\Panel;
use App\Plugins\Core\src\Contracts\PluginInterface;

class IntegrationPlugin implements Plugin, PluginInterface
{
    public static function make(): static
    {
        return new static();
    }
    
    public function getId(): string
    {
        return 'integration';
    }

    public function register(Panel $panel): void
    {
        // Integration plugin resource ve sayfalarını kaydet
        $panel
            ->resources([
                \App\Plugins\API\Filament\Resources\ApiUserResource::class,
                \App\Plugins\API\Filament\Resources\ApiMappingResource::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        // Plugin başlatma işlemleri
    }
    
    public function isEnabled(): bool
    {
        return true;
    }
    
    public function getInfo(): array
    {
        return [
            'name' => 'Integration',
            'description' => 'API entegrasyonları ve haritalama için plugin',
            'version' => '1.0.0',
            'author' => 'Filament',
        ];
    }
}