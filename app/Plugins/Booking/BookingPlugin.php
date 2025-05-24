<?php

namespace App\Plugins\Booking;

use Filament\Contracts\Plugin;
use Filament\Panel;
use App\Plugins\Core\src\Contracts\PluginInterface;

class BookingPlugin implements Plugin, PluginInterface
{
    public static function make(): static
    {
        return new static();
    }
    
    public function getId(): string
    {
        return 'booking';
    }

    public function register(Panel $panel): void
    {
        // Booking plugin resource ve sayfalarını kaydet
        $panel
            ->resources([
                Filament\Resources\ReservationResource::class,
                Filament\Resources\GuestResource::class,
            ])
            ->pages([
                \App\Plugins\Booking\Filament\Pages\BookingWizard::class,
                \App\Plugins\Booking\Filament\Pages\BookingWizardV2::class,
                \App\Plugins\Booking\Filament\Pages\BookingWizardV3::class,
            ])
            ->widgets([
                Filament\Widgets\ReservationStats::class,
                Filament\Widgets\RevenueChart::class,
                Filament\Widgets\LatestReservations::class,
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
            'name' => 'Booking',
            'description' => 'Rezervasyon ve misafir yönetimi için plugin',
            'version' => '1.0.0',
            'author' => 'Filament',
        ];
    }
}