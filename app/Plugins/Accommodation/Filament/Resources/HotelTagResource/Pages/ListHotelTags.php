<?php

namespace App\Plugins\Accommodation\Filament\Resources\HotelTagResource\Pages;

use App\Plugins\Accommodation\Filament\Resources\HotelTagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHotelTags extends ListRecords
{
    protected static string $resource = HotelTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}