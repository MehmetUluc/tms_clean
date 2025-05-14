<?php

namespace App\Plugins\Amenities;

use Filament\Contracts\Plugin;
use Filament\Panel;
use App\Plugins\Core\src\Contracts\PluginInterface;

class AmenitiesPlugin implements Plugin, PluginInterface
{
    public static function make(): static
    {
        return new static();
    }
    
    public function getId(): string
    {
        return 'amenities';
    }

    public function register(Panel $panel): void
    {
        // Amenities plugin resource ve sayfalarını kaydet
        $panel
            ->resources([
                Filament\Resources\HotelAmenityResource::class,
                Filament\Resources\RoomAmenityResource::class,
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
            'name' => 'Amenities',
            'description' => 'Otel ve oda özellikleri/etiketleri yönetimi için plugin',
            'version' => '1.0.0',
            'author' => 'Filament',
        ];
    }
}