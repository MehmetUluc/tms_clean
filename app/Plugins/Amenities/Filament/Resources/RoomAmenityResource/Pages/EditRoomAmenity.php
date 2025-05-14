<?php

namespace App\Plugins\Amenities\Filament\Resources\RoomAmenityResource\Pages;

use App\Plugins\Amenities\Filament\Resources\RoomAmenityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoomAmenity extends EditRecord
{
    protected static string $resource = RoomAmenityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}