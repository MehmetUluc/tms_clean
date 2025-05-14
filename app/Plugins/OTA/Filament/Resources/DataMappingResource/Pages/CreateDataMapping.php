<?php

namespace App\Plugins\OTA\Filament\Resources\DataMappingResource\Pages;

use App\Plugins\OTA\Filament\Resources\DataMappingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDataMapping extends CreateRecord
{
    protected static string $resource = DataMappingResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}