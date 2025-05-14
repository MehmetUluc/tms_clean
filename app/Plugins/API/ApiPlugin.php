<?php

namespace App\Plugins\API;

use Filament\Contracts\Plugin;
use Filament\Panel;
use App\Plugins\Core\src\Contracts\PluginInterface;

class ApiPlugin implements Plugin, PluginInterface
{
    public static function make(): static
    {
        return new static();
    }
    
    public function getId(): string
    {
        return 'api';
    }

    public function register(Panel $panel): void
    {
        // API plugin resource ve sayfalarını kaydet
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
            'name' => 'API',
            'description' => 'API entegrasyon yönetimi için plugin',
            'version' => '1.0.0',
            'author' => 'Filament',
        ];
    }
}