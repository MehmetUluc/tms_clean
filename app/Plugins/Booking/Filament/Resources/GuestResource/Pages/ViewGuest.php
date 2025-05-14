<?php

namespace App\Plugins\Booking\Filament\Resources\GuestResource\Pages;

use App\Plugins\Booking\Filament\Resources\GuestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGuest extends ViewRecord
{
    protected static string $resource = GuestResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('backToReservation')
                ->label('Rezervasyona Git')
                ->icon('heroicon-o-arrow-left')
                ->url(function () {
                    $reservation = $this->record->reservation;
                    if ($reservation) {
                        return route('filament.admin.resources.reservations.edit', ['record' => $reservation->id]);
                    }
                    return null;
                })
                ->visible(fn () => $this->record->reservation !== null),
        ];
    }
}