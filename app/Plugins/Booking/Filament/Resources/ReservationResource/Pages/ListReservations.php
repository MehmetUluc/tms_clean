<?php

namespace App\Plugins\Booking\Filament\Resources\ReservationResource\Pages;

use App\Plugins\Booking\Filament\Resources\ReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('wizardReservation')
                ->label('Yeni Rezervasyon Sihirbazı')
                ->color('success')
                ->icon('heroicon-o-sparkles')
                ->url(fn () => '/admin/booking-wizard'),
        ];
    }
}