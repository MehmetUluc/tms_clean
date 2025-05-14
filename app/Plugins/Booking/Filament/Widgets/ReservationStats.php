<?php

namespace App\Plugins\Booking\Filament\Widgets;

use App\Plugins\Booking\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReservationStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    
    protected function getStats(): array
    {
        // Get dummy data for now, but structured for real implementation later
        $totalReservations = 135;
        $confirmedReservations = 98;
        $pendingReservations = 37;
        $checkInToday = 12;
        $checkOutToday = 8;
        
        // Calculate dummy percentages for trend indicators
        $totalIncrease = 12.5;
        $confirmedIncrease = 15.2;
        $pendingDecrease = 8.7;
        
        return [
            Stat::make('Toplam Rezervasyon', $totalReservations)
                ->description("{$totalIncrease}% artış")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->chart([10, 15, 18, 14, 16, 14, 22, 28, 26, 32, 38, 42, 47, 53, 58])
                ->icon('heroicon-o-clipboard-document-list'),
                
            Stat::make('Onaylanmış Rezervasyon', $confirmedReservations)
                ->description("{$confirmedIncrease}% artış")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([5, 10, 15, 12, 18, 22, 28, 30, 35, 42, 48, 52, 60, 67, 75])
                ->icon('heroicon-o-check-circle'),
                
            Stat::make('Onay Bekleyen', $pendingReservations)
                ->description("{$pendingDecrease}% azalış")
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('warning')
                ->chart([22, 18, 16, 12, 10, 8, 6, 7, 8, 10, 12, 9, 8, 6, 5])
                ->icon('heroicon-o-clock'),
                
            Stat::make('Bugün Giriş', $checkInToday)
                ->color('info')
                ->icon('heroicon-o-arrow-right-circle'),
                
            Stat::make('Bugün Çıkış', $checkOutToday)
                ->color('danger')
                ->icon('heroicon-o-arrow-left-circle'),
        ];
    }
}