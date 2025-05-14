<?php

namespace App\Plugins\OTA\Filament\Resources\XmlMappingResource\Pages;

use App\Plugins\OTA\Filament\Resources\XmlMappingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateXmlMapping extends CreateRecord
{
    protected static string $resource = XmlMappingResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}