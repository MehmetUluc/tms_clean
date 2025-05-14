<?php

namespace App\Plugins\Amenities\Filament\Resources\RoomAmenityResource\Pages;

use App\Plugins\Amenities\Filament\Resources\RoomAmenityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomAmenities extends ListRecords
{
    protected static string $resource = RoomAmenityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}