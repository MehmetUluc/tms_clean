<?php

namespace App\Plugins\OTA\Filament\Resources\XmlMappingResource\Pages;

use App\Plugins\OTA\Filament\Resources\XmlMappingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListXmlMappings extends ListRecords
{
    protected static string $resource = XmlMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}