<?php

namespace App\Plugins\Amenities\Filament\Resources\HotelAmenityResource\Pages;

use App\Plugins\Amenities\Filament\Resources\HotelAmenityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHotelAmenity extends CreateRecord
{
    protected static string $resource = HotelAmenityResource::class;
}