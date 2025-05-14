<?php

namespace App\Plugins\Accommodation\Filament\Resources\HotelTypeResource\Pages;

use App\Plugins\Accommodation\Filament\Resources\HotelTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHotelType extends CreateRecord
{
    protected static string $resource = HotelTypeResource::class;
}