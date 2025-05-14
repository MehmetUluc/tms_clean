<?php

namespace App\Plugins\Accommodation\Filament\Resources\HotelTypeResource\Pages;

use App\Plugins\Accommodation\Filament\Resources\HotelTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHotelType extends EditRecord
{
    protected static string $resource = HotelTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}