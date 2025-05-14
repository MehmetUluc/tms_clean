<?php

namespace App\Plugins\OTA\Filament\Resources\DataMappingResource\Pages;

use App\Plugins\OTA\Filament\Resources\DataMappingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataMappings extends ListRecords
{
    protected static string $resource = DataMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}