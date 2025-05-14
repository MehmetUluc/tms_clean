<?php

namespace App\Plugins\Accommodation;

use Filament\Contracts\Plugin;
use Filament\Panel;
use App\Plugins\Core\src\Contracts\PluginInterface;

class AccommodationPlugin implements Plugin, PluginInterface
{
    public static function make(): static
    {
        return new static();
    }
    
    public function getId(): string
    {
        return 'accommodation';
    }

    public function register(Panel $panel): void
    {
        // Accommodation plugin resource ve sayfalarını kaydet
        $panel
            ->resources([
                Filament\Resources\HotelResource::class,
                Filament\Resources\RoomResource::class,
                Filament\Resources\RoomTypeResource::class,
                Filament\Resources\HotelTypeResource::class,
                Filament\Resources\RegionResource::class,
                Filament\Resources\HotelTagResource::class,
                Filament\Resources\BoardTypeResource::class,
            ])
            ->widgets([
                Filament\Widgets\HotelOccupancyChart::class,
                Filament\Widgets\PopularRoomTypes::class,
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
            'name' => 'Accommodation',
            'description' => 'Otel ve oda yönetimi için plugin',
            'version' => '1.0.0',
            'author' => 'Filament',
        ];
    }
}