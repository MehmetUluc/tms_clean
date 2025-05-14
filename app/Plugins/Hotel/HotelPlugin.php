<?php

namespace App\Plugins\Hotel;

use Filament\Contracts\Plugin;
use Filament\Panel;
use App\Plugins\Core\src\Contracts\PluginInterface;

class HotelPlugin implements Plugin, PluginInterface
{
    public static function make(): static
    {
        return new static();
    }
    
    public function getId(): string
    {
        return 'hotel';
    }

    public function register(Panel $panel): void
    {
        // Hotel plugin'in kaynaklarını ve sayfalarını kaydet
        $panel
            ->resources([
                // Şimdilik mevcut kaynakları kullan, sonra buraya taşı
                // Filament\Resources\HotelResource::class,
            ])
            ->pages([
                // Sayfalar
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
            'name' => 'Hotel',
            'description' => 'Otel ve oda yönetimi için plugin',
            'version' => '1.0.0',
            'author' => 'Filament',
        ];
    }
}