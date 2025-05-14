<?php

namespace App\Plugins\Booking\Filament\Resources\ReservationResource\Pages;

use App\Plugins\Booking\Filament\Resources\ReservationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;
}